<?php

if (!function_exists('encryptId')) {
    function encryptId(?int $id): string
    {
        $function_name = 'encryptId';
        try {
            if (!is_numeric($id)) {
                throw new InvalidArgumentException('Invalid input. The ID must be numeric.');
            }
            $salt = 65958579;
            return base64_encode($id * $salt);
        } catch (Exception | InvalidArgumentException $e) {
            logCatchException($e, 'helpers.php', $function_name);
            throw $e;
        }
    }
}

if (!function_exists('encryptString')) {
    function encryptString($string)
    {
        $function_name = 'encryptString';
        try {
            $salt = 'TejFcfFd*$#FFGHdDksFs%^7620fJFdl';
            $saltedString = $string . $salt;
            return base64_encode($saltedString);
        } catch (Exception | InvalidArgumentException $e) {
            logCatchException($e, 'helpers.php', $function_name);
            throw $e;
        }
    }
}

if (!function_exists('decryptId')) {
    function decryptId(string $encryptedId): ?int
    {
        $function_name = 'decryptId';
        try {
            $salt = 65958579;
            $decodedValue = base64_decode($encryptedId);

            if ($decodedValue === false || !is_numeric($decodedValue)) {
                throw new InvalidArgumentException('Invalid input. Unable to decode the encrypted ID.');
            }

            $id = $decodedValue / $salt;

            if (is_numeric($id)) {
                return (int) $id;
            } else {
                throw new InvalidArgumentException('Decryption failed. The resulting value is not numeric.');
            }
        } catch (Exception | InvalidArgumentException $e) {
            logCatchException($e, 'helpers.php', $function_name);
            return null; // Return null in case of failure
        }
    }
}


if (!function_exists('logCatchException')) {
    function logCatchException(Exception $e, $controller_name, $function_name, $channel = null): void
    {
        logger()->channel($channel)->error("$controller_name : $function_name : Exception :", [
            "Exception" => $e->getMessage(),
            "\nTraceAsString" => $e->getTraceAsString(),
            "\nall_request" => request()->all(),
            // "\ncandidate_detail" => auth('candidateApi')->user(),
            // "\npromoter_detail" => auth('promoter')->user(),
            "\nall_headers" => request()->headers->all(),
        ]);
    }
}

if (!function_exists('logValidationException')) {
    function logValidationException($controller_name, $function_name, $validator, $channel = 'validation'): void
    {
        logger()->channel($channel)->error("$controller_name : $function_name : Validation error occurred. :", [
            "errors_message" => $validator->errors()->all(),
            "\nkey_failed" => $validator->failed(),
            "\nall_request" => request()->all(),
            // "\ncandidate_detail" => auth('candidateApi')->user(),
            // "\npromoter_detail" => auth('promoter')->user(),
            "\nall_headers" => request()->headers->all(),
        ]);
    }
}

if (!function_exists('generateOTP')) {
    function generateOTP($calling_function)
    {
        $function_name = 'generateOTP';
        try {
            $otp = rand(100000, 999999);
            logInfo('helpers.php', $function_name, ['calling_function' => $calling_function, 'otp' => $otp], 'api');
            return $otp;
        } catch (Exception $e) {
            logCatchException($e, 'helpers.php', $function_name);
            return 000000;
        }
    }
}

if (!function_exists('logInfo')) {
    function logInfo($controller_name, $function_name, $dataOrMessage = [], $channel = null): void
    {
        logger()->channel($channel)->info("$controller_name : $function_name :", [
            "DataOrMessage" => $dataOrMessage,
            "\nall_request" => request()->all(),
            "\ncandidate_detail" => auth('web')->user(),
            // "\npromoter_detail" => auth('')->user(),
            "\nall_headers" => request()->headers->all(),
        ]);
    }
}
function logError($controller_name, $function_name, $dataOrMessage = [], $channel = null): void
{
    logger()->channel($channel)->error("$controller_name : $function_name :", [
        "DataOrMessage" => $dataOrMessage,
        "\nall_request" => request()->all(),
        // "\nuser_detail" => auth('candidateApi')->user(),
        "\nall_headers" => request()->headers->all(),
    ]);
}

if (!function_exists('sendWhatsAppOtp')) {
    function sendWhatsAppOtp($mobile_no, $templateId, $params)
    {
        $function_name = 'sendWhatsAppOtp';
        try {
            $apiKey = config('constants.whatsapp_api_key');
            $url = 'https://api.gupshup.io/sm/api/v1/template/msg';
            $destination_number = '91' . $mobile_no;

            $postData = [
                'channel' => 'whatsapp',
                'source' => config('constants.whatsapp_source_number'),
                'destination' => $destination_number,
                'template' => '{"id": "' . $templateId . '", "params": ' . $params . '}',
            ];

            $headers = [
                'apikey: ' . $apiKey,
                'Content-Type: application/x-www-form-urlencoded',
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            logCatchException($e, 'helpers.php', $function_name);
            return [];
        }
    }

    if (!function_exists('sendEmail')) {
        function sendEmail($data)
        {
            $function_name = 'sendEmail';
            try {
                Mail::to($data['to'])->send(new \App\Mail\sendEmail($data));
            } catch (Exception $e) {
                logCatchException($e, 'helpers.php', $function_name);
                return [];
            }
        }
    }

    if (!function_exists('encryptResponse')) {
        function encryptResponse($key, $iv, $data): string
        {
            $OPENSSL_CIPHER_NAME = "aes-128-cbc";
            $CIPHER_KEY_LEN = 16;

            if (strlen($key) < $CIPHER_KEY_LEN) {
                $key = str_pad("$key", $CIPHER_KEY_LEN, "0"); //0 pad to len 16
            } else if (strlen($key) > $CIPHER_KEY_LEN) {
                $key = substr($key, 0, $CIPHER_KEY_LEN); //truncate to 16 bytes
            }

            $encodedEncryptedData = base64_encode(openssl_encrypt($data, $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv));
            $encodedIV = base64_encode($iv);
            $encryptedPayload = '"' . $encodedEncryptedData . ':' . $encodedIV . '"';

            return $encryptedPayload;
        }
    }

    if (!function_exists('decryptResponse')) {
        function decryptResponse($key, $data): bool|string
        {
            $OPENSSL_CIPHER_NAME = "aes-128-cbc";
            $CIPHER_KEY_LEN = 16;

            if (strlen($key) < $CIPHER_KEY_LEN) {
                $key = str_pad("$key", $CIPHER_KEY_LEN, "0");
            } else if (strlen($key) > $CIPHER_KEY_LEN) {
                $key = substr($key, 0, $CIPHER_KEY_LEN);
            }

            $parts = explode(':', $data);

            if (!empty($parts[1])) {

                $decryptedData = openssl_decrypt(base64_decode($parts[0]), $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
                return $decryptedData;
            } else {
                return false;
            }
        }
    }

}