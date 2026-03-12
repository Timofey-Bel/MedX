{{-- Конвертировано из legacy: site/modules/sfera/page/page.tpl --}}
@extends('layouts.app')

@section('title', $title ?? 'Творческий Центр СФЕРА')

@push('styles')
    <link rel="stylesheet" href="/assets/sfera/css/page.css">
@endpush

@push('head')
    {{-- CSS из секций страницы --}}
    @if(!empty($sectionCss))
    <style>
        /* Стили контентных секций */
        {!! $sectionCss !!}
    </style>
    @endif
@endpush

@section('content')
<main class="main-content page-content">
    <div class="container">
        {!! $page->content !!}
    </div>
</main>
@endsection

@push('scripts')
    {{-- JS из секций страницы --}}
    @if(!empty($sectionJs))
    <script>
        // JavaScript контентных секций
        {!! $sectionJs !!}
    </script>
    @endif
@endpush
