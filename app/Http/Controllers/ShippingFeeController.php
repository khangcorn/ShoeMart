<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingFee;

class ShippingFeeController extends Controller
{
    public function index()
    {
        $shippingFees = ShippingFee::all();
        return view('shipping_fees.index', compact('shippingFees'));
    }

    public function create()
    {
        return view('shipping_fees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'fee' => 'required|numeric|min:0',
        ]);

        ShippingFee::create($request->all());

        return redirect()->route('shipping_fees.index')->with('success', 'thêm mới thành công.');
    }

    public function edit($id)
    {
        $shippingFee = ShippingFee::findOrFail($id);
        return view('shipping_fees.edit', compact('shippingFee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'fee' => 'required|numeric|min:0',
        ]);

        $shippingFee = ShippingFee::findOrFail($id);
        $shippingFee->update($request->all());

        return redirect()->route('shipping_fees.index')->with('success', 'sửa thành công.');
    }

    public function destroy($id)
    {
        $shippingFee = ShippingFee::findOrFail($id);
        $shippingFee->delete();

        return redirect()->route('shipping_fees.index')->with('success', 'xóa thành công.');
    }
}