<?php

if (!function_exists('format_amount')) {
    function format_amount($amount = 0) {
        return env('CUR').' '.$amount;
    }
}

if (!function_exists('custom_assets')) {
    function custom_assets($path = '') {
        return env('IMG_URL') . $path;
    }
}
