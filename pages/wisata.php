<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


$page_title =
    "Wisata Desa";


// ======================================================
// AMBIL DATA WISATA
// ======================================================

$query =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            name,
            description,
            location,
            facilities,
            operating_hours,
            ticket_price,
            contact,
            photo,
            created_at
        FROM tourism
        ORDER BY
            created_at DESC,
            id DESC
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
           CARD LINK
        ================================================== */

        .wisata-link {

            display:
                block;

            height:
                100%;

            color:
                inherit;

            text-decoration:
                none;

        }


        /* ==================================================
           CARD
        ================================================== */

        .wisata-card {

            height:
                100%;

            border:
                none;

            border-radius:
                18px;

            overflow:
                hidden;

            background:
                #ffffff;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .wisata-card:hover {

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
           IMAGE
        ================================================== */

        .wisata-image {

            width:
                100%;

            height:
                235px;

            object-fit:
                cover;

            display:
                block;

        }


        .wisata-placeholder {

            width:
                100%;

            height:
                235px;

            background:
                #eef1f5;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .wisata-placeholder i {

            font-size:
                70px;

            color:
                #adb5bd;

        }


        /* ==================================================
           TITLE
        ================================================== */

        .wisata-name {

            font-size:
                1.25rem;

            line-height:
                1.35;

        }


        /* ==================================================
           INFO
        ================================================== */

        .wisata-info {

            color:
                #6c757d;

            font-size:
                0.9rem;

        }


        .wisata-info i {

            width:
                20px;

            color:
                #0d6efd;

        }


        /* ==================================================
           PRICE
        ================================================== */

        .wisata-price {

            color:
                #198754;

            font-weight:
                700;

        }


        /* ==================================================
           DETAIL LINK
        ================================================== */

        .detail-link {

            color:
                #0d6efd;

            font-size:
                0.9rem;

            font-weight:
                600;

        }


        /* ==================================================
           EMPTY
        ================================================== */

        .empty-icon {

            font-size:
                65px;

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

                Wisata

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
            class="text-primary
                   fw-semibold
                   mb-2"
        >

            POTENSI DESA

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Wisata Desa Cisalada

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 720px;
            "
        >

            Temukan berbagai tempat menarik
            dan potensi wisata yang ada
            di Desa Cisalada.

        </p>


    </div>


</section>



<!-- ======================================================
     DAFTAR WISATA
====================================================== -->

<section
    class="pb-5"
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
                    $wisata =
                        mysqli_fetch_assoc(
                            $query
                        )
                ): ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <a
                            href="wisata-detail.php?id=<?= (int) $wisata["id"] ?>"
                            class="wisata-link"
                        >


                            <div
                                class="card
                                       wisata-card
                                       shadow-sm"
                            >


                                <!-- FOTO -->

                                <?php if (
                                    !empty(
                                        $wisata["photo"]
                                    )
                                ): ?>


                                    <img
                                        src="<?= BASE_URL ?>assets/uploads/wisata/<?= rawurlencode(basename($wisata["photo"])) ?>"
                                        alt="<?= clean($wisata["name"]) ?>"
                                        class="wisata-image"
                                    >


                                <?php else: ?>


                                    <div
                                        class="wisata-placeholder"
                                    >

                                        <i
                                            class="bi bi-geo-alt"
                                        ></i>

                                    </div>


                                <?php endif; ?>



                                <!-- BODY -->

                                <div
                                    class="card-body
                                           p-4
                                           d-flex
                                           flex-column"
                                >


                                    <!-- NAMA -->

                                    <h3
                                        class="wisata-name
                                               fw-bold
                                               mb-3"
                                    >

                                        <?= clean(
                                            $wisata["name"]
                                        ) ?>


                                    </h3>



                                    <!-- LOKASI -->

                                    <?php if (
                                        !empty(
                                            $wisata["location"]
                                        )
                                    ): ?>


                                        <div
                                            class="wisata-info
                                                   mb-2"
                                        >


                                            <i
                                                class="bi bi-geo-alt"
                                            ></i>


                                            <?= clean(
                                                mb_strimwidth(
                                                    $wisata["location"],
                                                    0,
                                                    75,
                                                    "..."
                                                )
                                            ) ?>


                                        </div>


                                    <?php endif; ?>



                                    <!-- JAM -->

                                    <?php if (
                                        !empty(
                                            $wisata["operating_hours"]
                                        )
                                    ): ?>


                                        <div
                                            class="wisata-info
                                                   mb-2"
                                        >


                                            <i
                                                class="bi bi-clock"
                                            ></i>


                                            <?= clean(
                                                $wisata[
                                                    "operating_hours"
                                                ]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>



                                    <!-- HARGA -->

                                    <div
                                        class="wisata-price
                                               mb-3"
                                    >


                                        <?php if (
                                            (float)
                                            $wisata["ticket_price"]
                                            >
                                            0
                                        ): ?>


                                            Rp
                                            <?= number_format(
                                                (float)
                                                $wisata["ticket_price"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>


                                        <?php else: ?>


                                            Gratis

                                        <?php endif; ?>


                                    </div>



                                    <!-- DESKRIPSI -->

                                    <?php if (
                                        !empty(
                                            $wisata["description"]
                                        )
                                    ): ?>


                                        <p
                                            class="small
                                                   text-muted
                                                   mb-4"
                                        >


                                            <?= clean(
                                                mb_strimwidth(
                                                    $wisata["description"],
                                                    0,
                                                    120,
                                                    "..."
                                                )
                                            ) ?>


                                        </p>


                                    <?php endif; ?>



                                    <!-- DETAIL -->

                                    <div
                                        class="detail-link
                                               mt-auto
                                               d-flex
                                               align-items-center
                                               justify-content-between"
                                    >


                                        <span>

                                            Lihat Detail Wisata

                                        </span>


                                        <i
                                            class="bi bi-arrow-right"
                                        ></i>


                                    </div>


                                </div>


                            </div>


                        </a>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <div
                class="text-center
                       py-5"
            >


                <i
                    class="bi bi-geo-alt
                           empty-icon
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Data Wisata

                </h4>


                <p
                    class="text-muted"
                >

                    Data wisata Desa Cisalada
                    belum tersedia.

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