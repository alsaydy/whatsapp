<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$instanceId = "instance112832";
$token = "1j6hcc8n7svgqlv1";

$numbers = explode("\n", trim($_POST['numbers']));
$message = trim($_POST['message']);
$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $imagePath = $uploadDir . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    $imageUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $imagePath;
}

$results = [];

foreach ($numbers as $number) {
    $number = trim($number);
    if ($number === '') continue;

    if ($imagePath) {
        $data = [
            "token" => $token,
            "to" => $number,
            "image" => $imageUrl,
            "caption" => $message
        ];
        $url = "https://api.ultramsg.com/{$instanceId}/messages/image";
    } else {
        $data = [
            "token" => $token,
            "to" => $number,
            "body" => $message
        ];
        $url = "https://api.ultramsg.com/{$instanceId}/messages/chat";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    $response = curl_exec($ch);
    curl_close($ch);

    $results[] = json_decode($response, true);
}

echo json_encode(["message" => "تم إرسال الرسائل", "responses" => $results]);
?>
