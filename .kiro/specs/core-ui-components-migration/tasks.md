# Implementation Plan: Core UI Components Migration

## Overview

This plan outlines the migration of core UI components from the legacy Smarty template system to Laravel Blade templates. The migration follows a structured approach: controllers first, then Blade templates, then JavaScript integration, and finally testing. Each task builds incrementally to ensure functionality is validated early and often.

## Tasks

- [x] 1. Set up project structure and routes
  - Create directory structure for controllers and Blade components
  - Define all named routes in routes/web.php for navigation and API endpoints
  - Configure session driver in Laravel config
  - _Requirements: 12.1, 12.2, 13.1, 13.2, 13.3, 13.4_

- [x] 2. Create MenuController for catalog menu functionality
  - [x] 2.1 Implement MenuController with catalog menu methods
    - Create app/Http/Controllers/MenuController.php
    - Implement getCatalogMenu() method to load root categories from tree table
    - Implement getChildren() private method for recursive category loading
    - Implement getSubcategories() method for AJAX endpoint
    - Add caching for catalog menu data (1 hour TTL)
    - Use Laravel query builder for all database queries
    - Filter out inactive categories (active = 1)
    - _Requirements: 4.1, 4.2, 4.5, 4.6, 13.1, 13.5, 13.6, 14.1, 14.6, 14.7, 17.1, 17.2_

  - [ ]* 2.2 Write unit tests for MenuController
    - Test getRootCategories() returns only root categories
    - Test getChildren() builds recursive tree correctly
    - Test inactive categories are filtered out
    - Test empty results when no categories exist
    - _Requirements: 4.1, 4.2, 4.6_

  - [ ]* 2.3 Write property test for category hierarchy loading
    - **Property 4: Category Hierarchy Loading**
    - **Validates: Requirements 4.1, 4.2, 4.5, 4.6, 10.2**
    - Test that for any category tree, parent-child relationships are correctly represented recursively

- [x] 3. Create SearchController for search functionality
  - [x] 3.1 Implement SearchController with search methods
    - Create app/Http/Controllers/SearchController.php
    - Implement index() method for search results page
    - Implement autocomplete() method for AJAX autocomplete endpoint
    - Implement getProducts() private method with pagination (20 per page)
    - Implement getProductsCount() private method for total count
    - Implement getProductImageUrl() private method for product images
    - Add session storage for search query
    - Add support for author and age filters
    - Use Laravel query builder with LEFT JOIN for prices table
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 6.1, 6.2, 6.3, 6.4, 13.2, 13.5, 13.6, 14.3, 14.4, 14.5, 14.6, 14.7, 15.1, 15.2, 15.3, 17.3_

  - [ ]* 3.2 Write unit tests for SearchController
    - Test index() returns empty results for empty query
    - Test index() stores search query in session
    - Test index() clears session when query is empty
    - Test autocomplete() returns max 10 suggestions
    - Test autocomplete() returns empty for queries < 2 characters
    - Test getProducts() applies author filters correctly
    - Test getProducts() applies age filters correctly
    - Test pagination calculates correct page numbers
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.6, 6.2, 6.3_

  - [ ]* 3.3 Write property test for autocomplete suggestions
    - **Property 1: Autocomplete Suggestions**
    - **Validates: Requirements 1.3, 6.1, 6.3, 6.4**
    - Test that for any search input with 2+ characters, autocomplete returns matching suggestions limited to 10 results

  - [ ]* 3.4 Write property test for search product matching
    - **Property 6: Search Product Matching**
    - **Validates: Requirements 5.1, 5.2**
    - Test that for any search query, results match either product name or ID

  - [ ]* 3.5 Write property test for search pagination
    - **Property 7: Search Pagination**
    - **Validates: Requirements 5.3, 5.7**
    - Test that for any search results exceeding 20 products, pagination works correctly with 20 per page

  - [ ]* 3.6 Write property test for search session round-trip
    - **Property 8: Search Session Round-Trip**
    - **Validates: Requirements 5.4, 15.1, 15.2**
    - Test that for any search query, storing and retrieving from session returns the same value

  - [ ]* 3.7 Write property test for search filter application
    - **Property 10: Search Filter Application**
    - **Validates: Requirements 5.5**
    - Test that for any combination of author and age filters, results match all selected criteria

- [x] 4. Create SecondaryNavController for navigation menu
  - [x] 4.1 Implement SecondaryNavController with menu methods
    - Create app/Http/Controllers/SecondaryNavController.php
    - Implement getMenuItems() method to load pages from pages table
    - Implement buildMenuTree() private method for hierarchical structure
    - Filter only active pages (active = 1)
    - Use page title if present, otherwise fall back to page name
    - Use placeholder link (#) for pages without content
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7, 13.4, 13.5, 13.6, 14.2, 14.6, 14.7_

  - [ ]* 4.2 Write unit tests for SecondaryNavController
    - Test getMenuItems() returns only active pages
    - Test buildMenuTree() creates correct hierarchy
    - Test title fallback to name when title is empty
    - Test placeholder link for pages without content
    - _Requirements: 7.2, 7.3, 7.5, 7.7_

  - [ ]* 4.3 Write property test for secondary navigation active pages
    - **Property 13: Secondary Navigation Active Pages**
    - **Validates: Requirements 7.2**
    - Test that for any set of pages, only active pages are displayed

  - [ ]* 4.4 Write property test for secondary navigation hierarchy
    - **Property 14: Secondary Navigation Hierarchy**
    - **Validates: Requirements 7.3**
    - Test that for any page tree with parent-child relationships, hierarchy is correctly represented

- [x] 5. Checkpoint - Verify all controllers work correctly
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Create header Blade component
  - [x] 6.1 Create header.blade.php template
    - Create resources/views/components/header.blade.php
    - Migrate header structure from legacy/site/modules/sfera/tpl/header.tpl
    - Include logo with route helper for home link
    - Include mobile menu button with ID "mobileMenuButton"
    - Include catalog button with ID "catalogButton"
    - Include catalog dropdown with ID "catalogDropdown" and overlay with ID "catalogOverlay"
    - Include search bar with session('search_query') value
    - Include search suggestions div with ID "searchSuggestions"
    - Include header actions (login, orders, favorites, cart) with route helpers
    - Include cart counter with KnockoutJS data-bind attributes
    - Include favorites counter with KnockoutJS data-bind attributes
    - Include secondary navigation section
    - Preserve all CSS classes from legacy template
    - Preserve all HTML IDs from legacy template
    - Preserve all data attributes from legacy template
    - Add ARIA labels for navigation elements
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 8.1, 8.4, 8.7, 9.1, 9.4, 9.7, 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 12.1, 12.2, 12.3, 12.4, 18.1_

  - [ ]* 6.2 Write unit tests for header component rendering
    - Test header renders with all required elements
    - Test mobile menu button ID is preserved
    - Test search query from session is displayed
    - Test cart counter has KnockoutJS bindings
    - Test favorites counter has KnockoutJS bindings
    - _Requirements: 1.1, 1.4, 8.7, 9.7, 11.2, 11.5_

- [x] 7. Create catalog menu Blade component
  - [x] 7.1 Create catalog-menu.blade.php template
    - Create resources/views/components/catalog-menu.blade.php
    - Migrate catalog menu structure from legacy catalog_menu.tpl
    - Loop through categories with @foreach
    - Display category names with route helpers for links
    - Conditionally display expand arrows for categories with children
    - Render nested subcategories recursively
    - Add data-category-id attributes for JavaScript
    - Preserve all CSS classes from legacy template
    - Add ARIA attributes for expandable sections
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.7, 4.8, 11.1, 11.2, 11.3, 11.4, 11.6, 12.3, 18.4_

  - [ ]* 7.2 Write property test for category visual indicators
    - **Property 5: Category Visual Indicators**
    - **Validates: Requirements 4.3**
    - Test that for any category with children, visual cues (arrows) are displayed

- [x] 8. Create footer and mobile bottom navigation Blade components
  - [x] 8.1 Create footer.blade.php template
    - Create resources/views/components/footer.blade.php
    - Migrate footer structure from legacy/site/modules/sfera/tpl/footer.tpl
    - Include mobile-bottom-nav component at the top
    - Create three columns: company info, customer info, help section
    - Add social media links (VK, Odnoklassniki, Telegram) as external links
    - Add copyright with dynamic year using date('Y')
    - Use route helpers for all internal links
    - Preserve all CSS classes from legacy template (including obfuscated classes)
    - Add responsive styles for mobile (max-width: 894px)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 11.1, 11.6, 11.7_

  - [x] 8.2 Create mobile-bottom-nav.blade.php template
    - Create resources/views/components/mobile-bottom-nav.blade.php
    - Add navigation items: home, catalog, favorites, cart, profile
    - Add cart counter with KnockoutJS data-bind attributes
    - Add favorites counter with KnockoutJS data-bind attributes
    - Highlight active page using request()->routeIs()
    - Add responsive styles: display flex for max-width 894px, display none for min-width 895px
    - Preserve all CSS classes from legacy template
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 8.2, 8.4, 8.7, 9.2, 9.4, 9.7, 11.1, 11.5, 11.7_

  - [ ]* 8.3 Write unit tests for footer component
    - Test footer renders with all three columns
    - Test social media links are present
    - Test copyright displays current year
    - Test mobile bottom nav is included
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x] 9. Create mobile menu drawer Blade component
  - [x] 9.1 Create mobile-menu.blade.php template
    - Create resources/views/components/mobile-menu.blade.php
    - Create mobile menu overlay with ID "mobileMenuOverlay"
    - Create mobile menu nav with ID "mobileMenu"
    - Add menu header with logo and close button (ID "mobileMenuClose")
    - Add catalog section with category hierarchy
    - Add expandable category sections with expand icons
    - Add profile section with login, orders, favorites links
    - Use route helpers for all links
    - Preserve all CSS classes from legacy template
    - Add ARIA attributes for drawer state
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 11.1, 11.2, 11.3, 11.6, 12.1, 12.3, 18.3_

  - [ ]* 9.2 Write unit tests for mobile menu component
    - Test mobile menu renders with overlay and nav elements
    - Test close button ID is preserved
    - Test category hierarchy is displayed
    - Test profile links are present
    - _Requirements: 10.1, 10.2, 10.4, 11.2_

- [x] 10. Create secondary navigation Blade component
  - [x] 10.1 Create secondary-nav.blade.php template
    - Create resources/views/components/secondary-nav.blade.php
    - Loop through menu items with @foreach
    - Display menu item titles with route helpers for links
    - Render submenu items if present
    - Preserve all CSS classes from legacy template
    - _Requirements: 7.1, 7.4, 7.5, 7.6, 11.1, 12.6_

  - [ ]* 10.2 Write property test for secondary navigation title display
    - **Property 15: Secondary Navigation Title Display**
    - **Validates: Requirements 7.5**
    - Test that for any page, title is displayed if present, otherwise name is used

- [x] 11. Create search results Blade template
  - [x] 11.1 Create search index.blade.php template
    - Create resources/views/search/index.blade.php
    - Display search query from view data
    - Loop through products with @foreach
    - Display product cards with image, name, price, quantity
    - Add pagination controls with page numbers
    - Display "no results" message when products array is empty
    - Use route helpers for product links
    - Preserve CSS classes from legacy search template
    - _Requirements: 5.1, 5.3, 5.6, 5.7, 5.8, 11.1, 12.4_

  - [ ]* 11.2 Write unit tests for search template rendering
    - Test search template displays products correctly
    - Test "no results" message appears for empty results
    - Test pagination displays correct page numbers
    - Test search query is displayed in input
    - _Requirements: 5.6, 5.7_

- [x] 12. Checkpoint - Verify all Blade templates render correctly
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 13. Integrate JavaScript for interactive functionality
  - [ ] 13.1 Verify KnockoutJS cart counter integration
    - Verify cart-counter class elements have data-bind attributes
    - Verify cart counter appears in header and mobile bottom nav
    - Test that existing KnockoutJS ViewModel works with new templates
    - Verify counter updates when cart data changes
    - Verify counter is hidden when cart is empty
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.7, 11.5_

  - [ ] 13.2 Verify KnockoutJS favorites counter integration
    - Verify favorites-counter class elements have data-bind attributes
    - Verify favorites counter appears in header and mobile bottom nav
    - Test that existing KnockoutJS ViewModel works with new templates
    - Verify counter updates when favorites data changes
    - Verify counter is hidden when favorites is empty
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.7, 11.5_

  - [ ] 13.3 Verify mobile menu toggle functionality
    - Verify mobile menu button (ID "mobileMenuButton") triggers menu open
    - Verify mobile menu close button (ID "mobileMenuClose") triggers menu close
    - Verify mobile menu overlay (ID "mobileMenuOverlay") triggers menu close on click
    - Verify menu drawer slides in/out correctly
    - _Requirements: 10.1, 10.5, 10.6, 11.2, 11.3, 11.4_

  - [ ] 13.4 Verify catalog dropdown functionality
    - Verify catalog button (ID "catalogButton") triggers dropdown open
    - Verify catalog overlay (ID "catalogOverlay") triggers dropdown close on click
    - Verify dropdown displays catalog menu correctly
    - _Requirements: 1.2, 11.2, 11.3, 11.4_

  - [ ] 13.5 Verify search autocomplete functionality
    - Verify search input triggers autocomplete on typing
    - Verify autocomplete displays suggestions in div (ID "searchSuggestions")
    - Verify autocomplete debounces requests (300ms)
    - Verify clicking suggestion navigates to product page
    - Verify autocomplete fails silently on errors
    - _Requirements: 1.3, 6.1, 6.5, 6.6, 11.2, 11.3, 11.4, 17.4_

  - [ ]* 13.6 Write property test for counter real-time updates
    - **Property 2: Counter Real-Time Updates**
    - **Validates: Requirements 1.4, 3.3, 8.3, 8.5, 9.3, 9.5**
    - Test that for any change to cart or favorites data, counters update immediately using KnockoutJS

  - [ ]* 13.7 Write property test for autocomplete debouncing
    - **Property 11: Autocomplete Debouncing**
    - **Validates: Requirements 6.6, 17.4**
    - Test that for any rapid sequence of inputs, fewer API calls are made than input events

- [ ] 14. Write comprehensive property tests for template preservation
  - [ ]* 14.1 Write property test for CSS class preservation
    - **Property 16: CSS Class Preservation**
    - **Validates: Requirements 11.1**
    - Test that for any migrated template, all CSS classes from legacy are present in Blade

  - [ ]* 14.2 Write property test for HTML ID preservation
    - **Property 17: HTML ID Preservation**
    - **Validates: Requirements 11.2**
    - Test that for any migrated template, all HTML IDs from legacy are present in Blade

  - [ ]* 14.3 Write property test for data attribute preservation
    - **Property 18: Data Attribute Preservation**
    - **Validates: Requirements 11.3**
    - Test that for any migrated template, all data attributes are present in Blade

  - [ ]* 14.4 Write property test for HTML structure preservation
    - **Property 19: HTML Structure Preservation**
    - **Validates: Requirements 11.4**
    - Test that for any migrated template, HTML structure remains compatible with JavaScript selectors

  - [ ]* 14.5 Write property test for KnockoutJS binding preservation
    - **Property 20: KnockoutJS Binding Preservation**
    - **Validates: Requirements 11.5**
    - Test that for any migrated template, all KnockoutJS data-bind attributes are present

  - [ ]* 14.6 Write property test for SVG icon preservation
    - **Property 21: SVG Icon Preservation**
    - **Validates: Requirements 11.6**
    - Test that for any migrated template, all SVG icons and styling are present

- [ ] 15. Write property tests for Laravel conventions
  - [ ]* 15.1 Write property test for Laravel route usage
    - **Property 3: Laravel Route Usage**
    - **Validates: Requirements 1.7, 2.7, 4.7, 7.4, 12.1, 12.2, 12.3, 12.4, 12.5, 12.6**
    - Test that for any navigation link in Blade templates, route() or url() helpers are used

  - [ ]* 15.2 Write property test for query builder usage
    - **Property 23: Query Builder Usage**
    - **Validates: Requirements 13.6**
    - Test that for any database access in controllers, Laravel query builder or Eloquent is used

  - [ ]* 15.3 Write property test for view data return
    - **Property 24: View Data Return**
    - **Validates: Requirements 13.7**
    - Test that for any controller method rendering a view, view() helper is used

  - [ ]* 15.4 Write property test for table-specific queries
    - **Property 25: Table-Specific Queries**
    - **Validates: Requirements 14.1, 14.2, 14.3, 14.4, 14.5**
    - Test that for any query type, the correct table is targeted

  - [ ]* 15.5 Write property test for session data passing
    - **Property 27: Session Data Passing**
    - **Validates: Requirements 15.5**
    - Test that for any Blade template requiring session data, session() helper is used

- [ ] 16. Write integration tests for complete flows
  - [ ]* 16.1 Write integration test for search flow
    - Test complete search flow: submit query → session storage → results display → autocomplete → clear query
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 6.1, 15.1, 15.2, 15.3_

  - [ ]* 16.2 Write integration test for catalog menu flow
    - Test catalog menu: load categories → display hierarchy → click category → navigate to category page
    - _Requirements: 4.1, 4.2, 4.4, 4.7_

  - [ ]* 16.3 Write integration test for mobile menu flow
    - Test mobile menu: open drawer → display categories → expand category → navigate → close drawer
    - _Requirements: 10.1, 10.2, 10.3, 10.5, 10.6_

- [ ] 17. Add error handling and edge cases
  - [ ] 17.1 Add error handling to all controllers
    - Wrap database queries in try-catch blocks
    - Return empty collections on query failures
    - Log errors for debugging
    - Use default values for missing session data
    - Add fallback URLs for undefined routes
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5_

  - [ ]* 17.2 Write unit tests for error handling
    - Test database query failures return empty results
    - Test missing session data uses defaults
    - Test undefined routes use fallback URLs
    - Test empty search results display message
    - Test categories without children don't show expand indicators
    - Test pages without content use placeholder links
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.6_

- [ ] 18. Add accessibility features
  - [ ] 18.1 Add ARIA labels to header navigation
    - Add aria-label to catalog button
    - Add aria-label to search input
    - Add aria-label to user action links
    - _Requirements: 18.1, 18.2_

  - [ ] 18.2 Add ARIA attributes to expandable sections
    - Add aria-expanded to catalog menu items with children
    - Add aria-expanded to mobile menu categories with children
    - Add aria-controls to buttons that toggle sections
    - _Requirements: 18.3, 18.4_

  - [ ] 18.3 Add ARIA live regions for counters
    - Add aria-live="polite" to cart counter
    - Add aria-live="polite" to favorites counter
    - _Requirements: 18.5_

  - [ ]* 18.4 Write unit tests for accessibility features
    - Test ARIA labels are present on navigation elements
    - Test ARIA attributes are present on expandable sections
    - Test ARIA live regions are present on counters
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5_

- [ ] 19. Final checkpoint - Verify complete migration
  - Ensure all tests pass, ask the user if questions arise.

- [x] 20. Create main layout template
  - [x] 20.1 Create app.blade.php layout
    - Create resources/views/layouts/app.blade.php
    - Include header component
    - Add @yield('content') for page content
    - Include footer component
    - Include mobile menu component
    - Add meta tags and title
    - Link to CSS and JavaScript assets
    - _Requirements: 1.1, 2.1, 10.1_

  - [ ]* 20.2 Write unit test for layout rendering
    - Test layout includes header, footer, and mobile menu
    - Test layout yields content section
    - _Requirements: 1.1, 2.1, 10.1_

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Property tests validate universal correctness properties from the design document
- Unit tests validate specific examples and edge cases
- All CSS classes, HTML IDs, and data attributes must be preserved to maintain JavaScript compatibility
- All navigation links must use Laravel route helpers (route() or url())
- All database queries must use Laravel query builder or Eloquent
- KnockoutJS bindings must be preserved exactly as they are in legacy templates
