<?php

/*
|--------------------------------------------------------------------------
| CISALADA GO - PUBLIC NAVBAR
|--------------------------------------------------------------------------
| File:
| includes/navbar.php
|--------------------------------------------------------------------------
*/

?>

<?php

// ==========================================================
// BASE PATH ASSET
// ==========================================================

$cisalada_base_url =
    rtrim(
        BASE_URL,
        "/"
    )
    . "/";


// ==========================================================
// LOGO
// ==========================================================

$logo_kkn =
    $cisalada_base_url
    .
    "assets/images/logo-kkn.png";


$logo_desa =
    $cisalada_base_url
    .
    "assets/images/logo-desa.png";

?>


<style>

    /* ==========================================================
       PUBLIC NAVBAR
    =========================================================== */

    .cisalada-navbar {

        position:
            sticky;

        top:
            0;

        z-index:
            1030;

        background:
            #ffffff;

    }


    /* ==========================================================
       BRAND / LOGO
    =========================================================== */

    .cisalada-brand {

        display:
            flex;

        align-items:
            center;

        gap:
            8px;

        white-space:
            nowrap;

        text-decoration:
            none;

        font-family:
            "Segoe UI",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Helvetica Neue",
            Arial,
            sans-serif !important;

        font-size:
            1.25rem !important;

        font-weight:
            700 !important;

        line-height:
            1 !important;

    }


    .cisalada-brand-logo {

        width:
            34px;

        height:
            34px;

        object-fit:
            contain;

        display:
            block;

        flex-shrink:
            0;

    }


    .cisalada-brand-title {

        display:
            inline-flex;

        align-items:
            center;

        font-family:
            "Segoe UI",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Helvetica Neue",
            Arial,
            sans-serif !important;

        font-size:
            1.25rem !important;

        font-weight:
            700 !important;

        line-height:
            1 !important;

    }


    .cisalada-brand-title
    .text-primary {

        color:
            #0d6efd !important;

        font-family:
            inherit !important;

        font-weight:
            700 !important;

    }


    .cisalada-brand-title
    .text-dark {

        color:
            #212529 !important;

        font-family:
            inherit !important;

        font-weight:
            700 !important;

    }


    /* ==========================================================
       DROPDOWN
    =========================================================== */

    .cisalada-navbar
    .dropdown-menu {

        margin-top:
            8px;

        border:
            1px solid #e9ecef;

        border-radius:
            10px;

        box-shadow:
            0 8px 24px
            rgba(
                0,
                0,
                0,
                0.10
            );

        min-width:
            210px;

        z-index:
            2000;

    }


    .cisalada-navbar
    .dropdown-item {

        padding:
            10px 16px;

        color:
            #495057;

    }


    .cisalada-navbar
    .dropdown-item:hover,
    .cisalada-navbar
    .dropdown-item:focus {

        background:
            #f5f7fa;

        color:
            #0d6efd;

    }


    .cisalada-navbar
    .dropdown-item i {

        width:
            20px;

    }


    /* ==========================================================
       NAV LINK
    =========================================================== */

    .cisalada-navbar
    .nav-link {

        cursor:
            pointer;

        font-family:
            "Segoe UI",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Helvetica Neue",
            Arial,
            sans-serif;

    }


    /* ==========================================================
       MOBILE
    =========================================================== */

    @media (
        max-width: 991.98px
    ) {


        .cisalada-navbar
        .dropdown-menu {

            margin-top:
                0;

            border:
                0;

            border-left:
                2px solid #e9ecef;

            border-radius:
                0;

            box-shadow:
                none;

            padding:
                5px 0 5px 10px;

        }


        .cisalada-navbar
        .dropdown-item {

            padding:
                9px 14px;

        }


        .cisalada-brand-logo {

            width:
                30px;

            height:
                30px;

        }


        .cisalada-brand,
        .cisalada-brand-title {

            font-size:
                1.15rem !important;

        }

    }

</style>



<!-- ==========================================================
     NAVBAR
=========================================================== -->

<nav
    class="navbar
           navbar-expand-lg
           bg-white
           border-bottom
           sticky-top
           cisalada-navbar"
>

    <div
        class="container"
    >


        <!-- ==========================================================
             BRAND / LOGO
        =========================================================== -->

        <a
            href="<?= $cisalada_base_url ?>"
            class="navbar-brand
                   cisalada-brand"
        >


            <!-- ==================================================
                 LOGO KKN
            =================================================== -->

            <img
                src="<?= htmlspecialchars(
                    $logo_kkn,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="Logo KKN"
                class="cisalada-brand-logo"
                width="34"
                height="34"
            >



            <!-- ==================================================
                 LOGO DESA
            =================================================== -->

            <img
                src="<?= htmlspecialchars(
                    $logo_desa,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="Logo Desa Cisalada"
                class="cisalada-brand-logo"
                width="34"
                height="34"
            >



            <!-- ==================================================
                 NAMA WEBSITE
            =================================================== -->

            <span
                class="cisalada-brand-title"
            >

                <span
                    class="text-primary"
                >

                    CISALADA

                </span>

                <span
                    class="text-dark"
                >

                    GO

                </span>

            </span>


        </a>



        <!-- ==========================================================
             MOBILE TOGGLE
        =========================================================== -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span
                class="navbar-toggler-icon"
            ></span>

        </button>



        <!-- ==========================================================
             NAVIGATION
        =========================================================== -->

        <div
            class="collapse
                   navbar-collapse"
            id="mainNavbar"
        >


            <ul
                class="navbar-nav
                       ms-auto
                       align-items-lg-center"
            >


                <!-- ==================================================
                     BERANDA
                =================================================== -->

                <li
                    class="nav-item"
                >

                    <a
                        href="<?= $cisalada_base_url ?>"
                        class="nav-link"
                    >

                        Beranda

                    </a>

                </li>



                <!-- ==================================================
                     PROFIL
                =================================================== -->

                <li
                    class="nav-item
                           dropdown"
                >

                    <a
                        class="nav-link
                               dropdown-toggle
                               cisalada-dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        Profil

                    </a>


                    <ul
                        class="dropdown-menu"
                    >


                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/profil.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-building
                                           me-2"
                                ></i>

                                Tentang Desa

                            </a>

                        </li>


                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/pemerintahan.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-people
                                           me-2"
                                ></i>

                                Pemerintahan Desa

                            </a>

                        </li>


                    </ul>

                </li>



                <!-- ==================================================
                     DATA DESA
                =================================================== -->

                <li
                    class="nav-item"
                >

                    <a
                        href="<?= $cisalada_base_url ?>pages/data-desa.php"
                        class="nav-link"
                    >

                        Data Desa

                    </a>

                </li>



                <!-- ==================================================
                     INFORMASI
                =================================================== -->

                <li
                    class="nav-item
                           dropdown"
                >

                    <a
                        class="nav-link
                               dropdown-toggle
                               cisalada-dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        Informasi

                    </a>


                    <ul
                        class="dropdown-menu"
                    >


                        <!-- BERITA -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/berita.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-newspaper
                                           me-2"
                                ></i>

                                Berita

                            </a>

                        </li>



                        <!-- PENGUMUMAN -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/pengumuman.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-megaphone
                                           me-2"
                                ></i>

                                Pengumuman

                            </a>

                        </li>



                        <!-- AGENDA -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/agenda.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-calendar-event
                                           me-2"
                                ></i>

                                Agenda

                            </a>

                        </li>



                        <!-- PEMISAH -->

                        <li>

                            <hr
                                class="dropdown-divider"
                            >

                        </li>



                        <!-- DOKUMEN -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/dokumen.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-file-earmark-text
                                           me-2"
                                ></i>

                                Dokumen

                            </a>

                        </li>


                    </ul>

                </li>



                <!-- ==================================================
                     LAYANAN
                =================================================== -->

                <li
                    class="nav-item"
                >

                    <a
                        href="<?= $cisalada_base_url ?>pages/layanan.php"
                        class="nav-link"
                    >

                        Layanan

                    </a>

                </li>



                <!-- ==================================================
                     POTENSI DESA
                =================================================== -->

                <li
                    class="nav-item
                           dropdown"
                >

                    <a
                        class="nav-link
                               dropdown-toggle
                               cisalada-dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        Potensi Desa

                    </a>


                    <ul
                        class="dropdown-menu"
                    >


                        <!-- UMKM -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/umkm.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-shop
                                           me-2"
                                ></i>

                                UMKM

                            </a>

                        </li>



                        <!-- PRODUK -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/produk.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-box-seam
                                           me-2"
                                ></i>

                                Produk

                            </a>

                        </li>



                        <!-- WISATA -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/wisata.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-geo-alt
                                           me-2"
                                ></i>

                                Wisata

                            </a>

                        </li>



                        <!-- PETA -->

                        <li>

                            <a
                                href="<?= $cisalada_base_url ?>pages/peta.php"
                                class="dropdown-item"
                            >

                                <i
                                    class="bi bi-map
                                           me-2"
                                ></i>

                                Peta Potensi Desa

                            </a>

                        </li>


                    </ul>

                </li>



                <!-- ==================================================
                     TRANSPARANSI
                =================================================== -->

                <li
                    class="nav-item"
                >

                    <a
                        href="<?= $cisalada_base_url ?>pages/transparansi.php"
                        class="nav-link"
                    >

                        Transparansi

                    </a>

                </li>



                <!-- ==================================================
                     GALERI
                =================================================== -->

                <li
                    class="nav-item"
                >

                    <a
                        href="<?= $cisalada_base_url ?>pages/galeri.php"
                        class="nav-link"
                    >

                        Galeri

                    </a>

                </li>



                <!-- ==================================================
                     KONTAK
                =================================================== -->

                <li
                    class="nav-item"
                >

                    <a
                        href="<?= $cisalada_base_url ?>pages/kontak.php"
                        class="nav-link"
                    >

                        Kontak

                    </a>

                </li>



                <!-- ==================================================
                     LOGIN ADMIN
                =================================================== -->

                <li
                    class="nav-item
                           ms-lg-2
                           mt-2
                           mt-lg-0"
                >

                    <a
                        href="<?= $cisalada_base_url ?>admin/login.php"
                        class="btn
                               btn-primary
                               btn-sm
                               px-3"
                    >

                        <i
                            class="bi bi-person-lock
                                   me-1"
                        ></i>

                        Login Admin

                    </a>

                </li>


            </ul>


        </div>

    </div>

</nav>



<!-- ==========================================================
     DROPDOWN FALLBACK
=========================================================== -->

<script>

    (function () {

        "use strict";


        function closeAllDropdowns(
            exceptItem
        ) {

            const dropdowns =
                document.querySelectorAll(
                    ".cisalada-navbar .nav-item.dropdown"
                );


            dropdowns.forEach(
                function (
                    item
                ) {

                    if (
                        item !==
                        exceptItem
                    ) {

                        item.classList.remove(
                            "show"
                        );


                        const menu =
                            item.querySelector(
                                ".dropdown-menu"
                            );


                        if (
                            menu
                        ) {

                            menu.classList.remove(
                                "show"
                            );

                        }


                        const toggle =
                            item.querySelector(
                                ".cisalada-dropdown-toggle"
                            );


                        if (
                            toggle
                        ) {

                            toggle.setAttribute(
                                "aria-expanded",
                                "false"
                            );

                        }

                    }

                }
            );

        }



        function initDropdownFallback() {

            const toggles =
                document.querySelectorAll(
                    ".cisalada-navbar .cisalada-dropdown-toggle"
                );


            if (
                !toggles.length
            ) {

                return;

            }


            toggles.forEach(
                function (
                    toggle
                ) {


                    if (
                        toggle.dataset.cisaladaReady
                        ===
                        "true"
                    ) {

                        return;

                    }


                    toggle.dataset.cisaladaReady =
                        "true";


                    toggle.addEventListener(
                        "click",
                        function (
                            event
                        ) {


                            event.preventDefault();

                            event.stopPropagation();


                            const parent =
                                toggle.closest(
                                    ".nav-item.dropdown"
                                );


                            if (
                                !parent
                            ) {

                                return;

                            }


                            const menu =
                                parent.querySelector(
                                    ".dropdown-menu"
                                );


                            if (
                                !menu
                            ) {

                                return;

                            }


                            const alreadyOpen =
                                parent.classList.contains(
                                    "show"
                                );


                            closeAllDropdowns(
                                parent
                            );


                            if (
                                alreadyOpen
                            ) {


                                parent.classList.remove(
                                    "show"
                                );


                                menu.classList.remove(
                                    "show"
                                );


                                toggle.setAttribute(
                                    "aria-expanded",
                                    "false"
                                );


                            } else {


                                parent.classList.add(
                                    "show"
                                );


                                menu.classList.add(
                                    "show"
                                );


                                toggle.setAttribute(
                                    "aria-expanded",
                                    "true"
                                );

                            }

                        },
                        true
                    );


                }
            );


            // ==================================================
            // KLIK DI LUAR
            // ==================================================

            document.addEventListener(
                "click",
                function (
                    event
                ) {


                    if (
                        !event.target.closest(
                            ".cisalada-navbar .nav-item.dropdown"
                        )
                    ) {

                        closeAllDropdowns(
                            null
                        );

                    }

                }
            );


            // ==================================================
            // ESC
            // ==================================================

            document.addEventListener(
                "keydown",
                function (
                    event
                ) {


                    if (
                        event.key ===
                        "Escape"
                    ) {

                        closeAllDropdowns(
                            null
                        );

                    }

                }
            );


        }



        // ==================================================
        // INITIALIZE
        // ==================================================

        if (
            document.readyState ===
            "loading"
        ) {

            document.addEventListener(
                "DOMContentLoaded",
                initDropdownFallback
            );

        } else {

            initDropdownFallback();

        }


    })();

</script>