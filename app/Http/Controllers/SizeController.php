<?php

namespace App\Http\Controllers;

use App\Models\VariantAttribute;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = VariantAttribute::where('attribute_name', 'size')->get();
        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);

        VariantAttribute::create([
            'attribute_name' => 'size',
            'attribute_value' => $request->attribute_value,
        ]);

        return redirect()->route('sizes.index')->with('success', 'Size created successfully');
    }

    public function edit($id)
    {
        $size = VariantAttribute::where('attribute_name', 'size')->findOrFail($id);
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        $size = VariantAttribute::where('attribute_name', 'size')->findOrFail($id);

        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);

        $size->update([
            'attribute_value' => $request->attribute_value,
        ]);

        return redirect()->route('sizes.index')->with('success', 'Size updated successfully');
    }

    public function destroy($id)
    {
        $size = VariantAttribute::where('attribute_name', 'size')->findOrFail($id);
        $size->delete();

        return redirect()->route('sizes.index')->with('success', 'Size deleted successfully');
    }
}
