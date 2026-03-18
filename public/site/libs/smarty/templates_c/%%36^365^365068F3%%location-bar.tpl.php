<?php /* Smarty version 2.6.11, created on 2025-12-29 10:00:39
         compiled from medx/tpl/location-bar.tpl */ ?>
<!-- ===== LOCATION BAR ===== -->
<div class="location-bar">
    <div class="header-container">
        <div class="location-content">
            <button class="location-selector" onclick="openCityModal()">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M8 1c-2.8 0-5 2.2-5 5 0 3.8 5 9 5 9s5-5.2 5-9c0-2.8-2.2-5-5-5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    ircle cx="8" cy="6" r="1.5" fill="currentColor"/>
                </svg>
                <span id="selected-city">Москва</span>
            </button>

            <span class="location-separator">|</span>
            <a href="/delivery/"><span class="location-link">Доставка на адрес</span></a>

            <span class="location-separator help-link">|</span>
            <a href="/help/"><span class="location-link help-link">Помощь</span></a>

            <span class="location-separator contacts-link">|</span>
            <a href="/contact/"><span class="location-link contacts-link">Контакты</span></a>

            <span class="location-separator" style="margin-left: auto;"></span>

            <span class="phone-info">☎ +7(495) 656-75-05</span>
            <span class="promo-text">Позвоните нам</span>

            <div class="social-icons">
                <a href="https://t.me/tc_medx" class="social-icon" rel="nofollow">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#29b6f6" d="M24,4C13,4,4,13,4,24s9,20,20,20s20-9,20-20S35,4,24,4z"></path><path fill="#fff" d="M34,15l-3.7,19.1c0,0-0.2,0.9-1.2,0.9c-0.6,0-0.9-0.3-0.9-0.3L20,28l-4-2l-5.1-1.4c0,0-0.9-0.3-0.9-1	c0-0.6,0.9-0.9,0.9-0.9l21.3-8.5c0,0,0.7-0.2,1.1-0.2c0.3,0,0.6,0.1,0.6,0.5C34,14.8,34,15,34,15z"></path><path fill="#b0bec5" d="M23,30.5l-3.4,3.4c0,0-0.1,0.1-0.3,0.1c-0.1,0-0.1,0-0.2,0l1-6L23,30.5z"></path><path fill="#cfd8dc" d="M29.9,18.2c-0.2-0.2-0.5-0.3-0.7-0.1L16,26c0,0,2.1,5.9,2.4,6.9c0.3,1,0.6,1,0.6,1l1-6l9.8-9.1	C30,18.7,30.1,18.4,29.9,18.2z"></path>
                    </svg>
                </a>
                <a href="https://vk.com/medxbook" class="social-icon" rel="nofollow">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#1876d2" d="M24,4C13,4,4,13,4,24s9,20,20,20s20-9,20-20S35,4,24,4z"></path><path fill="#fff" d="M25.2,33.2c-9,0-14.1-6.1-14.3-16.4h4.5c0.1,7.5,3.5,10.7,6.1,11.3V16.8h4.2v6.5c2.6-0.3,5.3-3.2,6.2-6.5h4.2	c-0.7,4-3.7,7-5.8,8.2c2.1,1,5.5,3.6,6.7,8.2h-4.7c-1-3.1-3.5-5.5-6.8-5.9v5.9H25.2z"></path>
                    </svg>
                </a>
                <a href="http://odnoklassniki.ru/tcmedxok" class="social-icon" rel="nofollow">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#FF9800" d="M42,37c0,2.8-2.2,5-5,5H11c-2.8,0-5-2.2-5-5V11c0-2.8,2.2-5,5-5h26c2.8,0,5,2.2,5,5V37z"></path><path fill="#FFF" d="M26.9,30.4c1.5-0.3,2.9-0.9,4.1-1.7c1-0.6,1.3-1.9,0.7-2.9c-0.6-1-1.9-1.3-2.9-0.7c-2.9,1.8-6.7,1.8-9.6,0c-1-0.6-2.3-0.3-2.9,0.7c-0.6,1-0.3,2.3,0.7,2.9c1.3,0.8,2.7,1.4,4.1,1.7l-4,4c-0.8,0.8-0.8,2.1,0,3c0.4,0.4,0.9,0.6,1.5,0.6c0.5,0,1.1-0.2,1.5-0.6l3.9-3.9l3.9,3.9c0.8,0.8,2.1,0.8,3,0c0.8-0.8,0.8-2.1,0-3C30.9,34.4,26.9,30.4,26.9,30.4z M24,10c-3.9,0-7,3.1-7,7c0,3.9,3.1,7,7,7c3.9,0,7-3.1,7-7C31,13.1,27.9,10,24,10z M24,20c-1.7,0-3-1.3-3-3c0-1.7,1.3-3,3-3c1.7,0,3,1.3,3,3C27,18.7,25.7,20,24,20z"></path>
                    </svg>
                </a>

                
                <a href="https://www.youtube.com/channel/UCnNY9gfezJjjeLCx3VfMoYA" class="social-icon" rel="nofollow">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#FF3D00" d="M43.2,33.9c-0.4,2.1-2.1,3.7-4.2,4c-3.3,0.5-8.8,1.1-15,1.1c-6.1,0-11.6-0.6-15-1.1c-2.1-0.3-3.8-1.9-4.2-4C4.4,31.6,4,28.2,4,24c0-4.2,0.4-7.6,0.8-9.9c0.4-2.1,2.1-3.7,4.2-4C12.3,9.6,17.8,9,24,9c6.2,0,11.6,0.6,15,1.1c2.1,0.3,3.8,1.9,4.2,4c0.4,2.3,0.9,5.7,0.9,9.9C44,28.2,43.6,31.6,43.2,33.9z"></path><path fill="#FFF" d="M20 31L20 17 32 24z"></path>
                    </svg>
                </a>
                <a href="https://zen.yandex.ru/id/6229aa597c83ed79b5fcffc5" class="social-icon" rel="nofollow">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 50 50">
                        <path d="M46.894 23.986c.004 0 .007 0 .011 0 .279 0 .545-.117.734-.322.192-.208.287-.487.262-.769C46.897 11.852 38.154 3.106 27.11 2.1c-.28-.022-.562.069-.77.262-.208.192-.324.463-.321.746C26.193 17.784 28.129 23.781 46.894 23.986zM46.894 26.014c-18.765.205-20.7 6.202-20.874 20.878-.003.283.113.554.321.746.186.171.429.266.679.266.03 0 .061-.001.091-.004 11.044-1.006 19.787-9.751 20.79-20.795.025-.282-.069-.561-.262-.769C47.446 26.128 47.177 26.025 46.894 26.014zM22.823 2.105C11.814 3.14 3.099 11.884 2.1 22.897c-.025.282.069.561.262.769.189.205.456.321.734.321.004 0 .008 0 .012 0 18.703-.215 20.634-6.209 20.81-20.875.003-.283-.114-.555-.322-.747C23.386 2.173 23.105 2.079 22.823 2.105zM3.107 26.013c-.311-.035-.555.113-.746.321-.192.208-.287.487-.262.769.999 11.013 9.715 19.757 20.724 20.792.031.003.063.004.094.004.25 0 .492-.094.678-.265.208-.192.325-.464.322-.747C23.741 32.222 21.811 26.228 3.107 26.013z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Location Bar */
    .location-bar {
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid #2a3a52;
        padding: 4px 0;
        /*position: sticky;*/
        top: 0;
        z-index: 999;
    }

    .location-content {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        width: 100%;
        gap: 12px;
        flex-wrap: nowrap;
    }

    .location-selector {
        display: flex;
        align-items: center;
        gap: 6px;
        background: none;
        padding: 4px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 500;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .location-selector svg {
        flex-shrink: 0;
        color: #ffffff;
    }

    .location-separator {
        color: #4a5a7a;
        font-size: 11px;
        flex-shrink: 0;
    }

    .location-link {
        font-size: 15px;
        color: #a0a0a0;
        cursor: pointer;
        transition: color 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .location-link:hover {
        color: #ffffff;
    }

    .phone-info {
        font-size: 15px;
        font-weight: 500;
        color: #ffffff;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .promo-text {
        font-size: 15px;
        color: #a0a0a0;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .social-icons {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .social-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        color: #ffffff;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    @media (max-width: 880px) {
        .promo-text {
            display: none !important;
        }
    }


</style>
