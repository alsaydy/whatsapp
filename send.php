<?php
header('Content-Type: application/json');

$instanceId = "instance112832";
$token = "1j6hcc8n7svgqlv1";

$numbers = explode("\\n", trim($_POST['numbers']));
$message = trim($_POST['message']);
$imageUrl = null;

$results = [];

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $imagePath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $imageUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/uploads/' . $imageName;
    }
}

foreach ($numbers as $number) {
    $number = trim($number);
    if ($number === '') continue;

    $url = $imageUrl 
        ? "https://api.ultramsg.com/{$instanceId}/messages/image"
        : "https://api.ultramsg.com/{$instanceId}/messages/chat";

    $data = [
        "token" => $token,
        "to" => $number
    ];

    if ($imageUrl) {
        $data["image"] = $imageUrl;
        $data["caption"] = $message;
    } else {
        $data["body"] = $message;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $results[] = [
        "to" => $number,
        "status" => json_decode($response, true),
        "http_code" => $httpCode
    ];
}

echo json_encode(["results" => $results], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
