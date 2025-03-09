<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
        // Optionally, pass parent categories if needed
        $categories = Category::all();
        return view('admin.category.create', compact('categories'));
    }

    /**
     * Tạo danh mục mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
                'regex:/^[^\d]+$/', // Không cho phép số
            ],
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại, vui lòng chọn tên khác.',
            'name.regex' => 'Tên danh mục không được chứa số.',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?? null, // Cẩn thận với `parent_id`
        ]);

        return redirect()->route('admin.categories.index')
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
        $category = Category::findOrFail($id); // Sử dụng `findOrFail` để tự động xử lý lỗi nếu không tìm thấy

        return view('admin.category.edit', compact('category'));
    }

    /**
     * Cập nhật danh mục.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id); // Sử dụng `findOrFail`

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Xóa danh mục.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }
}
