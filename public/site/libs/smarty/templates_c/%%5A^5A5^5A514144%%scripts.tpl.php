<?php /* Smarty version 2.6.11, created on 2026-03-04 14:21:11
         compiled from medx/tpl/scripts.tpl */ ?>
<!-- KnockoutJS Library -->
<script src="/js/knockoutjs/dist/knockout.js"></script>
<!-- KnockoutJS Mapping Plugin (для автоматического преобразования данных) -->
<script src="/js/knockout-mapping/knockout.mapping.js"></script>
<script>
// console.log('=== KnockoutJS Loaded ===');
// console.log('Knockout version:', ko.version);
// console.log('Knockout Mapping:', typeof ko.mapping !== 'undefined' ? 'loaded' : 'not loaded');
// console.log('========================');
</script>
<!-- jQuery (нужен для AJAX запросов в модульных моделях) -->
<script src="/js/jquery.min.js"></script>
<!-- Cart Counter ViewModel (global for all pages) -->
<script src="/assets/medx/js/cart-counter.js"></script>
<!-- Favorites Counter ViewModel (global for all pages) -->
<script src="/assets/medx/js/favorites-counter.js"></script>
<!-- Модульная модель корзины (старый подход с ko.mapping и прототипным наследованием) -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "models/cart_new/model_cart.js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- Модульная модель избранного (старый подход с ko.mapping и прототипным наследованием) -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "models/favorites/model_favorites.js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
