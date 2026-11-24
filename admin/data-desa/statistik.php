<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// AMBIL ID DATA TAHUNAN
// =========================================================

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


if ($id <= 0) {

    header("Location: index.php");

    exit;

}


// =========================================================
// AMBIL DATA TAHUN
// =========================================================

$stmt = mysqli_prepare(
    $conn,
    "
    SELECT
        id,
        year,
        population,
        is_baseline

    FROM village_yearly_data

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


$result = mysqli_stmt_get_result(
    $stmt
);


$year_data = mysqli_fetch_assoc(
    $result
);


mysqli_stmt_close($stmt);


if (!$year_data) {

    header("Location: index.php");

    exit;

}


$year = (int) $year_data["year"];


// =========================================================
// DEFINISI KATEGORI
// =========================================================

$religions = [

    "Islam",

    "Kristen",

    "Katolik",

    "Hindu",

    "Buddha",

    "Konghucu",

    "Kepercayaan"

];


$marital_statuses = [

    "Belum Kawin",

    "Kawin",

    "Cerai Hidup",

    "Cerai Mati"

];


$educations = [

    "Tidak / Belum Sekolah",

    "SD / Sederajat",

    "SMP / Sederajat",

    "SMA / Sederajat",

    "Diploma",

    "S1",

    "S2",

    "S3"

];


$occupations = [

    "Belum / Tidak Bekerja",

    "Pelajar / Mahasiswa",

    "Ibu Rumah Tangga",

    "PNS",

    "TNI",

    "POLRI",

    "Karyawan Swasta",

    "Wiraswasta",

    "Petani",

    "Buruh",

    "Pedagang",

    "Nelayan",

    "Guru / Dosen",

    "Tenaga Kesehatan",

    "Pensiunan",

    "Lainnya"

];


$age_groups = [

    "0 - 4 Tahun",

    "5 - 9 Tahun",

    "10 - 14 Tahun",

    "15 - 19 Tahun",

    "20 - 24 Tahun",

    "25 - 29 Tahun",

    "30 - 34 Tahun",

    "35 - 39 Tahun",

    "40 - 44 Tahun",

    "45 - 49 Tahun",

    "50 - 54 Tahun",

    "55 - 59 Tahun",

    "60 - 64 Tahun",

    "65 - 69 Tahun",

    "70 - 74 Tahun",

    "75 Tahun ke Atas"

];


$event_types = [

    "Kelahiran",

    "Kematian",

    "Penduduk Masuk",

    "Penduduk Keluar"

];


// =========================================================
// DEFAULT ERROR / SUCCESS
// =========================================================

$error = "";

$success = "";


// =========================================================
// PROSES SIMPAN
// =========================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {


    $posted_religions =
        $_POST["religion"] ?? [];


    $posted_marital =
        $_POST["marital"] ?? [];


    $posted_education =
        $_POST["education"] ?? [];


    $posted_occupation =
        $_POST["occupation"] ?? [];


    $posted_age =
        $_POST["age_group"] ?? [];


    $posted_events =
        $_POST["event"] ?? [];


    // =====================================================
    // MULAI TRANSAKSI
    // =====================================================

    mysqli_begin_transaction($conn);


    try {


        // =================================================
        // AGAMA
        // =================================================

        $delete_religion =
            mysqli_prepare(
                $conn,
                "
                DELETE FROM village_yearly_religions
                WHERE yearly_data_id = ?
                "
            );


        mysqli_stmt_bind_param(
            $delete_religion,
            "i",
            $id
        );


        mysqli_stmt_execute(
            $delete_religion
        );


        mysqli_stmt_close(
            $delete_religion
        );


        $insert_religion =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_religions
                (
                    yearly_data_id,
                    religion,
                    population
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
                "
            );


        foreach (
            $religions
            as $religion
        ) {


            $value =
                trim(
                    $posted_religions[
                        $religion
                    ] ?? ""
                );


            if (
                $value === ""
            ) {

                continue;

            }


            $value =
                (int) $value;


            if (
                $value < 0
            ) {

                throw new Exception(
                    "Jumlah agama tidak boleh negatif."
                );

            }


            mysqli_stmt_bind_param(
                $insert_religion,
                "isi",
                $id,
                $religion,
                $value
            );


            mysqli_stmt_execute(
                $insert_religion
            );

        }


        mysqli_stmt_close(
            $insert_religion
        );


        // =================================================
        // STATUS PERKAWINAN
        // =================================================

        $delete_marital =
            mysqli_prepare(
                $conn,
                "
                DELETE FROM village_yearly_marital
                WHERE yearly_data_id = ?
                "
            );


        mysqli_stmt_bind_param(
            $delete_marital,
            "i",
            $id
        );


        mysqli_stmt_execute(
            $delete_marital
        );


        mysqli_stmt_close(
            $delete_marital
        );


        $insert_marital =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_marital
                (
                    yearly_data_id,
                    marital_status,
                    population
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
                "
            );


        foreach (
            $marital_statuses
            as $marital
        ) {


            $value =
                trim(
                    $posted_marital[
                        $marital
                    ] ?? ""
                );


            if (
                $value === ""
            ) {

                continue;

            }


            $value =
                (int) $value;


            if (
                $value < 0
            ) {

                throw new Exception(
                    "Jumlah status perkawinan tidak boleh negatif."
                );

            }


            mysqli_stmt_bind_param(
                $insert_marital,
                "isi",
                $id,
                $marital,
                $value
            );


            mysqli_stmt_execute(
                $insert_marital
            );

        }


        mysqli_stmt_close(
            $insert_marital
        );


        // =================================================
        // PENDIDIKAN
        // =================================================

        $delete_education =
            mysqli_prepare(
                $conn,
                "
                DELETE FROM village_yearly_education
                WHERE yearly_data_id = ?
                "
            );


        mysqli_stmt_bind_param(
            $delete_education,
            "i",
            $id
        );


        mysqli_stmt_execute(
            $delete_education
        );


        mysqli_stmt_close(
            $delete_education
        );


        $insert_education =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_education
                (
                    yearly_data_id,
                    education,
                    population
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
                "
            );


        foreach (
            $educations
            as $education
        ) {


            $value =
                trim(
                    $posted_education[
                        $education
                    ] ?? ""
                );


            if (
                $value === ""
            ) {

                continue;

            }


            $value =
                (int) $value;


            if (
                $value < 0
            ) {

                throw new Exception(
                    "Jumlah pendidikan tidak boleh negatif."
                );

            }


            mysqli_stmt_bind_param(
                $insert_education,
                "isi",
                $id,
                $education,
                $value
            );


            mysqli_stmt_execute(
                $insert_education
            );

        }


        mysqli_stmt_close(
            $insert_education
        );


        // =================================================
        // PEKERJAAN
        // =================================================

        $delete_occupation =
            mysqli_prepare(
                $conn,
                "
                DELETE FROM village_yearly_occupations
                WHERE yearly_data_id = ?
                "
            );


        mysqli_stmt_bind_param(
            $delete_occupation,
            "i",
            $id
        );


        mysqli_stmt_execute(
            $delete_occupation
        );


        mysqli_stmt_close(
            $delete_occupation
        );


        $insert_occupation =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_occupations
                (
                    yearly_data_id,
                    occupation,
                    population
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
                "
            );


        foreach (
            $occupations
            as $occupation
        ) {


            $value =
                trim(
                    $posted_occupation[
                        $occupation
                    ] ?? ""
                );


            if (
                $value === ""
            ) {

                continue;

            }


            $value =
                (int) $value;


            if (
                $value < 0
            ) {

                throw new Exception(
                    "Jumlah pekerjaan tidak boleh negatif."
                );

            }


            mysqli_stmt_bind_param(
                $insert_occupation,
                "isi",
                $id,
                $occupation,
                $value
            );


            mysqli_stmt_execute(
                $insert_occupation
            );

        }


        mysqli_stmt_close(
            $insert_occupation
        );


        // =================================================
        // KELOMPOK UMUR
        // =================================================

        $delete_age =
            mysqli_prepare(
                $conn,
                "
                DELETE FROM village_yearly_age_groups
                WHERE yearly_data_id = ?
                "
            );


        mysqli_stmt_bind_param(
            $delete_age,
            "i",
            $id
        );


        mysqli_stmt_execute(
            $delete_age
        );


        mysqli_stmt_close(
            $delete_age
        );


        $insert_age =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_age_groups
                (
                    yearly_data_id,
                    age_group,
                    population
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
                "
            );


        foreach (
            $age_groups
            as $age_group
        ) {


            $value =
                trim(
                    $posted_age[
                        $age_group
                    ] ?? ""
                );


            if (
                $value === ""
            ) {

                continue;

            }


            $value =
                (int) $value;


            if (
                $value < 0
            ) {

                throw new Exception(
                    "Jumlah kelompok umur tidak boleh negatif."
                );

            }


            mysqli_stmt_bind_param(
                $insert_age,
                "isi",
                $id,
                $age_group,
                $value
            );


            mysqli_stmt_execute(
                $insert_age
            );

        }


        mysqli_stmt_close(
            $insert_age
        );


        // =================================================
        // PERISTIWA KEPENDUDUKAN
        // =================================================

        $delete_events =
            mysqli_prepare(
                $conn,
                "
                DELETE FROM village_yearly_events
                WHERE yearly_data_id = ?
                "
            );


        mysqli_stmt_bind_param(
            $delete_events,
            "i",
            $id
        );


        mysqli_stmt_execute(
            $delete_events
        );


        mysqli_stmt_close(
            $delete_events
        );


        $insert_events =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_events
                (
                    yearly_data_id,
                    event_type,
                    amount
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )
                "
            );


        foreach (
            $event_types
            as $event
        ) {


            $value =
                trim(
                    $posted_events[
                        $event
                    ] ?? ""
                );


            if (
                $value === ""
            ) {

                continue;

            }


            $value =
                (int) $value;


            if (
                $value < 0
            ) {

                throw new Exception(
                    "Jumlah peristiwa tidak boleh negatif."
                );

            }


            mysqli_stmt_bind_param(
                $insert_events,
                "isi",
                $id,
                $event,
                $value
            );


            mysqli_stmt_execute(
                $insert_events
            );

        }


        mysqli_stmt_close(
            $insert_events
        );


        // =================================================
        // RIWAYAT
        // =================================================

        $history =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO village_yearly_history
                (
                    yearly_data_id,
                    action_type,
                    description
                )
                VALUES
                (
                    ?,
                    'UPDATE',
                    ?
                )
                "
            );


        $history_description =
            "Memperbarui statistik detail "
            . "data desa tahun "
            . $year
            . ".";


        mysqli_stmt_bind_param(
            $history,
            "is",
            $id,
            $history_description
        );


        mysqli_stmt_execute(
            $history
        );


        mysqli_stmt_close(
            $history
        );


        // =================================================
        // COMMIT
        // =================================================

        mysqli_commit($conn);


        $success =
            "Statistik tahun "
            . $year
            . " berhasil diperbarui.";


    } catch (
        Exception $e
    ) {


        mysqli_rollback($conn);


        $error =
            $e->getMessage();

    }

}


// =========================================================
// AMBIL DATA YANG SUDAH TERSIMPAN
// =========================================================

$religion_data = [];

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            religion,
            population

        FROM village_yearly_religions

        WHERE yearly_data_id = $id
        "
    );


while (
    $row =
    mysqli_fetch_assoc($result)
) {

    $religion_data[
        $row["religion"]
    ] =
        $row["population"];

}


// =========================================================
// PERKAWINAN
// =========================================================

$marital_data = [];

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            marital_status,
            population

        FROM village_yearly_marital

        WHERE yearly_data_id = $id
        "
    );


while (
    $row =
    mysqli_fetch_assoc($result)
) {

    $marital_data[
        $row["marital_status"]
    ] =
        $row["population"];

}


// =========================================================
// PENDIDIKAN
// =========================================================

$education_data = [];

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            education,
            population

        FROM village_yearly_education

        WHERE yearly_data_id = $id
        "
    );


while (
    $row =
    mysqli_fetch_assoc($result)
) {

    $education_data[
        $row["education"]
    ] =
        $row["population"];

}


// =========================================================
// PEKERJAAN
// =========================================================

$occupation_data = [];

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            occupation,
            population

        FROM village_yearly_occupations

        WHERE yearly_data_id = $id
        "
    );


while (
    $row =
    mysqli_fetch_assoc($result)
) {

    $occupation_data[
        $row["occupation"]
    ] =
        $row["population"];

}


// =========================================================
// KELOMPOK UMUR
// =========================================================

$age_data = [];

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            age_group,
            population

        FROM village_yearly_age_groups

        WHERE yearly_data_id = $id
        "
    );


while (
    $row =
    mysqli_fetch_assoc($result)
) {

    $age_data[
        $row["age_group"]
    ] =
        $row["population"];

}


// =========================================================
// EVENTS
// =========================================================

$event_data = [];

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            event_type,
            amount

        FROM village_yearly_events

        WHERE yearly_data_id = $id
        "
    );


while (
    $row =
    mysqli_fetch_assoc($result)
) {

    $event_data[
        $row["event_type"]
    ] =
        $row["amount"];

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

        Statistik
        <?= $year ?>
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


    <style>

        body {

            background: #f5f7fa;

        }


        .card {

            border: 0;

            border-radius: 16px;

        }


        .section-card {

            margin-bottom: 24px;

        }


        .section-header {

            padding: 20px 24px;

            border-bottom: 1px solid #eeeeee;

        }


        .section-body {

            padding: 24px;

        }


        .section-icon {

            width: 44px;

            height: 44px;

            border-radius: 12px;

            background: #eef4ff;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .form-label {

            font-weight: 600;

        }


        .category-row {

            padding: 12px 0;

            border-bottom: 1px solid #f0f0f0;

        }


        .category-row:last-child {

            border-bottom: 0;

        }


        .sticky-save {

            position: sticky;

            bottom: 20px;

            z-index: 10;

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-dark bg-primary">

    <div class="container-fluid px-4">


        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>


        <div>


            <a
                href="index.php"
                class="btn btn-light btn-sm me-2"
            >

                <i
                    class="bi bi-bar-chart-line me-1"
                ></i>

                Rekap Tahunan

            </a>


            <a
                href="<?= BASE_URL ?>admin/logout.php"
                class="btn btn-outline-light btn-sm"
                onclick="return confirm('Yakin ingin logout?');"
            >

                <i
                    class="bi bi-box-arrow-right me-1"
                ></i>

                Logout

            </a>


        </div>

    </div>

</nav>



<!-- =====================================================
     CONTENT
===================================================== -->

<div class="container-fluid px-4 py-4">


    <!-- =================================================
         HEADER
    ================================================= -->

    <div
        class="card
               shadow-sm
               mb-4"
    >

        <div class="card-body p-4">


            <div
                class="d-flex
                       flex-wrap
                       justify-content-between
                       align-items-center
                       gap-3"
            >


                <div>


                    <div
                        class="d-flex
                               align-items-center
                               gap-2
                               mb-2"
                    >

                        <i
                            class="bi bi-pie-chart-fill
                                   text-primary
                                   fs-4"
                        ></i>


                        <h2
                            class="fw-bold
                                   mb-0"
                        >

                            Statistik Detail
                            Tahun <?= $year ?>

                        </h2>


                        <?php if (
                            (int)
                            $year_data[
                                "is_baseline"
                            ] === 1
                        ): ?>

                            <span
                                class="badge
                                       bg-success"
                            >

                                Baseline

                            </span>

                        <?php endif; ?>


                    </div>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Kelola data statistik Desa Cisalada
                        berdasarkan tahun <?= $year ?>.

                    </p>


                </div>


                <a
                    href="detail.php?id=<?= $id ?>"
                    class="btn btn-secondary"
                >

                    <i
                        class="bi bi-arrow-left me-1"
                    ></i>

                    Kembali ke Detail

                </a>


            </div>


        </div>

    </div>



    <!-- =================================================
         ALERT
    ================================================= -->

    <?php if (
        !empty($success)
    ): ?>


        <div
            class="alert
                   alert-success
                   border-0
                   shadow-sm"
        >

            <i
                class="bi bi-check-circle-fill me-2"
            ></i>

            <?= clean($success) ?>

        </div>


    <?php endif; ?>


    <?php if (
        !empty($error)
    ): ?>


        <div
            class="alert
                   alert-danger
                   border-0
                   shadow-sm"
        >

            <i
                class="bi bi-exclamation-circle-fill me-2"
            ></i>

            <?= clean($error) ?>

        </div>


    <?php endif; ?>



    <!-- =================================================
         INFORMASI
    ================================================= -->

    <div
        class="alert
               alert-info
               border-0
               shadow-sm
               mb-4"
    >

        <i
            class="bi bi-info-circle-fill me-2"
        ></i>

        Masukkan data yang benar-benar tersedia
        dari sumber resmi desa. Jika suatu kategori
        belum diketahui, biarkan kosong. Sistem tidak
        akan menganggap data kosong sebagai angka 0.

    </div>



    <form
        method="POST"
    >


        <!-- =================================================
             AGAMA
        ================================================= -->

        <div
            class="card
                   section-card
                   shadow-sm"
        >


            <div class="section-header">


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="section-icon">


                        <i
                            class="bi bi-moon-stars-fill
                                   text-primary"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Agama

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Jumlah penduduk berdasarkan
                            agama.

                        </small>


                    </div>


                </div>


            </div>


            <div class="section-body">


                <div class="row g-3">


                    <?php foreach (
                        $religions
                        as $religion
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-4"
                        >


                            <label
                                class="form-label"
                            >

                                <?= clean(
                                    $religion
                                ) ?>

                            </label>


                            <input
                                type="number"
                                name="religion[<?= clean($religion) ?>]"
                                class="form-control"
                                min="0"
                                value="<?= isset(
                                    $religion_data[
                                        $religion
                                    ]
                                )
                                    ? (int)
                                        $religion_data[
                                            $religion
                                        ]
                                    : "" ?>"
                                placeholder="Belum tersedia"
                            >


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        </div>



        <!-- =================================================
             PERKAWINAN
        ================================================= -->

        <div
            class="card
                   section-card
                   shadow-sm"
        >


            <div class="section-header">


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="section-icon">


                        <i
                            class="bi bi-heart-fill
                                   text-primary"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Status Perkawinan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Jumlah penduduk berdasarkan
                            status perkawinan.

                        </small>


                    </div>


                </div>


            </div>


            <div class="section-body">


                <div class="row g-3">


                    <?php foreach (
                        $marital_statuses
                        as $marital
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-3"
                        >


                            <label
                                class="form-label"
                            >

                                <?= clean(
                                    $marital
                                ) ?>

                            </label>


                            <input
                                type="number"
                                name="marital[<?= clean($marital) ?>]"
                                class="form-control"
                                min="0"
                                value="<?= isset(
                                    $marital_data[
                                        $marital
                                    ]
                                )
                                    ? (int)
                                        $marital_data[
                                            $marital
                                        ]
                                    : "" ?>"
                                placeholder="Belum tersedia"
                            >


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        </div>



        <!-- =================================================
             PENDIDIKAN
        ================================================= -->

        <div
            class="card
                   section-card
                   shadow-sm"
        >


            <div class="section-header">


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="section-icon">


                        <i
                            class="bi bi-mortarboard-fill
                                   text-primary"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Pendidikan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Jumlah penduduk berdasarkan
                            pendidikan terakhir.

                        </small>


                    </div>


                </div>


            </div>


            <div class="section-body">


                <div class="row g-3">


                    <?php foreach (
                        $educations
                        as $education
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-4"
                        >


                            <label
                                class="form-label"
                            >

                                <?= clean(
                                    $education
                                ) ?>

                            </label>


                            <input
                                type="number"
                                name="education[<?= clean($education) ?>]"
                                class="form-control"
                                min="0"
                                value="<?= isset(
                                    $education_data[
                                        $education
                                    ]
                                )
                                    ? (int)
                                        $education_data[
                                            $education
                                        ]
                                    : "" ?>"
                                placeholder="Belum tersedia"
                            >


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        </div>



        <!-- =================================================
             PEKERJAAN
        ================================================= -->

        <div
            class="card
                   section-card
                   shadow-sm"
        >


            <div class="section-header">


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="section-icon">


                        <i
                            class="bi bi-briefcase-fill
                                   text-primary"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Pekerjaan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Jumlah penduduk berdasarkan
                            pekerjaan.

                        </small>


                    </div>


                </div>


            </div>


            <div class="section-body">


                <div class="row g-3">


                    <?php foreach (
                        $occupations
                        as $occupation
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-4"
                        >


                            <label
                                class="form-label"
                            >

                                <?= clean(
                                    $occupation
                                ) ?>

                            </label>


                            <input
                                type="number"
                                name="occupation[<?= clean($occupation) ?>]"
                                class="form-control"
                                min="0"
                                value="<?= isset(
                                    $occupation_data[
                                        $occupation
                                    ]
                                )
                                    ? (int)
                                        $occupation_data[
                                            $occupation
                                        ]
                                    : "" ?>"
                                placeholder="Belum tersedia"
                            >


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        </div>



        <!-- =================================================
             KELOMPOK UMUR
        ================================================= -->

        <div
            class="card
                   section-card
                   shadow-sm"
        >


            <div class="section-header">


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="section-icon">


                        <i
                            class="bi bi-people-fill
                                   text-primary"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Kelompok Umur

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Jumlah penduduk berdasarkan
                            kelompok umur.

                        </small>


                    </div>


                </div>


            </div>


            <div class="section-body">


                <div class="row g-3">


                    <?php foreach (
                        $age_groups
                        as $age_group
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-4"
                        >


                            <label
                                class="form-label"
                            >

                                <?= clean(
                                    $age_group
                                ) ?>

                            </label>


                            <input
                                type="number"
                                name="age_group[<?= clean($age_group) ?>]"
                                class="form-control"
                                min="0"
                                value="<?= isset(
                                    $age_data[
                                        $age_group
                                    ]
                                )
                                    ? (int)
                                        $age_data[
                                            $age_group
                                        ]
                                    : "" ?>"
                                placeholder="Belum tersedia"
                            >


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        </div>



        <!-- =================================================
             PERISTIWA KEPENDUDUKAN
        ================================================= -->

        <div
            class="card
                   section-card
                   shadow-sm"
        >


            <div class="section-header">


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="section-icon">


                        <i
                            class="bi bi-arrow-left-right
                                   text-primary"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Peristiwa Kependudukan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Catat jumlah peristiwa
                            kependudukan pada tahun ini.

                        </small>


                    </div>


                </div>


            </div>


            <div class="section-body">


                <div class="row g-3">


                    <?php foreach (
                        $event_types
                        as $event
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-3"
                        >


                            <label
                                class="form-label"
                            >

                                <?= clean(
                                    $event
                                ) ?>

                            </label>


                            <input
                                type="number"
                                name="event[<?= clean($event) ?>]"
                                class="form-control"
                                min="0"
                                value="<?= isset(
                                    $event_data[
                                        $event
                                    ]
                                )
                                    ? (int)
                                        $event_data[
                                            $event
                                        ]
                                    : "" ?>"
                                placeholder="Belum tersedia"
                            >


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        </div>



        <!-- =================================================
             SAVE
        ================================================= -->

        <div
            class="sticky-save"
        >


            <div
                class="card
                       shadow-lg"
            >

                <div
                    class="card-body
                           p-3"
                >


                    <div
                        class="d-flex
                               flex-wrap
                               justify-content-between
                               align-items-center
                               gap-3"
                    >


                        <div
                            class="text-muted
                                   small"
                        >

                            <i
                                class="bi bi-shield-check me-1"
                            ></i>

                            Data akan disimpan untuk
                            tahun <?= $year ?>.

                        </div>


                        <div>


                            <a
                                href="detail.php?id=<?= $id ?>"
                                class="btn btn-secondary me-2"
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

                                Simpan Semua Statistik

                            </button>


                        </div>


                    </div>


                </div>

            </div>


        </div>


    </form>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>