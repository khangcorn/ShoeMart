<?php

if (! function_exists('normalizeAddress')) {
    function normalizeAddress($text)
    {
        $remove = ['Tỉnh', 'Thành phố', 'TP.', 'TP', 'Huyện', 'Thị xã', 'Phường', 'Xã', 'Thị trấn'];

        return trim(str_ireplace($remove, '', $text));
    }
}
function getImage($image, $type = 'default')
{
    $urlComponents = parse_url($image);
    if (isset($urlComponents['scheme']) && in_array($urlComponents['scheme'], ['http', 'https'])) {
        return $image;
    } else {
        $basePath = env('APP_URL');

        if (strpos($image, '/') === 0) {
            return $basePath.'/'.ltrim($image, '/');
        } else {
            return $basePath.'/'.$image;
        }
    }
}
function format_cash($price, $shorten = false)
{
    if ($shorten) {
        if ($price >= 1_000_000) {
            return number_format($price / 1_000_000, 0).'M'; // triệu
        } elseif ($price >= 1_000) {
            return number_format($price / 1_000, 0).'K'; // ngàn
        } elseif ($price >= 0) {
            return number_format($price, 0, ',', '.').'đ'; // đồng
        }
    }

    // Định dạng đầy đủ
    return number_format($price, 0, ',', '.').'đ'; // Đơn vị VND
}
function noti($notification, $type)
{
    return [
        'message' => $notification,
        'alert-type' => $type,
    ];
}
