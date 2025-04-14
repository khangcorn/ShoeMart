<?php

namespace App\Http\Controllers;

use App\Models\ShippingFee;
use Illuminate\Http\Request;

class ShippingFeeController extends Controller
{
    public function index()
    {
        $shippingFees = ShippingFee::orderByDesc('created_at')->get();

        return view('admin.shipping_fees.index', compact('shippingFees'));
    }

    public function create()
    {
        $data = json_decode(file_get_contents(public_path('data/vn-addresses.json')), true);
        return view('admin.shipping_fees.create', compact('data'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward'     => 'nullable|string|max:100',
            'fee'      => 'required|numeric|min:0',
        ]);

        ShippingFee::create($request->only('province', 'district', 'ward', 'fee'));

        return redirect()->route('shipping-fees.index')
                         ->with('success', 'Shipping fee created successfully.');
    }

    public function edit($id)
    {
        $shippingFee = ShippingFee::findOrFail($id);
        $data = json_decode(file_get_contents(public_path('data/vn-addresses.json')), true);
        
        return view('admin.shipping_fees.edit', compact('shippingFee', 'data'));
    }
    
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward'     => 'nullable|string|max:100',
            'fee'      => 'required|numeric|min:0',
        ]);
    
        $shippingFee = ShippingFee::findOrFail($id);
        $shippingFee->update($request->only('province', 'district', 'ward', 'fee'));
    
        return redirect()->route('shipping-fees.index')
                         ->with('success', 'Shipping fee updated successfully.');
    }
    

    public function destroy($id)
    {
        $shippingFee = ShippingFee::findOrFail($id);
        $shippingFee->delete();

        return redirect()->route('shipping-fees.index')
                         ->with('success', 'Shipping fee deleted successfully.');
    }
    
}
