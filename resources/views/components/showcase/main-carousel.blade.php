{{-- 
    Главный карусель баннеров
    Миграция из legacy: legacy/site/modules/sfera/main_carousel/main_carousel.tpl
--}}
<div class="main-carousel-container">
    <div class="main-carousel">
        @if(count($banners) > 0)
            <button class="carousel-btn carousel-prev" data-carousel="main">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="carousel-track" data-carousel="main">
                @foreach($banners as $index => $banner)
                    <div class="carousel-slide{{ $index === 0 ? ' active' : '' }}">
                        <img src="{{ $banner->url }}" alt="{{ $banner->title ?? $banner->name }}">
                    </div>
                @endforeach
            </div>
            <button class="carousel-btn carousel-next" data-carousel="main">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="carousel-dots" data-carousel="main">
                @foreach($banners as $index => $banner)
                    <button class="carousel-dot{{ $index === 0 ? ' active' : '' }}" data-index="{{ $index }}"></button>
                @endforeach
            </div>
        @else
            <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Баннеры не найдены</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/carousel.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/sfera/js/carousel.js') }}"></script>
@endpush
