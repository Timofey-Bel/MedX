<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DaDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API контроллер для работы с DaData
 */
class DaDataController extends Controller
{
    protected DaDataService $dadataService;

    public function __construct(DaDataService $dadataService)
    {
        $this->dadataService = $dadataService;
    }

    /**
     * Проверить ИНН организации
     * 
     * POST /api/dadata/check-inn
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkInn(Request $request): JsonResponse
    {
        $request->validate([
            'inn' => 'required|string|min:10|max:12',
        ]);

        $inn = $request->input('inn');

        // Валидация ИНН
        if (!$this->dadataService->validateInn($inn)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный формат ИНН',
            ], 422);
        }

        // Поиск организации
        $organization = $this->dadataService->findByInn($inn);

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Организация с таким ИНН не найдена',
            ], 404);
        }

        // Проверка статуса организации
        $status = $organization['data']['state']['status'] ?? 'UNKNOWN';
        
        if ($status === 'LIQUIDATED' || $status === 'LIQUIDATING') {
            return response()->json([
                'success' => false,
                'message' => 'Организация ликвидирована или находится в процессе ликвидации',
                'data' => $this->formatOrganizationData($organization),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Организация найдена',
            'data' => $this->formatOrganizationData($organization),
        ]);
    }

    /**
     * Форматировать данные организации для фронтенда
     * 
     * @param array $organization
     * @return array
     */
    protected function formatOrganizationData(array $organization): array
    {
        $data = $organization['data'] ?? [];
        
        return [
            'inn' => $data['inn'] ?? '',
            'kpp' => $data['kpp'] ?? '',
            'ogrn' => $data['ogrn'] ?? '',
            'name_full' => $data['name']['full_with_opf'] ?? $organization['value'] ?? '',
            'name_short' => $data['name']['short_with_opf'] ?? '',
            'legal_address' => $data['address']['value'] ?? '',
            'postal_code' => $data['address']['data']['postal_code'] ?? '',
            'director_name' => $data['management']['name'] ?? '',
            'director_position' => $data['management']['post'] ?? '',
            'opf' => $data['opf']['full'] ?? '',
            'opf_short' => $data['opf']['short'] ?? '',
            'status' => $data['state']['status'] ?? 'UNKNOWN',
            'registration_date' => $data['state']['registration_date'] ?? null,
        ];
    }
}
