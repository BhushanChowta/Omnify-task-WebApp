<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Requests\DiscountRequest;
use Log;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('discounts.create');
    }

    public function store(DiscountRequest $request)
    {
        Discount::create($request->validated()); //Server Side Validation

        return redirect()->route('discounts.index')
                         ->with('success', 'Discount created successfully.');
    }

    public function show($id)
    {
        $discount = Discount::findOrFail($id);
        return view('discounts.show', compact('discount'));
    }

    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        return view('discounts.edit', compact('discount'));
    }

    public function update(DiscountRequest $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $discount->update($request->validated()); //Server Side Validation

        return redirect()->route('discounts.index')
                         ->with('success', 'Discount updated successfully.');
    }

    public function delete($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('discounts.index')
                         ->with('success', 'Discount deleted successfully.');
    }
}
