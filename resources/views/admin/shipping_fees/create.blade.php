@extends('admin.layout')

@section('title', 'Add Shipping Fee')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Add New Shipping Fee</h2>

    <form method="POST" action="{{ route('shipping-fees.store') }}">
        @csrf

        {{-- Province --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Tỉnh / Thành phố</label>
            <select id="province" name="province">
                <option value="" selected>-- Chọn Tỉnh / Thành phố --</option>  <!-- Dòng đầu tiên cho tỉnh -->
                @foreach ($data as $item)
                    <option value="{{ $item['province'] }}">{{ $item['province'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- District --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Quận / Huyện</label>
            <select name="district" id="district" class="w-full border rounded-lg p-3 mt-2">
                <option value="">-- Chọn Quận/Huyện --</option>
            </select>
        </div>

        {{-- Ward --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Phường / Xã</label>
            <select name="ward" id="ward" class="w-full border rounded-lg p-3 mt-2">
                <option value="">-- Chọn Phường / Xã --</option>
            </select>
        </div>

        {{-- Fee --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Fee (VNĐ)</label>
            <input type="number" step="0.01" name="fee" value="{{ old('fee') }}" class="w-full border rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('fee') border-red-500 @enderror">
            @error('fee')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('shipping-fees.index') }}" class="bg-gray-300 text-black px-6 py-2 rounded-lg hover:bg-gray-400 transition-all">Back</a>
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-all">Save</button>
        </div>
    </form>
</div>

<script>
    // Chuyển dữ liệu từ PHP sang JavaScript
    const data = @json($data);  // Dữ liệu từ file JSON

    // Lắng nghe sự kiện thay đổi của dropdown Tỉnh / Thành phố
    document.getElementById("province").addEventListener("change", function() {
        const provinceName = this.value;  // Tỉnh / Thành phố đã chọn
        const province = data.find(p => p.province === provinceName);  // Tìm tỉnh trong dữ liệu

        const districtSelect = document.getElementById("district");
        districtSelect.innerHTML = '<option value="" disabled selected>-- Chọn Quận / Huyện --</option>';  // Reset quận

        // Kiểm tra nếu tỉnh có quận thì thêm các quận vào dropdown
        if (province && province.districts) {
            province.districts.forEach(district => {
                const option = document.createElement("option");
                option.value = district.district;
                option.textContent = district.district;
                districtSelect.appendChild(option);  // Thêm quận vào dropdown
            });
        }

        // Reset phường khi thay đổi tỉnh
        document.getElementById("ward").innerHTML = '<option value="" disabled selected>-- Chọn Phường / Xã --</option>';
    });

    // Lắng nghe sự kiện thay đổi của dropdown Quận / Huyện
    document.getElementById("district").addEventListener("change", function() {
        const districtName = this.value;  // Quận / Huyện đã chọn
        const provinceName = document.getElementById("province").value;  // Tỉnh / Thành phố đã chọn
        const province = data.find(p => p.province === provinceName);  // Tìm tỉnh

        const wardSelect = document.getElementById("ward");
        wardSelect.innerHTML = '<option value="" disabled selected>-- Chọn Phường / Xã --</option>';  // Reset phường

        // Kiểm tra nếu quận có phường thì thêm các phường vào dropdown
        if (province) {
            const district = province.districts.find(d => d.district === districtName);
            if (district && district.wards) {
                district.wards.forEach(ward => {
                    const option = document.createElement("option");
                    option.value = ward;
                    option.textContent = ward;
                    wardSelect.appendChild(option);  // Thêm phường vào dropdown
                });
            }
        }
    });
</script>
@endsection
