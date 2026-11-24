<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// VARIABEL
// ======================================================

$error = "";

$title = "";

$media_type = "image";

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
    // UPLOAD
    // ==================================================

    $file_name = "";


    $upload_directory =
        "../../assets/uploads/gallery/";


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


        // ==============================================
        // CEK ERROR
        // ==============================================

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
                    "Format file tidak sesuai dengan tipe media.";

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


            $prefix =
                $media_type
                ===
                "video"
                ?
                "video_"
                :
                "gallery_";


            $file_name =
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

    } elseif (
        empty($error)
    ) {

        $error =
            "File media wajib dipilih.";

    }


    // ==================================================
    // INSERT DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $sql = "
            INSERT INTO gallery
            (
                title,
                media_type,
                file,
                category,
                year
            )
            VALUES
            (
                ?,
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
                "Gagal mempersiapkan database: "
                .
                mysqli_error($conn);


        } else {


            $year_int =
                (int) $year;


            mysqli_stmt_bind_param(
                $stmt,
                "ssssi",
                $title,
                $media_type,
                $file_name,
                $category,
                $year_int
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
                    "Gagal menyimpan galeri: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
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
        Tambah Galeri - Cisalada GO
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


<!-- ======================================================
     NAVBAR
====================================================== -->

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

</nav>



<!-- ======================================================
     CONTENT
====================================================== -->

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

            <h2
                class="fw-bold
                       mb-1"
            >

                Tambah Galeri

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Tambahkan foto atau video
                dokumentasi Desa Cisalada.

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



    <!-- ==================================================
         ERROR
    =================================================== -->

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

            <?= clean($error) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>


    <?php endif; ?>



    <!-- ==================================================
         FORM
    =================================================== -->

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


                <!-- JUDUL -->

                <div
                    class="mb-3"
                >

                    <label
                        for="title"
                        class="form-label
                               fw-semibold"
                    >

                        Judul

                    </label>


                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        placeholder="Contoh: Upacara HUT RI ke-81"
                        value="<?= clean($title) ?>"
                        required
                    >

                </div>



                <!-- TIPE MEDIA -->

                <div
                    class="mb-3"
                >

                    <label
                        for="media_type"
                        class="form-label
                               fw-semibold"
                    >

                        Tipe Media

                    </label>


                    <select
                        id="media_type"
                        name="media_type"
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



                <!-- FILE -->

                <div
                    class="mb-3"
                >

                    <label
                        for="file"
                        class="form-label
                               fw-semibold"
                    >

                        File Media

                    </label>


                    <input
                        type="file"
                        id="file"
                        name="file"
                        class="form-control"
                        required
                    >


                    <div
                        id="fileHelp"
                        class="form-text"
                    >

                        Foto:
                        JPG, JPEG, PNG, WEBP.
                        Maksimal 5 MB.

                    </div>

                </div>



                <!-- KATEGORI -->

                <div
                    class="mb-3"
                >

                    <label
                        for="category"
                        class="form-label
                               fw-semibold"
                    >

                        Kategori

                        <span
                            class="text-muted
                                   fw-normal"
                        >

                            (Opsional)

                        </span>

                    </label>


                    <input
                        type="text"
                        id="category"
                        name="category"
                        class="form-control"
                        placeholder="Contoh: Kegiatan Desa"
                        value="<?= clean($category) ?>"
                    >

                </div>



                <!-- TAHUN -->

                <div
                    class="mb-4"
                >

                    <label
                        for="year"
                        class="form-label
                               fw-semibold"
                    >

                        Tahun

                    </label>


                    <input
                        type="number"
                        id="year"
                        name="year"
                        class="form-control"
                        min="2000"
                        max="2100"
                        value="<?= clean($year) ?>"
                        required
                    >

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
                            class="bi bi-x-lg
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

                        Simpan Galeri

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
            "Foto: JPG, JPEG, PNG, WEBP. Maksimal 5 MB.";

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