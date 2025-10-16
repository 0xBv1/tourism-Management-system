<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\RestaurantDataTable;
use App\Http\Requests\Dashboard\RestaurantRequest;
use App\Models\Restaurant;
use App\Models\City;
use App\Models\Meal;
use App\Enums\ResourceStatus;

class RestaurantController extends Controller
{
    public function index(RestaurantDataTable $dataTable)
    {
        return $dataTable->render('dashboard.restaurants.index');
    }

    public function create()
    {
        $cities = City::all();
        return view('dashboard.restaurants.create', compact('cities'));
    }

    public function store(RestaurantRequest $request)
    {
        $data = $request->getSanitized();
        $meals = $data['meals'] ?? [];
        unset($data['meals']);
        
        $restaurant = Restaurant::create($data);
        
        // Create meals
        if (!empty($meals)) {
            foreach ($meals as $mealData) {
                if (!empty($mealData['name']) && !empty($mealData['price'])) {
                    $mealData['restaurant_id'] = $restaurant->id;
                    $mealData['is_featured'] = isset($mealData['is_featured']) ? (bool)$mealData['is_featured'] : false;
                    $mealData['is_available'] = isset($mealData['is_available']) ? (bool)$mealData['is_available'] : true;
                    Meal::create($mealData);
                }
            }
        }
        
        session()->flash('message', 'Restaurant Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.restaurants.index');
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['city', 'bookings.bookingFile']);
        return view('dashboard.restaurants.show', compact('restaurant'));
    }

    public function edit(Restaurant $restaurant)
    {
        $cities = City::all();
        $restaurant->load('meals');
        return view('dashboard.restaurants.edit', compact('restaurant', 'cities'));
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        $data = $request->getSanitized();
        $meals = $data['meals'] ?? [];
        unset($data['meals']);
        
        $restaurant->update($data);
        
        // Handle meals update
        if (!empty($meals)) {
            $existingMealIds = [];
            
            foreach ($meals as $mealData) {
                if (!empty($mealData['name']) && !empty($mealData['price'])) {
                    $mealData['restaurant_id'] = $restaurant->id;
                    $mealData['is_featured'] = isset($mealData['is_featured']) ? (bool)$mealData['is_featured'] : false;
                    $mealData['is_available'] = isset($mealData['is_available']) ? (bool)$mealData['is_available'] : true;
                    
                    if (isset($mealData['id']) && $mealData['id']) {
                        // Update existing meal
                        $meal = Meal::find($mealData['id']);
                        if ($meal && $meal->restaurant_id == $restaurant->id) {
                            $meal->update($mealData);
                            $existingMealIds[] = $meal->id;
                        }
                    } else {
                        // Create new meal
                        $meal = Meal::create($mealData);
                        $existingMealIds[] = $meal->id;
                    }
                }
            }
            
            // Delete meals that are no longer in the form
            $restaurant->meals()->whereNotIn('id', $existingMealIds)->delete();
        } else {
            // If no meals provided, delete all existing meals
            $restaurant->meals()->delete();
        }
        
        session()->flash('message', 'Restaurant Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return response()->json([
            'message' => 'Restaurant Deleted Successfully!'
        ]);
    }
}
