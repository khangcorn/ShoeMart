<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\VariantAttribute;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    // Lấy danh sách tất cả biến thể sản phẩm
    public function index()
    {
        $variants = ProductVariant::with('products', 'attributes', 'images')->get();
        $products = Product::with('variants')->get();
      
        return view('product_variants.index', compact('products', 'variants'));
    }
    public function create()
    {
        $products = Product::all();
        $variants = ProductVariant::all();
        return view('product_variants.create ', compact('variants','products'));
        
    }
    // Tạo một biến thể mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variants' => 'required|array|min:1',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0|lte:variants.*.price', // Đảm bảo giá khuyến mãi không lớn hơn giá gốc
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attributes' => 'required|array|min:1',
            'variants.*.attributes.*.name' => 'required|string|max:255',
            'variants.*.attributes.*.value' => 'required|string|max:255',
            'variants.*.images' => 'required|array|min:1', // Bắt buộc phải có ít nhất một ảnh
            'variants.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Kiểm tra ảnh đúng định dạng
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không hợp lệ.',
            'variants.required' => 'Cần có ít nhất một biến thể.',
            'variants.*.price.required' => 'Vui lòng nhập giá.',
            'variants.*.price.numeric' => 'Giá phải là số hợp lệ.',
            'variants.*.price.min' => 'Giá không thể nhỏ hơn 0.',
            'variants.*.price_sale.numeric' => 'Giá khuyến mãi phải là số.',
            'variants.*.price_sale.min' => 'Giá khuyến mãi không thể nhỏ hơn 0.',
            'variants.*.price_sale.lte' => 'Giá khuyến mãi không thể lớn hơn giá gốc.',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng.',
            'variants.*.stock.integer' => 'Số lượng phải là số nguyên.',
            'variants.*.stock.min' => 'Số lượng không thể nhỏ hơn 0.',
            'variants.*.attributes.required' => 'Mỗi biến thể cần có ít nhất một thuộc tính.',
            'variants.*.attributes.*.name.required' => 'Tên biến thể không được để trống.',
            'variants.*.attributes.*.name.max' => 'Tên biến thể không được vượt quá 255 ký tự.',
            'variants.*.attributes.*.value.required' => 'Giá trị biến thể không được để trống.',
            'variants.*.attributes.*.value.max' => 'Giá trị biến thể không được vượt quá 255 ký tự.',
            'variants.*.images.required' => 'Vui lòng chọn ít nhất một hình ảnh.',
            'variants.*.images.*.image' => 'File tải lên phải là hình ảnh.',
            'variants.*.images.*.mimes' => 'Chỉ hỗ trợ các định dạng: jpeg, png, jpg, gif, svg.',
            'variants.*.images.*.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ]);

    // Lưu biến thể sản phẩm
    foreach ($validated['variants'] as $variant) {
        // Tạo biến thể sản phẩm
        $productVariant = ProductVariant::create([
            'product_id' => $validated['product_id'],
            'price' => $variant['price'],
            'price_sale' => $variant['price_sale'] ?? null,
            'stock' => $variant['stock'],
        ]);

        // Lấy variant_id sau khi tạo thành công bản ghi
        $productVariant->refresh(); // Tải lại đối tượng để đảm bảo các thuộc tính đã được cập nhật

        // Lưu ảnh cho biến thể nếu có
        if (isset($variant['images'])) {
            foreach ($variant['images'] as $image) {
                $imagePath = $image->store('images', 'public'); // Lưu ảnh vào storage/app/public/product_images

                // Lưu đường dẫn vào cơ sở dữ liệu
                ProductImage::create([
                    'product_id' => $validated['product_id'],
                    'variant_id' => $productVariant->variant_id,  // Liên kết ảnh với variant_id
                    'image_url' => $imagePath, // Lưu đường dẫn relative, không bao gồm 'storage/'
                    'type' => 'gallery',
                ]);
            }
        }

        // Lưu thuộc tính cho biến thể nếu có
        if (isset($variant['attributes'])) {
            foreach ($variant['attributes'] as $attributeData) {
                $productVariant->attributes()->create([
                    'attribute_name' => $attributeData['name'],
                    'attribute_value' => $attributeData['value'],
                ]);
            }
        }
    }

    return redirect()->route('product_variants.index')->with('success', 'Variant added successfully!');
}

    
    
    
    
    

    // Hiển thị thông tin của một biến thể
    public function show($id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }
        return response()->json($variant, 200);
    }

    // Cập nhật thông tin biến thể
   // Hiển thị form chỉnh sửa biến thể
// Hiển thị form chỉnh sửa biến thể
public function edit($id)
{
    $variant = ProductVariant::with('attributes', 'images')->find($id); // Lấy thông tin biến thể cùng với các thuộc tính và hình ảnh
    if (!$variant) {
        return redirect()->route('product_variants.index')->with('error', 'Biến thể không tồn tại.');
    }

    $products = Product::all(); // Lấy danh sách tất cả sản phẩm để chọn trong form

    return view('product_variants.edit', compact('variant', 'products'));
}


// Cập nhật thông tin biến thể
public function update(Request $request, $id)
{
    // Tìm biến thể theo ID
    $variant = ProductVariant::find($id);

    if (!$variant) {
        return redirect()->route('product_variants.index')->with('error', 'Biến thể không tồn tại.');
    }

    // Validate dữ liệu
    $validated = $request->validate([
        'product_id' => 'exists:products,id',
        'variants.0.price' => 'required|numeric|min:0',
        'variants.0.price_sale' => 'nullable|numeric|min:0',
        'variants.0.stock' => 'required|integer|min:0',
    ]);

    // Cập nhật các trường có thể thay đổi
    $variant->update([
        'price' => $validated['variants'][0]['price'],
        'price_sale' => $validated['variants'][0]['price_sale'] ?? null,
        'stock' => $validated['variants'][0]['stock'],
    ]);

    // Cập nhật hoặc tạo mới thuộc tính
    if (!empty($request->variants[0]['attributes'])) {
        foreach ($request->variants[0]['attributes'] as $attributeData) {
            VariantAttribute::updateOrCreate(
                [
                    'variant_id' => $variant->variant_id,
                    'attribute_name' => $attributeData['name'],
                ],
                [
                    'attribute_value' => $attributeData['value']
                ]
            );
        }
    }

    // Cập nhật ảnh nếu có
    if ($request->hasFile('variants.0.images')) {
        // Xóa ảnh cũ trong database và storage
        $oldImages = ProductImage::where('variant_id', $variant->variant_id)->get();
    
        foreach ($oldImages as $oldImage) {
            Storage::disk('public')->delete($oldImage->image_url); // Xóa file khỏi storage
            $oldImage->delete(); // Xóa record khỏi database
        }
    
        // Thêm ảnh mới
        foreach ($request->file('variants.0.images') as $image) {
            $imagePath = $image->store('images', 'public');
            ProductImage::create([
                'product_id' => $variant->product_id,
                'variant_id' => $variant->variant_id,
                'image_url' => $imagePath,
                'type' => 'gallery',
            ]);
        }
    }

    return redirect()->route('product_variants.index')->with('success', 'Biến thể đã được cập nhật.');
}




    // Xóa một biến thể sản phẩm
 // Xóa một biến thể sản phẩm
 public function destroy($id)
 {
     $variant = ProductVariant::find($id);
     
     if (!$variant) {
         return response()->json(['message' => 'Variant not found'], 404);
     }
 
     // Xóa các ảnh liên quan đến biến thể
     $variant->images->each(function ($image) {
         $imagePath = 'public/' . $image->image_url;
         if (Storage::exists($imagePath)) {
             Storage::delete($imagePath); // Xóa ảnh trong storage
         }
         $image->delete(); // Xóa dữ liệu ảnh trong cơ sở dữ liệu
     });
 
     // Xóa các thuộc tính liên quan đến biến thể (dùng cột id trong bảng product_variant_attributes)
     $variant->attributes->each(function ($attribute) {
         $attribute->delete(); // Xóa dữ liệu thuộc tính trong cơ sở dữ liệu
     });
 
     // Xóa biến thể
     $variant->delete();
 
     return redirect()->route('product_variants.index')->with('success', 'Product deleted successfully');
 }
 

}
