<?php
session_start();

$secretKey = 'ttmed2548P$u';

// ฟังก์ชัน base64url decode
function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function simple_jwt_decode($jwt, $key) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return null;

    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

    // ตรวจสอบ signature
    $signature = base64url_decode($base64UrlSignature);
    $expected  = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $key, true);

    if (!hash_equals($expected, $signature)) {
        return null; // ลายเซ็นไม่ถูกต้อง
    }

    // แปลง payload
    $payload = json_decode(base64url_decode($base64UrlPayload), true);

    // ตรวจสอบ exp
    if (isset($payload['exp']) && time() > $payload['exp']) {
        return null; // token หมดอายุ
    }

    return $payload;
}

// -------------------
$jwt = isset($_GET['jwt']) ? urldecode($_GET['jwt']) : '';

if ($jwt) {
    $userData = simple_jwt_decode($jwt, $secretKey);
    if ($userData) {
        $_SESSION['user_psusso'] = $userData;
        header('Location: /');
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>
