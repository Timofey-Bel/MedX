<!-- Левая боковая панель -->
<header>
        <div class="header-left">
            <div class="logo">
                <a href="{{ url('/') }}">
                    <img class="logo" alt="MedX" src="{{ asset('site/modules/medx/showcase/img/logo@2x.png') }}" style="width: 124px; height: 34px;" />
                </a>
            </div>
        </div>
        <div class="search-container">
            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="36" height="36" rx="18" fill="#74B5BC"/>
                <rect width="18" height="18" transform="translate(9 9)" fill="#74B5BC"/>
                <path d="M20.6252 22.4444V23.3333C20.6252 24.8061 19.4499 26 18.0001 26C16.5503 26 15.3751 24.8061 15.3751 23.3333V22.4444M20.6252 22.4444H15.3751M20.6252 22.4444H23.7669C24.1016 22.4444 24.2698 22.4444 24.4053 22.398C24.6642 22.3093 24.8668 22.1028 24.9541 21.8398C25 21.7016 25 21.5302 25 21.1875C25 21.0375 24.9998 20.9626 24.9883 20.8911C24.9665 20.7559 24.9148 20.6279 24.8362 20.5166C24.7947 20.4579 24.7419 20.4042 24.6378 20.2985L24.297 19.9523C24.187 19.8405 24.1252 19.689 24.1252 19.531V16.2222C24.1252 12.7858 21.3829 9.99999 18.0001 10C14.6173 10 11.875 12.7858 11.875 16.2222V19.5311C11.875 19.689 11.8131 19.8405 11.7031 19.9523L11.3623 20.2985C11.2579 20.4045 11.2057 20.4578 11.1641 20.5167C11.0855 20.6279 11.0334 20.7559 11.0116 20.8911C11 20.9626 11 21.0375 11 21.1875C11 21.5302 11 21.7015 11.0459 21.8398C11.1332 22.1028 11.3367 22.3093 11.5956 22.398C11.7311 22.4444 11.8987 22.4444 12.2334 22.4444H15.3751" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="24" cy="12" r="3" fill="#EE0004"/>
            </svg>

            <div class="search-box">
                <input type="text" class="search-input" placeholder="Поиск">
                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="36" height="36" rx="18" fill="#74B5BC"/>
                    <path d="M22.5395 22.5404L25.6567 25.6577L27.2154 27.2164M17.344 24.6186C13.3268 24.6186 10.0703 21.3621 10.0703 17.345C10.0703 13.3278 13.3268 10.0713 17.344 10.0713C21.3611 10.0713 24.6176 13.3278 24.6176 17.345C24.6176 21.3621 21.3611 24.6186 17.344 24.6186Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
</header>
