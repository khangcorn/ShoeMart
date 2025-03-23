<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Hiển thị form tạo danh mục mới.
     */
    public function create()
    {
        // Lấy danh sách danh mục cha để hiển thị trong dropdown
        $categories = Category::all();
        return view('admin.category.create', compact('categories'));
    }

    /**
     * Tạo danh mục mới.
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu nhập vào
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
                'regex:/^[^\d]+$/', // Không cho phép số
            ],
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Xác thực ảnh
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại, vui lòng chọn tên khác.',
            'name.regex' => 'Tên danh mục không được chứa số.',
            'image_url.image' => 'File tải lên phải là ảnh.',
            'image_url.mimes' => 'Chỉ chấp nhận các định dạng ảnh: jpeg, png, jpg, gif.',
            'image_url.max' => 'Ảnh không được vượt quá 2MB.',
        ]);
    
        // Xử lý tải ảnh lên nếu có
        if ($request->hasFile('image_url')) {
            $image = $request->file('image_url');
            $imagePath = $image->store('categories', 'public'); // Lưu ảnh vào thư mục 'storage/app/public/categories'
        } else {
            $imagePath = null; // Nếu không có ảnh, gán null
        }
    
        // Kiểm tra giá trị của $imagePath
     
    
        // Tạo danh mục mới
        Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?? null,
            'image_url' => $imagePath, // Lưu đường dẫn ảnh vào cơ sở dữ liệu
        ]);
    
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully');
    }
    

    /**
     * Hiển thị chi tiết một danh mục.
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id); // Lấy thông tin danh mục theo ID
        $categories = Category::whereNull('parent_id')->get(); // Lấy các danh mục không có parent (danh mục cha)
    
        return view('admin.category.edit', compact('category', 'categories'));
    }
    

    /**
     * Cập nhật danh mục.
     */
    public function update(Request $request, $id)
    {
        // Xác thực dữ liệu nhập vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Xác thực ảnh nếu có
        ]);

        // Tìm danh mục theo ID
        $category = Category::findOrFail($id);

        // Xử lý tải ảnh lên nếu có
        if ($request->hasFile('image_url')) {
            // Xóa ảnh cũ nếu có
            if ($category->image_url) {
                Storage::delete('public/' . $category->image_url); // Xóa ảnh cũ
            }
            $image = $request->file('image_url');
            $imagePath = $image->store('categories', 'public'); // Lưu ảnh vào thư mục 'storage/app/public/categories'
        } else {
            $imagePath = $category->image_url; // Giữ nguyên ảnh cũ nếu không tải ảnh mới
        }

        // Cập nhật danh mục
        $category->update([
            'name' => $validated['name'],
            'parent_id' => $request->parent_id ?? null,
            'image_url' => $imagePath, // Cập nhật đường dẫn ảnh nếu có
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Xóa danh mục.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Xóa ảnh nếu có
        if ($category->image_url) {
            Storage::delete('public/' . $category->image_url); // Xóa ảnh
        }

        // Xóa danh mục
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
