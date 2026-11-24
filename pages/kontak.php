<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// =========================================================
// AMBIL SETTINGS
// =========================================================

$settings = [];


$result =
    mysqli_query(
        $conn,
        "
        SELECT
            setting_key,
            setting_value
        FROM settings
        "
    );


if (
    $result
) {

    while (
        $row =
            mysqli_fetch_assoc(
                $result
            )
    ) {

        $settings[
            $row["setting_key"]
        ] =
            $row["setting_value"];

    }

}


// =========================================================
// DATA KONTAK
// =========================================================

$contact_address =
    $settings["contact_address"]
    ??
    "";

$contact_phone =
    $settings["contact_phone"]
    ??
    "";

$contact_email =
    $settings["contact_email"]
    ??
    "";

$contact_whatsapp =
    $settings["contact_whatsapp"]
    ??
    "";

$contact_facebook =
    $settings["contact_facebook"]
    ??
    "";

$contact_instagram =
    $settings["contact_instagram"]
    ??
    "";

$contact_youtube =
    $settings["contact_youtube"]
    ??
    "";

$contact_maps =
    $settings["contact_maps"]
    ??
    "";

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

        Kontak Desa -
        Cisalada GO

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


        .page-hero {

            padding:
                55px
                0
                45px;

        }


        .contact-card {

            border:
                0;

            border-radius:
                18px;

            height:
                100%;

            transition:
                transform .2s ease,
                box-shadow .2s ease;

        }


        .contact-card:hover {

            transform:
                translateY(-4px);

            box-shadow:
                0 10px 26px
                rgba(
                    0,
                    0,
                    0,
                    .08
                );

        }


        .contact-icon {

            width:
                54px;

            height:
                54px;

            min-width:
                54px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                14px;

            background:
                #eef4ff;

            color:
                #0d6efd;

            font-size:
                22px;

        }


        .social-card {

            border:
                1px solid
                #e9ecef;

            border-radius:
                14px;

            background:
                #ffffff;

            transition:
                .2s;

        }


        .social-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 8px 22px
                rgba(
                    0,
                    0,
                    0,
                    .06
                );

        }


        .map-card {

            border:
                0;

            border-radius:
                18px;

            overflow:
                hidden;

        }


        .map-placeholder {

            min-height:
                280px;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef4ff;

            text-align:
                center;

            padding:
                30px;

        }


        .contact-value {

            word-break:
                break-word;

        }


        @media (
            max-width: 767.98px
        ) {

            .page-hero {

                padding:
                    40px
                    0
                    35px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<?php

include "../includes/navbar.php";

?>



<!-- =====================================================
     BREADCRUMB
===================================================== -->

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

                Kontak

            </li>


        </ol>


    </nav>


</div>



<!-- =====================================================
     HERO
===================================================== -->

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

            HUBUNGI KAMI

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Kontak Desa

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 760px;
            "
        >

            Temukan informasi kontak resmi
            Desa Cisalada dan lokasi Kantor Desa.

        </p>


    </div>


</section>



<!-- =====================================================
     KONTAK UTAMA
===================================================== -->

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


            <!-- =================================================
                 ALAMAT
            ================================================== -->

            <div
                class="col-md-6
                       col-lg-4"
            >


                <div
                    class="card
                           contact-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <div
                            class="d-flex
                                   gap-3"
                        >


                            <div
                                class="contact-icon"
                            >

                                <i
                                    class="bi bi-geo-alt"
                                ></i>

                            </div>


                            <div
                                class="flex-grow-1"
                            >


                                <h5
                                    class="fw-bold
                                           mb-2"
                                >

                                    Alamat

                                </h5>


                                <?php if (
                                    $contact_address !==
                                    ""
                                ): ?>


                                    <div
                                        class="text-muted
                                               contact-value
                                               lh-lg"
                                    >

                                        <?= nl2br(
                                            clean(
                                                $contact_address
                                            )
                                        ) ?>


                                    </div>


                                <?php else: ?>


                                    <div
                                        class="text-muted"
                                    >

                                        [ALAMAT]

                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>


                    </div>


                </div>


            </div>



            <!-- =================================================
                 TELEPON
            ================================================== -->

            <div
                class="col-md-6
                       col-lg-4"
            >


                <div
                    class="card
                           contact-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <div
                            class="d-flex
                                   gap-3"
                        >


                            <div
                                class="contact-icon"
                            >

                                <i
                                    class="bi bi-telephone"
                                ></i>

                            </div>


                            <div
                                class="flex-grow-1"
                            >


                                <h5
                                    class="fw-bold
                                           mb-2"
                                >

                                    Telepon

                                </h5>


                                <?php if (
                                    $contact_phone !==
                                    ""
                                ): ?>


                                    <div
                                        class="text-muted
                                               contact-value
                                               mb-3"
                                    >

                                        <?= clean(
                                            $contact_phone
                                        ) ?>


                                    </div>


                                    <a
                                        href="tel:<?= rawurlencode(
                                            $contact_phone
                                        ) ?>"
                                        class="btn
                                               btn-outline-primary
                                               btn-sm"
                                    >

                                        <i
                                            class="bi bi-telephone
                                                   me-1"
                                        ></i>


                                        Telepon

                                    </a>


                                <?php else: ?>


                                    <div
                                        class="text-muted"
                                    >

                                        [TELEPON DESA]

                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>


                    </div>


                </div>


            </div>



            <!-- =================================================
                 EMAIL
            ================================================== -->

            <div
                class="col-md-6
                       col-lg-4"
            >


                <div
                    class="card
                           contact-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <div
                            class="d-flex
                                   gap-3"
                        >


                            <div
                                class="contact-icon"
                            >

                                <i
                                    class="bi bi-envelope"
                                ></i>

                            </div>


                            <div
                                class="flex-grow-1"
                            >


                                <h5
                                    class="fw-bold
                                           mb-2"
                                >

                                    Email

                                </h5>


                                <?php if (
                                    $contact_email !==
                                    ""
                                ): ?>


                                    <div
                                        class="text-muted
                                               contact-value
                                               mb-3"
                                    >

                                        <?= clean(
                                            $contact_email
                                        ) ?>


                                    </div>


                                    <a
                                        href="mailto:<?= rawurlencode(
                                            $contact_email
                                        ) ?>"
                                        class="btn
                                               btn-outline-primary
                                               btn-sm"
                                    >

                                        <i
                                            class="bi bi-envelope
                                                   me-1"
                                        ></i>


                                        Kirim Email

                                    </a>


                                <?php else: ?>


                                    <div
                                        class="text-muted"
                                    >

                                        [EMAIL DESA]

                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>


                    </div>


                </div>


            </div>



            <!-- =================================================
                 WHATSAPP
            ================================================== -->

            <div
                class="col-md-6
                       col-lg-4"
            >


                <div
                    class="card
                           contact-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <div
                            class="d-flex
                                   gap-3"
                        >


                            <div
                                class="contact-icon"
                            >

                                <i
                                    class="bi bi-whatsapp"
                                ></i>

                            </div>


                            <div
                                class="flex-grow-1"
                            >


                                <h5
                                    class="fw-bold
                                           mb-2"
                                >

                                    WhatsApp

                                </h5>


                                <?php if (
                                    $contact_whatsapp !==
                                    ""
                                ): ?>


                                    <div
                                        class="text-muted
                                               contact-value
                                               mb-3"
                                    >

                                        <?= clean(
                                            $contact_whatsapp
                                        ) ?>


                                    </div>


                                    <?php

                                    $wa_number =
                                        preg_replace(
                                            "/[^0-9]/",
                                            "",
                                            $contact_whatsapp
                                        );


                                    if (
                                        str_starts_with(
                                            $wa_number,
                                            "0"
                                        )
                                    ) {

                                        $wa_number =
                                            "62"
                                            .
                                            substr(
                                                $wa_number,
                                                1
                                            );

                                    }

                                    ?>


                                    <a
                                        href="https://wa.me/<?= clean(
                                            $wa_number
                                        ) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn
                                               btn-outline-success
                                               btn-sm"
                                    >

                                        <i
                                            class="bi bi-whatsapp
                                                   me-1"
                                        ></i>


                                        Buka WhatsApp

                                    </a>


                                <?php else: ?>


                                    <div
                                        class="text-muted"
                                    >

                                        [WHATSAPP DESA]

                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </div>


</section>



<!-- =====================================================
     MEDIA SOSIAL
===================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="text-center
                   mb-4"
        >


            <h3
                class="fw-bold"
            >

                Media Sosial

            </h3>


            <p
                class="text-muted
                       mb-0"
            >

                Ikuti informasi dan kegiatan
                Desa Cisalada melalui media sosial.

            </p>


        </div>



        <div
            class="row
                   g-3
                   justify-content-center"
        >


            <!-- FACEBOOK -->

            <?php if (
                $contact_facebook !==
                ""
            ): ?>


                <div
                    class="col-sm-6
                           col-lg-3"
                >


                    <a
                        href="<?= clean(
                            $contact_facebook
                        ) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-decoration-none"
                    >


                        <div
                            class="social-card
                                   p-4
                                   text-center"
                        >


                            <i
                                class="bi bi-facebook
                                       fs-2
                                       text-primary"
                            ></i>


                            <div
                                class="fw-semibold
                                       text-dark
                                       mt-2"
                            >

                                Facebook

                            </div>


                        </div>


                    </a>


                </div>


            <?php endif; ?>



            <!-- INSTAGRAM -->

            <?php if (
                $contact_instagram !==
                ""
            ): ?>


                <div
                    class="col-sm-6
                           col-lg-3"
                >


                    <a
                        href="<?= clean(
                            $contact_instagram
                        ) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-decoration-none"
                    >


                        <div
                            class="social-card
                                   p-4
                                   text-center"
                        >


                            <i
                                class="bi bi-instagram
                                       fs-2
                                       text-primary"
                            ></i>


                            <div
                                class="fw-semibold
                                       text-dark
                                       mt-2"
                            >

                                Instagram

                            </div>


                        </div>


                    </a>


                </div>


            <?php endif; ?>



            <!-- YOUTUBE -->

            <?php if (
                $contact_youtube !==
                ""
            ): ?>


                <div
                    class="col-sm-6
                           col-lg-3"
                >


                    <a
                        href="<?= clean(
                            $contact_youtube
                        ) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-decoration-none"
                    >


                        <div
                            class="social-card
                                   p-4
                                   text-center"
                        >


                            <i
                                class="bi bi-youtube
                                       fs-2
                                       text-danger"
                            ></i>


                            <div
                                class="fw-semibold
                                       text-dark
                                       mt-2"
                            >

                                YouTube

                            </div>


                        </div>


                    </a>


                </div>


            <?php endif; ?>



        </div>


    </div>


</section>



<!-- =====================================================
     LOKASI
===================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="card
                   map-card
                   shadow-sm"
        >


            <div
                class="card-body
                       p-0"
            >


                <?php if (
                    $contact_maps !==
                    ""
                ): ?>


                    <div
                        class="map-placeholder"
                    >


                        <i
                            class="bi bi-geo-alt-fill
                                   text-primary
                                   fs-1
                                   mb-3"
                        ></i>


                        <h4
                            class="fw-bold
                                   mb-2"
                        >

                            Lokasi Kantor Desa

                        </h4>


                        <p
                            class="text-muted
                                   mb-4"
                        >

                            Klik tombol di bawah
                            untuk melihat lokasi
                            Kantor Desa Cisalada
                            di Google Maps.

                        </p>


                        <a
                            href="<?= clean(
                                $contact_maps
                            ) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn
                                   btn-primary"
                        >

                            <i
                                class="bi bi-google
                                       me-1"
                            ></i>


                            Buka Google Maps

                        </a>


                    </div>


                <?php else: ?>


                    <div
                        class="map-placeholder"
                    >


                        <i
                            class="bi bi-geo-alt
                                   text-muted
                                   fs-1
                                   mb-3"
                        ></i>


                        <h4
                            class="fw-bold
                                   mb-2"
                        >

                            Lokasi Kantor Desa

                        </h4>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            [LINK GOOGLE MAPS
                            BELUM TERSEDIA]

                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </div>


</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<?php

include "../includes/footer.php";

?>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>