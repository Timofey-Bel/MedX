# Design Document: Core UI Components Migration

## Overview

This design document outlines the migration strategy for converting core UI components from the legacy Smarty template system to Laravel Blade templates. The migration encompasses header, footer, search functionality, mobile menu, catalog menu, secondary navigation, and cart/favorites counters.

### Migration Scope

The migration involves:
- **Template System**: Smarty (`.tpl` files) → Laravel Blade (`.blade.php` files)
- **Controller Architecture**: Legacy PHP classes extending `aModule` → Laravel controllers
- **Data Flow**: Direct database queries with Smarty assignment → Laravel query builder/Eloquent with view data
- **Session Management**: `$_SESSION` → Laravel session facade
- **Routing**: Legacy URL patterns → Laravel named routes
- **JavaScript Integration**: Preserve existing KnockoutJS bindings and event handlers

### Design Principles

1. **Backward Compatibility**: Preserve all CSS classes, HTML IDs, and data attributes to maintain JavaScript compatibility
2. **Progressive Migration**: Components can be migrated independently without breaking existing functionality
3. **Database Schema Preservation**: No database changes required; work with existing schema
4. **Performance Optimization**: Implement lazy loading and AJAX for dynamic content
5. **Accessibility**: Maintain and improve ARIA attributes and semantic HTML

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Browser (Client)                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Header     │  │    Footer    │  │ Mobile Menu  │      │
│  │  Component   │  │  Component   │  │  Component   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│         │                  │                  │              │
│         └──────────────────┴──────────────────┘              │
│                            │                                 │
│                   KnockoutJS Bindings                        │
│                            │                                 │
└────────────────────────────┼─────────────────────────────────┘
                             │
                    HTTP/AJAX Requests
                             │
┌────────────────────────────┼─────────────────────────────────┐
│                   Laravel Application                         │
│                            │                                 │
│  ┌─────────────────────────┴──────────────────────────────┐ │
│  │                    Routes (web.php)                     │ │
│  └─────────────────────────┬──────────────────────────────┘ │
│                            │                                 │
│  ┌─────────────────────────┴──────────────────────────────┐ │
│  │                    Controllers                          │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │ │
│  │  │    Menu      │  │    Search    │  │  Secondary   │ │ │
│  │  │  Controller  │  │  Controller  │  │     Nav      │ │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘ │ │
│  └─────────────────────────┬──────────────────────────────┘ │
│                            │                                 │
│  ┌─────────────────────────┴──────────────────────────────┐ │
│  │              Query Builder / Eloquent                   │ │
│  └─────────────────────────┬──────────────────────────────┘ │
│                            │                                 │
└────────────────────────────┼─────────────────────────────────┘
                             │
┌────────────────────────────┼─────────────────────────────────┐
│                        Database                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐    │
│  │   tree   │  │  pages   │  │ products │  │  authors │    │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘    │
└──────────────────────────────────────────────────────────────┘
```

### Component Architecture

Each UI component follows this pattern:

1. **Controller**: Handles data retrieval and business logic
2. **View (Blade Template)**: Renders HTML with data
3. **JavaScript**: Handles client-side interactions (preserved from legacy)
4. **CSS**: Styling (preserved from legacy)


## Components and Interfaces

### 1. File Structure Mapping

#### Legacy Structure
```
legacy/site/modules/sfera/
├── tpl/
│   ├── header.tpl              # Main header template
│   ├── footer.tpl              # Footer and mobile bottom nav
│   └── mobile_menu.tpl         # Mobile drawer menu
├── catalog_menu/
│   ├── catalog_menu.class.php  # Catalog menu controller
│   ├── catalog_menu.tpl        # Desktop catalog menu
│   └── mobile_menu.tpl         # Mobile catalog menu
├── search/
│   ├── search.class.php        # Search controller
│   └── *.tpl                   # Search templates
├── secondary_nav/
│   ├── secondary_nav.class.php # Secondary nav controller
│   └── secondary_nav.tpl       # Secondary nav template
└── menu/
    ├── menu.class.php          # Menu controller
    └── *.tpl                   # Various menu templates
```

#### Laravel Structure
```
app/Http/Controllers/
├── MenuController.php          # Handles catalog menu logic
├── SearchController.php        # Handles search functionality
├── SecondaryNavController.php  # Handles secondary navigation
└── ComponentController.php     # Handles reusable components

resources/views/
├── layouts/
│   └── app.blade.php           # Main layout with header/footer
├── components/
│   ├── header.blade.php        # Header component
│   ├── footer.blade.php        # Footer component
│   ├── mobile-menu.blade.php   # Mobile drawer menu
│   ├── mobile-bottom-nav.blade.php  # Mobile bottom navigation
│   ├── catalog-menu.blade.php  # Catalog dropdown menu
│   └── secondary-nav.blade.php # Secondary navigation
└── search/
    ├── index.blade.php         # Search results page
    └── autocomplete.blade.php  # Autocomplete suggestions

routes/
└── web.php                     # Route definitions
```

### 2. Template Syntax Conversion

#### Smarty → Blade Syntax Mapping

| Smarty Syntax | Blade Syntax | Example |
|---------------|--------------|---------|
| `{$variable}` | `{{ $variable }}` | `{{ $user->name }}` |
| `{if $condition}...{/if}` | `@if($condition)...@endif` | `@if($user->isAdmin())` |
| `{foreach $items as $item}...{/foreach}` | `@foreach($items as $item)...@endforeach` | `@foreach($products as $product)` |
| `{include file="path/file.tpl"}` | `@include('path.file')` | `@include('components.header')` |
| `{$smarty.session.variable}` | `{{ session('variable') }}` | `{{ session('cart.items') }}` |
| `~~mod path="sfera/" mod_name="menu"~` | `@include('components.menu')` or Controller method | `@include('components.catalog-menu')` |

#### Detailed Conversion Examples

**Legacy Smarty Header (header.tpl):**
```smarty
<header class="header">
    <div class="header-main">
        <div class="header-container">
            <a href="/"><img src="/assets/sfera/img/logo/logo_dark.svg" alt="Logo"></a>
            
            <form class="search-bar" action="/search/" method="get">
                <input type="text" name="query" value="~~if $search_query~~~$search_query~~~/if~">
            </form>
            
            ~~mod path="sfera/" mod_name="catalog_menu"~
        </div>
    </div>
</header>
```

**Laravel Blade Header (header.blade.php):**
```blade
<header class="header">
    <div class="header-main">
        <div class="header-container">
            <a href="{{ route('home') }}"><img src="{{ asset('assets/sfera/img/logo/logo_dark.svg') }}" alt="Logo"></a>
            
            <form class="search-bar" action="{{ route('search') }}" method="get">
                <input type="text" name="query" value="{{ session('search_query', '') }}">
            </form>
            
            @include('components.catalog-menu')
        </div>
    </div>
</header>
```

### 3. Controller Architecture Migration

#### Legacy Controller Pattern

```php
// legacy/site/modules/sfera/catalog_menu/catalog_menu.class.php
class catalog_menu extends aModule
{
    function execute($arr)
    {
        $query = "SELECT id, name, parent_id FROM tree 
                  WHERE (parent_id = '' OR parent_id IS NULL)
                  ORDER BY name ASC";
        $root_categories = rows($query);
        
        $categories = [];
        foreach ($root_categories as $category) {
            $category_data = [
                'id' => $category['id'],
                'name' => $category['name'],
                'children' => $this->getChildren($category['id'])
            ];
            $categories[] = $category_data;
        }
        
        $_SESSION['smarty']->assign('categories', $categories);
        print ($_SESSION['smarty']->fetch('sfera/catalog_menu/catalog_menu.tpl'));
    }
    
    private function getChildren($parent_id)
    {
        $parent_id_safe = noSQL($parent_id);
        $query = "SELECT id, name, parent_id FROM tree 
                  WHERE parent_id = '$parent_id_safe'
                  ORDER BY name ASC";
        $children = rows($query);
        
        $result = [];
        foreach ($children as $child) {
            $child_data = [
                'id' => $child['id'],
                'name' => $child['name'],
                'children' => $this->getChildren($child['id'])
            ];
            $result[] = $child_data;
        }
        return $result;
    }
}
```

#### Laravel Controller Pattern

```php
// app/Http/Controllers/MenuController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Get catalog menu data for rendering
     */
    public function getCatalogMenu()
    {
        // Cache the menu for 1 hour to improve performance
        $categories = Cache::remember('catalog_menu', 3600, function () {
            return $this->getRootCategories();
        });
        
        return view('components.catalog-menu', compact('categories'));
    }
    
    /**
     * Get root categories from tree table
     */
    private function getRootCategories()
    {
        $rootCategories = DB::table('tree')
            ->whereNull('parent_id')
            ->orWhere('parent_id', '')
            ->orderBy('name', 'asc')
            ->get();
        
        $categories = [];
        foreach ($rootCategories as $category) {
            $categories[] = [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $this->getChildren($category->id)
            ];
        }
        
        return $categories;
    }
    
    /**
     * Recursively get child categories
     */
    private function getChildren($parentId)
    {
        $children = DB::table('tree')
            ->where('parent_id', $parentId)
            ->orderBy('name', 'asc')
            ->get();
        
        $result = [];
        foreach ($children as $child) {
            $result[] = [
                'id' => $child->id,
                'name' => $child->name,
                'children' => $this->getChildren($child->id)
            ];
        }
        
        return $result;
    }
    
    /**
     * AJAX endpoint to get subcategories on demand
     */
    public function getSubcategories(Request $request, $categoryId)
    {
        $children = $this->getChildren($categoryId);
        return response()->json(['success' => true, 'categories' => $children]);
    }
}
```

### 4. Data Flow Comparison

#### Legacy Data Flow

```
1. Browser Request → /catalog/
2. Legacy Router → Loads catalog module
3. catalog.class.php::execute()
   ├── Direct SQL: rows("SELECT * FROM products...")
   ├── $_SESSION['smarty']->assign('products', $products)
   └── print $_SESSION['smarty']->fetch('catalog.tpl')
4. Smarty Template → Renders HTML
5. Response → Browser
```

#### Laravel Data Flow

```
1. Browser Request → /catalog/
2. Laravel Router (web.php) → Route::get('/catalog', [CatalogController::class, 'index'])
3. CatalogController::index()
   ├── DB::table('products')->get() or Product::all()
   ├── return view('catalog.index', ['products' => $products])
   └── Blade Template Engine processes view
4. Blade Template → Renders HTML
5. Response → Browser
```

### 5. Session Handling Migration

#### Legacy Session Usage

```php
// Setting session data
$_SESSION['search'] = $query;
$_SESSION['cart']['items'] = $items;

// Getting session data in template
{$smarty.session.search}
{$smarty.session.cart.items|@count}
```

#### Laravel Session Usage

```php
// Setting session data (in controller)
session(['search_query' => $query]);
session(['cart.items' => $items]);

// Getting session data (in controller)
$query = session('search_query');
$items = session('cart.items', []);

// Getting session data (in Blade template)
{{ session('search_query') }}
{{ count(session('cart.items', [])) }}
```

### 6. Route Mapping

#### Legacy URL Patterns

```
/                           → Main page
/catalog/                   → Catalog listing
/catalog/{category_id}/     → Category page
/search/?query=...          → Search results
/product/{product_id}/      → Product page
/page/{page_id}/            → Static page
/cart/                      → Cart page
/favorites/                 → Favorites page
```

#### Laravel Routes (web.php)

```php
// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{categoryId}', [CatalogController::class, 'index'])->name('category');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/product/{productId}', [ProductController::class, 'show'])->name('product');
Route::get('/page/{pageId}', [PageController::class, 'show'])->name('page');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites');

// API routes for AJAX
Route::get('/api/search/autocomplete', [SearchController::class, 'autocomplete'])->name('api.search.autocomplete');
Route::get('/api/menu/subcategories/{categoryId}', [MenuController::class, 'getSubcategories'])->name('api.menu.subcategories');
Route::get('/api/cart', [CartController::class, 'getCartData'])->name('api.cart');
Route::get('/api/favorites', [FavoritesController::class, 'getFavoritesData'])->name('api.favorites');
```

#### Using Routes in Blade Templates

```blade
{{-- Named routes --}}
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('catalog') }}">Catalog</a>
<a href="{{ route('category', ['categoryId' => $category->id]) }}">{{ $category->name }}</a>
<a href="{{ route('product', ['productId' => $product->id]) }}">{{ $product->name }}</a>

{{-- Routes with query parameters --}}
<a href="{{ route('search', ['query' => $searchTerm]) }}">Search</a>

{{-- Static assets --}}
<img src="{{ asset('assets/sfera/img/logo/logo_dark.svg') }}" alt="Logo">
```


### 7. JavaScript/CSS Preservation Strategy

#### CSS Class Preservation

All CSS classes from legacy templates must be preserved exactly to maintain styling:

```blade
{{-- Legacy Smarty --}}
<header class="header">
    <div class="header-main">
        <div class="header-container">
            <button class="catalog-button" id="catalogButton">

{{-- Laravel Blade (SAME classes) --}}
<header class="header">
    <div class="header-main">
        <div class="header-container">
            <button class="catalog-button" id="catalogButton">
```

#### HTML ID Preservation

All element IDs must be preserved for JavaScript selectors:

```blade
{{-- IDs that must be preserved --}}
<button id="mobileMenuButton">
<div id="catalogDropdown">
<div id="catalogOverlay">
<div id="searchSuggestions">
<nav id="mobileMenu">
<div id="mobileMenuOverlay">
```

#### Data Attribute Preservation

All data attributes used by JavaScript must be preserved:

```blade
{{-- Data attributes for JavaScript --}}
<div data-widget="footer">
<button data-action="toggle-menu">
<div data-category-id="{{ $category->id }}">
```

### 8. KnockoutJS Integration Points

#### Cart Counter Integration

**Legacy Smarty Template:**
```html
<span class="cart-counter" data-bind="text: formattedCount, visible: isVisible"></span>
```

**Laravel Blade Template (SAME):**
```blade
<span class="cart-counter" data-bind="text: formattedCount, visible: isVisible"></span>
```

**KnockoutJS ViewModel (Preserved):**
```javascript
// Existing JavaScript - NO CHANGES NEEDED
function CartCounterViewModel() {
    var self = this;
    self.count = ko.observable(0);
    self.formattedCount = ko.computed(function() {
        return self.count() > 0 ? self.count() : '';
    });
    self.isVisible = ko.computed(function() {
        return self.count() > 0;
    });
    
    // Fetch cart data from API
    self.loadCartData = function() {
        $.get('/api/cart', function(data) {
            self.count(data.count || 0);
        });
    };
    
    self.loadCartData();
    setInterval(self.loadCartData, 5000); // Refresh every 5 seconds
}

ko.applyBindings(new CartCounterViewModel(), document.querySelector('.cart-counter'));
```

#### Favorites Counter Integration

**Blade Template:**
```blade
<span class="favorites-counter" data-bind="text: formattedCount, visible: isVisible"></span>
```

**KnockoutJS ViewModel (Preserved):**
```javascript
function FavoritesCounterViewModel() {
    var self = this;
    self.count = ko.observable(0);
    self.formattedCount = ko.computed(function() {
        return self.count() > 0 ? self.count() : '';
    });
    self.isVisible = ko.computed(function() {
        return self.count() > 0;
    });
    
    self.loadFavoritesData = function() {
        $.get('/api/favorites', function(data) {
            self.count(data.count || 0);
        });
    };
    
    self.loadFavoritesData();
    setInterval(self.loadFavoritesData, 5000);
}

ko.applyBindings(new FavoritesCounterViewModel(), document.querySelector('.favorites-counter'));
```

### 9. Component-Specific Designs

#### Header Component

**File**: `resources/views/components/header.blade.php`

**Data Requirements:**
- `$searchQuery` (string): Current search query from session
- `$categories` (array): Root categories for catalog menu
- `$user` (object|null): Current authenticated user

**Template Structure:**
```blade
<header class="header">
    {{-- Location Bar (optional) --}}
    @include('components.location-bar')
    
    {{-- Main Header --}}
    <div class="header-main">
        <div class="header-container">
            {{-- Mobile Menu Button --}}
            <button class="mobile-menu-button" id="mobileMenuButton">
                <svg>...</svg>
            </button>

            {{-- Logo --}}
            <div class="header-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/sfera/img/logo/logo_dark.svg') }}" alt="Творческий Центр СФЕРА" height="100">
                </a>
            </div>

            {{-- Catalog Button --}}
            <button class="catalog-button" id="catalogButton">
                <svg>...</svg>
                <span>Каталог</span>
            </button>

            {{-- Catalog Dropdown Menu --}}
            <div class="catalog-dropdown" id="catalogDropdown">
                <div class="catalog-overlay" id="catalogOverlay"></div>
                <div class="catalog-menu">
                    @include('components.catalog-menu', ['categories' => $categories])
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="search-bar-wrapper">
                <form class="search-bar" action="{{ route('search') }}" method="get">
                    <input type="text" name="query" placeholder="Искать" class="search-input" 
                           value="{{ session('search_query', '') }}" autocomplete="off">
                    <button type="submit" class="search-submit">
                        <svg>...</svg>
                    </button>
                </form>
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>

            {{-- User Actions --}}
            <nav class="header-actions">
                <a href="{{ route('login') }}" class="header-action">
                    <svg>...</svg>
                    <span>Войти</span>
                </a>
                <a href="{{ route('orders') }}" class="header-action">
                    <svg>...</svg>
                    <span>Заказы</span>
                </a>
                <a href="{{ route('favorites') }}" class="header-action header-action-favorites">
                    <svg>...</svg>
                    <span>Избранное</span>
                    <span class="mobile-nav-badge favorites-counter" style="right:20%;top:-10px;" 
                          data-bind="text: formattedCount, visible: isVisible"></span>
                </a>
                <a href="{{ route('cart') }}" class="header-action header-action-cart">
                    <svg>...</svg>
                    <span>Корзина</span>
                    <span class="mobile-nav-badge cart-counter" style="right:20%;top:-10px;" 
                          data-bind="text: formattedCount, visible: isVisible"></span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Secondary Navigation --}}
    <div class="header-secondary">
        <div class="header-container">
            @include('components.secondary-nav')
        </div>
    </div>
</header>
```

#### Footer Component

**File**: `resources/views/components/footer.blade.php`

**Data Requirements:**
- `$footerLinks` (array): Footer navigation links
- `$socialMedia` (array): Social media links

**Template Structure:**
```blade
{{-- Mobile Bottom Navigation --}}
@include('components.mobile-bottom-nav')

<footer class="dj5_11" data-widget="footer">
    <div class="jd5_11">
        {{-- Top Section with Logo and Quick Links --}}
        <div class="dj6_11">
            <a href="{{ route('home') }}" class="q4b012-a j6d_11">
                <img src="{{ asset('assets/sfera/img/logo/logo_dark.svg') }}" alt="ClustBUY" class="dj7_11 b9100-a">
            </a>
            <div class="d6j_11">
                <a href="{{ route('vacancies') }}" class="q4b012-a jd6_11">
                    <svg>...</svg>
                    <span>Вакансии</span>
                </a>
                <a href="{{ route('events') }}" class="q4b012-a jd6_11">
                    <svg>...</svg>
                    <span>События</span>
                </a>
                <a href="{{ route('support') }}" class="q4b012-a jd6_11">
                    <svg>...</svg>
                    <span>Поддержка</span>
                </a>
            </div>
        </div>

        {{-- Main Footer Content --}}
        <div class="d7j_11">
            <div class="jd7_11">
                <div class="dk_11">
                    {{-- Company Info Column --}}
                    <div class="kd_11">
                        <span class="dk0_11">ИЗДАТЕЛЬСТВО "ТВОРЧЕСКИЙ ЦЕНТР СФЕРА"</span>
                        <a href="{{ route('about') }}" class="q4b012-a d0k_11">Об издательстве</a>
                    </div>

                    {{-- Customer Info Column --}}
                    <div class="kd_11">
                        <span class="dk0_11">Покупателям</span>
                        <a href="{{ route('catalogs') }}" class="q4b012-a d0k_11">Каталоги продукции</a>
                        <a href="{{ route('videos') }}" class="q4b012-a d0k_11">Обучающие видео</a>
                        <a href="{{ route('wholesale') }}" class="q4b012-a d0k_11">Оптовым клиентам</a>
                        <a href="{{ route('retail') }}" class="q4b012-a d0k_11">Розничным покупателям</a>
                        <a href="{{ route('contacts') }}" class="q4b012-a d0k_11">Контакты</a>
                    </div>

                    {{-- Help Column --}}
                    <div class="kd_11">
                        <span class="dk0_11">Помощь</span>
                        <a href="{{ route('how-to-buy') }}" class="q4b012-a d0k_11">Как купить</a>
                        <a href="{{ route('how-to-order') }}" class="q4b012-a d0k_11">Как оформить заказ</a>
                        <a href="{{ route('returns') }}" class="q4b012-a d0k_11">Возврат</a>
                        <a href="{{ route('delivery') }}" class="q4b012-a d0k_11">Способы доставки</a>
                        <a href="{{ route('payment') }}" class="q4b012-a d0k_11">Способы оплаты</a>
                        <a href="{{ route('feedback') }}" class="q4b012-a d0k_11">Обратная связь</a>
                    </div>
                </div>

                {{-- Social Media and Copyright --}}
                <div class="j7d_11">
                    <div class="y0c_11 d8j_11">
                        <a href="https://vk.com/..." target="_blank" class="q4b012-a cy1_11 c1y_11">
                            <svg>...</svg>
                        </a>
                        <a href="https://ok.ru/..." target="_blank" class="q4b012-a cy1_11 c1y_11">
                            <svg>...</svg>
                        </a>
                        <a href="https://t.me/..." target="_blank" class="q4b012-a cy1_11 c1y_11">
                            <svg>...</svg>
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('home') }}" class="q4b012-a w1c_11 dj8_11">
                            <span>© {{ date('Y') }} Творческий Центр СФЕРА</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
```

#### Mobile Bottom Navigation Component

**File**: `resources/views/components/mobile-bottom-nav.blade.php`

**Template Structure:**
```blade
<style>
    @media (max-width: 894px) {
        .dj5_11 {
            display: none;
        }
        .mobile-bottom-nav {
            display: flex;
        }
    }

    @media (min-width: 895px) {
        .mobile-bottom-nav {
            display: none;
        }
    }
</style>

<nav class="mobile-bottom-nav">
    <a href="{{ route('home') }}" class="mobile-bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <svg>...</svg>
        <span>Главная</span>
    </a>

    <a href="{{ route('catalog') }}" class="mobile-bottom-nav-item {{ request()->routeIs('catalog') ? 'active' : '' }}">
        <svg>...</svg>
        <span>Каталог</span>
    </a>

    <a href="{{ route('favorites') }}" class="mobile-bottom-nav-item {{ request()->routeIs('favorites') ? 'active' : '' }}">
        <svg>...</svg>
        <span>Избранное</span>
        <span class="mobile-nav-badge favorites-counter" data-bind="text: formattedCount, visible: isVisible"></span>
    </a>

    <a href="{{ route('cart') }}" class="mobile-bottom-nav-item {{ request()->routeIs('cart') ? 'active' : '' }}">
        <svg>...</svg>
        <span>Корзина</span>
        <span class="mobile-nav-badge mobile-cart-counter" data-bind="text: formattedCount, visible: isVisible"></span>
    </a>

    <a href="{{ route('profile') }}" class="mobile-bottom-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
        <svg>...</svg>
        <span>Профиль</span>
    </a>
</nav>
```

#### Catalog Menu Component

**File**: `resources/views/components/catalog-menu.blade.php`

**Data Requirements:**
- `$categories` (array): Hierarchical category tree

**Template Structure:**
```blade
<div class="catalog-menu-content">
    @foreach($categories as $category)
        <div class="catalog-menu-item" data-category-id="{{ $category['id'] }}">
            <a href="{{ route('category', ['categoryId' => $category['id']]) }}" class="catalog-menu-link">
                {{ $category['name'] }}
                @if(!empty($category['children']))
                    <svg class="catalog-menu-arrow">...</svg>
                @endif
            </a>
            
            @if(!empty($category['children']))
                <div class="catalog-submenu">
                    @foreach($category['children'] as $child)
                        <div class="catalog-submenu-item">
                            <a href="{{ route('category', ['categoryId' => $child['id']]) }}">
                                {{ $child['name'] }}
                            </a>
                            
                            @if(!empty($child['children']))
                                <div class="catalog-submenu-nested">
                                    @foreach($child['children'] as $grandchild)
                                        <a href="{{ route('category', ['categoryId' => $grandchild['id']]) }}">
                                            {{ $grandchild['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
```

#### Secondary Navigation Component

**File**: `resources/views/components/secondary-nav.blade.php`

**Data Requirements:**
- `$menuItems` (array): Hierarchical menu structure from pages table

**Controller Method:**
```php
// app/Http/Controllers/SecondaryNavController.php
public function getMenuItems()
{
    $pages = DB::table('pages')
        ->where('active', 1)
        ->orderBy('parent_id', 'asc')
        ->orderBy('sort', 'asc')
        ->get();
    
    return $this->buildMenuTree($pages->toArray(), 0);
}

private function buildMenuTree($pages, $parentId = 0)
{
    $menu = [];
    
    foreach ($pages as $page) {
        $pageParentId = ($page->parent_id === null || $page->parent_id === '' || intval($page->parent_id) === 0) 
            ? 0 
            : intval($page->parent_id);
        
        if ($pageParentId === intval($parentId)) {
            $menuItem = [
                'id' => $page->id,
                'title' => !empty($page->title) ? $page->title : $page->name,
                'link' => !empty($page->content) ? route('page', ['pageId' => $page->id]) : '#'
            ];
            
            $submenu = $this->buildMenuTree($pages, $page->id);
            if (!empty($submenu)) {
                $menuItem['submenu'] = $submenu;
            }
            
            $menu[] = $menuItem;
        }
    }
    
    return $menu;
}
```

**Template Structure:**
```blade
<nav class="secondary-nav">
    @foreach($menuItems as $item)
        <div class="secondary-nav-item">
            <a href="{{ $item['link'] }}" class="secondary-nav-link">
                {{ $item['title'] }}
            </a>
            
            @if(!empty($item['submenu']))
                <div class="secondary-nav-submenu">
                    @foreach($item['submenu'] as $subitem)
                        <a href="{{ $subitem['link'] }}" class="secondary-nav-sublink">
                            {{ $subitem['title'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</nav>
```


#### Search Controller

**File**: `app/Http/Controllers/SearchController.php`

**Methods:**

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Display search results
     */
    public function index(Request $request)
    {
        $query = $request->input('query', '');
        
        // Store query in session
        if (!empty($query)) {
            session(['search_query' => $query]);
        } else {
            session()->forget('search_query');
            $query = session('search_query', '');
        }
        
        $page = $request->input('page', 1);
        $limit = 20;
        
        // Get filters
        $filterAuthors = $request->input('author', []);
        $filterAges = $request->input('age', []);
        
        // Get products
        $products = $this->getProducts($page, $limit, $query, $filterAuthors, $filterAges);
        $total = $this->getProductsCount($query, $filterAuthors, $filterAges);
        
        // Calculate pagination
        $pages = $total > 0 ? ceil($total / $limit) : 1;
        $pagesPerGroup = 5;
        $currentGroup = ceil($page / $pagesPerGroup);
        $startPage = ($currentGroup - 1) * $pagesPerGroup + 1;
        $endPage = min($currentGroup * $pagesPerGroup, $pages);
        $nextGroupStart = $endPage + 1;
        $prevGroup = $currentGroup - 1;
        $prevGroupEnd = $prevGroup > 0 ? min($prevGroup * $pagesPerGroup, $pages) : 1;
        
        return view('search.index', [
            'products' => $products,
            'search_query' => $query,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => $pages,
            'startPage' => $startPage,
            'endPage' => $endPage,
            'nextGroupStart' => $nextGroupStart,
            'prevGroupEnd' => $prevGroupEnd,
            'hasNextGroup' => $nextGroupStart <= $pages,
            'hasPrevGroup' => $currentGroup > 1,
        ]);
    }
    
    /**
     * AJAX autocomplete endpoint
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }
        
        $suggestions = DB::table('products as p')
            ->leftJoin('prices as pr', function($join) {
                $join->on('p.id', '=', 'pr.product_id')
                     ->where('pr.price_type_id', '=', '000000002');
            })
            ->where(function($where) use ($query) {
                $where->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.id', 'LIKE', "%{$query}%");
            })
            ->select('p.id', 'p.name', 'p.picture', 'pr.price')
            ->limit(10)
            ->get();
        
        $results = [];
        foreach ($suggestions as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $this->getProductImageUrl($product->id),
                'price' => round($product->price ?? 0),
                'url' => route('product', ['productId' => $product->id])
            ];
        }
        
        return response()->json(['suggestions' => $results]);
    }
    
    private function getProducts($page, $limit, $query, $filterAuthors, $filterAges)
    {
        $offset = ($page - 1) * $limit;
        
        if (empty($query)) {
            return [];
        }
        
        $queryBuilder = DB::table('products as p')
            ->leftJoin('prices as pr', function($join) {
                $join->on('p.id', '=', 'pr.product_id')
                     ->where('pr.price_type_id', '=', '000000002');
            })
            ->where(function($where) use ($query) {
                $where->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.id', 'LIKE', "%{$query}%");
            });
        
        // Apply author filter
        if (!empty($filterAuthors)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAuthors) {
                $subquery->select(DB::raw(1))
                         ->from('authors as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.author_name', $filterAuthors);
            });
        }
        
        // Apply age filter
        if (!empty($filterAges)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAges) {
                $subquery->select(DB::raw(1))
                         ->from('ages as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.age', $filterAges);
            });
        }
        
        $results = $queryBuilder
            ->select('p.*', 'pr.price as product_price', 'p.quantity')
            ->orderBy('p.name', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        $products = [];
        foreach ($results as $row) {
            $products[] = [
                'id' => $row->id,
                'name' => $row->name ?? '',
                'description' => $row->description ?? '',
                'image' => $this->getProductImageUrl($row->id),
                'price' => round($row->product_price ?? 0),
                'quantity' => intval($row->quantity ?? 99)
            ];
        }
        
        return $products;
    }
    
    private function getProductsCount($query, $filterAuthors, $filterAges)
    {
        if (empty($query)) {
            return 0;
        }
        
        $queryBuilder = DB::table('products as p')
            ->where(function($where) use ($query) {
                $where->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.id', 'LIKE', "%{$query}%");
            });
        
        if (!empty($filterAuthors)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAuthors) {
                $subquery->select(DB::raw(1))
                         ->from('authors as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.author_name', $filterAuthors);
            });
        }
        
        if (!empty($filterAges)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAges) {
                $subquery->select(DB::raw(1))
                         ->from('ages as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.age', $filterAges);
            });
        }
        
        return $queryBuilder->count();
    }
    
    private function getProductImageUrl($productId)
    {
        if (empty($productId)) {
            return '/assets/img/product_empty.jpg';
        }
        
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $productId)
            ->first();
        
        if ($ozonProduct && !empty($ozonProduct->product_id)) {
            $oImage = DB::table('o_images')
                ->where('product_id', $ozonProduct->product_id)
                ->where('image_order', 0)
                ->first();
            
            if ($oImage) {
                return "/o_images/{$oImage->product_id}/0.jpg";
            }
        }
        
        return "/import_files/{$productId}b.jpg";
    }
}
```

#### Mobile Menu Drawer Component

**File**: `resources/views/components/mobile-menu.blade.php`

**Data Requirements:**
- `$categories` (array): Category hierarchy for mobile menu
- `$user` (object|null): Current authenticated user

**Template Structure:**
```blade
{{-- Mobile Menu Overlay --}}
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

{{-- Mobile Menu --}}
<nav class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <img src="{{ asset('assets/sfera/img/logo/logo_white.svg') }}" alt="Творческий Центр СФЕРА" height="50">
        <button class="mobile-menu-close" id="mobileMenuClose">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M5 5l10 10M15 5l-10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <div class="mobile-menu-section">
        <button class="mobile-menu-location">
            <svg>...</svg>
            <span>Москва</span>
        </button>
    </div>

    <div class="mobile-menu-section">
        <div class="mobile-menu-section-title">Каталог</div>
        <div class="mobile-menu-catalog">
            @foreach($categories as $category)
                <div class="mobile-menu-category" data-category-id="{{ $category['id'] }}">
                    <a href="{{ route('category', ['categoryId' => $category['id']]) }}" class="mobile-menu-category-link">
                        {{ $category['name'] }}
                        @if(!empty($category['children']))
                            <svg class="mobile-menu-expand-icon">...</svg>
                        @endif
                    </a>
                    
                    @if(!empty($category['children']))
                        <div class="mobile-menu-subcategories">
                            @foreach($category['children'] as $child)
                                <a href="{{ route('category', ['categoryId' => $child['id']]) }}" class="mobile-menu-subcategory-link">
                                    {{ $child['name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="mobile-menu-section">
        <div class="mobile-menu-section-title">Профиль</div>
        <div class="mobile-menu-links">
            <a href="{{ route('login') }}" class="mobile-menu-link">
                <svg>...</svg>
                Войти
            </a>
            <a href="{{ route('orders') }}" class="mobile-menu-link">
                <svg>...</svg>
                Заказы
            </a>
            <a href="{{ route('favorites') }}" class="mobile-menu-link">
                <svg>...</svg>
                Избранное
            </a>
        </div>
    </div>
</nav>
```

## Data Models

### Database Tables Used

#### 1. tree (Categories)

```sql
CREATE TABLE tree (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255),
    parent_id VARCHAR(255) NULL,
    active TINYINT(1) DEFAULT 1,
    sort INT DEFAULT 0,
    -- other fields...
);
```

**Usage**: Category hierarchy for catalog menu and mobile menu

**Query Pattern**:
```php
// Get root categories
DB::table('tree')
    ->whereNull('parent_id')
    ->orWhere('parent_id', '')
    ->where('active', 1)
    ->orderBy('name', 'asc')
    ->get();

// Get children of a category
DB::table('tree')
    ->where('parent_id', $parentId)
    ->where('active', 1)
    ->orderBy('name', 'asc')
    ->get();
```

#### 2. pages (Navigation Pages)

```sql
CREATE TABLE pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    title VARCHAR(255) NULL,
    content TEXT NULL,
    parent_id INT NULL,
    sort INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    -- other fields...
);
```

**Usage**: Secondary navigation menu

**Query Pattern**:
```php
DB::table('pages')
    ->where('active', 1)
    ->orderBy('parent_id', 'asc')
    ->orderBy('sort', 'asc')
    ->get();
```

#### 3. products (Product Catalog)

```sql
CREATE TABLE products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    picture VARCHAR(255),
    quantity INT DEFAULT 0,
    category_id VARCHAR(255),
    -- other fields...
);
```

**Usage**: Search results and product listings

**Query Pattern**:
```php
DB::table('products as p')
    ->leftJoin('prices as pr', function($join) {
        $join->on('p.id', '=', 'pr.product_id')
             ->where('pr.price_type_id', '=', '000000002');
    })
    ->where('p.name', 'LIKE', "%{$query}%")
    ->orWhere('p.id', 'LIKE', "%{$query}%")
    ->select('p.*', 'pr.price as product_price')
    ->get();
```

#### 4. authors (Product Authors)

```sql
CREATE TABLE authors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id VARCHAR(255),
    author_name VARCHAR(255),
    -- other fields...
);
```

**Usage**: Author filter in search

**Query Pattern**:
```php
// Filter products by authors
->whereExists(function($subquery) use ($filterAuthors) {
    $subquery->select(DB::raw(1))
             ->from('authors as a')
             ->whereColumn('a.product_id', 'p.id')
             ->whereIn('a.author_name', $filterAuthors);
});
```

#### 5. ages (Product Age Ranges)

```sql
CREATE TABLE ages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id VARCHAR(255),
    age VARCHAR(50),
    -- other fields...
);
```

**Usage**: Age filter in search

**Query Pattern**:
```php
// Filter products by age ranges
->whereExists(function($subquery) use ($filterAges) {
    $subquery->select(DB::raw(1))
             ->from('ages as a')
             ->whereColumn('a.product_id', 'p.id')
             ->whereIn('a.age', $filterAges);
});
```

### View Data Structures

#### Category Data Structure

```php
[
    'id' => 'category_id',
    'name' => 'Category Name',
    'parent_id' => 'parent_category_id' or null,
    'children' => [
        // Recursive array of child categories
        [
            'id' => 'child_id',
            'name' => 'Child Name',
            'children' => [...]
        ]
    ]
]
```

#### Menu Item Data Structure

```php
[
    'id' => 1,
    'title' => 'Page Title',
    'link' => '/page/1/',
    'submenu' => [
        // Recursive array of submenu items
        [
            'id' => 2,
            'title' => 'Subpage Title',
            'link' => '/page/2/',
            'submenu' => [...]
        ]
    ]
]
```

#### Product Data Structure

```php
[
    'id' => 'product_id',
    'name' => 'Product Name',
    'description' => 'Product Description',
    'image' => '/path/to/image.jpg',
    'price' => 1234.56,
    'quantity' => 10
]
```


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Autocomplete Suggestions

*For any* search input with 2 or more characters, the autocomplete endpoint should return suggestions that match the query in either product name or ID, limited to 10 results.

**Validates: Requirements 1.3, 6.1, 6.3, 6.4**

### Property 2: Counter Real-Time Updates

*For any* change to cart or favorites data, the counter components should update immediately to reflect the new count using KnockoutJS bindings.

**Validates: Requirements 1.4, 3.3, 8.3, 8.5, 9.3, 9.5**

### Property 3: Laravel Route Usage

*For any* navigation link in Blade templates, the link should be generated using Laravel's route() or url() helpers rather than hardcoded URLs.

**Validates: Requirements 1.7, 2.7, 4.7, 7.4, 12.1, 12.2, 12.3, 12.4, 12.5, 12.6**

### Property 4: Category Hierarchy Loading

*For any* category tree loaded from the tree table, the system should correctly represent parent-child relationships recursively, filtering out inactive categories.

**Validates: Requirements 4.1, 4.2, 4.5, 4.6, 10.2**

### Property 5: Category Visual Indicators

*For any* category with children, the catalog menu should display visual cues (arrows or expand indicators) to indicate expandable sections.

**Validates: Requirements 4.3**

### Property 6: Search Product Matching

*For any* search query, the search module should return products where the query matches either the product name or ID.

**Validates: Requirements 5.1, 5.2**

### Property 7: Search Pagination

*For any* search results exceeding 20 products, the system should paginate results with 20 products per page and display correct page numbers and navigation.

**Validates: Requirements 5.3, 5.7**

### Property 8: Search Session Round-Trip

*For any* search query submitted, storing it in the session and then retrieving it should return the same query value.

**Validates: Requirements 5.4, 15.1, 15.2**

### Property 9: Search Query Clearing

*For any* search session, when the query is cleared, the session should no longer contain the search_query key.

**Validates: Requirements 15.3**

### Property 10: Search Filter Application

*For any* combination of author and age filters, the search results should only include products that match all selected filter criteria.

**Validates: Requirements 5.5**

### Property 11: Autocomplete Debouncing

*For any* rapid sequence of search inputs, the autocomplete should debounce requests such that fewer API calls are made than the number of input events.

**Validates: Requirements 6.6, 17.4**

### Property 12: Autocomplete Result Limit

*For any* autocomplete query, the number of suggestions returned should never exceed 10 items.

**Validates: Requirements 17.3**

### Property 13: Secondary Navigation Active Pages

*For any* set of pages in the database, the secondary navigation should only display pages where active=1.

**Validates: Requirements 7.2**

### Property 14: Secondary Navigation Hierarchy

*For any* page tree with parent-child relationships, the secondary navigation should correctly represent the hierarchical structure.

**Validates: Requirements 7.3**

### Property 15: Secondary Navigation Title Display

*For any* page, the secondary navigation should display the page's title if present, otherwise fall back to the page's name.

**Validates: Requirements 7.5**

### Property 16: CSS Class Preservation

*For any* template migrated from Smarty to Blade, all CSS class names from the legacy template should be present in the Blade template.

**Validates: Requirements 11.1**

### Property 17: HTML ID Preservation

*For any* template migrated from Smarty to Blade, all HTML element IDs from the legacy template should be present in the Blade template.

**Validates: Requirements 11.2**

### Property 18: Data Attribute Preservation

*For any* template migrated from Smarty to Blade, all data attributes used by JavaScript should be present in the Blade template.

**Validates: Requirements 11.3**

### Property 19: HTML Structure Preservation

*For any* template migrated from Smarty to Blade, the HTML structure should remain compatible with existing JavaScript selectors.

**Validates: Requirements 11.4**

### Property 20: KnockoutJS Binding Preservation

*For any* template migrated from Smarty to Blade, all KnockoutJS data-bind attributes should be present in the Blade template.

**Validates: Requirements 11.5**

### Property 21: SVG Icon Preservation

*For any* template migrated from Smarty to Blade, all SVG icons and their styling should be present in the Blade template.

**Validates: Requirements 11.6**

### Property 22: Database Query Preservation

*For any* database query in the legacy system, an equivalent query should exist in the Laravel controllers using query builder or Eloquent.

**Validates: Requirements 13.5, 14.6, 14.7**

### Property 23: Query Builder Usage

*For any* database access in Laravel controllers, the system should use Laravel's query builder or Eloquent rather than raw SQL strings.

**Validates: Requirements 13.6**

### Property 24: View Data Return

*For any* controller method that renders a view, the method should return view data using Laravel's view() helper.

**Validates: Requirements 13.7**

### Property 25: Table-Specific Queries

*For any* query for category data, navigation data, product data, author data, or age data, the query should target the correct table (tree, pages, products, authors, ages respectively).

**Validates: Requirements 14.1, 14.2, 14.3, 14.4, 14.5**

### Property 26: Session Data Usage

*For any* counter component, the component should use session data for cart and favorites counts.

**Validates: Requirements 15.4**

### Property 27: Session Data Passing

*For any* Blade template requiring session data, the data should be passed using Laravel's session() helper.

**Validates: Requirements 15.5**

### Property 28: Eager Loading Optimization

*For any* query that requires related data, the system should use eager loading to minimize the number of database queries.

**Validates: Requirements 17.7**

### Property 29: ARIA Label Presence

*For any* navigation element in the header, the element should include appropriate ARIA labels for accessibility.

**Validates: Requirements 18.1**

### Property 30: Expandable Section ARIA

*For any* expandable section in the catalog menu, the section should include ARIA attributes indicating its expandable state.

**Validates: Requirements 18.4**

### Property 31: Keyboard Accessibility

*For any* interactive element in the UI, the element should be accessible via keyboard navigation.

**Validates: Requirements 18.7**


## Error Handling

### Database Query Failures

**Strategy**: Graceful degradation with empty results

```php
try {
    $categories = DB::table('tree')
        ->whereNull('parent_id')
        ->get();
} catch (\Exception $e) {
    \Log::error('Failed to load categories: ' . $e->getMessage());
    $categories = collect([]); // Return empty collection
}
```

**Behavior**:
- Database query failures should return empty arrays/collections
- Errors should be logged for debugging
- UI should display gracefully with no data rather than breaking

### Missing Session Data

**Strategy**: Default values

```php
$searchQuery = session('search_query', ''); // Default to empty string
$cartItems = session('cart.items', []); // Default to empty array
```

**Behavior**:
- Missing session data should use sensible defaults
- Empty strings for text values
- Empty arrays for collections
- Zero for numeric values

### Undefined Routes

**Strategy**: Fallback URLs

```blade
{{-- Use try-catch or check if route exists --}}
@php
    try {
        $url = route('page', ['pageId' => $page->id]);
    } catch (\Exception $e) {
        $url = '#'; // Fallback to placeholder
    }
@endphp
<a href="{{ $url }}">{{ $page->title }}</a>
```

**Behavior**:
- Undefined routes should fall back to '#' placeholder
- Log warnings for missing routes
- Don't break page rendering

### Autocomplete Failures

**Strategy**: Silent failure

```javascript
// In autocomplete JavaScript
$.get('/api/search/autocomplete', { query: query })
    .done(function(data) {
        displaySuggestions(data.suggestions);
    })
    .fail(function() {
        // Fail silently - don't break the search input
        console.warn('Autocomplete request failed');
        hideSuggestions();
    });
```

**Behavior**:
- Autocomplete failures should not break the search input
- Hide suggestions on failure
- Log warnings for debugging
- Allow user to continue typing and submit search

### Empty Search Results

**Strategy**: User-friendly message

```blade
@if(empty($products))
    <div class="no-results-message">
        <p>По запросу "{{ $search_query }}" ничего не найдено.</p>
        <p>Попробуйте изменить запрос или <a href="{{ route('catalog') }}">просмотреть каталог</a>.</p>
    </div>
@else
    {{-- Display products --}}
@endif
```

**Behavior**:
- Empty search results should display a helpful message
- Suggest alternative actions (browse catalog, modify query)
- Don't show pagination for empty results

### Categories Without Children

**Strategy**: Conditional rendering

```blade
@foreach($categories as $category)
    <div class="catalog-menu-item">
        <a href="{{ route('category', ['categoryId' => $category['id']]) }}">
            {{ $category['name'] }}
            @if(!empty($category['children']))
                <svg class="catalog-menu-arrow">...</svg>
            @endif
        </a>
    </div>
@endforeach
```

**Behavior**:
- Categories without children should not display expand indicators
- Links should still work normally
- No submenu should be rendered

### Pages Without Content

**Strategy**: Placeholder links

```php
// In SecondaryNavController
$menuItem = [
    'id' => $page->id,
    'title' => !empty($page->title) ? $page->title : $page->name,
    'link' => !empty($page->content) ? route('page', ['pageId' => $page->id]) : '#'
];
```

**Behavior**:
- Pages without content should use '#' as link
- Display the page title/name normally
- Don't navigate when clicked

## Testing Strategy

### Dual Testing Approach

This migration requires both unit testing and property-based testing to ensure correctness:

**Unit Tests**: Verify specific examples, edge cases, and error conditions
- Test specific component rendering with known data
- Test error handling scenarios
- Test integration points between components
- Test specific edge cases (empty data, missing fields, etc.)

**Property Tests**: Verify universal properties across all inputs
- Test that all templates preserve CSS classes, IDs, and data attributes
- Test that all routes use Laravel helpers
- Test that all database queries return correct data structures
- Test that session round-trips work correctly
- Test that filters work for any combination of inputs

### Property-Based Testing Configuration

**Library**: Use `phpunit` with custom property test helpers or a library like `eris/eris` for PHP property-based testing

**Configuration**:
- Minimum 100 iterations per property test
- Each property test must reference its design document property
- Tag format: `@test Feature: core-ui-components-migration, Property {number}: {property_text}`

### Unit Test Examples

#### Test Header Component Rendering

```php
// tests/Feature/Components/HeaderComponentTest.php
namespace Tests\Feature\Components;

use Tests\TestCase;

class HeaderComponentTest extends TestCase
{
    /** @test */
    public function it_renders_header_with_all_required_elements()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('header', false); // Check for header tag
        $response->assertSee('header-logo', false); // Check for logo class
        $response->assertSee('catalog-button', false); // Check for catalog button
        $response->assertSee('search-bar', false); // Check for search bar
        $response->assertSee('header-actions', false); // Check for user actions
    }
    
    /** @test */
    public function it_preserves_mobile_menu_button_id()
    {
        $response = $this->get('/');
        
        $response->assertSee('id="mobileMenuButton"', false);
    }
    
    /** @test */
    public function it_displays_search_query_from_session()
    {
        session(['search_query' => 'test query']);
        
        $response = $this->get('/');
        
        $response->assertSee('value="test query"', false);
    }
}
```

#### Test Search Controller

```php
// tests/Feature/SearchControllerTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_empty_results_for_empty_query()
    {
        $response = $this->get('/search?query=');
        
        $response->assertStatus(200);
        $response->assertViewHas('products', []);
        $response->assertViewHas('total', 0);
    }
    
    /** @test */
    public function it_stores_search_query_in_session()
    {
        $this->get('/search?query=test');
        
        $this->assertEquals('test', session('search_query'));
    }
    
    /** @test */
    public function it_clears_search_query_from_session_when_empty()
    {
        session(['search_query' => 'old query']);
        
        $this->get('/search?query=');
        
        $this->assertNull(session('search_query'));
    }
    
    /** @test */
    public function autocomplete_returns_max_10_suggestions()
    {
        // Create 20 test products
        for ($i = 1; $i <= 20; $i++) {
            DB::table('products')->insert([
                'id' => "product_{$i}",
                'name' => "Test Product {$i}",
            ]);
        }
        
        $response = $this->get('/api/search/autocomplete?query=test');
        
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertLessThanOrEqual(10, count($data['suggestions']));
    }
    
    /** @test */
    public function autocomplete_returns_empty_for_short_query()
    {
        $response = $this->get('/api/search/autocomplete?query=a');
        
        $response->assertStatus(200);
        $response->assertJson(['suggestions' => []]);
    }
}
```

#### Test Menu Controller

```php
// tests/Feature/MenuControllerTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_loads_root_categories_from_tree_table()
    {
        DB::table('tree')->insert([
            ['id' => '1', 'name' => 'Category 1', 'parent_id' => null, 'active' => 1],
            ['id' => '2', 'name' => 'Category 2', 'parent_id' => null, 'active' => 1],
            ['id' => '3', 'name' => 'Child Category', 'parent_id' => '1', 'active' => 1],
        ]);
        
        $controller = new \App\Http\Controllers\MenuController();
        $categories = $controller->getCatalogMenu()->getData()['categories'];
        
        $this->assertCount(2, $categories); // Only root categories
        $this->assertEquals('Category 1', $categories[0]['name']);
        $this->assertEquals('Category 2', $categories[1]['name']);
    }
    
    /** @test */
    public function it_filters_out_inactive_categories()
    {
        DB::table('tree')->insert([
            ['id' => '1', 'name' => 'Active Category', 'parent_id' => null, 'active' => 1],
            ['id' => '2', 'name' => 'Inactive Category', 'parent_id' => null, 'active' => 0],
        ]);
        
        $controller = new \App\Http\Controllers\MenuController();
        $categories = $controller->getCatalogMenu()->getData()['categories'];
        
        $this->assertCount(1, $categories);
        $this->assertEquals('Active Category', $categories[0]['name']);
    }
    
    /** @test */
    public function it_builds_recursive_category_tree()
    {
        DB::table('tree')->insert([
            ['id' => '1', 'name' => 'Parent', 'parent_id' => null, 'active' => 1],
            ['id' => '2', 'name' => 'Child', 'parent_id' => '1', 'active' => 1],
            ['id' => '3', 'name' => 'Grandchild', 'parent_id' => '2', 'active' => 1],
        ]);
        
        $controller = new \App\Http\Controllers\MenuController();
        $categories = $controller->getCatalogMenu()->getData()['categories'];
        
        $this->assertCount(1, $categories);
        $this->assertCount(1, $categories[0]['children']);
        $this->assertCount(1, $categories[0]['children'][0]['children']);
        $this->assertEquals('Grandchild', $categories[0]['children'][0]['children'][0]['name']);
    }
}
```

### Property Test Examples

#### Property Test: CSS Class Preservation

```php
// tests/Property/TemplatePreservationTest.php
namespace Tests\Property;

use Tests\TestCase;

class TemplatePreservationTest extends TestCase
{
    /**
     * @test
     * Feature: core-ui-components-migration, Property 16: For any template migrated from Smarty to Blade, all CSS class names from the legacy template should be present in the Blade template.
     */
    public function all_css_classes_are_preserved_in_blade_templates()
    {
        // Define legacy templates and their expected classes
        $templates = [
            'header' => [
                'header', 'header-main', 'header-container', 'mobile-menu-button',
                'header-logo', 'catalog-button', 'catalog-dropdown', 'catalog-overlay',
                'catalog-menu', 'search-bar-wrapper', 'search-bar', 'search-input',
                'search-submit', 'search-suggestions', 'header-actions', 'header-action',
                'header-action-favorites', 'header-action-cart', 'mobile-nav-badge',
                'favorites-counter', 'cart-counter', 'header-secondary'
            ],
            'footer' => [
                'mobile-bottom-nav', 'mobile-bottom-nav-item', 'dj5_11', 'jd5_11',
                'dj6_11', 'q4b012-a', 'j6d_11', 'dj7_11', 'b9100-a', 'd6j_11',
                'jd6_11', 'jd8_11', 'd7j_11', 'jd7_11', 'dk_11', 'kd_11', 'dk0_11',
                'd0k_11', 'j7d_11', 'y0c_11', 'd8j_11', 'cy1_11', 'c1y_11', 'yc0_11',
                'w1c_11', 'dj8_11'
            ],
        ];
        
        foreach ($templates as $template => $expectedClasses) {
            $response = $this->get('/');
            $content = $response->getContent();
            
            foreach ($expectedClasses as $class) {
                $this->assertStringContainsString("class=\"{$class}\"", $content, 
                    "Template {$template} is missing CSS class: {$class}");
            }
        }
    }
    
    /**
     * @test
     * Feature: core-ui-components-migration, Property 17: For any template migrated from Smarty to Blade, all HTML element IDs from the legacy template should be present in the Blade template.
     */
    public function all_html_ids_are_preserved_in_blade_templates()
    {
        $expectedIds = [
            'mobileMenuButton',
            'catalogButton',
            'catalogDropdown',
            'catalogOverlay',
            'searchSuggestions',
            'mobileMenu',
            'mobileMenuOverlay',
            'mobileMenuClose',
        ];
        
        $response = $this->get('/');
        $content = $response->getContent();
        
        foreach ($expectedIds as $id) {
            $this->assertStringContainsString("id=\"{$id}\"", $content,
                "Missing HTML ID: {$id}");
        }
    }
    
    /**
     * @test
     * Feature: core-ui-components-migration, Property 20: For any template migrated from Smarty to Blade, all KnockoutJS data-bind attributes should be present in the Blade template.
     */
    public function all_knockout_bindings_are_preserved()
    {
        $expectedBindings = [
            'data-bind="text: formattedCount, visible: isVisible"',
        ];
        
        $response = $this->get('/');
        $content = $response->getContent();
        
        foreach ($expectedBindings as $binding) {
            $this->assertStringContainsString($binding, $content,
                "Missing KnockoutJS binding: {$binding}");
        }
    }
}
```

#### Property Test: Route Helper Usage

```php
// tests/Property/RouteHelperTest.php
namespace Tests\Property;

use Tests\TestCase;

class RouteHelperTest extends TestCase
{
    /**
     * @test
     * Feature: core-ui-components-migration, Property 3: For any navigation link in Blade templates, the link should be generated using Laravel's route() or url() helpers rather than hardcoded URLs.
     */
    public function all_links_use_laravel_route_helpers()
    {
        $response = $this->get('/');
        $content = $response->getContent();
        
        // Check that hardcoded URLs are not present (except for external links)
        $hardcodedPatterns = [
            'href="/catalog/"',
            'href="/search/"',
            'href="/product/',
            'href="/cart/"',
            'href="/favorites/"',
        ];
        
        foreach ($hardcodedPatterns as $pattern) {
            $this->assertStringNotContainsString($pattern, $content,
                "Found hardcoded URL pattern: {$pattern}. Should use route() helper.");
        }
    }
}
```

#### Property Test: Session Round-Trip

```php
// tests/Property/SessionRoundTripTest.php
namespace Tests\Property;

use Tests\TestCase;

class SessionRoundTripTest extends TestCase
{
    /**
     * @test
     * Feature: core-ui-components-migration, Property 8: For any search query submitted, storing it in the session and then retrieving it should return the same query value.
     */
    public function search_query_session_round_trip_preserves_value()
    {
        $testQueries = [
            'simple query',
            'query with spaces',
            'query-with-dashes',
            'query_with_underscores',
            'query123',
            'Запрос на русском',
            'query with "quotes"',
        ];
        
        foreach ($testQueries as $query) {
            // Store in session
            session(['search_query' => $query]);
            
            // Retrieve from session
            $retrieved = session('search_query');
            
            $this->assertEquals($query, $retrieved,
                "Session round-trip failed for query: {$query}");
            
            // Clear session for next iteration
            session()->forget('search_query');
        }
    }
}
```

### Integration Testing

Test the complete flow from request to response:

```php
// tests/Feature/Integration/SearchFlowTest.php
namespace Tests\Feature\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchFlowTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function complete_search_flow_works_correctly()
    {
        // Setup: Create test products
        DB::table('products')->insert([
            ['id' => '1', 'name' => 'Test Product 1'],
            ['id' => '2', 'name' => 'Test Product 2'],
            ['id' => '3', 'name' => 'Another Product'],
        ]);
        
        // Step 1: Submit search
        $response = $this->get('/search?query=Test');
        $response->assertStatus(200);
        
        // Step 2: Verify session storage
        $this->assertEquals('Test', session('search_query'));
        
        // Step 3: Verify results
        $response->assertViewHas('products');
        $products = $response->viewData('products');
        $this->assertCount(2, $products);
        
        // Step 4: Test autocomplete
        $autocompleteResponse = $this->get('/api/search/autocomplete?query=Test');
        $autocompleteResponse->assertStatus(200);
        $suggestions = $autocompleteResponse->json('suggestions');
        $this->assertLessThanOrEqual(10, count($suggestions));
        
        // Step 5: Clear search
        $clearResponse = $this->get('/search?query=');
        $this->assertNull(session('search_query'));
    }
}
```

### Performance Testing

Test that optimizations are working:

```php
// tests/Performance/MenuPerformanceTest.php
namespace Tests\Performance;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class MenuPerformanceTest extends TestCase
{
    /** @test */
    public function catalog_menu_uses_caching()
    {
        // Clear cache
        \Cache::forget('catalog_menu');
        
        // First call - should hit database
        DB::enableQueryLog();
        $controller = new \App\Http\Controllers\MenuController();
        $controller->getCatalogMenu();
        $firstCallQueries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        // Second call - should use cache
        DB::enableQueryLog();
        $controller->getCatalogMenu();
        $secondCallQueries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        $this->assertEquals(0, $secondCallQueries, 
            'Second call should use cache and make no database queries');
    }
}
```


## Migration Checklist

### Phase 1: Setup and Preparation

- [ ] Create Laravel controllers directory structure
- [ ] Create Blade components directory structure
- [ ] Set up routes in web.php
- [ ] Configure session driver in Laravel
- [ ] Set up asset compilation (if needed)

### Phase 2: Controller Migration

- [ ] Create MenuController with catalog menu methods
- [ ] Create SearchController with search and autocomplete methods
- [ ] Create SecondaryNavController with navigation methods
- [ ] Create ComponentController for reusable components
- [ ] Migrate all database queries to Laravel query builder
- [ ] Add caching for frequently accessed data
- [ ] Test all controller methods with unit tests

### Phase 3: Template Migration

- [ ] Create main layout (app.blade.php)
- [ ] Migrate header.tpl → header.blade.php
- [ ] Migrate footer.tpl → footer.blade.php
- [ ] Migrate mobile_menu.tpl → mobile-menu.blade.php
- [ ] Create mobile-bottom-nav.blade.php
- [ ] Migrate catalog_menu.tpl → catalog-menu.blade.php
- [ ] Migrate secondary_nav.tpl → secondary-nav.blade.php
- [ ] Verify all CSS classes are preserved
- [ ] Verify all HTML IDs are preserved
- [ ] Verify all data attributes are preserved
- [ ] Verify all KnockoutJS bindings are preserved

### Phase 4: JavaScript Integration

- [ ] Verify KnockoutJS cart counter works
- [ ] Verify KnockoutJS favorites counter works
- [ ] Test mobile menu toggle functionality
- [ ] Test catalog dropdown functionality
- [ ] Test search autocomplete functionality
- [ ] Test mobile drawer menu functionality
- [ ] Verify all event handlers work correctly

### Phase 5: Route Configuration

- [ ] Define all named routes in web.php
- [ ] Test all route helpers in templates
- [ ] Verify route parameters work correctly
- [ ] Test fallback URLs for undefined routes
- [ ] Update any hardcoded URLs to use route helpers

### Phase 6: Session Management

- [ ] Migrate $_SESSION usage to Laravel session
- [ ] Test session storage and retrieval
- [ ] Test session clearing
- [ ] Verify session data is passed to templates correctly

### Phase 7: Testing

- [ ] Write unit tests for all controllers
- [ ] Write unit tests for all components
- [ ] Write property tests for template preservation
- [ ] Write property tests for route helper usage
- [ ] Write property tests for session round-trips
- [ ] Write integration tests for complete flows
- [ ] Write performance tests for caching
- [ ] Test responsive behavior at breakpoints (894px, 895px)
- [ ] Test accessibility with screen readers
- [ ] Test keyboard navigation

### Phase 8: Deployment

- [ ] Run all tests in staging environment
- [ ] Verify CSS and JavaScript assets load correctly
- [ ] Test with real database data
- [ ] Verify performance meets requirements
- [ ] Deploy to production
- [ ] Monitor for errors and issues
- [ ] Verify KnockoutJS counters update correctly in production

## Appendix: Quick Reference

### Common Smarty to Blade Conversions

```blade
{{-- Variables --}}
{$variable}                     → {{ $variable }}
{$object.property}              → {{ $object->property }}
{$array.key}                    → {{ $array['key'] }}

{{-- Conditionals --}}
{if $condition}...{/if}         → @if($condition)...@endif
{if $x}{else}{/if}              → @if($x)...@else...@endif
{if $x}{elseif $y}{/if}         → @if($x)...@elseif($y)...@endif

{{-- Loops --}}
{foreach $items as $item}       → @foreach($items as $item)
{/foreach}                      → @endforeach
{foreach $items as $k => $v}    → @foreach($items as $k => $v)

{{-- Includes --}}
{include file="path/file.tpl"}  → @include('path.file')

{{-- Session --}}
{$smarty.session.key}           → {{ session('key') }}

{{-- Comments --}}
{* comment *}                   → {{-- comment --}}

{{-- Raw output (no escaping) --}}
{$html nofilter}                → {!! $html !!}

{{-- Escaped output (default) --}}
{$text}                         → {{ $text }}
```

### Laravel Route Helpers

```blade
{{-- Named routes --}}
{{ route('home') }}
{{ route('catalog') }}
{{ route('category', ['categoryId' => $id]) }}
{{ route('product', ['productId' => $id]) }}
{{ route('search', ['query' => $query]) }}

{{-- URL helper --}}
{{ url('/path') }}

{{-- Asset helper --}}
{{ asset('assets/img/logo.svg') }}

{{-- Check current route --}}
{{ request()->routeIs('home') ? 'active' : '' }}
```

### Laravel Session Helpers

```php
// In controllers
session(['key' => 'value']);           // Set
$value = session('key');               // Get
$value = session('key', 'default');    // Get with default
session()->forget('key');              // Remove
session()->has('key');                 // Check existence
```

```blade
{{-- In Blade templates --}}
{{ session('key') }}
{{ session('key', 'default') }}
@if(session()->has('key'))
    {{ session('key') }}
@endif
```

### Database Query Patterns

```php
// Get all records
DB::table('tree')->get();

// Get with conditions
DB::table('tree')
    ->where('active', 1)
    ->whereNull('parent_id')
    ->get();

// Get single record
DB::table('tree')->where('id', $id)->first();

// Get with joins
DB::table('products as p')
    ->leftJoin('prices as pr', 'p.id', '=', 'pr.product_id')
    ->select('p.*', 'pr.price')
    ->get();

// Get with subquery
DB::table('products')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('authors')
              ->whereColumn('authors.product_id', 'products.id');
    })
    ->get();

// Count records
DB::table('products')->count();

// Pagination
DB::table('products')
    ->offset($offset)
    ->limit($limit)
    ->get();
```

### KnockoutJS Integration

```javascript
// Define ViewModel
function CounterViewModel() {
    var self = this;
    self.count = ko.observable(0);
    self.formattedCount = ko.computed(function() {
        return self.count() > 0 ? self.count() : '';
    });
    self.isVisible = ko.computed(function() {
        return self.count() > 0;
    });
}

// Apply bindings
ko.applyBindings(new CounterViewModel(), document.querySelector('.counter'));
```

```blade
{{-- In Blade template --}}
<span class="counter" data-bind="text: formattedCount, visible: isVisible"></span>
```

### Responsive Breakpoints

```css
/* Mobile: < 895px */
@media (max-width: 894px) {
    .mobile-bottom-nav {
        display: flex;
    }
    .desktop-only {
        display: none;
    }
}

/* Desktop: >= 895px */
@media (min-width: 895px) {
    .mobile-bottom-nav {
        display: none;
    }
    .desktop-only {
        display: block;
    }
}
```

## Summary

This design document provides a comprehensive guide for migrating core UI components from Smarty to Laravel Blade. The migration preserves all existing functionality, styling, and JavaScript integration while modernizing the codebase to use Laravel conventions.

Key aspects of the migration:

1. **File Structure**: Clear mapping from legacy modules to Laravel controllers and Blade components
2. **Template Syntax**: Direct conversion patterns from Smarty to Blade
3. **Controller Architecture**: Migration from legacy PHP classes to Laravel controllers with query builder
4. **Data Flow**: Transition from Smarty assignment to Laravel view data
5. **Session Management**: Migration from $_SESSION to Laravel session facade
6. **Route Mapping**: Conversion to Laravel named routes with route helpers
7. **JavaScript Preservation**: Maintaining all KnockoutJS bindings and event handlers
8. **CSS Preservation**: Keeping all CSS classes, IDs, and data attributes intact
9. **Error Handling**: Graceful degradation for all error scenarios
10. **Testing Strategy**: Comprehensive unit and property-based testing

The migration can be performed incrementally, with each component tested independently before deployment. All existing functionality will be preserved, ensuring a smooth transition for users and developers.
