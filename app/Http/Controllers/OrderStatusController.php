<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
public function index()
{
    $statuses = OrderStatus::all();
    return view('admin.order_statuses.index', compact('statuses'));
}

public function create()
{
    return view('admin.order_statuses.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|unique:order_statuses,name|max:50',
        'description' => 'nullable|string',
    ]);

    OrderStatus::create($request->all());

    return redirect()->route('order-statuses.index')->with('success', 'Thêm trạng thái thành công');
}

public function edit($id)
{
    $status = OrderStatus::findOrFail($id);
    return view('admin.order_statuses.edit', compact('status'));
}

public function update(Request $request, $id)
{
    $status = OrderStatus::findOrFail($id);

    $request->validate([
        'name' => 'required|max:50|unique:order_statuses,name,' . $id . ',status_id',
        'description' => 'nullable|string',
    ]);

    $status->update($request->all());

    return redirect()->route('order-statuses.index')->with('success', 'Cập nhật trạng thái thành công');
}

public function destroy($id)
{
    $status = OrderStatus::findOrFail($id);
    $status->delete();

    return redirect()->route('order-statuses.index')->with('success', 'Xóa trạng thái thành công');
}
}