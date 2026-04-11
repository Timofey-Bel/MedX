@extends('layouts.app')

@section('title', 'MedX Создать тест')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_tests.css') }}">
@endsection

@section('content')
    @include('partials.main_header')
    @include('partials.main_mobile_menu')

    <div class="overlay" id="overlay"></div>

    <main>
        <section>
            <h1 class="breadcrumbs-title">Тесты</h1>
            <nav class="breadcrumbs">
                <a class="active" href="#">Создать тест</a>
                <a href="#">Cтатистика</a>
                <a href="#">решённые тесты</a>

                <!-- <a href="#"><i class="heart-icon-small"></i> избранное</a> -->
            </nav>
        </section>

        <section class="accordion-section">
            <div class="form-group name-field">
                <label>Название</label>
                <input type="text" placeholder="Придумайте название теста">
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span class="accordion-question">Статус</span>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        <label class="checkbox-item">
                            <input type="checkbox" value="correct">
                            <span>Отвечены верно</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" value="incorrect">
                            <span>Отвечены неверно</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" value="unsolved">
                            <span>Ещё нерешённые</span>
                        </label>
                        <div class="dropdown-divider"></div>
                        <label class="checkbox-item select-all">
                            <input type="checkbox" class="select-all-checkbox">
                            <span>Выбрать все</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span class="accordion-question">Сложность</span>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        <label class="checkbox-item">
                            <input type="checkbox" value="easy">
                            <span>Лёгкие</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" value="medium">
                            <span>Средние</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" value="hard">
                            <span>Сложные</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span class="accordion-question">Дисциплины</span>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer disciplines-answer">
                        <div class="discipline-category">
                            <div class="category-header">
                                <span>Фундаментальные дисциплины</span>
                                <svg width="10" height="5" viewBox="0 0 10 5" fill="none">
                                    <path d="M1 1L5 4L9 1" stroke="#419AA4" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>

                        <div class="discipline-items">
                            <div class="discipline-group">
                                <label class="checkbox-item select-all">
                                    <input type="checkbox" class="category-select-all">
                                    <span>Выбрать все</span>
                                </label>
                            </div>

                            <div class="discipline-group">
                                <div class="discipline-main">
                                    <label class="checkbox-item">
                                        <input type="checkbox" value="anatomy">
                                        <span>Анатомия</span>
                                    </label>
                                    <!-- <svg class="expand-icon" width="8" height="4" viewBox="0 0 8 4" fill="none">
                                        <path d="M1 1L4 3L7 1" stroke="#28282F" stroke-width="1.5"/>
                                    </svg> -->
                                </div>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="histology">
                                    <span>Гистология</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="embryology">
                                    <span>Эмбриология</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="cytology">
                                    <span>Цитология</span>
                                </label>
                            </div>

                            <div class="discipline-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" value="physiology">
                                    <span>Физиология</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="biochemistry">
                                    <span>Биохимия</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="microbiology">
                                    <span>Микробиология</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="immunology">
                                    <span>Иммунология</span>
                                </label>
                            </div>

                            <div class="discipline-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" value="pathoanatomy">
                                    <span>Патологическая анатомия</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="pathophysiology">
                                    <span>Патологическая физиология</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="pharmacology">
                                    <span>Фармакология</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="medbiology">
                                    <span>Медицинская биология</span>
                                </label>
                            </div>

                            <div class="discipline-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" value="medphysics">
                                    <span>Медицинская физика</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" value="medgenetics">
                                    <span>Медицинская генетика</span>
                                </label>
                            </div>
                        </div>

                        <div class="discipline-category">
                            <div class="category-header">
                                <span>Клинические дисциплины</span>
                                <svg width="10" height="5" viewBox="0 0 10 5" fill="none">
                                    <path d="M1 1L5 4L9 1" stroke="#419AA4" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <span class="accordion-question">Количество вопросов</span>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer quantity">
                        <p class="helper-text">Максимальное количество вопросов в тесте — 100</p>
                        <div class="counter-display">
                            <input type="number" class="counter-input" value="0" min="0" max="100">
                            <span class="counter-separator">/</span>
                            <span class="counter-max">100</span>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn-create-test">создать тест</button>
        </section>

        <section class="motivation-block">
            <div class="vector-26"></div>
            <div class="vector-27"></div>
            <h2 class="motivation-title">Мотивация</h2>
            <p class="motivation-text">Текст текст текст текст текст текст текст Текст текст текст текст текст текст текст</p>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/medx/main_script.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.select-all-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const answer = this.closest('.accordion-answer');
                    const checkboxes = answer.querySelectorAll('input[type="checkbox"]:not(.select-all-checkbox):not(.category-select-all)');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            });

            document.querySelectorAll('.category-select-all').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const items = this.closest('.accordion-answer').querySelector('.discipline-items');
                    const checkboxes = items.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            });

            document.querySelectorAll('.discipline-main .expand-icon').forEach(icon => {
                icon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const group = this.closest('.discipline-group');
                    group.classList.toggle('expanded');
                });
            });

            document.querySelectorAll('.discipline-subitems .expand-icon').forEach(icon => {
                icon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const group = this.closest('.discipline-group');
                    group.classList.toggle('nested-expanded');
                });
            });

            document.querySelectorAll('.category-header').forEach(header => {
                header.addEventListener('click', function() {
                    const items = this.closest('.discipline-category').nextElementSibling;
                    if (items && items.classList.contains('discipline-items')) {
                        items.style.display = items.style.display === 'none' ? 'flex' : 'none';
                    }
                });
            });
        });
    </script>
@endsection
