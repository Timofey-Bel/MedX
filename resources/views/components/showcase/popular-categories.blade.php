{{-- 
    Блок популярных категорий
    Миграция из legacy: legacy/site/modules/sfera/popular_categories/popular_categories.tpl
    
    ВАЖНО: Используем $popularCategories вместо $categories, 
    так как GlobalDataComposer перезаписывает $categories глобальными данными
--}}
<section class="popular-categories-section">
    <div class="container">
        <h2 class="section-title">Популярные категории</h2>
        @if(isset($popularCategories) && count($popularCategories) > 0)
            <div class="categories-grid">
                @foreach($popularCategories as $category)
                    <a href="/catalog/{{ $category['guid'] ?? '' }}/" class="category-card">
                        <div class="category-image">
                            @if($category['image'] ?? null)
                                <img src="{{ asset($category['image']) }}" alt="{{ $category['title'] ?? 'Категория' }}">
                            @else
                                <img src="{{ asset('assets/sfera/img/category-placeholder.jpg') }}" alt="{{ $category['title'] ?? 'Категория' }}">
                            @endif
                        </div>
                        <div class="category-label">
                            <h3>{{ $category['title'] ?? 'Категория' }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Категории не найдены</p>
            </div>
        @endif
    </div>
</section>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/categories.css') }}">
@endpush
