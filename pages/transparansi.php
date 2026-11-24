<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// ======================================================
// AMBIL SEMUA DATA
// ======================================================

$query =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            title,
            category,
            year,
            income,
            expenditure,
            financing,
            description,
            document,
            created_at
        FROM transparency
        ORDER BY
            year DESC,
            id DESC
        "
    );


$transparency_data = [];


if (
    $query
) {

    while (
        $row =
            mysqli_fetch_assoc(
                $query
            )
    ) {

        $transparency_data[] =
            $row;

    }

}


// ======================================================
// PISAHKAN APBDES
// ======================================================

$apbdes_data = [];


$other_data = [];


foreach (
    $transparency_data
    as $row
) {


    if (
        strtolower(
            trim(
                $row["category"]
                ??
                ""
            )
        )
        ===
        "apbdes"
    ) {

        $apbdes_data[] =
            $row;

    } else {

        $other_data[] =
            $row;

    }

}


$page_title =
    "Transparansi Desa";

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


    <!-- ==================================================
         BOOTSTRAP
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ==================================================
         ICON
    ================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        /* ==================================================
           BODY
        ================================================== */

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
                55px 0
                45px;

            background:
                #f8f9fa;

        }



        /* ==================================================
           CARD
        ================================================== */

        .info-card {

            border:
                none;

            border-radius:
                18px;

            background:
                #ffffff;

        }



        /* ==================================================
           APBDES
        ================================================== */

        .apbdes-card {

            border:
                none;

            border-radius:
                18px;

            background:
                #ffffff;

            overflow:
                hidden;

        }



        .apbdes-header {

            background:
                #0d6efd;

            color:
                #ffffff;

        }



        .amount-card {

            border:
                1px solid #e9ecef;

            border-radius:
                14px;

            height:
                100%;

            background:
                #ffffff;

        }



        .amount-label {

            color:
                #6c757d;

            font-size:
                0.9rem;

        }



        .amount-value {

            font-size:
                1.35rem;

            font-weight:
                700;

            color:
                #198754;

        }



        /* ==================================================
           OTHER TRANSPARENCY
        ================================================== */

        .transparency-card {

            border:
                1px solid #e9ecef;

            border-radius:
                16px;

            background:
                #ffffff;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }



        .transparency-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 10px 24px
                rgba(
                    0,
                    0,
                    0,
                    0.07
                );

        }



        .transparency-icon {

            width:
                48px;

            height:
                48px;

            min-width:
                48px;

            border-radius:
                12px;

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

        }



        /* ==================================================
           DOCUMENT BUTTON
        ================================================== */

        .document-button {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

            border-radius:
                9px;

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

            text-align:
                center;

            padding:
                60px 20px;

        }



        /* ==================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 767.98px
        ) {

            .amount-value {

                font-size:
                    1.1rem;

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

                Transparansi

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

            KETERBUKAAN INFORMASI PUBLIK

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Transparansi Desa

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 760px;
            "
        >

            Informasi publik Desa Cisalada
            mengenai APBDes, pembangunan,
            peraturan, dan dokumen yang
            telah disetujui untuk dipublikasikan.

        </p>


    </div>


</section>



<!-- ======================================================
     APBDES
====================================================== -->

<?php if (
    count(
        $apbdes_data
    )
    >
    0
): ?>


<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="mb-4"
        >


            <h2
                class="fw-bold
                       mb-1"
            >

                APBDes

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Informasi Anggaran Pendapatan
                dan Belanja Desa.

            </p>


        </div>



        <div
            class="row
                   g-4"
        >


            <?php foreach (
                $apbdes_data
                as $row
            ): ?>


                <div
                    class="col-12"
                >


                    <div
                        class="card
                               apbdes-card
                               shadow-sm"
                    >


                        <div
                            class="apbdes-header
                                   p-4"
                        >


                            <div
                                class="d-flex
                                       justify-content-between
                                       align-items-start
                                       flex-wrap
                                       gap-2"
                            >


                                <div>


                                    <div
                                        class="small
                                               opacity-75
                                               mb-1"
                                    >

                                        APBDes

                                    </div>


                                    <h3
                                        class="fw-bold
                                               mb-0"
                                    >

                                        <?= clean(
                                            $row["title"]
                                        ) ?>


                                    </h3>


                                </div>


                                <?php if (
                                    !empty(
                                        $row["year"]
                                    )
                                ): ?>


                                    <span
                                        class="badge
                                               bg-white
                                               text-primary"
                                    >

                                        Tahun

                                        <?= (int)
                                            $row["year"]
                                        ?>


                                    </span>


                                <?php endif; ?>


                            </div>


                        </div>



                        <div
                            class="card-body
                                   p-4
                                   p-lg-5"
                        >


                            <div
                                class="row
                                       g-3"
                            >


                                <!-- PENDAPATAN -->

                                <div
                                    class="col-md-4"
                                >


                                    <div
                                        class="amount-card
                                               p-4"
                                    >


                                        <div
                                            class="amount-label
                                                   mb-2"
                                        >

                                            Pendapatan

                                        </div>


                                        <div
                                            class="amount-value"
                                        >

                                            Rp
                                            <?= number_format(
                                                (float)
                                                $row["income"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>


                                        </div>


                                    </div>


                                </div>



                                <!-- BELANJA -->

                                <div
                                    class="col-md-4"
                                >


                                    <div
                                        class="amount-card
                                               p-4"
                                    >


                                        <div
                                            class="amount-label
                                                   mb-2"
                                        >

                                            Belanja

                                        </div>


                                        <div
                                            class="amount-value"
                                        >

                                            Rp
                                            <?= number_format(
                                                (float)
                                                $row["expenditure"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>


                                        </div>


                                    </div>


                                </div>



                                <!-- PEMBIAYAAN -->

                                <div
                                    class="col-md-4"
                                >


                                    <div
                                        class="amount-card
                                               p-4"
                                    >


                                        <div
                                            class="amount-label
                                                   mb-2"
                                        >

                                            Pembiayaan

                                        </div>


                                        <div
                                            class="amount-value"
                                        >

                                            Rp
                                            <?= number_format(
                                                (float)
                                                $row["financing"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>


                                        </div>


                                    </div>


                                </div>


                            </div>



                            <?php if (
                                !empty(
                                    $row["description"]
                                )
                            ): ?>


                                <div
                                    class="border-top
                                           mt-4
                                           pt-4"
                                >


                                    <h5
                                        class="fw-bold
                                               mb-2"
                                    >

                                        Keterangan

                                    </h5>


                                    <div
                                        class="text-muted
                                               lh-lg"
                                        style="
                                            white-space: pre-line;
                                        "
                                    >

                                        <?= clean(
                                            $row["description"]
                                        ) ?>


                                    </div>


                                </div>


                            <?php endif; ?>



                            <?php if (
                                !empty(
                                    $row["document"]
                                )
                            ): ?>


                                <div
                                    class="border-top
                                           mt-4
                                           pt-4"
                                >


                                    <a
                                        href="<?= BASE_URL ?>assets/uploads/transparansi/<?= rawurlencode(
                                            basename(
                                                $row["document"]
                                            )
                                        ) ?>"
                                        target="_blank"
                                        class="btn
                                               btn-outline-danger
                                               document-button"
                                    >

                                        <i
                                            class="bi bi-file-earmark-pdf"
                                        ></i>


                                        Lihat Dokumen APBDes

                                    </a>


                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


    </div>


</section>


<?php endif; ?>



<!-- ======================================================
     INFORMASI TRANSPARANSI LAINNYA
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="mb-4"
        >


            <h2
                class="fw-bold
                       mb-1"
            >

                Informasi Transparansi

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Informasi publik lainnya yang
                tersedia dari Pemerintah Desa Cisalada.

            </p>


        </div>



        <?php if (
            count(
                $other_data
            )
            >
            0
        ): ?>


            <div
                class="row
                       g-4"
            >


                <?php foreach (
                    $other_data
                    as $row
                ): ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <div
                            class="transparency-card
                                   p-4
                                   h-100"
                        >


                            <div
                                class="d-flex
                                       gap-3
                                       mb-3"
                            >


                                <div
                                    class="transparency-icon"
                                >


                                    <?php

                                    $category_icon =
                                        "bi-file-earmark-text";


                                    $category_value =
                                        strtolower(
                                            $row[
                                                "category"
                                            ]
                                            ??
                                            ""
                                        );


                                    if (
                                        strpos(
                                            $category_value,
                                            "pembangunan"
                                        )
                                        !==
                                        false
                                    ) {

                                        $category_icon =
                                            "bi-building";

                                    } elseif (
                                        strpos(
                                            $category_value,
                                            "peraturan"
                                        )
                                        !==
                                        false
                                    ) {

                                        $category_icon =
                                            "bi-journal-text";

                                    } elseif (
                                        strpos(
                                            $category_value,
                                            "idm"
                                        )
                                        !==
                                        false
                                    ) {

                                        $category_icon =
                                            "bi-bar-chart";

                                    } elseif (
                                        strpos(
                                            $category_value,
                                            "realisasi"
                                        )
                                        !==
                                        false
                                    ) {

                                        $category_icon =
                                            "bi-clipboard-data";

                                    }

                                    ?>


                                    <i
                                        class="bi
                                               <?= $category_icon ?>"
                                    ></i>


                                </div>


                                <div
                                    class="flex-grow-1"
                                >


                                    <span
                                        class="badge
                                               bg-primary-subtle
                                               text-primary
                                               mb-2"
                                    >

                                        <?= clean(
                                            $row["category"]
                                            ??
                                            "Umum"
                                        ) ?>


                                    </span>


                                    <h4
                                        class="fw-bold
                                               mb-0"
                                    >

                                        <?= clean(
                                            $row["title"]
                                        ) ?>


                                    </h4>


                                </div>


                            </div>



                            <?php if (
                                !empty(
                                    $row["year"]
                                )
                            ): ?>


                                <div
                                    class="small
                                           text-muted
                                           mb-3"
                                >


                                    <i
                                        class="bi bi-calendar3
                                               me-1"
                                    ></i>


                                    Tahun

                                    <?= (int)
                                        $row["year"]
                                    ?>


                                </div>


                            <?php endif; ?>



                            <?php if (
                                !empty(
                                    $row["description"]
                                )
                            ): ?>


                                <p
                                    class="text-muted
                                           small
                                           lh-lg
                                           mb-4"
                                >

                                    <?= clean(
                                        mb_strimwidth(
                                            $row["description"],
                                            0,
                                            180,
                                            "..."
                                        )
                                    ) ?>


                                </p>


                            <?php endif; ?>



                            <?php if (
                                !empty(
                                    $row["document"]
                                )
                            ): ?>


                                <a
                                    href="<?= BASE_URL ?>assets/uploads/transparansi/<?= rawurlencode(
                                        basename(
                                            $row["document"]
                                        )
                                    ) ?>"
                                    target="_blank"
                                    class="btn
                                           btn-outline-danger
                                           btn-sm
                                           document-button"
                                >

                                    <i
                                        class="bi bi-file-earmark-pdf"
                                    ></i>


                                    Lihat Dokumen

                                </a>


                            <?php else: ?>


                                <span
                                    class="small
                                           text-muted"
                                >

                                    Tidak ada dokumen.

                                </span>


                            <?php endif; ?>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php elseif (
            count(
                $apbdes_data
            )
            ===
            0
        ): ?>


            <div
                class="empty-box"
            >


                <i
                    class="bi bi-file-earmark-text
                           fs-1
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Informasi Transparansi

                </h4>


                <p
                    class="text-muted
                           mb-0"
                >

                    Informasi transparansi desa
                    belum tersedia.

                </p>


            </div>


        <?php endif; ?>


    </div>


</section>



<!-- ======================================================
     INFO
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="info-card
                   shadow-sm
                   p-4
                   p-lg-5"
        >


            <div
                class="d-flex
                       gap-3"
            >


                <i
                    class="bi bi-info-circle-fill
                           text-primary
                           fs-4"
                ></i>


                <div>


                    <h5
                        class="fw-bold
                               mb-2"
                    >

                        Tentang Informasi Transparansi

                    </h5>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Informasi yang ditampilkan pada halaman
                        ini merupakan data publik yang telah
                        disediakan dan disetujui untuk dipublikasikan
                        oleh Pemerintah Desa Cisalada.

                    </p>


                </div>


            </div>


        </div>


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