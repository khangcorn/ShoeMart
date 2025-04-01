<?php

namespace App\Http\Controllers;

use App\Models\VariantAttribute;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = VariantAttribute::where('attribute_name', 'size')->get();
        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);
    
        // Kiểm tra xem size đã tồn tại chưa
        $exists = VariantAttribute::where('attribute_name', 'size')
            ->where('attribute_value', $request->attribute_value)
            ->exists();
    
        if ($exists) {
            return redirect()->back()->with('error', 'Size này đã tồn tại.');
        }
    
        // Nếu không trùng, tiến hành tạo mới
        VariantAttribute::create([
            'attribute_name' => 'size',
            'attribute_value' => $request->attribute_value,
        ]);
    
        return redirect()->route('sizes.index')->with('success', 'Size created successfully');
    }
    

    public function edit($id)
    {
        $size = VariantAttribute::where('attribute_name', 'size')->findOrFail($id);
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        // Tìm size cần cập nhật
        $size = VariantAttribute::where('attribute_name', 'size')->findOrFail($id);
    
        // Chuẩn hóa dữ liệu (loại bỏ khoảng trắng thừa)
        $newSizeValue = trim($request->attribute_value);
    
        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);
    
        // Kiểm tra xem size có bị trùng hay không
        $exists = VariantAttribute::where('attribute_name', 'size')
            ->where('attribute_value', $newSizeValue)
            ->where('attribute_id', '!=', $id) // Loại trừ ID hiện tại
            ->exists();
    
        if ($exists) {
            return redirect()->back()->with('error', 'Size này đã tồn tại.');
        }
    
        // Nếu không trùng, tiến hành cập nhật
        $size->update([
            'attribute_value' => $newSizeValue,
        ]);
    
        return redirect()->route('sizes.index')->with('success', 'Size updated successfully');
    }
    

    public function destroy($id)
    {
        // Tìm size theo ID
        $size = VariantAttribute::where('attribute_name', 'size')->findOrFail($id);
    
        // Kiểm tra xem size này có đang được sử dụng trong variant_attribute_values
        $isUsed = $size->variantAttributeValues()->exists();
    
        if ($isUsed) {
            return redirect()->route('sizes.index')->with('error', 'Không thể xóa! Size này đang được sử dụng trong một biến thể sản phẩm.');
        }
    
        // Nếu không bị ràng buộc, tiến hành xóa
        $size->delete();
    
        return redirect()->route('sizes.index')->with('success', 'Size deleted successfully');
    }
    
}
