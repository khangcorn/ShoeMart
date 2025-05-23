@extends('admin.layout')

@section('title', 'Edit Shipping Fee')

@section('content')
<div class="px-4 py-4">
    <h2 class="text-3xl font-bold mb-6">Chỉnh sửa phí vận chuyển</h2>

    <form method="POST" action="{{ route('shipping-fees.update', $shippingFee->shipping_id) }}">
        @csrf
        @method('PUT')

        {{-- Province --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Tỉnh / Thành phố</label>
            <select name="province" id="province" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('province') border-red-500 @enderror">
                <option value="">-- Chọn Tỉnh / Thành phố --</option>
                {{-- Options will be populated by JS --}}
            </select>
            @error('province')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- District --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Quận / Huyện</label>
            <select name="district" id="district" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('district') border-red-500 @enderror">
                <option value="">-- Chọn Quận / Huyện --</option>
            </select>
            @error('district')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ward --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Phường / Xã</label>
            <select name="ward" id="ward" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('ward') border-red-500 @enderror">
                <option value="">-- Chọn Phường / Xã --</option>
            </select>
            @error('ward')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fee --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Phí vận chuyển (VNĐ)</label>
            <input type="number" step="0.01" name="fee" value="{{ old('fee', $shippingFee->fee) }}" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('fee') border-red-500 @enderror" >
            @error('fee')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('shipping-fees.index') }}"  class="inline-flex items-center bg-gray-500 text-white font-semibold text-sm px-6 py-2 rounded-md hover:bg-gray-600 shadow-md transition">Quay lại</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg  transition-all">Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceSelect = document.getElementById("province");
        const districtSelect = document.getElementById("district");
        const wardSelect = document.getElementById("ward");

        // Fetch data from the external URL
        fetch("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json")
            .then(response => response.json())
            .then(data => {
                // Populate provinces
                data.forEach(province => {
                    const option = document.createElement("option");
                    option.value = province.Name;
                    option.textContent = province.Name;
                    provinceSelect.appendChild(option);
                });

                // Populate districts and wards based on selected province and district
                function populateDistricts(provinceName) {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

                    const province = data.find(p => p.Name === provinceName);
                    if (province && province.Districts) {
                        province.Districts.forEach(district => {
                            const option = document.createElement("option");
                            option.value = district.Name;
                            option.textContent = district.Name;
                            districtSelect.appendChild(option);
                        });
                    }
                }

                function populateWards(provinceName, districtName) {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

                    const province = data.find(p => p.Name === provinceName);
                    if (province) {
                        const district = province.Districts.find(d => d.Name === districtName);
                        if (district && district.Wards) {
                            district.Wards.forEach(ward => {
                                const option = document.createElement("option");
                                option.value = ward;
                                option.textContent = ward;
                                wardSelect.appendChild(option);
                            });
                        }
                    }
                }

                provinceSelect.addEventListener("change", function () {
                    populateDistricts(this.value);
                });

                districtSelect.addEventListener("change", function () {
                    populateWards(provinceSelect.value, this.value);
                });

                // Set initial values for editing (if any)
                const oldProvince = @json(old('province', $shippingFee->province));
                const oldDistrict = @json(old('district', $shippingFee->district));
                const oldWard = @json(old('ward', $shippingFee->ward));

                if (oldProvince) {
                    provinceSelect.value = oldProvince;
                    populateDistricts(oldProvince);
                    if (oldDistrict) {
                        districtSelect.value = oldDistrict;
                        populateWards(oldProvince, oldDistrict);
                        if (oldWard) {
                            wardSelect.value = oldWard;
                        }
                    }
                }
            })
            .catch(error => {
                console.error("Error loading address data:", error);
            });
    });
</script>
@endpush
