<?php

namespace App\Http\Controllers;

use App\Models\VariantAttribute;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = VariantAttribute::where('attribute_name', 'color')->get();
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);

        VariantAttribute::create([
            'attribute_name' => 'color',
            'attribute_value' => $request->attribute_value,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color created successfully');
    }

    public function edit($id)
    {
        $color = VariantAttribute::where('attribute_name', 'color')->findOrFail($id);
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        $color = VariantAttribute::where('attribute_name', 'color')->findOrFail($id);

        $request->validate([
            'attribute_value' => 'required|string|max:100',
        ]);

        $color->update([
            'attribute_value' => $request->attribute_value,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color updated successfully');
    }

    public function destroy($id)
    {
        $color = VariantAttribute::where('attribute_name', 'color')->findOrFail($id);
        $color->delete();

        return redirect()->route('colors.index')->with('success', 'Color deleted successfully');
    }
}
