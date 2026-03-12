<li class="category-item @if(isset($current_category_id) && $current_category_id == $category['id']) active @endif">
    <a href="{{ route('catalog.category', ['category_id' => $category['id']]) }}">
        {{ $category['name'] }}
    </a>
    @if (!empty($category['children']))
        <ul class="subcategory-list">
            @foreach ($category['children'] as $child)
                @include('catalog.partials.category_item', ['category' => $child, 'current_category_id' => $current_category_id])
            @endforeach
        </ul>
    @endif
</li>