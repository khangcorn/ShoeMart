<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    // Lấy danh sách tất cả biến thể sản phẩm
    public function index()
    {
        $variants = ProductVariant::with('products', 'attributes')->get();
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
            'variants' => 'required|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attributes' => 'required|array',
            'variants.*.attributes.*.name' => 'required|string',
            'variants.*.attributes.*.value' => 'required|string',
            'variants.*.images' => 'nullable|array', // Chấp nhận mảng ảnh cho từng biến thể
            'variants.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Kiểm tra ảnh
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

            // Lấy variant_id sau khi tạo thành công bản ghi
            $variant_id = $productVariant->variant_id;
            
         
    
            // Lưu ảnh cho biến thể nếu có
            if (isset($variant['images'])) {
                foreach ($variant['images'] as $image) {
                    $imagePath = $image->store('images', 'public'); // Lưu ảnh vào storage/app/public/product_images
    
                    // Lưu đường dẫn vào cơ sở dữ liệu
                    ProductImage::create([
                        'product_id' => $validated['product_id'],
                        'variant_id' => $variant_id,  // Liên kết ảnh với variant_id
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
    public function update(Request $request, $id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'exists:products,product_id',
            'price' => 'numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0',
            'stock' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $variant->update($request->all());
        return response()->json($variant, 200);
    }

    // Xóa một biến thể sản phẩm
    public function destroy($id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }
        $variant->delete();
        return response()->json(['message' => 'Variant deleted'], 200);
    }
}
