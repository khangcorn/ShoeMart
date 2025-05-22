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
    public function index(Request $request)
    {
        $query = Category::query();
    
        // Nếu có từ khóa tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        // Lấy toàn bộ danh mục hoặc đã lọc
        $categories = $query->get();
    
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
            'description' => 'nullable|string|max:1000', // Thêm validate cho mô tả
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Xác thực ảnh
        ]);
    
        // Xử lý tải ảnh lên nếu có
        $imagePath = $request->hasFile('image_url') 
            ? $request->file('image_url')->store('categories', 'public') 
            : null;
    
        // Tạo danh mục mới
        Category::create([
            'name' => $request->name,
            'description' => $request->description, // Lưu mô tả vào DB
            'parent_id' => $request->parent_id ?? null,
            'image_url' => $imagePath,
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
    $category = Category::findOrFail($id);
    $categories = Category::whereNull('parent_id')->get(); 

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
            'description' => 'nullable|string|max:1000', // Thêm validate cho mô tả
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $category = Category::findOrFail($id);
    
        // Xử lý tải ảnh lên nếu có
        if ($request->hasFile('image_url')) {
            if ($category->image_url) {
                Storage::delete('public/' . $category->image_url);
            }
            $imagePath = $request->file('image_url')->store('categories', 'public');
        } else {
            $imagePath = $category->image_url;
        }
    
        // Cập nhật danh mục
        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'], // Cập nhật mô tả
            'parent_id' => $request->parent_id ?? null,
            'image_url' => $imagePath,
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
