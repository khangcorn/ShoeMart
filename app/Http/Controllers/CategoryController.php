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
<<<<<<< HEAD
        return view('category.index', compact('categories'));
=======

        return view('admin.category.index', compact('categories'));
>>>>>>> 1bbab0a (Full code DATN)
    }
    public function create()
    {
        $categories = Category::all();
<<<<<<< HEAD
        return view('category.create ', compact('categories'));
        
=======

        return view('admin.category.create', compact('categories'));
>>>>>>> 1bbab0a (Full code DATN)
    }

    /**
     * Tạo danh mục mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

<<<<<<< HEAD
        $category = Category::create([
=======
        // Xử lý tải ảnh lên nếu có
        $imagePath = $request->hasFile('image_url')
            ? $request->file('image_url')->store('categories', 'public')
            : null;

        // Tạo danh mục mới
        Category::create([
>>>>>>> 1bbab0a (Full code DATN)
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
<<<<<<< HEAD
=======
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')->get();

        return view('admin.category.edit', compact('category', 'categories'));
    }

    /**
>>>>>>> 1bbab0a (Full code DATN)
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

<<<<<<< HEAD
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
=======
        $category = Category::findOrFail($id);

        // Xử lý tải ảnh lên nếu có
        if ($request->hasFile('image_url')) {
            if ($category->image_url) {
                Storage::delete('public/'.$category->image_url);
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

>>>>>>> 1bbab0a (Full code DATN)
    /**
     * Xóa danh mục.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
<<<<<<< HEAD
=======

        // Xóa ảnh nếu có
        if ($category->image_url) {
            Storage::delete('public/'.$category->image_url); // Xóa ảnh
        }

        // Xóa danh mục
>>>>>>> 1bbab0a (Full code DATN)
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Product deleted successfully.');
    }
}
