@extends('admin.layout')

@section('title', 'Edit Shipping Fee')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Edit Shipping Fee</h2>

    <form method="POST" action="{{ route('shipping-fees.update', $shippingFee->shipping_id) }}">
        @csrf
        @method('PUT')

        {{-- Province --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Province</label>
            <select name="province" id="province" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('province') border-red-500 @enderror">
                <option value="">-- Chọn Tỉnh / Thành phố --</option>
                @foreach($data as $item)
                    <option value="{{ $item['province'] }}" {{ old('province', $shippingFee->province) === $item['province'] ? 'selected' : '' }}>
                        {{ $item['province'] }}
                    </option>
                @endforeach
            </select>
            @error('province')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- District --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">District (optional)</label>
            <select name="district" id="district" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('district') border-red-500 @enderror">
                <option value="">-- Chọn Quận / Huyện --</option>
            </select>
            @error('district')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ward --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Ward (optional)</label>
            <select name="ward" id="ward" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('ward') border-red-500 @enderror">
                <option value="">-- Chọn Phường / Xã --</option>
            </select>
            @error('ward')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fee --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-600">Fee (VNĐ)</label>
            <input type="number" step="0.01" name="fee" value="{{ old('fee', $shippingFee->fee) }}" class="w-full border border-gray-300 rounded-lg p-3 mt-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('fee') border-red-500 @enderror" >
            @error('fee')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('shipping-fees.index') }}" class="bg-gray-300 text-black px-6 py-2 rounded-lg hover:bg-gray-400 transition-all">Back</a>
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-all">Save Changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const data = @json($data);

        const provinceSelect = document.getElementById("province");
        const districtSelect = document.getElementById("district");
        const wardSelect = document.getElementById("ward");

        function populateDistricts(provinceName) {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

            const province = data.find(p => p.province === provinceName);
            if (province) {
                province.districts.forEach(district => {
                    const option = document.createElement("option");
                    option.value = district.district;
                    option.textContent = district.district;
                    districtSelect.appendChild(option);
                });
            }
        }

        function populateWards(provinceName, districtName) {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

            const province = data.find(p => p.province === provinceName);
            if (province) {
                const district = province.districts.find(d => d.district === districtName);
                if (district) {
                    district.wards.forEach(ward => {
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

        // Set lại khi edit:
        const oldProvince = provinceSelect.value;
        const oldDistrict = @json(old('district', $shippingFee->district));
        const oldWard = @json(old('ward', $shippingFee->ward));

        if (oldProvince) {
            populateDistricts(oldProvince);
            if (oldDistrict) {
                districtSelect.value = oldDistrict;
                populateWards(oldProvince, oldDistrict);
                if (oldWard) {
                    wardSelect.value = oldWard;
                }
            }
        }
    });
</script>
@endpush
