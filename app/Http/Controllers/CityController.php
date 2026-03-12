<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Получить список всех активных городов
     */
    public function index()
    {
        $cities = City::getActiveCities();
        
        return response()->json([
            'success' => true,
            'cities' => $cities
        ]);
    }

    /**
     * Поиск городов по названию
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');
        
        if (empty($query)) {
            return $this->index();
        }
        
        $cities = City::searchByName($query);
        
        return response()->json([
            'success' => true,
            'cities' => $cities
        ]);
    }

    /**
     * Выбрать город и сохранить в сессию
     */
    public function select(Request $request)
    {
        $cityId = $request->input('city_id');
        
        if (empty($cityId)) {
            return response()->json([
                'success' => false,
                'message' => 'City ID is required'
            ], 400);
        }
        
        $city = City::find($cityId);
        
        if (!$city || !$city->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'City not found or inactive'
            ], 404);
        }
        
        // Сохраняем выбранный город в сессию
        session([
            'selected_city_id' => $city->id,
            'selected_city_name' => $city->name,
            'selected_city_slug' => $city->slug
        ]);
        
        return response()->json([
            'success' => true,
            'city' => [
                'id' => $city->id,
                'name' => $city->name,
                'slug' => $city->slug
            ]
        ]);
    }
}
