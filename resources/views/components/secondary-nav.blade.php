<nav class="secondary-nav">
    @foreach($menu_items ?? [] as $item)
    @if(!empty($item['submenu']))
    <div class="secondary-nav-dropdown">
        <button class="secondary-nav-dropdown-trigger">
            {{ $item['title'] }}
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path d="M3 4.5l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="secondary-nav-dropdown-menu">
            @foreach($item['submenu'] as $subitem)
            <a href="{{ $subitem['link'] }}">{{ $subitem['title'] }}</a>
            @endforeach
        </div>
    </div>
    @else
    <a href="{{ $item['link'] }}">{{ $item['title'] }}</a>
    @endif
    @endforeach
</nav>
