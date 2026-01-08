<?php
// src/helpers/debug.php

if (!function_exists('dd')) {
    function dd($data) {
        echo "<pre style='background:#111;color:#0f0;padding:10px;border-radius:8px;'>";
        print_r($data);
        echo "</pre>";
        die(); // หยุดโปรแกรม
    }
}
