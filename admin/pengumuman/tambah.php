<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// ERROR
// ==========================================

$error = "";


// ==========================================
// PROSES TAMBAH PENGUMUMAN
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    // ======================================
    // AMBIL DATA FORM
    // ======================================

    $title =
        trim(
            $_POST["title"] ?? ""
        );


    $type =
        trim(
            $_POST["type"] ?? "Informasi"
        );


    $content =
        trim(
            $_POST["content"] ?? ""
        );


    $published_at =
        $_POST["published_at"] ?? "";


    // ======================================
    // VALIDASI JUDUL
    // ======================================

    if (empty($title)) {

        $error =
            "Judul pengumuman wajib diisi.";

    }


    // ======================================
    // VALIDASI ISI
    // ======================================

    elseif (empty($content)) {

        $error =
            "Isi pengumuman wajib diisi.";

    }


    // ======================================
    // VALIDASI TANGGAL
    // ======================================

    elseif (empty($published_at)) {

        $error =
            "Tanggal pengumuman wajib diisi.";

    }


    // ======================================
    // VALIDASI JENIS
    // ======================================

    elseif (
        !in_array(
            $type,
            [
                "Informasi",
                "Penting",
                "Kegiatan",
                "Layanan"
            ]
        )
    ) {

        $error =
            "Jenis pengumuman tidak valid.";

    }


    // ======================================
    // SIMPAN DATABASE
    // ======================================

    else {


        $sql = "
            INSERT INTO announcements
            (
                title,
                content,
                type,
                published_at
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

            $error =
                "Gagal mempersiapkan query: "
                . mysqli_error($conn);

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $title,
                $content,
                $type,
                $published_at
            );


            if (
                mysqli_stmt_execute($stmt)
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
                    "Gagal menyimpan pengumuman: "
                    . mysqli_stmt_error(
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
        Tambah Pengumuman - Cisalada GO
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="bg-light">


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>


        <div>

            <a
                href="<?= BASE_URL ?>admin/logout.php"
                class="btn btn-outline-light btn-sm"
                onclick="return confirm(
                    'Yakin ingin logout?'
                );"
            >

                <i
                    class="bi bi-box-arrow-right me-1"
                ></i>

                Logout

            </a>

        </div>

    </div>

</nav>



<!-- ==========================================
     CONTENT
========================================== -->

<div class="container py-4">


    <!-- HEADER -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>

            <h2 class="fw-bold mb-1">

                Tambah Pengumuman

            </h2>


            <p class="text-muted mb-0">

                Buat pengumuman baru untuk masyarakat
                Desa Cisalada.

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



    <!-- ==========================================
         ERROR
    ========================================== -->

    <?php if (!empty($error)): ?>

        <div
            class="alert alert-danger
                   alert-dismissible fade show"
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



    <!-- ==========================================
         FORM
    ========================================== -->

    <div
        class="card
               border-0
               shadow-sm"
    >


        <div class="card-body p-4">


            <form
                method="POST"
                autocomplete="off"
            >


                <!-- ==================================
                     JUDUL
                ================================== -->

                <div class="mb-3">

                    <label
                        for="title"
                        class="form-label fw-semibold"
                    >

                        Judul Pengumuman

                    </label>


                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        placeholder="Contoh: Pengumuman Pelayanan Desa"
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



                <!-- ==================================
                     JENIS
                ================================== -->

                <div class="mb-3">

                    <label
                        for="type"
                        class="form-label fw-semibold"
                    >

                        Jenis Pengumuman

                    </label>


                    <select
                        id="type"
                        name="type"
                        class="form-select"
                        required
                    >


                        <option
                            value="Informasi"
                            <?= (
                                (
                                    $_POST["type"]
                                    ?? "Informasi"
                                )
                                ===
                                "Informasi"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Informasi

                        </option>


                        <option
                            value="Penting"
                            <?= (
                                (
                                    $_POST["type"]
                                    ?? ""
                                )
                                ===
                                "Penting"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Penting

                        </option>


                        <option
                            value="Kegiatan"
                            <?= (
                                (
                                    $_POST["type"]
                                    ?? ""
                                )
                                ===
                                "Kegiatan"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Kegiatan

                        </option>


                        <option
                            value="Layanan"
                            <?= (
                                (
                                    $_POST["type"]
                                    ?? ""
                                )
                                ===
                                "Layanan"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Layanan

                        </option>


                    </select>

                </div>



                <!-- ==================================
                     TANGGAL
                ================================== -->

                <div class="mb-3">

                    <label
                        for="published_at"
                        class="form-label fw-semibold"
                    >

                        Tanggal Pengumuman

                    </label>


                    <input
                        type="date"
                        id="published_at"
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
                            : date("Y-m-d")
                        ?>"
                        required
                    >

                </div>



                <!-- ==================================
                     ISI
                ================================== -->

                <div class="mb-4">

                    <label
                        for="content"
                        class="form-label fw-semibold"
                    >

                        Isi Pengumuman

                    </label>


                    <textarea
                        id="content"
                        name="content"
                        class="form-control"
                        rows="10"
                        placeholder="Tuliskan isi pengumuman..."
                        required
                    ><?= isset(
                        $_POST["content"]
                    )
                        ? clean(
                            $_POST["content"]
                        )
                        : ""
                    ?></textarea>


                    <div
                        class="form-text"
                    >

                        Tuliskan informasi yang jelas
                        dan mudah dipahami masyarakat.

                    </div>

                </div>



                <!-- ==================================
                     BUTTON
                ================================== -->

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i
                        class="bi bi-save me-1"
                    ></i>

                    Simpan Pengumuman

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