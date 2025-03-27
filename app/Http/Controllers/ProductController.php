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
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate từng ảnh
            'variants' => 'required|array',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock' => 'required|integer',
            'variants.*.images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate từng ảnh biến thể
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
    
        // ✅ Xử lý upload nhiều ảnh sản phẩm chính
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('products', 'public'); // Lưu vào storage/app/public/products
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_url' => $path,
                        'type' => 'main',
                    ]);
                }
            }
        }
    
        // ✅ Tạo biến thể cho sản phẩm
        foreach ($request->variants as $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $product->product_id,
                'price' => floatval($variantData['price']),
                'price_sale' => floatval($variantData['price_sale'] ?? 0),
                'stock' => intval($variantData['stock']),
            ]);
    
            // ✅ Xử lý upload nhiều ảnh cho biến thể
            if (!empty($variantData['images']) && is_array($variantData['images'])) {
                foreach ($variantData['images'] as $variantImage) {
                    if ($variantImage instanceof \Illuminate\Http\UploadedFile && $variantImage->isValid()) {
                        $path = $variantImage->store('variants', 'public'); // Lưu vào storage/app/public/variants
                        ProductImage::create([
                            'product_id' => $product->product_id,
                            'variant_id' => $variant->variant_id,
                            'image_url' => $path,
                            'type' => 'gallery',
                        ]);
                    }
                }
            }
    
            // ✅ Xử lý thuộc tính biến thể (color & size)
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
    
        return redirect()->route('products.index')->with('success', 'Sản phẩm và biến thể đã được tạo thành công!');
    }
    
    
    /**
     * Hiển thị chi tiết một sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'variants.variantAttributeValues', 'images'])->findOrFail($id);
        dd($product);  // Kiểm tra kết quả trước khi trả về view
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
    
        // Kiểm tra nếu request có biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                // Kiểm tra nếu variant_id tồn tại
                $variant = isset($variantData['variant_id']) ? ProductVariant::find($variantData['variant_id']) : new ProductVariant();
                
                if (!$variant) {
                    $variant = new ProductVariant();
                    $variant->product_id = $product->product_id;
                }
    
                // Cập nhật hoặc tạo mới biến thể
                $variant->price = floatval($variantData['price'] ?? 0);
                $variant->price_sale = floatval($variantData['price_sale'] ?? 0);
                $variant->stock = intval($variantData['stock'] ?? 0);
                $variant->save();
    
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
    
                // Xóa thuộc tính màu và size cũ
                VariantAttributeValue::where('variant_id', $variant->variant_id)->delete();
    
                // Cập nhật thuộc tính biến thể
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
    
    
    public function destroy($id)
    {
        // Tìm sản phẩm cần xóa
        $product = Product::findOrFail($id);
    
        // Xóa các bản ghi variant_attribute_values của sản phẩm trước khi xóa sản phẩm
        DB::table('variant_attribute_values')
            ->whereIn('variant_id', $product->variants->pluck('variant_id'))
            ->delete();
    
        // Tiếp tục với việc xóa hình ảnh, biến thể, và sản phẩm chính
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::delete('public/images/' . basename($image->image_url));
                $image->delete();
            }
        }
    
        if ($product->variants) {
            foreach ($product->variants as $variant) {
                if ($variant->images) {
                    foreach ($variant->images as $image) {
                        Storage::delete('public/images/' . basename($image->image_url));
                        $image->delete();
                    }
                }
    
                $variant->delete();
            }
        }
    
        $product->delete();
    
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công!');
    }
    public function deleteVariant($productId, $variantId)
{
    try {
        // Lấy biến thể theo productId và variantId
        $variant = ProductVariant::where('product_id', $productId)->where('variant_id', $variantId)->first();
        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Biến thể không tồn tại.'], 404);
        }

        // Xóa biến thể
        $variant->delete();

        return response()->json(['success' => true, 'message' => 'Biến thể đã được xóa thành công.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra.'], 500);
    }
}

}
