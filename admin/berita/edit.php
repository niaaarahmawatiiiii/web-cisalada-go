<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// CEK ID
// ==========================================

if (
    !isset($_GET["id"])
    ||
    !is_numeric(
        $_GET["id"]
    )
) {

    header(
        "Location: index.php"
    );

    exit;

}


$id =
    (int) $_GET["id"];


// ==========================================
// AMBIL DATA BERITA
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "SELECT * FROM news WHERE id = ?"
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


$news =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$news
) {

    header(
        "Location: index.php"
    );

    exit;

}


// ==========================================
// AMBIL KATEGORI
// ==========================================

$category_result =
    mysqli_query(
        $conn,
        "SELECT * FROM news_categories
         ORDER BY name ASC"
    );


// ==========================================
// PROSES UPDATE
// ==========================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    // ======================================
    // VALIDASI CSRF
    // ======================================

    require_csrf();


    $title =
        trim(
            $_POST["title"] ?? ""
        );


    $category_id =
        (int) (
            $_POST["category_id"]
            ?? 0
        );


    $content =
        trim(
            $_POST["content"] ?? ""
        );


    $author =
        trim(
            $_POST["author"] ?? ""
        );


    $published_at =
        $_POST["published_at"]
        ??
        "";



    // ======================================
    // VALIDASI
    // ======================================

    if (
        empty($title)
        ||
        empty($content)
        ||
        empty($author)
        ||
        empty($published_at)
    ) {

        $error =
            "Semua field wajib diisi.";

    } else {


        $image_name =
            $news["image"];


        // ==================================
        // UPLOAD GAMBAR BARU
        // ==================================

        if (
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


            $max_size =
                2 * 1024 * 1024;


            $allowed_extensions = [

                "jpg",

                "jpeg",

                "png",

                "webp"

            ];


            $extension =
                strtolower(
                    pathinfo(
                        $file["name"],
                        PATHINFO_EXTENSION
                    )
                );


            if (
                $file["size"]
                >
                $max_size
            ) {

                $error =
                    "Ukuran gambar maksimal 2 MB.";

            } elseif (
                !in_array(
                    $extension,
                    $allowed_extensions,
                    true
                )
            ) {

                $error =
                    "Format gambar harus JPG, JPEG, PNG, atau WEBP.";

            } else {


                $new_image_name =
                    uniqid(
                        "berita_"
                    )
                    .
                    "."
                    .
                    $extension;


                $upload_path =
                    "../../assets/uploads/berita/"
                    .
                    $new_image_name;


                if (
                    move_uploaded_file(
                        $file["tmp_name"],
                        $upload_path
                    )
                ) {


                    // ==================================
                    // HAPUS GAMBAR LAMA
                    // ==================================

                    if (
                        !empty(
                            $news["image"]
                        )
                    ) {


                        $old_file =
                            "../../assets/uploads/berita/"
                            .
                            $news["image"];


                        if (
                            file_exists(
                                $old_file
                            )
                        ) {

                            unlink(
                                $old_file
                            );

                        }

                    }


                    $image_name =
                        $new_image_name;


                } else {

                    $error =
                        "Gagal mengupload gambar.";

                }

            }

        }



        // ==================================
        // UPDATE DATABASE
        // ==================================

        if (
            !isset($error)
        ) {


            $sql = "
                UPDATE news

                SET
                    category_id = ?,
                    title = ?,
                    content = ?,
                    image = ?,
                    author = ?,
                    published_at = ?

                WHERE id = ?
            ";


            $stmt =
                mysqli_prepare(
                    $conn,
                    $sql
                );


            if (
                !$stmt
            ) {

                $error =
                    "Gagal menyiapkan query.";

            } else {


                mysqli_stmt_bind_param(
                    $stmt,
                    "isssssi",
                    $category_id,
                    $title,
                    $content,
                    $image_name,
                    $author,
                    $published_at,
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


                    regenerate_csrf_token();


                    header(
                        "Location: index.php?success=edit"
                    );

                    exit;


                } else {


                    $error =
                        "Gagal memperbarui berita.";


                    mysqli_stmt_close(
                        $stmt
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

        Edit Berita - Cisalada GO

    </title>


    <link
        rel="icon"
        type="image/png"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >


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


<nav
    class="navbar navbar-dark bg-primary"
>

    <div
        class="container"
    >

        <a
            href="index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>

    </div>

</nav>



<div
    class="container py-4"
>


    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >

        <div>

            <h2
                class="fw-bold"
            >

                Edit Berita

            </h2>


            <p
                class="text-muted mb-0"
            >

                Perbarui informasi berita.

            </p>

        </div>


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            Kembali

        </a>

    </div>



    <?php if (
        isset($error)
    ): ?>

        <div
            class="alert alert-danger"
        >

            <?= clean($error) ?>

        </div>

    <?php endif; ?>



    <div
        class="card border-0 shadow-sm"
    >

        <div
            class="card-body p-4"
        >


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- ==========================================
                     CSRF
                =========================================== -->

                <?= csrf_field() ?>



                <!-- JUDUL -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Judul Berita

                    </label>


                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?= clean(
                            $news["title"]
                        ) ?>"
                        required
                    >

                </div>



                <!-- KATEGORI -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Kategori

                    </label>


                    <select
                        name="category_id"
                        class="form-select"
                        required
                    >

                        <?php while (
                            $category =
                            mysqli_fetch_assoc(
                                $category_result
                            )
                        ): ?>


                            <option
                                value="<?= $category["id"] ?>"
                                <?= (
                                    $news[
                                        "category_id"
                                    ]
                                    ==
                                    $category[
                                        "id"
                                    ]
                                )
                                    ? "selected"
                                    : ""
                                ?>
                            >

                                <?= clean(
                                    $category[
                                        "name"
                                    ]
                                ) ?>

                            </option>


                        <?php endwhile; ?>


                    </select>

                </div>



                <!-- PENULIS -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Penulis

                    </label>


                    <input
                        type="text"
                        name="author"
                        class="form-control"
                        value="<?= clean(
                            $news["author"]
                        ) ?>"
                        required
                    >

                </div>



                <!-- TANGGAL -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Tanggal Publikasi

                    </label>


                    <input
                        type="date"
                        name="published_at"
                        class="form-control"
                        value="<?= clean(
                            $news[
                                "published_at"
                            ]
                        ) ?>"
                        required
                    >

                </div>



                <!-- ISI -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Isi Berita

                    </label>


                    <textarea
                        name="content"
                        class="form-control"
                        rows="10"
                        required
                    ><?= clean(
                        $news["content"]
                    ) ?></textarea>

                </div>



                <!-- GAMBAR SAAT INI -->

                <?php if (
                    !empty(
                        $news["image"]
                    )
                ): ?>

                    <div
                        class="mb-3"
                    >

                        <label
                            class="form-label fw-semibold"
                        >

                            Gambar Saat Ini

                        </label>


                        <br>


                        <img
                            src="../../assets/uploads/berita/<?= clean(
                                $news["image"]
                            ) ?>"
                            class="rounded"
                            style="
                                width:200px;
                                height:120px;
                                object-fit:cover;
                            "
                            alt="Gambar berita"
                        >

                    </div>

                <?php endif; ?>



                <!-- GANTI GAMBAR -->

                <div
                    class="mb-4"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Ganti Gambar

                    </label>


                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                    >


                    <small
                        class="text-muted"
                    >

                        Kosongkan jika tidak ingin
                        mengganti gambar.

                    </small>

                </div>



                <!-- BUTTON -->

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i
                        class="bi bi-check-lg me-1"
                    ></i>

                    Simpan Perubahan

                </button>


                <a
                    href="index.php"
                    class="btn btn-secondary"
                >

                    Batal

                </a>


            </form>

        </div>

    </div>

</div>


</body>

</html>