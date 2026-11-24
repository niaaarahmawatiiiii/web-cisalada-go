<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


$id =
    isset($_GET["id"])
    ?
    (int)
    $_GET["id"]
    :
    0;


if (
    $id <= 0
) {

    header(
        "Location: wisata.php"
    );

    exit;

}


// ======================================================
// AMBIL DATA
// ======================================================

$stmt =
    mysqli_prepare(
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
        WHERE id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: wisata.php"
    );

    exit;

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute(
    $stmt
);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


$wisata =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$wisata
) {

    header(
        "Location: wisata.php"
    );

    exit;

}


// ======================================================
// DATA
// ======================================================

$name =
    $wisata["name"]
    ??
    "";


$description =
    $wisata["description"]
    ??
    "";


$location =
    $wisata["location"]
    ??
    "";


$facilities =
    $wisata["facilities"]
    ??
    "";


$operating_hours =
    $wisata["operating_hours"]
    ??
    "";


$ticket_price =
    $wisata["ticket_price"]
    ??
    0;


$contact =
    $wisata["contact"]
    ??
    "";


$photo =
    $wisata["photo"]
    ??
    "";


// ======================================================
// WHATSAPP
// ======================================================

$whatsapp_number = "";


if (
    !empty($contact)
) {


    $whatsapp_number =
        preg_replace(
            "/[^0-9]/",
            "",
            $contact
        );


    if (
        substr(
            $whatsapp_number,
            0,
            1
        )
        ===
        "0"
    ) {

        $whatsapp_number =
            "62"
            .
            substr(
                $whatsapp_number,
                1
            );

    }


    if (
        substr(
            $whatsapp_number,
            0,
            2
        )
        !==
        "62"
    ) {

        $whatsapp_number =
            "62"
            .
            $whatsapp_number;

    }

}


$page_title =
    $name
    .
    " - Wisata Desa Cisalada";

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

        .detail-hero {

            padding:
                45px 0 35px;

            background:
                #f8f9fa;

        }


        /* ==================================================
           CARD
        ================================================== */

        .detail-card {

            border:
                none;

            border-radius:
                18px;

            overflow:
                hidden;

        }


        /* ==================================================
           IMAGE
        ================================================== */

        .detail-image {

            width:
                100%;

            height:
                460px;

            object-fit:
                cover;

            display:
                block;

        }


        .detail-placeholder {

            width:
                100%;

            height:
                460px;

            background:
                #eef1f5;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .detail-placeholder i {

            font-size:
                100px;

            color:
                #adb5bd;

        }


        /* ==================================================
           INFO ITEM
        ================================================== */

        .info-item {

            padding:
                15px 0;

            border-bottom:
                1px solid #eef0f2;

        }


        .info-item:last-child {

            border-bottom:
                none;

        }


        .info-icon {

            width:
                42px;

            height:
                42px;

            min-width:
                42px;

            border-radius:
                10px;

            background:
                #eef4ff;

            color:
                #0d6efd;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        /* ==================================================
           PRICE
        ================================================== */

        .ticket-price {

            color:
                #198754;

            font-size:
                1.35rem;

            font-weight:
                700;

        }


        /* ==================================================
           FACILITIES
        ================================================== */

        .facility-item {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                10px;

            margin-bottom:
                10px;

            color:
                #495057;

        }


        .facility-item i {

            color:
                #198754;

            margin-top:
                3px;

        }


        /* ==================================================
           BUTTON
        ================================================== */

        .action-button {

            min-height:
                46px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                8px;

            border-radius:
                10px;

        }


        /* ==================================================
           WHATSAPP
        ================================================== */

        .whatsapp-button {

            background:
                #198754;

            border:
                1px solid #198754;

            color:
                #ffffff;

        }


        .whatsapp-button:hover {

            background:
                #157347;

            border-color:
                #157347;

            color:
                #ffffff;

        }


        /* ==================================================
           BACK
        ================================================== */

        .back-button {

            background:
                #ffffff;

            border:
                1px solid #dee2e6;

            color:
                #495057;

        }


        .back-button:hover {

            background:
                #f1f3f5;

            color:
                #212529;

        }


        /* ==================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 767.98px
        ) {

            .detail-image {

                height:
                    300px;

            }


            .detail-placeholder {

                height:
                    300px;

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

                <a
                    href="wisata.php"
                    class="text-decoration-none"
                >

                    Wisata

                </a>

            </li>


            <li
                class="breadcrumb-item
                       active"
                aria-current="page"
            >

                <?= clean($name) ?>

            </li>


        </ol>


    </nav>


</div>



<!-- ======================================================
     HERO
====================================================== -->

<section
    class="detail-hero"
>


    <div
        class="container"
    >


        <div
            class="text-primary
                   fw-semibold
                   mb-2"
        >

            DETAIL WISATA

        </div>


        <h1
            class="fw-bold
                   mb-2"
        >

            <?= clean($name) ?>

        </h1>


        <?php if (
            !empty($location)
        ): ?>


            <div
                class="text-muted"
            >

                <i
                    class="bi bi-geo-alt
                           me-1"
                ></i>


                <?= clean($location) ?>


            </div>


        <?php endif; ?>


    </div>


</section>



<!-- ======================================================
     CONTENT
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="row
                   g-4"
        >


            <!-- ==================================================
                 LEFT
            =================================================== -->

            <div
                class="col-lg-7"
            >


                <div
                    class="card
                           detail-card
                           shadow-sm
                           mb-4"
                >


                    <!-- FOTO -->

                    <?php if (
                        !empty($photo)
                    ): ?>


                        <img
                            src="<?= BASE_URL ?>assets/uploads/wisata/<?= rawurlencode(basename($photo)) ?>"
                            alt="<?= clean($name) ?>"
                            class="detail-image"
                        >


                    <?php else: ?>


                        <div
                            class="detail-placeholder"
                        >

                            <i
                                class="bi bi-geo-alt"
                            ></i>

                        </div>


                    <?php endif; ?>



                    <!-- DESKRIPSI -->

                    <div
                        class="card-body
                               p-4
                               p-lg-5"
                    >


                        <h3
                            class="fw-bold
                                   mb-3"
                        >

                            Tentang Wisata

                        </h3>


                        <?php if (
                            !empty($description)
                        ): ?>


                            <div
                                class="text-muted
                                       lh-lg"
                                style="
                                    white-space: pre-line;
                                "
                            >

                                <?= clean(
                                    $description
                                ) ?>

                            </div>


                        <?php else: ?>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Belum ada deskripsi
                                yang tersedia.

                            </p>


                        <?php endif; ?>


                    </div>


                </div>



                <!-- ==================================================
                     FASILITAS
                =================================================== -->

                <?php if (
                    !empty($facilities)
                ): ?>


                    <div
                        class="card
                               detail-card
                               shadow-sm
                               mb-4"
                    >


                        <div
                            class="card-body
                                   p-4
                                   p-lg-5"
                        >


                            <h3
                                class="fw-bold
                                       mb-3"
                            >

                                Fasilitas

                            </h3>



                            <div>


                                <?php

                                $facility_list =
                                    preg_split(
                                        "/\r\n|\r|\n|,/",
                                        $facilities
                                    );

                                ?>


                                <?php foreach (
                                    $facility_list
                                    as $facility
                                ): ?>


                                    <?php

                                    $facility =
                                        trim(
                                            $facility
                                        );

                                    ?>


                                    <?php if (
                                        $facility
                                        !==
                                        ""
                                    ): ?>


                                        <div
                                            class="facility-item"
                                        >

                                            <i
                                                class="bi bi-check-circle-fill"
                                            ></i>


                                            <span>

                                                <?= clean(
                                                    $facility
                                                ) ?>

                                            </span>


                                        </div>


                                    <?php endif; ?>


                                <?php endforeach; ?>


                            </div>


                        </div>


                    </div>


                <?php endif; ?>


            </div>



            <!-- ==================================================
                 RIGHT
            =================================================== -->

            <div
                class="col-lg-5"
            >


                <div
                    class="card
                           detail-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <h4
                            class="fw-bold
                                   mb-3"
                        >

                            Informasi Wisata

                        </h4>



                        <!-- LOKASI -->

                        <?php if (
                            !empty($location)
                        ): ?>


                            <div
                                class="info-item
                                       d-flex
                                       gap-3"
                            >


                                <div
                                    class="info-icon"
                                >

                                    <i
                                        class="bi bi-geo-alt"
                                    ></i>

                                </div>


                                <div>


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Lokasi

                                    </div>


                                    <div
                                        class="fw-semibold"
                                    >

                                        <?= clean(
                                            $location
                                        ) ?>

                                    </div>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- JAM -->

                        <?php if (
                            !empty(
                                $operating_hours
                            )
                        ): ?>


                            <div
                                class="info-item
                                       d-flex
                                       gap-3"
                            >


                                <div
                                    class="info-icon"
                                >

                                    <i
                                        class="bi bi-clock"
                                    ></i>

                                </div>


                                <div>


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Jam Operasional

                                    </div>


                                    <div
                                        class="fw-semibold"
                                    >

                                        <?= clean(
                                            $operating_hours
                                        ) ?>

                                    </div>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- HARGA -->

                        <div
                            class="info-item
                                   d-flex
                                   gap-3"
                        >


                            <div
                                class="info-icon"
                            >

                                <i
                                    class="bi bi-ticket-perforated"
                                ></i>

                            </div>


                            <div>


                                <div
                                    class="small
                                           text-muted"
                                >

                                    Harga Tiket

                                </div>


                                <div
                                    class="ticket-price"
                                >


                                    <?php if (
                                        (float)
                                        $ticket_price
                                        >
                                        0
                                    ): ?>


                                        Rp
                                        <?= number_format(
                                            (float)
                                            $ticket_price,
                                            0,
                                            ",",
                                            "."
                                        ) ?>


                                    <?php else: ?>


                                        Gratis

                                    <?php endif; ?>


                                </div>


                            </div>


                        </div>



                        <!-- KONTAK -->

                        <?php if (
                            !empty($contact)
                        ): ?>


                            <div
                                class="info-item
                                       d-flex
                                       gap-3"
                            >


                                <div
                                    class="info-icon"
                                >

                                    <i
                                        class="bi bi-telephone"
                                    ></i>

                                </div>


                                <div>


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Kontak

                                    </div>


                                    <a
                                        href="tel:<?= clean($contact) ?>"
                                        class="fw-semibold
                                               text-decoration-none"
                                    >

                                        <?= clean(
                                            $contact
                                        ) ?>

                                    </a>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- ==================================================
                             ACTION
                        ================================================== -->

                        <div
                            class="pt-4"
                        >


                            <?php if (
                                !empty(
                                    $whatsapp_number
                                )
                            ): ?>


                                <a
                                    href="https://wa.me/<?= clean($whatsapp_number) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn
                                           whatsapp-button
                                           action-button
                                           w-100
                                           mb-2"
                                >

                                    <i
                                        class="bi bi-whatsapp
                                               fs-5"
                                    ></i>


                                    Hubungi via WhatsApp

                                </a>


                            <?php endif; ?>



                            <a
                                href="wisata.php"
                                class="btn
                                       back-button
                                       action-button
                                       w-100"
                            >

                                <i
                                    class="bi bi-arrow-left"
                                ></i>


                                Kembali ke Wisata

                            </a>


                        </div>


                    </div>


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