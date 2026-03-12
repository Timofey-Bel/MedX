<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="/assets/sfera/styles.css">
    <link rel="stylesheet" href="/assets/sfera/css/header.css">
    <link rel="stylesheet" href="/assets/sfera/css/catalog.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <a href="/">Главная</a>
        <span>/</span>
        <?php if(isset($search_query) && $search_query): ?>
            <a href="/search/">Поиск</a>
            <span>/</span>
            <span><?php echo e($search_query); ?></span>
        <?php else: ?>
            <a href="<?php echo e(route('catalog.index')); ?>">Каталог</a>
            <?php if(isset($category['id']) && $category['id']): ?>
                <span>/</span>
                <span><?php echo e($category['name']); ?></span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Catalog Page -->
<main class="catalog-page">
    <div class="container">
        <div class="catalog-layout">
            <!-- Sidebar Filters -->
            <?php
                $action_url = route('catalog.index');
                if (isset($search_query) && $search_query) {
                    $action_url = route('search.index'); // Предполагаем, что у вас есть маршрут 'search.index'
                } elseif (isset($category['id']) && $category['id']) {
                    $action_url = route('catalog.category', ['category_id' => $category['id']]);
                }
            ?>
            
            <div class="sidebar-filters">
                <form method="GET" action="<?php echo e(route('catalog.index', ['category_id' => $category['id'] ?? null])); ?>">
                    

                    
                    <?php if(!empty($authors)): ?>
                        <div class="filter-section">
                            <h4>Автор</h4>
                            <?php $__currentLoopData = $authors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $author): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="author[]" value="<?php echo e($author['name']); ?>" <?php if(in_array($author['name'], $filter_authors)): ?> checked <?php endif; ?>>
                                    <?php echo e($author['name']); ?> (<?php echo e($author['count']); ?>)
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(!empty($ages)): ?>
                        <div class="filter-section">
                            <h4>Возраст</h4>
                            <?php $__currentLoopData = $ages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $age): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="age[]" value="<?php echo e($age['value']); ?>" <?php if(in_array($age['value'], $filter_ages)): ?> checked <?php endif; ?>>
                                    <?php echo e($age['value']); ?> (<?php echo e($age['count']); ?>)
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(!empty($series)): ?>
                        <div class="filter-section">
                            <h4>Серия</h4>
                            <?php $__currentLoopData = $series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seriya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="seriya[]" value="<?php echo e($seriya['id']); ?>" <?php if(in_array($seriya['id'], $filter_series)): ?> checked <?php endif; ?>>
                                    <?php echo e($seriya['name']); ?> (<?php echo e($seriya['count']); ?>)
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(!empty($productTypes)): ?>
                        <div class="filter-section">
                            <h4>Тип товара</h4>
                            <?php $__currentLoopData = $productTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="product_type[]" value="<?php echo e($productType['id']); ?>" <?php if(in_array($productType['id'], $filter_product_types)): ?> checked <?php endif; ?>>
                                    <?php echo e($productType['name']); ?> (<?php echo e($productType['count']); ?>)
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(!empty($topics)): ?>
                        <div class="filter-section">
                            <h4>Тематика</h4>
                            <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="topic[]" value="<?php echo e($topic['id']); ?>" <?php if(in_array($topic['id'], $filter_topics)): ?> checked <?php endif; ?>>
                                    <?php echo e($topic['name']); ?> (<?php echo e($topic['count']); ?>)
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                <button type="submit" class="btn btn-primary mt-3">Применить фильтр</button>
                </form>
            </div>

            <!-- Main Content -->
            <div class="catalog-content">
                <!-- Toolbar -->
                <div class="catalog-toolbar">
                    <div class="toolbar-left">
                        <?php if(isset($search_query) && $search_query): ?>
                            <h1>Результаты поиска: "<?php echo e($search_query); ?>"</h1>
                        <?php else: ?>
                            <h1><?php echo e($category['name'] ?? 'Каталог'); ?></h1>
                        <?php endif; ?>
                        <span class="results-count" id="resultsCount">Найдено <?php echo e($total); ?> </span>
                    </div>
                    <div class="toolbar-right">
                        
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    <?php if(count($products) > 0): ?>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="product-card">
                                <button class="product-favorite <?php if(isset($favorites['items'][$product['id']])): ?> favorite-filled <?php endif; ?>" data-product-id="<?php echo e($product['id']); ?>" title="<?php if(isset($favorites['items'][$product['id']])): ?>Удалить из избранного<?php else: ?>Добавить в избранное<?php endif; ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    </svg>
                                </button>
                                <a href="/product/<?php echo e($product['id']); ?>/" class="product-link">
                                    <div class="product-image">
                                        <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php echo e($product['name']); ?></h3>

                                        <div class="vi_24 vi0_24 p6b3_0_4-a p6b3_0_4-a0 p6b3_0_4-a1 tsBodyMBold"
                                             style="text-align: left;height: 22px;">
                                            <?php if($product['rating'] > 0 || $product['reviews_count'] > 0): ?>
                                                <?php if($product['rating'] > 0): ?>
                                                    <span class="p6b3_0_4-a4">
                                                        <svg
                                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                class="p6b3_0_4-a6 p6b3_0_4-a5" style="color: var(--graphicRating);"><path
                                                                    fill="currentColor"
                                                                    d="M8 2a1 1 0 0 1 .87.508l1.538 2.723 2.782.537a1 1 0 0 1 .538 1.667L11.711 9.58l.512 3.266A1 1 0 0 1 10.8 13.9L8 12.548 5.2 13.9a1 1 0 0 1-1.423-1.055l.512-3.266-2.017-2.144a1 1 0 0 1 .538-1.667l2.782-.537 1.537-2.723A1 1 0 0 1 8 2"></path>
                                                        </svg>
                                                        <span style="color:var(--textPremium);"><?php echo e($product['rating']); ?></span>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if($product['reviews_count'] > 0): ?>
                                                    <span class="p6b3_0_4-a4">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                             height="16" class="p6b3_0_4-a5"
                                                                             style="color: var(--graphicTertiary);"><path
                                                                fill="currentColor"
                                                                d="M8.545 13C11.93 13 14 11.102 14 8s-2.07-5-5.455-5C5.161 3 3.091 4.897 3.091 8c0 1.202.31 2.223.889 3.023-.2.335-.42.643-.656.899-.494.539-.494 1.077.494 1.077.89 0 1.652-.15 2.308-.394.703.259 1.514.394 2.42.394"></path>
                                                        </svg>
                                                        
                                                        <span style="color: var(--textSecondary);"><?php echo e(number_format($product['reviews_count'], 0)); ?>&nbsp;отзыв(ов)</span>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <?php if($product['price'] && $product['price'] > 0): ?>
                                            <div class="product-price">
                                                <span class="price-current"><?php echo e($product['price']); ?> ₽</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="product-actions">
                                    <?php if(isset($cart['items'][$product['id']]) && isset($cart['items'][$product['id']]['product_amount'])): ?>
                                        <button class="btn-add-to-cart" data-product-id="<?php echo e($product['id']); ?>" type="button" style="display: none;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                                                <circle cx="9" cy="20" r="1" fill="currentColor"/>
                                                <circle cx="17" cy="20" r="1" fill="currentColor"/>
                                            </svg>
                                            <span>В корзину</span>
                                        </button>
                                        <a href="#" class="btn-buy-all" data-product-id="<?php echo e($product['id']); ?>" data-max-quantity="<?php echo e($product['quantity']); ?>">Купить всё</a>
                                        <div class="product-quantity-control" data-product-id="<?php echo e($product['id']); ?>">
                                            <button class="qty-btn qty-minus" data-product-id="<?php echo e($product['id']); ?>" type="button">−</button>
                                            <input type="number" class="qty-input" value="<?php echo e($cart['items'][$product['id']]['product_amount']); ?>" min="1" max="<?php echo e($product['quantity']); ?>" data-product-id="<?php echo e($product['id']); ?>">
                                            <button class="qty-btn qty-plus" data-product-id="<?php echo e($product['id']); ?>" type="button">+</button>
                                        </div>
                                    <?php else: ?>
                                        <button class="btn-add-to-cart" data-product-id="<?php echo e($product['id']); ?>" type="button">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                                                <circle cx="9" cy="20" r="1" fill="currentColor"/>
                                                <circle cx="17" cy="20" r="1" fill="currentColor"/>
                                            </svg>
                                            <span>В корзину</span>
                                        </button>
                                        <a href="#" class="btn-buy-all" data-product-id="<?php echo e($product['id']); ?>" data-max-quantity="<?php echo e($product['quantity']); ?>">Купить всё</a>
                                        <div class="product-quantity-control hidden" data-product-id="<?php echo e($product['id']); ?>">
                                            <button class="qty-btn qty-minus" data-product-id="<?php echo e($product['id']); ?>" type="button">−</button>
                                            <input type="number" class="qty-input" value="1" min="1" max="<?php echo e($product['quantity']); ?>" data-product-id="<?php echo e($product['id']); ?>">
                                            <button class="qty-btn qty-plus" data-product-id="<?php echo e($product['id']); ?>" type="button">+</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="no-products">
                            <p>Товары не найдены</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if($pages > 1): ?>
                    <?php
                        // Формируем параметры для пагинации с сохранением всех фильтров
                        $paginationParams = [];
                        if (isset($category['id']) && $category['id']) {
                            $paginationParams['category_id'] = $category['id'];
                        }
                        if (!empty($filter_authors)) {
                            $paginationParams['author'] = $filter_authors;
                        }
                        if (!empty($filter_ages)) {
                            $paginationParams['age'] = $filter_ages;
                        }
                        if (!empty($filter_series)) {
                            $paginationParams['seriya'] = $filter_series;
                        }
                        if (!empty($filter_product_types)) {
                            $paginationParams['product_type'] = $filter_product_types;
                        }
                        if (!empty($filter_topics)) {
                            $paginationParams['topic'] = $filter_topics;
                        }
                    ?>
                    <div class="pagination">
                        <?php if($hasPrevGroup): ?>
                            <?php if(isset($search_query) && $search_query): ?>
                                <a href="<?php echo e(route('search.index', array_merge(['query' => $search_query, 'page' => $prevGroupEnd], $paginationParams))); ?>" class="pagination-btn">
                            <?php else: ?>
                                <a href="<?php echo e(route('catalog.index', array_merge(['page' => $prevGroupEnd], $paginationParams))); ?>" class="pagination-btn">
                            <?php endif; ?>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 4l-4 4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </a>
                        <?php else: ?>
                            <button class="pagination-btn" disabled>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 4l-4 4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php if($i == $page): ?>
                                <button class="pagination-btn active"><?php echo e($i); ?></button>
                            <?php else: ?>
                                <?php if(isset($search_query) && $search_query): ?>
                                    <a href="<?php echo e(route('search.index', array_merge(['query' => $search_query, 'page' => $i], $paginationParams))); ?>" class="pagination-btn"><?php echo e($i); ?></a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('catalog.index', array_merge(['page' => $i], $paginationParams))); ?>" class="pagination-btn"><?php echo e($i); ?></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($hasNextGroup): ?>
                            <?php if(isset($search_query) && $search_query): ?>
                                <a href="<?php echo e(route('search.index', array_merge(['query' => $search_query, 'page' => $nextGroupStart], $paginationParams))); ?>" class="pagination-btn">
                            <?php else: ?>
                                <a href="<?php echo e(route('catalog.index', array_merge(['page' => $nextGroupStart], $paginationParams))); ?>" class="pagination-btn">
                            <?php endif; ?>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </a>
                        <?php else: ?>
                            <button class="pagination-btn" disabled>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="/assets/sfera/js/catalog.js"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/catalog/index.blade.php ENDPATH**/ ?>