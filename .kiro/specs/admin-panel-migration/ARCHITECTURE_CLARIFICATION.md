# Architecture Clarification: Admin Panel Migration

## Date: 2026-02-28

## Critical Correction

The initial spec incorrectly proposed replacing ExtJS with Vue.js/React/Filament. This document clarifies the ACTUAL architecture based on the existing legacy implementation.

## Actual Architecture

### What STAYS (ExtJS Desktop Shell)

The legacy system (`legacy/site/modules/admin/desktop/desktop.tpl`) has a **FULLY IMPLEMENTED** Windows 10 Desktop Interface using ExtJS 4.2.1:

1. **Desktop Area**
   - Full-screen workspace with Windows 10 wallpaper
   - Desktop shortcuts with Material Icons
   - Publisher logo watermark ("СФЕРА / Творческий Центр")

2. **Taskbar** (43px height, #004172 color)
   - Start button (Material Icons "apps")
   - Open windows container
   - System tray (notifications, user menu)
   - Clock (updates every second, shows time + date)

3. **Start Menu** (Three-panel Windows 10 style)
   - Left panel: User icons (menu, person, settings, power)
   - Center panel: Scrollable app list with categories
   - Right panel: Quick access tiles (4x grid, 90px tiles)

4. **Window Management**
   - ExtJS Ext.window.Window components
   - Draggable, resizable, minimizable, maximizable
   - Windows 10 styling (white header, 36px height)
   - Taskbar buttons for open windows

5. **Desktop Shortcuts**
   - Material Icons (48px, colored)
   - Click handlers (JavaScript functions)
   - Hover effects

**Migration Action:** Update API endpoints and module paths ONLY. Keep all UI unchanged.

### What CHANGES (Window Content)

Window content moves from Smarty templates to Laravel Blade templates:

**BEFORE (Legacy):**
```javascript
function openProducts() {
    var win = Ext.create('Ext.window.Window', {
        title: 'Управление товарами',
        width: 1200,
        height: 800,
        html: '<iframe src="/site/modules/admin/products/index.php" frameborder="0"></iframe>'
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
        html: '<iframe src="/admin/products" frameborder="0"></iframe>'
    });
    win.show();
}
```

**Inside iframe:**
- Laravel controller returns Blade view
- HTML/CSS/jQuery
- NO Vue.js, NO React, NO Filament
- Same patterns as public site (Blade + vanilla JS)

## Technology Stack

### Frontend (Desktop Shell) - UNCHANGED
- ExtJS 4.2.1
- Material Icons
- Custom CSS (Windows 10 styling)

### Frontend (Window Content) - NEW
- Laravel Blade templates
- HTML/CSS
- jQuery
- Iframe isolation

### Backend - NEW
- Laravel 12
- PHP 8.5
- MySQL 10.4+
- Laravel Gates/Policies for ACL

## Why This Architecture?

1. **Developers already know ExtJS** - No training needed
2. **Desktop UI is fully implemented** - No need to rebuild
3. **Iframe isolation** - Clean separation between shell and content
4. **Gradual migration** - Can migrate modules one by one
5. **Consistent with public site** - Same Blade + vanilla JS patterns

## What Was Removed from Spec

- ❌ Filament 3.x
- ❌ Livewire 3.x
- ❌ Alpine.js
- ❌ TailwindCSS (for admin panel)
- ❌ Vue.js
- ❌ React
- ❌ Complete UI rebuild

## What Was Added to Spec

- ✅ ExtJS Desktop documentation (ALREADY IMPLEMENTED)
- ✅ Iframe architecture details
- ✅ Laravel Blade layout for admin windows
- ✅ postMessage communication between iframe and parent
- ✅ ExtJS window function examples
- ✅ Migration strategy for updating API endpoints only

## Key Files Updated

1. `.kiro/specs/admin-panel-migration/requirements.md`
   - Requirement 2: Technology Stack Selection
   - Requirement 8: Windows 10 Desktop Interface (marked as ALREADY IMPLEMENTED)
   - Requirement 9: Window Content Architecture (iframe + Blade)

2. `.kiro/specs/admin-panel-migration/design.md`
   - Overview section
   - Technology Stack section
   - High-Level Architecture diagram
   - Directory Structure
   - Added Section 7: ExtJS Desktop Shell (ALREADY IMPLEMENTED)
   - Added Section 8: Admin Window Content (Laravel Blade)
   - Updated all migration phases

## Migration Phases Summary

### Phase 1: Foundation
- Package system infrastructure
- **Migrate ExtJS Desktop** (update API paths)
- Create admin Blade layout
- Migration bridge

### Phase 2: Core Modules
- Products, Orders, Users as **Blade views in iframes**
- Laravel controllers
- ExtJS window functions

### Phase 3: Content & Settings
- Content, Settings, Statistics as **Blade views in iframes**
- Laravel controllers
- ExtJS window functions

### Phase 4: Package Migration
- Migrate legacy packages to new structure

### Phase 5: Cleanup
- Remove legacy code
- Documentation

## Developer Guidelines

### Creating a New Admin Module

1. **Create Laravel Controller**
```php
// app/Http/Controllers/Admin/ExampleController.php
class ExampleController extends Controller
{
    public function index()
    {
        $this->authorize('example.view');
        return view('admin.example.index');
    }
}
```

2. **Create Blade View**
```blade
{{-- resources/views/admin/example/index.blade.php --}}
@extends('layouts.admin')

@section('content')
    <h1>Example Module</h1>
@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/js/example.js') }}"></script>
@endpush
```

3. **Create ExtJS Window Function**
```javascript
// public/admin/js/example_window.js
function openExample() {
    var win = Ext.create('Ext.window.Window', {
        title: 'Example Module',
        width: 1000,
        height: 600,
        html: '<iframe src="/admin/example" frameborder="0" style="width:100%;height:100%;"></iframe>'
    });
    win.show();
}
```

4. **Register Route**
```php
// routes/web.php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/example', [ExampleController::class, 'index'])->name('admin.example.index');
});
```

5. **Add Desktop Shortcut** (via package installer or manually in DB)

## Conclusion

The admin panel migration is NOT a complete UI rewrite. It's a **backend modernization** that:
- Keeps the ExtJS Desktop shell unchanged
- Replaces Smarty with Blade for window content
- Replaces custom PHP with Laravel backend
- Uses iframe architecture for clean separation

This approach minimizes risk, leverages existing developer knowledge, and allows gradual migration.
