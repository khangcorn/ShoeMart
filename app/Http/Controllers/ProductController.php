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
            'images' => 'nullable|array', // Thêm ảnh sản phẩm chính
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Giới hạn file ảnh
            'variants.*.images' => 'nullable|array', // Thêm ảnh cho biến thể
            'variants.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);
    
        // Tạo sản phẩm mới
        $product = Product::create($request->only(['name', 'description', 'price', 'price_sale', 'category_id']));
    
        $totalProductStock = 0; // Tổng stock của sản phẩm (tính từ biến thể)
    
        // Lưu ảnh cho sản phẩm chính
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('public/images'); // Lưu ảnh vào storage
                $imageUrl = str_replace('public/', 'storage/', $imagePath); // Chuyển đường dẫn storage
    
                $product->images()->create([
                    'image_url' => $imageUrl,
                    'type' => 'gallery',
                    'variant_id' => null, // Ảnh của sản phẩm chính
                ]);
            }
        }
    
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
    
                // **Lưu ảnh cho biến thể**
                if (isset($variantData['images'])) {
                    foreach ($variantData['images'] as $image) {
                        if ($image instanceof \Illuminate\Http\UploadedFile) { // Kiểm tra nếu là file ảnh hợp lệ
                            $imagePath = $image->store('public/images'); // Lưu vào storage
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
        $product = Product::with(['variants.variantAttributeValues', 'variants.images'])->findOrFail($id);

        $categories = Category::all();
        return view('admin.product.edit', compact('product', 'categories'));
    }
    
    /**
     * Cập nhật sản phẩm.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        // Cập nhật thông tin sản phẩm chính
        $product->update($request->only(['name', 'price', 'price_sale', 'stock', 'category_id']));
    
        // Biến để lưu tổng tồn kho của tất cả các màu sắc
        $totalProductStock = 0;
    
        // Kiểm tra và cập nhật biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                // Kiểm tra nếu có trường 'delete' trong variant
                if (!empty($variantData['delete']) && isset($variantData['variant_id'])) {
                    $variant = ProductVariant::find($variantData['variant_id']);
                    if ($variant) {
                        $variant->attributes()->delete(); // Xóa thuộc tính
                        $variant->images()->delete(); // Xóa hình ảnh
                        $variant->delete(); // Xóa biến thể
                    }
                    continue;
                }
    
                // Tìm biến thể nếu có
                $variant = ProductVariant::where('variant_id', $variantData['variant_id'] ?? null)
                    ->where('product_id', $product->product_id)
                    ->first();
    
                if ($variant) {
                    // Cập nhật thông tin biến thể
                    $variant->update([
                        'price' => $variantData['price'],
                        'price_sale' => $variantData['price_sale'] ?? null,
                        'color' => $variantData['color'] ?? null,
                    ]);
    
                    // Cập nhật kích thước và tồn kho
                    $totalColorStock = 0; // Biến để lưu tổng stock của màu
    
                    if (isset($variantData['sizes']) && isset($variantData['size_stock'])) {
                        foreach ($variantData['sizes'] as $size) {
                            $newStock = $variantData['size_stock'][$size] ?? 0;
    
                            // Tìm hoặc tạo mới thuộc tính size
                            $attribute = VariantAttribute::updateOrCreate(
                                ['attribute_name' => 'size'],
                                ['attribute_id' => VariantAttribute::firstOrCreate(['attribute_name' => 'size'])->id]
                            );
    
                            // Kiểm tra xem giá trị thuộc tính đã tồn tại chưa
                            $variantAttributeValue = VariantAttributeValue::where('variant_id', $variant->variant_id)
                                ->where('attribute_id', $attribute->attribute_id)
                                ->where('attribute_value', $size)
                                ->first();
    
                            if ($variantAttributeValue) {
                                $variantAttributeValue->update(['stock' => $newStock]);
                            } else {
                                VariantAttributeValue::create([
                                    'variant_id' => $variant->variant_id,
                                    'attribute_id' => $attribute->attribute_id,
                                    'attribute_value' => $size,
                                    'stock' => $newStock
                                ]);
                            }
    
                            // Cộng dồn số lượng tồn kho cho màu
                            $totalColorStock += $newStock;
                        }
                    }
    
                    // Cập nhật màu sắc nếu có thay đổi
                    if ($variantData['color']) {
                        $colorAttribute = VariantAttribute::firstOrCreate(['attribute_name' => 'color']);
    
                        // Kiểm tra xem màu có tồn tại trong bản ghi variant_attribute_value không
                        $colorAttributeValue = VariantAttributeValue::where('variant_id', $variant->variant_id)
                            ->where('attribute_id', $colorAttribute->attribute_id)
                            ->first();
    
                        if ($colorAttributeValue) {
                            // Nếu màu đã tồn tại, cập nhật lại màu sắc và tồn kho
                            $colorAttributeValue->update([
                                'attribute_value' => $variantData['color'],
                                'stock' => $totalColorStock
                            ]);
                        } else {
                            // Nếu không có màu cũ, tạo mới bản ghi màu
                            VariantAttributeValue::create([
                                'variant_id' => $variant->variant_id,
                                'attribute_id' => $colorAttribute->attribute_id,
                                'attribute_value' => $variantData['color'],
                                'stock' => $totalColorStock
                            ]);
                        }
    
                        // Cập nhật tồn kho của biến thể bằng tổng tồn kho của màu
                        $variant->update(['stock' => $totalColorStock]);
                    }
    
                    // Cộng dồn tồn kho của tất cả các màu vào tổng tồn kho sản phẩm
                    $totalProductStock += $totalColorStock;
                }
            }
        }
    
        // Cập nhật tồn kho cho sản phẩm chính bằng tổng tồn kho của tất cả màu
        $product->update(['stock' => $totalProductStock]);
    
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
