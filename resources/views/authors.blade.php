@extends('layouts.app')

@section('title', 'Авторы - Творческий Центр СФЕРА')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="font-size: 32px; margin-bottom: 30px; color: #333;">Авторы</h1>
    
    <div class="authors-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
        @foreach($authors as $author)
            <a href="{{ route('author', ['slug' => $author['slug']]) }}" 
               style="display: block; padding: 20px; background: white; border: 1px solid #e0e0e0; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s; text-align: center;">
                <div style="font-size: 16px; font-weight: 500;">{{ $author['name'] }}</div>
            </a>
        @endforeach
    </div>
</div>

@push('styles')
<style>
.authors-list a:hover {
    background: #f8f9fa;
    border-color: var(--cbPrimaryColor);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush
@endsection
