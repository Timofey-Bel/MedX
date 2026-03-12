{{--
    Компонент отзывов о товаре
    
    Props:
    - $reviews (array): Массив отзывов
    - $reviewsStats (array): Статистика отзывов (average_rating, total_count, rating_distribution)
    - $isAuthenticated (bool): Авторизован ли пользователь
    
    Структура сохраняет все CSS классы из legacy системы
--}}

<div class="product-reviews">
    {{-- Заголовок с общей статистикой --}}
    <div class="reviews-header">
        <h3 class="reviews-title">Отзывы ({{ $reviewsStats['total_count'] }})</h3>
        
        @if($reviewsStats['total_count'] > 0)
            <div class="reviews-rating">
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($reviewsStats['average_rating']))
                            <svg class="star star-filled" width="20" height="20" viewBox="0 0 20 20" fill="#FFB800">
                                <path d="M10 1L12.5 7.5L19 8.5L14.5 13L15.5 19L10 16L4.5 19L5.5 13L1 8.5L7.5 7.5L10 1Z"/>
                            </svg>
                        @elseif($i - 0.5 <= $reviewsStats['average_rating'])
                            <svg class="star star-half" width="20" height="20" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="half-{{ $i }}">
                                        <stop offset="50%" stop-color="#FFB800"/>
                                        <stop offset="50%" stop-color="#E0E0E0"/>
                                    </linearGradient>
                                </defs>
                                <path d="M10 1L12.5 7.5L19 8.5L14.5 13L15.5 19L10 16L4.5 19L5.5 13L1 8.5L7.5 7.5L10 1Z" fill="url(#half-{{ $i }})"/>
                            </svg>
                        @else
                            <svg class="star star-empty" width="20" height="20" viewBox="0 0 20 20" fill="#E0E0E0">
                                <path d="M10 1L12.5 7.5L19 8.5L14.5 13L15.5 19L10 16L4.5 19L5.5 13L1 8.5L7.5 7.5L10 1Z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="rating-value">{{ $reviewsStats['average_rating'] }}</span>
            </div>
        @endif
    </div>

    {{-- Статистика распределения по звездам --}}
    @if($reviewsStats['total_count'] > 0)
        <div class="reviews-stats">
            @foreach([5, 4, 3, 2, 1] as $stars)
                <div class="rating-bar">
                    <span class="rating-bar-label">{{ $stars }} звезд</span>
                    <div class="bar">
                        <div class="fill" style="width: {{ $reviewsStats['rating_distribution'][$stars]['percent'] }}%"></div>
                    </div>
                    <span class="rating-bar-count">{{ $reviewsStats['rating_distribution'][$stars]['count'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Список отзывов --}}
    @if(count($reviews) > 0)
        <div class="reviews-list">
            @foreach($reviews as $review)
                <div class="review-item">
                    <div class="review-avatar">{{ $review['first_letter'] }}</div>
                    <div class="review-content">
                        <div class="review-header">
                            <div class="review-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review['rating'])
                                        <svg class="star star-filled" width="16" height="16" viewBox="0 0 20 20" fill="#FFB800">
                                            <path d="M10 1L12.5 7.5L19 8.5L14.5 13L15.5 19L10 16L4.5 19L5.5 13L1 8.5L7.5 7.5L10 1Z"/>
                                        </svg>
                                    @else
                                        <svg class="star star-empty" width="16" height="16" viewBox="0 0 20 20" fill="#E0E0E0">
                                            <path d="M10 1L12.5 7.5L19 8.5L14.5 13L15.5 19L10 16L4.5 19L5.5 13L1 8.5L7.5 7.5L10 1Z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <div class="review-date">{{ $review['formatted_date'] }}</div>
                        </div>
                        <div class="review-text">{{ $review['text'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($reviewsStats['total_count'] === 0)
        <p class="no-reviews">Пока нет отзывов. Будьте первым!</p>
    @endif

    {{-- Форма добавления отзыва или сообщение для неавторизованных --}}
    @if($isAuthenticated)
        <div class="review-form">
            <h4 class="review-form-title">Оставить отзыв</h4>
            <form action="/reviews/add" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">
                
                <div class="form-group">
                    <label for="review-rating">Ваша оценка:</label>
                    <div class="rating-input">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" required>
                            <label for="rating-{{ $i }}" class="star-label">
                                <svg class="star" width="24" height="24" viewBox="0 0 20 20">
                                    <path d="M10 1L12.5 7.5L19 8.5L14.5 13L15.5 19L10 16L4.5 19L5.5 13L1 8.5L7.5 7.5L10 1Z"/>
                                </svg>
                            </label>
                        @endfor
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="review-text">Ваш отзыв:</label>
                    <textarea name="text" id="review-text" rows="5" required placeholder="Поделитесь своим мнением о товаре"></textarea>
                </div>
                
                <button type="submit" class="btn-submit-review">Отправить отзыв</button>
            </form>
        </div>
    @else
        <div class="review-login-prompt">
            <svg class="prompt-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 5C13.66 5 15 6.34 15 8C15 9.66 13.66 11 12 11C10.34 11 9 9.66 9 8C9 6.34 10.34 5 12 5ZM12 19.2C9.5 19.2 7.29 17.92 6 15.98C6.03 13.99 10 12.9 12 12.9C13.99 12.9 17.97 13.99 18 15.98C16.71 17.92 14.5 19.2 12 19.2Z" fill="currentColor"/>
            </svg>
            <p>Войдите, чтобы оставить отзыв</p>
            <a href="/login" class="btn-login">Войти</a>
        </div>
    @endif
</div>
