{{-- TODO: Добавить реальные скрипты --}}
<!-- KnockoutJS Library -->
<script src="/js/knockoutjs/dist/knockout.js"></script>
<!-- KnockoutJS Mapping Plugin (для автоматического преобразования данных) -->
<script src="/js/knockout-mapping/knockout.mapping.js"></script>
<!-- jQuery (нужен для AJAX запросов в модульных моделях) -->
<script src="/js/jquery.min.js"></script>
<!-- Cart Counter ViewModel (global for all pages) -->
<script src="/assets/sfera/js/cart-counter.js"></script>
<!-- Favorites Counter ViewModel (global for all pages) -->
<script src="/assets/sfera/js/favorites-counter.js"></script>
<!-- Модульная модель корзины -->
@include('js.models.cart_new.model_cart')
<!-- Модульная модель избранного -->
@include('js.models.favorites.model_favorites')