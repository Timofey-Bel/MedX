# Implementation Plan: Showcase Modules Migration

## Overview

This plan migrates 7 legacy Smarty-based showcase page modules to Laravel Blade templates. Each module preserves exact HTML structure, CSS classes, and JavaScript functionality while modernizing the data layer to use Laravel Query Builder/Eloquent. The migration includes Russian comments explaining relationships to legacy code.

## Tasks

- [x] 1. Set up controller foundation and session data handling
  - Update ShowcaseController to fetch and pass cart/favorites session data
  - Initialize empty arrays when session data is not present
  - Add error handling wrapper for database queries
  - Add Russian comments explaining legacy module replacement
  - _Requirements: 15.1, 15.2, 15.3, 15.4, 13.5, 10.1_

- [x] 2. Migrate main carousel module
  - [x] 2.1 Create database query for main carousel banners
    - Implement query in ShowcaseController using Laravel Query Builder
    - Filter by active = 1, order by sort ascending
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy main_carousel module
    - _Requirements: 1.1, 1.2, 10.1, 10.3, 13.5_
  
  - [x] 2.2 Create main-carousel Blade component
    - Create resources/views/components/showcase/main-carousel.blade.php
    - Preserve exact HTML structure from main_carousel.tpl
    - Include carousel navigation buttons with SVG icons
    - Include dot indicators matching banner count
    - Handle empty banner list with placeholder message
    - Add Russian comments referencing original Smarty template
    - _Requirements: 1.3, 1.4, 1.5, 1.8, 1.9, 10.2_
  
  - [x] 2.3 Add carousel assets to component
    - Use @push('styles') for carousel.css
    - Use @push('scripts') for carousel.js
    - Use asset() helper for all asset URLs
    - Ensure carousel ID is 'main' for JavaScript initialization
    - _Requirements: 1.6, 1.7, 14.1, 14.2, 14.3, 14.5_
  
  - [ ]* 2.4 Write property test for main carousel
    - **Property 1: Carousel Dot Indicators Match Banner Count**
    - **Validates: Requirements 1.5**
  
  - [ ]* 2.5 Write unit tests for main carousel
    - Test carousel renders with 3 banners
    - Test empty banner list displays placeholder
    - Test HTML structure preservation
    - Test navigation buttons present
    - Test asset loading
    - _Requirements: 1.3, 1.4, 1.6, 1.7, 1.8_

- [x] 3. Migrate product carousel module
  - [x] 3.1 Create database query for product carousel
    - Implement query in ShowcaseController using Laravel Query Builder
    - Join products with prices table (price_type_id = '000000002')
    - Filter by is_new = 1, random order, limit 3
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy product_carousel module
    - _Requirements: 2.1, 2.2, 10.1, 10.3, 13.5_
  
  - [x] 3.2 Create product-carousel Blade component
    - Create resources/views/components/showcase/product-carousel.blade.php
    - Preserve exact HTML structure from product_carousel.tpl
    - Include carousel navigation controls
    - Use unique carousel ID 'product' for JavaScript
    - Add Russian comments referencing original Smarty template
    - _Requirements: 2.3, 2.4, 2.5, 2.6, 2.7, 10.2_
  
  - [x] 3.3 Add carousel assets to component
    - Reuse carousel.js with unique carousel ID
    - Use @push directives for assets
    - _Requirements: 2.7, 14.1, 14.2_
  
  - [ ]* 3.4 Write unit tests for product carousel
    - Test carousel renders with product data
    - Test 1/4 width layout
    - Test unique carousel ID
    - _Requirements: 2.4, 2.7_

- [x] 4. Migrate promo carousel module
  - [x] 4.1 Create database query for promo carousel
    - Implement query in ShowcaseController using Laravel Query Builder
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy promo_carousel module
    - _Requirements: 3.1, 3.2, 10.1, 10.3, 13.5_
  
  - [x] 4.2 Create promo-carousel Blade component
    - Create resources/views/components/showcase/promo-carousel.blade.php
    - Preserve exact HTML structure from promo_carousel.tpl
    - Include carousel navigation controls
    - Use unique carousel ID 'promo' for JavaScript
    - Add Russian comments referencing original Smarty template
    - _Requirements: 3.3, 3.4, 3.5, 3.6, 3.7, 10.2_
  
  - [x] 4.3 Add carousel assets to component
    - Reuse carousel.js with unique carousel ID
    - Use @push directives for assets
    - _Requirements: 3.7, 14.1, 14.2_
  
  - [ ]* 4.4 Write property test for carousel uniqueness
    - **Property 2: Carousel IDs Are Unique**
    - **Validates: Requirements 2.7, 3.7**
  
  - [ ]* 4.5 Write unit tests for promo carousel
    - Test carousel renders with promo data
    - Test 1/4 width layout
    - Test unique carousel ID
    - _Requirements: 3.4, 3.7_

- [x] 5. Checkpoint - Verify carousel modules
  - Ensure all carousel tests pass, ask the user if questions arise.

- [x] 6. Migrate popular categories module
  - [x] 6.1 Create database query for popular categories
    - Implement query in ShowcaseController using Laravel Query Builder
    - Join popular_categories with tree table for names and GUIDs
    - Filter by active = 1, order by sort ascending
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy popular_categories module
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 10.1, 10.3, 13.5_
  
  - [x] 6.2 Create popular-categories Blade component
    - Create resources/views/components/showcase/popular-categories.blade.php
    - Preserve exact HTML structure from popular_categories.tpl
    - Display category images with fallback placeholders
    - Create clickable links using category GUIDs
    - Handle empty category list gracefully
    - Add Russian comments referencing original Smarty template
    - _Requirements: 4.6, 4.8, 4.9, 13.1, 10.2_
  
  - [x] 6.3 Add categories assets to component
    - Use @push('styles') for categories.css
    - Use asset() helper for all asset URLs
    - _Requirements: 4.7, 14.1, 14.5_
  
  - [ ]* 6.4 Write property tests for popular categories
    - **Property 3: Active Categories Filter**
    - **Property 4: Categories Sorted Ascending**
    - **Property 5: Category Images Rendered**
    - **Property 6: Category Links Contain GUIDs**
    - **Validates: Requirements 4.4, 4.5, 4.8, 4.9**
  
  - [ ]* 6.5 Write unit tests for popular categories
    - Test categories render with 5 categories
    - Test empty category list handling
    - Test HTML structure preservation
    - Test category images with placeholders
    - Test category links with GUIDs
    - _Requirements: 4.6, 4.8, 4.9, 13.1_

- [x] 7. Create ProductService for rating and image lookups
  - [x] 7.1 Create ProductService class
    - Create app/Services/ProductService.php
    - Implement getProductRating() method with SKU lookup
    - Query v_products_o_products for SKU, then o_reviews for rating
    - Implement getProductImageUrl() method delegating to helper
    - Add Russian comments explaining rating lookup logic
    - _Requirements: 5.7, 5.8, 10.3_
  
  - [ ]* 7.2 Write unit tests for ProductService
    - Test getProductRating() with valid product
    - Test getProductRating() with missing SKU
    - Test getProductImageUrl() returns correct URL
    - Test getProductImageUrl() returns placeholder for missing image
    - _Requirements: 5.7, 5.8, 13.3_

- [x] 8. Migrate TOP-10 products module
  - [x] 8.1 Create database query for TOP-10 products
    - Implement query in ShowcaseController using Laravel Query Builder
    - Join top10_products with products and prices tables
    - Use BINARY comparison for product_id joins
    - Filter by active = 1, order by sort ascending, limit 10
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy top10_products module
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 10.1, 10.3, 13.5_
  
  - [x] 8.2 Enrich TOP-10 products with ratings and images
    - Use ProductService to fetch ratings for each product
    - Use ProductService to fetch image URLs for each product
    - Add rating and reviews_count to product data
    - Optimize to prevent N+1 queries where possible
    - _Requirements: 5.7, 5.8, 12.2_
  
  - [x] 8.3 Create top10-slider Blade component
    - Create resources/views/components/showcase/top10-slider.blade.php
    - Preserve exact HTML structure from top10_products.tpl
    - Include slider navigation controls
    - Display product cards with favorite buttons
    - Display rating and review count
    - Include cart/quantity controls with KnockoutJS bindings
    - Pass cart and favorites session data to template
    - Handle empty product list gracefully
    - Add Russian comments referencing original Smarty template
    - _Requirements: 5.9, 5.12, 5.13, 13.1, 10.2_
  
  - [x] 8.4 Add TOP-10 assets to component
    - Use @push('styles') for top10-slider.css
    - Use @push('scripts') for top10-slider.js
    - Ensure correct loading order for KnockoutJS dependencies
    - Use asset() helper for all asset URLs
    - _Requirements: 5.10, 5.11, 14.1, 14.2, 14.4, 14.5_
  
  - [ ]* 8.5 Write property tests for TOP-10 products
    - **Property 7: Active Products Filter**
    - **Property 8: Products Sorted Ascending**
    - **Property 9: TOP-10 Product Count Limit**
    - **Property 10: Cart State Reflected in Product Display**
    - **Validates: Requirements 5.4, 5.5, 5.6, 5.13**
  
  - [ ]* 8.6 Write unit tests for TOP-10 products
    - Test slider renders with 10 products
    - Test empty product list handling
    - Test HTML structure preservation
    - Test KnockoutJS bindings preserved
    - Test cart state reflection
    - Test favorites state reflection
    - Test asset loading
    - _Requirements: 5.9, 5.10, 5.11, 5.12, 5.13, 13.1_

- [x] 9. Checkpoint - Verify TOP-10 module
  - Ensure all tests pass, ask the user if questions arise.

- [x] 10. Migrate product reviews module
  - [x] 10.1 Create database query for product reviews
    - Implement query in ShowcaseController using Laravel Query Builder
    - Filter by active = 1, order by sort ascending, limit 10
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy product_reviews module
    - _Requirements: 6.1, 6.2, 10.1, 10.3, 13.5_
  
  - [x] 10.2 Create product-reviews Blade component
    - Create resources/views/components/showcase/product-reviews.blade.php
    - Preserve exact HTML structure from product_reviews.tpl
    - Display reviewer names, ratings, review text, and dates
    - Include product information for each review
    - Create clickable links to product pages
    - Handle empty review list gracefully
    - Add Russian comments referencing original Smarty template
    - _Requirements: 6.3, 6.5, 6.6, 6.7, 13.1, 10.2_
  
  - [x] 10.3 Add product reviews assets to component
    - Use @push('styles') for product-reviews.css
    - Use asset() helper for all asset URLs
    - _Requirements: 6.4, 14.1, 14.5_
  
  - [ ]* 10.4 Write property tests for product reviews
    - **Property 11: Review Fields Present**
    - **Property 12: Review Product Links**
    - **Validates: Requirements 6.5, 6.6, 6.7**
  
  - [ ]* 10.5 Write unit tests for product reviews
    - Test reviews render with review data
    - Test empty review list handling
    - Test HTML structure preservation
    - Test review fields present
    - Test product links
    - _Requirements: 6.3, 6.5, 6.6, 6.7, 13.1_

- [x] 11. Migrate random products module
  - [x] 11.1 Create database query for random products
    - Implement query in ShowcaseController using Laravel Query Builder
    - Join products with prices table (price_type_id = '000000002')
    - Filter by is_new = 1, random order, limit 12
    - Add try-catch error handling with logging
    - Add Russian comments referencing legacy random_products module
    - _Requirements: 7.1, 7.2, 10.1, 10.3, 13.5_
  
  - [x] 11.2 Enrich random products with ratings
    - Use ProductService to fetch ratings for each product
    - Add rating and reviews_count to product data
    - Optimize to prevent N+1 queries where possible
    - _Requirements: 7.6, 12.2_
  
  - [x] 11.3 Create random-products Blade component
    - Create resources/views/components/showcase/random-products.blade.php
    - Preserve exact HTML structure from random_products.tpl
    - Display product images, names, prices, and availability
    - Include cart/favorites functionality with KnockoutJS bindings
    - Pass cart and favorites session data to template
    - Create clickable links to product detail pages
    - Handle empty product list gracefully
    - Handle missing images with placeholders
    - Handle missing/zero prices gracefully
    - Add Russian comments referencing original Smarty template
    - _Requirements: 7.3, 7.5, 7.7, 7.8, 7.9, 13.1, 13.3, 13.4, 10.2_
  
  - [x] 11.4 Add random products assets to component
    - Use @push('styles') for catalog.css
    - Ensure correct loading order for KnockoutJS dependencies
    - Use asset() helper for all asset URLs
    - _Requirements: 7.4, 14.1, 14.4, 14.5_
  
  - [ ]* 11.5 Write property tests for random products
    - **Property 13: Random Product Fields Present**
    - **Property 14: Favorites State Reflected in Product Display**
    - **Property 15: Product Detail Links**
    - **Validates: Requirements 7.5, 7.8, 7.9**
  
  - [ ]* 11.6 Write unit tests for random products
    - Test products render with product data
    - Test empty product list handling
    - Test HTML structure preservation
    - Test KnockoutJS bindings preserved
    - Test cart state reflection
    - Test favorites state reflection
    - Test missing image placeholder
    - Test missing price handling
    - _Requirements: 7.3, 7.5, 7.7, 7.8, 13.1, 13.3, 13.4_

- [x] 12. Checkpoint - Verify all product modules
  - Ensure all tests pass, ask the user if questions arise.

- [x] 13. Update showcase index view to use components
  - [x] 13.1 Replace TODO placeholders with component calls
    - Replace main carousel TODO with <x-showcase.main-carousel>
    - Replace product carousel TODO with <x-showcase.product-carousel>
    - Replace promo carousel TODO with <x-showcase.promo-carousel>
    - Replace popular categories TODO with <x-showcase.popular-categories>
    - Replace TOP-10 TODO with <x-showcase.top10-slider>
    - Replace product reviews TODO with <x-showcase.product-reviews>
    - Replace random products TODO with <x-showcase.random-products>
    - Pass appropriate data props to each component
    - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1_
  
  - [ ]* 13.2 Write integration test for showcase page
    - Test all modules render together
    - Test module positioning and layout
    - Test no console errors
    - _Requirements: 9.6_

- [ ] 14. Optimize database queries and performance
  - [ ] 14.1 Review and optimize query execution
    - Ensure all module data fetched in controller
    - Verify no queries executed in Blade templates
    - Implement eager loading where applicable
    - Share query results between modules where possible
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_
  
  - [ ]* 14.2 Write property test for query optimization
    - **Property 16: Query Count Optimization**
    - **Validates: Requirements 12.2, 12.5**

- [ ] 15. Implement responsive design verification
  - [ ] 15.1 Verify responsive layouts
    - Test carousel layouts on mobile viewport
    - Test category grid on mobile viewport
    - Test product grids on mobile viewport
    - Verify CSS media queries preserved
    - _Requirements: 8.1, 8.2, 8.3, 8.4_

- [ ] 16. Test error handling and graceful degradation
  - [ ]* 16.1 Write property test for module failure isolation
    - **Property 17: Module Failure Isolation**
    - **Validates: Requirements 13.5**
  
  - [ ]* 16.2 Write property tests for session data availability
    - **Property 18: Cart Session Data Available**
    - **Property 19: Favorites Session Data Available**
    - **Validates: Requirements 15.1, 15.2**
  
  - [ ]* 16.3 Write unit tests for error handling
    - Test database query failure logging
    - Test empty collection handling
    - Test missing session data initialization
    - _Requirements: 13.5, 15.3_

- [ ] 17. Final checkpoint - Complete migration verification
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties
- Unit tests validate specific examples and edge cases
- Russian comments required throughout for legacy code traceability
- All database queries use Laravel Query Builder or Eloquent
- KnockoutJS bindings must be preserved exactly for cart/favorites functionality
- Asset loading order is critical for JavaScript dependencies
