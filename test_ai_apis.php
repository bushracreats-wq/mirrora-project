<?php
// Test script to check AI Virtual Try-On API endpoints
require_once 'ai_service.php';

$aiService = new AiTryOnService();
$person_img = 'assets/images/1784390842_men1.jpg';
$garment_img = 'assets/images/1784391048_men2.jpg';

echo "--- TESTING HUGGINGFACE GRADIO VTON API (yisol/IDM-VTON) ---\n";

function testHuggingFaceIdmVton($personPath, $garmentPath) {
    // 1. Upload local images to tmpfiles to get public HTTPS URLs
    $ch = curl_init('https://tmpfiles.org/api/v1/upload');
    $cfile1 = new CURLFile(realpath($personPath), 'image/jpeg', 'person.jpg');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['file' => $cfile1],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $res1 = json_decode(curl_exec($ch), true);
    curl_close($ch);
    $personUrl = str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $res1['data']['url']);

    $ch = curl_init('https://tmpfiles.org/api/v1/upload');
    $cfile2 = new CURLFile(realpath($garmentPath), 'image/jpeg', 'garment.jpg');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['file' => $cfile2],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $res2 = json_decode(curl_exec($ch), true);
    curl_close($ch);
    $garmentUrl = str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $res2['data']['url']);

    echo "Person Public URL: $personUrl\n";
    echo "Garment Public URL: $garmentUrl\n";

    // 2. Call HuggingFace Gradio Space API for IDM-VTON
    $hfEndpoint = "https://yisol-idm-vton.hf.space/call/tryon";
    $payload = [
        "data" => [
            ["background" => $personUrl, "layers" => [], "composite" => $personUrl],
            $garmentUrl,
            "clothing outfit",
            true,
            true,
            30,
            42
        ]
    ];

    $ch = curl_init($hfEndpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HuggingFace Event POST HTTP Code: $httpCode\n";
    echo "Response: $response\n";

    $resData = json_decode($response, true);
    if (isset($resData['event_id'])) {
        $eventId = $resData['event_id'];
        echo "Event ID: $eventId - Polling result...\n";
        
        $getResultUrl = "https://yisol-idm-vton.hf.space/call/tryon/" . $eventId;
        $startTime = time();
        while ((time() - $startTime) < 60) {
            sleep(3);
            $ch = curl_init($getResultUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $pollRes = curl_exec($ch);
            curl_close($ch);
            
            if (strpos($pollRes, 'event: complete') !== false) {
                echo "Complete event received!\n";
                if (preg_match('/"url":"([^"]+)"/', $pollRes, $m)) {
                    $outImgUrl = stripslashes($m[1]);
                    echo "SUCCESS! AI Generated Image URL: $outImgUrl\n";
                    return $outImgUrl;
                }
            }
        }
    }
    return null;
}

$out = testHuggingFaceIdmVton($person_img, $garment_img);
if ($out) {
    echo "AI Try-On Generated Image URL: $out\n";
} else {
    echo "HuggingFace test ended.\n";
}
?>
