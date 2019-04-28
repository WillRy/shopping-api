<?php

$header = [
    "typ"=>"JWT",
    "alg"=>"HS256"
];

$header_json = json_encode($header);
$header_base64 = base64_encode($header_json);
echo "Cabecalho JSON : $header_json";
echo PHP_EOL;
echo "Cabecalho BASE64 : $header_base64";
echo PHP_EOL;

$payload = [
    "first_name"=>"fulano",
    "last_name"=>"de tal",
    "email"=>"teste@teste.com",
    "iat"=>time()
];
$payload_json = json_encode($payload);
$payload_base64 = base64_encode($payload_json);
echo "Payload JSON : $payload_json";
echo PHP_EOL;
echo "Payload BASE64 : $payload_base64";
echo PHP_EOL;

$key = 'jcdo2842028cmdkcndkjc23083';

$hash = $header_base64.'.'.$payload_base64;
$signature = hash_hmac('sha256',$hash,$key,true);
$signature_base64 = base64_encode($signature);

echo PHP_EOL;
echo "Assinatura: $signature_base64";
echo PHP_EOL;
echo "Token: $header_base64.$payload_base64.$signature_base64";
