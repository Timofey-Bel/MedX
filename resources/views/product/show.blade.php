@extends($useWholesaleLayout ?? false ? 'wholesale.layout' : 'layouts.app')

@if(!($useWholesaleLayout ?? false))
@section('title', $seoData['title'] ?? $product->name)
@else
@section('page-title', $product->name)
@endif

@push('head')
    @if(isset($seoData['description']))
    <meta name="description" content="{{ $seoData['description'] }}">
    @endif
    
    @if(isset($seoData['og_title']))
    <meta property="og:title" content="{{ $seoData['og_title'] }}">
    <meta property="og:description" content="{{ $seoData['og_description'] }}">
    <meta property="og:image" content="{{ $seoData['og_image'] }}">
    <meta property="og:url" content="{{ $seoData['og_url'] }}">
    <meta property="og:type" content="product">
    @endif
    
    @if(isset($seoData['schema']))
    <script type="application/ld+json">
    {!! json_encode($seoData['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    @endif
@endpush

@push('styles')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="/assets/libs/swiper/swiper-bundle.min.css">
    
    <link rel="stylesheet" href="/assets/sfera/styles.css">
    <link rel="stylesheet" href="/assets/sfera/css/product.css">
    <link rel="stylesheet" href="/assets/sfera/css/product-hashtags.css">
    <link rel="stylesheet" href="/assets/sfera/css/product-gallery.css">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        @foreach($breadcrumbs as $crumb)
        @if($crumb['url'])
        <a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a>
        @else
        <span>{{ $crumb['title'] }}</span>
        @endif
        @if(!$loop->last)
        <span>/</span>
        @endif
        @endforeach
    </div>
</div>

<!-- Product Page -->
<main class="product-page">
    <div class="container">
        <!-- Product Meta Actions -->

        <div class="product-meta-actions">
            <div class="product-article"></div>
            <div class="meta-actions-right">
            </div>
        </div>


        <div class="product-layout">
            <!-- Product Gallery - Vertical Swiper -->
            <div class="product-gallery" role="region" aria-label="Галерея изображений товара">
                <div class="product-gallery__flex">
                    <!-- Thumbnails Column with Navigation -->
                    <div class="product-gallery__col">
                        <!-- Prev Button -->
                        <button class="product-gallery__prev" aria-label="Предыдущее изображение" type="button">▲</button>

                        <!-- Thumbnails Swiper (Vertical) -->
                        <div class="product-gallery__thumbs" aria-label="Миниатюры изображений">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    @if(count($images) > 0)
                                        @foreach($images as $index => $image)
                                        <div class="swiper-slide" role="button" tabindex="0" aria-label="Изображение {{ $index + 1 }} из {{ count($images) }}">
                                            <div class="product-gallery__image">
                                                <img src="{{ $image->url }}" alt="{{ $product->name }} - изображение {{ $index + 1 }}" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="swiper-slide" role="button" tabindex="0" aria-label="Изображение товара">
                                            <div class="product-gallery__image">
                                                <img src="/assets/img/product_empty.jpg" alt="{{ $product->name }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Next Button -->
                        <button class="product-gallery__next" aria-label="Следующее изображение" type="button">▼</button>
                    </div>

                    <!-- Main Images Swiper -->
                    <div class="product-gallery__images" aria-label="Основное изображение товара">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @if(count($images) > 0)
                                    @foreach($images as $index => $image)
                                    <div class="swiper-slide">
                                        <div class="product-gallery__image">
                                            <img src="{{ $image->url }}" alt="{{ $product->name }} - изображение {{ $index + 1 }}" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';" role="button" tabindex="0" aria-label="Нажмите для увеличения">
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide">
                                        <div class="product-gallery__image">
                                            <img src="/assets/img/product_empty.jpg" alt="{{ $product->name }}" role="button" tabindex="0" aria-label="Нажмите для увеличения">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Right Column: Product Info & Actions -->
            <div class="product-info-column">



                <link rel="stylesheet" href="/assets/sfera/css/product/ui-kit-notification.css">
                <link rel="stylesheet" href="/assets/sfera/css/product/ui-kit-radio-group.css">
                <link rel="stylesheet" href="/assets/sfera/css/product/pdp-all-delivery-v7.css">
                <link rel="stylesheet" href="/assets/sfera/css/product/pdp-mobile-characteristics-v7.css">
                <link rel="stylesheet" href="/assets/sfera/css/product/common-all-tag-list.css">
                <link rel="stylesheet" href="/assets/sfera/css/product/pdp-all-relations.css">
                <link rel="stylesheet" href="/assets/sfera/css/product/product-info-column.css">


                <div data-widget="webProductHeading" class="pdp_gb9 pdp_g9b">
                    <h1 class="pdp_bg9 tsHeadline550Medium">
                        {{ $product->name }}
                    </h1>
                </div>


                @if($reviewsStats['total_count'] > 0)
                <div data-widget="webPdpGrid"
                     data-replace-layout-path="[3][0][2][0][0][0][0][0][1][0][0][0][0][0][1][0]"
                     class="pdp_s8a pdp_s9a pdp_t2a" style="width: auto;">
                    <div data-widget="webSingleProductScore"><a
                                href="/product/propisi-ya-gotovlyus-k-pismu-dlya-detey-4-5-let-chistyakova-nina-andreevna-523319123/reviews/"
                                target="_self" class="nn6_28 ga5_3_10-a ga5_3_10-a5"
                                style="color:var(--textSecondary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                 class="ga5_3_10-a0 ga5_3_10-a1" style="color: var(--graphicRating);">
                                <path fill="currentColor"
                                      d="M5.798 3.113C6.775 1.371 7.264.5 8 .5s1.225.871 2.202 2.613c.216.385.404.865.771 1.129.35.251.838.276 1.252.351 1.963.358 2.945.536 3.202 1.214q.028.075.047.152c.173.706-.526 1.318-1.93 2.76-.439.452-.639.666-.726.958-.082.271-.042.565.036 1.152.27 2.016.402 3.134-.175 3.59-1.033.818-2.699-.617-3.632-1.048C8.532 13.133 8.274 13 8 13s-.531.133-1.047.371c-.933.43-2.599 1.866-3.632 1.048-.576-.456-.44-1.574-.17-3.59.078-.587.117-.88.036-1.152-.088-.292-.288-.506-.727-.957C1.057 7.277.353 6.665.526 5.96q.019-.078.047-.153c.257-.678 1.239-.856 3.202-1.214.414-.075.902-.1 1.252-.351.367-.264.555-.744.771-1.129"></path>
                            </svg>
                            <div class="ga5_3_10-a2 tsBodyControl500Medium">{{ $reviewsStats['average_rating'] }} • {{ $reviewsStats['total_count'] }} отзыв{{ $reviewsStats['total_count'] > 1 ? 'ов' : '' }}</div>
                        </a>
                    </div>

                </div>
                @endif

                <div data-widget="webPdpGrid"
                     data-replace-layout-path="[3][0][2][0][0][0][0][0][1][0][0][0][0][0][2][0]"
                     class="pdp_s8a pdp_t4a pdp_t1a pdp_ta3" style="width: auto;">
                    <div data-widget="webAspects" class="pdp_ea2">
                        <div class="pdp_ag8 pdp_ba6">
                            <div class="pdp_r3 pdp_a8g">
                                <div class="pdp_l">
                                    <span class="q6b3_0_4-a">
                                        <span class="q6b3_0_4-a2">Тип товара:</span>
                                    </span>
                                </div>
                                <div class="pdp_q6">
                                    <div screen-offset="16" class="ea5_3_12-a pdp_q8">
                                        @php
                                        $product_type_attr = null;
                                        foreach($attributes as $attr) {
                                            if($attr->name == "Тип товара" && $attr->value && $attr->value != '') {
                                                $product_type_attr = $attr;
                                                break;
                                            }
                                        }
                                        @endphp
                                        @if($product_type_attr && isset($product_type_attr->value))
                                        @if(isset($product_type_attr->product_type_id) && $product_type_attr->product_type_id > 0)
                                        <a href="/product_type/?product_type_id={{ $product_type_attr->product_type_id }}"
                                                class="pdp_f3 pdp_f4">
                                            <div class="pdp_f6">
                                                <div title="" class="pdp_e7 pdp_e8 pdp_f2 pdp_r" style="cursor: pointer;">
                                                    <div class="pdp_q3">
                                                        <span class="q6b3_0_4-a pdp_q5 pdp_f1">
                                                            <span style="color:;">{{ $product_type_attr->value }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        @else
                                        <div class="pdp_f6">
                                            <div title="" class="pdp_e7 pdp_e8 pdp_f2 pdp_r">
                                                <div class="pdp_q3">
                                                    <span class="q6b3_0_4-a pdp_q5 pdp_f1">
                                                        <span style="color:;">{{ $product_type_attr->value }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div> </div>
                    </div>

                    <div data-widget="webShortCharacteristics" class="pdp_mb5">
                        <div class="pdp_m5b"><span class="q6b3_0_4-a" ><span class="tsHeadline500Medium"
                                                                            style="color:var(--textPrimary);">О товаре</span></span>
                            <div class="b5_4_7-a0 b5_4_7-b3 b5_4_7-a6 b5_4_7-a7" onclick="location.href='#product_description'"
                                 style="border-top-left-radius:8px;border-top-right-radius:8px;border-bottom-right-radius:8px;border-bottom-left-radius:8px;">
                                <div class="b5_4_7-b">
                                    <div title="Перейти к описанию" class="b5_4_7-b0 tsBodyControl400Small">
                                        Перейти к описанию
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                     class="b5_4_7-a3">
                                    <path fill="currentColor"
                                          d="M6.293 4.293a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414L8.586 8 6.293 5.707a1 1 0 0 1 0-1.414"></path>
                                </svg>
                                <div class="b5_4_7-a"></div>
                            </div>
                        </div>
                        <div class="pdp_bm6">
                            @if(count($attributes) > 0)
                            @foreach($attributes as $attr)
                            @if($attr->name == "Авторы" && $attr->value && $attr->value != '')
                            <div class="pdp_b6m pdp_b2m">
                                <div class="pdp_mb2 pdp_bm3"><span class="q6b3_0_4-a pdp_b3m"><span
                                                class="tsBodyM" style="color:var(--textSecondary);">Автор</span></span>
                                </div>
                                <div class="pdp_mb2 pdp_m3b"><span class="pdp_b4m tsBody400Small"
                                                                   style="color:var(--textPrimary);">{{ $attr->value }}</span>
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @foreach($attributes as $attr)
                            @if($attr->name == "Издательство" && $attr->value && $attr->value != '')
                            <div class="pdp_b6m pdp_b2m">
                                <div class="pdp_mb2 pdp_bm3"><span class="q6b3_0_4-a pdp_b3m"><span
                                                class="tsBodyM" style="color:var(--textSecondary);">Издательство</span></span>
                                </div>
                                <div class="pdp_mb2 pdp_m3b"><span class="pdp_b4m tsBody400Small"
                                                                   style="color:var(--textPrimary);">{{ $attr->value }}</span>
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @foreach($attributes as $attr)
                            @if($attr->name == "Серия" && $attr->value && $attr->value != '')
                            <div class="pdp_b6m pdp_b2m">
                                <div class="pdp_mb2 pdp_bm3"><span class="q6b3_0_4-a pdp_b3m"><span
                                                class="tsBodyM" style="color:var(--textSecondary);">Серия</span></span>
                                </div>
                                <div class="pdp_mb2 pdp_m3b">
                                    @if(isset($attr->seriya_id) && $attr->seriya_id > 0)
                                    <a href="/seriya/?seriya_id={{ $attr->seriya_id }}" class="pdp_b4m tsBody400Small" style="color:var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @else
                                    <span class="pdp_b4m tsBody400Small" style="color:var(--textPrimary);">{{ $attr->value }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @foreach($attributes as $attr)
                            @if($attr->name == "Год" && $attr->value && $attr->value != '')
                            <div class="pdp_b6m pdp_b2m">
                                <div class="pdp_mb2 pdp_bm3"><span class="q6b3_0_4-a pdp_b3m"><span
                                                class="tsBodyM" style="color:var(--textSecondary);">Год выпуска</span></span>
                                </div>
                                <div class="pdp_mb2 pdp_m3b"><span class="pdp_b4m tsBody400Small"
                                                                   style="color:var(--textPrimary);">{{ $attr->value }}</span>
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div data-widget="row"
                         data-replace-layout-path="[3][0][2][0][0][0][0][0][1][0][0][0][0][0][2][0][3][0]"
                         class="e1">
                        <div data-widget="column"
                             data-widget-disallow="[3][0][2][0][0][0][0][0][1][0][0][0][0][0][2][0][3][0][0]"
                             class="d8 c8 d"></div>
                    </div>
                </div>



            </div>

            <!-- Sticky Sidebar: Purchase Block -->
            <div class="product-purchase-sidebar">
                <div class="purchase-sticky-content">
                    <!-- Price Block -->

                    <div class="product-price-block">
                        <div class="price-main">
                            <span class="price-current">{{ number_format($product->price, 0, ',', ' ') }} ₽</span>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="quantity-selector" style="display: none;">
                        <button class="quantity-btn" id="decreaseQty" aria-label="Уменьшить количество">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 8h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <input type="number" class="quantity-input" id="quantityInput" value="1" min="1" max="@if(isset($product->quantity) && $product->quantity > 0){{ $product->quantity }}@endif">
                        <button class="quantity-btn" id="increaseQty" aria-label="Увеличить количество">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M8 4v8M4 8h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>


                    <link rel="stylesheet" href="/assets/sfera/css/product/pdp-all-description-v3.css">
                    <link rel="stylesheet" href="/assets/sfera/css/product/pdp-all-attachments.css">
                    <link rel="stylesheet" href="/assets/sfera/css/product/purchase-sticky-content.css">

                    <!-- Action Buttons -->

                    <div data-widget="webPdpGrid"
                         class="pdp_s8a pdp_ta2" style="width: auto;">
                        <div data-widget="webAddToCart" class="pdp_f9a">
                            <div class="pdp_ba6">
                                <div screen-offset="8" class="ea5_3_12-a pdp_ga">
                                    <div class="pdp_fa7">
                                        <div screen-offset="16" fixed="" class="ea5_3_12-a pdp_f7a">
                                            <button  data-product-id="{{ $product->id }}"  class="btn-add-to-cart pdp_f5a pdp_ag b25_5_1-a0 b25_5_1-b3 b25_5_1-a5"
                                                    style="background: var(--bgActionPrimary); color: var(--textLightKey);">
                                                <div class="b25_5_1-a2">
                                                    <div class="b25_5_1-a9 tsBodyControl500Medium">В корзину
                                                    </div>
                                                </div>
                                                <div class="b25_5_1-a"
                                                     style="background-color: var(--textLightKey);"></div>
                                            </button>
                                        </div>
                                        <div class="pdp_a6f">
                                            <div class="pdp_a7f">
                                                <a href="#" class="btn-buy-all" data-product-id="{{ $product->id }}" data-max-quantity="@if(isset($product->quantity) && $product->quantity > 0){{ $product->quantity }}@endif">Купить всё</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-widget="webAddToFavorite" class="ea5_3_12-a">
                            <button data-product-id="{{ $product->id }}" aria-label="Добавить в избранное" class="product-favorite pdp_a1g ag5_6_1-a0 ag5_6_1-a4"
                                    style="background: var(--bgActionSecondary); color: var(--graphicActionPrimary);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="ag5_6_1-b2">
                                    <path fill="currentColor"
                                          d="M7 5a4 4 0 0 0-4 4c0 3.552 2.218 6.296 4.621 8.22A21.5 21.5 0 0 0 12 19.91a21.6 21.6 0 0 0 4.377-2.69C18.78 15.294 21 12.551 21 9a4 4 0 0 0-4-4c-1.957 0-3.652 1.396-4.02 3.2a1 1 0 0 1-1.96 0C10.652 6.396 8.957 5 7 5m5 17c-.316-.02-.56-.147-.848-.278a23.5 23.5 0 0 1-4.781-2.942C3.777 16.705 1 13.449 1 9a6 6 0 0 1 6-6 6.18 6.18 0 0 1 5 2.568A6.18 6.18 0 0 1 17 3a6 6 0 0 1 6 6c0 4.448-2.78 7.705-5.375 9.78a23.6 23.6 0 0 1-4.78 2.942c-.543.249-.732.278-.845.278"></path>
                                </svg>
                                @if(isset($favorites['items'][$product->id]))
                                <div class="ag5_6_1-a" style="background-color: var(--bgAccentPrimary);"></div>
                                @else
                                <div class="ag5_6_1-a" style="background-color: var(--graphicActionPrimary);"></div>
                                @endif
                            </button>
                        </div>
                    </div>


                </div>
            </div>
        </div>



        <style nonce="" data-href="">
            .pdp_p5a { max-height: 99999px; overflow: hidden; overflow-wrap: break-word; position: relative; transition: max-height 0.2s; }
            .pdp_p5a ul { display: block; list-style-type: disc; margin-block: 1em; margin-inline: 0px; padding-inline-start: 40px; }
            .pdp_p5a ul li::marker { content: normal; display: initial; }
            .pdp_pa8 { color: var(--textPrimary); display: inline-block; font-size: 24px; font-weight: 700; line-height: 30px; outline: none; }
            .pdp_ap9 .pdp_pa8 { font-size: 20px; line-height: 26px; margin-right: 20px; }
            .pdp_a9p { align-items: center; display: flex; margin-bottom: 16px; }
            .pdp_a9p.pdp_pa9 { margin-bottom: 8px; }
            .pdp_a9p.pdp_pa9 .pdp_pa8 { font-size: 20px; line-height: 26px; margin-bottom: 8px; }
            .pdp_a9p.pdp_pa9 .pdp_pa8 { font-size: 20px; line-height: 26px; margin-bottom: 8px; }
            .pdp_pa8 + .pdp_p9a { margin-left: 12px; }

            .section-description{
                background: #ffffff;
                border-radius: 12px;
                overflow: hidden;
                padding: 32px;
            }

        </style>
        <div class="section-description" id="section-description">
            <div class="pdp_a9p" id="product_description" ><h2 class="pdp_pa8">Описание</h2> </div>
            <div>
                <div class="">
                    <div class="pdp_p5a" style="max-height: 700px;">
                        <div class="RA-a1">
                        @if($product->description)
                        {!! nl2br(e($product->description)) !!}
                        @else
                        Описание товара отсутствует.
                        @endif
                        </div>
                    </div>  </div> </div>

            <!-- Product Hashtags Module -->
            <div class="container" style="margin-top: 24px;">
            </div>



            <link rel="stylesheet" href="/assets/sfera/css/product/pdp-mobile-characteristics-v7.css">
            <link rel="stylesheet" href="/assets/sfera/css/product/characteristics.css">

            <div id="section-characteristics" class="">
                <div class="pdp_ai1"><h2 class="pdp_i3a"><a
                                href="https://www.ozon.ru/product/propisi-ya-gotovlyus-k-pismu-dlya-detey-4-5-let-chistyakova-nina-andreevna-523319123/features/"
                                class="pdp_i0a">Характеристики</a></h2></div>
                <div class="">
                    <div class="pdp_i1a">
                        <div style="width: calc(50%);">
                            @if($product->id)
                            <dl class="pdp_i5a">
                                <dt class="pdp_i4a"><span class="pdp_a5i">Артикул</span></dt>
                                <dd class="pdp_ai5"><span class="pdp_ia6"><span class="pdp_i6a">{{ $product->id }}</span><div
                                                class="pdp_s3 pdp_s4 pdp_ai7 pdp_ai7"><svg xmlns="http://www.w3.org/2000/svg"
                                                                                           width="16" height="16"><path
                                                        fill="currentColor"
                                                        d="M1.333 6.333c0-4.117.883-5 5-5 4.118 0 5 .883 5 5 0 4.118-.882 5-5 5-4.117 0-5-.882-5-5"></path><path
                                                        fill="currentColor"
                                                        d="M6.333 12.167c-.416 0-.833.416-.833.833 0 1.667 3.249 1.667 4.167 1.667 4.117 0 5-.883 5-5 0-.918 0-4.167-1.667-4.167-.442.035-.833.417-.833.833 0 4.584-1.25 5.834-5.834 5.834"></path></svg></div></span>
                                </dd>
                            </dl>
                            @endif
                            @if(count($attributes) > 0)
                            @foreach($attributes as $index => $attr)
                            @if($index % 2 == 0)
                            @if($attr->value && $attr->value != '')
                            <dl class="pdp_i5a">
                                <dt class="pdp_i4a"><span class="pdp_a5i">{{ $attr->name }}</span></dt>
                                <dd class="pdp_ai5">
                                    @if($attr->name == "Серия" && isset($attr->seriya_id) && $attr->seriya_id > 0)
                                    <a target="_blank" href="/seriya/?seriya_id={{ $attr->seriya_id }}" style="color: var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @elseif($attr->name == "Тематика" && isset($attr->topic_id) && $attr->topic_id > 0)
                                    <a target="_blank" href="/topic/?topic_id={{ $attr->topic_id }}" style="color: var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @elseif($attr->name == "Тип товара" && isset($attr->product_type_id) && $attr->product_type_id > 0)
                                    <a target="_blank" href="/product_type/?product_type_id={{ $attr->product_type_id }}" style="color: var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @else
                                    {{ $attr->value }}
                                    @endif
                                </dd>
                            </dl>
                            @endif
                            @endif
                            @endforeach
                            @endif
                        </div>
                        <div style="width: calc(50%);">
                            @if(count($attributes) > 0)
                            @foreach($attributes as $index => $attr)
                            @if($index % 2 == 1)
                            @if($attr->value && $attr->value != '')
                            <dl class="pdp_i5a">
                                <dt class="pdp_i4a"><span class="pdp_a5i">{{ $attr->name }}</span></dt>
                                <dd class="pdp_ai5">
                                    @if($attr->name == "Серия" && isset($attr->seriya_id) && $attr->seriya_id > 0)
                                    <a target="_blank" href="/seriya/?seriya_id={{ $attr->seriya_id }}" style="color: var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @elseif($attr->name == "Тематика" && isset($attr->topic_id) && $attr->topic_id > 0)
                                    <a target="_blank" href="/topic/?topic_id={{ $attr->topic_id }}" style="color: var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @elseif($attr->name == "Тип товара" && isset($attr->product_type_id) && $attr->product_type_id > 0)
                                    <a target="_blank" href="/product_type/?product_type_id={{ $attr->product_type_id }}" style="color: var(--textAction); text-decoration: none;">{{ $attr->value }}</a>
                                    @else
                                    {{ $attr->value }}
                                    @endif
                                </dd>
                            </dl>
                            @endif
                            @endif
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <small class="pdp_ia3">Информация о технических характеристиках, комплекте поставки, стране
                    изготовления, внешнем виде и цвете товара носит справочный характер и основывается на последних
                    доступных к моменту публикации сведениях</small>
            </div>


        </div>

        @if(count($reviews) > 0)
        <link rel="stylesheet" href="/assets/sfera/css/product/ui-kit-tabs.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/rp-desktop-web-review-gallery-v2.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/products-desktop-sku-grid.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/rp-all-web-review-product-score-v2.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/rp-desktop-context-questions-statistic-v2.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/rp-all-list-questions.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/rp-all-web-list-reviews-v3.css">
        <link rel="stylesheet" href="/assets/sfera/css/product/review.css">
        <div class="e1" data-widget="row">
            <div class="d6 c8" data-widget="column">
                <div class="uw_a4n" data-widget="separator" style="height: 36px;"></div>
                <div class="uw_a4n" data-widget="separator" style="height: 24px;"></div>
                <div data-widget="paginator" style="min-height: 0px;">
                    <div class="">
                        <div class="" data-widget="webListReviews"> 
                            <div class="mp1_28">
                                <div class="om9_28">
                                    <span class="m8o_28">
                                        Все отзывы:
                                    </span>
                                </div>
                            </div>
                            <div class="pm1_28">
                                <div class="p2m_28">

                                    @foreach($reviews as $review)
                                    <div data-review-uuid="{{ $review->review_id }}"
                                         class="ql7_28">
                                        <div class="q7l_28">
                                            <div class="n1l_28 ln2_28">
                                                <div class="yk0_28 k1y_28 l2n_28"><span>{{ $review->first_letter }}</span></div>
                                                <div>
                                                    <div class="nl2_28"><span class="n2l_28">
                                                        Покупатель
                                                    </span> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lq0_28">
                                                @if($review->formatted_date)
                                                <div class="l0q_28"> {{ $review->formatted_date }}</div>
                                                @endif
                                                <div class="q0l_28">
                                                    <div class="a5d5_3_10-a a5d5_3_10-a0">
                                                        @php $review_rating = $review->rating; @endphp
                                                        @for($star_num = 1; $star_num <= 5; $star_num++)
                                                        @if($review_rating >= $star_num)
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                             viewBox="0 0 24 24" style="color: var(--graphicRating);">
                                                            <path fill="currentColor"
                                                                  d="M9.358 6.136C10.53 4.046 11.117 3 12 3s1.47 1.045 2.643 3.136c.259.462.484 1.038.925 1.354.42.302 1.006.332 1.502.422 2.356.429 3.534.643 3.842 1.457q.034.09.057.182c.208.847-.632 1.581-2.316 3.313-.527.541-.766.798-.872 1.149-.097.325-.05.677.044 1.381.323 2.42.482 3.762-.21 4.31-1.24.98-3.24-.742-4.359-1.259C12.638 18.16 12.33 18 12 18s-.638.16-1.256.445c-1.12.517-3.119 2.24-4.358 1.258-.693-.547-.528-1.889-.205-4.309.094-.704.14-1.056.043-1.381-.105-.351-.345-.607-.872-1.15-1.684-1.73-2.529-2.465-2.32-3.312q.021-.093.056-.182c.308-.814 1.486-1.028 3.842-1.457.496-.09 1.083-.12 1.502-.422.441-.316.666-.893.926-1.354"></path>
                                                        </svg>
                                                        @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                             viewBox="0 0 24 24" style="color: #E5E5E5;">
                                                            <path fill="currentColor"
                                                                  d="M9.358 6.136C10.53 4.046 11.117 3 12 3s1.47 1.045 2.643 3.136c.259.462.484 1.038.925 1.354.42.302 1.006.332 1.502.422 2.356.429 3.534.643 3.842 1.457q.034.09.057.182c.208.847-.632 1.581-2.316 3.313-.527.541-.766.798-.872 1.149-.097.325-.05.677.044 1.381.323 2.42.482 3.762-.21 4.31-1.24.98-3.24-.742-4.359-1.259C12.638 18.16 12.33 18 12 18s-.638.16-1.256.445c-1.12.517-3.119 2.24-4.358 1.258-.693-.547-.528-1.889-.205-4.309.094-.704.14-1.056.043-1.381-.105-.351-.345-.607-.872-1.15-1.684-1.73-2.529-2.465-2.32-3.312q.021-.093.056-.182c.308-.814 1.486-1.028 3.842-1.457.496-.09 1.083-.12 1.502-.422.441-.316.666-.893.926-1.354"></path>
                                                        </svg>
                                                        @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="l8q_28">
                                            <div class="ql1_28 q9l_28">
                                                <div class="l2q_28">
                                                    <div>
                                                        <span class="q1l_28">
                                                            {{ e($review->text) }}&nbsp;
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    @if(!$loop->last)
                                    <div class="pm3_28">
                                        <div class=""> 
                                            <div class="lp9_28">
                                            </div> 
                                        </div> 
                                    </div>
                                    @endif
                                    @endforeach


                                </div>

                            </div>  
                        </div>
                    </div>
                    <div class="ds8">
                        <div></div>
                    </div> 
                </div>
            </div>
            <div class="d1 c8" data-widget="column"></div>
            <div class="d2 c8" data-widget="column">
                <div class="uw_a4n" data-widget="separator" style="height: 24px;"></div>
                <div>
                    @if($reviewsStats['total_count'] > 0)
                    <div class="m4z_28">
                        <div class="zm4_28">
                            <div class="a5d5_3_10-a a5d5_3_10-a2">
                                @php $avg_rating = $reviewsStats['average_rating']; @endphp
                                @for($star_num = 1; $star_num <= 5; $star_num++)
                                @if($avg_rating >= $star_num)
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                     style="color: var(--graphicRating);">
                                    <path fill="currentColor"
                                          d="M9.358 6.136C10.53 4.046 11.117 3 12 3s1.47 1.045 2.643 3.136c.259.462.484 1.038.925 1.354.42.302 1.006.332 1.502.422 2.356.429 3.534.643 3.842 1.457q.034.09.057.182c.208.847-.632 1.581-2.316 3.313-.527.541-.766.798-.872 1.149-.097.325-.05.677.044 1.381.323 2.42.482 3.762-.21 4.31-1.24.98-3.24-.742-4.359-1.259C12.638 18.16 12.33 18 12 18s-.638.16-1.256.445c-1.12.517-3.119 2.24-4.358 1.258-.693-.547-.528-1.889-.205-4.309.094-.704.14-1.056.043-1.381-.105-.351-.345-.607-.872-1.15-1.684-1.73-2.529-2.465-2.32-3.312q.021-.093.056-.182c.308-.814 1.486-1.028 3.842-1.457.496-.09 1.083-.12 1.502-.422.441-.316.666-.893.926-1.354"></path>
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                     style="color: #E5E5E5;">
                                    <path fill="currentColor"
                                          d="M9.358 6.136C10.53 4.046 11.117 3 12 3s1.47 1.045 2.643 3.136c.259.462.484 1.038.925 1.354.42.302 1.006.332 1.502.422 2.356.429 3.534.643 3.842 1.457q.034.09.057.182c.208.847-.632 1.581-2.316 3.313-.527.541-.766.798-.872 1.149-.097.325-.05.677.044 1.381.323 2.42.482 3.762-.21 4.31-1.24.98-3.24-.742-4.359-1.259C12.638 18.16 12.33 18 12 18s-.638.16-1.256.445c-1.12.517-3.119 2.24-4.358 1.258-.693-.547-.528-1.889-.205-4.309.094-.704.14-1.056.043-1.381-.105-.351-.345-.607-.872-1.15-1.684-1.73-2.529-2.465-2.32-3.312q.021-.093.056-.182c.308-.814 1.486-1.028 3.842-1.457.496-.09 1.083-.12 1.502-.422.441-.316.666-.893.926-1.354"></path>
                                </svg>
                                @endif
                                @endfor
                            </div>
                            <div class="z5m_28"><span>{{ $reviewsStats['average_rating'] }} / 5</span></div>
                        </div>

                        <div class="z4m_28">
                            <div class="m6z_28">
                                <div class="m7z_28">5 звёзд</div>
                                <div class="zm6_28">
                                    <div class="z6m_28">
                                        <div class="mz7_28" style="width: {{ $reviewsStats['rating_distribution'][5]['percent'] }}%;"></div>
                                    </div>
                                </div>
                                <div class="zm7_28">{{ $reviewsStats['rating_distribution'][5]['count'] }}</div>
                            </div>
                            <div class="m6z_28">
                                <div class="m7z_28">4 звезды</div>
                                <div class="zm6_28">
                                    <div class="z6m_28">
                                        <div class="mz7_28" style="width: {{ $reviewsStats['rating_distribution'][4]['percent'] }}%;"></div>
                                    </div>
                                </div>
                                <div class="zm7_28">{{ $reviewsStats['rating_distribution'][4]['count'] }}</div>
                            </div>
                            <div class="m6z_28">
                                <div class="m7z_28">3 звезды</div>
                                <div class="zm6_28">
                                    <div class="z6m_28">
                                        <div class="mz7_28" style="width: {{ $reviewsStats['rating_distribution'][3]['percent'] }}%;"></div>
                                    </div>
                                </div>
                                <div class="zm7_28">{{ $reviewsStats['rating_distribution'][3]['count'] }}</div>
                            </div>
                            <div class="m6z_28">
                                <div class="m7z_28">2 звезды</div>
                                <div class="zm6_28">
                                    <div class="z6m_28">
                                        <div class="mz7_28" style="width: {{ $reviewsStats['rating_distribution'][2]['percent'] }}%;"></div>
                                    </div>
                                </div>
                                <div class="zm7_28">{{ $reviewsStats['rating_distribution'][2]['count'] }}</div>

                            </div>
                            <div class="m6z_28">
                                <div class="m7z_28">1 звезда</div>
                                <div class="zm6_28">
                                    <div class="z6m_28">
                                        <div class="mz7_28" style="width: {{ $reviewsStats['rating_distribution'][1]['percent'] }}%;"></div>
                                    </div>
                                </div>
                                <div class="zm7_28">{{ $reviewsStats['rating_distribution'][1]['count'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</main>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav">
    <a href="/" class="mobile-bottom-nav-item active">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Главная</span>
    </a>

    <a href="#" class="mobile-bottom-nav-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        <span>Избранное</span>
    </a>

    <a href="#" class="mobile-bottom-nav-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span>Корзина</span>
        <span class="mobile-nav-badge">3</span>
    </a>

    <a href="#" class="mobile-bottom-nav-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span>Профиль</span>
    </a>
</nav>
@endsection

@push('scripts')
<!-- Swiper JS -->
<script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

<script src="/assets/sfera/js/catalog.js"></script>
<script src="/assets/sfera/js/product.js"></script>
<script src="/assets/sfera/js/product-gallery.js"></script>
@endpush
