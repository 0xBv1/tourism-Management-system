<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Coupon;
use App\Models\Tour;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CouponRequest;
use App\DataTables\CouponDataTable;

class CouponController extends Controller
{

    public function index(CouponDataTable $dataTable)
    {
        return $dataTable->render('dashboard.coupons.index');
    }


    public function create()
    {
        $tours = Tour::where('enabled', true)
            ->get()
            ->sortBy('title')
            ->mapWithKeys(function($tour) {
                return [$tour->id => $tour->title];
            })
            ->toArray();
            
        $categories = \App\Models\Category::where('enabled', true)
            ->get()
            ->sortBy('title')
            ->mapWithKeys(function($category) {
                return [$category->id => $category->title];
            })
            ->toArray();
            
        return view('dashboard.coupons.create', compact('tours', 'categories'));
    }


    public function store(CouponRequest $request)
    {
        $coupon = Coupon::create($request->getSanitized());
        if ($request->get('tours')) {
            $coupon->tours()->attach($request->get('tours'));
        }
        if ($request->get('categories')) {
            $coupon->categories()->attach($request->get('categories'));
        }
        session()->flash('message', 'Coupon Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.coupons.edit', $coupon);
    }


    public function show(Coupon $coupon)
    {
        //
    }


    public function edit(Coupon $coupon)
    {
        $tours = Tour::where('enabled', true)
            ->get()
            ->sortBy('title')
            ->mapWithKeys(function($tour) {
                return [$tour->id => $tour->title];
            })
            ->toArray();
            
        $categories = \App\Models\Category::where('enabled', true)
            ->get()
            ->sortBy('title')
            ->mapWithKeys(function($category) {
                return [$category->id => $category->title];
            })
            ->toArray();
            
        return view('dashboard.coupons.edit', compact('coupon', 'tours', 'categories'));
    }


    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->getSanitized());
        $coupon->tours()->sync($request->get('tours'));
        $coupon->categories()->sync($request->get('categories'));
        session()->flash('message', 'Coupon Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json([
            'message' => 'Coupon Deleted Successfully!'
        ]);
    }
}
