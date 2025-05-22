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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
    // Lấy tất cả các danh mục (gồm cả gốc và con)
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

public function store(Request $request)
{
    try {
    // Validate dữ liệu đầu vào
    $request->validate([
        'name' => 'required|string|max:255|unique:products,name',
        'description' => 'nullable|string|max:1000',
        'price' => 'required|numeric|min:0',
        'price_sale' => 'nullable|numeric|min:0|lte:price',
        'category_id' => 'required|exists:categories,category_id',
        'product_images' => 'required|array|min:1|max:5',
        'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,bmp,tiff|max:2048',
        'stock' => 'nullable|numeric|min:0',
        'variants' => 'nullable|array',
        'variants.*.images' => 'nullable|array|max:5',
        'variants.*.images.*' => 'nullable|mimes:jpeg,png,jpg,gif,bmp,tiff|max:2048',
    ]);

    $totalStock = 0;

    // Tạo sản phẩm chính (product)
    $product = Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => floatval($request->price),
        'price_sale' => $request->price_sale ?? null,
        'stock' => 0,
        'category_id' => $request->category_id,
    ]);

  // Xử lý ảnh sản phẩm chính
if ($request->hasFile('product_images')) {
    $images = $request->file('product_images');
    
    // Kiểm tra nếu có ít nhất 1 ảnh và tối đa 5 ảnh
    if (count($images) >= 1 && count($images) <= 5) {
        foreach ($images as $index => $image) {
            if ($image->isValid()) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_url' => $path,
                    'type' => 'main', // Gán tất cả ảnh đều là ảnh chính
                ]);
            }
        }
    } else {
        return redirect()->back()->withInput()->withErrors(['product_images' => 'Sản phẩm phải có ít nhất 1 ảnh và tối đa 5 ảnh.']);
    }
}

    // Xử lý biến thể sản phẩm
    $variants = $request->input('variants', []);
    if (is_array($variants) && count($variants) > 0) {
        foreach ($variants as $index => $variantData) {
            if (!empty($variantData['stock'])) {
                $totalStock += intval($variantData['stock']);
            }

            $variant = ProductVariant::create([
                'product_id' => $product->product_id,
                'price' => floatval($variantData['price']),
                'price_sale' => isset($variantData['price_sale']) ? floatval($variantData['price_sale']) : null,
                'stock' => intval($variantData['stock']),
            ]);

            if ($request->hasFile("variant_images_{$index}")) {
                $images = $request->file("variant_images_{$index}");
            
                if (is_array($images)) {
                    foreach ($images as $image) {
                        if ($image && $image->isValid()) {
                            $path = $image->store('variants', 'public');
                            ProductImage::create([
                                'product_id' => $product->product_id,
                                'variant_id' => $variant->variant_id,
                                'image_url' => $path,
                                'type' => 'gallery',
                            ]);
                        }
                    }
                }
            }
            
            

            // Lưu thuộc tính màu sắc
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

            // Lưu thuộc tính size
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

    // Cập nhật stock sản phẩm chính
    if ($totalStock > 0) {
        $product->stock = $totalStock;
    } else {
        $product->stock = $request->input('stock', 0);
    }
    $product->save();

    return response()->json([
        'success' => 'Sản phẩm đã được tạo thành công!',
        'redirect_url' => route('products.index') // Cung cấp URL để redirect khi hoàn tất
    ], 200);

} catch (\Exception $e) {
    // Log lỗi chi tiết
    Log::error('Lỗi tạo sản phẩm: ' . $e->getMessage());

    return response()->json([
        'error' => 'Đã xảy ra lỗi khi tạo sản phẩm. Vui lòng thử lại.',
        'message' => $e->getMessage()
    ], 500);
}
}

    
    public function show($id)
{
    $product = Product::with([
        'category',
        'variants.variantAttributeValues.variantAttribute', 
        'images'
    ])->findOrFail($id);

    $variants = $product->variants->groupBy(function ($variant) {
        // Lấy giá trị attribute_value của màu sắc (color), bảo vệ từng bước truy vấn
        $color = optional(optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'color'))->variantAttribute)->attribute_value;
    
        // Nếu không có màu, trả về 'Không có màu'
        return $color ?? 'Không có màu';
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
            // 'product_images' => 'array|min:1|max:5',  // Kiểm tra số lượng ảnh chính từ 1 đến 5
            // 'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,bmp,tiff|max:2048', // Kiểm tra định dạng và kích thước ảnh
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0|lt:variants.*.price',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.color' => 'required|string|exists:variant_attributes,attribute_value',
            'variants.*.size' => 'required|string|exists:variant_attributes,attribute_value',
            'variants.*.images' => 'nullable|array|max:5', // Kiểm tra ảnh của biến thể
        ]);
        
        
        $invalidImageFound = false;
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif','image/jpg' ];
        
        foreach ($request->file('variants', []) as $variant) {
            if (isset($variant['images']) && is_array($variant['images'])) {
                foreach ($variant['images'] as $image) {
                    if (!$image->isValid()) continue;
        
                    $mime = $image->getMimeType();
                    if (!in_array($mime, $allowedMimeTypes)) {
                        $invalidImageFound = true;
                        break 2; // Thoát khỏi cả 2 vòng lặp nếu phát hiện 1 ảnh sai
                    }
                }
            }
        }
        
        if ($invalidImageFound) {
            return back()
                ->withInput()
                ->withErrors(['image_format' => 'Chỉ cho phép tải ảnh có định dạng jpg, jpeg, png, gif.']);
        }
        
        
    
        // ✅ 1. Tìm sản phẩm cần cập nhật
        $product = Product::findOrFail($id);
    
           // Kiểm tra nếu sản phẩm không có biến thể
    if ($product->variants->isEmpty()) {
        // Cập nhật thông tin sản phẩm và stock trực tiếp từ request
        $product->update($request->only(['name', 'description', 'price', 'price_sale', 'stock', 'category_id']));
    } else {
        // Nếu có biến thể, chỉ cập nhật thông tin sản phẩm mà không thay đổi stock của sản phẩm
        $product->update($request->only(['name', 'description', 'price', 'price_sale', 'category_id']));
    }
                
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
            $variant->price_sale = filled($variantData['price_sale'])
            ? floatval($variantData['price_sale'])
            : null;
            $variant->stock = intval($variantData['stock']);
            $variant->save();
            $variantId = $variant->variant_id;
    
            // ✅ Xóa ảnh cũ của biến thể
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
    
        // ✅ 6. Cập nhật stock cho sản phẩm:
        $hasVariants = $product->variants()->exists();

        if ($hasVariants) {
            // Nếu có biến thể → stock = tổng stock của biến thể
            $product->stock = $product->variants()->sum('stock');
        } else {
            // Nếu không có biến thể → stock = stock nhập vào
            $product->stock = $request->input('stock');
        }

        $product->save();

            
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


