@extends('admin.layout')

@section('title', 'Edit Slider')

@section('content')
    <div class="card">
        <div class="card-header bg-warning">
            <h4>Edit Slider</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sliders.update', $slider->slider_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Image URL</label>
                    <input type="text" name="image_url" class="form-control" value="{{ $slider->image_url }}" required>
                    @error('image_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Caption</label>
                    <textarea name="caption" class="form-control">{{ $slider->caption }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link</label>
                    <input type="text" name="link" class="form-control" value="{{ $slider->link }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input type="number" name="position" class="form-control" value="{{ $slider->position }}">
                </div>

                <button type="submit" class="btn btn-warning">Update</button>
                <a href="{{ route('sliders.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
@endsection
