<?php

require 'connection.php';

define('IMGBB_API_KEY', '---');
define('IMGBB_API_URL', 'https://api.imgbb.com/1/upload');


header('Content-Type: application/json');

function return_error($message, $full_response = null)
{
    echo json_encode([
        'success' => false,
        'message' => $message,
        'full_response' => $full_response
    ]);
    exit;
}

if(empty($_FILES['image'])){
    return_error('No file uploaded');
}

$file = $_FILES['image'];

if($file['error'] !== UPLOAD_ERR_OK){
    return_error('File upload error: ' . $file['error']);
}

$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
if(!in_array($file['type'],$allowed_types)){
    return_error('Unsupported file type. Only JPG, PNG, and WEBP are allowed.');
}

$image_data = file_get_contents($file['tmp_name']);
$base64_image = base64_encode($image_data);

$post_data =[
    'key' => IMGBB_API_KEY,
    'image' => $base64_image,
];

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, IMGBB_API_URL);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($post_data));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

$respose = curl_exec($ch);

if(curl_errno($ch)){
    $error_msg = curl_error($ch);
    curl_close($ch);
    return_error('Curl error:'.$error_msg);
}

curl_close($ch);
$imgbb_response = json_decode($respose,true);

if (!isset($imgbb_response['success'])|| $imgbb_response ['success']!==true) {
    $error_msg = $imgbb_response['error']['message'] ?? 'Unknown error from ImgBB API.';
    return_error('ImgBB API error: ' . $error_msg, $imgbb_response);
}

$image_url = $imgbb_response['data']['url'];
$delete_url = $imgbb_response['data']['delete_url'];

$conn = new Connection();
$conn->iud("INSERT INTO `image_data` (`image_url`) VALUES ('".$image_url."')");

// $conn->close();

echo json_encode([
    'success' => true,
    'image_url' => $image_url,
    'delete_url' => $delete_url
]);


?>
