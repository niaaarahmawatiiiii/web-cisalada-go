<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


$page_title =
    "UMKM Desa";


// ======================================================
// AMBIL DATA UMKM
// ======================================================

$query =
    mysqli_query(
        $conn,
        "
        SELECT
            umkm.*,

            (
                SELECT COUNT(*)
                FROM products
                WHERE products.umkm_id = umkm.id
            ) AS total_products

        FROM umkm

        ORDER BY
            umkm.created_at DESC,
            umkm.id DESC
        "
    );

?>

<!DOCTYPE html>

<html
    lang="id"
>

<head>

    <meta
        charset="UTF-8"
    >


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>

        <?= clean($page_title) ?>

        - Cisalada GO

    </title>



    <!-- ==================================================
         BOOTSTRAP
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >



    <!-- ==================================================
         BOOTSTRAP ICON
    ================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >



    <style>

        body {

            background:
                #f8f9fa;

            color:
                #212529;

        }



        /* ==================================================
           HERO
        ================================================== */

        .page-hero {

            background:
                #f8f9fa;

            padding:
                55px 0 45px;

        }



        /* ==================================================
           CARD
        ================================================== */

        .umkm-card {

            border:
                0;

            border-radius:
                18px;

            overflow:
                hidden;

            height:
                100%;

            background:
                #ffffff;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .umkm-card:hover {

            transform:
                translateY(-5px);

            box-shadow:
                0 12px 30px
                rgba(
                    0,
                    0,
                    0,
                    0.09
                ) !important;

        }



        /* ==================================================
           FOTO
        ================================================== */

        .umkm-image {

            width:
                100%;

            height:
                220px;

            object-fit:
                cover;

            display:
                block;

        }


        .umkm-placeholder {

            width:
                100%;

            height:
                220px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef1f5;

        }


        .umkm-placeholder i {

            font-size:
                60px;

            color:
                #adb5bd;

        }



        /* ==================================================
           BADGE
        ================================================== */

        .category-badge {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                5px;

            font-size:
                0.78rem;

            padding:
                7px 11px;

            border-radius:
                8px;

        }



        /* ==================================================
           NAME
        ================================================== */

        .umkm-name {

            font-size:
                1.25rem;

            line-height:
                1.3;

        }



        /* ==================================================
           META
        ================================================== */

        .umkm-meta {

            color:
                #6c757d;

            font-size:
                0.9rem;

        }


        .umkm-meta i {

            width:
                18px;

        }



        /* ==================================================
           PHONE
        ================================================== */

        .phone-box {

            padding:
                10px 12px;

            border:
                1px solid #e9ecef;

            border-radius:
                10px;

            background:
                #f8f9fa;

        }


        .phone-box i {

            color:
                #198754;

        }



        /* ==================================================
           PRODUCT COUNT
        ================================================== */

        .product-count-box {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                10px 12px;

            border:
                1px solid #e9ecef;

            border-radius:
                10px;

            background:
                #f8f9fa;

            font-size:
                0.88rem;

            color:
                #495057;

        }



        /* ==================================================
           BUTTON
        ================================================== */

        .action-button {

            min-height:
                44px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                7px;

            border-radius:
                10px;

            font-weight:
                600;

        }



        .detail-button {

            background:
                #0d6efd;

            color:
                #ffffff;

            border:
                1px solid #0d6efd;

        }


        .detail-button:hover {

            background:
                #0b5ed7;

            border-color:
                #0a58ca;

            color:
                #ffffff;

        }



        .product-button {

            background:
                #ffffff;

            color:
                #198754;

            border:
                1px solid #198754;

        }


        .product-button:hover {

            background:
                #198754;

            color:
                #ffffff;

            border-color:
                #198754;

        }



        /* ==================================================
           EMPTY
        ================================================== */

        .empty-icon {

            font-size:
                60px;

        }



        /* ==================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 767.98px
        ) {

            .umkm-image {

                height:
                    210px;

            }


            .umkm-placeholder {

                height:
                    210px;

            }

        }

    </style>

</head>


<body>


<!-- ======================================================
     NAVBAR
====================================================== -->

<?php

include "../includes/navbar.php";

?>



<!-- ======================================================
     BREADCRUMB
====================================================== -->

<div
    class="container
           pt-4"
>


    <nav
        aria-label="breadcrumb"
    >


        <ol
            class="breadcrumb
                   mb-0"
        >


            <li
                class="breadcrumb-item"
            >

                <a
                    href="<?= BASE_URL ?>"
                    class="text-decoration-none"
                >

                    Beranda

                </a>

            </li>


            <li
                class="breadcrumb-item"
            >

                Potensi Desa

            </li>


            <li
                class="breadcrumb-item
                       active"
                aria-current="page"
            >

                UMKM

            </li>


        </ol>


    </nav>


</div>



<!-- ======================================================
     HERO
====================================================== -->

<section
    class="page-hero"
>


    <div
        class="container
               text-center"
    >


        <div
            class="mb-2"
        >

            <span
                class="text-primary
                       fw-semibold"
            >

                POTENSI DESA

            </span>

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            UMKM Desa Cisalada

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 720px;
            "
        >

            Kenali berbagai usaha masyarakat
            Desa Cisalada dan temukan informasi,
            produk, serta kontak usahanya.

        </p>


    </div>


</section>



<!-- ======================================================
     DAFTAR UMKM
====================================================== -->

<section
    class="py-5"
>


    <div
        class="container"
    >


        <?php if (
            $query
            &&
            mysqli_num_rows(
                $query
            ) > 0
        ): ?>


            <div
                class="row
                       g-4"
            >


                <?php while (
                    $umkm =
                        mysqli_fetch_assoc(
                            $query
                        )
                ): ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <div
                            class="card
                                   umkm-card
                                   shadow-sm"
                        >


                            <!-- ==================================
                                 FOTO
                            =================================== -->

                            <?php if (
                                !empty(
                                    $umkm["photo"]
                                )
                            ): ?>


                                <img
                                    src="<?= BASE_URL ?>assets/uploads/umkm/<?= rawurlencode(basename($umkm["photo"])) ?>"
                                    alt="<?= clean($umkm["name"]) ?>"
                                    class="umkm-image"
                                >


                            <?php else: ?>


                                <div
                                    class="umkm-placeholder"
                                >

                                    <i
                                        class="bi bi-shop"
                                    ></i>

                                </div>


                            <?php endif; ?>



                            <!-- ==================================
                                 BODY
                            =================================== -->

                            <div
                                class="card-body
                                       p-4
                                       d-flex
                                       flex-column"
                            >


                                <!-- KATEGORI -->

                                <?php if (
                                    !empty(
                                        $umkm["category"]
                                    )
                                ): ?>


                                    <div
                                        class="mb-3"
                                    >

                                        <span
                                            class="badge
                                                   bg-primary
                                                   category-badge"
                                        >

                                            <i
                                                class="bi bi-tag-fill"
                                            ></i>

                                            <?= clean(
                                                $umkm["category"]
                                            ) ?>

                                        </span>

                                    </div>


                                <?php endif; ?>



                                <!-- NAMA -->

                                <h3
                                    class="fw-bold
                                           umkm-name
                                           mb-2"
                                >

                                    <?= clean(
                                        $umkm["name"]
                                    ) ?>

                                </h3>



                                <!-- PEMILIK -->

                                <?php if (
                                    !empty(
                                        $umkm["owner"]
                                    )
                                ): ?>


                                    <div
                                        class="umkm-meta
                                               mb-2"
                                    >

                                        <i
                                            class="bi bi-person"
                                        ></i>

                                        <?= clean(
                                            $umkm["owner"]
                                        ) ?>

                                    </div>


                                <?php endif; ?>



                                <!-- ALAMAT -->

                                <?php if (
                                    !empty(
                                        $umkm["address"]
                                    )
                                ): ?>


                                    <div
                                        class="umkm-meta
                                               mb-3"
                                    >

                                        <i
                                            class="bi bi-geo-alt"
                                        ></i>

                                        <?= clean(
                                            mb_strimwidth(
                                                $umkm["address"],
                                                0,
                                                70,
                                                "..."
                                            )
                                        ) ?>


                                    </div>


                                <?php endif; ?>



                                <!-- DESKRIPSI SINGKAT -->

                                <?php if (
                                    !empty(
                                        $umkm["description"]
                                    )
                                ): ?>


                                    <p
                                        class="text-muted
                                               small
                                               mb-3"
                                    >

                                        <?= clean(
                                            mb_strimwidth(
                                                $umkm["description"],
                                                0,
                                                100,
                                                "..."
                                            )
                                        ) ?>

                                    </p>


                                <?php endif; ?>



                                <!-- TELEPON -->

                                <?php if (
                                    !empty(
                                        $umkm["phone"]
                                    )
                                ): ?>


                                    <div
                                        class="phone-box
                                               d-flex
                                               align-items-center
                                               gap-2
                                               mb-3"
                                    >

                                        <i
                                            class="bi bi-telephone-fill"
                                        ></i>


                                        <span
                                            class="small
                                                   fw-semibold"
                                        >

                                            <?= clean(
                                                $umkm["phone"]
                                            ) ?>

                                        </span>


                                    </div>


                                <?php endif; ?>



                                <!-- PRODUCT COUNT -->

                                <div
                                    class="product-count-box
                                           mb-3"
                                >


                                    <div>

                                        <i
                                            class="bi bi-box-seam
                                                   me-1"
                                        ></i>

                                        Produk UMKM

                                    </div>


                                    <strong>

                                        <?= (int)
                                            $umkm["total_products"] ?>

                                    </strong>


                                </div>



                                <!-- ==================================
                                     ACTIONS
                                =================================== -->

                                <div
                                    class="row
                                           g-2
                                           mt-auto"
                                >


                                    <!-- DETAIL -->

                                    <div
                                        class="col-12"
                                    >


                                        <a
                                            href="umkm-detail.php?id=<?= (int) $umkm["id"] ?>"
                                            class="btn
                                                   detail-button
                                                   action-button
                                                   w-100"
                                        >

                                            <i
                                                class="bi bi-shop"
                                            ></i>

                                            Lihat Detail UMKM

                                        </a>


                                    </div>



                                    <!-- PRODUK -->

                                    <?php if (
                                        (int)
                                        $umkm["total_products"]
                                        >
                                        0
                                    ): ?>


                                        <div
                                            class="col-12"
                                        >


                                            <a
                                                href="produk.php?umkm_id=<?= (int) $umkm["id"] ?>"
                                                class="btn
                                                       product-button
                                                       action-button
                                                       w-100"
                                            >

                                                <i
                                                    class="bi bi-box-seam"
                                                ></i>

                                                Lihat Produk
                                                UMKM Ini

                                            </a>


                                        </div>


                                    <?php else: ?>


                                        <div
                                            class="col-12"
                                        >


                                            <a
                                                href="produk.php?umkm_id=<?= (int) $umkm["id"] ?>"
                                                class="btn
                                                       product-button
                                                       action-button
                                                       w-100"
                                            >

                                                <i
                                                    class="bi bi-box-seam"
                                                ></i>

                                                Belum Ada Produk

                                            </a>


                                        </div>


                                    <?php endif; ?>


                                </div>


                            </div>


                        </div>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- ==================================================
                 EMPTY
            =================================================== -->

            <div
                class="text-center
                       py-5"
            >


                <i
                    class="bi bi-shop
                           empty-icon
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Data UMKM

                </h4>


                <p
                    class="text-muted"
                >

                    Data UMKM Desa Cisalada
                    belum tersedia saat ini.

                </p>


                <a
                    href="<?= BASE_URL ?>"
                    class="btn
                           btn-outline-primary"
                >

                    <i
                        class="bi bi-arrow-left
                               me-1"
                    ></i>

                    Kembali ke Beranda

                </a>


            </div>


        <?php endif; ?>


    </div>


</section>



<!-- ======================================================
     FOOTER
====================================================== -->

<?php

include "../includes/footer.php";

?>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>