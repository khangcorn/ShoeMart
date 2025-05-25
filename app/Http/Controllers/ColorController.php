<?php

namespace App\Http\Controllers;

use App\Models\VariantAttribute;
use Illuminate\Http\Request;

class ColorController extends Controller
{
      public function __construct()
    {
        $this->middleware('check_permission:view_colors')->only(['index', 'show']);
        $this->middleware('check_permission:create_colors')->only(['create', 'store']);
        $this->middleware('check_permission:edit_colors')->only(['edit', 'update']);
        $this->middleware('check_permission:delete_colors')->only(['destroy']);
    }
    public function index()
    {
        $colors = VariantAttribute::where('attribute_name', 'color')->get();

        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);

        // Kiểm tra xem color đã tồn tại chưa
        $exists = VariantAttribute::where('attribute_name', 'color')
            ->where('attribute_value', $request->attribute_value)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Color này đã tồn tại.');
        }

        // Nếu không trùng, tiến hành tạo mới
        VariantAttribute::create([
            'attribute_name' => 'Color',
            'attribute_value' => $request->attribute_value,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color created successfully');
    }

    public function edit($id)
    {
        $color = VariantAttribute::where('attribute_name', 'color')->findOrFail($id);

        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        // Tìm color cần cập nhật
        $color = VariantAttribute::where('attribute_name', 'color')->findOrFail($id);

        // Chuẩn hóa dữ liệu (loại bỏ khoảng trắng thừa)
        $newColorValue = trim($request->attribute_value);

        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);

        // Kiểm tra xem color có bị trùng hay không
        $exists = VariantAttribute::where('attribute_name', 'color')
            ->where('attribute_value', $newColorValue)
            ->where('attribute_id', '!=', $id) // Loại trừ ID hiện tại
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'color này đã tồn tại.');
        }

        // Nếu không trùng, tiến hành cập nhật
        $color->update([
            'attribute_value' => $newColorValue,
        ]);

        return redirect()->route('colors.index')->with('success', 'color updated successfully');
    }

    public function destroy($id)
    {
        // Tìm color theo ID
        $color = VariantAttribute::where('attribute_name', 'color')->findOrFail($id);

        // Kiểm tra xem color này có đang được sử dụng trong variant_attribute_values
        $isUsed = $color->variantAttributeValues()->exists();

        if ($isUsed) {
            return redirect()->route('colors.index')->with('error', 'Không thể xóa! color này đang được sử dụng trong một biến thể sản phẩm.');
        }

        // Nếu không bị ràng buộc, tiến hành xóa
        $color->delete();

        return redirect()->route('colors.index')->with('success', 'color deleted successfully');
    }
}
