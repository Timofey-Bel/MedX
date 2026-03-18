<?php /* Smarty version 2.6.11, created on 2025-12-28 18:55:21
         compiled from medx/random_products/random_products.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'medx/random_products/random_products.tpl', 2, false),array('modifier', 'number_format', 'medx/random_products/random_products.tpl', 38, false),array('modifier', 'fins', 'medx/random_products/random_products.tpl', 38, false),)), $this); ?>
<div class="product-grid">
<?php if (count($this->_tpl_vars['products']) > 0): ?>
<?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['product']):
?>
    <article class="product-card">
        <button class="product-favorite<?php if (isset ( $this->_tpl_vars['favorites']['items'][$this->_tpl_vars['product']['id']] )): ?> favorite-filled<?php endif; ?>" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" title="<?php if (isset ( $this->_tpl_vars['favorites']['items'][$this->_tpl_vars['product']['id']] )): ?>Удалить из избранного<?php else: ?>Добавить в избранное<?php endif; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </button>
        <a href="/product/<?php echo $this->_tpl_vars['product']['id']; ?>
/" class="product-link">
            <div class="product-image">
                <img src="<?php echo $this->_tpl_vars['product']['image']; ?>
" alt="<?php echo $this->_tpl_vars['product']['name']; ?>
" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?php echo $this->_tpl_vars['product']['name']; ?>
</h3>
                <div class="vi_24 vi0_24 p6b3_0_4-a p6b3_0_4-a0 p6b3_0_4-a1 tsBodyMBold"
                     style="text-align: left;height: 22px;">
                    <?php if ($this->_tpl_vars['product']['rating'] > 0 || $this->_tpl_vars['product']['reviews_count'] > 0): ?>
                    <?php if ($this->_tpl_vars['product']['rating'] > 0): ?>
                    <span class="p6b3_0_4-a4">
                        <svg
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                class="p6b3_0_4-a6 p6b3_0_4-a5" style="color: var(--graphicRating);"><path
                                    fill="currentColor"
                                    d="M8 2a1 1 0 0 1 .87.508l1.538 2.723 2.782.537a1 1 0 0 1 .538 1.667L11.711 9.58l.512 3.266A1 1 0 0 1 10.8 13.9L8 12.548 5.2 13.9a1 1 0 0 1-1.423-1.055l.512-3.266-2.017-2.144a1 1 0 0 1 .538-1.667l2.782-.537 1.537-2.723A1 1 0 0 1 8 2"></path>
                        </svg>
                        <span style="color:var(--textPremium);"><?php echo $this->_tpl_vars['product']['rating']; ?>
</span>
                    </span>
                    <?php endif; ?>
                    <?php if ($this->_tpl_vars['product']['reviews_count'] > 0): ?>
                    <span class="p6b3_0_4-a4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                 height="16" class="p6b3_0_4-a5"
                                                                 style="color: var(--graphicTertiary);"><path
                                    fill="currentColor"
                                    d="M8.545 13C11.93 13 14 11.102 14 8s-2.07-5-5.455-5C5.161 3 3.091 4.897 3.091 8c0 1.202.31 2.223.889 3.023-.2.335-.42.643-.656.899-.494.539-.494 1.077.494 1.077.89 0 1.652-.15 2.308-.394.703.259 1.514.394 2.42.394"></path>
                        </svg>
                        <span style="color: var(--textSecondary);"><?php echo ((is_array($_tmp=$this->_tpl_vars['product']['reviews_count'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 2) : smarty_modifier_number_format($_tmp, 2)); ?>
&nbsp;отзыв<?php echo ((is_array($_tmp=$this->_tpl_vars['product']['reviews_count'])) ? $this->_run_mod_handler('fins', true, $_tmp) : smarty_modifier_fins($_tmp)); ?>
</span>
                    </span>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
<?php if ($this->_tpl_vars['product']['price'] && $this->_tpl_vars['product']['price'] > 0): ?>
                <div class="product-price">
                    <span class="price-current"><?php echo $this->_tpl_vars['product']['price']; ?>
 ₽</span>
                </div>
<?php endif; ?>
            </div>
        </a>
        <div class="product-actions">
<?php if (isset ( $this->_tpl_vars['cart']['items'][$this->_tpl_vars['product']['id']] ) && isset ( $this->_tpl_vars['cart']['items'][$this->_tpl_vars['product']['id']]['product_amount'] )): ?>
            <button class="btn-add-to-cart" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" type="button" style="display: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                    <circle cx="9" cy="20" r="1" fill="currentColor"/>
                    <circle cx="17" cy="20" r="1" fill="currentColor"/>
                </svg>
                <span>В корзину</span>
            </button>
            <a href="#" class="btn-buy-all" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" data-max-quantity="<?php echo $this->_tpl_vars['product']['quantity']; ?>
">Купить всё</a>
            <div class="product-quantity-control" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
">
                <button class="qty-btn qty-minus" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" type="button">−</button>
                <input type="number" class="qty-input" value="<?php echo $this->_tpl_vars['cart']['items'][$this->_tpl_vars['product']['id']]['product_amount']; ?>
" min="1" max="<?php echo $this->_tpl_vars['product']['quantity']; ?>
" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
">
                <button class="qty-btn qty-plus" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" type="button">+</button>
            </div>
<?php else: ?>
            <button class="btn-add-to-cart" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                    <circle cx="9" cy="20" r="1" fill="currentColor"/>
                    <circle cx="17" cy="20" r="1" fill="currentColor"/>
                </svg>
                <span>В корзину</span>
            </button>
            <a href="#" class="btn-buy-all" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" data-max-quantity="<?php echo $this->_tpl_vars['product']['quantity']; ?>
">Купить всё</a>
            <div class="product-quantity-control hidden" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
">
                <button class="qty-btn qty-minus" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" type="button">−</button>
                <input type="number" class="qty-input" value="1" min="1" max="<?php echo $this->_tpl_vars['product']['quantity']; ?>
" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
">
                <button class="qty-btn qty-plus" data-product-id="<?php echo $this->_tpl_vars['product']['id']; ?>
" type="button">+</button>
            </div>
<?php endif; ?>
        </div>
    </article>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
</div>

<style>
    .wrap {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn_splash {
        min-width: 250px;
        min-height: 30px;
        display: inline-flex;
        font-family: 'Nunito', sans-serif;
        font-size: 18px;
        align-items: center;
        justify-content: center;
        text-align: center;
        letter-spacing: 1.3px;
        font-weight: 400;
        color: #fff;
        background: #4FD1C5;
        background: linear-gradient(90deg, #FBC100 0%, #FBC100 100%);
        border: none;
        border-radius: 1000px;
        transition: all 0.3s ease-in-out 0s;
        cursor: pointer;
        outline: none;
        position: relative;
        padding: 10px;
    }

    .btn_splash::before {
        content: '';
        border-radius: 1000px;
        min-width: calc(300px + 12px);
        min-height: calc(60px + 12px);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: all .3s ease-in-out 0s;
    }

    .btn_splash:hover,
    .btn_splash:focus {
        color: #fff;
        transform: translateY(-6px);
    }

    .btn_splash:hover::before,
    .btn_splash:focus::before {
        opacity: 1;
    }

    .btn_splash::after {
        content: '';
        width: 30px; height: 30px;
        border-radius: 100%;
        border: 6px solid #FBC100;
        position: absolute;
        z-index: -1;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: ring 1.5s infinite;
    }

    .btn_splash:hover::after,
    .btn_splash:focus::after {
        animation: none;
        display: none;
    }

    @keyframes ring {
        0% {
            width: 30px;
            height: 30px;
            opacity: 1;
        }
        100% {
            width: 300px;
            height: 300px;
            opacity: 0;
        }
    }
    </style>
<div class="catalog-link-container" style="text-align: center; margin-top: 32px; padding: 16px 0;">


    <div class="wrap">
        <a href="/catalog/" c class="btn_splash">Посмотреть каталог</a>
    </div>

    
</div>
