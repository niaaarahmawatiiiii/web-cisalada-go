<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// AMBIL ID
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
        male_population,
        female_population,
        family_count,
        hamlet_count,
        rw_count,
        rt_count,
        area,
        population_density,
        source,
        notes,
        is_baseline,
        created_at,
        updated_at

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


$data = mysqli_fetch_assoc(
    $result
);


mysqli_stmt_close($stmt);


if (!$data) {

    header("Location: index.php");

    exit;

}


// =========================================================
// AMBIL DATA SEBELUMNYA
// =========================================================

$previous_data = null;


$stmt_previous = mysqli_prepare(
    $conn,
    "
    SELECT
        year,
        population

    FROM village_yearly_data

    WHERE year < ?

    ORDER BY year DESC

    LIMIT 1
    "
);


mysqli_stmt_bind_param(
    $stmt_previous,
    "i",
    $data["year"]
);


mysqli_stmt_execute(
    $stmt_previous
);


$result_previous =
    mysqli_stmt_get_result(
        $stmt_previous
    );


$previous_data =
    mysqli_fetch_assoc(
        $result_previous
    );


mysqli_stmt_close(
    $stmt_previous
);


// =========================================================
// PERTUMBUHAN
// =========================================================

$growth = null;

$growth_percent = null;


if (
    $previous_data
    &&
    $previous_data["population"] !== null
    &&
    $data["population"] !== null
) {

    $growth =
        (int) $data["population"]
        -
        (int) $previous_data["population"];


    if (
        (int) $previous_data["population"] > 0
    ) {

        $growth_percent =
            (
                $growth
                /
                (int) $previous_data["population"]
            )
            * 100;

    }

}


// =========================================================
// CEK JUMLAH STATISTIK DETAIL
// =========================================================

$stat_count = 0;


$stat_tables = [

    "village_yearly_religions",

    "village_yearly_marital",

    "village_yearly_education",

    "village_yearly_occupations",

    "village_yearly_age_groups",

    "village_yearly_events"

];


foreach (
    $stat_tables
    as $table
) {

    $query =
        mysqli_query(
            $conn,
            "
            SELECT COUNT(*) AS total
            FROM $table
            WHERE yearly_data_id = $id
            "
        );


    if ($query) {

        $row =
            mysqli_fetch_assoc(
                $query
            );


        $stat_count +=
            (int) $row["total"];

    }

}


// =========================================================
// FORMAT ANGKA
// =========================================================

function formatNumber($value)
{

    if (
        $value === null
        ||
        $value === ""
    ) {

        return "Belum tersedia";

    }


    return number_format(
        (int) $value,
        0,
        ",",
        "."
    );

}


// =========================================================
// FORMAT DECIMAL
// =========================================================

function formatDecimal($value)
{

    if (
        $value === null
        ||
        $value === ""
    ) {

        return "Belum tersedia";

    }


    return number_format(
        (float) $value,
        2,
        ",",
        "."
    );

}


// =========================================================
// FORMAT TANGGAL
// =========================================================

function formatDateIndonesia($date)
{

    if (
        empty($date)
    ) {

        return "Belum tersedia";

    }


    return date(
        "d-m-Y H:i",
        strtotime($date)
    );

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

        Detail Data Desa
        <?= (int) $data["year"] ?>
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


        .page-header {

            background: white;

            border-radius: 16px;

            padding: 24px;

            margin-bottom: 24px;

        }


        .data-card {

            transition: .2s;

        }


        .data-card:hover {

            transform: translateY(-2px);

        }


        .data-icon {

            width: 48px;

            height: 48px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eef4ff;

        }


        .data-value {

            font-size: 25px;

            font-weight: 700;

        }


        .data-label {

            color: #6c757d;

            font-size: 14px;

        }


        .growth-box {

            border-radius: 14px;

            padding: 20px;

        }


        .growth-positive {

            color: #198754;

        }


        .growth-negative {

            color: #dc3545;

        }


        .growth-zero {

            color: #6c757d;

        }


        .info-label {

            color: #6c757d;

            font-size: 13px;

            margin-bottom: 4px;

        }


        .info-value {

            font-weight: 600;

        }


        .statistik-card {

            transition: .2s;

        }


        .statistik-card:hover {

            transform: translateY(-2px);

        }


        .statistik-icon {

            width: 52px;

            height: 52px;

            border-radius: 14px;

            background: #eef4ff;

            display: flex;

            align-items: center;

            justify-content: center;

        }

    </style>

</head>


<body>


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
                href="<?= BASE_URL ?>admin/index.php"
                class="btn btn-light btn-sm me-2"
            >

                <i
                    class="bi bi-speedometer2 me-1"
                ></i>

                Dashboard

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



<div class="container-fluid px-4 py-4">


    <!-- =================================================
         HEADER
    ================================================= -->

    <div class="page-header">


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
                        class="bi bi-calendar3
                               text-primary
                               fs-4"
                    ></i>


                    <h2
                        class="fw-bold
                               mb-0"
                    >

                        Data Desa Tahun
                        <?= (int) $data["year"] ?>

                    </h2>


                    <?php if (
                        (int)
                        $data["is_baseline"]
                        === 1
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

                    Detail rekap data Desa Cisalada
                    tahun <?= (int) $data["year"] ?>.

                </p>


            </div>


            <div>


                <a
                    href="statistik.php?id=<?= (int) $data["id"] ?>"
                    class="btn btn-primary me-2"
                >

                    <i
                        class="bi bi-pie-chart-fill me-1"
                    ></i>

                    Statistik Detail

                    <?php if (
                        $stat_count > 0
                    ): ?>

                        <span
                            class="badge
                                   bg-white
                                   text-primary
                                   ms-1"
                        >

                            <?= $stat_count ?>

                        </span>

                    <?php endif; ?>


                </a>


                <a
                    href="edit.php?id=<?= (int) $data["id"] ?>"
                    class="btn btn-warning me-2"
                >

                    <i
                        class="bi bi-pencil me-1"
                    ></i>

                    Edit Data

                </a>


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


        </div>


    </div>



    <!-- =================================================
         PERTUMBUHAN
    ================================================= -->

    <?php if (
        $growth !== null
    ): ?>


        <div
            class="growth-box
                   bg-white
                   shadow-sm
                   mb-4"
        >


            <div
                class="d-flex
                       flex-wrap
                       justify-content-between
                       align-items-center
                       gap-3"
            >


                <div>


                    <h5
                        class="fw-bold
                               mb-1"
                    >

                        <i
                            class="bi bi-graph-up-arrow
                                   text-primary
                                   me-2"
                        ></i>

                        Pertumbuhan Penduduk

                    </h5>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Dibandingkan dengan tahun
                        <?= (int)
                            $previous_data["year"] ?>.

                    </p>


                </div>


                <div class="text-end">


                    <?php if (
                        $growth > 0
                    ): ?>


                        <div
                            class="growth-positive
                                   fs-3
                                   fw-bold"
                        >

                            +<?= formatNumber(
                                $growth
                            ) ?>

                            jiwa

                        </div>


                    <?php elseif (
                        $growth < 0
                    ): ?>


                        <div
                            class="growth-negative
                                   fs-3
                                   fw-bold"
                        >

                            <?= formatNumber(
                                $growth
                            ) ?>

                            jiwa

                        </div>


                    <?php else: ?>


                        <div
                            class="growth-zero
                                   fs-3
                                   fw-bold"
                        >

                            0 jiwa

                        </div>


                    <?php endif; ?>


                    <?php if (
                        $growth_percent !== null
                    ): ?>


                        <div class="text-muted">

                            <?= number_format(
                                $growth_percent,
                                2,
                                ",",
                                "."
                            ) ?>%

                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </div>


    <?php else: ?>


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

            <?php if (
                (int)
                $data["is_baseline"]
                === 1
            ): ?>

                Tahun ini merupakan
                <strong>baseline</strong>.
                Pertumbuhan akan muncul setelah
                tersedia data tahun berikutnya.

            <?php else: ?>

                Belum tersedia data penduduk
                tahun sebelumnya untuk menghitung
                pertumbuhan.

            <?php endif; ?>


        </div>


    <?php endif; ?>



    <!-- =================================================
         KEPENDUDUKAN
    ================================================= -->

    <h5
        class="fw-bold
               mb-3"
    >

        Data Kependudukan

    </h5>


    <div class="row g-4 mb-4">


        <div class="col-md-6 col-xl-3">


            <div
                class="card
                       data-card
                       shadow-sm
                       h-100"
            >

                <div class="card-body p-4">


                    <div
                        class="d-flex
                               justify-content-between"
                    >


                        <div>

                            <div class="data-label">

                                Penduduk

                            </div>


                            <div class="data-value">

                                <?= formatNumber(
                                    $data["population"]
                                ) ?>

                            </div>

                        </div>


                        <div class="data-icon">


                            <i
                                class="bi bi-people-fill
                                       text-primary
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>



        <div class="col-md-6 col-xl-3">


            <div
                class="card
                       data-card
                       shadow-sm
                       h-100"
            >

                <div class="card-body p-4">


                    <div
                        class="d-flex
                               justify-content-between"
                    >


                        <div>

                            <div class="data-label">

                                Laki-laki

                            </div>


                            <div class="data-value">

                                <?= formatNumber(
                                    $data[
                                        "male_population"
                                    ]
                                ) ?>

                            </div>

                        </div>


                        <div class="data-icon">


                            <i
                                class="bi bi-gender-male
                                       text-primary
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>



        <div class="col-md-6 col-xl-3">


            <div
                class="card
                       data-card
                       shadow-sm
                       h-100"
            >

                <div class="card-body p-4">


                    <div
                        class="d-flex
                               justify-content-between"
                    >


                        <div>

                            <div class="data-label">

                                Perempuan

                            </div>


                            <div class="data-value">

                                <?= formatNumber(
                                    $data[
                                        "female_population"
                                    ]
                                ) ?>

                            </div>

                        </div>


                        <div class="data-icon">


                            <i
                                class="bi bi-gender-female
                                       text-danger
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>



        <div class="col-md-6 col-xl-3">


            <div
                class="card
                       data-card
                       shadow-sm
                       h-100"
            >

                <div class="card-body p-4">


                    <div
                        class="d-flex
                               justify-content-between"
                    >


                        <div>

                            <div class="data-label">

                                Kepala Keluarga

                            </div>


                            <div class="data-value">

                                <?= formatNumber(
                                    $data[
                                        "family_count"
                                    ]
                                ) ?>

                            </div>

                        </div>


                        <div class="data-icon">


                            <i
                                class="bi bi-house-fill
                                       text-primary
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>


    </div>



    <!-- =================================================
         STATISTIK DETAIL CARD
    ================================================= -->

    <div
        class="card
               statistik-card
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


                <div
                    class="d-flex
                           align-items-center
                           gap-3"
                >


                    <div class="statistik-icon">


                        <i
                            class="bi bi-pie-chart-fill
                                   text-primary
                                   fs-4"
                        ></i>


                    </div>


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Statistik Detail

                        </h5>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Agama, pendidikan, pekerjaan,
                            perkawinan, kelompok umur,
                            dan peristiwa kependudukan.

                        </p>


                    </div>


                </div>


                <a
                    href="statistik.php?id=<?= (int) $data["id"] ?>"
                    class="btn btn-primary"
                >

                    <i
                        class="bi bi-pencil-square me-1"
                    ></i>

                    Kelola Statistik

                </a>


            </div>


        </div>

    </div>



    <!-- =================================================
         WILAYAH
    ================================================= -->

    <h5
        class="fw-bold
               mb-3"
    >

        Data Wilayah

    </h5>


    <div class="row g-4 mb-4">


        <div class="col-md-6 col-xl-3">


            <div class="card shadow-sm h-100">

                <div class="card-body p-4">


                    <div class="info-label">

                        Dusun

                    </div>


                    <div class="fs-4 fw-bold">


                        <?php if (
                            $data["hamlet_count"]
                            !== null
                        ): ?>

                            <?= formatNumber(
                                $data["hamlet_count"]
                            ) ?>

                        <?php else: ?>

                            <span
                                class="text-muted fs-6"
                            >

                                Belum tersedia

                            </span>

                        <?php endif; ?>


                    </div>


                </div>

            </div>


        </div>



        <div class="col-md-6 col-xl-3">


            <div class="card shadow-sm h-100">

                <div class="card-body p-4">


                    <div class="info-label">

                        RW

                    </div>


                    <div class="fs-4 fw-bold">


                        <?php if (
                            $data["rw_count"]
                            !== null
                        ): ?>

                            <?= formatNumber(
                                $data["rw_count"]
                            ) ?>

                        <?php else: ?>

                            <span
                                class="text-muted fs-6"
                            >

                                Belum tersedia

                            </span>

                        <?php endif; ?>


                    </div>


                </div>

            </div>


        </div>



        <div class="col-md-6 col-xl-3">


            <div class="card shadow-sm h-100">

                <div class="card-body p-4">


                    <div class="info-label">

                        RT

                    </div>


                    <div class="fs-4 fw-bold">


                        <?php if (
                            $data["rt_count"]
                            !== null
                        ): ?>

                            <?= formatNumber(
                                $data["rt_count"]
                            ) ?>

                        <?php else: ?>

                            <span
                                class="text-muted fs-6"
                            >

                                Belum tersedia

                            </span>

                        <?php endif; ?>


                    </div>


                </div>

            </div>


        </div>



        <div class="col-md-6 col-xl-3">


            <div class="card shadow-sm h-100">

                <div class="card-body p-4">


                    <div class="info-label">

                        Luas Wilayah

                    </div>


                    <div class="fs-4 fw-bold">


                        <?php if (
                            $data["area"]
                            !== null
                        ): ?>

                            <?= formatDecimal(
                                $data["area"]
                            ) ?>

                            <small
                                class="fs-6
                                       fw-normal"
                            >

                                Ha

                            </small>

                        <?php else: ?>

                            <span
                                class="text-muted fs-6"
                            >

                                Belum tersedia

                            </span>

                        <?php endif; ?>


                    </div>


                </div>

            </div>


        </div>


    </div>



    <!-- =================================================
         INFORMASI
    ================================================= -->

    <div
        class="card
               shadow-sm
               mb-4"
    >

        <div class="card-body p-4">


            <div class="row g-4">


                <div class="col-md-4">


                    <div class="info-label">

                        Kepadatan Penduduk

                    </div>


                    <div class="info-value">


                        <?php if (
                            $data[
                                "population_density"
                            ]
                            !== null
                        ): ?>

                            <?= formatDecimal(
                                $data[
                                    "population_density"
                                ]
                            ) ?>

                            jiwa/Ha

                        <?php else: ?>

                            Belum tersedia

                        <?php endif; ?>


                    </div>


                </div>


                <div class="col-md-4">


                    <div class="info-label">

                        Sumber Data

                    </div>


                    <div class="info-value">

                        <?= !empty(
                            $data["source"]
                        )
                            ? clean(
                                $data["source"]
                            )
                            : "Belum tersedia" ?>

                    </div>


                </div>


                <div class="col-md-4">


                    <div class="info-label">

                        Terakhir Diperbarui

                    </div>


                    <div class="info-value">

                        <?= formatDateIndonesia(
                            $data["updated_at"]
                        ) ?>

                    </div>


                </div>


            </div>


        </div>

    </div>



    <!-- =================================================
         CATATAN
    ================================================= -->

    <div
        class="card
               shadow-sm
               mb-4"
    >

        <div class="card-body p-4">


            <h5
                class="fw-bold
                       mb-3"
            >

                <i
                    class="bi bi-journal-text
                           text-primary
                           me-2"
                ></i>

                Catatan

            </h5>


            <?php if (
                !empty(
                    $data["notes"]
                )
            ): ?>

                <p
                    class="text-muted
                           mb-0"
                >

                    <?= nl2br(
                        clean(
                            $data["notes"]
                        )
                    ) ?>

                </p>

            <?php else: ?>

                <p
                    class="text-muted
                           mb-0"
                >

                    Tidak ada catatan.

                </p>

            <?php endif; ?>


        </div>

    </div>



    <div
        class="d-flex
               justify-content-between"
    >


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            <i
                class="bi bi-arrow-left me-1"
            ></i>

            Kembali ke Rekap

        </a>


        <div>


            <a
                href="statistik.php?id=<?= (int) $data["id"] ?>"
                class="btn btn-primary me-2"
            >

                <i
                    class="bi bi-pie-chart-fill me-1"
                ></i>

                Statistik Detail

            </a>


            <a
                href="edit.php?id=<?= (int) $data["id"] ?>"
                class="btn btn-warning"
            >

                <i
                    class="bi bi-pencil me-1"
                ></i>

                Edit Data

            </a>


        </div>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>