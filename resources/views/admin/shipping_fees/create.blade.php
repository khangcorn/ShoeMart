@extends('admin.layout')

@section('title', 'Add Shipping Fee')

@section('content')
<div class="px-4 py-4">
    <h2 class="text-3xl font-bold mb-6">Thêm mới phí vận chuyển</h2>

    <form method="POST" action="{{ route('shipping-fees.store') }}">
        @csrf

        {{-- Province --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Tỉnh / Thành phố</label>
            <select id="province" name="province"class="w-full border rounded-lg p-3 mt-2">
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
            <label class="block text-sm font-medium text-gray-600">Phí vận chuyển (VNĐ)</label>
            <input 
                type="number" 
                step="0.01" 
                min="0" 
                name="fee" 
                value="{{ old('fee') }}" 
                class="w-full border rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('fee') border-red-500 @enderror">
            @error('fee')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('shipping-fees.index') }}"                 class="inline-flex items-center bg-gray-500 text-white font-semibold text-sm px-6 py-2 rounded-md hover:bg-gray-600 shadow-md transition">
                Quay lại</a>
            <button type="submit" class=" text-white px-6 py-2 rounded-lg bg-blue-600 transition-all">Thêm mới</button>
        </div>
    </form>
</div>

<script>
    document.querySelector('input[name="fee"]').addEventListener('input', function(e) {
    if (parseFloat(e.target.value) < 0) {
        e.target.value = 0;
    }
});

   document.addEventListener('DOMContentLoaded', function () {
    fetch("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json")
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById("province");
            const districtSelect = document.getElementById("district");
            const wardSelect = document.getElementById("ward");

            // Không cần loại bỏ tiền tố nữa, giữ nguyên tên đầy đủ
            // Hàm loại bỏ tiền tố không sử dụng nữa
            // function removePrefix(name) {
            //     return name.replace(/(Tỉnh|Thành phố|Huyện|Quận|Phường|Xã)/, "").trim();
            // }

            // Load tất cả Tỉnh / Thành phố
            data.forEach(province => {
                const option = document.createElement("option");
                option.value = province.Name;
                option.textContent = province.Name;  // Giữ nguyên tên đầy đủ với tiền tố
                provinceSelect.appendChild(option);
            });

            // Khi chọn Tỉnh / Thành phố
            provinceSelect.addEventListener("change", function () {
                const selectedProvince = data.find(p => p.Name === this.value);
                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

                if (selectedProvince && selectedProvince.Districts) {
                    selectedProvince.Districts.forEach(district => {
                        const option = document.createElement("option");
                        option.value = district.Name;
                        option.textContent = district.Name;  // Giữ nguyên tên đầy đủ của Quận/Huyện
                        districtSelect.appendChild(option);
                    });
                }
            });

            // Khi chọn Quận / Huyện
            districtSelect.addEventListener("change", function () {
                const provinceName = provinceSelect.value;
                const districtName = this.value;
                const selectedProvince = data.find(p => p.Name === provinceName);
                const selectedDistrict = selectedProvince?.Districts.find(d => d.Name === districtName);

                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

                if (selectedDistrict && selectedDistrict.Wards) {
                    selectedDistrict.Wards.forEach(ward => {
                        const option = document.createElement("option");
                        option.value = ward.Name;
                        option.textContent = ward.Name;  // Giữ nguyên tên đầy đủ của Phường/Xã
                        wardSelect.appendChild(option);
                    });
                }
            });
        })
        .catch(error => {
            console.error("Lỗi khi tải dữ liệu địa chỉ:", error);
        });
});
</script>


@endsection
