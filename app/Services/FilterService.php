<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FilterService
{
    /**
     * Получение списка авторов для фильтра
     * Адаптировано из legacy/site/modules/sfera/filter/filter.class.php -> getAuthors
     *
     * @param int $limit Количество авторов для отображения
     * @return array
     */
    public function getAuthors(int $limit = 4): array
    {
        $query = "SELECT 
                    author_name,
                    COUNT(*) AS cnt
                  FROM authors a
                  GROUP BY author_name
                  ORDER BY cnt DESC
                  LIMIT ?";
        
        $result = DB::select($query, [$limit]);
        
        $authors = [];
        foreach ($result as $row) {
            $authors[] = [
                'name' => $row->author_name,
                'count' => (int)$row->cnt
            ];
        }
        
        return $authors;
    }

    // Здесь будут добавлены другие методы фильтрации (getAges, getSeries и т.д.)

    /**
     * Получение списка возрастов для фильтра
     * @return array
     */
    public function getAges(): array
    {
        $query = "SELECT age, COUNT(*) AS cnt 
                  FROM ages 
                  GROUP BY age 
                  ORDER BY age ASC";

        $result = DB::select($query);

        $ages = [];
        $zeroPlus = null;

        foreach ($result as $row) {
            $value = trim($row->age);

            // Пропускаем пустые значения
            if (empty($value)) {
                continue;
            }

            $ageData = [
                'value' => $value,
                'count' => (int)$row->cnt
            ];

            // Отдельно сохраняем "0+" для размещения в начале
            // Проверяем различные варианты записи "0+"
            if (stripos($value, '0+') !== false || 
                $value === '0' || 
                $value === '0-' ||
                preg_match('/^0\s*\+/i', $value) ||
                preg_match('/^0\s*-\s*0/i', $value)) {
                // Если уже есть запись "0+", объединяем счетчики
                if ($zeroPlus !== null) {
                    $zeroPlus['count'] += $ageData['count'];
                    // Используем более полное значение (содержащее "+")
                    if (stripos($value, '+') !== false || stripos($value, '0+') !== false) {
                        $zeroPlus['value'] = $value;
                    }
                } else {
                    $zeroPlus = $ageData;
                }
            } else {
                // Добавляем максимальное число для сортировки
                $ageData['max_age'] = $this->extractAgeNumber($value);
                $ages[] = $ageData;
            }
        }

        // Сортируем возрасты по возрастанию максимального числа в диапазоне
        usort($ages, function($a, $b) {
            // Сравниваем по максимальному числу
            if ($a['max_age'] !== $b['max_age']) {
                return $a['max_age'] - $b['max_age'];
            }

            // Если максимальные числа равны, сравниваем по строке
            return strcmp($a['value'], $b['value']);
        });

        // Добавляем "0+" в начало, если он был найден
        if ($zeroPlus !== null) {
            array_unshift($ages, $zeroPlus);
        }

        return $ages;
    }

    /**
     * Извлечение числового значения возраста из строки
     * Для диапазонов (например, "3-11 лет") возвращает максимальное число
     * @param string $ageString Строка с возрастом (например, "3-5 лет", "7+", "12", "3-11 лет")
     * @return int Числовое значение возраста (максимальное для диапазонов)
     */
    private function extractAgeNumber($ageString): int
    {
        // Ищем все числа в строке (используем более точный паттерн)
        if (preg_match_all('/\d+/', $ageString, $matches)) {
            $numbers = array_map('intval', $matches[0]);
            // Возвращаем максимальное число (для диапазонов это будет второе число)
            $maxNum = max($numbers);
            return $maxNum;
        }

        // Если число не найдено, возвращаем большое число для сортировки в конец
        return 999;
    }

    /**
     * Получение списка серий для фильтра
     * Адаптировано из legacy системы
     * 
     * @param int $limit Количество серий для отображения (0 = все)
     * @return array Массив серий с количеством товаров
     */
    public function getSeries(int $limit = 0): array
    {
        $query = "SELECT 
                    id,
                    value AS name,
                    cnt AS count
                  FROM v_seriya
                  ORDER BY cnt DESC, value ASC";
        
        if ($limit > 0) {
            $query .= " LIMIT ?";
            $result = DB::select($query, [$limit]);
        } else {
            $result = DB::select($query);
        }
        
        $series = [];
        foreach ($result as $row) {
            $series[] = [
                'id' => (int)$row->id,
                'name' => $row->name,
                'count' => (int)$row->count
            ];
        }
        
        return $series;
    }

    /**
     * Получение списка типов товаров для фильтра
     * Адаптировано из legacy системы
     * 
     * @param int $limit Количество типов для отображения (0 = все)
     * @return array Массив типов товаров с количеством товаров
     */
    public function getProductTypes(int $limit = 0): array
    {
        $query = "SELECT 
                    id,
                    value AS name,
                    cnt AS count
                  FROM v_tip_tovara
                  ORDER BY cnt DESC, value ASC";
        
        if ($limit > 0) {
            $query .= " LIMIT ?";
            $result = DB::select($query, [$limit]);
        } else {
            $result = DB::select($query);
        }
        
        $productTypes = [];
        foreach ($result as $row) {
            $productTypes[] = [
                'id' => (int)$row->id,
                'name' => $row->name,
                'count' => (int)$row->count
            ];
        }
        
        return $productTypes;
    }

    /**
     * Получение списка тематик для фильтра
     * Адаптировано из legacy системы
     * 
     * @param int $limit Количество тематик для отображения (0 = все)
     * @return array Массив тематик с количеством товаров
     */
    public function getTopics(int $limit = 0): array
    {
        $query = "SELECT 
                    id,
                    value AS name,
                    cnt AS count
                  FROM v_tematika
                  ORDER BY cnt DESC, value ASC";
        
        if ($limit > 0) {
            $query .= " LIMIT ?";
            $result = DB::select($query, [$limit]);
        } else {
            $result = DB::select($query);
        }
        
        $topics = [];
        foreach ($result as $row) {
            $topics[] = [
                'id' => (int)$row->id,
                'name' => $row->name,
                'count' => (int)$row->count
            ];
        }
        
        return $topics;
    }

    /**
     * Применить фильтр по сериям к запросу товаров
     * 
     * @param array $seriesIds Массив ID серий для фильтрации
     * @param string $baseQuery Базовый SQL запрос
     * @param array $bindings Массив параметров для запроса
     * @return array ['query' => string, 'bindings' => array]
     */
    public function applySeriesFilter(array $seriesIds, string $baseQuery, array $bindings): array
    {
        if (empty($seriesIds)) {
            return ['query' => $baseQuery, 'bindings' => $bindings];
        }

        // Получаем названия серий по ID
        $placeholders = implode(',', array_fill(0, count($seriesIds), '?'));
        $seriesNames = DB::select("SELECT value FROM v_seriya WHERE id IN ($placeholders)", $seriesIds);
        
        if (empty($seriesNames)) {
            return ['query' => $baseQuery, 'bindings' => $bindings];
        }

        $names = array_map(fn($s) => $s->value, $seriesNames);
        $placeholders = implode(',', array_fill(0, count($names), '?'));
        
        $baseQuery .= " AND EXISTS (
            SELECT 1 FROM attributes a 
            WHERE a.product_id = p.id 
            AND BINARY a.name = 'Серия' 
            AND BINARY a.value IN ($placeholders)
        )";
        
        $bindings = array_merge($bindings, $names);
        
        return ['query' => $baseQuery, 'bindings' => $bindings];
    }

    /**
     * Применить фильтр по типам товаров к запросу товаров
     * 
     * @param array $productTypeIds Массив ID типов товаров для фильтрации
     * @param string $baseQuery Базовый SQL запрос
     * @param array $bindings Массив параметров для запроса
     * @return array ['query' => string, 'bindings' => array]
     */
    public function applyProductTypeFilter(array $productTypeIds, string $baseQuery, array $bindings): array
    {
        if (empty($productTypeIds)) {
            return ['query' => $baseQuery, 'bindings' => $bindings];
        }

        // Получаем названия типов товаров по ID
        $placeholders = implode(',', array_fill(0, count($productTypeIds), '?'));
        $productTypeNames = DB::select("SELECT value FROM v_tip_tovara WHERE id IN ($placeholders)", $productTypeIds);
        
        if (empty($productTypeNames)) {
            return ['query' => $baseQuery, 'bindings' => $bindings];
        }

        $names = array_map(fn($pt) => $pt->value, $productTypeNames);
        $placeholders = implode(',', array_fill(0, count($names), '?'));
        
        $baseQuery .= " AND EXISTS (
            SELECT 1 FROM attributes a 
            WHERE a.product_id = p.id 
            AND BINARY a.name = 'Тип товара' 
            AND BINARY a.value IN ($placeholders)
        )";
        
        $bindings = array_merge($bindings, $names);
        
        return ['query' => $baseQuery, 'bindings' => $bindings];
    }

    /**
     * Применить фильтр по тематикам к запросу товаров
     * 
     * @param array $topicIds Массив ID тематик для фильтрации
     * @param string $baseQuery Базовый SQL запрос
     * @param array $bindings Массив параметров для запроса
     * @return array ['query' => string, 'bindings' => array]
     */
    public function applyTopicFilter(array $topicIds, string $baseQuery, array $bindings): array
    {
        if (empty($topicIds)) {
            return ['query' => $baseQuery, 'bindings' => $bindings];
        }

        // Получаем названия тематик по ID
        $placeholders = implode(',', array_fill(0, count($topicIds), '?'));
        $topicNames = DB::select("SELECT value FROM v_tematika WHERE id IN ($placeholders)", $topicIds);
        
        if (empty($topicNames)) {
            return ['query' => $baseQuery, 'bindings' => $bindings];
        }

        $names = array_map(fn($t) => $t->value, $topicNames);
        $placeholders = implode(',', array_fill(0, count($names), '?'));
        
        $baseQuery .= " AND EXISTS (
            SELECT 1 FROM attributes a 
            WHERE a.product_id = p.id 
            AND BINARY a.name = 'Тематика' 
            AND BINARY a.value IN ($placeholders)
        )";
        
        $bindings = array_merge($bindings, $names);
        
        return ['query' => $baseQuery, 'bindings' => $bindings];
    }
}
