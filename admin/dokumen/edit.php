<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$error = "";


if (
    !isset($_GET["id"])
    ||
    !is_numeric($_GET["id"])
) {

    header(
        "Location: index.php"
    );

    exit;

}


$id =
    (int) $_GET["id"];


// ======================================================
// AMBIL DATA
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT *
        FROM documents
        WHERE id = ?
        LIMIT 1
        "
    );


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


$document =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$document
) {

    header(
        "Location: index.php"
    );

    exit;

}


// ======================================================
// DATA FORM
// ======================================================

$name =
    $document["name"]
    ??
    "";

$category =
    $document["category"]
    ??
    "";

$year =
    $document["year"]
    ??
    date("Y");

$old_file =
    $document["file"]
    ??
    "";


// ======================================================
// PROSES UPDATE
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


    $new_file =
        $old_file;


    $new_uploaded =
        false;


    $upload_directory =
        "../../assets/uploads/dokumen/";


    // ==================================================
    // FILE BARU
    // ==================================================

    if (
        empty($error)
        &&
        isset($_FILES["file"])
        &&
        $_FILES["file"]["error"]
        !==
        UPLOAD_ERR_NO_FILE
    ) {


        $uploaded_file =
            $_FILES["file"];


        if (
            $uploaded_file["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "File gagal diupload.";

        }


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
                    "File bukan PDF yang valid.";

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


            $new_file =
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
                $new_file;


            if (
                move_uploaded_file(
                    $uploaded_file[
                        "tmp_name"
                    ],
                    $destination
                )
            ) {

                $new_uploaded = true;

            } else {

                $error =
                    "File baru gagal disimpan.";

            }

        }

    }


    // ==================================================
    // UPDATE DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $year_int =
            (int) $year;


        $stmt =
            mysqli_prepare(
                $conn,
                "
                UPDATE documents

                SET
                    name = ?,
                    category = ?,
                    year = ?,
                    file = ?

                WHERE id = ?
                "
            );


        mysqli_stmt_bind_param(
            $stmt,
            "ssisi",
            $name,
            $category,
            $year_int,
            $new_file,
            $id
        );


        if (
            mysqli_stmt_execute(
                $stmt
            )
        ) {


            mysqli_stmt_close(
                $stmt
            );


            // ==========================================
            // HAPUS FILE LAMA
            // ==========================================

            if (
                $new_uploaded
                &&
                !empty($old_file)
            ) {


                $old_path =
                    $upload_directory
                    .
                    basename(
                        $old_file
                    );


                if (
                    file_exists(
                        $old_path
                    )
                ) {

                    @unlink(
                        $old_path
                    );

                }

            }


            header(
                "Location: index.php?success=edit"
            );

            exit;


        } else {


            $error =
                "Gagal memperbarui dokumen: "
                .
                mysqli_stmt_error(
                    $stmt
                );


            mysqli_stmt_close(
                $stmt
            );


            if (
                $new_uploaded
            ) {

                @unlink(
                    $upload_directory
                    .
                    $new_file
                );

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
        Edit Dokumen - Cisalada GO
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

                Edit Dokumen

            </h2>


            <p class="text-muted mb-0">

                Perbarui informasi dokumen desa.

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
            class="alert alert-danger"
        >

            <?= clean($error) ?>

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
                        value="<?= clean($name) ?>"
                        required
                    >

                </div>



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
                        value="<?= clean($category) ?>"
                    >

                </div>



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



                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        File Baru

                        <span
                            class="text-muted fw-normal"
                        >

                            (Opsional)

                        </span>

                    </label>


                    <input
                        type="file"
                        name="file"
                        class="form-control"
                        accept=".pdf,application/pdf"
                    >


                    <div class="form-text">

                        Kosongkan jika tidak ingin
                        mengganti file.

                    </div>

                </div>



                <?php if (
                    !empty($old_file)
                ): ?>

                    <div
                        class="alert
                               alert-light
                               border"
                    >

                        <strong>
                            File saat ini:
                        </strong>

                        <br>

                        <i
                            class="bi bi-file-earmark-pdf
                                   text-danger
                                   me-1"
                        ></i>

                        <?= clean(
                            $old_file
                        ) ?>

                    </div>

                <?php endif; ?>



                <div class="d-flex gap-2">

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

                        Simpan Perubahan

                    </button>

                </div>


            </form>


        </div>

    </div>


</div>


</body>

</html>