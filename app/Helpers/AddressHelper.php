<?php

if (!function_exists('normalizeAddress')) {
    function normalizeAddress($text) {
        $remove = ['Tỉnh', 'Thành phố', 'TP.', 'TP', 'Huyện', 'Thị xã', 'Phường', 'Xã', 'Thị trấn'];
        return trim(str_ireplace($remove, '', $text));
    }
}
