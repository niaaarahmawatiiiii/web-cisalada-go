<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// CEK ID
// ==========================================

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {

    header(
        "Location: index.php"
    );

    exit;
}


$id =
    (int) $_GET["id"];


// ==========================================
// AMBIL DATA
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT *
        FROM announcements
        WHERE id = ?
        LIMIT 1
        "
    );


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute($stmt);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


$announcement =
    mysqli_fetch_assoc(
        $result
    );


if (!$announcement) {

    header(
        "Location: index.php"
    );

    exit;
}


// ==========================================
// ERROR
// ==========================================

$error = "";


// ==========================================
// PROSES UPDATE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


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
    // VALIDASI
    // ======================================

    if (
        empty($title) ||
        empty($content) ||
        empty($published_at)
    ) {

        $error =
            "Judul, isi, dan tanggal wajib diisi.";

    } else {


        // ==================================
        // UPDATE
        // ==================================

        $sql = "
            UPDATE announcements

            SET
                title = ?,
                content = ?,
                type = ?,
                published_at = ?

            WHERE id = ?
        ";


        $stmt =
            mysqli_prepare(
                $conn,
                $sql
            );


        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $title,
            $content,
            $type,
            $published_at,
            $id
        );


        if (
            mysqli_stmt_execute($stmt)
        ) {


            header(
                "Location: index.php?success=edit"
            );

            exit;


        } else {


            $error =
                "Gagal memperbarui pengumuman.";

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
        Edit Pengumuman - Cisalada GO
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


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>

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

                Edit Pengumuman

            </h2>


            <p class="text-muted mb-0">

                Perbarui informasi pengumuman.

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



    <!-- ERROR -->

    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <i
                class="bi bi-exclamation-circle me-1"
            ></i>

            <?= clean($error) ?>

        </div>

    <?php endif; ?>



    <!-- FORM -->

    <div
        class="card
               border-0
               shadow-sm"
    >


        <div class="card-body p-4">


            <form method="POST">


                <!-- JUDUL -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Judul Pengumuman

                    </label>


                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?= clean(
                            $announcement[
                                "title"
                            ]
                        ) ?>"
                        required
                    >

                </div>



                <!-- JENIS -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Jenis Pengumuman

                    </label>


                    <select
                        name="type"
                        class="form-select"
                        required
                    >


                        <option
                            value="Informasi"
                            <?= (
                                $announcement[
                                    "type"
                                ]
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
                                $announcement[
                                    "type"
                                ]
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
                                $announcement[
                                    "type"
                                ]
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
                                $announcement[
                                    "type"
                                ]
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



                <!-- TANGGAL -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Tanggal Pengumuman

                    </label>


                    <input
                        type="date"
                        name="published_at"
                        class="form-control"
                        value="<?= clean(
                            $announcement[
                                "published_at"
                            ]
                        ) ?>"
                        required
                    >

                </div>



                <!-- ISI -->

                <div class="mb-4">

                    <label
                        class="form-label fw-semibold"
                    >

                        Isi Pengumuman

                    </label>


                    <textarea
                        name="content"
                        class="form-control"
                        rows="10"
                        required
                    ><?= clean(
                        $announcement[
                            "content"
                        ]
                    ) ?></textarea>

                </div>



                <!-- BUTTON -->

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i
                        class="bi bi-save me-1"
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