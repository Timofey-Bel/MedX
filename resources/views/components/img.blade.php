@props([
    'category' => 'main',
    'subcategory' => null,
    'src' => '',
    'alt' => '',
    'class' => ''
])

@php
    use App\Helpers\ImageHelper;
    $imageUrl = ImageHelper::get($category, $src, $subcategory);
@endphp

<img src="{{ $imageUrl }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => $class]) }} />
