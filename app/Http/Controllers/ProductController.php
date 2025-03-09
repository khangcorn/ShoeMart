<?php

namespace App\Http\Controllers;
use App\Models\ProductImage;
use App\Models\VariantAttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     */
    // public function index()
    // {
    //     $products = Product::with('category')->get();
    //     $categories = Category::with('products')->get();
        
    //     return view('product.index', compact('products', 'categories'));

    // }
    public function index(Request $request)
    {
        $sort = $request->input('sort');
        $categoryFilter = $request->input('category');
        $search = $request->input('search');
    
        $query = Product::with('category');
    
        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }
    
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
    

        if ($sort == 'asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'desc') {
            $query->orderBy('price', 'desc');
        }
    
        $products = $query->paginate(10);
        $categories = Category::all();
    
        return view('admin.product.index', compact('products', 'categories'));
    }
 
    
    

    public function create()
    {
        // Lấy danh sách sản phẩm và danh mục
        $categories = Category::all();
    
        // Lấy các giá trị thuộc tính từ bảng variant_attribute_values, phân theo loại thuộc tính
        $colors = VariantAttributeValue::whereHas('variantAttribute', function ($query) {
            $query->where('attribute_name', 'Color');  // Tìm các giá trị có thuộc tính 'Color'
        })->pluck('attribute_value', 'value_id');
    
        $sizes = VariantAttributeValue::whereHas('variantAttribute', function ($query) {
            $query->where('attribute_name', 'Size');  // Tìm các giá trị có thuộc tính 'Size'
        })->pluck('attribute_value', 'value_id');
    
        // Trả về view và truyền dữ liệu
        return view('admin.product.create', compact('categories', 'colors', 'sizes'));
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
            'price_sale' => 'nullable|numeric|min:0|lt:price',
            'category_id' => 'required|exists:categories,category_id',
            'variants' => 'nullable|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sizes' => 'nullable|array',
            'variants.*.sizes.*' => 'required|string',
            'variants.*.size_stock' => 'nullable|array',
            'variants.*.color' => 'nullable|string',
        ]);
    
        // Tạo sản phẩm mới
        $product = Product::create($request->only(['name', 'description', 'price', 'price_sale', 'category_id']));
    
        $totalProductStock = 0; // Tổng stock của sản phẩm (tính từ biến thể)
    
        // Thêm các biến thể cho sản phẩm
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $totalVariantStock = 0; // Tổng stock của biến thể (tính từ size)
    
                // Tính tổng stock từ size
                if (isset($variantData['sizes']) && is_array($variantData['sizes'])) {
                    foreach ($variantData['sizes'] as $size) {
                        $sizeStock = $variantData['size_stock'][$size] ?? 0;
                        $totalVariantStock += $sizeStock;
                    }
                }
    
                // Tạo biến thể mà không lưu `stock`
                $variant = $product->variants()->create([
                    'price' => $variantData['price'],
                    'price_sale' => $variantData['price_sale'] ?? null,
                ]);
    
                // Cập nhật tổng stock sản phẩm
                $totalProductStock += $totalVariantStock;
    
                // Lưu màu sắc mà không lưu stock
                if (!empty($variantData['color'])) {
                    $colorAttribute = VariantAttribute::firstOrCreate(['attribute_name' => 'color']);
                    $variant->variantAttributeValues()->create([
                        'attribute_id' => $colorAttribute->attribute_id,
                        'attribute_value' => $variantData['color'],
                        'stock' => $totalVariantStock, // Cập nhật stock của màu bằng tổng stock của biến thể
                    ]);
                }
    
                // Lưu kích thước và stock của từng kích thước
                if (isset($variantData['sizes']) && is_array($variantData['sizes'])) {
                    $sizeAttribute = VariantAttribute::firstOrCreate(['attribute_name' => 'size']);
                    foreach ($variantData['sizes'] as $size) {
                        $variant->variantAttributeValues()->create([
                            'attribute_id' => $sizeAttribute->attribute_id,
                            'attribute_value' => $size,
                            'stock' => $variantData['size_stock'][$size] ?? 0, // Cập nhật stock cho từng size
                        ]);
                    }
                }
                
                // Cập nhật stock của biến thể bằng tổng stock từ các size
                $variant->update(['stock' => $totalVariantStock]);
            }
        }
    
        // Cập nhật lại stock của sản phẩm với tổng stock từ các biến thể
        $product->update(['stock' => $totalProductStock]);
    
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được tạo thành công.');
    }
    
    
    
    
    
    
    
    
    
    
    

    
    /**
     * Hiển thị chi tiết một sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'variants.attributes', 'images'])->findOrFail($id);
        return view('admin.product.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with(['variants.attributes', 'variants.images'])->findOrFail($id);
        $categories = Category::all(); // Lấy danh sách danh mục để hiển thị trong dropdown
        return view('admin.product.edit', compact('product', 'categories'));
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
            'category_id' => 'sometimes|required|exists:categories,category_id',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,variant_id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.id' => 'nullable|exists:variant_attributes,attribute_id',
            'variants.*.attributes.*.name' => 'required|string|max:50',
            'variants.*.attributes.*.value' => 'required|string|max:100',
            'variants.*.delete' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Cập nhật thông tin sản phẩm
        $product->update($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));
    
        // **Xóa các biến thể được đánh dấu để xóa**
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (isset($variantData['delete']) && $variantData['delete'] == 1 && isset($variantData['id'])) {
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->attributes()->delete(); // Xóa thuộc tính
                        $variant->images()->delete(); // Xóa hình ảnh
                        $variant->delete(); // Xóa biến thể
                    }
                    continue; // Tiếp tục vòng lặp, bỏ qua bước cập nhật
                }
    
                // **Cập nhật hoặc tạo mới biến thể**
                $variant = ProductVariant::updateOrCreate(
                    ['variant_id' => $variantData['id'] ?? null, 
                    'product_id' => $product->product_id],
                    [
                        'price' => $variantData['price'],
                        'price_sale' => $variantData['price_sale'] ?? null,
                        'stock' => $variantData['stock'],
                    ]
                );
    
                // **Cập nhật attributes**
                if (isset($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attributeData) {
                        VariantAttribute::updateOrCreate(
                            [
                                'variant_id' => $variant->variant_id,
                                'attribute_name' => $attributeData['name']
                            ],
                            ['attribute_value' => $attributeData['value']]
                        );
                    }
                }
    
                // **Xử lý ảnh của biến thể**
                if (isset($variantData['images'])) {
                    // Xóa ảnh cũ của biến thể
                    $variant->images()->delete();
    
                    foreach ($variantData['images'] as $image) {
                        if ($image instanceof \Illuminate\Http\UploadedFile) { // Kiểm tra nếu là file ảnh hợp lệ
                            $imagePath = $image->store('public/images'); // Lưu vào storage/app/public/images
                            $imageUrl = str_replace('public/', 'storage/', $imagePath); // Chuyển đổi đường dẫn đúng
                            
                            $variant->images()->create([
                                'image_url' => $imageUrl, // Lưu đường dẫn đúng
                                'type' => 'gallery',
                                'product_id' => $product->product_id,
                            ]);
                        }
                    }
                }
            }
        }
    
        // **Thêm hình ảnh cho sản phẩm chính**
        if ($request->hasFile('images')) {
            // Xóa ảnh cũ của sản phẩm chính
            $product->images()->delete();
    
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/images');
                $product->images()->create([
                    'image_url' => Storage::url($path),
                    'type' => 'gallery',
                    'variant_id' => null // Ảnh của sản phẩm chính
                ]);
            }
        }
    
        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công');
    }
    
    
    
    
/**
 * Xóa sản phẩm.
 */
public function destroy($id)
{
    // Tìm sản phẩm cần xóa
    $product = Product::findOrFail($id);

    // Xóa các hình ảnh liên quan đến sản phẩm và biến thể
    foreach ($product->images as $image) {
        Storage::delete('public/images/' . basename($image->image_url));
        $image->delete();
    }

    foreach ($product->variants as $variant) {
        foreach ($variant->images as $image) {
            Storage::delete('public/images/' . basename($image->image_url));
            $image->delete();
        }
        foreach ($variant->attributes as $attribute) {
            $attribute->delete();
        }
        $variant->delete();
    }

    // Xóa sản phẩm
    $product->delete();

    return redirect()->route('products.index')->with('success', 'Product deleted successfully');
}

public function destroyVariant($id)
{
    $variant = ProductVariant::findOrFail($id);

    // Xóa tất cả thuộc tính liên quan
    $variant->attributes()->delete();

    // Xóa tất cả hình ảnh liên quan
    foreach ($variant->images as $image) {
        Storage::delete('public/images/' . basename($image->image_url));
        $image->delete();
    }

    // Xóa biến thể
    $variant->delete();

    return response()->json(['success' => true, 'message' => 'Biến thể đã được xóa']);
}


}
