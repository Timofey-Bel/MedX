# Design Document: Admin Panel Migration

## Overview

This design document specifies the architecture for migrating the legacy admin panel from a custom ExtJS + Smarty + custom PHP implementation to Laravel while preserving the EXISTING Windows 10 Desktop interface. The migration is a backend modernization, not a UI rewrite.

### Key Architectural Decisions

1. **ExtJS Desktop Shell REMAINS UNCHANGED**: The fully implemented Windows 10 Desktop UI (desktop, taskbar, start menu, window management) in `legacy/site/modules/admin/desktop/desktop.tpl` will be preserved with only API endpoint updates.

2. **Iframe Architecture**: ExtJS windows contain `<iframe>` elements that load Laravel routes returning Blade templates.

3. **No New JavaScript Frameworks**: Developers already know ExtJS. Window content uses HTML/CSS/vanilla JavaScript or jQuery (same patterns as public site).

4. **Package System Modernization**: The sophisticated installable package system will be reimplemented using Laravel services while maintaining the same manifest.json structure.

5. **ACL Integration**: Legacy ACL system integrates with Laravel Gates and Policies.

### Migration Scope

**What Changes:**
- Backend: Custom PHP → Laravel controllers/services
- Templates: Smarty → Blade
- API endpoints: `/site/modules/admin/...` → `/admin/...`
- Package installer: Custom PHP classes → Laravel services

**What Stays:**
- ExtJS 4.2.1 Desktop shell (desktop, taskbar, start menu, windows)
- Windows 10 UI styling
- Window management behavior
- Desktop shortcuts and quick access tiles
- Material Icons



## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    ExtJS Desktop Shell (UNCHANGED)               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Desktop Area (Windows 10 wallpaper, shortcuts, logo)    │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Taskbar (Start button, open windows, system tray, clock)│  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Start Menu (3-panel: user icons, apps list, tiles)      │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Ext.window.Window (draggable, resizable, minimizable)   │  │
│  │  ┌────────────────────────────────────────────────────┐  │  │
│  │  │  <iframe src="/admin/products">                    │  │  │
│  │  │  ┌──────────────────────────────────────────────┐  │  │  │
│  │  │  │  Laravel Controller → Blade View             │  │  │  │
│  │  │  │  HTML/CSS/jQuery                             │  │  │  │
│  │  │  └──────────────────────────────────────────────┘  │  │  │
│  │  └────────────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Laravel Backend (NEW)                       │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Admin Controllers (Products, Orders, Users, etc.)       │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Package System Services                                  │  │
│  │  - PackageInstaller                                       │  │
│  │  - PackageUninstaller                                     │  │
│  │  - RouteManager                                           │  │
│  │  - ManifestParser                                         │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  ACL System (Laravel Gates/Policies)                     │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Database (MySQL 10.4+)                                   │  │
│  │  - installed_apps, app_routes, app_desktop_shortcuts     │  │
│  │  - admin_permissions, admin_users, admin_roles           │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### Technology Stack

#### Frontend (Desktop Shell) - UNCHANGED
- **ExtJS 4.2.1**: Desktop module, window management, taskbar
- **Material Icons**: Icon library for shortcuts and UI elements
- **Custom CSS**: Windows 10 styling (already implemented)

#### Frontend (Window Content) - NEW
- **Laravel Blade**: Templating engine (replaces Smarty)
- **HTML/CSS**: Standard markup and styling
- **jQuery**: DOM manipulation and AJAX (optional, can use vanilla JS)
- **Iframe isolation**: Clean separation between shell and content

#### Backend - NEW
- **Laravel 12**: PHP framework
- **PHP 8.5**: Programming language
- **MySQL 10.4+**: Database
- **Laravel Gates/Policies**: Authorization system



## Components and Interfaces

### 1. Package System Components

#### 1.1 Package Structure

```
.inst/{package-name}/
├── manifest.json           # Package metadata and configuration
├── install.php            # Installation script (optional)
├── backend/               # Laravel controllers, models, services
│   ├── Controllers/
│   ├── Models/
│   └── Services/
├── frontend/              # Blade views and assets
│   ├── views/
│   └── assets/
│       ├── css/
│       └── js/
├── public/                # Public assets (images, fonts, etc.)
└── database/              # Database migrations and seeds
    ├── schema.sql         # Database schema
    ├── permissions.sql    # ACL permissions
    └── rollback.sql       # Rollback script
```

#### 1.2 Manifest Schema

```json
{
  "id": "products",
  "name": "Product Management",
  "version": "1.0.0",
  "author": "SFERA",
  "description": "Manage products, categories, and inventory",
  "category": "E-commerce",
  "icon": "inventory_2",
  "icon_color": "#4CAF50",
  "requirements": {
    "php": ">=8.5",
    "mysql": ">=10.4",
    "dependencies": []
  },
  "installation": {
    "backend": "backend/",
    "frontend": "frontend/",
    "public": "public/",
    "database": "database/"
  },
  "routing": {
    "admin": [
      {
        "path": "/admin/products",
        "controller": "ProductController@index",
        "method": "GET",
        "auth": true,
        "permission": "products.view"
      }
    ],
    "public": []
  },
  "permissions": [
    {
      "module": "products",
      "action": "view",
      "title": "View Products",
      "description": "View product list and details"
    },
    {
      "module": "products",
      "action": "create",
      "title": "Create Products",
      "description": "Create new products"
    }
  ],
  "desktop": {
    "shortcut": {
      "title": "Products",
      "icon": "inventory_2",
      "icon_color": "#4CAF50",
      "function_name": "openProducts",
      "show_on_desktop": true,
      "show_in_quick_access": true
    }
  }
}
```



#### 1.3 PackageInstaller Service

```php
namespace App\Services\Admin;

class PackageInstaller
{
    public function install(string $packagePath, array $options = []): InstallationResult
    {
        // 1. Parse manifest.json
        $manifest = $this->manifestParser->parse($packagePath . '/manifest.json');
        
        // 2. Check system requirements
        $this->checkRequirements($manifest->requirements);
        
        // 3. Check dependencies
        $this->checkDependencies($manifest->requirements->dependencies);
        
        // 4. Check if already installed
        if ($this->isInstalled($manifest->id)) {
            throw new PackageAlreadyInstalledException();
        }
        
        // 5. Begin transaction
        DB::beginTransaction();
        
        try {
            // 6. Install database schema
            $this->installDatabase($packagePath, $manifest);
            
            // 7. Copy backend files
            $this->copyBackendFiles($packagePath, $manifest);
            
            // 8. Copy frontend files
            $this->copyFrontendFiles($packagePath, $manifest);
            
            // 9. Copy public files (if option enabled)
            if ($options['install_public_files'] ?? true) {
                $this->copyPublicFiles($packagePath, $manifest);
            }
            
            // 10. Register routes
            $this->routeManager->registerRoutes($manifest);
            
            // 11. Register permissions
            $this->registerPermissions($manifest);
            
            // 12. Register desktop shortcuts
            if ($options['create_desktop_shortcut'] ?? true) {
                $this->registerDesktopShortcuts($manifest);
            }
            
            // 13. Mark as installed
            $this->markAsInstalled($manifest);
            
            DB::commit();
            
            return new InstallationResult(true, 'Package installed successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->rollbackFiles($packagePath, $manifest);
            throw $e;
        }
    }
}
```

#### 1.4 RouteManager Service

```php
namespace App\Services\Admin;

class RouteManager
{
    public function registerRoutes(Manifest $manifest): void
    {
        foreach ($manifest->routing->admin as $route) {
            DB::table('app_routes')->insert([
                'app_id' => $manifest->id,
                'path' => $route->path,
                'controller' => $route->controller,
                'method' => $route->method,
                'auth_required' => $route->auth,
                'permission' => $route->permission,
                'created_at' => now()
            ]);
        }
        
        // Clear route cache
        Artisan::call('route:clear');
        Artisan::call('route:cache');
    }
    
    public function loadDynamicRoutes(): void
    {
        $routes = DB::table('app_routes')
            ->join('installed_apps', 'app_routes.app_id', '=', 'installed_apps.app_id')
            ->where('installed_apps.active', 1)
            ->get();
        
        foreach ($routes as $route) {
            Route::{strtolower($route->method)}($route->path, $route->controller)
                ->middleware($route->auth_required ? ['auth', 'admin'] : [])
                ->middleware($route->permission ? "can:{$route->permission}" : []);
        }
    }
}
```



### 2. ExtJS Desktop Shell (ALREADY IMPLEMENTED)

The ExtJS Desktop shell is fully implemented in `legacy/site/modules/admin/desktop/desktop.tpl`. Migration only requires updating API endpoints and module paths.

#### 2.1 Desktop Components

**Desktop Area:**
- Full-screen workspace with Windows 10 wallpaper background
- Desktop shortcuts with Material Icons (48px, colored)
- Publisher logo watermark ("СФЕРА / Творческий Центр")
- Click handlers for shortcuts

**Taskbar (43px height, #004172 background):**
- Start button (Material Icons "apps")
- Open windows container (taskbar buttons)
- System tray (notifications, user menu)
- Clock (updates every second, shows time + date)

**Start Menu (Three-panel Windows 10 style):**
- Left panel: User icons (menu, person, settings, power)
- Center panel: Scrollable app list with categories
- Right panel: Quick access tiles (4x grid, 90px tiles)

**Window Management:**
- Ext.window.Window components
- Draggable, resizable, minimizable, maximizable
- Windows 10 styling (white header, 36px height)
- Taskbar buttons for open windows
- Z-index management for window stacking

#### 2.2 Migration Changes

**BEFORE (Legacy):**
```javascript
function openProducts() {
    var win = Ext.create('Ext.window.Window', {
        title: 'Управление товарами',
        width: 1200,
        height: 800,
        html: '<iframe src="/site/modules/admin/products/index.php"></iframe>'
    });
    win.show();
}
```

**AFTER (Laravel):**
```javascript
function openProducts() {
    var win = Ext.create('Ext.window.Window', {
        title: 'Управление товарами',
        width: 1200,
        height: 800,
        html: '<iframe src="/admin/products" frameborder="0" style="width:100%;height:100%;"></iframe>'
    });
    win.show();
}
```

**Changes:**
- Update iframe `src` from legacy PHP path to Laravel route
- Add inline styles for iframe (width:100%, height:100%)
- No changes to ExtJS window configuration
- No changes to window management logic



### 3. Admin Window Content (Laravel Blade)

Window content is rendered by Laravel controllers returning Blade views inside iframes.

#### 3.1 Admin Layout Template

```blade
{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    
    <!-- Base admin styles -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin.css') }}">
    
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    
    @stack('styles')
</head>
<body>
    <div class="admin-content">
        @yield('content')
    </div>
    
    <!-- jQuery (optional, can use vanilla JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Base admin scripts -->
    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
```

#### 3.2 Example Admin Module View

```blade
{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Product Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/products.css') }}">
@endpush

@section('content')
<div class="admin-module">
    <div class="module-header">
        <h1>Product Management</h1>
        <button class="btn btn-primary" onclick="createProduct()">
            <span class="material-icons">add</span>
            Add Product
        </button>
    </div>
    
    <div class="module-toolbar">
        <input type="text" id="search" placeholder="Search products..." />
        <select id="category-filter">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="module-content">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="products-table-body">
                @foreach($products as $product)
                <tr data-id="{{ $product->id }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price, 2) }} ₽</td>
                    <td>{{ $product->quantity }}</td>
                    <td>
                        <span class="status-badge status-{{ $product->active ? 'active' : 'inactive' }}">
                            {{ $product->active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <button onclick="editProduct({{ $product->id }})" title="Edit">
                            <span class="material-icons">edit</span>
                        </button>
                        <button onclick="deleteProduct({{ $product->id }})" title="Delete">
                            <span class="material-icons">delete</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="pagination">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/js/products.js') }}"></script>
@endpush
```

#### 3.3 Admin Controller Example

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('products.view');
        
        $query = DB::table('v_products')
            ->select('id', 'name', 'product_price as price', 'quantity', 'active');
        
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }
        
        $products = $query->paginate(50);
        $categories = DB::table('categories')->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }
    
    public function store(Request $request)
    {
        $this->authorize('products.create');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);
        
        $productId = DB::table('v_products')->insertGetId($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $productId
        ]);
    }
}
```



### 4. ACL System Integration

#### 4.1 Database Schema

```sql
-- Admin permissions table
CREATE TABLE admin_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_permission (module, action)
);

-- Admin roles table
CREATE TABLE admin_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Role permissions junction table
CREATE TABLE admin_role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES admin_permissions(id) ON DELETE CASCADE
);

-- User roles junction table
CREATE TABLE admin_user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES admin_roles(id) ON DELETE CASCADE
);
```

#### 4.2 Laravel Gate Integration

```php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
        
        // Define gates from database permissions
        $permissions = DB::table('admin_permissions')->get();
        
        foreach ($permissions as $permission) {
            $gateName = "{$permission->module}.{$permission->action}";
            
            Gate::define($gateName, function ($user) use ($permission) {
                return $this->userHasPermission($user, $permission->id);
            });
        }
    }
    
    protected function userHasPermission($user, $permissionId): bool
    {
        return DB::table('admin_user_roles')
            ->join('admin_role_permissions', 'admin_user_roles.role_id', '=', 'admin_role_permissions.role_id')
            ->where('admin_user_roles.user_id', $user->id)
            ->where('admin_role_permissions.permission_id', $permissionId)
            ->exists();
    }
}
```

#### 4.3 Usage in Controllers

```php
// Check permission in controller
public function index()
{
    $this->authorize('products.view');
    // ... controller logic
}

// Check permission in Blade
@can('products.create')
    <button onclick="createProduct()">Add Product</button>
@endcan

// Check permission in route middleware
Route::get('/admin/products', [ProductController::class, 'index'])
    ->middleware(['auth', 'admin', 'can:products.view']);
```



### 5. Core Admin Modules

#### 5.1 Product Management Module

**Routes:**
- `GET /admin/products` - List products
- `GET /admin/products/create` - Create form
- `POST /admin/products` - Store product
- `GET /admin/products/{id}/edit` - Edit form
- `PUT /admin/products/{id}` - Update product
- `DELETE /admin/products/{id}` - Delete product

**Features:**
- Paginated product list with search and filters
- Product CRUD operations
- Image upload and management
- Bulk operations (delete, update price, update quantity)
- Integration with Ozon sync (v_products_o_products)
- Product attributes (authors, series, topics, types, age ranges)

#### 5.2 Order Management Module

**Routes:**
- `GET /admin/orders` - List orders
- `GET /admin/orders/{id}` - View order details
- `PUT /admin/orders/{id}/status` - Update order status
- `GET /admin/orders/{id}/invoice` - Generate invoice PDF

**Features:**
- Paginated order list with filters (status, date range, customer)
- Order details view (customer, items, total, status, timeline)
- Order status updates with email notifications
- Invoice generation (PDF)
- Integration with 1C export
- Order statistics dashboard

#### 5.3 User Management Module

**Routes:**
- `GET /admin/users` - List users
- `GET /admin/users/create` - Create form
- `POST /admin/users` - Store user
- `GET /admin/users/{id}/edit` - Edit form
- `PUT /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Delete user

**Features:**
- Paginated user list with search and filters
- User CRUD operations
- Role assignment (admin, manager, editor, viewer, customer)
- User status management (active, inactive, banned)
- Password reset functionality
- User order history view

#### 5.4 Content Management Module

**Routes:**
- `GET /admin/content/pages` - List pages
- `GET /admin/content/pages/{id}/edit` - Edit page
- `GET /admin/content/banners` - List banners
- `GET /admin/content/carousels` - List carousels

**Features:**
- Page editor (WYSIWYG or markdown)
- Shortcode system for sections ([section guid="..."])
- Banner management (upload, title, link, order, active status)
- Carousel management (main carousel, promo carousel)
- Image upload with automatic resizing
- Preview functionality
- Scheduling (publish/unpublish dates)
- SEO fields (meta title, description, keywords)

#### 5.5 Settings Management Module

**Routes:**
- `GET /admin/settings` - Settings dashboard
- `GET /admin/settings/{group}` - Settings group
- `PUT /admin/settings/{group}` - Update settings

**Features:**
- General settings (site name, logo, contact info)
- Email settings (SMTP configuration, templates)
- Payment settings (methods, credentials)
- Shipping settings (methods, rates, zones)
- Tax settings (rates, classes)
- SEO settings (default meta tags, sitemap)
- Settings validation
- Import/export (JSON format)
- Settings caching

#### 5.6 Statistics and Reports Module

**Routes:**
- `GET /admin/reports/dashboard` - Dashboard
- `GET /admin/reports/sales` - Sales report
- `GET /admin/reports/products` - Product performance
- `GET /admin/reports/customers` - Customer statistics

**Features:**
- Sales statistics (daily, weekly, monthly, yearly)
- Revenue charts (line chart, bar chart)
- Top-selling products (by quantity, by revenue)
- Customer statistics (new, returning)
- Order statistics (average order value, orders per day)
- Date range filtering
- Export reports (CSV, Excel, PDF)
- Dashboard widgets for key metrics



## Data Models

### 1. Package System Models

#### InstalledApp Model

```php
namespace App\Models\Admin;

class InstalledApp extends Model
{
    protected $table = 'installed_apps';
    protected $primaryKey = 'app_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'app_id',
        'app_name',
        'version',
        'author',
        'description',
        'category',
        'icon',
        'icon_color',
        'active',
        'installed_at'
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'installed_at' => 'datetime'
    ];
    
    public function routes()
    {
        return $this->hasMany(AppRoute::class, 'app_id', 'app_id');
    }
    
    public function permissions()
    {
        return $this->hasMany(AdminPermission::class, 'module', 'app_id');
    }
    
    public function desktopShortcuts()
    {
        return $this->hasMany(AppDesktopShortcut::class, 'app_id', 'app_id');
    }
}
```

#### AppRoute Model

```php
namespace App\Models\Admin;

class AppRoute extends Model
{
    protected $table = 'app_routes';
    
    protected $fillable = [
        'app_id',
        'path',
        'controller',
        'method',
        'auth_required',
        'permission'
    ];
    
    protected $casts = [
        'auth_required' => 'boolean'
    ];
    
    public function app()
    {
        return $this->belongsTo(InstalledApp::class, 'app_id', 'app_id');
    }
}
```

#### AppDesktopShortcut Model

```php
namespace App\Models\Admin;

class AppDesktopShortcut extends Model
{
    protected $table = 'app_desktop_shortcuts';
    
    protected $fillable = [
        'app_id',
        'title',
        'icon',
        'icon_color',
        'function_name',
        'show_on_desktop',
        'show_in_quick_access',
        'order'
    ];
    
    protected $casts = [
        'show_on_desktop' => 'boolean',
        'show_in_quick_access' => 'boolean',
        'order' => 'integer'
    ];
    
    public function app()
    {
        return $this->belongsTo(InstalledApp::class, 'app_id', 'app_id');
    }
}
```

### 2. ACL Models

#### AdminPermission Model

```php
namespace App\Models\Admin;

class AdminPermission extends Model
{
    protected $table = 'admin_permissions';
    
    protected $fillable = [
        'module',
        'action',
        'title',
        'description'
    ];
    
    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_permissions', 'permission_id', 'role_id');
    }
    
    public function getGateNameAttribute(): string
    {
        return "{$this->module}.{$this->action}";
    }
}
```

#### AdminRole Model

```php
namespace App\Models\Admin;

class AdminRole extends Model
{
    protected $table = 'admin_roles';
    
    protected $fillable = [
        'name',
        'title',
        'description'
    ];
    
    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_role_permissions', 'role_id', 'permission_id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'admin_user_roles', 'role_id', 'user_id');
    }
}
```

### 3. Database Schema

```sql
-- Installed apps table
CREATE TABLE installed_apps (
    app_id VARCHAR(50) PRIMARY KEY,
    app_name VARCHAR(255) NOT NULL,
    version VARCHAR(20) NOT NULL,
    author VARCHAR(255),
    description TEXT,
    category VARCHAR(50),
    icon VARCHAR(50),
    icon_color VARCHAR(20),
    active TINYINT(1) DEFAULT 1,
    installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- App routes table
CREATE TABLE app_routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_id VARCHAR(50) NOT NULL,
    path VARCHAR(255) NOT NULL,
    controller VARCHAR(255) NOT NULL,
    method VARCHAR(10) NOT NULL,
    auth_required TINYINT(1) DEFAULT 1,
    permission VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES installed_apps(app_id) ON DELETE CASCADE,
    UNIQUE KEY unique_route (path, method)
);

-- App desktop shortcuts table
CREATE TABLE app_desktop_shortcuts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_id VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    icon_color VARCHAR(20) DEFAULT '#0078d7',
    function_name VARCHAR(100) NOT NULL,
    show_on_desktop TINYINT(1) DEFAULT 0,
    show_in_quick_access TINYINT(1) DEFAULT 0,
    `order` INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES installed_apps(app_id) ON DELETE CASCADE
);

-- Installation log table
CREATE TABLE app_installation_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_id VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_app_id (app_id),
    INDEX idx_created_at (created_at)
);
```



## Correctness Properties

A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.

### Property Reflection

After analyzing all acceptance criteria, I identified the following redundancies:

1. **Manifest Parsing Properties (3.1-3.6)**: These can be combined into a single comprehensive property about manifest parsing completeness
2. **File Installation Properties (4.6-4.8)**: These can be combined into a single property about file copying
3. **Database Registration Properties (4.9-4.11)**: These can be combined into a single property about database registration
4. **File Removal Properties (5.3-5.5)**: These can be combined into a single property about file removal
5. **Database Cleanup Properties (5.6-5.8)**: These can be combined into a single property about database cleanup
6. **Route Registration Properties (6.1-6.2)**: These can be combined into a single property about route registration

### Property 1: Manifest Parsing Completeness

*For any* valid package manifest JSON file, parsing should extract all required sections (metadata, requirements, installation paths, routing, permissions, desktop shortcuts) and return a complete Manifest object with all fields populated.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

### Property 2: Manifest Validation Rejects Invalid Schemas

*For any* invalid package manifest (missing required fields, invalid data types, malformed JSON), validation should reject the manifest and return a descriptive error message indicating which field is invalid.

**Validates: Requirements 3.7, 3.8**

### Property 3: System Requirements Validation

*For any* package with system requirements (PHP version, MySQL version, extensions), the installer should check the current system against these requirements and reject installation if requirements are not met.

**Validates: Requirements 4.1**

### Property 4: Dependency Resolution

*For any* package with dependencies, the installer should check that all required packages are installed with minimum required versions before allowing installation.

**Validates: Requirements 4.2**

### Property 5: Package Installation Round Trip

*For any* valid package, installing then immediately uninstalling should return the system to its original state (no database entries, no files, no routes, no permissions).

**Validates: Requirements 4.4, 4.5, 4.6, 4.7, 4.8, 4.9, 4.10, 4.11, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8**

### Property 6: Installation Logging Completeness

*For any* package installation (successful or failed), the installation log should contain entries for all attempted steps with timestamps, status (success/failure), and descriptive messages.

**Validates: Requirements 4.12, 4.14**

### Property 7: Installation Rollback on Failure

*For any* package installation that fails at any step, all changes made before the failure (database tables, files, routes, permissions) should be rolled back, leaving no partial installation artifacts.

**Validates: Requirements 4.13**

### Property 8: Dependency Checking Before Uninstallation

*For any* installed package that is a dependency of other packages, attempting to uninstall should be rejected with an error message listing the dependent packages.

**Validates: Requirements 5.1**

### Property 9: Uninstallation Logging Completeness

*For any* package uninstallation (successful or failed), the uninstallation log should contain entries for all attempted steps with timestamps, status, and messages.

**Validates: Requirements 5.9, 5.10**

### Property 10: Route Registration Completeness

*For any* package with routes defined in manifest (admin and/or public), after installation all routes should be registered in app_routes table and accessible via HTTP requests.

**Validates: Requirements 6.1, 6.2, 6.3, 6.4**

### Property 11: Route Authentication Enforcement

*For any* route marked with auth_required=true in the manifest, accessing the route without authentication should return 401/403 status, and accessing with authentication should return 200 status.

**Validates: Requirements 6.6**

### Property 12: Route Middleware Application

*For any* route with middleware specified in the manifest, the middleware should be applied and executed when the route is accessed.

**Validates: Requirements 6.7**

### Property 13: Route Removal on Uninstallation

*For any* installed package with routes, after uninstallation all package routes should be removed from app_routes table and return 404 when accessed.

**Validates: Requirements 6.8**

### Property 14: Route Cache Consistency

*For any* change to app_routes table (insert, update, delete), after running the route cache rebuild command, the cached routes should match the database routes exactly.

**Validates: Requirements 6.9**

### Property 15: Permission Registration Completeness

*For any* package with permissions defined in manifest, after installation all permissions should be registered in admin_permissions table with correct module, action, title, and description.

**Validates: Requirements 7.1, 7.8**

### Property 16: Permission Authorization Enforcement

*For any* user without a specific permission, attempting to access a resource requiring that permission should be denied (authorize() throws exception or Gate::allows() returns false).

**Validates: Requirements 7.2, 7.3, 7.4**

### Property 17: Role-Based Permission Inheritance

*For any* user assigned to a role, the user should have all permissions assigned to that role (Gate::allows() returns true for all role permissions).

**Validates: Requirements 7.5**

### Property 18: Permission Middleware Protection

*For any* route protected with permission middleware (can:module.action), accessing without the permission should return 403, and accessing with the permission should return 200.

**Validates: Requirements 7.7**

### Property 19: Permission Removal on Uninstallation

*For any* installed package with permissions, after uninstallation all package permissions should be removed from admin_permissions table and related role_permissions entries should be deleted.

**Validates: Requirements 7.9**

### Property 20: Available Packages List Accuracy

*For any* set of packages in the .inst/ directory, the Package Manager UI should display exactly those packages (no more, no less) with correct metadata from their manifests.

**Validates: Requirements 10.1**

### Property 21: Installed Packages List Accuracy

*For any* set of installed packages in installed_apps table, the Package Manager UI should display exactly those packages with correct status (active/inactive) and version information.

**Validates: Requirements 10.2**

### Property 22: Desktop Shortcuts Display Accuracy

*For any* set of shortcuts in app_desktop_shortcuts table with show_on_desktop=1, the desktop should display exactly those shortcuts with correct icon, color, and title.

**Validates: Requirements 11.1**

### Property 23: Admin Route Authentication Requirement

*For all* admin routes (paths starting with /admin/), accessing without authentication should redirect to login or return 401/403 status.

**Validates: Requirements 23.1**

### Property 24: CSRF Protection on Forms

*For any* admin form submission (POST, PUT, DELETE), submitting without a valid CSRF token should be rejected with 419 status.

**Validates: Requirements 23.2**

### Property 25: SQL Injection Prevention

*For any* database query with user input, attempting SQL injection (e.g., "'; DROP TABLE users; --") should not execute the malicious SQL and should return safe results or error.

**Validates: Requirements 23.3**

### Property 26: XSS Prevention

*For any* user input displayed in admin views, attempting XSS injection (e.g., "<script>alert('XSS')</script>") should be escaped and rendered as text, not executed as JavaScript.

**Validates: Requirements 23.4**



## Error Handling

### 1. Package Installation Errors

**Error Types:**
- Invalid manifest format (malformed JSON, missing required fields)
- System requirements not met (PHP version, MySQL version, extensions)
- Missing dependencies (required packages not installed)
- Duplicate installation (package already installed)
- Database errors (schema creation failed, permission denied)
- File system errors (copy failed, permission denied)
- Route conflicts (route already registered by another package)

**Error Handling Strategy:**
- Validate manifest before any installation steps
- Check all requirements before beginning transaction
- Use database transactions for atomic operations
- Rollback all changes on any error
- Log all errors with detailed context
- Return structured error responses with actionable messages
- Preserve system state on failure (no partial installations)

**Example Error Response:**
```json
{
  "success": false,
  "error": "SYSTEM_REQUIREMENTS_NOT_MET",
  "message": "Package requires PHP 8.5 or higher. Current version: 8.3",
  "details": {
    "required": ">=8.5",
    "current": "8.3",
    "package": "advanced-reports"
  }
}
```

### 2. Package Uninstallation Errors

**Error Types:**
- Package not found (not installed)
- Dependency conflict (other packages depend on this package)
- Database errors (rollback SQL failed)
- File system errors (file removal failed, permission denied)

**Error Handling Strategy:**
- Check dependencies before uninstallation
- Provide clear error messages listing dependent packages
- Use database transactions for atomic operations
- Log all errors with context
- Mark package as inactive even if file removal fails
- Provide manual cleanup instructions if needed

### 3. Route Registration Errors

**Error Types:**
- Route conflict (path already registered)
- Invalid controller (class not found)
- Invalid method (method not found in controller)
- Permission not found (referenced permission doesn't exist)

**Error Handling Strategy:**
- Validate all routes before registration
- Check for conflicts with existing routes
- Verify controller and method existence
- Provide detailed error messages with conflicting route information
- Rollback route registration on any error

### 4. ACL Errors

**Error Types:**
- Permission not found
- Role not found
- User not found
- Unauthorized access (user lacks required permission)
- Invalid permission format (module.action)

**Error Handling Strategy:**
- Use Laravel's authorization exceptions (AuthorizationException)
- Return 403 Forbidden for unauthorized access
- Log all authorization failures for security audit
- Provide clear error messages without exposing sensitive information
- Use middleware for consistent error handling

### 5. Admin Module Errors

**Error Types:**
- Validation errors (invalid input data)
- Database errors (query failed, constraint violation)
- File upload errors (size limit, invalid format)
- Resource not found (product, order, user not found)

**Error Handling Strategy:**
- Use Laravel validation for input validation
- Return structured validation error responses
- Use try-catch blocks for database operations
- Log all errors with context
- Provide user-friendly error messages in admin UI
- Use HTTP status codes appropriately (400, 404, 500)

**Example Validation Error Response:**
```json
{
  "success": false,
  "errors": {
    "name": ["The name field is required."],
    "price": ["The price must be a number.", "The price must be at least 0."]
  }
}
```



## Testing Strategy

### Dual Testing Approach

The admin panel migration requires both unit tests and property-based tests for comprehensive coverage:

- **Unit tests**: Verify specific examples, edge cases, error conditions, and integration points
- **Property tests**: Verify universal properties across all inputs through randomization
- Both approaches are complementary and necessary for ensuring system correctness

### Property-Based Testing Configuration

**Library Selection:**
- PHP: Use **Eris** (property-based testing library for PHPUnit)
- Installation: `composer require --dev giorgiosironi/eris`

**Test Configuration:**
- Minimum 100 iterations per property test (due to randomization)
- Each property test must reference its design document property
- Tag format: `@test Feature: admin-panel-migration, Property {number}: {property_text}`

**Example Property Test:**

```php
use Eris\Generator;
use Eris\TestTrait;

class PackageInstallerTest extends TestCase
{
    use TestTrait;
    
    /**
     * @test
     * Feature: admin-panel-migration, Property 5: Package Installation Round Trip
     * 
     * For any valid package, installing then immediately uninstalling should 
     * return the system to its original state.
     */
    public function package_installation_round_trip()
    {
        $this->forAll(
            Generator\associative([
                'id' => Generator\regex('[a-z_]{3,20}'),
                'name' => Generator\string(),
                'version' => Generator\regex('\d+\.\d+\.\d+'),
                'routes' => Generator\seq(Generator\associative([
                    'path' => Generator\regex('/admin/[a-z]+'),
                    'controller' => Generator\string(),
                    'method' => Generator\elements(['GET', 'POST', 'PUT', 'DELETE'])
                ]))
            ])
        )
        ->withMaxSize(100)
        ->then(function ($packageData) {
            // Capture initial state
            $initialApps = DB::table('installed_apps')->count();
            $initialRoutes = DB::table('app_routes')->count();
            $initialShortcuts = DB::table('app_desktop_shortcuts')->count();
            
            // Create package
            $package = $this->createTestPackage($packageData);
            
            // Install package
            $installer = new PackageInstaller();
            $result = $installer->install($package->path);
            $this->assertTrue($result->success);
            
            // Uninstall package
            $uninstaller = new PackageUninstaller();
            $result = $uninstaller->uninstall($packageData['id']);
            $this->assertTrue($result->success);
            
            // Verify state restored
            $this->assertEquals($initialApps, DB::table('installed_apps')->count());
            $this->assertEquals($initialRoutes, DB::table('app_routes')->count());
            $this->assertEquals($initialShortcuts, DB::table('app_desktop_shortcuts')->count());
        });
    }
}
```

### Unit Testing Strategy

**Focus Areas for Unit Tests:**
1. Specific examples demonstrating correct behavior
2. Edge cases (empty manifests, missing fields, special characters)
3. Error conditions (invalid JSON, missing dependencies, file permission errors)
4. Integration points (database transactions, file system operations)

**Example Unit Test:**

```php
class PackageInstallerTest extends TestCase
{
    /** @test */
    public function it_rejects_duplicate_installation()
    {
        // Arrange
        $package = $this->createTestPackage(['id' => 'test-package']);
        $installer = new PackageInstaller();
        
        // Install once
        $result = $installer->install($package->path);
        $this->assertTrue($result->success);
        
        // Act & Assert - attempt duplicate installation
        $this->expectException(PackageAlreadyInstalledException::class);
        $installer->install($package->path);
    }
    
    /** @test */
    public function it_validates_system_requirements()
    {
        // Arrange
        $package = $this->createTestPackage([
            'requirements' => [
                'php' => '>=9.0' // Higher than current PHP version
            ]
        ]);
        $installer = new PackageInstaller();
        
        // Act & Assert
        $this->expectException(SystemRequirementsNotMetException::class);
        $this->expectExceptionMessage('PHP 9.0 or higher');
        $installer->install($package->path);
    }
}
```

### Test Coverage Requirements

**Minimum Coverage Targets:**
- Package System Services: 90% code coverage
- ACL System: 85% code coverage
- Admin Controllers: 80% code coverage
- Route Manager: 90% code coverage

**Coverage Exclusions:**
- ExtJS Desktop shell (already implemented, only API updates)
- Blade view templates (tested via browser tests)
- Migration bridge (temporary compatibility layer)

### Browser Testing (Laravel Dusk)

**Test Scenarios:**
1. Desktop UI loads correctly with taskbar, start menu, and shortcuts
2. Clicking desktop shortcut opens window with correct content
3. Window management (minimize, maximize, close, drag, resize)
4. Package Manager UI displays available and installed packages
5. Installing package via UI shows progress and success message
6. Admin module forms submit correctly and show validation errors

**Example Dusk Test:**

```php
class AdminDesktopTest extends DuskTestCase
{
    /** @test */
    public function desktop_shortcut_opens_window_with_correct_content()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/admin/desktop')
                    ->waitFor('.desktop-shortcuts')
                    ->click('.shortcut[onclick*="openProducts"]')
                    ->waitFor('.x-window-header:contains("Управление товарами")')
                    ->withinFrame('iframe', function ($frame) {
                        $frame->assertSee('Product Management')
                              ->assertSee('Add Product');
                    });
        });
    }
}
```

### Integration Testing

**Test Scenarios:**
1. Package installation creates database tables, copies files, registers routes
2. Route registration makes routes accessible via HTTP
3. ACL integration blocks unauthorized access
4. Package uninstallation removes all traces
5. Route cache rebuild updates cached routes

### Performance Testing

**Test Scenarios:**
1. Dashboard loads in < 2 seconds with 1000 products
2. Product list (100 items) loads in < 1 second
3. Order list (100 items) loads in < 1 second
4. Package installation completes in < 10 seconds
5. Route cache rebuild completes in < 5 seconds

**Tools:**
- Apache Bench (ab) for load testing
- Laravel Telescope for query profiling
- Xdebug for performance profiling

### Security Testing

**Test Scenarios:**
1. SQL injection attempts are blocked
2. XSS injection attempts are escaped
3. CSRF token validation works on all forms
4. Unauthorized access to admin routes is blocked
5. File upload restrictions are enforced

**Tools:**
- OWASP ZAP for automated security scanning
- Manual penetration testing
- Laravel's built-in security features testing

### Continuous Integration

**CI Pipeline:**
1. Run PHPUnit tests (unit + property tests)
2. Check code coverage (fail if below thresholds)
3. Run PHP CodeSniffer (PSR-12 compliance)
4. Run PHPStan (static analysis, level 8)
5. Run Laravel Dusk tests (browser tests)
6. Generate test report

**CI Configuration (.github/workflows/tests.yml):**

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.5
          extensions: mbstring, pdo, pdo_mysql
          
      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress
        
      - name: Run Tests
        run: php artisan test --coverage --min=80
        
      - name: Run Static Analysis
        run: vendor/bin/phpstan analyse
        
      - name: Run Code Style Check
        run: vendor/bin/phpcs
```

### Test Data Management

**Strategies:**
1. Use database factories for generating test data
2. Use database transactions for test isolation
3. Use RefreshDatabase trait for clean state
4. Create reusable test fixtures for common scenarios
5. Use Faker for generating realistic random data

**Example Factory:**

```php
class PackageFactory extends Factory
{
    public function definition()
    {
        return [
            'app_id' => $this->faker->slug,
            'app_name' => $this->faker->words(3, true),
            'version' => $this->faker->semver(),
            'author' => $this->faker->company,
            'description' => $this->faker->sentence,
            'category' => $this->faker->randomElement(['E-commerce', 'Content', 'Reports']),
            'icon' => 'inventory_2',
            'icon_color' => $this->faker->hexColor,
            'active' => true
        ];
    }
}
```



## Migration Strategy

### Phase 1: Foundation (Weeks 1-2)

**Objectives:**
- Set up package system infrastructure
- Migrate ExtJS Desktop shell (update API endpoints only)
- Create admin Blade layout
- Implement migration bridge for backward compatibility

**Tasks:**
1. Create database schema (installed_apps, app_routes, app_desktop_shortcuts, admin_permissions, admin_roles)
2. Implement ManifestParser service
3. Implement PackageInstaller service (basic version)
4. Implement PackageUninstaller service (basic version)
5. Implement RouteManager service
6. Update ExtJS Desktop template (change API endpoints from legacy to Laravel)
7. Create admin Blade layout (resources/views/layouts/admin.blade.php)
8. Implement migration bridge (legacy compatibility helpers)
9. Write unit tests for core services
10. Write property tests for manifest parsing and validation

**Deliverables:**
- Working package system (install/uninstall)
- Updated ExtJS Desktop loading from Laravel
- Admin layout template
- Test suite with 80%+ coverage

### Phase 2: Core Modules (Weeks 3-5)

**Objectives:**
- Migrate Product Management module
- Migrate Order Management module
- Migrate User Management module
- Implement ACL integration with Laravel

**Tasks:**
1. Create ProductController with index, create, store, edit, update, delete actions
2. Create Blade views for product management (list, form)
3. Create JavaScript for product module (AJAX, validation)
4. Create OrderController with index, show, updateStatus, generateInvoice actions
5. Create Blade views for order management
6. Create JavaScript for order module
7. Create UserController with index, create, store, edit, update, delete actions
8. Create Blade views for user management
9. Create JavaScript for user module
10. Implement ACL system (Gates, Policies, middleware)
11. Create ExtJS window functions for each module
12. Write unit tests for controllers
13. Write property tests for ACL system
14. Write browser tests for module UI

**Deliverables:**
- Working Product Management module
- Working Order Management module
- Working User Management module
- ACL system integrated with Laravel
- Test suite with 80%+ coverage

### Phase 3: Content & Settings (Weeks 6-7)

**Objectives:**
- Migrate Content Management module
- Migrate Settings Management module
- Migrate Statistics and Reports module

**Tasks:**
1. Create ContentController for pages, banners, carousels
2. Create Blade views for content management
3. Implement WYSIWYG editor integration
4. Create SettingsController for site settings
5. Create Blade views for settings management
6. Implement settings caching
7. Create ReportsController for statistics and reports
8. Create Blade views for reports with charts
9. Create ExtJS window functions for each module
10. Write unit tests for controllers
11. Write browser tests for module UI

**Deliverables:**
- Working Content Management module
- Working Settings Management module
- Working Statistics and Reports module
- Test suite with 80%+ coverage

### Phase 4: Package System Enhancement (Weeks 8-9)

**Objectives:**
- Implement Package Manager UI
- Enhance package installer with advanced features
- Create package API for custom modules
- Document package development

**Tasks:**
1. Create PackageManagerController
2. Create Blade views for Package Manager UI
3. Implement real-time installation log display
4. Implement package search and filtering
5. Enhance PackageInstaller with dependency resolution
6. Implement package update functionality
7. Create base classes for package development (BaseController, BaseModel)
8. Create package template generator (artisan command)
9. Write package development documentation
10. Write unit tests for Package Manager
11. Write browser tests for Package Manager UI

**Deliverables:**
- Working Package Manager UI
- Enhanced package system
- Package development API
- Package development documentation
- Test suite with 80%+ coverage

### Phase 5: Migration & Cleanup (Weeks 10-11)

**Objectives:**
- Migrate legacy packages to new structure
- Remove migration bridge
- Optimize performance
- Complete documentation

**Tasks:**
1. Identify all legacy packages
2. Convert legacy packages to new manifest format
3. Test each migrated package
4. Remove migration bridge code
5. Optimize database queries (add indexes, eager loading)
6. Implement caching for frequently accessed data
7. Run performance tests and optimize bottlenecks
8. Complete user documentation
9. Complete developer documentation
10. Create video tutorials
11. Conduct final testing (unit, property, browser, security)
12. Prepare deployment plan

**Deliverables:**
- All legacy packages migrated
- Migration bridge removed
- Optimized performance
- Complete documentation
- Deployment-ready system

### Rollback Strategy

**Rollback Triggers:**
- Critical bugs discovered in production
- Performance degradation
- Data integrity issues
- Security vulnerabilities

**Rollback Procedure:**
1. Enable feature flag to switch back to legacy admin panel
2. Restore database backup (if schema changes were made)
3. Revert code deployment
4. Verify legacy admin panel functionality
5. Investigate and fix issues
6. Re-deploy when ready

**Rollback Testing:**
- Test rollback procedure in staging environment
- Document rollback steps
- Train operations team on rollback procedure
- Maintain legacy admin panel code until migration is stable

### Parallel Running Strategy

**Approach:**
- Run legacy and Laravel admin panels in parallel during migration
- Use feature flags to control which panel is active
- Allow gradual migration of users
- Monitor both systems for issues

**Configuration:**

```php
// config/admin.php
return [
    'use_legacy_panel' => env('ADMIN_USE_LEGACY', false),
    'legacy_url' => env('ADMIN_LEGACY_URL', '/legacy/admin'),
    'new_url' => env('ADMIN_NEW_URL', '/admin')
];
```

**Routing:**

```php
// routes/web.php
if (config('admin.use_legacy_panel')) {
    Route::redirect('/admin', config('admin.legacy_url'));
} else {
    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        // New Laravel admin routes
    });
}
```



## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── ProductController.php
│   │       ├── OrderController.php
│   │       ├── UserController.php
│   │       ├── ContentController.php
│   │       ├── SettingsController.php
│   │       ├── ReportsController.php
│   │       └── PackageManagerController.php
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── CheckPermission.php
├── Models/
│   └── Admin/
│       ├── InstalledApp.php
│       ├── AppRoute.php
│       ├── AppDesktopShortcut.php
│       ├── AdminPermission.php
│       ├── AdminRole.php
│       └── AppInstallationLog.php
├── Services/
│   └── Admin/
│       ├── PackageInstaller.php
│       ├── PackageUninstaller.php
│       ├── ManifestParser.php
│       ├── RouteManager.php
│       └── ACLManager.php
├── Providers/
│   └── AdminServiceProvider.php
└── Exceptions/
    └── Admin/
        ├── PackageAlreadyInstalledException.php
        ├── SystemRequirementsNotMetException.php
        ├── DependencyNotMetException.php
        └── InvalidManifestException.php

resources/
├── views/
│   ├── layouts/
│   │   └── admin.blade.php
│   └── admin/
│       ├── desktop/
│       │   └── index.blade.php
│       ├── products/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── orders/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── users/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── content/
│       │   ├── pages/
│       │   ├── banners/
│       │   └── carousels/
│       ├── settings/
│       │   └── index.blade.php
│       ├── reports/
│       │   └── dashboard.blade.php
│       └── packages/
│           └── index.blade.php

public/
├── admin/
│   ├── js/
│   │   ├── desktop.js              # ExtJS Desktop shell (updated from legacy)
│   │   ├── products_window.js      # ExtJS window function for products
│   │   ├── orders_window.js        # ExtJS window function for orders
│   │   ├── users_window.js         # ExtJS window function for users
│   │   └── packages_window.js      # ExtJS window function for packages
│   └── css/
│       └── desktop.css             # Desktop styling (updated from legacy)
└── assets/
    └── admin/
        ├── css/
        │   ├── admin.css           # Base admin styles
        │   ├── products.css        # Product module styles
        │   ├── orders.css          # Order module styles
        │   └── users.css           # User module styles
        └── js/
            ├── admin.js            # Base admin JavaScript
            ├── products.js         # Product module JavaScript
            ├── orders.js           # Order module JavaScript
            └── users.js            # User module JavaScript

database/
├── migrations/
│   ├── 2024_01_01_000001_create_installed_apps_table.php
│   ├── 2024_01_01_000002_create_app_routes_table.php
│   ├── 2024_01_01_000003_create_app_desktop_shortcuts_table.php
│   ├── 2024_01_01_000004_create_admin_permissions_table.php
│   ├── 2024_01_01_000005_create_admin_roles_table.php
│   ├── 2024_01_01_000006_create_admin_role_permissions_table.php
│   ├── 2024_01_01_000007_create_admin_user_roles_table.php
│   └── 2024_01_01_000008_create_app_installation_log_table.php
└── factories/
    └── Admin/
        ├── InstalledAppFactory.php
        ├── AppRouteFactory.php
        └── AdminPermissionFactory.php

tests/
├── Unit/
│   └── Admin/
│       ├── PackageInstallerTest.php
│       ├── PackageUninstallerTest.php
│       ├── ManifestParserTest.php
│       ├── RouteManagerTest.php
│       └── ACLManagerTest.php
├── Feature/
│   └── Admin/
│       ├── ProductControllerTest.php
│       ├── OrderControllerTest.php
│       ├── UserControllerTest.php
│       └── PackageManagerControllerTest.php
├── Browser/
│   └── Admin/
│       ├── DesktopTest.php
│       ├── ProductModuleTest.php
│       ├── OrderModuleTest.php
│       └── PackageManagerTest.php
└── Property/
    └── Admin/
        ├── ManifestParsingTest.php
        ├── PackageInstallationTest.php
        ├── RouteRegistrationTest.php
        └── ACLSystemTest.php

.inst/
├── products/                       # Example package
│   ├── manifest.json
│   ├── backend/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── frontend/
│   │   ├── views/
│   │   └── assets/
│   ├── public/
│   └── database/
│       ├── schema.sql
│       ├── permissions.sql
│       └── rollback.sql
└── orders/                         # Example package
    └── ...

routes/
├── web.php                         # Main routes
├── admin.php                       # Admin routes (loaded dynamically)
└── api.php                         # API routes

config/
├── admin.php                       # Admin panel configuration
└── packages.php                    # Package system configuration

docs/
├── admin-panel-migration/
│   ├── architecture.md
│   ├── package-development.md
│   ├── migration-guide.md
│   ├── api-reference.md
│   └── troubleshooting.md
└── videos/
    ├── package-installation.mp4
    ├── module-development.mp4
    └── desktop-usage.mp4
```

### Key Directory Explanations

**app/Services/Admin/**: Contains all package system services (installer, uninstaller, manifest parser, route manager). These are the core of the package system.

**resources/views/admin/**: Contains all Blade templates for admin modules. Each module has its own subdirectory with views for list, create, edit, etc.

**public/admin/js/**: Contains ExtJS Desktop shell and window functions. These are updated from legacy with new API endpoints.

**public/assets/admin/**: Contains module-specific CSS and JavaScript for window content (not ExtJS, but vanilla JS/jQuery).

**.inst/**: Contains installable packages. Each package has its own directory with manifest.json and all necessary files.

**tests/Property/**: Contains property-based tests using Eris library. These test universal properties across randomized inputs.

**database/migrations/**: Contains migrations for package system tables. These are run once during initial setup.



## Summary

This design document specifies a backend modernization approach for migrating the admin panel from legacy ExtJS + Smarty + custom PHP to Laravel while preserving the fully implemented Windows 10 Desktop interface.

### Key Design Decisions

1. **Preserve ExtJS Desktop Shell**: The Windows 10 Desktop UI (desktop, taskbar, start menu, window management) is already fully implemented and will be kept unchanged. Only API endpoints will be updated.

2. **Iframe Architecture**: ExtJS windows contain iframes loading Laravel routes that return Blade templates. This provides clean separation between the desktop shell and window content.

3. **No New JavaScript Frameworks**: Developers already know ExtJS. Window content uses HTML/CSS/vanilla JavaScript or jQuery, following the same patterns as the public site.

4. **Package System Modernization**: The sophisticated installable package system will be reimplemented using Laravel services (PackageInstaller, PackageUninstaller, RouteManager) while maintaining the same manifest.json structure.

5. **ACL Integration**: Legacy ACL system integrates with Laravel Gates and Policies for consistent authorization.

6. **Gradual Migration**: Five-phase migration strategy allows incremental delivery and testing. Parallel running of legacy and Laravel admin panels reduces risk.

7. **Comprehensive Testing**: Dual testing approach with unit tests (specific examples, edge cases) and property-based tests (universal properties across randomized inputs) ensures correctness.

### Migration Benefits

- **Modernized Backend**: Laravel provides better structure, security, and maintainability than custom PHP
- **Preserved UI**: No retraining needed for administrators, familiar Windows 10 Desktop interface
- **Leveraged Knowledge**: Developers already know ExtJS, no new framework learning curve
- **Gradual Transition**: Phased migration reduces risk and allows early feedback
- **Improved Testing**: Property-based testing catches edge cases that unit tests miss
- **Better Documentation**: Comprehensive docs for package development and migration

### Next Steps

1. Review and approve this design document
2. Proceed to task creation phase
3. Begin Phase 1 implementation (Foundation)
4. Conduct regular reviews and adjust as needed

