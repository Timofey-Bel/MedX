# Requirements Document

## Introduction

This document specifies requirements for migrating 7 legacy Smarty-based showcase page modules to Laravel Blade templates. The showcase (home) page currently displays TODO placeholders where these modules should render. Each module must be migrated while preserving exact HTML structure, CSS classes, JavaScript functionality, and responsive design behavior.

## Glossary

- **Legacy_Module**: A Smarty-based module located in `legacy/site/modules/sfera/[module_name]/` containing a PHP controller class and `.tpl` template file
- **Showcase_Page**: The main home page of the application rendered by ShowcaseController at route `/`
- **Migration_System**: The Laravel-based system that will replace Smarty template rendering with Blade components
- **Carousel_Module**: A module that displays rotating content (banners, products, or promotions) with navigation controls
- **Product_Display_Module**: A module that fetches and displays product information including images, prices, ratings, and availability
- **Blade_Component**: A reusable Laravel Blade component that encapsulates module rendering logic
- **KnockoutJS_Binding**: JavaScript data bindings used in legacy templates that must be preserved during migration
- **Responsive_Layout**: CSS layout that adapts to desktop and mobile screen sizes

## Requirements

### Requirement 1: Migrate Main Carousel Module

**User Story:** As a site visitor, I want to see the main banner carousel on the home page, so that I can view featured promotions and navigate through them.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the main carousel with banners from the database
2. THE Migration_System SHALL fetch banner data using Laravel Query Builder or Eloquent instead of legacy database functions
3. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `main_carousel.tpl`
4. THE Migration_System SHALL include carousel navigation buttons (previous/next) with SVG icons
5. THE Migration_System SHALL include carousel dot indicators for each banner
6. THE Migration_System SHALL load the existing `carousel.js` JavaScript file for carousel functionality
7. THE Migration_System SHALL load the existing `carousel.css` stylesheet
8. WHEN no banners exist in the database, THE Migration_System SHALL display a placeholder message
9. THE Migration_System SHALL render the carousel at 3/4 width of the container as per the original layout

### Requirement 2: Migrate Product Carousel Module

**User Story:** As a site visitor, I want to see product promotions in a side carousel, so that I can discover featured products.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the product carousel adjacent to the main carousel
2. THE Migration_System SHALL fetch product carousel data from the database using Laravel Query Builder or Eloquent
3. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `product_carousel.tpl`
4. THE Migration_System SHALL render the product carousel at 1/4 width of the container
5. THE Migration_System SHALL position the product carousel in the top slot of the side carousel container
6. THE Migration_System SHALL include carousel navigation controls
7. THE Migration_System SHALL reuse the existing `carousel.js` functionality with a unique carousel ID

### Requirement 3: Migrate Promo Carousel Module

**User Story:** As a site visitor, I want to see promotional content in a side carousel, so that I can learn about special offers.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the promo carousel below the product carousel
2. THE Migration_System SHALL fetch promo carousel data from the database using Laravel Query Builder or Eloquent
3. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `promo_carousel.tpl`
4. THE Migration_System SHALL render the promo carousel at 1/4 width of the container
5. THE Migration_System SHALL position the promo carousel in the bottom slot of the side carousel container
6. THE Migration_System SHALL include carousel navigation controls
7. THE Migration_System SHALL reuse the existing `carousel.js` functionality with a unique carousel ID

### Requirement 4: Migrate Popular Categories Module

**User Story:** As a site visitor, I want to see popular product categories displayed in a grid, so that I can quickly navigate to categories of interest.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the popular categories grid below the carousel section
2. THE Migration_System SHALL fetch category data from the `popular_categories` and `tree` tables using Laravel Query Builder or Eloquent
3. THE Migration_System SHALL join category data with the tree table to retrieve category names and GUIDs
4. THE Migration_System SHALL filter only active categories where `active = 1`
5. THE Migration_System SHALL sort categories by the `sort` field in ascending order
6. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `popular_categories.tpl`
7. THE Migration_System SHALL load the existing `categories.css` stylesheet
8. THE Migration_System SHALL display category images from the database
9. THE Migration_System SHALL create clickable links to category pages using category GUIDs

### Requirement 5: Migrate TOP-10 Products Module

**User Story:** As a site visitor, I want to see the top 10 bestselling products in a horizontal slider, so that I can discover popular items.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the TOP-10 products slider below the popular categories section
2. THE Migration_System SHALL fetch product data from `top10_products`, `products`, and `prices` tables using Laravel Query Builder or Eloquent
3. THE Migration_System SHALL join product data with prices where `price_type_id = '000000002'`
4. THE Migration_System SHALL filter only active products where `active = 1`
5. THE Migration_System SHALL sort products by the `sort` field in ascending order
6. THE Migration_System SHALL limit results to 10 products
7. THE Migration_System SHALL fetch product ratings and review counts from the `o_reviews` table via SKU lookup
8. THE Migration_System SHALL fetch product images using the `getProductImageUrl()` helper function
9. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `top10_products.tpl`
10. THE Migration_System SHALL load the existing `top10-slider.js` JavaScript file
11. THE Migration_System SHALL load the existing `top10-slider.css` stylesheet
12. THE Migration_System SHALL preserve KnockoutJS bindings for cart and favorites functionality
13. THE Migration_System SHALL pass cart and favorites session data to the template for product state display

### Requirement 6: Migrate Product Reviews Module

**User Story:** As a site visitor, I want to see recent product reviews on the home page, so that I can read customer feedback about products.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the product reviews section below the TOP-10 products slider
2. THE Migration_System SHALL fetch recent review data from the database using Laravel Query Builder or Eloquent
3. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `product_reviews.tpl`
4. THE Migration_System SHALL load the existing `product-reviews.css` stylesheet
5. THE Migration_System SHALL display reviewer names, ratings, review text, and review dates
6. THE Migration_System SHALL include product information associated with each review
7. THE Migration_System SHALL create clickable links to product pages from reviews

### Requirement 7: Migrate Random Products Module

**User Story:** As a site visitor, I want to see new or random products displayed in a grid, so that I can discover items I might be interested in.

#### Acceptance Criteria

1. WHEN the Showcase_Page loads, THE Migration_System SHALL render the random products grid below the product reviews section
2. THE Migration_System SHALL fetch product data from the database using Laravel Query Builder or Eloquent
3. THE Migration_System SHALL preserve the exact HTML structure and CSS classes from `random_products.tpl`
4. THE Migration_System SHALL load the existing `catalog.css` stylesheet
5. THE Migration_System SHALL display product images, names, prices, and availability
6. THE Migration_System SHALL fetch product ratings and review counts
7. THE Migration_System SHALL preserve KnockoutJS bindings for cart and favorites functionality
8. THE Migration_System SHALL pass cart and favorites session data to the template for product state display
9. THE Migration_System SHALL create clickable links to product detail pages

### Requirement 8: Preserve Responsive Design

**User Story:** As a mobile user, I want all showcase modules to display correctly on my device, so that I can browse the site comfortably.

#### Acceptance Criteria

1. WHEN the Showcase_Page is viewed on a mobile device, THE Migration_System SHALL render all modules with mobile-responsive layouts
2. THE Migration_System SHALL preserve all existing CSS media queries from legacy stylesheets
3. THE Migration_System SHALL maintain the mobile layout behavior for carousels, grids, and sliders
4. WHEN the viewport width changes, THE Migration_System SHALL adapt module layouts without breaking functionality

### Requirement 9: Preserve JavaScript Functionality

**User Story:** As a site visitor, I want all interactive elements to work correctly, so that I can navigate carousels and interact with products.

#### Acceptance Criteria

1. WHEN carousel navigation buttons are clicked, THE Migration_System SHALL advance or reverse the carousel slides
2. WHEN carousel dot indicators are clicked, THE Migration_System SHALL navigate to the corresponding slide
3. WHEN the TOP-10 slider navigation is used, THE Migration_System SHALL scroll through products smoothly
4. THE Migration_System SHALL preserve all KnockoutJS data bindings for cart and favorites interactions
5. THE Migration_System SHALL load all legacy JavaScript files in the correct order
6. WHEN JavaScript executes, THE Migration_System SHALL produce no console errors related to migrated modules

### Requirement 10: Maintain Code Documentation

**User Story:** As a developer, I want Russian comments explaining the migration, so that I can understand the relationship between legacy and new code.

#### Acceptance Criteria

1. THE Migration_System SHALL include Russian comments in controller methods explaining which legacy module is being replaced
2. THE Migration_System SHALL include Russian comments in Blade templates referencing the original Smarty template file
3. THE Migration_System SHALL document any changes to database queries with explanatory comments
4. THE Migration_System SHALL document any deviations from the original implementation with justification

### Requirement 11: Create Reusable Blade Components

**User Story:** As a developer, I want modular Blade components for showcase modules, so that I can reuse and maintain them easily.

#### Acceptance Criteria

1. WHERE a module has self-contained rendering logic, THE Migration_System SHALL create a Blade component
2. THE Migration_System SHALL pass data to Blade components via component properties
3. THE Migration_System SHALL organize Blade components in `resources/views/components/showcase/` directory
4. THE Migration_System SHALL use component naming that reflects the legacy module name for traceability

### Requirement 12: Implement Database Query Optimization

**User Story:** As a site administrator, I want the showcase page to load quickly, so that visitors have a good experience.

#### Acceptance Criteria

1. THE Migration_System SHALL use Laravel Query Builder or Eloquent for all database queries
2. THE Migration_System SHALL use eager loading to prevent N+1 query problems where applicable
3. THE Migration_System SHALL fetch all module data in the controller before rendering views
4. THE Migration_System SHALL avoid executing queries within Blade templates
5. WHEN multiple modules need similar data, THE Migration_System SHALL share query results to reduce database load

### Requirement 13: Handle Missing Data Gracefully

**User Story:** As a site visitor, I want the page to display correctly even when some data is missing, so that I don't see errors.

#### Acceptance Criteria

1. WHEN a module's database table is empty, THE Migration_System SHALL render the module section without errors
2. WHEN a module's database table is empty, THE Migration_System SHALL display an appropriate empty state or hide the section
3. WHEN product images are missing, THE Migration_System SHALL display a placeholder image
4. WHEN product prices are missing or zero, THE Migration_System SHALL handle the display gracefully
5. IF a database query fails, THEN THE Migration_System SHALL log the error and continue rendering other modules

### Requirement 14: Maintain Asset Loading

**User Story:** As a site visitor, I want all styles and scripts to load correctly, so that the page displays and functions properly.

#### Acceptance Criteria

1. THE Migration_System SHALL load all required CSS files using the `@push('styles')` directive
2. THE Migration_System SHALL load all required JavaScript files using the `@push('scripts')` directive
3. THE Migration_System SHALL reference existing legacy asset files in `assets/sfera/css/` and `assets/sfera/js/`
4. THE Migration_System SHALL maintain the correct loading order for CSS and JavaScript files
5. THE Migration_System SHALL use the `asset()` helper function for all asset URLs

### Requirement 15: Preserve Session Data Integration

**User Story:** As a logged-in user, I want my cart and favorites to display correctly in product modules, so that I can see which items I've already selected.

#### Acceptance Criteria

1. THE Migration_System SHALL pass cart session data to Product_Display_Module templates
2. THE Migration_System SHALL pass favorites session data to Product_Display_Module templates
3. THE Migration_System SHALL initialize empty cart and favorites arrays when session data is not present
4. THE Migration_System SHALL preserve the session data structure expected by KnockoutJS bindings
5. WHEN a user adds items to cart or favorites, THE Migration_System SHALL reflect these changes in the module display
