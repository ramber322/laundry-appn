<?php
namespace App\Services;

class SmsService
{
    public function send(string $number, string $message): bool
    {
        $apiKey   = env('TEXTBEE_API_KEY');
        $deviceId = env('TEXTBEE_DEVICE_ID');

        // TextBee requires E.164 format: +639171234567
        // If your DB stores 0917..., convert it
        if (str_starts_with($number, '0')) {
            $number = '+63' . substr($number, 1);
        }

        $payload = [
            'recipients' => [$number],
            'message'    => $message,
        ];

        // deviceId is optional — only pass it if you really want to lock to one phone
        if (!empty($deviceId)) {
            $payload['deviceId'] = $deviceId;
        }

        $ch = curl_init('https://api.textbee.dev/api/v1/gateway/send-sms');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 200 means TextBee accepted it and queued it on the phone — not yet "delivered"
        return $httpCode === 200;
    }
}