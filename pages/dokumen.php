<?php

require_once "../config/database.php";
require_once "../includes/functions.php";

$page_title = "Dokumen Desa";

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

        <?= $page_title ?>
        - Cisalada GO

    </title>


    <!-- ======================================================
         BOOTSTRAP
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ======================================================
         BOOTSTRAP ICON
    ======================================================= -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- ======================================================
         STYLE
    ======================================================= -->

    <style>

        body {
            background-color: #f8f9fa;
        }


        .page-hero {
            background-color: #f8f9fa;
            padding: 60px 0 45px;
        }


        .document-card {
            border: 0;
            border-radius: 16px;
            transition: all 0.2s ease;
        }


        .document-card:hover {
            transform: translateY(-4px);
        }


        .document-icon {
            width: 58px;
            height: 58px;
            border-radius: 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            background-color: #fff1f1;
        }


        .document-title {
            min-height: 50px;
        }


        .empty-icon {
            font-size: 55px;
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

                Informasi

            </li>


            <li
                class="breadcrumb-item
                       active"
                aria-current="page"
            >

                Dokumen

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

                INFORMASI PUBLIK

            </span>

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Dokumen Desa

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 700px;
            "
        >

            Kumpulan dokumen dan informasi publik
            Desa Cisalada yang dapat diakses
            masyarakat.

        </p>


    </div>

</section>



<!-- ======================================================
     DOCUMENT LIST
====================================================== -->

<section
    class="py-5"
>

    <div
        class="container"
    >


        <?php

        $query = mysqli_query(
            $conn,
            "
            SELECT
                id,
                name,
                category,
                year,
                file,
                created_at

            FROM documents

            ORDER BY
                year DESC,
                created_at DESC,
                id DESC
            "
        );

        ?>



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
                    $document =
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
                                   document-card
                                   shadow-sm
                                   h-100"
                        >


                            <div
                                class="card-body
                                       p-4
                                       d-flex
                                       flex-column"
                            >


                                <!-- ==================================================
                                     HEADER CARD
                                =================================================== -->

                                <div
                                    class="d-flex
                                           gap-3
                                           align-items-start"
                                >


                                    <!-- ICON -->

                                    <div
                                        class="document-icon
                                               flex-shrink-0"
                                    >

                                        <i
                                            class="bi bi-file-earmark-pdf
                                                   text-danger
                                                   fs-3"
                                        ></i>

                                    </div>



                                    <!-- INFO -->

                                    <div
                                        class="flex-grow-1"
                                    >


                                        <h5
                                            class="fw-bold
                                                   document-title
                                                   mb-2"
                                        >

                                            <?= clean(
                                                $document[
                                                    "name"
                                                ]
                                            ) ?>

                                        </h5>


                                        <!-- CATEGORY -->

                                        <?php if (
                                            !empty(
                                                $document[
                                                    "category"
                                                ]
                                            )
                                        ): ?>

                                            <span
                                                class="badge
                                                       bg-primary
                                                       me-1"
                                            >

                                                <?= clean(
                                                    $document[
                                                        "category"
                                                    ]
                                                ) ?>

                                            </span>

                                        <?php endif; ?>


                                        <!-- YEAR -->

                                        <?php if (
                                            !empty(
                                                $document[
                                                    "year"
                                                ]
                                            )
                                        ): ?>

                                            <span
                                                class="badge
                                                       bg-light
                                                       text-dark
                                                       border"
                                            >

                                                <?= clean(
                                                    $document[
                                                        "year"
                                                    ]
                                                ) ?>

                                            </span>

                                        <?php endif; ?>


                                    </div>


                                </div>



                                <!-- ==================================================
                                     DATE
                                =================================================== -->

                                <div
                                    class="text-muted
                                           small
                                           mt-3"
                                >

                                    <i
                                        class="bi bi-calendar3
                                               me-1"
                                    ></i>


                                    Dipublikasikan

                                    <?php if (
                                        !empty(
                                            $document[
                                                "created_at"
                                            ]
                                        )
                                    ): ?>

                                        <?= date(
                                            "d-m-Y",
                                            strtotime(
                                                $document[
                                                    "created_at"
                                                ]
                                            )
                                        ) ?>

                                    <?php endif; ?>


                                </div>



                                <!-- ==================================================
                                     BUTTON
                                =================================================== -->

                                <div
                                    class="mt-auto
                                           pt-4"
                                >


                                    <?php if (
                                        !empty(
                                            $document[
                                                "file"
                                            ]
                                        )
                                    ): ?>


                                        <a
                                            href="<?= BASE_URL ?>assets/uploads/dokumen/<?= rawurlencode(basename($document["file"])) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn
                                                   btn-primary
                                                   w-100"
                                        >

                                            <i
                                                class="bi bi-file-earmark-pdf
                                                       me-1"
                                            ></i>

                                            Lihat Dokumen

                                        </a>


                                    <?php else: ?>


                                        <button
                                            type="button"
                                            class="btn
                                                   btn-secondary
                                                   w-100"
                                            disabled
                                        >

                                            File Tidak Tersedia

                                        </button>


                                    <?php endif; ?>


                                </div>


                            </div>

                        </div>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- ==================================================
                 EMPTY STATE
            =================================================== -->

            <div
                class="text-center
                       py-5"
            >


                <div
                    class="mb-3"
                >

                    <i
                        class="bi bi-file-earmark-text
                               empty-icon
                               text-muted"
                    ></i>

                </div>


                <h4
                    class="fw-bold"
                >

                    Belum Ada Dokumen

                </h4>


                <p
                    class="text-muted"
                >

                    Belum ada dokumen publik
                    yang tersedia saat ini.

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



<!-- ======================================================
     BOOTSTRAP JS
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>