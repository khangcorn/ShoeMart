@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4 text-center">Add Shipping Fee</h2>
    <form action="{{ route('shipping_fees.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="province" class="form-label">Province</label>
            <input type="text" name="province" class="form-control" id="province" required>
        </div>
        <div class="mb-3">
            <label for="district" class="form-label">District</label>
            <input type="text" name="district" class="form-control" id="district">
        </div>
        <div class="mb-3">
            <label for="ward" class="form-label">Ward</label>
            <input type="text" name="ward" class="form-control" id="ward">
        </div>
        <div class="mb-3">
            <label for="fee" class="form-label">Fee</label>
            <input type="number" step="0.01" name="fee" class="form-control" id="fee" required>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Save Shipping Fee</button>
        </div>
    </form>
</div>
@endsection
