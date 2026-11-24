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
        FROM agendas
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


$agenda =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (!$agenda) {

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


    $event_date =
        $_POST["event_date"] ?? "";


    $event_time =
        $_POST["event_time"] ?? "";


    $location =
        trim(
            $_POST["location"] ?? ""
        );


    $description =
        trim(
            $_POST["description"] ?? ""
        );


    $status =
        $_POST["status"]
        ?? "Akan Datang";


    // ======================================
    // VALIDASI
    // ======================================

    if (empty($title)) {

        $error =
            "Nama agenda wajib diisi.";

    } elseif (
        empty($event_date)
    ) {

        $error =
            "Tanggal agenda wajib diisi.";

    } elseif (
        empty($event_time)
    ) {

        $error =
            "Waktu agenda wajib diisi.";

    } elseif (
        empty($location)
    ) {

        $error =
            "Lokasi agenda wajib diisi.";

    } elseif (
        empty($description)
    ) {

        $error =
            "Deskripsi agenda wajib diisi.";

    } elseif (
        !in_array(
            $status,
            [
                "Akan Datang",
                "Sedang Berlangsung",
                "Selesai",
                "Dibatalkan"
            ]
        )
    ) {

        $error =
            "Status agenda tidak valid.";

    } else {


        // ==================================
        // UPDATE
        // ==================================

        $sql = "
            UPDATE agendas

            SET
                title = ?,
                event_date = ?,
                event_time = ?,
                location = ?,
                description = ?,
                status = ?

            WHERE id = ?
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
                "ssssssi",
                $title,
                $event_date,
                $event_time,
                $location,
                $description,
                $status,
                $id
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {


                mysqli_stmt_close(
                    $stmt
                );


                header(
                    "Location: index.php?success=edit"
                );

                exit;


            } else {


                $error =
                    "Gagal memperbarui agenda: "
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
        Edit Agenda - Cisalada GO
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


<!-- NAVBAR -->

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

</nav>



<!-- CONTENT -->

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

                Edit Agenda

            </h2>


            <p class="text-muted mb-0">

                Perbarui informasi agenda desa.

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



    <!-- FORM -->

    <div
        class="card
               border-0
               shadow-sm"
    >

        <div class="card-body p-4">


            <form method="POST">


                <!-- NAMA -->

                <div class="mb-3">

                    <label
                        for="title"
                        class="form-label fw-semibold"
                    >

                        Nama Agenda

                    </label>


                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        value="<?= clean(
                            $agenda["title"]
                        ) ?>"
                        required
                    >

                </div>



                <!-- TANGGAL + WAKTU -->

                <div class="row">


                    <div class="col-md-6 mb-3">

                        <label
                            for="event_date"
                            class="form-label fw-semibold"
                        >

                            Tanggal

                        </label>


                        <input
                            type="date"
                            id="event_date"
                            name="event_date"
                            class="form-control"
                            value="<?= clean(
                                $agenda[
                                    "event_date"
                                ]
                            ) ?>"
                            required
                        >

                    </div>



                    <div class="col-md-6 mb-3">

                        <label
                            for="event_time"
                            class="form-label fw-semibold"
                        >

                            Waktu

                        </label>


                        <input
                            type="time"
                            id="event_time"
                            name="event_time"
                            class="form-control"
                            value="<?= clean(
                                $agenda[
                                    "event_time"
                                ]
                            ) ?>"
                            required
                        >

                    </div>


                </div>



                <!-- LOKASI -->

                <div class="mb-3">

                    <label
                        for="location"
                        class="form-label fw-semibold"
                    >

                        Lokasi

                    </label>


                    <input
                        type="text"
                        id="location"
                        name="location"
                        class="form-control"
                        value="<?= clean(
                            $agenda[
                                "location"
                            ]
                        ) ?>"
                        required
                    >

                </div>



                <!-- STATUS -->

                <div class="mb-3">

                    <label
                        for="status"
                        class="form-label fw-semibold"
                    >

                        Status

                    </label>


                    <select
                        id="status"
                        name="status"
                        class="form-select"
                        required
                    >


                        <option
                            value="Akan Datang"
                            <?= (
                                $agenda[
                                    "status"
                                ]
                                ===
                                "Akan Datang"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Akan Datang

                        </option>


                        <option
                            value="Sedang Berlangsung"
                            <?= (
                                $agenda[
                                    "status"
                                ]
                                ===
                                "Sedang Berlangsung"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Sedang Berlangsung

                        </option>


                        <option
                            value="Selesai"
                            <?= (
                                $agenda[
                                    "status"
                                ]
                                ===
                                "Selesai"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Selesai

                        </option>


                        <option
                            value="Dibatalkan"
                            <?= (
                                $agenda[
                                    "status"
                                ]
                                ===
                                "Dibatalkan"
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            Dibatalkan

                        </option>


                    </select>

                </div>



                <!-- DESKRIPSI -->

                <div class="mb-4">

                    <label
                        for="description"
                        class="form-label fw-semibold"
                    >

                        Deskripsi

                    </label>


                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="8"
                        required
                    ><?= clean(
                        $agenda[
                            "description"
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