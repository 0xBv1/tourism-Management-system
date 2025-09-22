<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Display a listing of chat messages for an inquiry.
     */
    public function index(Inquiry $inquiry): JsonResponse
    {
        try {
            $this->authorize('view', $inquiry);
            
            // Filter chats based on user's role and visibility
            $chats = $inquiry->chats()
                ->visibleTo(auth()->user())
                ->with('sender')
                ->orderBy('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $chats
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: ' . $e->getMessage(),
                'user_roles' => auth()->user()->roles->pluck('name'),
                'inquiry_id' => $inquiry->id
            ], 403);
        }
    }

    /**
     * Store a newly created chat message.
     */
    public function store(Request $request, Inquiry $inquiry): JsonResponse
    {
        try {
            $this->authorize('view', $inquiry);

            $request->validate([
                'message' => 'required|string|max:1000',
                'visibility' => 'nullable|string|in:all,reservation,operation,admin'
            ]);

            // Determine visibility based on sender's role
            $visibility = $this->determineMessageVisibility(auth()->user(), $request->input('visibility'));

            $chat = Chat::create([
                'inquiry_id' => $inquiry->id,
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'visibility' => $visibility,
            ]);

            $chat->load('sender');

            // Fire the chat message sent event (with error handling)
            try {
                event(new ChatMessageSent($chat, $inquiry, auth()->user()));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error('Failed to fire ChatMessageSent event: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $chat
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: ' . $e->getMessage()
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Chat message creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }

    /**
     * Mark a chat message as read.
     */
    public function markAsRead(Chat $chat): JsonResponse
    {
        $this->authorize('view', $chat->inquiry);
        
        $chat->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    /**
     * Mark all messages in an inquiry as read for the current user.
     */
    public function markAllAsRead(Inquiry $inquiry): JsonResponse
    {
        $this->authorize('view', $inquiry);
        
        $inquiry->chats()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read'
        ]);
    }

    /**
     * Determine the visibility of a message based on sender's role and request.
     */
    private function determineMessageVisibility(User $sender, ?string $requestedVisibility = null): string
    {
        $senderRoles = $sender->roles->pluck('name')->toArray();
        
        // If user explicitly requested a visibility, use it (if they have permission)
        if ($requestedVisibility && $this->canSetVisibility($sender, $requestedVisibility)) {
            return $requestedVisibility;
        }
        
        // Default visibility based on sender's role
        if (in_array('Admin', $senderRoles) || in_array('Administrator', $senderRoles)) {
            return 'all'; // Admin messages are visible to all
        }
        
        if (in_array('Reservation', $senderRoles)) {
            return 'reservation'; // Reservation messages are only visible to Reservation and Admin
        }
        
        if (in_array('Operation', $senderRoles)) {
            return 'operation'; // Operation messages are only visible to Operation and Admin
        }
        
        // Default to 'all' for other roles
        return 'all';
    }

    /**
     * Check if a user can set a specific visibility.
     */
    private function canSetVisibility(User $user, string $visibility): bool
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Admin can set any visibility
        if (in_array('Admin', $userRoles) || in_array('Administrator', $userRoles)) {
            return true;
        }
        
        // Regular users can only set visibility for their own role or 'all'
        if ($visibility === 'all') {
            return true;
        }
        
        foreach ($userRoles as $role) {
            if ($visibility === strtolower($role)) {
                return true;
            }
        }
        
        return false;
    }
}
