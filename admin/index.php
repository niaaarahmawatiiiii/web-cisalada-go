<?php

require_once "auth.php";

require_once "../config/database.php";

require_once "../includes/functions.php";


// ==========================================
// FUNGSI HITUNG DATA
// ==========================================

function count_data($conn, $table)
{

    $allowed_tables = [

        "news",
        "announcements",
        "agendas",
        "umkm",
        "products",
        "tourism",
        "gallery",
        "documents",
        "complaints",
        "users"

    ];


    if (
        !in_array(
            $table,
            $allowed_tables
        )
    ) {

        return 0;

    }


    $result =
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total
             FROM $table"
        );


    if (
        !$result
    ) {

        return 0;

    }


    $data =
        mysqli_fetch_assoc(
            $result
        );


    return $data["total"] ?? 0;

}


// ==========================================
// STATISTIK KONTEN
// ==========================================

$total_news =
    count_data(
        $conn,
        "news"
    );


$total_announcements =
    count_data(
        $conn,
        "announcements"
    );


$total_agendas =
    count_data(
        $conn,
        "agendas"
    );


$total_umkm =
    count_data(
        $conn,
        "umkm"
    );


$total_products =
    count_data(
        $conn,
        "products"
    );


$total_tourism =
    count_data(
        $conn,
        "tourism"
    );


$total_gallery =
    count_data(
        $conn,
        "gallery"
    );


$total_documents =
    count_data(
        $conn,
        "documents"
    );


$total_users =
    count_data(
        $conn,
        "users"
    );


// ==========================================
// DATA DESA
// ==========================================

$village_result =
    mysqli_query(
        $conn,
        "
        SELECT
            village_name
        FROM village_data
        ORDER BY id ASC
        LIMIT 1
        "
    );


$village_data =
    mysqli_fetch_assoc(
        $village_result
    );

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Admin - Cisalada GO
    </title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- BOOTSTRAP ICON -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        /* ==========================================
           BODY
        ========================================== */

        body {

            background-color: #f5f7fa;

        }



        /* ==========================================
           STAT CARD
        ========================================== */

        .stat-card {

            border: none;

            border-radius: 14px;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .stat-card:hover {

            transform:
                translateY(-3px);

        }



        /* ==========================================
           MENU CARD
        ========================================== */

        .menu-card {

            border: none;

            border-radius: 14px;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .menu-card:hover {

            transform:
                translateY(-3px);

        }



        /* ==========================================
           MENU ICON
        ========================================== */

        .menu-icon {

            width: 50px;

            height: 50px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            background-color: #eaf1ff;

        }



        /* ==========================================
           MENU LINK
        ========================================== */

        .menu-link {

            color: #212529;

            text-decoration: none;

        }


        .menu-link:hover {

            color: #212529;

        }



        /* ==========================================
           DATA DESA
        ========================================== */

        .village-card {

            border: none;

            border-radius: 14px;

        }



        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (
            max-width: 767.98px
        ) {

            .dashboard-container {

                padding-left:
                    15px !important;

                padding-right:
                    15px !important;

            }

        }

    </style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav
    class="navbar
           navbar-dark
           bg-primary"
>

    <div
        class="container-fluid
               px-4"
    >


        <!-- LOGO -->

        <a
            href="index.php"
            class="navbar-brand
                   fw-bold"
        >

            CISALADA GO

        </a>



        <!-- RIGHT -->

        <div
            class="d-flex
                   align-items-center
                   text-white"
        >


            <!-- ADMIN NAME -->

            <div
                class="d-none
                       d-md-block
                       me-3"
            >

                <i
                    class="bi bi-person-circle
                           me-1"
                ></i>


                <?= clean(
                    $_SESSION[
                        "admin_name"
                    ]
                ) ?>


            </div>



            <!-- WEBSITE -->

            <a
                href="<?= BASE_URL ?>index.php"
                class="btn
                       btn-light
                       btn-sm
                       me-2"
            >

                <i
                    class="bi bi-globe
                           me-1"
                ></i>

                Website

            </a>



            <!-- LOGOUT -->

            <a
                href="logout.php"
                class="btn
                       btn-outline-light
                       btn-sm"
                onclick="
                    return confirm(
                        'Yakin ingin logout?'
                    );
                "
            >

                <i
                    class="bi bi-box-arrow-right
                           me-1"
                ></i>

                Logout

            </a>


        </div>


    </div>

</nav>



<!-- ==========================================
     CONTENT
========================================== -->

<div
    class="container-fluid
           dashboard-container
           px-4
           py-4"
>


    <!-- ==========================================
         HEADER
    =========================================== -->

    <div
        class="mb-4"
    >


        <h2
            class="fw-bold
                   mb-1"
        >

            Dashboard

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Selamat datang,
            <?= clean(
                $_SESSION[
                    "admin_name"
                ]
            ) ?>.


            Kelola informasi
            Desa Cisalada
            melalui halaman ini.

        </p>


    </div>



    <!-- ==========================================
         STATISTIK
    =========================================== -->

    <div
        class="row
               g-4"
    >


        <!-- ======================================
             BERITA
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-newspaper
                               fs-3
                               text-primary"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_news ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Berita

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             PENGUMUMAN
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-megaphone
                               fs-3
                               text-danger"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_announcements ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Pengumuman

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             AGENDA
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-calendar-event
                               fs-3
                               text-success"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_agendas ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Agenda

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             UMKM
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-shop
                               fs-3
                               text-warning"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_umkm ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        UMKM

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             PRODUK
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-box-seam
                               fs-3
                               text-success"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_products ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Produk

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             WISATA
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-geo-alt
                               fs-3
                               text-info"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_tourism ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Wisata

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             GALERI
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-images
                               fs-3
                               text-primary"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_gallery ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Galeri

                    </p>


                </div>

            </div>

        </div>



        <!-- ======================================
             DOKUMEN
        ======================================= -->

        <div
            class="col-sm-6
                   col-lg-4
                   col-xl-2"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body"
                >


                    <i
                        class="bi bi-file-earmark-text
                               fs-3
                               text-primary"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        <?= $total_documents ?>

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Dokumen

                    </p>


                </div>

            </div>

        </div>


    </div>



    <!-- ==========================================
         DATA DESA
    =========================================== -->

    <div
        class="mt-5"
    >


        <div
            class="card
                   village-card
                   shadow-sm"
        >

            <div
                class="card-body
                       p-4"
            >


                <div
                    class="d-flex
                           justify-content-between
                           align-items-center
                           flex-wrap
                           gap-3"
                >


                    <div>


                        <h4
                            class="fw-bold
                                   mb-1"
                        >

                            <i
                                class="bi bi-building
                                       text-primary
                                       me-2"
                            ></i>


                            Data Desa

                        </h4>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Informasi dasar dan statistik
                            Desa Cisalada.

                        </p>


                    </div>


                    <a
                        href="data-desa/index.php"
                        class="btn
                               btn-primary"
                    >

                        <i
                            class="bi bi-pencil
                                   me-1"
                        ></i>

                        Kelola Data

                    </a>


                </div>



                <?php if (
                    $village_data
                ): ?>


                    <hr>


                    <div
                        class="d-flex
                               align-items-center"
                    >


                        <i
                            class="bi bi-geo-alt
                                   text-primary
                                   fs-4
                                   me-3"
                        ></i>


                        <div>


                            <div
                                class="fw-semibold"
                            >

                                <?= clean(
                                    $village_data[
                                        "village_name"
                                    ]
                                ) ?>


                            </div>


                            <small
                                class="text-muted"
                            >

                                Klik Kelola Data
                                untuk memperbarui
                                informasi desa.

                            </small>


                        </div>


                    </div>


                <?php endif; ?>


            </div>

        </div>


    </div>



    <!-- ==========================================
         MANAJEMEN KONTEN
    =========================================== -->

    <div
        class="mt-5"
    >


        <h4
            class="fw-bold
                   mb-3"
        >

            Manajemen Konten

        </h4>



        <div
            class="row
                   g-3"
        >


            <!-- ==================================
                 BERITA
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="berita/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-newspaper
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Berita

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola berita desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



        <!-- ==================================
            PROFIL DESA
        =================================== -->

        <div
            class="col-md-4
                col-lg-3"
        >

            <a
                href="profil/index.php"
                class="menu-link"
            >

                <div
                    class="card
                        menu-card
                        shadow-sm
                        h-100"
                >

                    <div
                        class="card-body"
                    >

                        <div
                            class="menu-icon"
                        >

                            <i
                                class="bi bi-building
                                    fs-4
                                    text-primary"
                            ></i>

                        </div>


                        <h5
                            class="fw-bold
                                mt-3"
                        >

                            Profil Desa

                        </h5>


                        <p
                            class="text-muted
                                mb-0"
                        >

                            Kelola profil, sejarah, visi, dan misi desa.

                        </p>


                    </div>

                </div>

            </a>

        </div>



            <!-- ==================================
                 DOKUMEN
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="dokumen/"
                    class="text-decoration-none"
                >

                    <div
                        class="card
                               border-0
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >

                            <i
                                class="bi bi-file-earmark-text
                                       fs-2
                                       text-primary"
                            ></i>

                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Dokumen

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola dokumen publik desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 PENGUMUMAN
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="pengumuman/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-megaphone
                                           fs-4
                                           text-danger"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Pengumuman

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola pengumuman desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 AGENDA
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="agenda/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-calendar-event
                                           fs-4
                                           text-success"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Agenda Desa

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola agenda dan
                                kegiatan desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 GALERI
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="gallery/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-images
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Galeri

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola foto dan video
                                dokumentasi desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 UMKM
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="umkm/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-shop
                                           fs-4
                                           text-warning"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                UMKM

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola data UMKM desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 WISATA
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="wisata/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-geo-alt
                                           fs-4
                                           text-info"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Wisata

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola potensi wisata desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 PENDUDUK
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="penduduk/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-people
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Penduduk

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola data penduduk desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 PENGADUAN
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="pengaduan/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-chat-left-text
                                           fs-4
                                           text-danger"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Pengaduan

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola pengaduan warga.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 PRODUK
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="produk/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-box-seam
                                           fs-4
                                           text-success"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Produk

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola data produk desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 TRANSAKSI
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="transaksi/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-receipt
                                           fs-4
                                           text-success"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Transaksi

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola data transaksi.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 DATA DESA
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="data-desa/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-building
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Data Desa

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola informasi dan
                                statistik desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 STATISTIK DATA DESA
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="data-desa/statistik-dashboard.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-bar-chart-line
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Statistik Data Desa

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Lihat grafik dan rekap
                                perkembangan data desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 PEMERINTAHAN
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="pemerintahan/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-person-badge
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Pemerintahan

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola perangkat
                                pemerintahan desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 AKUN ADMIN
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="akun/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >


                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-people
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Akun Admin

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola akun perangkat
                                desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 TRANSPARANSI
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="transparansi/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >

                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-bar-chart-line
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>

                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Transparansi

                            </h5>

                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola APBDes dan informasi publik desa.

                            </p>

                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 LAYANAN
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="layanan/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >

                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-headset
                                           fs-4
                                           text-success"
                                ></i>

                            </div>

                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Layanan

                            </h5>

                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola informasi layanan desa.

                            </p>

                        </div>

                    </div>

                </a>

            </div>



            <!-- ==================================
                 KONTAK
            =================================== -->

            <div
                class="col-md-4
                       col-lg-3"
            >

                <a
                    href="kontak/index.php"
                    class="menu-link"
                >

                    <div
                        class="card
                               menu-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body"
                        >

                            <div
                                class="menu-icon"
                            >

                                <i
                                    class="bi bi-telephone
                                           fs-4
                                           text-primary"
                                ></i>

                            </div>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Kontak

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Kelola informasi kontak Desa Cisalada.

                            </p>


                        </div>

                    </div>

                </a>

            </div>


        </div>

    </div>



    <!-- ==========================================
         AKSES CEPAT
    =========================================== -->

    <div
        class="mt-5"
    >


        <h4
            class="fw-bold
                   mb-3"
        >

            Akses Cepat

        </h4>



        <div
            class="row
                   g-3"
        >


            <!-- WEBSITE -->

            <div
                class="col-md-4"
            >

                <a
                    href="<?= BASE_URL ?>index.php"
                    class="btn
                           btn-light
                           border
                           w-100
                           text-start
                           p-3"
                >

                    <i
                        class="bi bi-globe
                               text-primary
                               me-2"
                    ></i>

                    Lihat Website Public

                </a>

            </div>



            <!-- GALERI -->

            <div
                class="col-md-4"
            >

                <a
                    href="gallery/index.php"
                    class="btn
                           btn-light
                           border
                           w-100
                           text-start
                           p-3"
                >

                    <i
                        class="bi bi-images
                               text-primary
                               me-2"
                    ></i>

                    Kelola Galeri

                </a>

            </div>



            <!-- AKUN -->

            <div
                class="col-md-4"
            >

                <a
                    href="akun/index.php"
                    class="btn
                           btn-light
                           border
                           w-100
                           text-start
                           p-3"
                >

                    <i
                        class="bi bi-person-plus
                               text-primary
                               me-2"
                    ></i>

                    Kelola Akun Admin

                </a>

            </div>



            <!-- LOGOUT -->

            <div
                class="col-md-4"
            >

                <a
                    href="logout.php"
                    class="btn
                           btn-light
                           border
                           w-100
                           text-start
                           p-3"
                    onclick="
                        return confirm(
                            'Yakin ingin logout?'
                        );
                    "
                >

                    <i
                        class="bi bi-box-arrow-right
                               text-danger
                               me-2"
                    ></i>

                    Logout

                </a>

            </div>


        </div>

    </div>


</div>



<!-- ==========================================
     BOOTSTRAP JS
========================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>