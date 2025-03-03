@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Shipping Fee</h2>
    <form action="{{ route('shipping_fees.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Province</label>
            <input type="text" name="province" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>District</label>
            <input type="text" name="district" class="form-control">
        </div>
        <div class="mb-3">
            <label>Ward</label>
            <input type="text" name="ward" class="form-control">
        </div>
        <div class="mb-3">
            <label>Fee</label>
            <input type="number" step="0.01" name="fee" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection