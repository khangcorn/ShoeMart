<?php

namespace App\Http\Controllers;

use App\Models\CartDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            $query->where('name', 'like', '%'.$search.'%');
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
    DB::beginTransaction();
    try {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,category_id',
            'product_images' => 'required|array|min:1|max:5',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,bmp,tiff|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.price_sale' => 'nullable|numeric|min:0|lte:variants.*.price',
            'variants.*.stock' => 'required|numeric|min:0',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.images' => 'nullable|array|max:5',
            'variants.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,bmp,tiff|max:2048',
        ]);

        $totalStock = 0;
        $priceList = [];
        $discountList = [];

        // Tạo sản phẩm chính
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => 0, // Tạm thời
            'price_sale' => null,
            'stock' => 0,
            'category_id' => $request->category_id,
        ]);

        // Ảnh sản phẩm chính
        foreach ($request->file('product_images') as $image) {
            if ($image->isValid()) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->product_id,
                    'image_url' => $path,
                    'type' => 'main',
                ]);
            }
        }

        // Thêm các biến thể
        foreach ($request->variants as $index => $variantData) {
            $variant = ProductVariant::create([
                'product_id' => $product->product_id,
                'price' => $variantData['price'],
                'price_sale' => $variantData['price_sale'] ?? null,
                'stock' => $variantData['stock'],
            ]);

            $totalStock += intval($variantData['stock']);
            $priceList[] = floatval($variantData['price']);

            if (!empty($variantData['price_sale'])) {
                $discountList[] = floatval($variantData['price_sale']);
            }

            // Ảnh biến thể
            if ($request->hasFile("variant_images_{$index}")) {
                foreach ($request->file("variant_images_{$index}") as $image) {
                    if ($image->isValid()) {
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

            // Thuộc tính màu
            if (!empty($variantData['color'])) {
                $color = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Color',
                    'attribute_value' => $variantData['color'],
                ]);
                VariantAttributeValue::create([
                    'variant_id' => $variant->variant_id,
                    'attribute_id' => $color->attribute_id,
                ]);
            }

            // Thuộc tính size
            if (!empty($variantData['size'])) {
                $size = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Size',
                    'attribute_value' => $variantData['size'],
                ]);
                VariantAttributeValue::create([
                    'variant_id' => $variant->variant_id,
                    'attribute_id' => $size->attribute_id,
                ]);
            }
        }

        // ✅ Cập nhật giá và số lượng vào product
        $product->stock = $totalStock;

        if (!empty($discountList)) {
            $product->price = min($discountList); // Giá khuyến mãi thấp nhất
            $product->price_sale = min($discountList);
        } else {
            $product->price = min($priceList); // Giá gốc thấp nhất
            $product->price_sale = null;
        }

        $product->save();

        DB::commit();

        return response()->json([
            'success' => 'Sản phẩm đã được tạo thành công!',
            'redirect_url' => route('products.index'),
        ], 200);

    } catch (\Exception $e) {
    DB::rollBack();
    Log::error('Lỗi tạo sản phẩm: ' . $e->getMessage());

    return response()->json([
        'error' => true,
        'message' => 'Đã xảy ra lỗi khi tạo sản phẩm: ' . $e->getMessage()
    ], 500);
}


}


    public function show($id)
{
    $product = Product::with([
        'category',
        'variants.variantAttributeValues.variantAttribute',
        'images',
    ])->findOrFail($id);

    $variants = $product->variants->groupBy(function ($variant) {
        $color = optional(optional($variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'color'))->variantAttribute)->attribute_value;
        return $color ?? 'Không có màu';
    });

    // Lấy danh sách product_id đã yêu thích của user đang đăng nhập
    $wishlistedProductIds = [];
    if (auth()->check()) {
        $wishlistedProductIds = Wishlist::where('user_id', auth()->id())
            ->pluck('product_id')
            ->toArray();
    }

    return view('admin.product.show', compact('product', 'variants', 'wishlistedProductIds'));
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
        return view('admin.product.edit', compact('product', 'categories', 'colors', 'sizes'));
    }

    /**
     * Cập nhật sản phẩm.
     */
 public function update(Request $request, $id)
{
    // ✅ 1. Validate cơ bản
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,category_id',
        'product_images' => 'array|min:1|max:5',
        'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,bmp,tiff|max:2048',

        'variants' => 'required|array|min:1',
        'variants.*.price' => 'required|numeric|min:0',
        'variants.*.price_sale' => 'nullable|numeric|min:0',
        'variants.*.stock' => 'required|integer|min:0',
        'variants.*.color' => 'required|string|exists:variant_attributes,attribute_value',
        'variants.*.size' => 'required|string|exists:variant_attributes,attribute_value',
        'variants.*.images' => 'nullable|array|max:5',
        'variants.*.images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
    ]);

    // ✅ 2. Kiểm tra logic: Giá khuyến mãi < Giá gốc
    foreach ($request->variants as $index => $variant) {
        $price = $variant['price'] ?? 0;
        $priceSale = $variant['price_sale'] ?? null;
        if ($priceSale !== null && $priceSale >= $price) {
            return back()->withInput()->withErrors([
                "variants.$index.price_sale" => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            ]);
        }
    }

    // ✅ 3. Cập nhật sản phẩm
    $product = Product::findOrFail($id);
    $product->update($request->only(['name', 'description', 'category_id']));

    // ✅ 4. Cập nhật ảnh chính
    if ($request->hasFile('product_images')) {
        $oldMainImages = ProductImage::where('product_id', $product->product_id)->where('type', 'main')->get();
        foreach ($oldMainImages as $oldImage) {
            Storage::delete('public/' . $oldImage->image_url);
        }
        ProductImage::where('product_id', $product->product_id)->where('type', 'main')->delete();

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

    // ✅ 5. Cập nhật biến thể
    $variants = $request->input('variants', []);
    foreach ($variants as $index => $variantData) {
        $variant = !empty($variantData['variant_id'])
            ? ProductVariant::find($variantData['variant_id'])
            : new ProductVariant(['product_id' => $product->product_id]);

        $variant->price = floatval($variantData['price']);
        $variant->price_sale = filled($variantData['price_sale']) ? floatval($variantData['price_sale']) : null;
        $variant->stock = intval($variantData['stock']);
        $variant->save();
        $variantId = $variant->variant_id;

        // ✅ 5.1 Xử lý ảnh biến thể
        if ($request->hasFile("variants.{$index}.images")) {
            // Xóa ảnh cũ
            $oldVariantImages = ProductImage::where('variant_id', $variantId)->get();
            foreach ($oldVariantImages as $oldImage) {
                Storage::delete('public/' . $oldImage->image_url);
            }
            ProductImage::where('variant_id', $variantId)->delete();

            foreach ($request->file("variants.{$index}.images") as $image) {
                if ($image->isValid()) {
                    $path = $image->store('public/variants');
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'variant_id' => $variantId,
                        'image_url' => str_replace('public/', '', $path),
                        'type' => 'gallery',
                    ]);
                }
            }
        }

        // ✅ 5.2 Xử lý thuộc tính màu/kích cỡ
VariantAttributeValue::where('variant_id', $variant->variant_id)->delete();

// ✅ Gắn thuộc tính màu
if (!empty($variantData['color'])) {
    $colorValue = trim($variantData['color']);
    
    $colorAttr = VariantAttribute::where('attribute_name', 'Color')
        ->where('attribute_value', $colorValue)
        ->first();

    if (!$colorAttr) {
        $colorAttr = VariantAttribute::create([
            'attribute_name' => 'Color',
            'attribute_value' => $colorValue,
        ]);
    }

    if ($colorAttr) {
        VariantAttributeValue::create([
            'variant_id' => $variant->variant_id,
            'attribute_id' => $colorAttr->attribute_id,
        ]);
    }
}

// ✅ Gắn thuộc tính size
if (!empty($variantData['size'])) {
    $sizeValue = trim($variantData['size']);
    
    $sizeAttr = VariantAttribute::where('attribute_name', 'Size')
        ->where('attribute_value', $sizeValue)
        ->first();

    if (!$sizeAttr) {
        $sizeAttr = VariantAttribute::create([
            'attribute_name' => 'Size',
            'attribute_value' => $sizeValue,
        ]);
    }

    if ($sizeAttr) {
        VariantAttributeValue::create([
            'variant_id' => $variant->variant_id,
            'attribute_id' => $sizeAttr->attribute_id,
        ]);
    }
}


    }

    // ✅ 6. Cập nhật lại giá và stock sản phẩm chính
    $allVariants = $product->variants()->get();
    $minPrice = $allVariants->min(function ($variant) {
        return filled($variant->price_sale) ? $variant->price_sale : $variant->price;
    });
    $totalStock = $allVariants->sum('stock');

    $product->update([
        'price' => $minPrice,
        'stock' => $totalStock,
    ]);

    return redirect()->route('products.index')->with('success', 'Sản phẩm và biến thể đã được cập nhật thành công!');
}



  public function destroy($id)
{
    $product = Product::findOrFail($id);

    // Kiểm tra nếu bất kỳ biến thể nào tồn tại trong order_details thì không cho xóa
    foreach ($product->variants as $variant) {
        $existsInOrderDetails = \App\Models\OrderDetail::where('variant_id', $variant->variant_id)->exists();
        if ($existsInOrderDetails) {
            return redirect()->back()->with('error', "Không thể xóa sản phẩm vì biến thể của sản phẩm đã có đơn hàng liên quan.");
        }
    }

    // Nếu không có biến thể nào bị ràng buộc, xóa các bản ghi variant_attribute_values
    DB::table('variant_attribute_values')
        ->whereIn('variant_id', $product->variants->pluck('variant_id'))
        ->delete();

    // Xóa hình ảnh sản phẩm
    if ($product->images) {
        foreach ($product->images as $image) {
            Storage::delete('public/images/'.basename($image->image_url));
            $image->delete();
        }
    }

    // Xóa ảnh và biến thể
    foreach ($product->variants as $variant) {
        if ($variant->images) {
            foreach ($variant->images as $image) {
                Storage::delete('public/images/'.basename($image->image_url));
                $image->delete();
            }
        }
        $variant->delete();
    }

    // Xóa sản phẩm chính
    $product->delete();

    return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công!');
}

public function deleteVariant($productId, $variantId)
{
    try {
        $variant = ProductVariant::where('product_id', $productId)->where('variant_id', $variantId)->first();

        if (! $variant) {
            return redirect()->route('products.index')->with('error', 'Biến thể không tồn tại.');
        }

        $existsInOrderDetails = \App\Models\OrderDetail::where('variant_id', $variantId)->exists();

        if ($existsInOrderDetails) {
            return redirect()->route('products.index')->with('error', 'Không thể xóa biến thể này vì đã có đơn hàng liên quan.');
        }

        $variant->delete();

        return redirect()->route('products.index')->with('success', 'Biến thể đã được xóa thành công.');
    } catch (\Exception $e) {
        return redirect()->route('products.index')->with('error', 'Đã có lỗi xảy ra khi xóa biến thể.');
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
