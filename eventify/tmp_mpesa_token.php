<?php
$url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
$credentials = base64_encode("PTcfOiuvZyveUJeQVWwhKbohdyg1y4MAo38HcSu3jDLUNwWm:ju5aIaPB0dxS5lLJv0gLd3WUE0IgFuoA08JBJBW4vXansLQA72ATcQ01oXnuwbbqT");
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials", "Accept: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$err = curl_error($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP=$http\nERR=$err\nRESP=$response\n";
?>