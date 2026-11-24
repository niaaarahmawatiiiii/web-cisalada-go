<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


$error = "";

$success = "";


// ======================================================
// DATA DEFAULT
// ======================================================

$name = "";

$contact = "";

$category = "";

$title = "";

$complaint = "";


// ======================================================
// PROSES FORM
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $name =
        trim(
            $_POST["name"]
            ??
            ""
        );


    $contact =
        trim(
            $_POST["contact"]
            ??
            ""
        );


    $category =
        trim(
            $_POST["category"]
            ??
            ""
        );


    $title =
        trim(
            $_POST["title"]
            ??
            ""
        );


    $complaint =
        trim(
            $_POST["complaint"]
            ??
            ""
        );


    // ==================================================
    // VALIDASI
    // ==================================================

    if (
        $name === ""
    ) {

        $error =
            "Nama wajib diisi.";

    } elseif (
        $title === ""
    ) {

        $error =
            "Judul pengaduan wajib diisi.";

    } elseif (
        $complaint === ""
    ) {

        $error =
            "Isi pengaduan wajib diisi.";

    }



    // ==================================================
    // VALIDASI KONTAK
    // ==================================================

    if (
        empty($error)
        &&
        $contact !== ""
    ) {


        if (
            strlen(
                $contact
            )
            >
            100
        ) {

            $error =
                "Kontak terlalu panjang.";

        }

    }



    // ==================================================
    // UPLOAD LAMPIRAN
    // ==================================================

    $attachment_name =
        "";


    $upload_directory =
        "../assets/uploads/pengaduan/";


    if (
        empty($error)
        &&
        isset(
            $_FILES["attachment"]
        )
        &&
        $_FILES["attachment"]["error"]
        !==
        UPLOAD_ERR_NO_FILE
    ) {


        $file =
            $_FILES["attachment"];


        if (
            $file["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "Lampiran gagal diupload.";

        }



        if (
            empty($error)
            &&
            $file["size"]
            >
            5 * 1024 * 1024
        ) {

            $error =
                "Ukuran lampiran maksimal 5 MB.";

        }



        if (
            empty($error)
        ) {


            $extension =
                strtolower(
                    pathinfo(
                        $file["name"],
                        PATHINFO_EXTENSION
                    )
                );


            $allowed_extensions = [

                "jpg",
                "jpeg",
                "png",
                "webp",
                "pdf"

            ];


            if (
                !in_array(
                    $extension,
                    $allowed_extensions,
                    true
                )
            ) {

                $error =
                    "Format lampiran yang diperbolehkan: JPG, JPEG, PNG, WEBP, atau PDF.";

            }

        }



        if (
            empty($error)
        ) {


            $finfo =
                finfo_open(
                    FILEINFO_MIME_TYPE
                );


            $mime =
                finfo_file(
                    $finfo,
                    $file["tmp_name"]
                );


            finfo_close(
                $finfo
            );


            $allowed_mimes = [

                "image/jpeg",
                "image/png",
                "image/webp",
                "application/pdf"

            ];


            if (
                !in_array(
                    $mime,
                    $allowed_mimes,
                    true
                )
            ) {

                $error =
                    "Jenis file lampiran tidak valid.";

            }

        }



        if (
            empty($error)
            &&
            !is_dir(
                $upload_directory
            )
        ) {

            mkdir(
                $upload_directory,
                0777,
                true
            );

        }



        if (
            empty($error)
        ) {


            $attachment_name =
                "pengaduan_"
                .
                date("YmdHis")
                .
                "_"
                .
                uniqid()
                .
                "."
                .
                $extension;


            $destination =
                $upload_directory
                .
                $attachment_name;


            if (
                !move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )
            ) {

                $error =
                    "Lampiran gagal disimpan.";

                $attachment_name =
                    "";

            }

        }

    }



    // ==================================================
    // SIMPAN KE DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $stmt =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO complaints
                (
                    name,
                    contact,
                    category,
                    title,
                    complaint,
                    attachment,
                    status
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'baru'
                )
                "
            );


        if (
            !$stmt
        ) {


            if (
                !empty(
                    $attachment_name
                )
            ) {

                @unlink(
                    $upload_directory
                    .
                    $attachment_name
                );

            }


            $error =
                "Gagal mempersiapkan database.";

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $name,
                $contact,
                $category,
                $title,
                $complaint,
                $attachment_name
            );


            if (
                mysqli_stmt_execute(
                    $stmt
                )
            ) {


                mysqli_stmt_close(
                    $stmt
                );


                $name =
                    "";

                $contact =
                    "";

                $category =
                    "";

                $title =
                    "";

                $complaint =
                    "";


                $success =
                    "Pengaduan berhasil dikirim. Terima kasih atas masukan Anda.";

            } else {


                $error =
                    "Pengaduan gagal dikirim: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    !empty(
                        $attachment_name
                    )
                ) {

                    @unlink(
                        $upload_directory
                        .
                        $attachment_name
                    );

                }

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

        Pengaduan Warga - Cisalada GO

    </title>



    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >



    <!-- ICON -->

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
                55px 0
                45px;

        }



        .complaint-card {

            border:
                0;

            border-radius:
                18px;

        }



        .info-card {

            background:
                #ffffff;

            border:
                1px solid #e9ecef;

            border-radius:
                16px;

        }



        textarea {

            resize:
                vertical;

        }



        .required {

            color:
                #dc3545;

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
            >

                Pengaduan

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
            class="text-danger
                   fw-semibold
                   mb-2"
        >

            ASPIRASI MASYARAKAT

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Pengaduan Warga

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 760px;
            "
        >

            Sampaikan pengaduan, keluhan,
            atau masukan mengenai pelayanan
            dan kondisi Desa Cisalada.

        </p>


    </div>


</section>



<!-- ======================================================
     FORM
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="row
                   justify-content-center
                   g-4"
        >


            <!-- ==================================================
                 FORM CARD
            =================================================== -->

            <div
                class="col-lg-8"
            >


                <div
                    class="card
                           complaint-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4
                               p-lg-5"
                    >


                        <?php if (
                            !empty($success)
                        ): ?>


                            <div
                                class="alert
                                       alert-success
                                       alert-dismissible
                                       fade show"
                            >


                                <i
                                    class="bi bi-check-circle-fill
                                           me-2"
                                ></i>


                                <?= clean(
                                    $success
                                ) ?>


                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                ></button>


                            </div>


                        <?php endif; ?>



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
                                    class="bi bi-exclamation-circle-fill
                                           me-2"
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



                        <form
                            method="POST"
                            enctype="multipart/form-data"
                        >


                            <!-- NAMA -->

                            <div
                                class="mb-3"
                            >


                                <label
                                    class="form-label
                                           fw-semibold"
                                >

                                    Nama

                                    <span
                                        class="required"
                                    >

                                        *

                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    value="<?= clean(
                                        $name
                                    ) ?>"
                                    placeholder="Nama Anda"
                                    required
                                >


                            </div>



                            <!-- KONTAK -->

                            <div
                                class="mb-3"
                            >


                                <label
                                    class="form-label
                                           fw-semibold"
                                >

                                    Email / No. HP

                                    <span
                                        class="text-muted
                                               fw-normal"
                                    >

                                        (Opsional)

                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="contact"
                                    class="form-control"
                                    value="<?= clean(
                                        $contact
                                    ) ?>"
                                    placeholder="Contoh: 081234567890 atau email@contoh.com"
                                >


                            </div>



                            <!-- KATEGORI -->

                            <div
                                class="mb-3"
                            >


                                <label
                                    class="form-label
                                           fw-semibold"
                                >

                                    Kategori

                                </label>


                                <select
                                    name="category"
                                    class="form-select"
                                >


                                    <option
                                        value=""
                                    >

                                        -- Pilih Kategori --

                                    </option>


                                    <option
                                        value="Pelayanan Desa"
                                        <?= $category === "Pelayanan Desa"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Pelayanan Desa

                                    </option>


                                    <option
                                        value="Infrastruktur"
                                        <?= $category === "Infrastruktur"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Infrastruktur

                                    </option>


                                    <option
                                        value="Kebersihan"
                                        <?= $category === "Kebersihan"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Kebersihan

                                    </option>


                                    <option
                                        value="Keamanan"
                                        <?= $category === "Keamanan"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Keamanan

                                    </option>


                                    <option
                                        value="Lingkungan"
                                        <?= $category === "Lingkungan"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Lingkungan

                                    </option>


                                    <option
                                        value="Sosial"
                                        <?= $category === "Sosial"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Sosial

                                    </option>


                                    <option
                                        value="Lainnya"
                                        <?= $category === "Lainnya"
                                            ? "selected"
                                            : "" ?>
                                    >

                                        Lainnya

                                    </option>


                                </select>


                            </div>



                            <!-- JUDUL -->

                            <div
                                class="mb-3"
                            >


                                <label
                                    class="form-label
                                           fw-semibold"
                                >

                                    Judul Pengaduan

                                    <span
                                        class="required"
                                    >

                                        *

                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    value="<?= clean(
                                        $title
                                    ) ?>"
                                    placeholder="Tuliskan judul pengaduan"
                                    required
                                >


                            </div>



                            <!-- ISI -->

                            <div
                                class="mb-3"
                            >


                                <label
                                    class="form-label
                                           fw-semibold"
                                >

                                    Isi Pengaduan

                                    <span
                                        class="required"
                                    >

                                        *

                                    </span>

                                </label>


                                <textarea
                                    name="complaint"
                                    class="form-control"
                                    rows="8"
                                    placeholder="Jelaskan pengaduan atau masukan Anda secara jelas."
                                    required
                                ><?= clean(
                                    $complaint
                                ) ?></textarea>


                            </div>



                            <!-- LAMPIRAN -->

                            <div
                                class="mb-4"
                            >


                                <label
                                    class="form-label
                                           fw-semibold"
                                >

                                    Lampiran

                                    <span
                                        class="text-muted
                                               fw-normal"
                                    >

                                        (Opsional)

                                    </span>

                                </label>


                                <input
                                    type="file"
                                    name="attachment"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf"
                                >


                                <div
                                    class="form-text"
                                >

                                    Maksimal 5 MB.
                                    Format JPG, JPEG, PNG, WEBP, atau PDF.

                                </div>


                            </div>



                            <!-- BUTTON -->

                            <button
                                type="submit"
                                class="btn
                                       btn-danger
                                       w-100"
                            >


                                <i
                                    class="bi bi-send
                                           me-1"
                                ></i>


                                Kirim Pengaduan


                            </button>


                        </form>


                    </div>


                </div>


            </div>



            <!-- ==================================================
                 INFO
            =================================================== -->

            <div
                class="col-lg-4"
            >


                <div
                    class="info-card
                           p-4
                           shadow-sm"
                >


                    <div
                        class="d-flex
                               gap-3
                               mb-3"
                    >


                        <div
                            class="text-danger
                                   fs-3"
                        >

                            <i
                                class="bi bi-chat-left-text"
                            ></i>

                        </div>


                        <div>


                            <h5
                                class="fw-bold
                                       mb-1"
                            >

                                Sampaikan Aspirasi

                            </h5>


                            <p
                                class="text-muted
                                       small
                                       mb-0"
                            >

                                Pengaduan Anda akan
                                diterima oleh admin
                                Desa Cisalada.

                            </p>


                        </div>


                    </div>



                    <hr>



                    <div
                        class="small
                               text-muted
                               lh-lg"
                    >


                        <p>

                            Mohon tuliskan pengaduan
                            dengan bahasa yang jelas
                            dan informasi yang benar.

                        </p>


                        <p
                            class="mb-0"
                        >

                            Lampiran dapat digunakan
                            untuk mengirim foto atau
                            dokumen pendukung.

                        </p>


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