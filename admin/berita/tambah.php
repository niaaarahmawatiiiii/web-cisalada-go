<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// AMBIL KATEGORI
// ==========================================

$category_result = mysqli_query(
    $conn,
    "SELECT * FROM news_categories
     ORDER BY name ASC"
);


// ==========================================
// PROSES FORM
// ==========================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
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
        empty($title) ||
        empty($content) ||
        empty($author) ||
        empty($published_at)
    ) {

        $error =
            "Semua field wajib diisi.";

    } else {


        // ==================================
        // UPLOAD GAMBAR
        // ==================================

        $image_name =
            null;


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


            // Maksimal 2 MB

            $max_size =
                2 * 1024 * 1024;


            // Ekstensi yang diperbolehkan

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


                // Generate nama file unik

                $image_name =
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
                    $image_name;


                if (
                    !move_uploaded_file(
                        $file["tmp_name"],
                        $upload_path
                    )
                ) {

                    $error =
                        "Gagal mengupload gambar.";

                }

            }

        }



        // ==================================
        // INSERT DATABASE
        // ==================================

        if (
            !isset($error)
        ) {


            $sql = "
                INSERT INTO news
                (
                    category_id,
                    title,
                    content,
                    image,
                    author,
                    published_at
                )

                VALUES (?, ?, ?, ?, ?, ?)
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
                    "isssss",
                    $category_id,
                    $title,
                    $content,
                    $image_name,
                    $author,
                    $published_at
                );


                if (
                    mysqli_stmt_execute(
                        $stmt
                    )
                ) {


                    mysqli_stmt_close(
                        $stmt
                    );


                    // Token baru setelah operasi berhasil

                    regenerate_csrf_token();


                    header(
                        "Location: index.php?success=tambah"
                    );

                    exit;


                } else {


                    $error =
                        "Gagal menyimpan berita: "
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

        Tambah Berita - Cisalada GO

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
                class="fw-bold mb-1"
            >

                Tambah Berita

            </h2>


            <p
                class="text-muted mb-0"
            >

                Tambahkan berita baru.

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
                        value="<?= isset(
                            $_POST["title"]
                        )
                            ? clean(
                                $_POST["title"]
                            )
                            : ""
                        ?>"
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

                        <option
                            value=""
                        >

                            -- Pilih Kategori --

                        </option>


                        <?php while (
                            $category =
                            mysqli_fetch_assoc(
                                $category_result
                            )
                        ): ?>


                            <option
                                value="<?= $category["id"] ?>"
                                <?= (
                                    isset(
                                        $_POST[
                                            "category_id"
                                        ]
                                    )
                                    &&
                                    $_POST[
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
                        value="<?= isset(
                            $_POST["author"]
                        )
                            ? clean(
                                $_POST["author"]
                            )
                            : $_SESSION[
                                "admin_name"
                            ]
                        ?>"
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
                        value="<?= isset(
                            $_POST[
                                "published_at"
                            ]
                        )
                            ? clean(
                                $_POST[
                                    "published_at"
                                ]
                            )
                            : date(
                                "Y-m-d"
                            )
                        ?>"
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
                    ><?= isset(
                        $_POST["content"]
                    )
                        ? clean(
                            $_POST["content"]
                        )
                        : ""
                    ?></textarea>

                </div>



                <!-- GAMBAR -->

                <div
                    class="mb-4"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Gambar Berita

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

                        Format:
                        JPG, JPEG, PNG, WEBP.
                        Maksimal 2 MB.

                    </small>

                </div>



                <!-- BUTTON -->

                <div
                    class="d-flex gap-2"
                >

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i
                            class="bi bi-check-lg me-1"
                        ></i>

                        Simpan Berita

                    </button>


                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >

                        Batal

                    </a>

                </div>


            </form>

        </div>

    </div>

</div>


</body>

</html>