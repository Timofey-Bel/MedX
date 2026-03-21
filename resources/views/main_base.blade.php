@extends('layouts.app')

@section('title', 'MedX База знаний')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_base.css') }}">
@endsection

@section('content')
    @include('partials.main_header')
    @include('partials.main_mobile_menu')

    <div class="overlay" id="overlay"></div>

    <main>
        <section>
            <h1 class="breadcrumbs-title">База знаний</h1>
            <nav class="breadcrumbs">
                <a class="active" href="{{ url('/main_base') }}">Фундаментальные дисциплины</a>
                <a href="{{ url('/main_base_clinical') }}">Клинические дисциплины</a>
                <a href="#">Медфакты</a>
                <a href="#">Изучённые</a>
                <a href="#"><i class="heart-icon-small"></i> Избранное</a>
            </nav>
        </section>

        <section class="disciplines-tags">
            <button class="tag-btn active">
                <img src="{{ asset('images/medical/human-organs-1@2x.png') }}" alt="Анатомия" class="tag-icon">
                Анатомия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/biopsy-1@2x.png') }}" alt="Гистология" class="tag-icon">
                Гистология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/embryo-1@2x.png') }}" alt="Эмбриология" class="tag-icon">
                Эмбриология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/cell-1@2x.png') }}" alt="Цитология" class="tag-icon">
                Цитология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/ecg-1@2x.png') }}" alt="Физиология" class="tag-icon">
                Физиология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/biochemistry@2x.png') }}" alt="Биохимия" class="tag-icon">
                Биохимия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/bacterias-1@2x.png') }}" alt="Микробиология" class="tag-icon">
                Микробиология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/red-blood-cells-1@2x.png') }}" alt="Иммунология" class="tag-icon">
                Иммунология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/cancer-cell-1@2x.png') }}" alt="Патологическая анатомия" class="tag-icon">
                Патологическая анатомия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/electrocardiogram-1@2x.png') }}" alt="Патологическая физиология" class="tag-icon">
                Патологическая физиология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/pill-1@2x.png') }}" alt="Фармакология" class="tag-icon">
                Фармакология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/dna-2@2x.png') }}" alt="Медицинская биология" class="tag-icon">
                Медицинская биология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/dna-2@2x.png') }}" alt="Медицинская генетика" class="tag-icon">
                Медицинская генетика
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/ultrasound-1@2x.png') }}" alt="Медицинская физика" class="tag-icon">
                Медицинская физика
            </button>
        </section>

        <section class="knowledge-grid">
            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/Rectangle-126.png') }}" alt="Фармакокинетика">
                </div>
                <div class="card-info">
                    <span class="card-category">Фармакология /</span>
                    <h3 class="card-title">Фармакокинетика: всасывание, распределение, метаболизм, выведение</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/Rectangle-127.png') }}" alt="Гастрит">
                </div>
                <div class="card-info">
                    <span class="card-category">Патологическая анатомия /</span>
                    <h3 class="card-title">Гастрит и язвенная болезнь желудка и 12-перстной кишки</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/Rectangle-128.png') }}" alt="Черепные нервы">
                </div>
                <div class="card-info">
                    <span class="card-category">Анатомия /</span>
                    <h3 class="card-title">Черепные нервы</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/Rectangle-131.png') }}" alt="Гликолиз">
                </div>
                <div class="card-info">
                    <span class="card-category">Биохимия /</span>
                    <h3 class="card-title">Гликолиз: ферменты и этапы</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/Rectangle-129.png') }}" alt="Генетика">
                </div>
                <div class="card-info">
                    <span class="card-category">Медицинская генетика /</span>
                    <h3 class="card-title">Генетические варианты: мутации, полиморфизмы</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/Rectangle-130.png') }}" alt="Микробиология">
                </div>
                <div class="card-info">
                    <span class="card-category">Микробиология /</span>
                    <h3 class="card-title">Спирохеты и микоплазмы: Treponema pallidum; Borrelia burgdorferi; Leptospira...</h3>
                </div>
            </div>
        </section>

        <div class="load-more">
            <button class="btn-load-more">Загрузить ещё</button>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
