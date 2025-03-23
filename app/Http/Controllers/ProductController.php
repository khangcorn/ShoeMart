<?php

namespace App\Http\Controllers;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
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
    
        // Lấy màu sắc và kích cỡ từ variant_attributes
        $colors = DB::table('variant_attributes')
            ->where('attribute_name', 'Color')
            ->get();
    
        $sizes = DB::table('variant_attributes')
            ->where('attribute_name', 'Size')
            ->get();
        
        // Trả về view và truyền dữ liệu
        return view('admin.product.create', compact('categories', 'colors', 'sizes'));
    }
    
    
    /**
     * Tạo sản phẩm mới.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'price_sale' => 'nullable|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,category_id',
            'variants' => 'required|array',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock' => 'required|integer',
        ]);
    
        // Tạo sản phẩm
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => floatval($request->price),
            'price_sale' => floatval($request->price_sale ?? 0),
            'stock' => intval($request->stock),
            'category_id' => $request->category_id,
        ]);
    
        // Xử lý upload ảnh sản phẩm chính
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_url' => $path,
                    'type' => 'main', // Hoặc 'gallery'
                ]);
            }
        }
    
        // Tạo biến thể cho sản phẩm
        foreach ($request->variants as $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $product->product_id,
                'price' => floatval($variantData['price']),
                'price_sale' => floatval($variantData['price_sale'] ?? 0),
                'stock' => intval($variantData['stock']),
            ]);
    
            // Xử lý upload ảnh biến thể
            if (isset($variantData['images']) && is_array($variantData['images'])) {
                foreach ($variantData['images'] as $variantImage) {
                    if ($variantImage instanceof \Illuminate\Http\UploadedFile) {
                        $path = $variantImage->store('variants', 'public');
                        ProductImage::create([
                            'product_id' => $product->product_id,
                            'variant_id' => $variant->variant_id,
                            'image_url' => $path,
                            'type' => 'gallery',
                        ]);
                    }
                }
            }
    
            // Xử lý thuộc tính biến thể (color và size)
            if (isset($variantData['color'])) {
                // Kiểm tra nếu đã có thuộc tính color, nếu chưa tạo mới
                $colorAttribute = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Color',
                    'attribute_value' => $variantData['color'],
                ]);
                
                // Lưu vào bảng variant_attribute_values
                VariantAttributeValue::create([
                    'variant_id' => $variant->variant_id,
                    'attribute_id' => $colorAttribute->attribute_id,
                ]);
            }
    
            if (isset($variantData['size'])) {
                // Kiểm tra nếu đã có thuộc tính size, nếu chưa tạo mới
                $sizeAttribute = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Size',
                    'attribute_value' => $variantData['size'],
                ]);
    
                // Lưu vào bảng variant_attribute_values
                VariantAttributeValue::create([
                    'variant_id' => $variant->variant_id,
                    'attribute_id' => $sizeAttribute->attribute_id,
                ]);
            }
        }
    
        return redirect()->route('products.index')->with('success', 'Sản phẩm và biến thể đã được tạo thành công!');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    /**
     * Hiển thị chi tiết một sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'variants.variantAttributeValues', 'images'])->findOrFail($id);
        return view('admin.product.show', compact('product'));
    }

    public function edit($id)
    {
        // Lấy sản phẩm cùng với các biến thể và thuộc tính của chúng
        $product = Product::with(['variants.variantAttributeValues.variantAttribute', 'category'])->findOrFail($id);
 

        $categories = Category::all();
        // Lấy tất cả các màu sắc và kích thước
        $colors = VariantAttribute::where('attribute_name', 'Color')->get();
        $sizes = VariantAttribute::where('attribute_name', 'Size')->get();
    
        // Trả dữ liệu vào view
        return view('admin.product.edit', compact('product','categories', 'colors', 'sizes'));
    }
    
    /**
     * Cập nhật sản phẩm.
     */
    public function update(Request $request, $id)
    {
        // Tìm sản phẩm
        $product = Product::findOrFail($id);
    
        // Cập nhật thông tin sản phẩm
        $product->update($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));
    
        // Cập nhật ảnh sản phẩm
        if ($request->hasFile('product_images')) {
            ProductImage::where('product_id', $product->product_id)->where('type', 'main')->delete();
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_url' => $path,
                    'type' => 'main',
                ]);
            }
        }
    
        // Cập nhật hoặc thêm mới biến thể
        foreach ($request->variants as $variantData) {
            $variant = ProductVariant::find($variantData['variant_id']);
    
            if ($variant) {
                // Cập nhật thông tin biến thể
                $variant->update([
                    'price' => floatval($variantData['price']),
                    'price_sale' => floatval($variantData['price_sale'] ?? 0),
                    'stock' => intval($variantData['stock']),
                ]);
    
                // Xử lý ảnh biến thể
                if (isset($variantData['images']) && is_array($variantData['images'])) {
                    ProductImage::where('variant_id', $variant->variant_id)->where('type', 'gallery')->delete();
                    foreach ($variantData['images'] as $variantImage) {
                        if ($variantImage instanceof \Illuminate\Http\UploadedFile) {
                            $path = $variantImage->store('variants', 'public');
                            ProductImage::create([
                                'product_id' => $product->product_id,
                                'variant_id' => $variant->variant_id,
                                'image_url' => $path,
                                'type' => 'gallery',
                            ]);
                        }
                    }
                }
              
                // Xóa thuộc tính màu và size cũ để cập nhật lại
                VariantAttributeValue::where('variant_id', $variant->variant_id)->delete();
    
                // Cập nhật thuộc tính biến thể (color)
                if (!empty($variantData['color'])) {
                    $colorAttribute = VariantAttribute::firstOrCreate([
                        'attribute_name' => 'Color',
                        'attribute_value' => $variantData['color'],
                    ]);
    
                    VariantAttributeValue::create([
                        'variant_id' => $variant->variant_id,
                        'attribute_id' => $colorAttribute->attribute_id,
                    ]);
                }
    
                // Cập nhật thuộc tính biến thể (size)
                if (!empty($variantData['size'])) {
                    $sizeAttribute = VariantAttribute::firstOrCreate([
                        'attribute_name' => 'Size',
                        'attribute_value' => $variantData['size'],
                    ]);
    
                    VariantAttributeValue::create([
                        'variant_id' => $variant->variant_id,
                        'attribute_id' => $sizeAttribute->attribute_id,
                    ]);
                }
            }
        }
    
        return redirect()->route('products.index')->with('success', 'Sản phẩm và biến thể đã được cập nhật thành công!');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
/**
 * Xóa sản phẩm.
 */
public function destroy($id)
{
    // Tìm sản phẩm cần xóa
    $product = Product::findOrFail($id);

    // Kiểm tra và xóa các hình ảnh liên quan đến sản phẩm chính (nếu có)
    if ($product->images) {
        foreach ($product->images as $image) {
            // Xóa hình ảnh từ storage
            Storage::delete('public/images/' . basename($image->image_url));
            // Xóa bản ghi hình ảnh trong database
            $image->delete();
        }
    }

    // Kiểm tra và xóa các biến thể và hình ảnh của biến thể (nếu có)
    if ($product->variants) {
        foreach ($product->variants as $variant) {
            // Kiểm tra và xóa hình ảnh liên quan đến biến thể (nếu có)
            if ($variant->images) {
                foreach ($variant->images as $image) {
                    Storage::delete('public/images/' . basename($image->image_url));
                    $image->delete();
                }
            }

            // Kiểm tra và xóa các thuộc tính liên quan đến biến thể (nếu có)
            if ($variant->attributes) {
                foreach ($variant->attributes as $attribute) {
                    $attribute->delete();
                }
            }

            // Xóa biến thể
            $variant->delete();
        }
    }

    // Xóa sản phẩm chính
    $product->delete();

    // Quay lại trang danh sách sản phẩm với thông báo thành công
    return redirect()->route('products.index')->with('success', 'Product deleted successfully');
}
public function deleteVariant(Request $request, $product_id, $variant_id)
{
    try {
        // Tìm sản phẩm theo product_id
        $product = Product::findOrFail($product_id);
        
        // Tìm biến thể liên quan đến sản phẩm
        $variant = $product->variants()->findOrFail($variant_id);

        // Xóa tất cả hình ảnh liên quan nếu có
        if ($variant->images->isNotEmpty()) {
            $imagePaths = $variant->images->pluck('image_url')->toArray();
            
            // Xóa hình ảnh khỏi storage
            Storage::delete($imagePaths);

            // Xóa ảnh khỏi database
            $variant->images()->delete();
        }

        // Xóa tất cả thuộc tính liên quan
        $variant->variantAttributeValues()->delete();

        // Xóa biến thể
        $variant->delete();

        // Trả về JSON thay vì redirect
        return response()->json([
            'success' => true,
            'message' => 'Biến thể đã được xóa thành công.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi xóa biến thể: ' . $e->getMessage()
        ], 500);
    }
}









}
