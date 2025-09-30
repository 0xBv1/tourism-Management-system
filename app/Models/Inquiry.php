<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inquiry_id',
        'guest_name',
        'email',
        'phone',
        'arrival_date',
        'departure_date',
        'number_pax',
        'tour_name',
        'nationality',
        'subject',
        'status',
        'client_id',
        'assigned_to',
        'assigned_reservation_id',
        'assigned_operator_id',
        'assigned_admin_id',
        'booking_file_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => InquiryStatus::class,
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedReservation(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_reservation_id');
    }

    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function bookingFile(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class);
    }

    /**
     * Get the latest payment for this inquiry through booking file
     */
    public function latestPayment()
    {
        return $this->bookingFile?->payments()->latest()->first();
    }

    /**
     * Get all payments for this inquiry through booking file
     */
    public function payments()
    {
        return $this->bookingFile?->payments() ?? collect();
    }

    public function syncBookingFileData(): void
    {
        if ($this->bookingFile) {
            $this->bookingFile->update([
                'total_amount' => $this->total_amount,
                'currency' => 'USD', // Default currency, can be made configurable
            ]);
        }
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class)->orderBy('created_at');
    }

    /**
     * Get all resources associated with this inquiry.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(InquiryResource::class)->orderBy('created_at');
    }

    /**
     * Get all assigned users for this inquiry
     */
    public function getAllAssignedUsers(): array
    {
        $assignedUsers = [];
        
        // Check if assigned_to user has Sales role
        if ($this->assignedUser) {
            $userRoles = $this->assignedUser->roles->pluck('name')->toArray();
            $role = in_array('Sales', $userRoles) ? 'Sales' : 'General';
            
            $assignedUsers[] = [
                'user' => $this->assignedUser,
                'role' => $role,
                'field' => 'assigned_to',
                'type' => 'user'
            ];
        }
        
        if ($this->assignedReservation) {
            $assignedUsers[] = [
                'user' => $this->assignedReservation,
                'role' => 'Reservation',
                'field' => 'assigned_reservation_id',
                'type' => 'user'
            ];
        }
        
        if ($this->assignedOperator) {
            $assignedUsers[] = [
                'user' => $this->assignedOperator,
                'role' => 'Operator',
                'field' => 'assigned_operator_id',
                'type' => 'user'
            ];
        }
        
        if ($this->assignedAdmin) {
            $assignedUsers[] = [
                'user' => $this->assignedAdmin,
                'role' => 'Admin',
                'field' => 'assigned_admin_id',
                'type' => 'user'
            ];
        }
        
        // Add assigned resources
        foreach ($this->resources as $resource) {
            $assignedUsers[] = [
                'resource' => $resource,
                'role' => ucfirst($resource->resource_type),
                'field' => 'resource',
                'type' => 'resource',
                'added_by' => $resource->addedBy
            ];
        }
        
        return $assignedUsers;
    }

    /**
     * Check if a user is assigned to this inquiry in any role
     */
    public function isAssignedToUser(User $user): bool
    {
        return $this->assigned_to === $user->id ||
               $this->assigned_reservation_id === $user->id ||
               $this->assigned_operator_id === $user->id ||
               $this->assigned_admin_id === $user->id;
    }

    /**
     * Regenerate the booking file PDF with updated inquiry data
     */
    public function regenerateBookingFile(): void
    {
        if (!$this->bookingFile) {
            return;
        }

        try {
            // Generate new PDF content
            $pdfContent = $this->generateBookingFilePDF();
            
            // Generate new filename with updated data
            $name = $this->sanitizeForFilename($this->guest_name ?? 'Guest_Name');
            $nationality = $this->sanitizeForFilename($this->nationality ?? 'Nationality');
            $filename = "booking-{$this->id}-{$name}-{$nationality}.pdf";
            $filepath = "booking-files/{$filename}";
            
            // Delete old file if it exists
            if (\Storage::disk('public')->exists($this->bookingFile->file_path)) {
                \Storage::disk('public')->delete($this->bookingFile->file_path);
            }
            
            // Update booking file record
            $this->bookingFile->update([
                'file_name' => $filename,
                'file_path' => $filepath,
                'generated_at' => now(),
            ]);
            
            // Store the new PDF file
            \Storage::disk('public')->put($filepath, $pdfContent);
            
        } catch (\Exception $e) {
            \Log::error("Failed to regenerate booking file for inquiry {$this->id}: " . $e->getMessage());
        }
    }

    /**
     * Generate booking file PDF content
     */
    private function generateBookingFilePDF(): string
    {
        // Load all necessary relationships for PDF generation
        $this->load(['client', 'assignedUser.roles', 'assignedReservation.roles', 'assignedOperator.roles', 'assignedAdmin.roles', 'resources.resource', 'resources.addedBy', 'bookingFile.payments']);
        
        $data = [
            'inquiry' => $this,
            'generated_at' => now(),
            'booking_id' => $this->id,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.booking-confirmation-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
        ]);

        return $pdf->output();
    }

    /**
     * Sanitize string for use in filename
     */
    private function sanitizeForFilename(string $string): string
    {
        // Handle empty or whitespace-only strings
        if (empty(trim($string))) {
            return 'unknown';
        }
        
        // Remove special characters and replace spaces with hyphens
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $string);
        $sanitized = preg_replace('/\s+/', '-', trim($sanitized));
        $sanitized = strtolower($sanitized);
        
        // Limit length to avoid filesystem issues
        return substr($sanitized, 0, 50);
    }

    /**
     * Get the role of a specific user for this inquiry
     */
    public function getUserRole(User $user): ?string
    {
        if ($this->assigned_to === $user->id) return 'General';
        if ($this->assigned_reservation_id === $user->id) return 'Reservation';
        if ($this->assigned_operator_id === $user->id) return 'Operator';
        if ($this->assigned_admin_id === $user->id) return 'Admin';
        
        return null;
    }

    public function scopePending($query)
    {
        return $query->where('status', InquiryStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', InquiryStatus::CONFIRMED);
    }


    public function scopeCancelled($query)
    {
        return $query->where('status', InquiryStatus::CANCELLED);
    }

    /**
     * Generate custom inquiry ID
     */
    public function generateInquiryId(): string
    {
        $guestName = str_replace(' ', '', $this->guest_name);
        $nationality = str_replace(' ', '', $this->nationality);
        return "Inquiry #{$this->id}.{$guestName}.{$nationality}";
    }

    /**
     * Calculate remaining amount
     */
    public function calculateRemainingAmount(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Boot method to set inquiry_id and calculate remaining amount
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($inquiry) {
            if (empty($inquiry->inquiry_id)) {
                $inquiry->update(['inquiry_id' => $inquiry->generateInquiryId()]);
            }
        });

        static::updating(function ($inquiry) {
            if ($inquiry->total_amount && $inquiry->paid_amount) {
                $inquiry->remaining_amount = $inquiry->calculateRemainingAmount();
            }
        });

        static::updated(function ($inquiry) {
            // Regenerate PDF if inquiry data that affects the PDF has changed
            $pdfAffectingFields = ['guest_name', 'email', 'phone', 'nationality', 'subject', 'total_amount', 'paid_amount', 'remaining_amount'];
            $changes = $inquiry->getChanges();
            
            // Check if any PDF-affecting fields have changed
            $hasRelevantChanges = false;
            foreach ($pdfAffectingFields as $field) {
                if (isset($changes[$field])) {
                    $hasRelevantChanges = true;
                    break;
                }
            }
            
            // Regenerate PDF if there are relevant changes and a booking file exists
            if ($hasRelevantChanges && $inquiry->bookingFile) {
                $inquiry->regenerateBookingFile();
            }
        });
    }
}
