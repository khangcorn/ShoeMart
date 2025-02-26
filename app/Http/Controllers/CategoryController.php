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
        return view('category.index', compact('categories'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('category.create ', compact('categories'));
        
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
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('categories.index')
        ->with('success','Product updated successfully');
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
     * Cập nhật danh mục.
     */
    public function edit($id)
    {
        $category = Category::find($id);
        
        // If the category doesn't exist, return an error or redirect
        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Category not found');
        }

        return view('category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Add any other validation rules here
        ]);

        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Category not found');
        }

        // Update the category with validated data
        $category->update($validated);

        // Redirect to the categories index or show a success message
        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }
    /**
     * Xóa danh mục.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Product deleted successfully.');
    }
}
