<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// CEK ID
// ======================================================

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
        FROM gallery
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


$gallery =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$gallery
) {

    header(
        "Location: index.php"
    );

    exit;

}


// ======================================================
// VARIABEL
// ======================================================

$error = "";

$title =
    $gallery["title"]
    ??
    "";

$media_type =
    $gallery["media_type"]
    ??
    "image";

$category =
    $gallery["category"]
    ??
    "";

$year =
    $gallery["year"]
    ??
    date("Y");

$old_file =
    $gallery["file"]
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


    $title =
        trim(
            $_POST["title"]
            ??
            ""
        );


    $media_type =
        $_POST["media_type"]
        ??
        "image";


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
        empty($title)
    ) {

        $error =
            "Judul galeri wajib diisi.";

    } elseif (
        !in_array(
            $media_type,
            [
                "image",
                "video"
            ],
            true
        )
    ) {

        $error =
            "Tipe media tidak valid.";

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
    // FILE BARU
    // ==================================================

    $new_file =
        $old_file;


    $upload_directory =
        "../../assets/uploads/gallery/";


    $new_uploaded_file =
        false;


    if (
        empty($error)
        &&
        isset(
            $_FILES["file"]
        )
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


        // ==============================================
        // UKURAN
        // ==============================================

        if (
            empty($error)
            &&
            $media_type
            ===
            "image"
            &&
            $uploaded_file["size"]
            >
            5 * 1024 * 1024
        ) {

            $error =
                "Ukuran foto maksimal 5 MB.";

        }


        if (
            empty($error)
            &&
            $media_type
            ===
            "video"
            &&
            $uploaded_file["size"]
            >
            50 * 1024 * 1024
        ) {

            $error =
                "Ukuran video maksimal 50 MB.";

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
                $media_type
                ===
                "image"
            ) {

                $allowed_extensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];

            } else {

                $allowed_extensions = [
                    "mp4",
                    "webm",
                    "ogg",
                    "mov"
                ];

            }


            if (
                !in_array(
                    $extension,
                    $allowed_extensions,
                    true
                )
            ) {

                $error =
                    "Format file tidak sesuai.";

            }

        }


        // ==============================================
        // MIME
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
                $media_type
                ===
                "image"
            ) {

                $allowed_mimes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];

            } else {

                $allowed_mimes = [
                    "video/mp4",
                    "video/webm",
                    "video/ogg",
                    "video/quicktime"
                ];

            }


            if (
                !in_array(
                    $mime_type,
                    $allowed_mimes,
                    true
                )
            ) {

                $error =
                    "Jenis file tidak valid.";

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

            mkdir(
                $upload_directory,
                0777,
                true
            );

        }


        // ==============================================
        // SIMPAN FILE BARU
        // ==============================================

        if (
            empty($error)
        ) {


            $prefix =
                $media_type
                ===
                "video"
                ?
                "video_"
                :
                "gallery_";


            $new_file =
                $prefix
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
                $new_file;


            if (
                move_uploaded_file(
                    $uploaded_file[
                        "tmp_name"
                    ],
                    $destination
                )
            ) {

                $new_uploaded_file =
                    true;

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


        $sql = "
            UPDATE gallery

            SET
                title = ?,
                media_type = ?,
                file = ?,
                category = ?,
                year = ?

            WHERE id = ?
        ";


        $stmt =
            mysqli_prepare(
                $conn,
                $sql
            );


        $year_int =
            (int) $year;


        mysqli_stmt_bind_param(
            $stmt,
            "ssssii",
            $title,
            $media_type,
            $new_file,
            $category,
            $year_int,
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
                $new_uploaded_file
                &&
                !empty($old_file)
                &&
                $old_file
                !==
                $new_file
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
                "Gagal memperbarui galeri: "
                .
                mysqli_stmt_error(
                    $stmt
                );


            mysqli_stmt_close(
                $stmt
            );


            if (
                $new_uploaded_file
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
        Edit Galeri - Cisalada GO
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


<nav
    class="navbar
           navbar-dark
           bg-primary"
>

    <div
        class="container"
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

            Logout

        </a>

    </div>

</nav>



<div
    class="container
           py-4"
>


    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >

        <div>

            <h2
                class="fw-bold
                       mb-1"
            >

                Edit Galeri

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Perbarui informasi dokumentasi desa.

            </p>

        </div>


        <a
            href="index.php"
            class="btn
                   btn-secondary"
        >

            <i
                class="bi bi-arrow-left
                       me-1"
            ></i>

            Kembali

        </a>

    </div>



    <?php if (
        !empty($error)
    ): ?>

        <div
            class="alert
                   alert-danger"
        >

            <?= clean($error) ?>

        </div>

    <?php endif; ?>



    <div
        class="card
               border-0
               shadow-sm"
    >

        <div
            class="card-body
                   p-4"
        >


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Judul

                    </label>


                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?= clean($title) ?>"
                        required
                    >

                </div>



                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Tipe Media

                    </label>


                    <select
                        name="media_type"
                        id="media_type"
                        class="form-select"
                        required
                    >

                        <option
                            value="image"
                            <?= $media_type === "image"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Foto

                        </option>


                        <option
                            value="video"
                            <?= $media_type === "video"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Video

                        </option>

                    </select>

                </div>



                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        File Baru

                        <span
                            class="text-muted
                                   fw-normal"
                        >

                            (Opsional)

                        </span>

                    </label>


                    <input
                        type="file"
                        name="file"
                        id="file"
                        class="form-control"
                    >


                    <div
                        id="fileHelp"
                        class="form-text"
                    >

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
                               border
                               mb-3"
                    >

                        <strong>
                            File saat ini:
                        </strong>

                        <br>

                        <?= clean(
                            $old_file
                        ) ?>

                    </div>

                <?php endif; ?>



                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Kategori

                    </label>


                    <input
                        type="text"
                        name="category"
                        class="form-control"
                        value="<?= clean($category) ?>"
                        placeholder="Contoh: Kegiatan Desa"
                    >

                </div>



                <div
                    class="mb-4"
                >

                    <label
                        class="form-label
                               fw-semibold"
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



                <div
                    class="d-flex
                           gap-2"
                >

                    <a
                        href="index.php"
                        class="btn
                               btn-secondary"
                    >

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

                        Simpan Perubahan

                    </button>

                </div>


            </form>


        </div>

    </div>


</div>



<script>

const mediaType =
    document.getElementById(
        "media_type"
    );

const fileInput =
    document.getElementById(
        "file"
    );

const fileHelp =
    document.getElementById(
        "fileHelp"
    );


function updateFileInput() {

    if (
        mediaType.value
        ===
        "video"
    ) {

        fileInput.accept =
            ".mp4,.webm,.ogg,.mov";

        fileHelp.textContent =
            "Video: MP4, WEBM, OGG, MOV. Maksimal 50 MB.";

    } else {

        fileInput.accept =
            ".jpg,.jpeg,.png,.webp";

        fileHelp.textContent =
            "Foto: JPG, JPEG, PNG, WEBP. Kosongkan jika tidak mengganti file.";

    }

}


mediaType.addEventListener(
    "change",
    updateFileInput
);


updateFileInput();

</script>


</body>

</html>