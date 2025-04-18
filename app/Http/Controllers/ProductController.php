<?php

namespace App\Http\Controllers;

use App\Models\CartDetail;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort');
        $categoryFilter = $request->input('category');
        $search = $request->input('search');
    
        $query = Product::with(['category', 'mainImage']); // Lấy cả ảnh chính
    
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
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'product_images' => 'required|array|min:1',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    
        try {
            // Tạo sản phẩm mới
            $product = Product::create([
                'name' => $request->name,
                'price' => floatval($request->price),
                'stock' => intval($request->stock),
                'category_id' => $request->category_id,
            ]);
    
            // Xử lý ảnh sản phẩm chính
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $image) {
                    if ($image->isValid()) {
                        // Lưu ảnh vào thư mục public
                        $path = $image->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->product_id,
                            'image_url' => $path,
                            'type' => 'main',
                        ]);
                    } else {
                        return back()->with('error', 'Một số ảnh không hợp lệ!');
                    }
                }
            }
    
            return redirect()->route('products.index')->with('success', 'Sản phẩm đã được tạo thành công!');
            
        } catch (\Exception $e) {
            // Xử lý lỗi
            return back()->with('error', 'Đã có lỗi xảy ra khi lưu ảnh: ' . $e->getMessage());
        }
    }
    
    
    public function show($id)
{
    $product = Product::with([
        'category',
        'variants.variantAttributeValues.variantAttribute', 
        'images'
    ])->findOrFail($id);

    // Nhóm biến thể theo màu
    $variants = $product->variants->groupBy(function ($variant) {
        return optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'color'))->variantAttribute->attribute_value;
    });

    return view('admin.product.show', compact('product', 'variants'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
    
            // Validation cho các biến thể
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0|lt:variants.*.price',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.color' => 'required|string|exists:variant_attributes,attribute_value',
            'variants.*.size' => 'required|string|exists:variant_attributes,attribute_value',
            'variants.*.images' => 'nullable|array|max:5',
            'variants.*.images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // giới hạn 5 ảnh, tối đa 2MB
        ]);
        // ✅ 1. Tìm sản phẩm cần cập nhật
        $product = Product::findOrFail($id);
    
        // ✅ 2. Cập nhật thông tin sản phẩm
        $product->update($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));
    
        // ✅ 3. Xử lý ảnh sản phẩm chính
        if ($request->hasFile('product_images')) {
            // Xóa ảnh chính cũ
            $oldMainImages = ProductImage::where('product_id', $product->product_id)->where('type', 'main')->get();
            foreach ($oldMainImages as $oldImage) {
                Storage::delete('public/' . $oldImage->image_url);
            }
            ProductImage::where('product_id', $product->product_id)->where('type', 'main')->delete();
    
            // Lưu ảnh chính mới
            foreach ($request->file('product_images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('public/products');
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_url' => str_replace('public/', '', $path),
                        'type' => 'main',
                    ]);
                }
            }
        }
    
        // ✅ 4. Xử lý biến thể sản phẩm
        $variants = $request->input('variants', []);
        foreach ($variants as $index => $variantData) {
            // ✅ Kiểm tra nếu biến thể đã tồn tại hoặc cần tạo mới
            $variant = !empty($variantData['variant_id']) 
            ? ProductVariant::find($variantData['variant_id']) 
            : new ProductVariant(['product_id' => $product->product_id]);
        
            // ✅ Cập nhật thông tin biến thể
            $variant->price = floatval($variantData['price']);
            $variant->price_sale = floatval($variantData['price_sale'] ?? 0);
            $variant->stock = intval($variantData['stock']);
            $variant->save(); // Lưu biến thể
            $variantId = $variant->variant_id; // Lấy ID của biến thể
    
            // ✅ Xóa ảnh cũ của biến thể
       // ✅ Kiểm tra nếu có ảnh mới thì mới xóa ảnh cũ
if ($request->hasFile("variants.{$index}.images")) {
    $oldVariantImages = ProductImage::where('variant_id', $variantId)->get();
    foreach ($oldVariantImages as $oldImage) {
        Storage::delete('public/' . $oldImage->image_url);
    }
    ProductImage::where('variant_id', $variantId)->delete();
}

    
            // ✅ Lưu ảnh biến thể mới (nếu có)
            if ($request->hasFile("variants.{$index}.images")) {
                foreach ($request->file("variants.{$index}.images") as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('public/variants');
                        ProductImage::create([
                            'product_id' => $product->product_id,
                          'variant_id' => $variant->variant_id,
                            'image_url' => str_replace('public/', '', $path),
                            'type' => 'gallery',
                        ]);
                    }
                }
            }
    
            // ✅ 5. Cập nhật thuộc tính biến thể (Color & Size)
            VariantAttributeValue::where('variant_id', $variantId)->delete();
    
            if (!empty($variantData['color'])) {
                $colorAttribute = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Color',
                    'attribute_value' => $variantData['color'],
                ]);
                VariantAttributeValue::create([
                    'variant_id' => $variantId,
                    'attribute_id' => $colorAttribute->attribute_id,
                ]);
            }
    
            if (!empty($variantData['size'])) {
                $sizeAttribute = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Size',
                    'attribute_value' => $variantData['size'],
                ]);
                VariantAttributeValue::create([
                    'variant_id' => $variantId,
                    'attribute_id' => $sizeAttribute->attribute_id,
                ]);
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
public function count()
{
    $userId = Auth::id();
    $cartCount = CartDetail::whereHas('cart', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })->sum('quantity');

    return response()->json(['count' => $cartCount]);
}

}
