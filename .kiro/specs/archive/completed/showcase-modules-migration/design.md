# Design Document: Showcase Modules Migration

## Overview

This design document specifies the technical architecture for migrating 7 legacy Smarty-based showcase page modules to Laravel Blade templates. The migration preserves exact HTML structure, CSS classes, JavaScript functionality, and responsive design while modernizing the data layer to use Laravel Query Builder/Eloquent.

### Migration Scope

The following modules will be migrated from `legacy/site/modules/sfera/`:

1. **main_carousel** - Main banner carousel (3/4 width)
2. **product_carousel** - Product carousel (1/4 width, top slot)
3. **promo_carousel** - Promo carousel (1/4 width, bottom slot)
4. **popular_categories** - Category grid display
5. **top10_products** - TOP-10 products horizontal slider
6. **product_reviews** - Recent product reviews section
7. **random_products** - New/random products grid

### Design Goals

- **Preserve Legacy Behavior**: Maintain exact HTML structure, CSS classes, and JavaScript functionality
- **Modernize Data Layer**: Replace legacy `rows()` functions with Laravel Query Builder/Eloquent
- **Component Reusability**: Create modular Blade components for maintainability
- **Performance**: Optimize database queries and prevent N+1 problems
- **Documentation**: Include Russian comments explaining migration relationships

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     ShowcaseController                       │
│  - Orchestrates data fetching for all modules               │
│  - Passes data to view layer                                │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ├──> Service Layer (optional for complex logic)
                 │    - ProductService (ratings, images)
                 │    - CarouselService (banner data)
                 │
                 ├──> Database Layer
                 │    - Laravel Query Builder
                 │    - Eloquent Models (where appropriate)
                 │
                 └──> View Layer
                      ├── showcase/index.blade.php (main view)
                      └── components/showcase/
                          ├── main-carousel.blade.php
                          ├── product-carousel.blade.php
                          ├── promo-carousel.blade.php
                          ├── popular-categories.blade.php
                          ├── top10-slider.blade.php
                          ├── product-reviews.blade.php
                          └── random-products.blade.php
```

### Controller Architecture

The `ShowcaseController` will be responsible for:
- Fetching all module data in a single request
- Handling missing data gracefully
- Passing session data (cart, favorites) to views
- Keeping controller methods thin (delegate complex logic to services)

### Service Layer Architecture

Optional service classes for complex operations:
- **ProductService**: Handles product rating lookups, image URL generation
- **CarouselService**: Handles banner data fetching and formatting

### Component Architecture

Each module will be implemented as a Blade component:
- Self-contained rendering logic
- Receives data via component properties
- Preserves exact HTML structure from legacy `.tpl` files
- Located in `resources/views/components/showcase/`

## Components and Interfaces

### 1. ShowcaseController

**Location**: `app/Http/Controllers/ShowcaseController.php`

**Methods**:

```php
public function index(): View
```
- Fetches data for all 7 modules
- Handles database query failures gracefully
- Passes cart and favorites session data
- Returns showcase.index view

**Data Flow**:
```
Controller → Query Builder → Database
         ↓
    View with data
         ↓
    Blade Components
```

### 2. Blade Components

#### MainCarousel Component
**Location**: `resources/views/components/showcase/main-carousel.blade.php`

**Props**:
- `$banners` (array): Banner data with url, title, name
- `$carouselId` (string): Unique carousel identifier (default: 'main')

**HTML Structure**: Preserves `main_carousel.tpl` structure
- Carousel container with prev/next buttons
- Carousel track with slides
- Dot indicators for navigation

#### ProductCarousel Component
**Location**: `resources/views/components/showcase/product-carousel.blade.php`

**Props**:
- `$products` (array): Product data with id, name, image, price, link
- `$carouselId` (string): Unique carousel identifier (default: 'product')

**HTML Structure**: Preserves `product_carousel.tpl` structure

#### PromoCarousel Component
**Location**: `resources/views/components/showcase/promo-carousel.blade.php`

**Props**:
- `$promos` (array): Promo data with image, title, link
- `$carouselId` (string): Unique carousel identifier (default: 'promo')

**HTML Structure**: Preserves `promo_carousel.tpl` structure

#### PopularCategories Component
**Location**: `resources/views/components/showcase/popular-categories.blade.php`

**Props**:
- `$categories` (array): Category data with id, guid, title, image, sort

**HTML Structure**: Preserves `popular_categories.tpl` structure
- Section with container
- Grid layout for category cards
- Category images with fallback placeholders

#### Top10Slider Component
**Location**: `resources/views/components/showcase/top10-slider.blade.php`

**Props**:
- `$products` (array): Product data with id, name, price, rating, reviews_count, image_url, quantity
- `$cart` (array): Cart session data
- `$favorites` (array): Favorites session data

**HTML Structure**: Preserves `top10_products.tpl` structure
- Section with slider navigation
- Product cards with favorite buttons
- Rating and review display
- Cart/quantity controls with KnockoutJS bindings

#### ProductReviews Component
**Location**: `resources/views/components/showcase/product-reviews.blade.php`

**Props**:
- `$reviews` (array): Review data with id, name, title, image, content, parent_id

**HTML Structure**: Preserves `product_reviews.tpl` structure

#### RandomProducts Component
**Location**: `resources/views/components/showcase/random-products.blade.php`

**Props**:
- `$products` (array): Product data with id, name, description, image, price, quantity, rating, reviews_count
- `$cart` (array): Cart session data
- `$favorites` (array): Favorites session data

**HTML Structure**: Preserves `random_products.tpl` structure
- Product grid layout
- Product cards with cart/favorites functionality
- KnockoutJS bindings preserved

### 3. Service Classes (Optional)

#### ProductService
**Location**: `app/Services/ProductService.php`

**Methods**:

```php
public function getProductRating(string $productId): array
```
- Returns: `['rating' => float, 'reviews_count' => int]`
- Looks up SKU from `v_products_o_products`
- Queries `o_reviews` table for rating data

```php
public function getProductImageUrl(string $productId): string
```
- Returns product image URL or placeholder
- Delegates to existing `getProductImageUrl()` helper

### 4. Database Queries

#### Main Carousel Query
```php
DB::table('banners')
    ->where('active', 1)
    ->orderBy('sort', 'asc')
    ->get(['id', 'url', 'title', 'name']);
```

#### Product Carousel Query
```php
DB::table('products as p')
    ->leftJoin('prices as pr', function($join) {
        $join->on('p.id', '=', 'pr.product_id')
             ->where('pr.price_type_id', '=', '000000002');
    })
    ->where('p.is_new', 1)
    ->inRandomOrder()
    ->limit(3)
    ->get(['p.id', 'p.name', 'p.picture', 'pr.price as product_price']);
```

#### Popular Categories Query
```php
DB::table('popular_categories as pc')
    ->leftJoin('tree as t', 'pc.category_id', '=', 't.id')
    ->where('pc.active', 1)
    ->orderBy('pc.sort', 'asc')
    ->get(['pc.id', 'pc.category_id', 'pc.sort', 'pc.image', 't.name as title', 't.id as guid']);
```

#### TOP-10 Products Query
```php
DB::table('top10_products as t')
    ->leftJoin('products as p', DB::raw('BINARY t.product_id'), '=', DB::raw('BINARY p.id'))
    ->leftJoin('prices as pr', function($join) {
        $join->on('t.product_id', '=', 'pr.product_id')
             ->where('pr.price_type_id', '=', '000000002');
    })
    ->where('t.active', 1)
    ->orderBy('t.sort', 'asc')
    ->limit(10)
    ->get(['t.id', 't.product_id', 't.sort', 't.active', 'p.name as product_name', 'pr.price as product_price', 'p.quantity']);
```

#### Product Reviews Query
```php
DB::table('product_reviews')
    ->where('active', 1)
    ->orderBy('sort', 'asc')
    ->limit(10)
    ->get(['id', 'name', 'title', 'image', 'content', 'parent_id', 'sort', 'active']);
```

#### Random Products Query
```php
DB::table('products as p')
    ->leftJoin('prices as pr', function($join) {
        $join->on('p.id', '=', 'pr.product_id')
             ->where('pr.price_type_id', '=', '000000002');
    })
    ->where('p.is_new', 1)
    ->inRandomOrder()
    ->limit(12)
    ->get(['p.*', 'pr.price as product_price', 'p.quantity']);
```

#### Product Rating Query (via ProductService)
```php
// Step 1: Get SKU
$sku = DB::table('v_products_o_products')
    ->where('offer_id', $productId)
    ->value('sku');

// Step 2: Get rating data
DB::table('o_reviews')
    ->where('sku', $sku)
    ->whereNotNull('rating')
    ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_count')
    ->first();
```

## Data Models

### Banner Data Structure
```php
[
    'id' => string,
    'url' => string,        // Image URL
    'title' => string,      // Banner title
    'name' => string,       // Banner name
]
```

### Product Data Structure (Product Carousel)
```php
[
    'id' => string,
    'name' => string,
    'picture' => string,
    'product_price' => float,
    'link' => string,       // Generated: /product/{id}/
]
```

### Category Data Structure
```php
[
    'id' => string,
    'category_id' => string,
    'guid' => string,       // From tree table
    'title' => string,      // From tree table
    'image' => string,
    'sort' => int,
]
```

### Product Data Structure (TOP-10 and Random)
```php
[
    'id' => string,
    'product_id' => string,
    'product_name' => string,
    'product_price' => float,
    'quantity' => int,
    'rating' => float,
    'reviews_count' => int,
    'image_url' => string,
    'sort' => int,          // TOP-10 only
]
```

### Review Data Structure
```php
[
    'id' => string,
    'name' => string,
    'title' => string,
    'image' => string,
    'content' => string,
    'parent_id' => string,
    'sort' => int,
    'active' => int,
]
```

### Session Data Structures

#### Cart Session Data
```php
$_SESSION['cart'] = [
    'items' => [
        'product_id_1' => [
            'product_amount' => int,
            // ... other cart item data
        ],
        // ...
    ]
]
```

#### Favorites Session Data
```php
$_SESSION['favorites'] = [
    'items' => [
        'product_id_1' => [...],
        'product_id_2' => [...],
        // ...
    ]
]
```


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, I identified the following testable properties. During reflection, I consolidated redundant properties:

- **Carousel ID uniqueness** (2.7, 3.7): Combined into a single property that applies to all carousels
- **Active filtering** (4.4, 5.4): Both test the same pattern - filtering by active = 1
- **Sort order validation** (4.5, 5.5): Both test ascending sort order
- **Session data passing** (5.13, 7.8, 15.1, 15.2): Combined into properties about cart and favorites data availability
- **Product display fields** (6.5, 7.5): Both test that required fields are present in rendered output

### Property 1: Carousel Dot Indicators Match Banner Count

*For any* set of banners passed to the main carousel component, the number of rendered dot indicators should equal the number of banners.

**Validates: Requirements 1.5**

### Property 2: Carousel IDs Are Unique

*For any* showcase page render with multiple carousels (main, product, promo), all carousel IDs in the rendered HTML should be unique.

**Validates: Requirements 2.7, 3.7**

### Property 3: Active Categories Filter

*For any* set of categories fetched from the database for the popular categories module, all categories should have `active = 1`.

**Validates: Requirements 4.4**

### Property 4: Categories Sorted Ascending

*For any* set of categories rendered in the popular categories module, the `sort` field values should be in ascending order.

**Validates: Requirements 4.5**

### Property 5: Category Images Rendered

*For any* category with a non-empty `image` field, the rendered HTML should contain an `<img>` tag with that image URL.

**Validates: Requirements 4.8**

### Property 6: Category Links Contain GUIDs

*For any* category rendered in the popular categories module, the category link href should contain the category's GUID.

**Validates: Requirements 4.9**

### Property 7: Active Products Filter

*For any* set of products fetched from the database for the TOP-10 module, all products should have `active = 1`.

**Validates: Requirements 5.4**

### Property 8: Products Sorted Ascending

*For any* set of products rendered in the TOP-10 module, the `sort` field values should be in ascending order.

**Validates: Requirements 5.5**

### Property 9: TOP-10 Product Count Limit

*For any* set of products rendered in the TOP-10 module, the number of products should be at most 10.

**Validates: Requirements 5.6**

### Property 10: Cart State Reflected in Product Display

*For any* product that exists in the cart session data, the rendered HTML for that product should reflect the cart state (quantity controls visible, "add to cart" button hidden).

**Validates: Requirements 5.13**

### Property 11: Review Fields Present

*For any* review rendered in the product reviews module, the rendered HTML should contain the reviewer name, rating, review text, and review date fields.

**Validates: Requirements 6.5**

### Property 12: Review Product Links

*For any* review with an associated product (non-null parent_id), the rendered HTML should contain a clickable link to that product's page.

**Validates: Requirements 6.6, 6.7**

### Property 13: Random Product Fields Present

*For any* product rendered in the random products module, the rendered HTML should contain the product image, name, price, and availability fields.

**Validates: Requirements 7.5**

### Property 14: Favorites State Reflected in Product Display

*For any* product that exists in the favorites session data, the rendered HTML for that product should have the favorite button marked as active (favorite-filled class).

**Validates: Requirements 7.8**

### Property 15: Product Detail Links

*For any* product rendered in the random products module, the rendered HTML should contain a clickable link to that product's detail page.

**Validates: Requirements 7.9**

### Property 16: Query Count Optimization

*For any* showcase page render, the total number of database queries executed should not exceed N + M where N is the number of modules (7) and M is the number of product rating lookups (at most 22 for TOP-10 + random products).

**Validates: Requirements 12.2, 12.5**

### Property 17: Module Failure Isolation

*For any* showcase page render where one module's database query fails, the other modules should still render successfully without errors.

**Validates: Requirements 13.5**

### Property 18: Cart Session Data Available

*For any* product display module (TOP-10, random products), the cart session data should be available in the view context, either as actual cart data or as an empty array.

**Validates: Requirements 15.1**

### Property 19: Favorites Session Data Available

*For any* product display module (TOP-10, random products), the favorites session data should be available in the view context, either as actual favorites data or as an empty array.

**Validates: Requirements 15.2**

## Error Handling

### Database Query Failures

**Strategy**: Graceful degradation with logging

- Each module's data fetching should be wrapped in try-catch blocks
- On query failure, log the error with context (module name, query details)
- Return empty array for that module's data
- Continue rendering other modules
- Display empty state or hide the failed module section

**Implementation**:
```php
try {
    $banners = DB::table('banners')
        ->where('active', 1)
        ->orderBy('sort', 'asc')
        ->get();
} catch (\Exception $e) {
    Log::error('Failed to fetch main carousel banners', [
        'error' => $e->getMessage(),
        'module' => 'main_carousel'
    ]);
    $banners = collect(); // Empty collection
}
```

### Missing Data Handling

**Empty Collections**:
- When a module's query returns no results, pass empty collection to component
- Component should check for empty data and either:
  - Display an empty state message (for main carousel)
  - Hide the section entirely (for optional modules)

**Missing Product Images**:
- Use `onerror` attribute on `<img>` tags to fallback to placeholder
- Fallback image: `/assets/img/product_empty.jpg`

**Missing Product Prices**:
- Check if price is null or zero before rendering
- Display "Цена не указана" or hide price element

**Missing Session Data**:
- Initialize cart and favorites as empty arrays if not present in session
- Prevents undefined index errors in Blade templates

### Asset Loading Failures

**Strategy**: Fail gracefully with browser defaults

- If CSS fails to load, HTML structure remains functional
- If JavaScript fails to load, static content still displays
- Use `onerror` handlers on critical script tags if needed

## Testing Strategy

### Dual Testing Approach

This feature requires both unit tests and property-based tests for comprehensive coverage:

- **Unit tests**: Verify specific examples, edge cases, and error conditions
- **Property tests**: Verify universal properties across all inputs
- Both are complementary and necessary

### Unit Testing

Unit tests should focus on:

1. **Specific Examples**:
   - Test main carousel renders with 3 banners
   - Test popular categories renders with 5 categories
   - Test TOP-10 slider renders with exactly 10 products

2. **Edge Cases**:
   - Empty banner list displays placeholder (Requirement 1.8)
   - Empty module tables render without errors (Requirement 13.1)
   - Missing product images show placeholder (Requirement 13.3)
   - Missing/zero prices handled gracefully (Requirement 13.4)
   - Missing session data initializes empty arrays (Requirement 15.3)

3. **HTML Structure Verification**:
   - Main carousel has correct structure (Requirement 1.3)
   - Navigation buttons present (Requirement 1.4)
   - Popular categories structure preserved (Requirement 4.6)
   - TOP-10 structure preserved (Requirement 5.9)
   - Product reviews structure preserved (Requirement 6.3)
   - Random products structure preserved (Requirement 7.3)

4. **Asset Loading**:
   - carousel.js loaded (Requirement 1.6)
   - carousel.css loaded (Requirement 1.7)
   - categories.css loaded (Requirement 4.7)
   - top10-slider.js loaded (Requirement 5.10)
   - top10-slider.css loaded (Requirement 5.11)
   - product-reviews.css loaded (Requirement 6.4)
   - catalog.css loaded (Requirement 7.4)
   - KnockoutJS bindings preserved (Requirements 5.12, 7.7, 9.4)
   - Script loading order correct (Requirement 9.5)
   - Asset paths correct (Requirement 14.3)
   - CSS push directive used (Requirement 14.1)
   - Scripts push directive used (Requirement 14.2)
   - Asset loading order maintained (Requirement 14.4)

5. **Component Integration**:
   - Navigation controls present in carousels (Requirements 2.6, 3.6)
   - Session data structure matches KnockoutJS expectations (Requirement 15.4)

6. **Error Handling**:
   - Database query failures logged and handled gracefully

### Property-Based Testing

**Library**: Use **Pest PHP** with **pest-plugin-faker** for property-based testing in Laravel

**Configuration**: Each property test should run minimum 100 iterations

**Test Tagging**: Each test must reference its design document property:
```php
// Feature: showcase-modules-migration, Property 1: Carousel Dot Indicators Match Banner Count
```

**Property Tests**:

1. **Property 1 - Carousel Dot Indicators Match Banner Count**:
   - Generate random number of banners (1-20)
   - Render main carousel component
   - Count dot indicators in HTML
   - Assert: dot count equals banner count

2. **Property 2 - Carousel IDs Are Unique**:
   - Generate random carousel data for all three carousels
   - Render showcase page
   - Extract all carousel IDs from HTML
   - Assert: all IDs are unique

3. **Property 3 - Active Categories Filter**:
   - Generate random categories with mixed active values
   - Fetch categories through controller method
   - Assert: all returned categories have active = 1

4. **Property 4 - Categories Sorted Ascending**:
   - Generate random categories with random sort values
   - Fetch categories through controller method
   - Assert: sort values are in ascending order

5. **Property 5 - Category Images Rendered**:
   - Generate random categories with image URLs
   - Render popular categories component
   - For each category with image, assert: HTML contains that image URL

6. **Property 6 - Category Links Contain GUIDs**:
   - Generate random categories with GUIDs
   - Render popular categories component
   - For each category, assert: link href contains the GUID

7. **Property 7 - Active Products Filter**:
   - Generate random products with mixed active values
   - Fetch products through controller method
   - Assert: all returned products have active = 1

8. **Property 8 - Products Sorted Ascending**:
   - Generate random products with random sort values
   - Fetch products through controller method
   - Assert: sort values are in ascending order

9. **Property 9 - TOP-10 Product Count Limit**:
   - Generate random number of products (0-50)
   - Fetch TOP-10 products through controller method
   - Assert: returned count is at most 10

10. **Property 10 - Cart State Reflected**:
    - Generate random products and random cart session data
    - Render TOP-10 component with cart data
    - For each product in cart, assert: quantity controls visible in HTML

11. **Property 11 - Review Fields Present**:
    - Generate random reviews with all required fields
    - Render product reviews component
    - For each review, assert: HTML contains name, rating, text, date

12. **Property 12 - Review Product Links**:
    - Generate random reviews with parent_id values
    - Render product reviews component
    - For each review with parent_id, assert: HTML contains product link

13. **Property 13 - Random Product Fields Present**:
    - Generate random products with all required fields
    - Render random products component
    - For each product, assert: HTML contains image, name, price, availability

14. **Property 14 - Favorites State Reflected**:
    - Generate random products and random favorites session data
    - Render random products component with favorites data
    - For each product in favorites, assert: favorite button has favorite-filled class

15. **Property 15 - Product Detail Links**:
    - Generate random products
    - Render random products component
    - For each product, assert: HTML contains link to /product/{id}/

16. **Property 16 - Query Count Optimization**:
    - Generate random data for all modules
    - Enable query logging
    - Render showcase page
    - Count total queries
    - Assert: query count <= 7 + (number of products needing ratings)

17. **Property 17 - Module Failure Isolation**:
    - Mock database to fail for one random module
    - Render showcase page
    - Assert: other modules render successfully
    - Assert: error is logged

18. **Property 18 - Cart Session Data Available**:
    - Generate random cart session data (including null/empty cases)
    - Render product display modules
    - Assert: cart variable exists in view context
    - Assert: cart is array (empty or populated)

19. **Property 19 - Favorites Session Data Available**:
    - Generate random favorites session data (including null/empty cases)
    - Render product display modules
    - Assert: favorites variable exists in view context
    - Assert: favorites is array (empty or populated)

### Test Organization

```
tests/
├── Unit/
│   ├── Controllers/
│   │   └── ShowcaseControllerTest.php
│   └── Components/
│       ├── MainCarouselTest.php
│       ├── ProductCarouselTest.php
│       ├── PromoCarouselTest.php
│       ├── PopularCategoriesTest.php
│       ├── Top10SliderTest.php
│       ├── ProductReviewsTest.php
│       └── RandomProductsTest.php
└── Property/
    └── ShowcaseModulesPropertyTest.php
```

### Manual Testing Checklist

Since some requirements cannot be automated (responsive design, JavaScript interactions), manual testing is required:

- [ ] Responsive layouts work on mobile devices (Requirement 8.1, 8.3, 8.4)
- [ ] Carousel navigation buttons work (Requirement 9.1)
- [ ] Carousel dot indicators work (Requirement 9.2)
- [ ] TOP-10 slider navigation works (Requirement 9.3)
- [ ] No JavaScript console errors (Requirement 9.6)
- [ ] Carousel renders at 3/4 width (Requirement 1.9)
- [ ] Product carousel at 1/4 width (Requirement 2.4)
- [ ] Product carousel in top slot (Requirement 2.5)
- [ ] Promo carousel at 1/4 width (Requirement 3.4)
- [ ] Promo carousel in bottom slot (Requirement 3.5)
- [ ] Popular categories below carousels (Requirement 4.1)
- [ ] TOP-10 below categories (Requirement 5.1)
- [ ] Reviews below TOP-10 (Requirement 6.1)
- [ ] Random products below reviews (Requirement 7.1)
- [ ] Product carousel adjacent to main carousel (Requirement 2.1)
- [ ] Promo carousel below product carousel (Requirement 3.1)

### Code Review Checklist

Some requirements are verified through code review:

- [ ] Laravel Query Builder/Eloquent used (Requirements 1.2, 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 12.1)
- [ ] Russian comments in controllers (Requirement 10.1)
- [ ] Russian comments in Blade templates (Requirement 10.2)
- [ ] Database query comments (Requirement 10.3)
- [ ] Deviation documentation (Requirement 10.4)
- [ ] Blade components created (Requirement 11.1)
- [ ] Component properties used (Requirement 11.2)
- [ ] Components in correct directory (Requirement 11.3)
- [ ] Component naming reflects legacy modules (Requirement 11.4)
- [ ] Eager loading used (Requirement 12.2)
- [ ] Data fetched in controller (Requirement 12.3)
- [ ] No queries in templates (Requirement 12.4)
- [ ] asset() helper used (Requirement 14.5)
- [ ] Price type join correct (Requirement 5.3)
- [ ] Category tree join correct (Requirement 4.3)
- [ ] Rating lookup via SKU (Requirement 5.7)
- [ ] getProductImageUrl() used (Requirement 5.8)
- [ ] Rating fetch implementation (Requirement 7.6)

