<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::all();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_url' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'caption' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
        ]);
    
        // Lưu ảnh vào storage/app/public/slider
        $path = $request->file('image_url')->store('slider', 'public');
    
        Slider::create([
            'image_url' => 'storage/' . $path, // đường dẫn để dùng trên website
            'caption' => $request->caption,
            'link' => $request->link,
            'position' => $request->position ?? 0,
        ]);
    
        return redirect()->route('sliders.index')->with('success', 'Slider created successfully.');
    }

    public function show(Slider $slider)
    {
        return view('sliders.show', compact('slider'));
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'image_url' => 'required|string|max:255',
            'caption' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
        ]);

        $slider->update($request->all());

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();

        return redirect()->route('sliders.index')->with('success', 'Slider deleted successfully.');
    }
}