<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\DataTables\SupplierTourDataTable;
use App\Models\SupplierTour;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Duration;
use App\Models\TourOption;
use App\Http\Requests\Dashboard\SupplierTourRequest;
use Illuminate\Support\Facades\Auth;

class SupplierTourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SupplierTourDataTable $dataTable)
    {

        return $dataTable->render('dashboard.supplier.tours.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $relations = [
            'categories' => Category::orderByTranslation('title')->get()->pluck('title', 'id')->toArray(),
            'destinations' => Destination::orderByTranslation('title')->get()->pluck('title', 'id')->toArray(),
            'options' => TourOption::orderByTranslation('name')->get()->pluck('name', 'id')->toArray(),
            'durations' => Duration::where('enabled', true)->orderBy('display_order', 'asc')->get()->mapWithKeys(function($duration) {
                return [$duration->id => $duration->title];
            })->toArray(),
        ];

        return view('dashboard.supplier.tours.create', compact('relations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierTourRequest $request)
    {
        $supplier = Auth::user()->supplier;
        
        // Get the main locale title for the main table
        $mainLocale = config('app.locale');
        $mainTitle = $request->input($mainLocale . '.title');
        
        // If no title is provided, try to get from any available translation
        if (empty($mainTitle)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $title = $request->input($locale . '.title');
                if (!empty($title)) {
                    $mainTitle = $title;
                    break;
                }
            }
        }
        
        // If still no title, use a default
        if (empty($mainTitle)) {
            $mainTitle = 'Untitled Tour ' . time();
        }
        
        // Get duration and type from translatable data or provide defaults
        $mainDuration = $request->input($mainLocale . '.duration');
        $mainType = $request->input($mainLocale . '.type');
        
        // If no duration is provided, try to get from any available translation
        if (empty($mainDuration)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $duration = $request->input($locale . '.duration');
                if (!empty($duration)) {
                    $mainDuration = $duration;
                    break;
                }
            }
        }
        
        // If no type is provided, try to get from any available translation
        if (empty($mainType)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $type = $request->input($locale . '.type');
                if (!empty($type)) {
                    $mainType = $type;
                    break;
                }
            }
        }
        
        // Create tour with title, duration, and type included
        $tour = SupplierTour::create(array_merge(
            $request->getSanitized(),
            [
                'supplier_id' => $supplier->id,
                'title' => $mainTitle,
                'duration' => $mainDuration,
                'type' => $mainType
            ]
        ));

        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $tour->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $tour->save();

        // Handle relationships
        $relationshipData = $request->getRelationshipData();
        if (!empty($relationshipData['categories'])) {
            $tour->categories()->attach($relationshipData['categories']);
        }
        if (!empty($relationshipData['destinations'])) {
            $tour->destinations()->attach($relationshipData['destinations']);
        }
        if (!empty($relationshipData['options'])) {
            $tour->options()->attach($relationshipData['options']);
        }
        
        // Handle duration relationship
        if ($request->has('duration_id')) {
            $tour->durations()->attach($request->get('duration_id'));
        }

        return redirect()->route('supplier.tours.index')
            ->with('success', 'Tour created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierTour $tour)
    {
        $this->authorize('supplier.tours.edit', $tour);

        // Eager load relationships
        $tour->load(['tourDays', 'categories', 'destinations', 'options', 'durations']);

        $relations = [
            'categories' => Category::orderByTranslation('title')->get()->pluck('title', 'id')->toArray(),
            'destinations' => Destination::orderByTranslation('title')->get()->pluck('title', 'id')->toArray(),
            'options' => TourOption::orderByTranslation('name')->get()->pluck('name', 'id')->toArray(),
            'durations' => Duration::where('enabled', true)->orderBy('display_order', 'asc')->get()->mapWithKeys(function($duration) {
                return [$duration->id => $duration->title];
            })->toArray(),
        ];

        return view('dashboard.supplier.tours.edit', compact('tour', 'relations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierTourRequest $request, SupplierTour $tour)
    {
        $this->authorize('supplier.tours.edit', $tour);

        // Get the main locale title for the main table
        $mainLocale = config('app.locale');
        $mainTitle = $request->input($mainLocale . '.title');
        
        // If no title is provided, try to get from any available translation
        if (empty($mainTitle)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $title = $request->input($locale . '.title');
                if (!empty($title)) {
                    $mainTitle = $title;
                    break;
                }
            }
        }
        
        // If still no title, use existing title or default
        if (empty($mainTitle)) {
            $mainTitle = $tour->title ?? 'Untitled Tour ' . time();
        }

        // Get duration and type from translatable data or provide defaults
        $mainDuration = $request->input($mainLocale . '.duration');
        $mainType = $request->input($mainLocale . '.type');
        
        // If no duration is provided, try to get from any available translation
        if (empty($mainDuration)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $duration = $request->input($locale . '.duration');
                if (!empty($duration)) {
                    $mainDuration = $duration;
                    break;
                }
            }
        }
        
        // If no type is provided, try to get from any available translation
        if (empty($mainType)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $type = $request->input($locale . '.type');
                if (!empty($type)) {
                    $mainType = $type;
                    break;
                }
            }
        }

        // Update tour with title, duration, and type included
        $tour->update(array_merge(
            $request->getSanitized(),
            [
                'title' => $mainTitle,
                'duration' => $mainDuration,
                'type' => $mainType
            ]
        ));

        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $tour->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $tour->save();

        // Handle relationships
        $relationshipData = $request->getRelationshipData();
        if (isset($relationshipData['categories'])) {
            $tour->categories()->sync($relationshipData['categories']);
        }
        if (isset($relationshipData['destinations'])) {
            $tour->destinations()->sync($relationshipData['destinations']);
        }
        if (isset($relationshipData['options'])) {
            $tour->options()->sync($relationshipData['options']);
        }
        
        // Handle duration relationship
        if ($request->has('duration_id')) {
            $tour->durations()->sync([$request->get('duration_id')]);
        }

        // Handle tour days
        if ($request->has('days')) {
            $tour->tourDays()->delete(); // Remove existing days
            foreach ($request->input('days', []) as $dayIndex => $dayData) {
                if (!empty($dayData[$mainLocale]['title'])) {
                    $day = $tour->tourDays()->create([
                        'day_number' => $dayIndex + 1,
                    ]);

                    // Handle translatable fields for days
                    foreach ($dayData as $locale => $localeData) {
                        foreach ($localeData as $field => $value) {
                            if ($value !== null && $value !== '') {
                                $day->translateOrNew($locale)->$field = $value;
                            }
                        }
                    }
                    $day->save();
                }
            }
        }

        // Handle pricing groups
        if ($request->has('pricing_groups')) {
            $tour->update(['pricing_groups' => $request->input('pricing_groups')]);
        }

        return redirect()->route('supplier.tours.index')
            ->with('success', 'Tour updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierTour $tour)
    {
        $this->authorize('supplier.tours.delete', $tour);
        $tour->delete();
        return response()->json([
            'message' => 'Tour Deleted Successfully!'
        ]);
    }
}
