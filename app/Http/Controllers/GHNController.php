<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GHNController extends Controller
{
    private $token = '176b7daa-15d8-11f0-833d-ba06b9a114b5';
    private $shopDistrictId = 1483; // Ví dụ: Cầu Giấy - Hà Nội
    private $shopId = '5727016';

    public function checkout()
    {
        $response = Http::withHeaders([
            'Token' => $this->token
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
    
        // Kiểm tra phản hồi từ API và lấy danh sách tỉnh
        if ($response->successful()) {
            $provinces = $response->json('data');
        } else {
            $provinces = [];
        }
    
        return view('checkout', compact('provinces'));
    }
    

    public function getDistricts(Request $request)
    {
        $provinceId = $request->province_id;
    
        $response = Http::withToken($this->token)->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');
    
        if ($response->successful()) {
            $allDistricts = $response->json('data');
    
            // Lọc theo province_id
            $districts = collect($allDistricts)->where('ProvinceID', $provinceId)->values();
    
            return response()->json($districts);
        } else {
            return response()->json(['error' => 'Không có dữ liệu quận.'], 400);
        }
    }
    
    
    public function getWards(Request $request)
    {
        $response = Http::withToken($this->token)
            ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                'district_id' => $request->district_id,
            ]);
    
        // Kiểm tra nếu có lỗi từ API
        if ($response->successful()) {
            $wards = $response->json('data');
            return response()->json($wards);  // Trả về dữ liệu phường
        } else {
            return response()->json(['error' => 'Không có dữ liệu phường.'], 400);
        }
    }
    
    

    public function calculateShippingFee(Request $request)
    {
        $response = Http::withToken($this->token)
            ->withHeaders(['ShopId' => $this->shopId])
            ->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
                'from_district_id' => $this->shopDistrictId,
                'service_type_id' => 2,
                'to_district_id' => $request->district_id,
                'to_ward_code' => $request->ward_code,
                'height' => 10,
                'length' => 20,
                'weight' => $request->weight ?? 500,
                'width' => 15,
                'insurance_value' => 500000,
            ]);

        if ($response->successful()) {
            return response()->json(['fee' => $response['data']['total']]);
        }

        return response()->json(['error' => 'Không tính được phí'], 500);
    }
}
