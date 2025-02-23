<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::with('products')->get();
        
        return view('product.index', compact('products', 'categories'));

    }
    public function create()
    {
        $products = Product::all();
        $categories = Category::all();
        return view('product.create ', compact('products','categories'));
        
    }

    /**
     * Tạo sản phẩm mới.
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'nullable|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.name' => 'required|string|max:50',
            'variants.*.attributes.*.value' => 'required|string|max:100',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Tạo sản phẩm mới
        $product = Product::create($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));
    
        // Thêm các biến thể cho sản phẩm
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $variant = $product->variants()->create([
                    'price' => $variantData['price'],
                    'price_sale' => $variantData['price_sale'] ?? null,
                    'stock' => $variantData['stock'],
                ]);
    
                // Thêm các thuộc tính cho biến thể
                if (isset($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attributeData) {
                        $variant->attributes()->create([
                            'attribute_name' => $attributeData['name'],
                            'attribute_value' => $attributeData['value'],
                        ]);
                    }
                }
            }
        }
    
        // Thêm hình ảnh cho sản phẩm
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/images');
                $product->images()->create([
                    'image_url' => Storage::url($path),
                    'type' => 'gallery', // Hoặc 'main' tùy theo logic của bạn
                ]);
            }
        }
    
        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }
    /**
     * Hiển thị chi tiết một sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'variants.attributes', 'images'])->findOrFail($id);
        return view('product.show', compact('product'));
    }
    

    /**
     * Cập nhật sản phẩm.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,variant_id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.id' => 'nullable|exists:variant_attributes,attribute_id',
            'variants.*.attributes.*.name' => 'required|string|max:50',
            'variants.*.attributes.*.value' => 'required|string|max:100',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Cập nhật thông tin sản phẩm
        $product->update($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));
    
        // Cập nhật hoặc thêm mới các biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $variant = $product->variants()->updateOrCreate(
                    ['variant_id' => $variantData['id'] ?? null],
                    [
                        'price' => $variantData['price'],
                        'price_sale' => $variantData['price_sale'] ?? null,
                        'stock' => $variantData['stock'],
                    ]
                );
    
                // Cập nhật hoặc thêm mới các thuộc tính cho biến thể
                if (isset($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attributeData) {
                        $variant->attributes()->updateOrCreate(
                            ['attribute_id' => $attributeData['id'] ?? null],
                            [
                                'attribute_name' => $attributeData['name'],
                                'attribute_value' => $attributeData['value'],
                            ]
                        );
                    }
                }
            }
        }
    
        // Thêm hình ảnh mới cho sản phẩm
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/images');
                $product->images()->create([
                    'image_url' => Storage::url($path),
                    'type' => 'gallery', // Hoặc 'main' tùy theo logic của bạn
                ]);
            }
        }
    
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }
    

    /**
     * Xóa sản phẩm.
     */
    public function destroy($id)
{
    // Bắt đầu transaction để đảm bảo tính toàn vẹn của dữ liệu
    DB::beginTransaction();

    try {
        // Tìm sản phẩm theo ID
        $product = Product::findOrFail($id);

        // Xóa các hình ảnh liên quan đến sản phẩm
        foreach ($product->images as $image) {
            // Xóa file hình ảnh khỏi storage
            Storage::delete($image->image_url);

            // Xóa bản ghi hình ảnh khỏi cơ sở dữ liệu
            $image->delete();
        }

        // Duyệt qua từng biến thể của sản phẩm
        foreach ($product->variants as $variant) {
            // Xóa các thuộc tính của biến thể
            $variant->attributes()->delete();

            // Xóa biến thể
            $variant->delete();
        }

        // Xóa sản phẩm
        $product->delete();

        // Commit transaction sau khi xóa thành công
        DB::commit();

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công.');
    } catch (\Exception $e) {
        // Rollback transaction nếu có lỗi xảy ra
        DB::rollBack();

        // Ghi log lỗi (nếu cần) và trả về thông báo lỗi
        // Log::error($e->getMessage());
        return redirect()->route('products.index')->with('error', 'Đã xảy ra lỗi khi xóa sản phẩm.');
    }
}
}
