<?php
namespace App\Services;

class SmsService
{
    /**
     * Send a single SMS via TextBee.
     * Returns ['success' => bool, 'message' => string].
     */
    public function send(string $number, string $message): array
    {
        $apiKey   = env('TEXTBEE_API_KEY');
        $deviceId = env('TEXTBEE_DEVICE_ID');

        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'SMS API key not configured'];
        }

        // Normalize 0917... → +63917...
        $number = trim($number);
        if (str_starts_with($number, '0')) {
            $number = '+63' . substr($number, 1);
        }
        // Strip any dashes or spaces
        $number = preg_replace('/[^\d+]/', '', $number);

        if (!preg_match('/^\+\d{10,15}$/', $number)) {
            return ['success' => false, 'message' => 'Invalid phone number format'];
        }

        $payload = [
            'recipients' => [$number],
            'message'    => $message,
        ];
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

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'message' => 'Network error: ' . $curlError];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'message' => 'SMS sent'];
        }

        // Try to extract a useful error from TextBee's JSON response
        $decoded = json_decode($response, true);
        $reason = $decoded['message'] ?? $decoded['error'] ?? "HTTP $httpCode";

        return ['success' => false, 'message' => $reason];
    }

    /**
     * Fill placeholders in an SMS template.
     */
    public function buildMessage(string $template, array $order, array $settings): string
    {
        return strtr($template, [
            '{name}'          => $order['name'] ?? '',
            '{code}'          => $order['claim_code'] ?? '',
            '{weight}'        => $order['weight'] ?? '',
            '{tracking_link}' => base_url('track/' . ($order['claim_code'] ?? '')),
            '{shop_name}'     => $settings['shop_name'] ?? '',
        ]);
    }
}