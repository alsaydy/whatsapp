<?php
$instanceId = "instance112832";
$token = "1j6hcc8n7svgqlv1";

$numbers = explode(",", $_POST['numbers']);
$message = $_POST['message'];

foreach ($numbers as $number) {
    $number = trim($number);

    $data = [
        "token" => $token,
        "to" => $number,
        "body" => $message
    ];

    $url = "https://api.ultramsg.com/$instance_id/messages/chat";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    echo "تم إرسال الرسالة إلى: $number<br>";
}
?>
