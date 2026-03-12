<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstalledApp;
use App\Models\AppDesktopShortcut;
use App\Models\AppRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Контроллер для админ-панели с Windows 10 Desktop интерфейсом
 * 
 * Отвечает за отображение рабочего стола администратора с ExtJS Desktop shell
 * и предоставление данных для desktop shortcuts, установленных приложений и т.д.
 */
class AdminDesktopController extends Controller
{
    /**
     * Отображение главной страницы админ-панели (Desktop)
     * 
     * Загружает ExtJS Desktop shell с Windows 10 интерфейсом:
     * - Desktop Area с ярлыками
     * - Taskbar с кнопкой Start, открытыми окнами, system tray и часами
     * - Start Menu с тремя панелями (user icons, apps list, quick access tiles)
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Получаем установленные приложения
        $installedApps = $this->getInstalledApps();
        
        // Получаем ярлыки рабочего стола
        $desktopShortcuts = $this->getDesktopShortcuts();
        
        // Получаем приложения для автозапуска (если есть)
        $startApps = $this->getStartApps();
        
        // Получаем ярлыки быстрого доступа (Quick Access)
        $quickAccessShortcuts = $this->getQuickAccessShortcuts();
        
        // Получаем права пользователя (ACL)
        $userPermissions = $this->getUserPermissions();
        
        // Получаем данные текущего администратора из сессии
        $adminUser = session('admin_user');
        
        // Получаем пользовательские названия ярлыков
        $customShortcutNames = $this->getCustomShortcutNames();
        
        return view('admin.desktop.index', [
            'installed_apps' => $installedApps,
            'desktop_shortcuts' => $desktopShortcuts,
            'start_apps' => $startApps,
            'quick_access_shortcuts' => $quickAccessShortcuts,
            'moduleAccess' => $userPermissions,  // Переименовано из user_permissions
            'admin_user' => $adminUser,           // Добавлены данные администратора
            'custom_shortcut_names' => $customShortcutNames  // Пользовательские названия ярлыков
        ]);
    }
    
    /**
     * Получение списка установленных приложений
     * 
     * Возвращает активные приложения из таблицы installed_apps
     * для отображения в Start Menu
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getInstalledApps()
    {
        return InstalledApp::where('status', 'active')
            ->orderBy('category')
            ->orderBy('name')
            ->get(['app_id', 'name', 'description', 'icon', 'icon_color', 'category']);
    }
    
    /**
     * Получение ярлыков рабочего стола
     * 
     * Возвращает shortcuts с флагом show_on_desktop=1
     * для отображения на Desktop Area
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getDesktopShortcuts()
    {
        return AppDesktopShortcut::join('installed_apps', 'app_desktop_shortcuts.app_id', '=', 'installed_apps.app_id')
            ->where('installed_apps.status', 'active')
            ->where('app_desktop_shortcuts.enabled', 1)
            ->where('app_desktop_shortcuts.show_on_desktop', 1)
            ->orderBy('app_desktop_shortcuts.sort_order')
            ->get([
                'app_desktop_shortcuts.id',
                'app_desktop_shortcuts.app_id',
                'app_desktop_shortcuts.title',
                'app_desktop_shortcuts.icon',
                'app_desktop_shortcuts.icon_color',
                'app_desktop_shortcuts.function_name'
            ]);
    }
    
    /**
     * Получение приложений для автозапуска
     * 
     * Возвращает приложения, которые должны открыться автоматически
     * при загрузке desktop (если такая функциональность будет реализована)
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getStartApps()
    {
        // Пока возвращаем пустую коллекцию
        // В будущем можно добавить поле auto_start в installed_apps
        return collect([]);
    }
    
    /**
     * Получение ярлыков быстрого доступа (Quick Access)
     * 
     * Возвращает shortcuts с флагом show_in_quick_access=1
     * для отображения в правой панели Start Menu (4x grid, 90px tiles)
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getQuickAccessShortcuts()
    {
        return AppDesktopShortcut::join('installed_apps', 'app_desktop_shortcuts.app_id', '=', 'installed_apps.app_id')
            ->where('installed_apps.status', 'active')
            ->where('app_desktop_shortcuts.enabled', 1)
            ->where('app_desktop_shortcuts.show_in_quick_access', 1)
            ->orderBy('app_desktop_shortcuts.sort_order')
            ->get([
                'app_desktop_shortcuts.id',
                'app_desktop_shortcuts.app_id',
                'app_desktop_shortcuts.title',
                'app_desktop_shortcuts.icon',
                'app_desktop_shortcuts.icon_color',
                'app_desktop_shortcuts.function_name'
            ]);
    }
    
    /**
     * Получение прав пользователя (ACL)
     * 
     * Возвращает массив прав текущего пользователя для проверки доступа
     * к различным модулям и функциям админ-панели
     * 
     * Пока возвращаем все права (для тестирования без авторизации)
     * В будущем будет интеграция с Laravel Gates и admin_permissions
     * 
     * @return array
     */
    protected function getUserPermissions()
    {
        // TODO: Интеграция с Laravel Gates и admin_permissions
        // Пока возвращаем mock данные - все права разрешены
        
        // В будущем будет примерно так:
        // $user = auth()->user();
        // return DB::table('admin_user_roles')
        //     ->join('admin_role_permissions', 'admin_user_roles.role_id', '=', 'admin_role_permissions.role_id')
        //     ->join('admin_permissions', 'admin_role_permissions.permission_id', '=', 'admin_permissions.id')
        //     ->where('admin_user_roles.user_id', $user->id)
        //     ->pluck('admin_permissions.module', 'admin_permissions.action')
        //     ->toArray();
        
        return [
            'permissions' => true,      // Доступ к управлению правами
            'users' => true,            // Доступ к управлению пользователями
            'products' => true,         // Доступ к управлению товарами
            'import' => true,           // Доступ к импорту данных
            'app_installer' => true     // Доступ к установщику приложений
            // Для установленных приложений права будут проверяться динамически
        ];
    }
    
    /**
     * Получение пользовательских названий ярлыков
     * 
     * Возвращает массив данных ярлыков для текущего пользователя
     * в формате [shortcut_id => ['custom_name' => ..., 'position_x' => ..., 'position_y' => ...]]
     * 
     * @return array
     */
    protected function getCustomShortcutNames()
    {
        $userId = session('admin_user')['id'] ?? null;
        
        if (!$userId) {
            return [];
        }
        
        return DB::table('desktop_shortcuts')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('shortcut_id')
            ->map(function ($item) {
                return [
                    'custom_name' => $item->custom_name,
                    'original_name' => $item->original_name,
                    'position_x' => $item->position_x,
                    'position_y' => $item->position_y
                ];
            })
            ->toArray();
    }
}
