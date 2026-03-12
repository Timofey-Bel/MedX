# Requirements Document

## Introduction

This document specifies the requirements for migrating core UI components from the legacy Smarty template system to Laravel Blade templates. The migration includes header, footer, search functionality, mobile menu, catalog menu, secondary navigation, and cart/favorites counters. The goal is to modernize the template system while preserving all existing functionality, styling, and user experience.

## Glossary

- **Legacy_System**: The existing Smarty-based template system located in `legacy/site/modules/sfera/`
- **Blade_System**: The target Laravel Blade template system in `resources/views/sfera/`
- **Header_Component**: Main navigation bar with logo, catalog button, search bar, and user actions
- **Footer_Component**: Site footer with company information, links, and social media
- **Catalog_Menu**: Hierarchical category dropdown menu from the `tree` table
- **Search_Module**: Search functionality with autocomplete and results page
- **Mobile_Menu**: Mobile navigation drawer for small screens
- **Secondary_Nav**: Additional navigation menu from the `pages` table
- **Counter_Component**: Real-time cart and favorites item counters using KnockoutJS
- **Template_Migration**: The process of converting Smarty templates to Blade templates
- **Controller_Migration**: The process of converting legacy PHP classes to Laravel controllers

## Requirements

### Requirement 1: Header Component Migration

**User Story:** As a site visitor, I want to see the main navigation header with all functionality preserved, so that I can navigate the site and access my account features.

#### Acceptance Criteria

1. THE Blade_System SHALL render a header component with logo, catalog button, search bar, and user action links
2. WHEN a user clicks the catalog button, THE Catalog_Menu SHALL display with category hierarchy
3. WHEN a user types in the search bar, THE Search_Module SHALL provide autocomplete suggestions
4. THE Header_Component SHALL display cart and favorites counters with real-time updates
5. THE Header_Component SHALL be responsive and adapt to mobile screen sizes
6. THE Header_Component SHALL preserve all existing CSS classes and styling
7. THE Header_Component SHALL use Laravel route helpers for all navigation links

### Requirement 2: Footer Component Migration

**User Story:** As a site visitor, I want to see the footer with company information and links, so that I can access important pages and social media.

#### Acceptance Criteria

1. THE Blade_System SHALL render a footer component with company information, link sections, and social media icons
2. THE Footer_Component SHALL display three columns of links: company info, customer info, and help section
3. THE Footer_Component SHALL include social media links for VK, Odnoklassniki, and Telegram
4. THE Footer_Component SHALL display copyright information
5. THE Footer_Component SHALL preserve all existing CSS classes and styling
6. THE Footer_Component SHALL be responsive and adapt to mobile screen sizes
7. THE Footer_Component SHALL use Laravel route helpers for all internal links

### Requirement 3: Mobile Bottom Navigation Migration

**User Story:** As a mobile user, I want to see a bottom navigation bar, so that I can quickly access main sections of the site.

#### Acceptance Criteria

1. WHEN the viewport width is less than 895px, THE Blade_System SHALL display a mobile bottom navigation bar
2. THE Mobile_Menu SHALL include links to home, catalog, favorites, cart, and profile
3. THE Mobile_Menu SHALL display cart and favorites counters with real-time updates
4. THE Mobile_Menu SHALL highlight the active page
5. THE Mobile_Menu SHALL preserve all existing CSS classes and styling
6. WHEN the viewport width is 895px or greater, THE Mobile_Menu SHALL be hidden

### Requirement 4: Catalog Menu Migration

**User Story:** As a site visitor, I want to browse product categories through a dropdown menu, so that I can find products by category.

#### Acceptance Criteria

1. THE Catalog_Menu SHALL load category hierarchy from the `tree` table
2. THE Catalog_Menu SHALL display root categories with their children
3. WHEN a category has children, THE Catalog_Menu SHALL indicate this with visual cues
4. WHEN a user hovers over a category with children, THE Catalog_Menu SHALL display subcategories
5. THE Catalog_Menu SHALL support recursive category nesting
6. THE Catalog_Menu SHALL filter out inactive categories
7. THE Catalog_Menu SHALL generate category links using Laravel routes
8. THE Catalog_Menu SHALL preserve all existing CSS classes and styling

### Requirement 5: Search Functionality Migration

**User Story:** As a site visitor, I want to search for products, so that I can quickly find what I'm looking for.

#### Acceptance Criteria

1. WHEN a user submits a search query, THE Search_Module SHALL display matching products
2. THE Search_Module SHALL search by product name and ID
3. THE Search_Module SHALL support pagination with 20 products per page
4. THE Search_Module SHALL preserve the search query in the session
5. THE Search_Module SHALL support filtering by author and age
6. WHEN the search query is empty, THE Search_Module SHALL display no products
7. THE Search_Module SHALL display total result count and page numbers
8. THE Search_Module SHALL use the same template layout as the catalog page

### Requirement 6: Search Autocomplete

**User Story:** As a site visitor, I want to see search suggestions as I type, so that I can find products faster.

#### Acceptance Criteria

1. WHEN a user types in the search input, THE Search_Module SHALL provide autocomplete suggestions
2. THE Search_Module SHALL return suggestions after a minimum of 2 characters
3. THE Search_Module SHALL limit suggestions to 10 results
4. THE Search_Module SHALL display product names and images in suggestions
5. WHEN a user clicks a suggestion, THE Search_Module SHALL navigate to the product page
6. THE Search_Module SHALL debounce autocomplete requests to avoid excessive API calls

### Requirement 7: Secondary Navigation Migration

**User Story:** As a site visitor, I want to see additional navigation links, so that I can access important pages like events, vacancies, and support.

#### Acceptance Criteria

1. THE Secondary_Nav SHALL load menu items from the `pages` table
2. THE Secondary_Nav SHALL display only active pages
3. THE Secondary_Nav SHALL support hierarchical menu structure with parent-child relationships
4. THE Secondary_Nav SHALL generate page links using Laravel routes
5. THE Secondary_Nav SHALL display page titles or names
6. THE Secondary_Nav SHALL preserve all existing CSS classes and styling
7. WHEN a page has no content, THE Secondary_Nav SHALL use a placeholder link

### Requirement 8: Cart Counter Integration

**User Story:** As a site visitor, I want to see the number of items in my cart, so that I know how many products I've added.

#### Acceptance Criteria

1. THE Counter_Component SHALL display the cart item count in the header
2. THE Counter_Component SHALL display the cart item count in the mobile bottom navigation
3. THE Counter_Component SHALL use KnockoutJS for real-time updates
4. WHEN the cart is empty, THE Counter_Component SHALL hide the cart counter badge
5. WHEN items are added to the cart, THE Counter_Component SHALL update immediately
6. THE Counter_Component SHALL fetch cart data from the `/api/cart` endpoint
7. THE Counter_Component SHALL preserve all existing KnockoutJS bindings and CSS classes

### Requirement 9: Favorites Counter Integration

**User Story:** As a site visitor, I want to see the number of items in my favorites, so that I know how many products I've saved.

#### Acceptance Criteria

1. THE Counter_Component SHALL display the favorites item count in the header
2. THE Counter_Component SHALL display the favorites item count in the mobile bottom navigation
3. THE Counter_Component SHALL use KnockoutJS for real-time updates
4. WHEN favorites is empty, THE Counter_Component SHALL hide the favorites counter badge
5. WHEN items are added to favorites, THE Counter_Component SHALL update immediately
6. THE Counter_Component SHALL fetch favorites data from the `/api/favorites` endpoint
7. THE Counter_Component SHALL preserve all existing KnockoutJS bindings and CSS classes

### Requirement 10: Mobile Menu Drawer Migration

**User Story:** As a mobile user, I want to open a navigation drawer, so that I can access the full site menu.

#### Acceptance Criteria

1. WHEN a user clicks the mobile menu button, THE Mobile_Menu SHALL open a navigation drawer
2. THE Mobile_Menu SHALL display category hierarchy from the `tree` table
3. THE Mobile_Menu SHALL support expanding and collapsing category sections
4. THE Mobile_Menu SHALL include user account links
5. THE Mobile_Menu SHALL overlay the page content when open
6. WHEN a user clicks outside the drawer, THE Mobile_Menu SHALL close
7. THE Mobile_Menu SHALL preserve all existing CSS classes and styling

### Requirement 11: CSS and JavaScript Preservation

**User Story:** As a developer, I want all existing styles and scripts to work with the new templates, so that the migration doesn't break the user interface.

#### Acceptance Criteria

1. THE Template_Migration SHALL preserve all CSS class names from legacy templates
2. THE Template_Migration SHALL preserve all HTML element IDs from legacy templates
3. THE Template_Migration SHALL preserve all data attributes used by JavaScript
4. THE Template_Migration SHALL maintain the same HTML structure for JavaScript selectors
5. THE Template_Migration SHALL preserve all KnockoutJS data-bind attributes
6. THE Template_Migration SHALL preserve all SVG icons and their styling
7. THE Template_Migration SHALL maintain responsive breakpoints at 894px and 895px

### Requirement 12: Laravel Route Integration

**User Story:** As a developer, I want all links to use Laravel routes, so that the application uses consistent URL generation.

#### Acceptance Criteria

1. THE Blade_System SHALL use `route()` helper for all named routes
2. THE Blade_System SHALL use `url()` helper for static URLs
3. THE Blade_System SHALL generate category links using the category route with ID parameter
4. THE Blade_System SHALL generate product links using the product route with ID parameter
5. THE Blade_System SHALL generate search links with query parameters
6. THE Blade_System SHALL generate page links using the page route with ID parameter
7. WHEN a route is not defined, THE Blade_System SHALL use a placeholder URL

### Requirement 13: Controller Migration

**User Story:** As a developer, I want legacy PHP classes converted to Laravel controllers, so that the application follows Laravel conventions.

#### Acceptance Criteria

1. THE Controller_Migration SHALL create a MenuController for menu functionality
2. THE Controller_Migration SHALL create a SearchController for search functionality
3. THE Controller_Migration SHALL create a CatalogMenuController for catalog menu functionality
4. THE Controller_Migration SHALL create a SecondaryNavController for secondary navigation
5. THE Controller_Migration SHALL preserve all database queries from legacy classes
6. THE Controller_Migration SHALL use Laravel query builder or Eloquent for database access
7. THE Controller_Migration SHALL return view data using Laravel's view system

### Requirement 14: Database Query Compatibility

**User Story:** As a developer, I want database queries to work with the existing schema, so that no database changes are required.

#### Acceptance Criteria

1. THE Controller_Migration SHALL query the `tree` table for category data
2. THE Controller_Migration SHALL query the `pages` table for navigation data
3. THE Controller_Migration SHALL query the `products` table for search results
4. THE Controller_Migration SHALL query the `authors` table for author filters
5. THE Controller_Migration SHALL query the `ages` table for age filters
6. THE Controller_Migration SHALL preserve all JOIN operations from legacy queries
7. THE Controller_Migration SHALL preserve all WHERE conditions from legacy queries

### Requirement 15: Session and State Management

**User Story:** As a site visitor, I want my search queries and preferences preserved, so that I have a consistent experience.

#### Acceptance Criteria

1. THE Search_Module SHALL store the current search query in the session
2. THE Search_Module SHALL retrieve the search query from the session when returning to search
3. WHEN the search query is cleared, THE Search_Module SHALL remove it from the session
4. THE Counter_Component SHALL use session data for cart and favorites counts
5. THE Blade_System SHALL pass session data to templates using Laravel's session system

### Requirement 16: Error Handling and Edge Cases

**User Story:** As a developer, I want the system to handle errors gracefully, so that users don't see broken pages.

#### Acceptance Criteria

1. WHEN a database query fails, THE Blade_System SHALL return an empty array instead of throwing an error
2. WHEN a category has no children, THE Catalog_Menu SHALL display the category without expand indicators
3. WHEN a search returns no results, THE Search_Module SHALL display a "no results" message
4. WHEN autocomplete fails, THE Search_Module SHALL fail silently without breaking the search input
5. WHEN a route is not found, THE Blade_System SHALL use a fallback URL
6. WHEN session data is missing, THE Blade_System SHALL use default values

### Requirement 17: Performance Optimization

**User Story:** As a site visitor, I want pages to load quickly, so that I have a smooth browsing experience.

#### Acceptance Criteria

1. THE Catalog_Menu SHALL load only root categories initially
2. THE Catalog_Menu SHALL load subcategories on demand via AJAX
3. THE Search_Module SHALL limit autocomplete results to 10 items
4. THE Search_Module SHALL debounce autocomplete requests by 300ms
5. THE Counter_Component SHALL cache API responses for 5 seconds
6. THE Blade_System SHALL use lazy loading for images in the footer
7. THE Blade_System SHALL minimize database queries by using eager loading

### Requirement 18: Accessibility Compliance

**User Story:** As a user with accessibility needs, I want the interface to be accessible, so that I can use the site effectively.

#### Acceptance Criteria

1. THE Header_Component SHALL include appropriate ARIA labels for navigation elements
2. THE Search_Module SHALL include ARIA labels for the search input and button
3. THE Mobile_Menu SHALL include ARIA attributes for the drawer state
4. THE Catalog_Menu SHALL include ARIA attributes for expandable sections
5. THE Counter_Component SHALL include ARIA live regions for counter updates
6. THE Footer_Component SHALL use semantic HTML elements
7. THE Blade_System SHALL ensure all interactive elements are keyboard accessible
