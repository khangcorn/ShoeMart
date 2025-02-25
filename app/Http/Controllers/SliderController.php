<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hiển thị danh sách
        $sliders = Slider::orderBy('position', 'asc')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Hiển thị form tạo slider
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Lưu slider mới vào database
        $request->validate([
            'image_url' => 'required|string|max:255',
            'caption' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'position' => 'integer|min:0',
        ]);
        Slider::create($request->all());
        return redirect()->route('admin.sliders.index')->with('success', 'Slider đã được tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        return view('admin.sliders.show', compact('slider')); // Sửa đường dẫn
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Slider $slider)
    {
        //Hiển thị form chỉnh sửa slider
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider)
    {
        //Cập nhật slider trong db
        $request->validate([
            'image_url' => 'required|string|max:255',
            'caption' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'position' => 'integer|min:0',
        ]);

        $slider->update($request->all());
        return redirect()->route('admin.sliders.index')->with('success', 'slider đã được cập nhật');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider đã bị xóa!');
    }
}
