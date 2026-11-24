<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$error = "";


$name = "";

$category = "";

$year = date("Y");


// ======================================================
// PROSES TAMBAH
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


    $category =
        trim(
            $_POST["category"]
            ??
            ""
        );


    $year =
        trim(
            $_POST["year"]
            ??
            ""
        );


    // ==================================================
    // VALIDASI
    // ==================================================

    if (
        empty($name)
    ) {

        $error =
            "Nama dokumen wajib diisi.";

    } elseif (
        empty($year)
        ||
        !is_numeric($year)
        ||
        (int) $year < 2000
        ||
        (int) $year > 2100
    ) {

        $error =
            "Tahun harus berada antara 2000 sampai 2100.";

    }


    // ==================================================
    // UPLOAD FILE
    // ==================================================

    $file_name = "";

    $upload_directory =
        "../../assets/uploads/dokumen/";


    if (
        empty($error)
        &&
        isset(
            $_FILES["file"]
        )
    ) {


        $uploaded_file =
            $_FILES["file"];


        if (
            $uploaded_file["error"]
            ===
            UPLOAD_ERR_NO_FILE
        ) {

            $error =
                "File PDF wajib dipilih.";

        }


        // ==============================================
        // ERROR UPLOAD
        // ==============================================

        if (
            empty($error)
            &&
            $uploaded_file["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "File gagal diupload.";

        }


        // ==============================================
        // UKURAN
        // ==============================================

        if (
            empty($error)
            &&
            $uploaded_file["size"]
            >
            10 * 1024 * 1024
        ) {

            $error =
                "Ukuran PDF maksimal 10 MB.";

        }


        // ==============================================
        // EKSTENSI
        // ==============================================

        if (
            empty($error)
        ) {


            $extension =
                strtolower(
                    pathinfo(
                        $uploaded_file["name"],
                        PATHINFO_EXTENSION
                    )
                );


            if (
                $extension
                !==
                "pdf"
            ) {

                $error =
                    "File harus berformat PDF.";

            }

        }


        // ==============================================
        // MIME TYPE
        // ==============================================

        if (
            empty($error)
        ) {


            $finfo =
                finfo_open(
                    FILEINFO_MIME_TYPE
                );


            $mime_type =
                finfo_file(
                    $finfo,
                    $uploaded_file["tmp_name"]
                );


            finfo_close(
                $finfo
            );


            if (
                $mime_type
                !==
                "application/pdf"
            ) {

                $error =
                    "File yang dipilih bukan PDF yang valid.";

            }

        }


        // ==============================================
        // FOLDER
        // ==============================================

        if (
            empty($error)
            &&
            !is_dir(
                $upload_directory
            )
        ) {

            if (
                !mkdir(
                    $upload_directory,
                    0777,
                    true
                )
            ) {

                $error =
                    "Folder upload gagal dibuat.";

            }

        }


        // ==============================================
        // SIMPAN FILE
        // ==============================================

        if (
            empty($error)
        ) {


            $file_name =
                "dokumen_"
                .
                date("YmdHis")
                .
                "_"
                .
                uniqid()
                .
                ".pdf";


            $destination =
                $upload_directory
                .
                $file_name;


            if (
                !move_uploaded_file(
                    $uploaded_file[
                        "tmp_name"
                    ],
                    $destination
                )
            ) {

                $error =
                    "File gagal disimpan.";

                $file_name = "";

            }

        }

    }


    // ==================================================
    // INSERT DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $sql = "
            INSERT INTO documents
            (
                name,
                category,
                year,
                file
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ";


        $stmt =
            mysqli_prepare(
                $conn,
                $sql
            );


        if (!$stmt) {


            if (
                !empty($file_name)
            ) {

                @unlink(
                    $upload_directory
                    .
                    $file_name
                );

            }


            $error =
                "Gagal mempersiapkan database.";

        } else {


            $year_int =
                (int) $year;


            mysqli_stmt_bind_param(
                $stmt,
                "ssis",
                $name,
                $category,
                $year_int,
                $file_name
            );


            if (
                mysqli_stmt_execute(
                    $stmt
                )
            ) {


                mysqli_stmt_close(
                    $stmt
                );


                header(
                    "Location: index.php?success=tambah"
                );

                exit;


            } else {


                $error =
                    "Gagal menyimpan dokumen: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    !empty($file_name)
                ) {

                    @unlink(
                        $upload_directory
                        .
                        $file_name
                    );

                }

            }

        }

    }

}

?>


<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Tambah Dokumen - Cisalada GO
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="bg-light">


<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>


        <a
            href="<?= BASE_URL ?>admin/logout.php"
            class="btn btn-outline-light btn-sm"
        >

            <i
                class="bi bi-box-arrow-right me-1"
            ></i>

            Logout

        </a>

    </div>

</nav>



<div class="container py-4">


    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >

        <div>

            <h2 class="fw-bold mb-1">

                Tambah Dokumen

            </h2>


            <p class="text-muted mb-0">

                Upload dokumen publik Desa Cisalada.

            </p>

        </div>


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            <i
                class="bi bi-arrow-left me-1"
            ></i>

            Kembali

        </a>

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
                class="bi bi-exclamation-circle me-1"
            ></i>

            <?= clean($error) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>



    <div
        class="card
               border-0
               shadow-sm"
    >

        <div class="card-body p-4">


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- NAMA -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Nama Dokumen

                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Contoh: APBDes Desa Cisalada Tahun 2026"
                        value="<?= clean($name) ?>"
                        required
                    >

                </div>



                <!-- KATEGORI -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Kategori

                    </label>


                    <input
                        type="text"
                        name="category"
                        class="form-control"
                        placeholder="Contoh: APBDes, Peraturan Desa, Profil Desa"
                        value="<?= clean($category) ?>"
                    >

                </div>



                <!-- TAHUN -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Tahun

                    </label>


                    <input
                        type="number"
                        name="year"
                        class="form-control"
                        min="2000"
                        max="2100"
                        value="<?= clean($year) ?>"
                        required
                    >

                </div>



                <!-- FILE -->

                <div class="mb-4">

                    <label
                        class="form-label fw-semibold"
                    >

                        File Dokumen

                    </label>


                    <input
                        type="file"
                        name="file"
                        class="form-control"
                        accept=".pdf,application/pdf"
                        required
                    >


                    <div class="form-text">

                        Hanya PDF.
                        Ukuran maksimal 10 MB.

                    </div>

                </div>



                <!-- BUTTON -->

                <div
                    class="d-flex
                           gap-2"
                >

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >

                        Batal

                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i
                            class="bi bi-save me-1"
                        ></i>

                        Simpan Dokumen

                    </button>

                </div>


            </form>


        </div>

    </div>


</div>


</body>

</html>