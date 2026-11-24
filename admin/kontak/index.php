<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// AMBIL DATA SETTINGS
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
        ORDER BY id ASC
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


// =========================================================
// DETEKSI PESAN
// =========================================================

$success =
    $_GET["success"]
    ??
    "";

$error =
    $_GET["error"]
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

        Kelola Kontak - Cisalada GO

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
                #f5f7fa;

        }


        .page-card {

            border:
                0;

            border-radius:
                16px;

        }


        .contact-icon {

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

            font-size:
                20px;

        }


        .value-text {

            word-break:
                break-word;

        }


        .empty-text {

            color:
                #adb5bd;

            font-style:
                italic;

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav
    class="navbar
           navbar-dark
           bg-primary"
>

    <div
        class="container-fluid
               px-4"
    >


        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand
                   fw-bold"
        >

            CISALADA GO

        </a>


        <div
            class="d-flex
                   align-items-center"
        >


            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="btn
                       btn-light
                       btn-sm
                       me-2"
            >

                <i
                    class="bi bi-speedometer2
                           me-1"
                ></i>

                Dashboard

            </a>


            <a
                href="<?= BASE_URL ?>admin/logout.php"
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



<!-- =====================================================
     CONTENT
===================================================== -->

<div
    class="container
           py-4"
>


    <!-- HEADER -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               flex-wrap
               gap-3
               mb-4"
    >


        <div>


            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="text-decoration-none
                       text-muted"
            >

                <i
                    class="bi bi-arrow-left
                           me-1"
                ></i>

                Kembali ke Dashboard

            </a>


            <h2
                class="fw-bold
                       mt-3
                       mb-1"
            >

                Kontak Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Kelola informasi kontak resmi
                Desa Cisalada.

            </p>


        </div>


        <a
            href="edit.php"
            class="btn
                   btn-primary"
        >

            <i
                class="bi bi-pencil
                       me-1"
            ></i>

            Edit Kontak

        </a>


    </div>



    <!-- ALERT SUCCESS -->

    <?php if (
        $success ===
        "edit"
    ): ?>


        <div
            class="alert
                   alert-success
                   alert-dismissible
                   fade show"
        >


            <i
                class="bi bi-check-circle
                       me-1"
            ></i>


            Informasi kontak berhasil diperbarui.


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ALERT ERROR -->

    <?php if (
        $error ===
        "save"
    ): ?>


        <div
            class="alert
                   alert-danger
                   alert-dismissible
                   fade show"
        >


            <i
                class="bi bi-exclamation-circle
                       me-1"
            ></i>


            Informasi kontak gagal disimpan.


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ==================================================
         KONTAK UTAMA
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


            <h5
                class="fw-bold
                       mb-4"
            >

                Kontak Utama

            </h5>


            <div
                class="row
                       g-4"
            >


                <!-- ALAMAT -->

                <div
                    class="col-md-6"
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


                        <div>


                            <div
                                class="text-muted
                                       small
                                       mb-1"
                            >

                                Alamat

                            </div>


                            <?php if (
                                $contact_address !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           fw-semibold"
                                >

                                    <?= nl2br(
                                        clean(
                                            $contact_address
                                        )
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- TELEPON -->

                <div
                    class="col-md-6"
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


                        <div>


                            <div
                                class="text-muted
                                       small
                                       mb-1"
                            >

                                Nomor Telepon

                            </div>


                            <?php if (
                                $contact_phone !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           fw-semibold"
                                >

                                    <?= clean(
                                        $contact_phone
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- EMAIL -->

                <div
                    class="col-md-6"
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


                        <div>


                            <div
                                class="text-muted
                                       small
                                       mb-1"
                            >

                                Email

                            </div>


                            <?php if (
                                $contact_email !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           fw-semibold"
                                >

                                    <?= clean(
                                        $contact_email
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- WHATSAPP -->

                <div
                    class="col-md-6"
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


                        <div>


                            <div
                                class="text-muted
                                       small
                                       mb-1"
                            >

                                WhatsApp

                            </div>


                            <?php if (
                                $contact_whatsapp !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           fw-semibold"
                                >

                                    <?= clean(
                                        $contact_whatsapp
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         MEDIA SOSIAL
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


            <h5
                class="fw-bold
                       mb-4"
            >

                Media Sosial

            </h5>


            <div
                class="row
                       g-4"
            >


                <!-- FACEBOOK -->

                <div
                    class="col-md-4"
                >


                    <div
                        class="d-flex
                               gap-3"
                    >


                        <div
                            class="contact-icon"
                        >

                            <i
                                class="bi bi-facebook"
                            ></i>

                        </div>


                        <div>


                            <div
                                class="text-muted
                                       small"
                            >

                                Facebook

                            </div>


                            <?php if (
                                $contact_facebook !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           small
                                           fw-semibold"
                                >

                                    <?= clean(
                                        $contact_facebook
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text
                                           small"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- INSTAGRAM -->

                <div
                    class="col-md-4"
                >


                    <div
                        class="d-flex
                               gap-3"
                    >


                        <div
                            class="contact-icon"
                        >

                            <i
                                class="bi bi-instagram"
                            ></i>

                        </div>


                        <div>


                            <div
                                class="text-muted
                                       small"
                            >

                                Instagram

                            </div>


                            <?php if (
                                $contact_instagram !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           small
                                           fw-semibold"
                                >

                                    <?= clean(
                                        $contact_instagram
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text
                                           small"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- YOUTUBE -->

                <div
                    class="col-md-4"
                >


                    <div
                        class="d-flex
                               gap-3"
                    >


                        <div
                            class="contact-icon"
                        >

                            <i
                                class="bi bi-youtube"
                            ></i>

                        </div>


                        <div>


                            <div
                                class="text-muted
                                       small"
                            >

                                YouTube

                            </div>


                            <?php if (
                                $contact_youtube !==
                                ""
                            ): ?>


                                <div
                                    class="value-text
                                           small
                                           fw-semibold"
                                >

                                    <?= clean(
                                        $contact_youtube
                                    ) ?>


                                </div>


                            <?php else: ?>


                                <div
                                    class="empty-text
                                           small"
                                >

                                    Belum diisi

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         GOOGLE MAPS
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm"
    >


        <div
            class="card-body
                   p-4"
        >


            <h5
                class="fw-bold
                       mb-4"
            >

                Lokasi Google Maps

            </h5>


            <div
                class="d-flex
                       gap-3
                       align-items-start"
            >


                <div
                    class="contact-icon"
                >

                    <i
                        class="bi bi-map"
                    ></i>

                </div>


                <div
                    class="flex-grow-1"
                >


                    <?php if (
                        $contact_maps !==
                        ""
                    ): ?>


                        <div
                            class="value-text
                                   fw-semibold
                                   mb-3"
                        >

                            <?= clean(
                                $contact_maps
                            ) ?>


                        </div>


                        <a
                            href="<?= clean(
                                $contact_maps
                            ) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn
                                   btn-outline-primary
                                   btn-sm"
                        >

                            <i
                                class="bi bi-box-arrow-up-right
                                       me-1"
                            ></i>

                            Buka Google Maps

                        </a>


                    <?php else: ?>


                        <div
                            class="empty-text"
                        >

                            Link Google Maps
                            belum diisi.

                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </div>


    </div>


</div>



</body>

</html>