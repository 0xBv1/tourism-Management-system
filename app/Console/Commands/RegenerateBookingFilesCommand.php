<?php

namespace App\Console\Commands;

use App\Models\BookingFile;
use App\Models\Inquiry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateBookingFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:regenerate-pdfs {--force : Force regeneration even if PDF exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate booking files as proper PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        
        $this->info('Starting booking file PDF regeneration...');

        $bookingFiles = BookingFile::with('inquiry')->get();
        $regenerated = 0;
        $skipped = 0;

        foreach ($bookingFiles as $bookingFile) {
            if (!$bookingFile->inquiry) {
                $this->warn("Skipping booking file {$bookingFile->id} - no inquiry found");
                continue;
            }

            // Check if file already exists and we're not forcing regeneration
            if (!$force && $bookingFile->fileExists()) {
                $this->line("Skipping booking file {$bookingFile->id} - file already exists");
                $skipped++;
                continue;
            }

            try {
                // Generate new PDF
                $pdfContent = $this->generateBookingFilePDF($bookingFile->inquiry);
                
                // Store the PDF file
                Storage::disk('public')->put($bookingFile->file_path, $pdfContent);
                
                $this->info("Regenerated PDF for booking file {$bookingFile->id}");
                $regenerated++;
                
            } catch (\Exception $e) {
                $this->error("Failed to regenerate booking file {$bookingFile->id}: " . $e->getMessage());
            }
        }

        $this->info("PDF regeneration completed!");
        $this->info("Regenerated: {$regenerated} files");
        $this->info("Skipped: {$skipped} files");
    }

    /**
     * Generate booking file PDF
     *
     * @param \App\Models\Inquiry $inquiry
     * @return string
     */
    private function generateBookingFilePDF($inquiry): string
    {
        $data = [
            'inquiry' => $inquiry,
            'generated_at' => now(),
            'booking_id' => $inquiry->id,
        ];

        $pdf = Pdf::loadView('emails.booking-confirmation-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
        ]);

        return $pdf->output();
    }
}
