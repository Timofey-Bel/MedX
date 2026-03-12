<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ShortcodeParser;

class PageController extends Controller
{
    private ShortcodeParser $shortcodeParser;
    
    /**
     * Конструктор контроллера
     * 
     * @param ShortcodeParser $shortcodeParser - Парсер shortcode для обработки секций
     */
    public function __construct(ShortcodeParser $shortcodeParser)
    {
        $this->shortcodeParser = $shortcodeParser;
    }
    
    /**
     * Отображение статической страницы
     * 
     * Универсальный метод для отображения любой страницы из таблицы pages.
     * Страницы создаются динамически через добавление записей в БД.
     * Все ссылки в footer используют route('page', ['slug' => 'Название страницы'])
     * 
     * Поддерживает shortcode для вставки секций:
     * - [section guid="kawsfk9k"] - вставляет секцию из таблицы page_sections
     * 
     * Структура таблицы pages:
     * - id: уникальный идентификатор
     * - active: 1 = активна, 0 = неактивна
     * - name: системное имя страницы (slug)
     * - title: заголовок страницы
     * - parent_id: ID родительской страницы (для иерархии)
     * - content: HTML-контент страницы (может содержать shortcode)
     * - sort: порядок сортировки
     * 
     * @param string $slug - системное имя страницы (name) или ID
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Пытаемся найти страницу по name или по id
        // Сначала проверяем, является ли slug числом (ID)
        if (is_numeric($slug)) {
            $page = DB::selectOne("
                SELECT id, name, title, parent_id, content, active, sort
                FROM pages
                WHERE id = ? AND active = 1
                LIMIT 1
            ", [intval($slug)]);
        } else {
            // Ищем по системному имени (name)
            $page = DB::selectOne("
                SELECT id, name, title, parent_id, content, active, sort
                FROM pages
                WHERE name = ? AND active = 1
                LIMIT 1
            ", [$slug]);
        }

        // Если страница не найдена - показываем 404
        if (!$page) {
            abort(404, 'Страница не найдена');
        }

        // НОВОЕ: Парсим shortcode в контенте
        // Заменяем [section guid="..."] на реальный HTML из page_sections
        // parse() возвращает массив ['html' => string, 'css' => string, 'js' => string]
        $parsed = $this->shortcodeParser->parse($page->content);
        $page->content = $parsed['html'];

        // Получаем корзину и избранное из сессии для header
        $cart = session('cart', ['items' => []]);
        
        // Преобразование формата избранного для неавторизованных пользователей
        $sessionFavorites = session('favorites', []);
        $favorites = ['items' => []];
        
        if (is_array($sessionFavorites)) {
            // Если это массив ID (для неавторизованных)
            if (isset($sessionFavorites[0]) && !isset($sessionFavorites['items'])) {
                foreach ($sessionFavorites as $productId) {
                    $favorites['items'][$productId] = true;
                }
            } else {
                // Если уже в правильном формате (для авторизованных)
                $favorites = $sessionFavorites;
            }
        }

        return view('page.show', [
            'page' => $page,
            'title' => $page->title ?: $page->name,
            'cart' => $cart,
            'favorites' => $favorites,
            'sectionCss' => $parsed['css'] ?? '',
            'sectionJs' => $parsed['js'] ?? ''
        ]);
    }
}
