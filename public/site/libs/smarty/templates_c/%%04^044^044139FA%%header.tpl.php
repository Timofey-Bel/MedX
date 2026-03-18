<?php /* Smarty version 2.6.11, created on 2025-12-29 10:00:38
         compiled from medx/tpl/header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'mod', 'medx/tpl/header.tpl', 56, false),)), $this); ?>
<!-- Header -->
<header class="header">


    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/location-bar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        
    <div class="header-main">
        <div class="header-container">
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-button" id="mobileMenuButton">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <!-- Logo -->
            <div class="header-logo">
                <a href="/"><img src="/assets/medx/img/logo/logo_white.svg" alt="Творческий Центр СФЕРА" height="100"></a>
            </div>

            <!-- Catalog Button -->
            <button class="catalog-button" id="catalogButton">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M2 5h16M2 10h16M2 15h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Каталог</span>
            </button>

            <!-- Catalog Dropdown Menu -->
            <div class="catalog-dropdown" id="catalogDropdown">
                <div class="catalog-overlay" id="catalogOverlay"></div>
                <div class="catalog-menu">
                    <?php echo smarty_function_mod(array('path' => "medx/",'mod_name' => 'catalog_menu'), $this);?>

                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-bar-wrapper">
                <form class="search-bar" action="/search/" method="get">
                        <input type="text" name="query" placeholder="Искать" class="search-input" value="<?php if ($this->_tpl_vars['search_query']):  echo $this->_tpl_vars['search_query'];  endif; ?>" autocomplete="off">
                    <button type="submit" class="search-submit">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
                            <path d="M12.5 12.5l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>

            <!-- User Actions -->
    <?php if ($_SESSION['auth_user']): ?>
        <!-- Пользователь авторизован -->
        <a href="/profile/" class="header-action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                <path d="M5 20c0-4 3-7 7-7s7 3 7 7" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span>Профиль</span>
        </a>
    <?php else: ?>
        <!-- Пользователь НЕ авторизован -->
        <a href="/login/" class="header-action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                <path d="M5 20c0-4 3-7 7-7s7 3 7 7" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span>Войти</span>
        </a>
    <?php endif; ?>
                <a href="/orders/" class="header-action">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="6" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 10h18M8 14h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Заказы</span>
                </a>
                <a href="/favorites/" class="header-action header-action-favorites">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Избранное</span>
                    <span class="mobile-nav-badge favorites-counter" style="right:20%;top:-10px;" data-bind="text: formattedCount, visible: isVisible"></span>
                </a>
                <a href="/cart/" class="header-action header-action-cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="9" cy="20" r="1" fill="currentColor"/>
                        <circle cx="17" cy="20" r="1" fill="currentColor"/>
                    </svg>
                    <span>Корзина</span>
                    <span class="mobile-nav-badge cart-counter" style="right:20%;top:-10px;" data-bind="text: formattedCount, visible: isVisible"></span>
                </a>

            </nav>
        </div>
    </div>

    <!-- Secondary Navigation -->
    <div class="header-secondary">
        <div class="header-container">
            <?php echo smarty_function_mod(array('path' => "medx/",'mod_name' => 'secondary_nav'), $this);?>

        </div>
    </div>
</header>