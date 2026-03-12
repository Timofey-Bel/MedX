{{-- 
    Обзоры продукции
    Миграция из legacy: legacy/site/modules/sfera/product_reviews/product_reviews.tpl
--}}
<section class="product-reviews-section">
    <div class="container">
        <h2 class="section-title" style="font-size: 24px;
    line-height: 30px;
    font-weight: 500;
    letter-spacing: -0.01em;
    color: #0d0d0d;
    margin-bottom: 24px;">Обзоры продукции</h2>

        @if(count($reviews) > 0)
            <div class="review-blocks-container">
                <!-- First Horizontal Block -->
                <div class="review-block-horizontal">
                    <!-- Main Review Card (Left - 4/6 width) -->
                    @if(isset($reviews[0]))
                    @php $review0 = is_array($reviews[0]) ? $reviews[0] : (array)$reviews[0]; @endphp
                    <a href="/page/{{ $review0['id'] }}/" class="review-card review-card-main">
                        <div class="review-image-container">
                            @if(!empty($review0['image']))
                            <img src="{{ $review0['image'] }}" alt="{{ $review0['name'] }}">
                            @else
                            <img src="https://via.placeholder.com/800x400" alt="{{ $review0['name'] }}">
                            @endif
                            <div class="review-caption">
                                <h3 class="review-caption-title">{{ $review0['name'] }}</h3>
                            </div>
                        </div>
                    </a>
                    @endif

                    <!-- Side Review Grid (Right - 2/6 width, 2x3 grid) -->
                    <div class="review-side-grid">
                        @if(isset($reviews[1]))
                        @php $review1 = is_array($reviews[1]) ? $reviews[1] : (array)$reviews[1]; @endphp
                        <a href="/page/{{ $review1['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review1['image']))
                                <img src="{{ $review1['image'] }}" alt="{{ $review1['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review1['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review1['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if(isset($reviews[2]))
                        @php $review2 = is_array($reviews[2]) ? $reviews[2] : (array)$reviews[2]; @endphp
                        <a href="/page/{{ $review2['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review2['image']))
                                <img src="{{ $review2['image'] }}" alt="{{ $review2['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review2['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review2['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if(isset($reviews[3]))
                        @php $review3 = is_array($reviews[3]) ? $reviews[3] : (array)$reviews[3]; @endphp
                        <a href="/page/{{ $review3['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review3['image']))
                                <img src="{{ $review3['image'] }}" alt="{{ $review3['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review3['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review3['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if(isset($reviews[4]))
                        @php $review4 = is_array($reviews[4]) ? $reviews[4] : (array)$reviews[4]; @endphp
                        <a href="/page/{{ $review4['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review4['image']))
                                <img src="{{ $review4['image'] }}" alt="{{ $review4['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review4['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review4['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if(isset($reviews[5]))
                        @php $review5 = is_array($reviews[5]) ? $reviews[5] : (array)$reviews[5]; @endphp
                        <a href="/page/{{ $review5['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review5['image']))
                                <img src="{{ $review5['image'] }}" alt="{{ $review5['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review5['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review5['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if(isset($reviews[6]))
                        @php $review6 = is_array($reviews[6]) ? $reviews[6] : (array)$reviews[6]; @endphp
                        <a href="/page/{{ $review6['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review6['image']))
                                <img src="{{ $review6['image'] }}" alt="{{ $review6['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review6['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review6['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
                
                {{--
                <!-- Second Horizontal Block -->
                <div class="review-block-horizontal">
                    <!-- Main Review Card (Left - 4/6 width) -->
                    @if(isset($reviews[7]))
                    @php $review7 = is_array($reviews[7]) ? $reviews[7] : (array)$reviews[7]; @endphp
                    <a href="/page/{{ $review7['id'] }}/" class="review-card review-card-main">
                        <div class="review-image-container">
                            @if(!empty($review7['image']))
                            <img src="{{ $review7['image'] }}" alt="{{ $review7['name'] }}">
                            @else
                            <img src="https://via.placeholder.com/800x400" alt="{{ $review7['name'] }}">
                            @endif
                            <div class="review-caption">
                                <h3 class="review-caption-title">{{ $review7['name'] }}</h3>
                            </div>
                        </div>
                    </a>
                    @endif

                    <!-- Side Review Grid (Right - 2/6 width, 2x1 grid) -->
                    <div class="review-side-grid">
                        @if(isset($reviews[8]))
                        @php $review8 = is_array($reviews[8]) ? $reviews[8] : (array)$reviews[8]; @endphp
                        <a href="/page/{{ $review8['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review8['image']))
                                <img src="{{ $review8['image'] }}" alt="{{ $review8['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review8['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review8['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if(isset($reviews[9]))
                        @php $review9 = is_array($reviews[9]) ? $reviews[9] : (array)$reviews[9]; @endphp
                        <a href="/page/{{ $review9['id'] }}/" class="review-card review-card-small">
                            <div class="review-image-container">
                                @if(!empty($review9['image']))
                                <img src="{{ $review9['image'] }}" alt="{{ $review9['name'] }}">
                                @else
                                <img src="https://via.placeholder.com/400x300" alt="{{ $review9['name'] }}">
                                @endif
                                <div class="review-caption">
                                    <h3 class="review-caption-title">{{ $review9['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
                --}}

            </div>
        @else
            <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Обзоры не найдены</p>
            </div>
        @endif
    </div>
</section>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/product-reviews.css') }}">
@endpush
