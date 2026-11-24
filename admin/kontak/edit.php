<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// DEFAULT
// =========================================================

$contact_address =
    "";

$contact_phone =
    "";

$contact_email =
    "";

$contact_whatsapp =
    "";

$contact_facebook =
    "";

$contact_instagram =
    "";

$contact_youtube =
    "";

$contact_maps =
    "";

$error =
    "";


// =========================================================
// AMBIL DATA
// =========================================================

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


        switch (
            $row["setting_key"]
        ) {


            case "contact_address":

                $contact_address =
                    $row["setting_value"];

                break;


            case "contact_phone":

                $contact_phone =
                    $row["setting_value"];

                break;


            case "contact_email":

                $contact_email =
                    $row["setting_value"];

                break;


            case "contact_whatsapp":

                $contact_whatsapp =
                    $row["setting_value"];

                break;


            case "contact_facebook":

                $contact_facebook =
                    $row["setting_value"];

                break;


            case "contact_instagram":

                $contact_instagram =
                    $row["setting_value"];

                break;


            case "contact_youtube":

                $contact_youtube =
                    $row["setting_value"];

                break;


            case "contact_maps":

                $contact_maps =
                    $row["setting_value"];

                break;


        }

    }

}


// =========================================================
// PROSES SIMPAN
// =========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $contact_address =
        trim(
            $_POST[
                "contact_address"
            ]
            ??
            ""
        );


    $contact_phone =
        trim(
            $_POST[
                "contact_phone"
            ]
            ??
            ""
        );


    $contact_email =
        trim(
            $_POST[
                "contact_email"
            ]
            ??
            ""
        );


    $contact_whatsapp =
        trim(
            $_POST[
                "contact_whatsapp"
            ]
            ??
            ""
        );


    $contact_facebook =
        trim(
            $_POST[
                "contact_facebook"
            ]
            ??
            ""
        );


    $contact_instagram =
        trim(
            $_POST[
                "contact_instagram"
            ]
            ??
            ""
        );


    $contact_youtube =
        trim(
            $_POST[
                "contact_youtube"
            ]
            ??
            ""
        );


    $contact_maps =
        trim(
            $_POST[
                "contact_maps"
            ]
            ??
            ""
        );



    // =====================================================
    // VALIDASI EMAIL
    // =====================================================

    if (
        $contact_email !== ""
        &&
        !filter_var(
            $contact_email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Format email tidak valid.";

    }



    // =====================================================
    // VALIDASI URL
    // =====================================================

    $url_fields = [

        "contact_facebook" =>
            $contact_facebook,

        "contact_instagram" =>
            $contact_instagram,

        "contact_youtube" =>
            $contact_youtube,

        "contact_maps" =>
            $contact_maps

    ];


    if (
        empty($error)
    ) {


        foreach (
            $url_fields
            as $field =>
            $url
        ) {


            if (
                $url !== ""
                &&
                !filter_var(
                    $url,
                    FILTER_VALIDATE_URL
                )
            ) {

                $error =
                    "Link pada "
                    .
                    $field
                    .
                    " tidak valid.";

                break;

            }

        }

    }



    // =====================================================
    // SIMPAN SETTINGS
    // =====================================================

    if (
        empty($error)
    ) {


        $data = [

            "contact_address" =>
                $contact_address,

            "contact_phone" =>
                $contact_phone,

            "contact_email" =>
                $contact_email,

            "contact_whatsapp" =>
                $contact_whatsapp,

            "contact_facebook" =>
                $contact_facebook,

            "contact_instagram" =>
                $contact_instagram,

            "contact_youtube" =>
                $contact_youtube,

            "contact_maps" =>
                $contact_maps

        ];


        $stmt =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO settings
                (
                    setting_key,
                    setting_value
                )
                VALUES
                (
                    ?,
                    ?
                )
                ON DUPLICATE KEY UPDATE
                    setting_value =
                    VALUES(setting_value)
                "
            );


        if (
            !$stmt
        ) {

            $error =
                "Gagal menyiapkan query database.";

        } else {


            foreach (
                $data
                as $key =>
                $value
            ) {


                mysqli_stmt_bind_param(
                    $stmt,
                    "ss",
                    $key,
                    $value
                );


                if (
                    !mysqli_stmt_execute(
                        $stmt
                    )
                ) {

                    $error =
                        "Gagal menyimpan data kontak.";

                    break;

                }

            }


            mysqli_stmt_close(
                $stmt
            );


            if (
                empty($error)
            ) {


                header(
                    "Location: index.php?success=edit"
                );


                exit;

            }

        }

    }

}

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

        Edit Kontak - Cisalada GO

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


        .form-card {

            border:
                0;

            border-radius:
                16px;

        }


        .section-title {

            font-size:
                1rem;

            font-weight:
                700;

            margin-bottom:
                18px;

        }


        textarea {

            resize:
                vertical;

        }


        .help-text {

            font-size:
                0.82rem;

            color:
                #6c757d;

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


        <a
            href="<?= BASE_URL ?>admin/logout.php"
            class="btn
                   btn-outline-light
                   btn-sm"
        >

            <i
                class="bi bi-box-arrow-right
                       me-1"
            ></i>

            Logout

        </a>


    </div>

</nav>



<!-- =====================================================
     CONTENT
===================================================== -->

<div
    class="container
           py-4"
>


    <div
        class="mb-4"
    >


        <a
            href="index.php"
            class="text-decoration-none
                   text-muted"
        >

            <i
                class="bi bi-arrow-left
                       me-1"
            ></i>

            Kembali ke Kontak


        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Edit Kontak Desa

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Perbarui informasi kontak resmi
            yang ditampilkan pada website.

        </p>


    </div>



    <?php if (
        !empty($error)
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


            <?= clean(
                $error
            ) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <div
        class="card
               form-card
               shadow-sm"
    >


        <div
            class="card-body
                   p-4
                   p-lg-5"
        >


            <form
                method="POST"
            >


                <!-- =================================================
                     KONTAK UTAMA
                ================================================== -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-telephone
                               text-primary
                               me-1"
                    ></i>


                    Kontak Utama

                </div>


                <!-- ALAMAT -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Alamat Desa

                    </label>


                    <textarea
                        name="contact_address"
                        class="form-control"
                        rows="4"
                        placeholder="Masukkan alamat lengkap Kantor Desa Cisalada"
                    ><?= clean(
                        $contact_address
                    ) ?></textarea>


                    <div
                        class="help-text
                               mt-1"
                    >

                        Contoh:
                        Kantor Desa Cisalada,
                        Kecamatan Cigombong,
                        Kabupaten Bogor.

                    </div>


                </div>



                <!-- TELEPON -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Nomor Telepon

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-telephone"
                            ></i>

                        </span>


                        <input
                            type="text"
                            name="contact_phone"
                            class="form-control"
                            value="<?= clean(
                                $contact_phone
                            ) ?>"
                            placeholder="Contoh: 0251xxxxxxxx"
                        >


                    </div>


                </div>



                <!-- EMAIL -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Email Desa

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-envelope"
                            ></i>

                        </span>


                        <input
                            type="email"
                            name="contact_email"
                            class="form-control"
                            value="<?= clean(
                                $contact_email
                            ) ?>"
                            placeholder="Contoh: desa@bogorkab.go.id"
                        >


                    </div>


                </div>



                <!-- WHATSAPP -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        WhatsApp

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-whatsapp"
                            ></i>

                        </span>


                        <input
                            type="text"
                            name="contact_whatsapp"
                            class="form-control"
                            value="<?= clean(
                                $contact_whatsapp
                            ) ?>"
                            placeholder="Contoh: 628123456789"
                        >


                    </div>


                    <div
                        class="help-text
                               mt-1"
                    >

                        Bisa diisi nomor WhatsApp
                        dengan format 628xxxxxxxxxx.

                    </div>


                </div>



                <hr
                    class="my-4"
                >



                <!-- =================================================
                     MEDIA SOSIAL
                ================================================== -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-share
                               text-primary
                               me-1"
                    ></i>


                    Media Sosial

                </div>



                <!-- FACEBOOK -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Facebook

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-facebook"
                            ></i>

                        </span>


                        <input
                            type="url"
                            name="contact_facebook"
                            class="form-control"
                            value="<?= clean(
                                $contact_facebook
                            ) ?>"
                            placeholder="https://facebook.com/..."
                        >


                    </div>


                </div>



                <!-- INSTAGRAM -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Instagram

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-instagram"
                            ></i>

                        </span>


                        <input
                            type="url"
                            name="contact_instagram"
                            class="form-control"
                            value="<?= clean(
                                $contact_instagram
                            ) ?>"
                            placeholder="https://instagram.com/..."
                        >


                    </div>


                </div>



                <!-- YOUTUBE -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        YouTube

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-youtube"
                            ></i>

                        </span>


                        <input
                            type="url"
                            name="contact_youtube"
                            class="form-control"
                            value="<?= clean(
                                $contact_youtube
                            ) ?>"
                            placeholder="https://youtube.com/..."
                        >


                    </div>


                </div>



                <hr
                    class="my-4"
                >



                <!-- =================================================
                     MAPS
                ================================================== -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-map
                               text-primary
                               me-1"
                    ></i>


                    Lokasi

                </div>


                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Link Google Maps

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            <i
                                class="bi bi-geo-alt"
                            ></i>

                        </span>


                        <input
                            type="url"
                            name="contact_maps"
                            class="form-control"
                            value="<?= clean(
                                $contact_maps
                            ) ?>"
                            placeholder="https://maps.google.com/..."
                        >


                    </div>


                    <div
                        class="help-text
                               mt-1"
                    >

                        Gunakan link Google Maps
                        kantor Desa Cisalada.

                    </div>


                </div>



                <!-- INFO -->

                <div
                    class="alert
                           alert-info"
                >


                    <i
                        class="bi bi-info-circle
                               me-1"
                    ></i>


                    Data yang kamu isi di halaman ini
                    otomatis disimpan pada tabel
                    <strong>settings</strong>
                    dan digunakan oleh halaman Kontak.


                </div>



                <!-- BUTTON -->

                <div
                    class="d-flex
                           gap-2"
                >


                    <a
                        href="index.php"
                        class="btn
                               btn-secondary"
                    >

                        <i
                            class="bi bi-arrow-left
                                   me-1"
                        ></i>


                        Batal

                    </a>


                    <button
                        type="submit"
                        class="btn
                               btn-primary"
                    >

                        <i
                            class="bi bi-save
                                   me-1"
                        ></i>


                        Simpan Kontak

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>


</body>

</html>