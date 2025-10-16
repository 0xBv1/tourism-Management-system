<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Dashboard\SettlementController;
use App\Services\SettlementService;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Dahabia;
use App\Models\Restaurant;
use App\Models\Ticket;
use App\Models\Extra;

class TestSettlementController extends Command
{
    protected $signature = 'test:settlement-controller';
    protected $description = 'Test SettlementController create method';

    public function handle()
    {
        $this->info('Testing SettlementController...');
        
        try {
            // Test controller instantiation
            $service = new SettlementService();
            $controller = new SettlementController($service);
            $this->info('✓ Controller instantiated successfully');
            
            // Test data retrieval
            $guides = Guide::active()->get();
            $representatives = Representative::active()->get();
            $hotels = Hotel::active()->get();
            $vehicles = Vehicle::active()->get();
            $dahabias = Dahabia::active()->get();
            $restaurants = Restaurant::active()->get();
            $tickets = Ticket::active()->get();
            $extras = Extra::active()->get();
            
            $this->info('✓ Data retrieved successfully:');
            $this->line("  - Guides: {$guides->count()}");
            $this->line("  - Representatives: {$representatives->count()}");
            $this->line("  - Hotels: {$hotels->count()}");
            $this->line("  - Vehicles: {$vehicles->count()}");
            $this->line("  - Dahabias: {$dahabias->count()}");
            $this->line("  - Restaurants: {$restaurants->count()}");
            $this->line("  - Tickets: {$tickets->count()}");
            $this->line("  - Extras: {$extras->count()}");
            
            // Test JSON encoding
            $guidesJson = json_encode($guides);
            $this->info('✓ JSON encoding successful');
            $this->line("  - Guides JSON length: " . strlen($guidesJson));
            
            // Test sample data
            if ($guides->count() > 0) {
                $sampleGuide = $guides->first();
                $this->info('✓ Sample guide data:');
                $this->line("  - ID: {$sampleGuide->id}");
                $this->line("  - Name: {$sampleGuide->name}");
                $this->line("  - Active: " . ($sampleGuide->active ? 'Yes' : 'No'));
                $this->line("  - Enabled: " . ($sampleGuide->enabled ? 'Yes' : 'No'));
            }
            
            $this->info('✓ All tests passed!');
            
        } catch (\Exception $e) {
            $this->error('✗ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}