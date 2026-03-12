<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для работы с API DaData
 * 
 * Документация: https://dadata.ru/api/
 */
class DaDataService
{
    /**
     * Базовый URL API DaData
     */
    protected string $baseUrl = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs';

    /**
     * Токен API
     */
    protected string $token;

    /**
     * Секретный ключ (опционально)
     */
    protected ?string $secret;

    public function __construct()
    {
        $this->token = config('services.dadata.token', env('DADATA_TOKEN'));
        $this->secret = config('services.dadata.secret', env('DADATA_SECRET'));
    }

    /**
     * Найти организацию по ИНН
     * 
     * @param string $inn ИНН организации (10 или 12 цифр)
     * @return array|null Данные организации или null, если не найдена
     */
    public function findByInn(string $inn): ?array
    {
        // Валидация ИНН
        $inn = preg_replace('/\D/', '', $inn);
        
        if (!$this->validateInn($inn)) {
            return null;
        }

        // Проверяем кеш (кешируем на 24 часа)
        $cacheKey = "dadata_inn_{$inn}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Token {$this->token}",
            ])->post("{$this->baseUrl}/findById/party", [
                'query' => $inn,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['suggestions']) && count($data['suggestions']) > 0) {
                    $result = $data['suggestions'][0];
                    
                    // Кешируем результат на 24 часа
                    Cache::put($cacheKey, $result, now()->addHours(24));
                    
                    return $result;
                }
            }

            Log::warning('DaData: Organization not found', [
                'inn' => $inn,
                'status' => $response->status(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('DaData API Error', [
                'inn' => $inn,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Валидация ИНН
     * 
     * @param string $inn ИНН (10 или 12 цифр)
     * @return bool
     */
    public function validateInn(string $inn): bool
    {
        $inn = preg_replace('/\D/', '', $inn);
        
        // ИНН должен быть 10 или 12 цифр
        if (!in_array(strlen($inn), [10, 12])) {
            return false;
        }

        // Проверка контрольной суммы для ИНН из 10 цифр (юридические лица)
        if (strlen($inn) === 10) {
            $checksum = (
                2 * $inn[0] + 4 * $inn[1] + 10 * $inn[2] +
                3 * $inn[3] + 5 * $inn[4] + 9 * $inn[5] +
                4 * $inn[6] + 6 * $inn[7] + 8 * $inn[8]
            ) % 11 % 10;

            return $checksum == $inn[9];
        }

        // Проверка контрольной суммы для ИНН из 12 цифр (физические лица)
        if (strlen($inn) === 12) {
            $checksum1 = (
                7 * $inn[0] + 2 * $inn[1] + 4 * $inn[2] +
                10 * $inn[3] + 3 * $inn[4] + 5 * $inn[5] +
                9 * $inn[6] + 4 * $inn[7] + 6 * $inn[8] +
                8 * $inn[9]
            ) % 11 % 10;

            $checksum2 = (
                3 * $inn[0] + 7 * $inn[1] + 2 * $inn[2] +
                4 * $inn[3] + 10 * $inn[4] + 3 * $inn[5] +
                5 * $inn[6] + 9 * $inn[7] + 4 * $inn[8] +
                6 * $inn[9] + 8 * $inn[10]
            ) % 11 % 10;

            return $checksum1 == $inn[10] && $checksum2 == $inn[11];
        }

        return false;
    }

    /**
     * Очистить кеш для конкретного ИНН
     * 
     * @param string $inn
     * @return bool
     */
    public function clearCache(string $inn): bool
    {
        $inn = preg_replace('/\D/', '', $inn);
        $cacheKey = "dadata_inn_{$inn}";
        
        return Cache::forget($cacheKey);
    }

    /**
     * Получить подсказки по названию организации
     * 
     * @param string $query Поисковый запрос
     * @param int $count Количество результатов (по умолчанию 10)
     * @return array
     */
    public function suggestByName(string $query, int $count = 10): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Token {$this->token}",
            ])->post("{$this->baseUrl}/suggest/party", [
                'query' => $query,
                'count' => $count,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['suggestions'] ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error('DaData Suggest API Error', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
