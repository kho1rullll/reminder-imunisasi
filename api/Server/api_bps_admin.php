<?php
header('Content-Type: application/json');

$url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/1600/var/878/th/124/key/8c8656940ee1357155c3b9302f319ac7";

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: PHP\r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo json_encode(["status" => "error", "message" => "Gagal mengambil data"]);
    exit;
}

echo $response;