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
            'stock' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,category_id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants' => 'nullable|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:1',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.name' => 'required|string',
            'variants.*.attributes.*.value' => 'required|string',
            'variants.*.sizes' => 'nullable|array',
            'variants.*.sizes.*' => 'required|string',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá phải là một số.',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
            'price_sale.numeric' => 'Giá khuyến mãi phải là một số.',
            'price_sale.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'price_sale.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'stock.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'stock.integer' => 'Số lượng phải là một số nguyên.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
        ]);
    
        // Tạo sản phẩm mới
        $product = Product::create($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));

        // Thêm các biến thể cho sản phẩm
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                // Tạo biến thể
                $variant = $product->variants()->create([
                    'price' => $variantData['price'],
                    'price_sale' => $variantData['price_sale'] ?? null,
                    'stock' => $variantData['stock'],
                ]);
    
                // Thêm các thuộc tính cho biến thể
                if (isset($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attributeData) {
                        // Tạo hoặc tìm thuộc tính
                        $attribute = VariantAttribute::firstOrCreate(
                            ['attribute_name' => $attributeData['name']] // Dùng attribute_name thay vì name
                        );
    
                        // Thêm giá trị thuộc tính vào bảng variant_attribute_values
                        $variant->variantAttributeValues()->create([  // Đảm bảo dùng đúng tên quan hệ
                            'attribute_id' => $attribute->attribute_id, // Lưu attribute_id vào bảng variant_attribute_values
                            'attribute_value' => $attributeData['value'],
                            'stock' => $variantData['stock'], // Bạn có thể thay đổi nếu cần
                        ]);
                    }
                }
             // Thêm màu sắc (color) cho biến thể
if (isset($variantData['color'])) {
    // Tạo hoặc tìm thuộc tính màu sắc (color)
    $colorAttribute = VariantAttribute::firstOrCreate([
        'attribute_name' => 'color'
    ]);

    // Thêm giá trị màu sắc vào bảng variant_attribute_values
    $variant->variantAttributeValues()->create([
        'attribute_id' => $colorAttribute->attribute_id,
        'attribute_value' => $variantData['color'],
        'stock' => $variantData['stock'] ?? 0, // Hoặc bạn có thể thay đổi cách xử lý stock
    ]);
}

    
                // Thêm kích thước (size) cho biến thể
                if (isset($variantData['sizes']) && is_array($variantData['sizes'])) {
                    foreach ($variantData['sizes'] as $size) {
                        $attribute = VariantAttribute::firstOrCreate([
                            'attribute_name' => 'size'
                        ]);
    
                        $variant->variantAttributeValues()->create([  // Đảm bảo dùng đúng tên quan hệ
                            'attribute_id' => $attribute->attribute_id,
                            'attribute_value' => $size,
                            'stock' => $variantData['stock'], // Cũng có thể thay đổi nếu cần
                        ]);
                    }
                }
    
                // Thêm hình ảnh cho biến thể (nếu có)
                if (isset($variantData['images']) && is_array($variantData['images'])) {
                    foreach ($variantData['images'] as $image) {
                        // Lưu ảnh và lấy đường dẫn
                        $imagePath = $image->store('images', 'public');
    
                        // Lưu thông tin ảnh vào bảng ProductImage
                        ProductImage::create([
                            'product_id' => $product->product_id,
                            'variant_id' => $variant->variant_id, // Gán ảnh cho biến thể
                            'image_url' => $imagePath,
                            'type' => 'gallery', // Loại ảnh là của biến thể
                        ]);
                    }
                }
            }
        }
    
        // Quay lại danh sách sản phẩm với thông báo thành công
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
