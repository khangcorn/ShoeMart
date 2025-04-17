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
    // Loại bỏ tiền tố (Tỉnh, Thành phố, Huyện, Quận, Phường, Xã)
    $province = $this->removePrefix($request->input('province'));
    $district = $this->removePrefix($request->input('district'));
    $ward = $this->removePrefix($request->input('ward'));

    // Cập nhật lại dữ liệu trong request sau khi đã loại bỏ tiền tố
    $request->merge([
        'province' => $province,
        'district' => $district,
        'ward' => $ward,
    ]);

    // Validate request
    $request->validate([
        'province' => 'required|string|max:100',
        'district' => 'nullable|string|max:100',
        'ward'     => 'nullable|string|max:100',
        'fee'      => 'required|numeric|min:0',
    ]);

    // Lưu vào database
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
    private function removePrefix($name)
    {
        // Loại bỏ các tiền tố như "Tỉnh", "Thành phố", "Huyện", "Quận", "Phường", "Xã"
        $prefixes = ['Tỉnh', 'Thành phố', 'Huyện', 'Quận', 'Thị trấn', 'Phường', 'Xã'];
    
        foreach ($prefixes as $prefix) {
            // Sử dụng str_ireplace để thay thế tiền tố không phân biệt chữ hoa/thường
            $name = preg_replace('/^' . preg_quote($prefix, '/') . '\s*/i', '', $name);
        }
    
        return $name;
    }
    
}
