<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::query();

        // Search by ID
        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        // Search by name (keep for backward compatibility)
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Order by name
        $cities = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'slug' => $city->slug,
                ];
            })
        ]);
    }
} 