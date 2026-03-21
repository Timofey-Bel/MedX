@extends('layouts.app')

@section('title', 'MedX - О нас')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endsection

@section('content')
@include('partials.header')
<main>
    <section class="page-header">
        <div class="page-header-bg-1"></div>
        <div class="page-header-bg-2"></div>
        <div class="container">
            <h2 class="page-subtitle">Наша команда</h2>
        </div>
    </section>
    <section class="team">
        <div class="container">
            <div class="founder-card">
                <div class="founder-photo"></div>
                <div class="founder-info">
                    <div class="founder-role">основатель</div>
                    <div class="founder-name">джавид искендеров</div>
                    <div class="bio-link">
                        <span>биография</span>
                        <img src="{{ asset('site/modules/medx/showcase/img/about/bio-icon.svg') }}" alt="bio-icon"/>
                    </div>
                </div>
            </div>

            <div class="join-btn">стать частью medx</div>

            <div class="team-grid">
                <div class="team-card card-1">
                    <div class="team-photo"></div>
                    <div class="team-info">
                        <div class="team-role">веб-дизайнер</div>
                        <div class="team-name">Кристина в.</div>
                        <div class="bio-link">
                            <span>биография</span>
                            <img src="{{ asset('site/modules/medx/showcase/img/about/bio-icon.svg') }}" alt="bio-icon"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.footer')
@endsection
