<?php

/*
|--------------------------------------------------------------------------
| CISALADA GO - GLOBAL PAGE LOADER
|--------------------------------------------------------------------------
| File:
| includes/loader.php
|
| Fungsi:
|
| 1. Full loader hanya saat pertama kali membuka website.
| 2. Full loader saat logo / CISALADA GO diklik.
| 3. Thin loader saat menu navbar lain diklik.
| 4. Dropdown tidak memicu loader.
| 5. Tidak mengubah sistem PHP / database / CRUD.
|--------------------------------------------------------------------------
*/

?>

<style>

    /* ==========================================================
       FULL PAGE LOADER
    =========================================================== */

    #cisalada-full-loader {

        position:
            fixed;

        inset:
            0;

        z-index:
            99999;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        background:
            #ffffff;

        opacity:
            1;

        visibility:
            visible;

        transition:
            opacity 0.55s ease,
            visibility 0.55s ease;

    }


    #cisalada-full-loader.hide {

        opacity:
            0;

        visibility:
            hidden;

        pointer-events:
            none;

    }



    /* ==========================================================
       FULL LOADER CONTENT
    =========================================================== */

    .cisalada-full-loader-content {

        display:
            flex;

        flex-direction:
            column;

        align-items:
            center;

        justify-content:
            center;

        text-align:
            center;

        padding:
            30px;

    }



    /* ==========================================================
       LOGO
    =========================================================== */

    .cisalada-full-loader-logos {

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        gap:
            10px;

        margin-bottom:
            18px;

    }


    .cisalada-full-loader-logo {

        width:
            54px;

        height:
            54px;

        object-fit:
            contain;

        display:
            block;

        animation:
            cisaladaLoaderLogoPulse
            1.6s
            ease-in-out
            infinite;

    }



    /* ==========================================================
       TITLE
    =========================================================== */

    .cisalada-full-loader-title {

        margin:
            0;

        font-family:
            "Segoe UI",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Helvetica Neue",
            Arial,
            sans-serif;

        font-size:
            1.5rem;

        font-weight:
            700;

        line-height:
            1;

    }


    .cisalada-full-loader-title
    .primary {

        color:
            #0d6efd;

    }


    .cisalada-full-loader-title
    .dark {

        color:
            #212529;

    }



    /* ==========================================================
       SUBTITLE
    =========================================================== */

    .cisalada-full-loader-subtitle {

        margin-top:
            10px;

        margin-bottom:
            20px;

        color:
            #6c757d;

        font-family:
            "Segoe UI",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Helvetica Neue",
            Arial,
            sans-serif;

        font-size:
            0.9rem;

    }



    /* ==========================================================
       DOTS
    =========================================================== */

    .cisalada-full-loader-dots {

        display:
            inline-flex;

        gap:
            4px;

        margin-left:
            3px;

    }


    .cisalada-full-loader-dot {

        width:
            4px;

        height:
            4px;

        border-radius:
            50%;

        background:
            #6c757d;

        animation:
            cisaladaLoaderDot
            1.2s
            ease-in-out
            infinite;

    }


    .cisalada-full-loader-dot:nth-child(2) {

        animation-delay:
            0.15s;

    }


    .cisalada-full-loader-dot:nth-child(3) {

        animation-delay:
            0.30s;

    }



    /* ==========================================================
       SPINNER
    =========================================================== */

    .cisalada-full-loader-spinner {

        width:
            38px;

        height:
            38px;

        border:
            3px solid
            #e9ecef;

        border-top-color:
            #0d6efd;

        border-radius:
            50%;

        animation:
            cisaladaLoaderSpin
            0.8s
            linear
            infinite;

    }



    /* ==========================================================
       THIN LOADER
    =========================================================== */

    #cisalada-thin-loader {

        position:
            fixed;

        top:
            0;

        left:
            0;

        width:
            100%;

        height:
            3px;

        z-index:
            100000;

        background:
            transparent;

        opacity:
            0;

        visibility:
            hidden;

        pointer-events:
            none;

    }


    #cisalada-thin-loader.active {

        opacity:
            1;

        visibility:
            visible;

    }


    .cisalada-thin-loader-bar {

        width:
            0%;

        height:
            100%;

        background:
            linear-gradient(
                90deg,
                #0d6efd 0%,
                #4da3ff 50%,
                #0d6efd 100%
            );

        box-shadow:
            0 0 8px
            rgba(
                13,
                110,
                253,
                0.55
            );

        animation:
            cisaladaThinProgress
            0.85s
            ease-in-out
            infinite;

    }



    /* ==========================================================
       FULL LOADER ANIMATION
    =========================================================== */

    @keyframes cisaladaLoaderSpin {

        to {

            transform:
                rotate(360deg);

        }

    }


    @keyframes cisaladaLoaderLogoPulse {

        0%,
        100% {

            transform:
                scale(1);

            opacity:
                1;

        }


        50% {

            transform:
                scale(1.07);

            opacity:
                0.82;

        }

    }


    @keyframes cisaladaLoaderDot {

        0%,
        100% {

            transform:
                translateY(0);

            opacity:
                0.35;

        }


        50% {

            transform:
                translateY(-3px);

            opacity:
                1;

        }

    }



    /* ==========================================================
       THIN PROGRESS
    =========================================================== */

    @keyframes cisaladaThinProgress {

        0% {

            width:
                0%;

        }


        45% {

            width:
                55%;

        }


        75% {

            width:
                82%;

        }


        100% {

            width:
                100%;

        }

    }



    /* ==========================================================
       MOBILE
    =========================================================== */

    @media (
        max-width: 575.98px
    ) {


        .cisalada-full-loader-logo {

            width:
                44px;

            height:
                44px;

        }


        .cisalada-full-loader-title {

            font-size:
                1.25rem;

        }


        .cisalada-full-loader-subtitle {

            font-size:
                0.82rem;

        }


        #cisalada-thin-loader {

            height:
                2px;

        }

    }

</style>



<!-- ==========================================================
     FULL PAGE LOADER
=========================================================== -->

<div
    id="cisalada-full-loader"
    aria-label="Memuat halaman"
    role="status"
>


    <div
        class="cisalada-full-loader-content"
    >


        <!-- ==================================================
             LOGO
        =================================================== -->

        <div
            class="cisalada-full-loader-logos"
        >


            <img
                src="<?= BASE_URL ?>assets/images/logo-kkn.png"
                alt="Logo KKN"
                class="cisalada-full-loader-logo"
                width="54"
                height="54"
            >


            <img
                src="<?= BASE_URL ?>assets/images/logo-desa.png"
                alt="Logo Desa Cisalada"
                class="cisalada-full-loader-logo"
                width="54"
                height="54"
            >


        </div>



        <!-- ==================================================
             BRAND
        =================================================== -->

        <h1
            class="cisalada-full-loader-title"
        >


            <span
                class="primary"
            >

                CISALADA

            </span>


            <span
                class="dark"
            >

                GO

            </span>


        </h1>



        <!-- ==================================================
             SUBTITLE
        =================================================== -->

        <div
            class="cisalada-full-loader-subtitle"
        >

            Memuat informasi Desa Cisalada


            <span
                class="cisalada-full-loader-dots"
            >

                <span
                    class="cisalada-full-loader-dot"
                ></span>

                <span
                    class="cisalada-full-loader-dot"
                ></span>

                <span
                    class="cisalada-full-loader-dot"
                ></span>

            </span>

        </div>



        <!-- ==================================================
             SPINNER
        =================================================== -->

        <div
            class="cisalada-full-loader-spinner"
        ></div>


    </div>


</div>



<!-- ==========================================================
     THIN LOADER
=========================================================== -->

<div
    id="cisalada-thin-loader"
    aria-hidden="true"
>

    <div
        class="cisalada-thin-loader-bar"
    ></div>

</div>



<script>

(function () {

    "use strict";


    /* ==========================================================
       ELEMENT
    =========================================================== */

    const fullLoader =
        document.getElementById(
            "cisalada-full-loader"
        );


    const thinLoader =
        document.getElementById(
            "cisalada-thin-loader"
        );



    /* ==========================================================
       STORAGE KEY
    =========================================================== */

    const LOADER_STATE_KEY =
        "cisalada_go_loader_state";



    /* ==========================================================
       FULL LOADER STATE
       NILAI YANG DIGUNAKAN:

       initial
       brand
       thin
    =========================================================== */

    let loaderState =
        "initial";


    try {

        loaderState =
            sessionStorage.getItem(
                LOADER_STATE_KEY
            )
            ||
            "initial";

    } catch (
        error
    ) {

        loaderState =
            "initial";

    }



    /* ==========================================================
       HIDE FULL LOADER
    =========================================================== */

    function hideFullLoader()
    {

        if (
            !fullLoader
        ) {

            return;

        }


        fullLoader.classList.add(
            "hide"
        );


        setTimeout(
            function () {

                if (
                    fullLoader
                    &&
                    fullLoader.parentNode
                ) {

                    fullLoader.parentNode.removeChild(
                        fullLoader
                    );

                }

            },
            650
        );

    }



    /* ==========================================================
       SHOW FULL LOADER
    =========================================================== */

    function showFullLoader()
    {

        if (
            !fullLoader
        ) {

            return;

        }


        fullLoader.classList.remove(
            "hide"
        );

    }



    /* ==========================================================
       SHOW THIN LOADER
    =========================================================== */

    function showThinLoader()
    {

        if (
            !thinLoader
        ) {

            return;

        }


        thinLoader.classList.add(
            "active"
        );

    }



    /* ==========================================================
       HIDE THIN LOADER
    =========================================================== */

    function hideThinLoader()
    {

        if (
            !thinLoader
        ) {

            return;

        }


        thinLoader.classList.remove(
            "active"
        );

    }



    /* ==========================================================
       CEK APA INI FULL LOADER
    =========================================================== */

    let useFullLoader =
        false;


    if (
        loaderState ===
        "initial"
    ) {

        /*
         * Pertama kali membuka website
         */

        useFullLoader =
            true;


    } else if (
        loaderState ===
        "brand"
    ) {

        /*
         * Datang dari klik logo / CISALADA GO
         */

        useFullLoader =
            true;


    } else {

        /*
         * Datang dari menu navbar biasa
         */

        useFullLoader =
            false;

    }



    /* ==========================================================
       SET STATE SELESAI
    =========================================================== */

    try {

        sessionStorage.setItem(
            LOADER_STATE_KEY,
            "normal"
        );

    } catch (
        error
    ) {

        // Tidak masalah jika sessionStorage gagal.

    }



    /* ==========================================================
       INITIAL DISPLAY
    =========================================================== */

    if (
        useFullLoader
    ) {

        showFullLoader();

    } else {

        hideFullLoader();

    }



    /* ==========================================================
       PAGE LOADED
    =========================================================== */

    window.addEventListener(
        "load",
        function () {


            if (
                useFullLoader
            ) {


                /*
                 * Full loader minimal 1.2 detik
                 */

                setTimeout(
                    function () {

                        hideFullLoader();

                    },
                    1200
                );


            } else {


                /*
                 * Thin loader hilang
                 */

                hideThinLoader();

            }

        }
    );



    /* ==========================================================
       FALLBACK
       Full loader maksimal 8 detik
    =========================================================== */

    if (
        useFullLoader
    ) {


        setTimeout(
            function () {

                hideFullLoader();

            },
            8000
        );

    }



    /* ==========================================================
       HANDLE SEMUA LINK
    =========================================================== */

    function setupLinkLoading()
    {

        const links =
            document.querySelectorAll(
                "a"
            );


        links.forEach(
            function (
                link
            ) {


                if (
                    link.dataset.cisaladaLoaderReady
                    ===
                    "true"
                ) {

                    return;

                }


                link.dataset.cisaladaLoaderReady =
                    "true";



                link.addEventListener(
                    "click",
                    function (
                        event
                    ) {


                        /* ======================================
                           MODIFIER
                        ====================================== */

                        if (
                            event.ctrlKey
                            ||
                            event.metaKey
                            ||
                            event.shiftKey
                            ||
                            event.altKey
                        ) {

                            return;

                        }


                        if (
                            event.button !==
                            0
                        ) {

                            return;

                        }



                        /* ======================================
                           HREF
                        ====================================== */

                        const href =
                            link.getAttribute(
                                "href"
                            );


                        if (
                            !href
                            ||
                            href === "#"
                        ) {

                            return;

                        }



                        /* ======================================
                           NON PAGE LINK
                        ====================================== */

                        if (
                            href.startsWith(
                                "mailto:"
                            )
                            ||
                            href.startsWith(
                                "tel:"
                            )
                            ||
                            href.startsWith(
                                "javascript:"
                            )
                            ||
                            link.hasAttribute(
                                "download"
                            )
                        ) {

                            return;

                        }



                        /* ======================================
                           TARGET BARU
                        ====================================== */

                        const target =
                            link.getAttribute(
                                "target"
                            );


                        if (
                            target
                            &&
                            target !==
                            "_self"
                        ) {

                            return;

                        }



                        /* ======================================
                           URL
                        ====================================== */

                        let targetUrl;


                        try {

                            targetUrl =
                                new URL(
                                    href,
                                    window.location.href
                                );

                        } catch (
                            error
                        ) {

                            return;

                        }



                        /* ======================================
                           EXTERNAL
                        ====================================== */

                        if (
                            targetUrl.origin
                            !==
                            window.location.origin
                        ) {

                            return;

                        }



                        /* ======================================
                           DROPDOWN
                        ====================================== */

                        const isDropdown =
                            link.classList.contains(
                                "cisalada-dropdown-toggle"
                            )
                            ||
                            (
                                link.getAttribute(
                                    "data-bs-toggle"
                                )
                                ===
                                "dropdown"
                            );


                        if (
                            isDropdown
                        ) {

                            return;

                        }



                        /* ======================================
                           LOGO / CISALADA GO
                        ====================================== */

                        const isBrand =
                            link.closest(
                                ".cisalada-brand"
                            )
                            !==
                            null;


                        if (
                            isBrand
                        ) {


                            /*
                             * Tandai halaman berikutnya
                             * agar menampilkan FULL LOADER.
                             */

                            try {

                                sessionStorage.setItem(
                                    LOADER_STATE_KEY,
                                    "brand"
                                );

                            } catch (
                                error
                            ) {

                                // Abaikan.

                            }


                            /*
                             * Tampilkan full loader
                             * sebelum berpindah.
                             */

                            showFullLoader();


                            return;

                        }



                        /* ======================================
                           MENU NAVBAR LAIN
                        ====================================== */

                        const insideNavbar =
                            link.closest(
                                ".cisalada-navbar"
                            )
                            !==
                            null;


                        if (
                            insideNavbar
                        ) {


                            /*
                             * Tandai halaman berikutnya
                             * sebagai navigasi biasa.
                             */

                            try {

                                sessionStorage.setItem(
                                    LOADER_STATE_KEY,
                                    "thin"
                                );

                            } catch (
                                error
                            ) {

                                // Abaikan.

                            }


                            /*
                             * Tampilkan thin loader.
                             */

                            showThinLoader();

                        }

                    },
                    true
                );

            }
        );

    }



    /* ==========================================================
       INIT
    =========================================================== */

    if (
        document.readyState ===
        "loading"
    ) {


        document.addEventListener(
            "DOMContentLoaded",
            setupLinkLoading
        );


    } else {


        setupLinkLoading();

    }



    /* ==========================================================
       BACK / FORWARD
    =========================================================== */

    window.addEventListener(
        "pageshow",
        function () {

            hideThinLoader();

        }
    );


})();

</script>