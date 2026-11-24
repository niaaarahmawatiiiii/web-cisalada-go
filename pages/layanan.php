<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// ======================================================
// AMBIL DATA LAYANAN
// ======================================================

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            name,
            category,
            requirements,
            service_procedure,
            service_time,
            cost,
            location
        FROM services
        ORDER BY
            category ASC,
            name ASC
        "
    );


$services = [];


if (
    $result
) {

    while (
        $row =
            mysqli_fetch_assoc(
                $result
            )
    ) {

        $services[] =
            $row;

    }

}


$page_title =
    "Layanan Desa";

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

        <?= clean(
            $page_title
        ) ?>

        - Cisalada GO

    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


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

            padding:
                55px 0 45px;

        }



        /* ==================================================
           CARD
        ================================================== */

        .service-card {

            background:
                #ffffff;

            border:
                1px solid #e9ecef;

            border-radius:
                16px;

            height:
                100%;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .service-card:hover {

            transform:
                translateY(-4px);

            box-shadow:
                0 10px 26px
                rgba(
                    0,
                    0,
                    0,
                    0.08
                );

        }



        /* ==================================================
           ICON
        ================================================== */

        .service-icon {

            width:
                50px;

            height:
                50px;

            min-width:
                50px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef4ff;

            color:
                #0d6efd;

            border-radius:
                12px;

        }



        /* ==================================================
           CATEGORY
        ================================================== */

        .service-category {

            font-size:
                11px;

            font-weight:
                700;

            color:
                #0d6efd;

            text-transform:
                uppercase;

            letter-spacing:
                0.4px;

        }



        /* ==================================================
           TITLE
        ================================================== */

        .service-title {

            font-size:
                1.1rem;

            font-weight:
                700;

        }



        /* ==================================================
           META
        ================================================== */

        .service-meta {

            display:
                flex;

            align-items:
                center;

            gap:
                7px;

            color:
                #6c757d;

            font-size:
                0.85rem;

        }



        /* ==================================================
           DETAIL BOX
        ================================================== */

        .detail-box {

            background:
                #f8f9fa;

            border-radius:
                12px;

            padding:
                16px;

        }



        .detail-title {

            font-size:
                0.85rem;

            font-weight:
                700;

            margin-bottom:
                8px;

        }



        .detail-text {

            color:
                #6c757d;

            white-space:
                pre-line;

            line-height:
                1.7;

        }



        /* ==================================================
           EMPTY
        ================================================== */

        .empty-box {

            background:
                #ffffff;

            border:
                1px solid #e9ecef;

            border-radius:
                16px;

            padding:
                65px 20px;

            text-align:
                center;

        }



        /* ==================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 767.98px
        ) {

            .page-hero {

                padding:
                    40px 0 35px;

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
                class="breadcrumb-item
                       active"
                aria-current="page"
            >

                Layanan Desa

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

            INFORMASI PELAYANAN

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Layanan Desa

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 760px;
            "
        >

            Temukan informasi layanan administrasi
            Desa Cisalada, mulai dari persyaratan,
            prosedur, waktu pelayanan, biaya,
            dan lokasi pelayanan.

        </p>


    </div>


</section>



<!-- ======================================================
     SERVICES
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <?php if (
            count(
                $services
            )
            >
            0
        ): ?>


            <div
                class="row
                       g-4"
            >


                <?php foreach (
                    $services
                    as $service
                ): ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <div
                            class="service-card
                                   p-4"
                        >


                            <!-- HEADER -->

                            <div
                                class="d-flex
                                       gap-3
                                       mb-3"
                            >


                                <div
                                    class="service-icon"
                                >

                                    <i
                                        class="bi bi-file-earmark-text
                                               fs-4"
                                    ></i>

                                </div>


                                <div
                                    class="flex-grow-1"
                                >


                                    <?php if (
                                        !empty(
                                            $service["category"]
                                        )
                                    ): ?>


                                        <div
                                            class="service-category
                                                   mb-1"
                                        >

                                            <?= clean(
                                                $service["category"]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>


                                    <div
                                        class="service-title"
                                    >

                                        <?= clean(
                                            $service["name"]
                                        ) ?>


                                    </div>


                                </div>


                            </div>



                            <!-- META -->

                            <div
                                class="d-flex
                                       flex-column
                                       gap-2
                                       mb-4"
                            >


                                <?php if (
                                    !empty(
                                        $service["service_time"]
                                    )
                                ): ?>


                                    <div
                                        class="service-meta"
                                    >

                                        <i
                                            class="bi bi-clock"
                                        ></i>


                                        <?= clean(
                                            $service["service_time"]
                                        ) ?>


                                    </div>


                                <?php endif; ?>


                                <div
                                    class="service-meta"
                                >

                                    <i
                                        class="bi bi-wallet2"
                                    ></i>


                                    <?php

                                    $cost_text =
                                        trim(
                                            $service["cost"]
                                            ??
                                            ""
                                        );

                                    ?>


                                    <?php if (
                                        $cost_text === ""
                                        ||
                                        $cost_text === "0"
                                    ): ?>


                                        Gratis


                                    <?php else: ?>


                                        <?= clean(
                                            $cost_text
                                        ) ?>


                                    <?php endif; ?>


                                </div>



                                <?php if (
                                    !empty(
                                        $service["location"]
                                    )
                                ): ?>


                                    <div
                                        class="service-meta"
                                    >

                                        <i
                                            class="bi bi-geo-alt"
                                        ></i>


                                        <?= clean(
                                            $service["location"]
                                        ) ?>


                                    </div>


                                <?php endif; ?>


                            </div>



                            <!-- BUTTON -->

                            <button
                                type="button"
                                class="btn
                                       btn-primary
                                       w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#serviceModal<?= (int) $service["id"] ?>"
                            >

                                <i
                                    class="bi bi-eye
                                           me-1"
                                ></i>


                                Lihat Detail Layanan

                            </button>


                        </div>


                    </div>



                    <!-- ==================================================
                         MODAL DETAIL
                    =================================================== -->

                    <div
                        class="modal
                               fade"
                        id="serviceModal<?= (int) $service["id"] ?>"
                        tabindex="-1"
                        aria-hidden="true"
                    >


                        <div
                            class="modal-dialog
                                   modal-lg
                                   modal-dialog-scrollable"
                        >


                            <div
                                class="modal-content"
                            >


                                <!-- HEADER -->

                                <div
                                    class="modal-header"
                                >


                                    <div>


                                        <?php if (
                                            !empty(
                                                $service["category"]
                                            )
                                        ): ?>


                                            <div
                                                class="service-category
                                                       mb-1"
                                            >

                                                <?= clean(
                                                    $service["category"]
                                                ) ?>


                                            </div>


                                        <?php endif; ?>


                                        <h5
                                            class="modal-title
                                                   fw-bold"
                                        >

                                            <?= clean(
                                                $service["name"]
                                            ) ?>


                                        </h5>


                                    </div>


                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                    ></button>


                                </div>



                                <!-- BODY -->

                                <div
                                    class="modal-body
                                           p-4"
                                >


                                    <!-- PERSYARATAN -->

                                    <?php if (
                                        !empty(
                                            $service["requirements"]
                                        )
                                    ): ?>


                                        <div
                                            class="detail-box
                                                   mb-3"
                                        >


                                            <div
                                                class="detail-title"
                                            >

                                                <i
                                                    class="bi bi-check2-square
                                                           me-1"
                                                ></i>


                                                Persyaratan

                                            </div>


                                            <div
                                                class="detail-text"
                                            >

                                                <?= clean(
                                                    $service["requirements"]
                                                ) ?>


                                            </div>


                                        </div>


                                    <?php endif; ?>



                                    <!-- PROSEDUR -->

                                    <?php if (
                                        !empty(
                                            $service["service_procedure"]
                                        )
                                    ): ?>


                                        <div
                                            class="detail-box
                                                   mb-3"
                                        >


                                            <div
                                                class="detail-title"
                                            >

                                                <i
                                                    class="bi bi-list-ol
                                                           me-1"
                                                ></i>


                                                Prosedur

                                            </div>


                                            <div
                                                class="detail-text"
                                            >

                                                <?= clean(
                                                    $service["service_procedure"]
                                                ) ?>


                                            </div>


                                        </div>


                                    <?php endif; ?>



                                    <!-- INFORMASI -->

                                    <div
                                        class="row
                                               g-3"
                                    >


                                        <?php if (
                                            !empty(
                                                $service["service_time"]
                                            )
                                        ): ?>


                                            <div
                                                class="col-md-6"
                                            >


                                                <div
                                                    class="detail-box
                                                           h-100"
                                                >


                                                    <div
                                                        class="detail-title"
                                                    >

                                                        <i
                                                            class="bi bi-clock
                                                                   me-1"
                                                        ></i>


                                                        Waktu Pelayanan

                                                    </div>


                                                    <div
                                                        class="detail-text"
                                                    >

                                                        <?= clean(
                                                            $service["service_time"]
                                                        ) ?>


                                                    </div>


                                                </div>


                                            </div>


                                        <?php endif; ?>



                                        <div
                                            class="col-md-6"
                                        >


                                            <div
                                                class="detail-box
                                                       h-100"
                                            >


                                                <div
                                                    class="detail-title"
                                                >

                                                    <i
                                                        class="bi bi-wallet2
                                                               me-1"
                                                    ></i>


                                                    Biaya

                                                </div>


                                                <div
                                                    class="detail-text"
                                                >


                                                    <?php

                                                    $cost_text =
                                                        trim(
                                                            $service["cost"]
                                                            ??
                                                            ""
                                                        );

                                                    ?>


                                                    <?php if (
                                                        $cost_text === ""
                                                        ||
                                                        $cost_text === "0"
                                                    ): ?>


                                                        Gratis


                                                    <?php else: ?>


                                                        <?= clean(
                                                            $cost_text
                                                        ) ?>


                                                    <?php endif; ?>


                                                </div>


                                            </div>


                                        </div>



                                        <?php if (
                                            !empty(
                                                $service["location"]
                                            )
                                        ): ?>


                                            <div
                                                class="col-md-6"
                                            >


                                                <div
                                                    class="detail-box
                                                           h-100"
                                                >


                                                    <div
                                                        class="detail-title"
                                                    >

                                                        <i
                                                            class="bi bi-geo-alt
                                                                   me-1"
                                                        ></i>


                                                        Lokasi Pelayanan

                                                    </div>


                                                    <div
                                                        class="detail-text"
                                                    >

                                                        <?= clean(
                                                            $service["location"]
                                                        ) ?>


                                                    </div>


                                                </div>


                                            </div>


                                        <?php endif; ?>


                                    </div>


                                </div>



                                <!-- FOOTER -->

                                <div
                                    class="modal-footer"
                                >


                                    <button
                                        type="button"
                                        class="btn
                                               btn-secondary"
                                        data-bs-dismiss="modal"
                                    >

                                        Tutup

                                    </button>


                                </div>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div
                class="empty-box"
            >


                <i
                    class="bi bi-headset
                           fs-1
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Layanan Belum Tersedia

                </h4>


                <p
                    class="text-muted
                           mb-0"
                >

                    Informasi layanan Desa Cisalada
                    belum tersedia.

                </p>


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