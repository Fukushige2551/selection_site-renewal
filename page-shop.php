<?php get_header(); ?>

<main id="page-shop" class="c-main p-page-shop">
    <section class="c-bg p-page-shop__bg--top">
        <!-- タイトル start -->
        <h1 class="c-section__title">チラシ・店舗情報一覧</h1>
        <p class="c-section__title--sub">Flyers and shop information</p>
        <!-- タイトル end -->

        <!-- 都道府県セクションへスクロール start -->
        <div class="p-page-shop__scroll">
            <a href="#chiba" class="c-btn p-page-shop__scroll__link">千葉</a>
            <a href="#tokyo" class="c-btn p-page-shop__scroll__link">東京</a>
            <a href="#saitama" class="c-btn p-page-shop__scroll__link">埼玉</a>
        </div>
        <!-- 都道府県セクションへスクロール end -->
    </section>

    <section class="p-page-shop__section" id="chiba">
        <h2 class="c-section__title">千葉県</h2>
        <p class="c-section__title--sub">Chiba</p>

        <!-- 店舗情報のカードを表示 -->
        <ul class="c-shop-card-list">
            <li class="c-shop-card is-chiba is-active">
                <p class="c-shop-card__name">行徳店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ~ 22:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒272-0132</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県市川市湊新田</span>
                                <span class="c-shop-card__detail__address__street">1-6-8</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-390-3336</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-chiba is-active">
                <p class="c-shop-card__name">西船橋店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 22:30</span>
                                <span class="c-shop-card__detail__time__note">（土日<span class="c-shop-card__detail__time__note__highlight">22:00</span>閉店）</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒273-0025</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県船橋市印内町</span>
                                <span class="c-shop-card__detail__address__street">579-1</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-420-3840</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-chiba">
                <p class="c-shop-card__name">西原店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒277-0885</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県柏市西原</span>
                                <span class="c-shop-card__detail__address__street">7-8-1</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-156-8007</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-chiba">
                <p class="c-shop-card__name">花野井店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒277-0812</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県柏市花野井</span>
                                <span class="c-shop-card__detail__address__street">737-8</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-137-0195</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-chiba">
                <p class="c-shop-card__name">しいの木台店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒277-0945</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県柏市しいの木台</span>
                                <span class="c-shop-card__detail__address__street">2-12</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-388-1176</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-chiba">
                <p class="c-shop-card__name">青葉台店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒277-0055</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県柏市青葉台</span>
                                <span class="c-shop-card__detail__address__street">1-2-1</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-171-3570</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-chiba">
                <p class="c-shop-card__name">松戸店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒270-2241</span><br>
                                <span class="c-shop-card__detail__address__prefecture">千葉県松戸市松戸新田</span>
                                <span class="c-shop-card__detail__address__street">418-5</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">047-382-5190</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
        </ul>
    </section>
    <section class="p-page-shop__section" id="tokyo">
        <h2 class="c-section__title">東京都</h2>
        <p class="c-section__title--sub">Tokyo</p>

        <!-- 店舗情報のカードを表示 -->
        <ul class="c-shop-card-list">
            <li class="c-shop-card is-tokyo">
                <p class="c-shop-card__name">西新井店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒123-0852</span><br>
                                <span class="c-shop-card__detail__address__prefecture">東京都足立区関原</span>
                                <span class="c-shop-card__detail__address__street">3-12-11</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">03-6806-3651</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
        </ul>
    </section>
    <section class="p-page-shop__section" id="saitama">
        <h2 class="c-section__title">埼玉県</h2>
        <p class="c-section__title--sub">Saitama</p>

        <!-- 店舗情報のカードを表示 -->
        <ul class="c-shop-card-list">
            <li class="c-shop-card is-saitama">
                <p class="c-shop-card__name">三郷店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒341-0035</span><br>
                                <span class="c-shop-card__detail__address__prefecture">埼玉県三郷市鷹野</span>
                                <span class="c-shop-card__detail__address__street">4-428</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">048-948-1815</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
            <li class="c-shop-card is-saitama">
                <p class="c-shop-card__name">八潮店</p>
                <dl class="c-shop-card__detail">
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">営業時間</dt>
                        <dd class="c-shop-card__detail__content">
                            <time class="c-shop-card__detail__time">
                                <span class="c-shop-card__detail__time__main">9:30 ～ 21:00</span>
                            </time>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">住所</dt>
                        <dd class="c-shop-card__detail__content">
                            <address class="c-shop-card__detail__address">
                                <span class="c-shop-card__detail__address__zip">〒340-0815</span><br>
                                <span class="c-shop-card__detail__address__prefecture">埼玉県八潮市八潮</span>
                                <span class="c-shop-card__detail__address__street">4-10-2</span>
                            </address>
                        </dd>
                    </div>
                    <div class="c-shop-card__detail__item">
                        <dt class="c-shop-card__detail__title">電話番号</dt>
                        <dd class="c-shop-card__detail__content">
                            <tel class="c-shop-card__detail__tel">048-994-1185</tel>
                        </dd>
                    </div>
                </dl>

                <div class="c-shop-card__link--wrapper">
                    <a class="c-shop-card__link u-bg--green" href="">
                        <svg class="c-shop-card__link__icon" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.6994 0H5.65984L5.30157 0.346124L0.358266 5.12558L0 5.47171V15.8112C0 17.5676 1.4788 19 3.30062 19H12.7032C14.5212 19 16 17.5713 16 15.8112V3.18876C16 1.43236 14.5212 0 12.7032 0H12.6994ZM14.7766 15.8112C14.7766 16.9196 13.8466 17.818 12.6994 17.818H3.30062C2.15341 17.818 1.22344 16.9196 1.22344 15.8112V5.95775H4.44021C5.39686 5.95775 6.17056 5.21027 6.17056 4.28605V1.17829H12.7032C13.8504 1.17829 14.7804 2.07674 14.7804 3.18508V15.8076L14.7766 15.8112Z" fill="white"/>
                            <path d="M12.2534 4.901H7.49689V9.49635H12.2534V4.901Z" fill="white"/>
                            <path d="M12.2535 11.3815H3.74274V12.3094H12.2535V11.3815Z" fill="white"/>
                            <path d="M12.2535 13.937H3.74274V14.8649H12.2535V13.937Z" fill="white"/>
                        </svg>
                        <span class="c-shop-card__link__text">店舗チラシ</span>
                    </a>
                    <a class="c-shop-card__link u-bg--blue" href="">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_1061_3012)">
                            <path d="M12.85 11.21C12.18 11.68 11.37 11.95 10.49 11.95C9.61001 11.95 8.80001 11.67 8.13001 11.21C6.96001 11.71 6.24001 12.59 5.81001 13.37C5.23001 14.4 5.68001 15.87 6.69001 15.87H14.29C15.3 15.87 15.75 14.41 15.17 13.37C14.73 12.59 14.01 11.71 12.85 11.21Z" fill="white"/>
                            <path d="M10.49 10.98C12.21 10.98 13.6 9.59002 13.6 7.87002V7.13002C13.6 5.41002 12.21 4.02002 10.49 4.02002C8.77 4.02002 7.38 5.41002 7.38 7.13002V7.87002C7.38 9.59002 8.77 10.98 10.49 10.98Z" fill="white"/>
                            <path d="M22.44 19.95L19.02 16.62C21.97 12.52 21.6 6.76004 17.92 3.08004C13.82 -1.01996 7.16 -1.01996 3.07 3.07004C-1.02 7.16004 -1.02 13.82 3.07 17.91C6.75 21.59 12.51 21.96 16.61 19.01L19.94 22.43C20.65 23.28 21.69 23.33 22.49 22.53C23.3 21.72 23.28 20.65 22.43 19.94L22.44 19.95ZM4.53 16.46C1.24 13.17 1.24 7.83004 4.53 4.53004C7.82 1.24004 13.16 1.24004 16.46 4.53004C19.75 7.82004 19.75 13.16 16.46 16.46C13.17 19.75 7.83 19.75 4.53 16.46Z" fill="white"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_1061_3012">
                            <rect width="23.09" height="23.11" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="c-shop-card__link__text">求人</span>
                    </a>
                </div>
            </li>
        </ul>
    </section>
</main>

<?php get_footer(); ?>