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

        // Validate dữ liệu đầu vào
     
    
        // Tạo sản phẩm
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => floatval($request->price),
            'price_sale' => floatval($request->price_sale ?? 0),
            'stock' => intval($request->stock),
            'category_id' => $request->category_id,
        ]);
    
        // ✅ Xử lý ảnh sản phẩm chính
        if ($request->hasFile('product_images')) {
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
        }
    
        // ✅ Tạo biến thể cho sản phẩm
        $errors = [];
        foreach ($request->variants as $variantData) {
            // 🔥 Kiểm tra biến thể đã tồn tại chưa (Color & Size)
            $existingVariant = ProductVariant::where('product_id', $product->product_id)
                ->whereHas('variantAttributeValues', function ($query) use ($variantData) {
                    $query->whereHas('variantAttribute', function ($subQuery) use ($variantData) {
                        $subQuery->where('attribute_name', 'Color')
                                 ->where('attribute_value', $variantData['color']);
                    });
                })
                ->whereHas('variantAttributeValues', function ($query) use ($variantData) {
                    $query->whereHas('variantAttribute', function ($subQuery) use ($variantData) {
                        $subQuery->where('attribute_name', 'Size')
                                 ->where('attribute_value', $variantData['size']);
                    });
                })
                ->first();
    
            if ($existingVariant) {
                $errors[] = "⚠️ Biến thể với màu **{$variantData['color']}** và size **{$variantData['size']}** đã tồn tại!";
                continue; // ❌ Bỏ qua không tạo mới
            }
    
            // ✅ Nếu không có biến thể trùng, tạo mới
            $variant = ProductVariant::create([
                'product_id' => $product->product_id,
                'price' => floatval($variantData['price']),
                'price_sale' => floatval($variantData['price_sale'] ?? 0),
                'stock' => intval($variantData['stock']),
            ]);
         
            // ✅ Xử lý ảnh biến thể
            if (!empty($variantData['images']) && is_array($variantData['images'])) {
                foreach ($variantData['images'] as $variantImage) {
                    if ($variantImage instanceof \Illuminate\Http\UploadedFile && $variantImage->isValid()) {
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
    
            // ✅ Lưu Color vào bảng variant_attribute_values
          // ✅ Lưu Color vào bảng variant_attribute_values
$colorAttribute = VariantAttribute::firstOrCreate([
    'attribute_name' => 'Color',
    'attribute_value' => $variantData['color'],
]);

$variantAttributeValue = VariantAttributeValue::create([
    'variant_id' => $variant->variant_id,
    'attribute_id' => $colorAttribute->attribute_id,
]);



// ✅ Lưu Size vào bảng variant_attribute_values
$sizeAttribute = VariantAttribute::firstOrCreate([
    'attribute_name' => 'Size',
    'attribute_value' => $variantData['size'],
]);

VariantAttributeValue::create([
    'variant_id' => $variant->variant_id,
    'attribute_id' => $sizeAttribute->attribute_id,
]);

        }
    
        // 🚨 Nếu có lỗi, hiển thị lỗi và quay lại trang
        if (!empty($errors)) {
            return redirect()->back()->withInput()->withErrors(['variants' => $errors]);
        }
    
        return redirect()->route('products.index')->with('success', '✅ Sản phẩm và biến thể đã được tạo thành công!');
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
    
        // Danh sách các biến thể đã tồn tại để kiểm tra trùng lặp
        $existingVariants = ProductVariant::where('product_id', $product->product_id)
            ->with(['attributes'])
            ->get()
            ->map(function ($variant) {
                $color = $variant->attributes->where('attribute_name', 'Color')->first();
                $size = $variant->attributes->where('attribute_name', 'Size')->first();
                return [
                    'variant_id' => $variant->variant_id,
                    'color' => $color ? $color->attribute_value : null,
                    'size' => $size ? $size->attribute_value : null
                ];
            });
    
        // Cập nhật hoặc thêm mới biến thể
        foreach ($request->variants as $variantData) {
            $colorValue = $variantData['color'] ?? null;
            $sizeValue = $variantData['size'] ?? null;
    
            // Kiểm tra biến thể có bị trùng không
            $isDuplicate = $existingVariants->contains(function ($variant) use ($colorValue, $sizeValue, $variantData) {
                return $variant['color'] === $colorValue && $variant['size'] === $sizeValue &&
                       (!isset($variantData['variant_id']) || $variant['variant_id'] !== $variantData['variant_id']);
            });
    
            if ($isDuplicate) {
                return back()->withErrors(['variants' => "Biến thể với màu $colorValue và size $sizeValue đã tồn tại."]);
            }
    
            // Kiểm tra nếu variant_id tồn tại
            if (isset($variantData['variant_id'])) {
                $variant = ProductVariant::find($variantData['variant_id']);
            } else {
                // Nếu không có variant_id, tạo mới
                $variant = new ProductVariant();
                $variant->product_id = $product->product_id;
            }
    
            // Cập nhật biến thể
            $variant->price = floatval($variantData['price']);
            $variant->price_sale = floatval($variantData['price_sale'] ?? 0);
            $variant->stock = intval($variantData['stock']);
            $variant->save();
    
            // Cập nhật ảnh biến thể
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
    
            // Xóa thuộc tính cũ
            VariantAttributeValue::where('variant_id', $variant->variant_id)->delete();
    
            // Thêm thuộc tính mới (Color)
            if (!empty($colorValue)) {
                $colorAttribute = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Color',
                    'attribute_value' => $colorValue,
                ]);
    
                VariantAttributeValue::create([
                    'variant_id' => $variant->variant_id,
                    'attribute_id' => $colorAttribute->attribute_id,
                ]);
            }
    
            // Thêm thuộc tính mới (Size)
            if (!empty($sizeValue)) {
                $sizeAttribute = VariantAttribute::firstOrCreate([
                    'attribute_name' => 'Size',
                    'attribute_value' => $sizeValue,
                ]);
    
                VariantAttributeValue::create([
                    'variant_id' => $variant->variant_id,
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

}
