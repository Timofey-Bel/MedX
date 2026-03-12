@extends('layouts.app')

@section('title', 'Творческий Центр СФЕРА Интернет-магазин')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/categories.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/top10-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/product-reviews.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/catalog.css') }}">
@endpush

@section('content')
<!-- Main Content -->
<main class="main-content">


    
    <!-- Triple Carousel Block -->
    <section class="triple-carousel-section">
        <div class="container">
            <div class="triple-carousel-wrapper">

                @include('components.showcase.main-carousel', ['banners' => $mainBanners])

                {{--<!-- Side Carousels (1/4 width) -->
                <div class="side-carousels-container">
                    @include('components.showcase.product-carousel', ['products' => $productCarousel])
                    @include('components.showcase.promo-carousel', ['products' => $promoCarousel])
                </div>
            </div>--}}
        </div>
    </section>        
    


    <!-- Popular Categories Block -->
    @include('components.showcase.popular-categories', ['categories' => $popularCategories])

    <!-- TOP-10 Slider Block -->
    @include('components.showcase.top10-slider', ['products' => $top10Products, 'cart' => $cart, 'favorites' => $favorites])

    <!-- Product Reviews Section -->
    @include('components.showcase.product-reviews', ['reviews' => $productReviews])

    <!-- Product Grid -->
    <section class="product-grid-section">
        <div class="container">
            <h2 class="section-title" style="font-size: 24px;
    line-height: 30px;
    font-weight: 500;
    letter-spacing: -0.01em;
    color: #0d0d0d;
    margin-bottom: 24px;">Новинки</h2>
            @include('components.showcase.random-products', ['products' => $randomProducts, 'cart' => $cart, 'favorites' => $favorites])
        </div>
    </section>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('assets/sfera/js/carousel.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/top10-slider.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/showcase-init.js') }}"></script>
@endpush
