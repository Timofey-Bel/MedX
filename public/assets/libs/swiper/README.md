# Swiper.js Installation Instructions

## Скачивание Swiper.js

Необходимо скачать Swiper.js версии 11.x с официального сайта и разместить файлы в этой директории.

### Вариант 1: Скачать с официального сайта (рекомендуется)

1. Перейдите на https://swiperjs.com/get-started
2. Нажмите "Download Swiper" или перейдите в раздел "Download assets"
3. Скачайте архив с последней версией (11.x)
4. Извлеките из архива следующие файлы:
   - `swiper-bundle.min.css` → разместите здесь
   - `swiper-bundle.min.js` → разместите здесь

### Вариант 2: Скачать напрямую с CDN

Можно скачать файлы напрямую с CDN:

**CSS:**
```
https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css
```

**JavaScript:**
```
https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js
```

Сохраните эти файлы в текущую директорию (`public/assets/libs/swiper/`).

### Вариант 3: Использовать CDN напрямую (временное решение)

Если нужно быстро протестировать, можно временно использовать CDN, изменив пути в Blade шаблоне:

```php
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endpush
```

**Примечание:** Для production рекомендуется использовать локальные файлы (Вариант 1 или 2).

## Проверка установки

После размещения файлов в этой директории должна быть следующая структура:

```
public/assets/libs/swiper/
├── README.md (этот файл)
├── swiper-bundle.min.css
└── swiper-bundle.min.js
```

Проверьте доступность файлов, открыв в браузере:
- http://sfera/assets/libs/swiper/swiper-bundle.min.css
- http://sfera/assets/libs/swiper/swiper-bundle.min.js

## Лицензия

Swiper.js распространяется под лицензией MIT.
Официальный сайт: https://swiperjs.com/
GitHub: https://github.com/nolimits4web/swiper
