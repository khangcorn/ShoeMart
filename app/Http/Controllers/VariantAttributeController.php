<?php
namespace App\Http\Controllers;

use App\Models\VariantAttribute;
use Illuminate\Http\Request;

class VariantAttributeController extends Controller
{
    // Phương thức để trả về form tạo mới thuộc tính
    public function create()
    {
        return view('admin.variant_attributes.create');
    }

    // Phương thức để xử lý việc tạo mới thuộc tính
    public function store(Request $request)
    {
        // Validating input data
        $request->validate([
            'attribute_name' => 'required|string|max:50',
            'attribute_value' => 'required|string|max:100',
        ]);
    
        // Lưu thuộc tính mới
        VariantAttribute::create([
            'attribute_name' => $request->attribute_name,
            'attribute_value' => $request->attribute_value,
        ]);
    
        // Redirect về trang danh sách
        return redirect()->route('variant_attributes.index')->with('success', 'Attribute created successfully');
    }
    
    public function index()
{
    // Lấy tất cả các thuộc tính
    $variantAttributes = VariantAttribute::all();

    // Trả về view và truyền dữ liệu
    return view('admin.variant_attributes.index', compact('variantAttributes'));
}

}
