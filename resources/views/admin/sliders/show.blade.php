@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chi tiết Slider</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Chú thích: {{ $slider->caption }}</h5>
            <p class="card-text">
                <strong>Hình ảnh:</strong> <br>
                <img src="{{ asset($slider->image_url) }}" width="300">
            </p>
            <p><strong>Liên kết:</strong> <a href="{{ $slider->link }}" target="_blank">{{ $slider->link }}</a></p>
            <p><strong>Vị trí:</strong> {{ $slider->position }}</p>

            <a href="{{ route('admin.sliders.index') }}" class="btn btn-primary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
