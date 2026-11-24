<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$error = "";


$umkm_id = "";


$name = "";


$description = "";


$price = "";


// ======================================================
// AMBIL DATA UMKM
// ======================================================

$umkm_query =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            name,
            owner
        FROM umkm
        ORDER BY
            name ASC
        "
    );


// ======================================================
// PROSES TAMBAH
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {


    $umkm_id =
        (int)
        (
            $_POST["umkm_id"]
            ??
            0
        );


    $name =
        trim(
            $_POST["name"]
            ??
            ""
        );


    $description =
        trim(
            $_POST["description"]
            ??
            ""
        );


    $price =
        trim(
            $_POST["price"]
            ??
            ""
        );


    // ==================================================
    // VALIDASI
    // ==================================================

    if (
        $umkm_id <= 0
    ) {

        $error =
            "UMKM wajib dipilih.";

    } elseif (
        $name === ""
    ) {

        $error =
            "Nama produk wajib diisi.";

    }


    // ==================================================
    // VALIDASI HARGA
    // ==================================================

    $price_value =
        0;


    if (
        empty($error)
        &&
        $price !== ""
    ) {


        if (
            !is_numeric(
                $price
            )
        ) {

            $error =
                "Harga harus berupa angka.";

        } elseif (
            (float)
            $price
            <
            0
        ) {

            $error =
                "Harga tidak boleh kurang dari 0.";

        } else {

            $price_value =
                (float)
                $price;

        }

    }



    // ==================================================
    // UPLOAD FOTO
    // ==================================================

    $image_name =
        "";


    $upload_directory =
        "../../assets/uploads/products/";


    if (
        empty($error)
        &&
        isset(
            $_FILES["image"]
        )
        &&
        $_FILES["image"]["error"]
        !==
        UPLOAD_ERR_NO_FILE
    ) {


        $file =
            $_FILES["image"];


        if (
            $file["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "Foto produk gagal diupload.";

        }


        if (
            empty($error)
            &&
            $file["size"]
            >
            5 * 1024 * 1024
        ) {

            $error =
                "Ukuran foto maksimal 5 MB.";

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
                "webp"

            ];


            if (
                !in_array(
                    $extension,
                    $allowed_extensions,
                    true
                )
            ) {

                $error =
                    "Format foto harus JPG, JPEG, PNG, atau WEBP.";

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
                "image/webp"

            ];


            if (
                !in_array(
                    $mime,
                    $allowed_mimes,
                    true
                )
            ) {

                $error =
                    "File yang diupload bukan gambar yang valid.";

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


            $image_name =
                "product_"
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
                $image_name;


            if (
                !move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )
            ) {

                $error =
                    "Foto produk gagal disimpan.";

                $image_name =
                    "";

            }

        }

    }



    // ==================================================
    // SIMPAN DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $sql = "
            INSERT INTO products
            (
                umkm_id,
                name,
                description,
                price,
                image
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


        if (
            !$stmt
        ) {


            if (
                !empty(
                    $image_name
                )
            ) {

                @unlink(
                    $upload_directory
                    .
                    $image_name
                );

            }


            $error =
                "Gagal mempersiapkan database: "
                .
                mysqli_error(
                    $conn
                );

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "issds",
                $umkm_id,
                $name,
                $description,
                $price_value,
                $image_name
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
                    "Gagal menyimpan produk: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    !empty(
                        $image_name
                    )
                ) {

                    @unlink(
                        $upload_directory
                        .
                        $image_name
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

        Tambah Produk
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

</head>


<body
    class="bg-light"
>


<!-- ======================================================
     NAVBAR
====================================================== -->

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

            Kembali ke Produk

        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Tambah Produk

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Tambahkan produk dari UMKM Desa Cisalada.

        </p>


    </div>



    <!-- ERROR -->

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



    <!-- FORM -->

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


                <!-- UMKM -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        UMKM

                        <span
                            class="text-danger"
                        >

                            *

                        </span>


                    </label>


                    <select
                        name="umkm_id"
                        class="form-select"
                        required
                    >


                        <option
                            value=""
                        >

                            -- Pilih UMKM --

                        </option>


                        <?php if (
                            $umkm_query
                            &&
                            mysqli_num_rows(
                                $umkm_query
                            ) > 0
                        ): ?>


                            <?php while (
                                $umkm =
                                    mysqli_fetch_assoc(
                                        $umkm_query
                                    )
                            ): ?>


                                <option
                                    value="<?= (int) $umkm["id"] ?>"
                                    <?= (int) $umkm_id
                                        ===
                                        (int) $umkm["id"]
                                        ?
                                        "selected"
                                        :
                                        "" ?>
                                >

                                    <?= clean(
                                        $umkm["name"]
                                    ) ?>


                                    <?php if (
                                        !empty(
                                            $umkm["owner"]
                                        )
                                    ): ?>

                                        —
                                        <?= clean(
                                            $umkm["owner"]
                                        ) ?>

                                    <?php endif; ?>


                                </option>


                            <?php endwhile; ?>


                        <?php endif; ?>


                    </select>


                </div>



                <!-- NAMA -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Nama Produk

                        <span
                            class="text-danger"
                        >

                            *

                        </span>


                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= clean($name) ?>"
                        placeholder="Contoh: Keripik Pisang Original"
                        required
                    >


                </div>



                <!-- HARGA -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Harga

                    </label>


                    <div
                        class="input-group"
                    >

                        <span
                            class="input-group-text"
                        >

                            Rp

                        </span>


                        <input
                            type="number"
                            name="price"
                            class="form-control"
                            value="<?= clean($price) ?>"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 15000"
                        >


                    </div>


                    <div
                        class="form-text"
                    >

                        Kosongkan jika harga tidak ingin
                        ditampilkan.

                    </div>


                </div>



                <!-- DESKRIPSI -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Deskripsi Produk

                    </label>


                    <textarea
                        name="description"
                        class="form-control"
                        rows="5"
                        placeholder="Jelaskan produk secara singkat."
                    ><?= clean($description) ?></textarea>


                </div>



                <!-- FOTO -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Foto Produk

                    </label>


                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                    >


                    <div
                        class="form-text"
                    >

                        Format JPG, JPEG, PNG, atau WEBP.
                        Maksimal 5 MB.

                    </div>


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


                        Simpan Produk

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>