<?php

namespace App\Services\BPJS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpjsAntreanService
{
    protected string $consId;
    protected string $secretKey;
    protected string $userKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->consId = config('bpjs.cons_id', '');
        $this->secretKey = config('bpjs.secret_key', '');
        $this->userKey = config('bpjs.user_key', '');
        
        $env = config('bpjs.env', 'dev');
        $baseUrls = config('bpjs.base_urls', []);
        $this->baseUrl = $baseUrls[$env] ?? 'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev';

        // Load LZString if needed
        $lzDir = __DIR__ . '/LZCompressor';
        if (is_dir($lzDir)) {
            foreach (glob($lzDir . '/*.php') as $file) {
                require_once $file;
            }
        }
    }

    /**
     * Generate BPJS standard auth headers
     */
    protected function generateHeaders(): array
    {
        date_default_timezone_set('UTC');
        $timestamp = strval(time());

        $signature = base64_encode(
            hash_hmac('sha256', $this->consId . "&" . $timestamp, $this->secretKey, true)
        );

        return [
            'x-cons-id' => $this->consId,
            'x-timestamp' => $timestamp,
            'x-signature' => $signature,
            'user_key' => $this->userKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Hit Check In (Update Waktu) to BPJS Antrean RS
     *
     * @param string $kodebooking
     * @param int $taskId 1 for New Patient, 3 for Existing Patient
     * @param int|null $waktuMs Timestamp in milliseconds
     * @return array
     */
    public function updateWaktuAntrean(string $kodebooking, int $taskId = 3, ?int $waktuMs = null): array
    {
        // If credentials are not set, return simulated response for testing
        if (empty($this->consId) || empty($this->secretKey) || empty($this->userKey)) {
            return [
                'status' => true,
                'is_simulated' => true,
                'http_code' => 200,
                'metadata' => [
                    'code' => 200,
                    'message' => 'Simulasi Sukses (Kredensial BPJS di .env belum diisi)',
                ],
                'response' => 'OK',
            ];
        }

        $url = rtrim($this->baseUrl, '/') . '/antrean/updatewaktu';
        $waktu = $waktuMs ?? (int)round(microtime(true) * 1000);

        $payload = [
            'kodebooking' => $kodebooking,
            'taskid' => $taskId,
            'waktu' => $waktu,
        ];

        try {
            $headers = $this->generateHeaders();
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post($url, $payload);

            $data = $response->json();
            $metadata = $data['metadata'] ?? [];

            return [
                'status' => ($metadata['code'] ?? 0) == 200,
                'http_code' => $response->status(),
                'metadata' => $metadata,
                'response' => $data['response'] ?? null,
                'raw' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('BPJS Check In Error: ' . $e->getMessage(), ['kodebooking' => $kodebooking]);
            return [
                'status' => false,
                'http_code' => 500,
                'metadata' => [
                    'code' => 500,
                    'message' => 'Gagal koneksi ke BPJS: ' . $e->getMessage(),
                ],
            ];
        }
    }

    /**
     * Hit Batal Antrean to BPJS Antrean RS
     *
     * @param string $kodebooking
     * @param string $keterangan Alasan pembatalan
     * @return array
     */
    public function batalAntrean(string $kodebooking, string $keterangan): array
    {
        if (empty($this->consId) || empty($this->secretKey) || empty($this->userKey)) {
            return [
                'status' => true,
                'is_simulated' => true,
                'http_code' => 200,
                'metadata' => [
                    'code' => 200,
                    'message' => 'Simulasi Sukses Batal (Kredensial BPJS di .env belum diisi)',
                ],
                'response' => 'OK',
            ];
        }

        $url = rtrim($this->baseUrl, '/') . '/antrean/batal';
        $payload = [
            'kodebooking' => $kodebooking,
            'keterangan' => $keterangan,
        ];

        try {
            $headers = $this->generateHeaders();
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post($url, $payload);

            $data = $response->json();
            $metadata = $data['metadata'] ?? [];

            return [
                'status' => ($metadata['code'] ?? 0) == 200,
                'http_code' => $response->status(),
                'metadata' => $metadata,
                'response' => $data['response'] ?? null,
                'raw' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('BPJS Batal Antrean Error: ' . $e->getMessage(), ['kodebooking' => $kodebooking]);
            return [
                'status' => false,
                'http_code' => 500,
                'metadata' => [
                    'code' => 500,
                    'message' => 'Gagal koneksi ke BPJS: ' . $e->getMessage(),
                ],
            ];
        }
    }
}
