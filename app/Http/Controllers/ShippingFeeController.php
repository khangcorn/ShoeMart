<?php

namespace App\Http\Controllers;

use App\Models\ShippingFee;
use Illuminate\Http\Request;

class ShippingFeeController extends Controller
{
      public function __construct()
    {
        $this->middleware('check_permission:view_shipping_fees')->only(['index', 'show']);
        $this->middleware('check_permission:create_shipping_fees')->only(['create', 'store']);
        $this->middleware('check_permission:edit_shipping_fees')->only(['edit', 'update']);
        $this->middleware('check_permission:delete_shipping_fees')->only(['destroy']);
    }
  public function index()
{
    $shippingFees = ShippingFee::orderByDesc('created_at')->paginate(5);

    return view('admin.shipping_fees.index', compact('shippingFees'));
}


    public function create()
    {
        $data = json_decode(file_get_contents(public_path('data/vn-addresses.json')), true);

        return view('admin.shipping_fees.create', compact('data'));
    }

    public function store(Request $request)
    {
        // Không loại bỏ tiền tố, giữ nguyên giá trị nhập từ form
        $province = $request->input('province');
        $district = $request->input('district');
        $ward = $request->input('ward');

        // Cập nhật lại dữ liệu trong request với các giá trị gốc
        $request->merge([
            'province' => $province,
            'district' => $district,
            'ward' => $ward,
        ]);

        // Validate request
        $request->validate([
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'fee' => 'required|numeric|min:0',
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
            'ward' => 'nullable|string|max:100',
            'fee' => 'required|numeric|min:0',
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
            $name = preg_replace('/^'.preg_quote($prefix, '/').'\s*/i', '', $name);
        }

        return $name;
    }

    public function updateShippingFee(Request $request)
    {
        $addressId = $request->input('address_id');
        $userAddress = auth()->user()->userAddresses()->find($addressId);

        if (! $userAddress) {
            return response()->json(['shipping_fee' => null, 'shipping_id' => null], 404);
        }

        // Tìm phí ship dựa trên tỉnh, huyện, xã
        $shippingFees = ShippingFee::all();
        $shippingFee = $shippingFees->firstWhere(function ($fee) use ($userAddress) {
            return strtolower($fee->province) === strtolower($userAddress->city) &&
                   strtolower($fee->district) === strtolower($userAddress->district) &&
                   strtolower($fee->ward) === strtolower($userAddress->ward);
        });

        // Nếu không tìm thấy phí ship cụ thể, dùng phí mặc định
        $shippingFee = $shippingFee ?: $shippingFees->firstWhere('shipping_id', 3);

        $shippingFeeValue = $shippingFee ? $shippingFee->fee : 120000; // fallback
        $shippingId = $shippingFee ? $shippingFee->shipping_id : null;

        return response()->json([
            'shipping_fee' => $shippingFeeValue,
            'shipping_id' => $shippingId,
        ]);
    }
}
