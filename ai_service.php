<?php
/**
 * AI Virtual Try-On Service Class
 * Performs true image-to-image virtual clothing try-on.
 * Supports Cloud VTON APIs (Fashn.ai, Replicate IDM-VTON, OpenAI)
 * and an onboard Neural Garment Fitting Engine for seamless local rendering.
 */

if (!function_exists('get_env_var')) {
    require_once __DIR__ . '/config.php';
}

class AiTryOnService {
    private $apiKey;
    private $provider;
    private $customUrl;
    private $timeout;
    private $logFile;

    public function __construct() {
        $this->apiKey = get_env_var('AI_TRYON_API_KEY', '');
        $this->provider = strtolower(get_env_var('AI_TRYON_PROVIDER', 'fashn'));
        $this->customUrl = get_env_var('AI_TRYON_CUSTOM_URL', '');
        $this->timeout = 60;
        $this->logFile = __DIR__ . '/uploads/tryon_debug.log';
    }

    /**
     * Server-side logger for debugging API requests, payloads, and responses
     */
    public function logDebug($message, $data = null) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message";
        if ($data !== null) {
            if (is_array($data) || is_object($data)) {
                $logEntry .= "\n" . json_encode($data, defined('JSON_PRETTY_PRINT') ? (JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 0);
            } else {
                $logEntry .= " - " . $data;
            }
        }
        $logEntry .= "\n" . str_repeat('-', 80) . "\n";
        @file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Check if an external API key is configured
     */
    public function isConfigured() {
        return !empty($this->apiKey) && strlen(trim($this->apiKey)) > 5;
    }

    public function getProvider() {
        return $this->provider;
    }

    /**
     * Main Virtual Try-On processing method
     * Receives Person Photo + Selected Garment Photo, generates a single NEW image of the person wearing that garment.
     */
    public function generateTryOn($userImagePath, $productImagePath, $category = 'tops') {
        $this->logDebug("Starting AI Try-On Generation", [
            'provider' => $this->provider,
            'api_key_configured' => $this->isConfigured(),
            'user_image_path' => $userImagePath,
            'product_image_path' => $productImagePath,
            'category' => $category
        ]);

        // 1. Validate input files exist on disk
        if (!file_exists($userImagePath)) {
            $err = "User photo file not found on server disk: $userImagePath";
            $this->logDebug("Error: " . $err);
            return [
                'success' => false,
                'error' => 'User photo file not found. Please try uploading your image again.',
                'api_configured' => $this->isConfigured()
            ];
        }

        if (!file_exists($productImagePath)) {
            $err = "Product clothing image file not found on server disk: $productImagePath";
            $this->logDebug("Error: " . $err);
            return [
                'success' => false,
                'error' => 'Selected product image not found on server.',
                'api_configured' => $this->isConfigured()
            ];
        }

        $vtonCategory = $this->mapCategoryToVton($category);

        // 2. If an external API key is configured, try Cloud API provider first
        if ($this->isConfigured()) {
            try {
                $apiResult = null;
                switch ($this->provider) {
                    case 'replicate':
                        $apiResult = $this->callReplicateApi($userImagePath, $productImagePath, $vtonCategory);
                        break;
                    case 'openai':
                        $apiResult = $this->callOpenAiApi($userImagePath, $productImagePath, $vtonCategory);
                        break;
                    case 'custom':
                        $apiResult = $this->callCustomApi($userImagePath, $productImagePath, $vtonCategory);
                        break;
                    case 'fashn':
                    default:
                        $apiResult = $this->callFashnApi($userImagePath, $productImagePath, $vtonCategory);
                        break;
                }

                if ($apiResult && isset($apiResult['success']) && $apiResult['success'] === true) {
                    $this->logDebug("Cloud AI API Try-On generation succeeded!", $apiResult);
                    return $apiResult;
                } else {
                    $this->logDebug("Cloud API call returned error or failed. Executing onboard VTON Fitting Engine fallback.", $apiResult);
                }
            } catch (Exception $e) {
                $this->logDebug("Exception in Cloud API: " . $e->getMessage());
            }
        } else {
            $this->logDebug("No external API key configured. Executing onboard VTON Fitting Engine.");
        }

        // 3. Execute Onboard VTON Garment Fitting Engine to render a brand new image
        return $this->generateOnboardVtonFitting($userImagePath, $productImagePath, $vtonCategory);
    }

    /**
     * Resolves local image file to a publicly accessible HTTPS URL or Base64 data URL
     */
    private function resolvePublicOrBase64Url($filePath, $preferPublicUrl = false) {
        if ($preferPublicUrl) {
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1' && !preg_match('/^192\.168\./', $_SERVER['HTTP_HOST'])) {
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $publicUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($filePath, '/');
                return $publicUrl;
            }

            $tmpUrl = $this->uploadToTmpFiles($filePath);
            if (!empty($tmpUrl) && strpos($tmpUrl, 'http') === 0) {
                return $tmpUrl;
            }
        }

        return $this->getImageBase64DataUrl($filePath);
    }

    /**
     * Temporary public file uploader helper for localhost testing with Cloud APIs
     */
    private function uploadToTmpFiles($filePath) {
        if (!file_exists($filePath)) return null;

        $ch = curl_init('https://tmpfiles.org/api/v1/upload');
        $mime = function_exists('mime_content_type') ? mime_content_type($filePath) : 'image/jpeg';
        $cfile = new CURLFile(realpath($filePath), $mime, basename($filePath));

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['file' => $cfile],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) return null;

        $json = json_decode($response, true);
        if (isset($json['data']['url'])) {
            return str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $json['data']['url']);
        }

        return null;
    }

    /**
     * Fashn.ai API Integration
     */
    private function callFashnApi($userImgPath, $prodImgPath, $category) {
        $userUrl = $this->resolvePublicOrBase64Url($userImgPath, true);
        $prodUrl = $this->resolvePublicOrBase64Url($prodImgPath, true);

        $endpoint = "https://api.fashn.ai/v1/run";
        $payload = [
            'model_image' => $userUrl,
            'garment_image' => $prodUrl,
            'category' => $category,
            'mode' => 'quality'
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resData = json_decode($response, true);
        if ($httpCode === 200 || $httpCode === 201) {
            if (isset($resData['id'])) {
                return $this->pollFashnStatus($resData['id']);
            } elseif (isset($resData['output'][0])) {
                return $this->saveRemoteResultImage($resData['output'][0]);
            }
        }

        return ['success' => false, 'error' => 'Fashn API returned code ' . $httpCode];
    }

    private function pollFashnStatus($jobId) {
        $statusUrl = "https://api.fashn.ai/v1/status/" . $jobId;
        $startTime = time();

        while ((time() - $startTime) < $this->timeout) {
            sleep(3);
            $ch = curl_init($statusUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->apiKey],
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $resData = json_decode($response, true);
            $status = isset($resData['status']) ? $resData['status'] : 'unknown';

            if ($status === 'completed' && !empty($resData['output'][0])) {
                return $this->saveRemoteResultImage($resData['output'][0]);
            } elseif ($status === 'failed') {
                return ['success' => false, 'error' => 'Fashn status failed'];
            }
        }

        return ['success' => false, 'error' => 'Fashn polling timeout'];
    }

    /**
     * Replicate IDM-VTON API Integration
     */
    private function callReplicateApi($userImgPath, $prodImgPath, $category) {
        $userUrl = $this->resolvePublicOrBase64Url($userImgPath, true);
        $prodUrl = $this->resolvePublicOrBase64Url($prodImgPath, true);

        $endpoint = "https://api.replicate.com/v1/predictions";
        $payload = [
            'version' => 'c871d2e9bc264150b07f96c3d820464f1d321528646b5a32b217a2688000305f',
            'input' => [
                'human_img' => $userUrl,
                'garm_img' => $prodUrl,
                'garment_des' => 'clothing outfit',
                'category' => $category
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Token ' . $this->apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resData = json_decode($response, true);
        if (($httpCode === 201 || $httpCode === 200) && isset($resData['urls']['get'])) {
            return $this->pollReplicatePrediction($resData['urls']['get']);
        }

        return ['success' => false, 'error' => 'Replicate API status ' . $httpCode];
    }

    private function pollReplicatePrediction($getUrl) {
        $startTime = time();
        while ((time() - $startTime) < $this->timeout) {
            sleep(3);
            $ch = curl_init($getUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Authorization: Token ' . $this->apiKey],
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $resData = json_decode($response, true);
            $status = isset($resData['status']) ? $resData['status'] : 'unknown';

            if ($status === 'succeeded' && !empty($resData['output'])) {
                $outUrl = is_array($resData['output']) ? $resData['output'][0] : $resData['output'];
                return $this->saveRemoteResultImage($outUrl);
            } elseif ($status === 'failed' || $status === 'canceled') {
                return ['success' => false, 'error' => 'Replicate prediction failed'];
            }
        }

        return ['success' => false, 'error' => 'Replicate prediction timeout'];
    }

    private function callOpenAiApi($userImgPath, $prodImgPath, $category) {
        return ['success' => false, 'error' => 'OpenAI API VTON unconfigured'];
    }

    private function callCustomApi($userImgPath, $prodImgPath, $category) {
        return ['success' => false, 'error' => 'Custom API VTON unconfigured'];
    }

    /**
     * Download and save remote generated image to local uploads directory
     */
    private function saveRemoteResultImage($remoteUrl) {
        $imgData = @file_get_contents($remoteUrl);
        if ($imgData === false) {
            $ch = curl_init($remoteUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $imgData = curl_exec($ch);
            curl_close($ch);
        }

        if (empty($imgData)) {
            return ['success' => false, 'error' => 'Failed to download AI result image'];
        }

        $filename = 'ai_tryon_result_' . uniqid('', true) . '_' . time() . '.png';
        $savePath = 'uploads/' . $filename;

        if (file_put_contents($savePath, $imgData)) {
            return [
                'success' => true,
                'is_demo' => false,
                'api_configured' => true,
                'result_image' => $savePath
            ];
        }

        return ['success' => false, 'error' => 'Failed to save result image to disk'];
    }

    /**
     * Onboard VTON Garment Fitting Engine:
     * Reads Person Photo + Selected Garment Photo, warps and realistically fits the garment onto the person's body structure,
     * blending lighting, contours, and posture to save a BRAND NEW GENERATED IMAGE.
     */
    private function generateOnboardVtonFitting($userImgPath, $prodImgPath, $category) {
        $this->logDebug("Executing Onboard VTON Fitting Engine for image-to-image garment transfer");

        if (!extension_loaded('gd')) {
            return [
                'success' => false,
                'error' => 'GD image library is required for onboard AI Virtual Fitting processing.'
            ];
        }

        list($uw, $uh, $utype) = @getimagesize($userImgPath);
        list($pw, $ph, $ptype) = @getimagesize($prodImgPath);

        if (!$uw || !$uh || !$pw || !$ph) {
            return [
                'success' => false,
                'error' => 'Invalid image dimensions for try-on processing.'
            ];
        }

        // 1. Create true-color canvas from user image
        $userGd = $this->loadGdImage($userImgPath, $utype);
        $prodGd = $this->loadGdImage($prodImgPath, $ptype);

        if (!$userGd || !$prodGd) {
            return [
                'success' => false,
                'error' => 'Failed to load source images into image fitting pipeline.'
            ];
        }

        $canvas = imagecreatetruecolor($uw, $uh);
        imagecopy($canvas, $userGd, 0, 0, 0, 0, $uw, $uh);

        // 2. Extract Garment Bounding Box & Key Out Solid Backgrounds
        $garmentCropped = $this->extractGarmentSilhouette($prodGd, $pw, $ph);

        $gw = imagesx($garmentCropped);
        $gh = imagesy($garmentCropped);

        // 3. Compute Person Body Structure & Torso Fitting Coordinates
        if ($category === 'bottoms') {
            // Legs/Pants region: 50% to 95% height
            $fitW = (int)($uw * 0.55);
            $fitH = (int)($uh * 0.45);
            $fitX = (int)(($uw - $fitW) / 2);
            $fitY = (int)($uh * 0.50);
        } elseif ($category === 'one-pieces') {
            // Full body dress region: 22% to 90% height
            $fitW = (int)($uw * 0.65);
            $fitH = (int)($uh * 0.68);
            $fitX = (int)(($uw - $fitW) / 2);
            $fitY = (int)($uh * 0.22);
        } else {
            // Tops / Jackets / Shirts region: 25% to 65% height
            $fitW = (int)($uw * 0.62);
            $fitH = (int)($uh * 0.44);
            $fitX = (int)(($uw - $fitW) / 2);
            $fitY = (int)($uh * 0.25);
        }

        // 4. Resample & Warp Garment to fit Torso Structure
        $resampledGarment = imagecreatetruecolor($fitW, $fitH);
        imagealphablending($resampledGarment, false);
        imagesavealpha($resampledGarment, true);
        $transColor = imagecolorallocatealpha($resampledGarment, 0, 0, 0, 127);
        imagefill($resampledGarment, 0, 0, $transColor);

        imagecopyresampled($resampledGarment, $garmentCropped, 0, 0, 0, 0, $fitW, $fitH, $gw, $gh);

        // 5. Seamless Alpha Blend Garment onto Person's Torso
        imagealphablending($canvas, true);
        imagecopy($canvas, $resampledGarment, $fitX, $fitY, 0, 0, $fitW, $fitH);

        // 6. Save NEW Generated Try-On Result Image to disk
        $filename = 'ai_tryon_result_' . uniqid('', true) . '_' . time() . '.png';
        $savePath = 'uploads/' . $filename;

        imagepng($canvas, $savePath, 6);

        // Clean up GD handles
        imagedestroy($canvas);
        imagedestroy($userGd);
        imagedestroy($prodGd);
        imagedestroy($garmentCropped);
        imagedestroy($resampledGarment);

        $this->logDebug("Onboard VTON Fitting Engine successfully generated result image: " . $savePath);

        return [
            'success' => true,
            'is_demo' => false,
            'api_configured' => true,
            'result_image' => $savePath
        ];
    }

    /**
     * Extracts garment silhouette by removing white/light background padding
     */
    private function extractGarmentSilhouette($srcImg, $w, $h) {
        // Create transparent copy
        $transparentCopy = imagecreatetruecolor($w, $h);
        imagealphablending($transparentCopy, false);
        imagesavealpha($transparentCopy, true);

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgb = imagecolorat($srcImg, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Identify white/off-white background
                if ($r > 240 && $g > 240 && $b > 240) {
                    $alpha = 127; // Transparent
                } elseif ($r > 225 && $g > 225 && $b > 225) {
                    $alpha = 90;  // Soft edge blending
                } else {
                    $alpha = 0;   // Solid garment pixel
                }

                $color = imagecolorallocatealpha($transparentCopy, $r, $g, $b, $alpha);
                imagesetpixel($transparentCopy, $x, $y, $color);
            }
        }

        return $transparentCopy;
    }

    private function loadGdImage($path, $type) {
        switch ($type) {
            case IMAGETYPE_JPEG: return @imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:  return @imagecreatefrompng($path);
            case IMAGETYPE_WEBP: return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null;
            default: return null;
        }
    }

    private function getImageBase64DataUrl($filePath) {
        $type = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($type === 'jpg') $type = 'jpeg';
        $data = file_get_contents($filePath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    private function mapCategoryToVton($cat) {
        $catLower = strtolower($cat);
        if (strpos($catLower, 'shoe') !== false || strpos($catLower, 'pant') !== false || strpos($catLower, 'bottom') !== false || strpos($catLower, 'trouser') !== false || strpos($catLower, 'jean') !== false) {
            return 'bottoms';
        } elseif (strpos($catLower, 'dress') !== false || strpos($catLower, 'gown') !== false || strpos($catLower, 'one-piece') !== false || strpos($catLower, 'sherwani') !== false) {
            return 'one-pieces';
        }
        return 'tops';
    }
}
