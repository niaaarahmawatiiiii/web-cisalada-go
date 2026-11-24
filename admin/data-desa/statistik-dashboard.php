<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// AMBIL SEMUA DATA TAHUNAN
// =========================================================

$yearly_data = [];

$query_yearly = mysqli_query(
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

    ORDER BY year ASC
    "
);


while (
    $row = mysqli_fetch_assoc($query_yearly)
) {

    $yearly_data[] = $row;

}


// =========================================================
// DATA TERBARU
// =========================================================

$latest = null;

if (
    count($yearly_data) > 0
) {

    $latest =
        $yearly_data[
            count($yearly_data) - 1
        ];

}


// =========================================================
// DATA SEBELUMNYA
// =========================================================

$previous = null;

if (
    count($yearly_data) > 1
) {

    $previous =
        $yearly_data[
            count($yearly_data) - 2
        ];

}


// =========================================================
// PERTUMBUHAN
// =========================================================

$growth = null;

$growth_percent = null;


if (
    $latest !== null
    &&
    $previous !== null
    &&
    $latest["population"] !== null
    &&
    $previous["population"] !== null
) {

    $growth =
        (int) $latest["population"]
        -
        (int) $previous["population"];


    if (
        (int) $previous["population"] > 0
    ) {

        $growth_percent =
            (
                $growth
                /
                (int) $previous["population"]
            )
            * 100;

    }

}


// =========================================================
// ARRAY UNTUK GRAFIK
// =========================================================

$years = [];

$populations = [];

$male_populations = [];

$female_populations = [];

$family_counts = [];


foreach (
    $yearly_data
    as $row
) {

    $years[] =
        (int) $row["year"];


    $populations[] =
        $row["population"] !== null
            ? (int) $row["population"]
            : null;


    $male_populations[] =
        $row["male_population"] !== null
            ? (int) $row["male_population"]
            : null;


    $female_populations[] =
        $row["female_population"] !== null
            ? (int) $row["female_population"]
            : null;


    $family_counts[] =
        $row["family_count"] !== null
            ? (int) $row["family_count"]
            : null;

}


// =========================================================
// STATISTIK AGAMA TAHUN TERBARU
// =========================================================

$religion_labels = [];

$religion_values = [];


if (
    $latest !== null
) {

    $latest_id =
        (int) $latest["id"];


    $query =
        mysqli_query(
            $conn,
            "
            SELECT
                religion,
                population

            FROM village_yearly_religions

            WHERE yearly_data_id = $latest_id

            ORDER BY population DESC
            "
        );


    while (
        $row = mysqli_fetch_assoc($query)
    ) {

        $religion_labels[] =
            $row["religion"];


        $religion_values[] =
            (int) $row["population"];

    }

}


// =========================================================
// STATISTIK PENDIDIKAN
// =========================================================

$education_labels = [];

$education_values = [];


if (
    $latest !== null
) {

    $query =
        mysqli_query(
            $conn,
            "
            SELECT
                education,
                population

            FROM village_yearly_education

            WHERE yearly_data_id = $latest_id

            ORDER BY population DESC
            "
        );


    while (
        $row = mysqli_fetch_assoc($query)
    ) {

        $education_labels[] =
            $row["education"];


        $education_values[] =
            (int) $row["population"];

    }

}


// =========================================================
// STATISTIK PEKERJAAN
// =========================================================

$occupation_labels = [];

$occupation_values = [];


if (
    $latest !== null
) {

    $query =
        mysqli_query(
            $conn,
            "
            SELECT
                occupation,
                population

            FROM village_yearly_occupations

            WHERE yearly_data_id = $latest_id

            ORDER BY population DESC
            "
        );


    while (
        $row = mysqli_fetch_assoc($query)
    ) {

        $occupation_labels[] =
            $row["occupation"];


        $occupation_values[] =
            (int) $row["population"];

    }

}


// =========================================================
// STATISTIK PERKAWINAN
// =========================================================

$marital_labels = [];

$marital_values = [];


if (
    $latest !== null
) {

    $query =
        mysqli_query(
            $conn,
            "
            SELECT
                marital_status,
                population

            FROM village_yearly_marital

            WHERE yearly_data_id = $latest_id

            ORDER BY population DESC
            "
        );


    while (
        $row = mysqli_fetch_assoc($query)
    ) {

        $marital_labels[] =
            $row["marital_status"];


        $marital_values[] =
            (int) $row["population"];

    }

}


// =========================================================
// KELOMPOK UMUR
// =========================================================

$age_labels = [];

$age_values = [];


if (
    $latest !== null
) {

    $query =
        mysqli_query(
            $conn,
            "
            SELECT
                age_group,
                population

            FROM village_yearly_age_groups

            WHERE yearly_data_id = $latest_id

            ORDER BY id ASC
            "
        );


    while (
        $row = mysqli_fetch_assoc($query)
    ) {

        $age_labels[] =
            $row["age_group"];


        $age_values[] =
            (int) $row["population"];

    }

}


// =========================================================
// PERISTIWA KEPENDUDUKAN PER TAHUN
// =========================================================

$event_years = [];

$birth_values = [];

$death_values = [];

$in_values = [];

$out_values = [];


foreach (
    $yearly_data
    as $row
) {

    $year_id =
        (int) $row["id"];


    $event_years[] =
        (int) $row["year"];


    $events = [

        "Kelahiran" => 0,

        "Kematian" => 0,

        "Penduduk Masuk" => 0,

        "Penduduk Keluar" => 0

    ];


    $query_events =
        mysqli_query(
            $conn,
            "
            SELECT
                event_type,
                amount

            FROM village_yearly_events

            WHERE yearly_data_id = $year_id
            "
        );


    while (
        $event =
        mysqli_fetch_assoc(
            $query_events
        )
    ) {

        if (
            isset(
                $events[
                    $event["event_type"]
                ]
            )
        ) {

            $events[
                $event["event_type"]
            ] =
                (int) $event["amount"];

        }

    }


    $birth_values[] =
        $events["Kelahiran"];


    $death_values[] =
        $events["Kematian"];


    $in_values[] =
        $events["Penduduk Masuk"];


    $out_values[] =
        $events["Penduduk Keluar"];

}


// =========================================================
// FORMAT ANGKA
// =========================================================

function formatNumberDashboard(
    $value
) {

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
// JSON UNTUK JAVASCRIPT
// =========================================================

$years_json =
    json_encode(
        $years
    );


$populations_json =
    json_encode(
        $populations
    );


$male_json =
    json_encode(
        $male_populations
    );


$female_json =
    json_encode(
        $female_populations
    );


$family_json =
    json_encode(
        $family_counts
    );


$religion_labels_json =
    json_encode(
        $religion_labels,
        JSON_UNESCAPED_UNICODE
    );


$religion_values_json =
    json_encode(
        $religion_values
    );


$education_labels_json =
    json_encode(
        $education_labels,
        JSON_UNESCAPED_UNICODE
    );


$education_values_json =
    json_encode(
        $education_values
    );


$occupation_labels_json =
    json_encode(
        $occupation_labels,
        JSON_UNESCAPED_UNICODE
    );


$occupation_values_json =
    json_encode(
        $occupation_values
    );


$marital_labels_json =
    json_encode(
        $marital_labels,
        JSON_UNESCAPED_UNICODE
    );


$marital_values_json =
    json_encode(
        $marital_values
    );


$age_labels_json =
    json_encode(
        $age_labels,
        JSON_UNESCAPED_UNICODE
    );


$age_values_json =
    json_encode(
        $age_values
    );


$event_years_json =
    json_encode(
        $event_years
    );


$birth_json =
    json_encode(
        $birth_values
    );


$death_json =
    json_encode(
        $death_values
    );


$in_json =
    json_encode(
        $in_values
    );


$out_json =
    json_encode(
        $out_values
    );

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
        Dashboard Statistik - Cisalada GO
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>


    <style>

        body {

            background: #f5f7fa;

        }


        .card {

            border: 0;

            border-radius: 16px;

        }


        .stat-card {

            transition: .2s;

        }


        .stat-card:hover {

            transform: translateY(-3px);

        }


        .stat-icon {

            width: 52px;

            height: 52px;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eef4ff;

        }


        .stat-number {

            font-size: 28px;

            font-weight: 700;

        }


        .chart-container {

            position: relative;

            height: 360px;

        }


        .chart-container-small {

            position: relative;

            height: 320px;

        }


        .empty-chart {

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #6c757d;

            text-align: center;

        }


        .latest-badge {

            font-size: 12px;

        }


        .growth-up {

            color: #198754;

        }


        .growth-down {

            color: #dc3545;

        }


        .growth-flat {

            color: #6c757d;

        }


        .year-table th {

            white-space: nowrap;

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


            <!-- KEMBALI KE DASHBOARD -->

            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="btn btn-light btn-sm me-2"
            >

                <i
                    class="bi bi-speedometer2 me-1"
                ></i>

                Dashboard

            </a>


            <!-- REKAP DATA -->

            <a
                href="index.php"
                class="btn btn-light btn-sm me-2"
            >

                <i
                    class="bi bi-database me-1"
                ></i>

                Rekap Data

            </a>


            <!-- LOGOUT -->

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
                            class="bi bi-bar-chart-fill
                                   text-primary
                                   fs-3"
                        ></i>


                        <h2
                            class="fw-bold
                                   mb-0"
                        >

                            Dashboard Statistik

                        </h2>


                    </div>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Statistik perkembangan Desa
                        Cisalada berdasarkan data tahunan
                        yang dikelola admin.

                    </p>


                </div>


                <?php if (
                    $latest !== null
                ): ?>


                    <div class="text-end">


                        <span
                            class="badge
                                   bg-primary
                                   latest-badge
                                   mb-1"
                        >

                            DATA TERBARU

                        </span>


                        <div
                            class="fw-bold
                                   fs-5"
                        >

                            Tahun
                            <?= (int)
                                $latest["year"] ?>

                        </div>


                    </div>


                <?php endif; ?>


            </div>


        </div>

    </div>



    <?php if (
        $latest === null
    ): ?>


        <!-- =============================================
             BELUM ADA DATA
        ============================================== -->

        <div
            class="card
                   shadow-sm"
        >

            <div
                class="card-body
                       text-center
                       py-5"
            >


                <i
                    class="bi bi-bar-chart
                           fs-1
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Data Tahunan

                </h4>


                <p
                    class="text-muted"
                >

                    Tambahkan data tahunan terlebih dahulu
                    untuk melihat statistik.

                </p>


                <a
                    href="tambah.php"
                    class="btn btn-primary"
                >

                    <i
                        class="bi bi-plus-lg me-1"
                    ></i>

                    Tambah Data Tahunan

                </a>


            </div>

        </div>


    <?php else: ?>


        <!-- =================================================
             SUMMARY CARDS
        ================================================= -->

        <div class="row g-4 mb-4">


            <!-- PENDUDUK -->

            <div class="col-md-6 col-xl-3">


                <div
                    class="card
                           stat-card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <div
                            class="d-flex
                                   justify-content-between"
                        >


                            <div>


                                <div
                                    class="text-muted
                                           small
                                           mb-1"
                                >

                                    Penduduk Terbaru

                                </div>


                                <div class="stat-number">

                                    <?= formatNumberDashboard(
                                        $latest[
                                            "population"
                                        ]
                                    ) ?>

                                </div>


                                <div
                                    class="text-muted
                                           small"
                                >

                                    Tahun
                                    <?= (int)
                                        $latest["year"] ?>

                                </div>


                            </div>


                            <div class="stat-icon">


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



            <!-- PERTUMBUHAN -->

            <div class="col-md-6 col-xl-3">


                <div
                    class="card
                           stat-card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <div
                            class="d-flex
                                   justify-content-between"
                        >


                            <div>


                                <div
                                    class="text-muted
                                           small
                                           mb-1"
                                >

                                    Pertumbuhan

                                </div>


                                <?php if (
                                    $growth !== null
                                ): ?>


                                    <div
                                        class="stat-number
                                        <?=
                                            $growth > 0
                                                ? "growth-up"
                                                : (
                                                    $growth < 0
                                                        ? "growth-down"
                                                        : "growth-flat"
                                                )
                                        ?>"
                                    >

                                        <?php if (
                                            $growth > 0
                                        ): ?>

                                            +

                                        <?php endif; ?>


                                        <?= formatNumberDashboard(
                                            $growth
                                        ) ?>

                                    </div>


                                    <?php if (
                                        $growth_percent !== null
                                    ): ?>

                                        <div
                                            class="text-muted
                                                   small"
                                        >

                                            <?= number_format(
                                                $growth_percent,
                                                2,
                                                ",",
                                                "."
                                            ) ?>%

                                        </div>

                                    <?php endif; ?>


                                <?php else: ?>


                                    <div
                                        class="stat-number
                                               text-muted"
                                    >

                                        -

                                    </div>


                                    <div
                                        class="text-muted
                                               small"
                                    >

                                        Belum tersedia

                                    </div>


                                <?php endif; ?>


                            </div>


                            <div class="stat-icon">


                                <i
                                    class="bi bi-graph-up-arrow
                                           text-primary
                                           fs-4"
                                ></i>


                            </div>


                        </div>


                    </div>

                </div>


            </div>



            <!-- KK -->

            <div class="col-md-6 col-xl-3">


                <div
                    class="card
                           stat-card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <div
                            class="d-flex
                                   justify-content-between"
                        >


                            <div>


                                <div
                                    class="text-muted
                                           small
                                           mb-1"
                                >

                                    Kepala Keluarga

                                </div>


                                <div class="stat-number">

                                    <?= formatNumberDashboard(
                                        $latest[
                                            "family_count"
                                        ]
                                    ) ?>

                                </div>


                                <div
                                    class="text-muted
                                           small"
                                >

                                    Tahun
                                    <?= (int)
                                        $latest["year"] ?>

                                </div>


                            </div>


                            <div class="stat-icon">


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



            <!-- LUAS -->

            <div class="col-md-6 col-xl-3">


                <div
                    class="card
                           stat-card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <div
                            class="d-flex
                                   justify-content-between"
                        >


                            <div>


                                <div
                                    class="text-muted
                                           small
                                           mb-1"
                                >

                                    Luas Wilayah

                                </div>


                                <div class="stat-number">

                                    <?php if (
                                        $latest["area"]
                                        !== null
                                    ): ?>

                                        <?= number_format(
                                            $latest["area"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>


                                </div>


                                <div
                                    class="text-muted
                                           small"
                                >

                                    Hektare

                                </div>


                            </div>


                            <div class="stat-icon">


                                <i
                                    class="bi bi-geo-alt-fill
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
             GRAFIK PERTUMBUHAN
        ================================================= -->

        <div
            class="card
                   shadow-sm
                   mb-4"
        >

            <div class="card-body p-4">


                <div
                    class="d-flex
                           justify-content-between
                           align-items-center
                           mb-3"
                >


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            <i
                                class="bi bi-graph-up
                                       text-primary
                                       me-2"
                            ></i>

                            Pertumbuhan Penduduk

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Perkembangan jumlah penduduk
                            dari tahun ke tahun.

                        </small>


                    </div>


                </div>


                <div class="chart-container">


                    <canvas
                        id="populationChart"
                    ></canvas>


                </div>


            </div>

        </div>



        <!-- =================================================
             GENDER + KK
        ================================================= -->

        <div class="row g-4 mb-4">


            <div class="col-xl-8">


                <div
                    class="card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Komposisi Penduduk

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Perbandingan laki-laki
                            dan perempuan setiap tahun.

                        </small>


                        <div
                            class="chart-container
                                   mt-3"
                        >

                            <canvas
                                id="genderChart"
                            ></canvas>

                        </div>


                    </div>

                </div>


            </div>



            <div class="col-xl-4">


                <div
                    class="card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Kepala Keluarga

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Perkembangan jumlah KK.

                        </small>


                        <div
                            class="chart-container
                                   mt-3"
                        >

                            <canvas
                                id="familyChart"
                            ></canvas>

                        </div>


                    </div>

                </div>


            </div>


        </div>



        <!-- =================================================
             AGAMA
        ================================================= -->

        <div class="row g-4 mb-4">


            <div class="col-xl-6">


                <div
                    class="card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Penduduk Berdasarkan Agama

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Data tahun
                            <?= (int)
                                $latest["year"] ?>.

                        </small>


                        <div
                            class="chart-container-small
                                   mt-3"
                        >


                            <?php if (
                                count(
                                    $religion_labels
                                ) > 0
                            ): ?>


                                <canvas
                                    id="religionChart"
                                ></canvas>


                            <?php else: ?>


                                <div
                                    class="empty-chart"
                                >

                                    Data agama belum
                                    tersedia untuk tahun
                                    <?= (int)
                                        $latest["year"] ?>.

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>

                </div>


            </div>



            <!-- PERKAWINAN -->

            <div class="col-xl-6">


                <div
                    class="card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Status Perkawinan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Data tahun
                            <?= (int)
                                $latest["year"] ?>.

                        </small>


                        <div
                            class="chart-container-small
                                   mt-3"
                        >


                            <?php if (
                                count(
                                    $marital_labels
                                ) > 0
                            ): ?>


                                <canvas
                                    id="maritalChart"
                                ></canvas>


                            <?php else: ?>


                                <div
                                    class="empty-chart"
                                >

                                    Data perkawinan belum
                                    tersedia untuk tahun
                                    <?= (int)
                                        $latest["year"] ?>.

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>

                </div>


            </div>


        </div>



        <!-- =================================================
             PENDIDIKAN + PEKERJAAN
        ================================================= -->

        <div class="row g-4 mb-4">


            <div class="col-xl-6">


                <div
                    class="card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Pendidikan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Data tahun
                            <?= (int)
                                $latest["year"] ?>.

                        </small>


                        <div
                            class="chart-container-small
                                   mt-3"
                        >


                            <?php if (
                                count(
                                    $education_labels
                                ) > 0
                            ): ?>


                                <canvas
                                    id="educationChart"
                                ></canvas>


                            <?php else: ?>


                                <div
                                    class="empty-chart"
                                >

                                    Data pendidikan belum
                                    tersedia untuk tahun
                                    <?= (int)
                                        $latest["year"] ?>.

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>

                </div>


            </div>



            <div class="col-xl-6">


                <div
                    class="card
                           shadow-sm
                           h-100"
                >

                    <div class="card-body p-4">


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Pekerjaan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Data tahun
                            <?= (int)
                                $latest["year"] ?>.

                        </small>


                        <div
                            class="chart-container-small
                                   mt-3"
                        >


                            <?php if (
                                count(
                                    $occupation_labels
                                ) > 0
                            ): ?>


                                <canvas
                                    id="occupationChart"
                                ></canvas>


                            <?php else: ?>


                                <div
                                    class="empty-chart"
                                >

                                    Data pekerjaan belum
                                    tersedia untuk tahun
                                    <?= (int)
                                        $latest["year"] ?>.

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>

                </div>


            </div>


        </div>



        <!-- =================================================
             UMUR
        ================================================= -->

        <div
            class="card
                   shadow-sm
                   mb-4"
        >

            <div class="card-body p-4">


                <h5
                    class="fw-bold
                           mb-1"
                >

                    Kelompok Umur

                </h5>


                <small
                    class="text-muted"
                >

                    Distribusi penduduk berdasarkan
                    kelompok umur tahun
                    <?= (int)
                        $latest["year"] ?>.

                </small>


                <div
                    class="chart-container
                           mt-3"
                >


                    <?php if (
                        count(
                            $age_labels
                        ) > 0
                    ): ?>


                        <canvas
                            id="ageChart"
                        ></canvas>


                    <?php else: ?>


                        <div
                            class="empty-chart"
                        >

                            Data kelompok umur belum
                            tersedia.

                        </div>


                    <?php endif; ?>


                </div>


            </div>

        </div>



        <!-- =================================================
             PERISTIWA KEPENDUDUKAN
        ================================================= -->

        <div
            class="card
                   shadow-sm
                   mb-4"
        >

            <div class="card-body p-4">


                <h5
                    class="fw-bold
                           mb-1"
                >

                    Peristiwa Kependudukan

                </h5>


                <small
                    class="text-muted"
                >

                    Kelahiran, kematian, penduduk masuk,
                    dan penduduk keluar berdasarkan tahun.

                </small>


                <div
                    class="chart-container
                           mt-3"
                >


                    <canvas
                        id="eventChart"
                    ></canvas>


                </div>


            </div>

        </div>



        <!-- =================================================
             TABEL REKAP TAHUNAN
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
                           mb-3"
                >


                    <div>


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            Rekap Data Tahunan

                        </h5>


                        <small
                            class="text-muted"
                        >

                            Seluruh data yang tersimpan
                            di database.

                        </small>


                    </div>


                    <a
                        href="tambah.php"
                        class="btn btn-primary"
                    >

                        <i
                            class="bi bi-plus-lg me-1"
                        ></i>

                        Tambah Tahun

                    </a>


                </div>


                <div
                    class="table-responsive"
                >


                    <table
                        class="table
                               table-hover
                               align-middle
                               year-table"
                    >

                        <thead>


                            <tr>


                                <th>
                                    Tahun
                                </th>


                                <th>
                                    Penduduk
                                </th>


                                <th>
                                    Laki-laki
                                </th>


                                <th>
                                    Perempuan
                                </th>


                                <th>
                                    KK
                                </th>


                                <th>
                                    Pertumbuhan
                                </th>


                                <th
                                    class="text-end"
                                >

                                    Aksi

                                </th>


                            </tr>


                        </thead>


                        <tbody>


                            <?php

                            $table_previous =
                                null;


                            foreach (
                                array_reverse(
                                    $yearly_data
                                )
                                as $row
                            ):

                                $row_growth =
                                    null;


                                if (
                                    $table_previous
                                    !== null
                                    &&
                                    $row["population"]
                                    !== null
                                    &&
                                    $table_previous[
                                        "population"
                                    ]
                                    !== null
                                ) {

                                    $row_growth =
                                        (int)
                                            $row[
                                                "population"
                                            ]
                                        -
                                        (int)
                                            $table_previous[
                                                "population"
                                            ];

                                }

                            ?>


                                <tr>


                                    <td>


                                        <strong>

                                            <?= (int)
                                                $row["year"] ?>

                                        </strong>


                                        <?php if (
                                            (int)
                                            $row[
                                                "is_baseline"
                                            ] === 1
                                        ): ?>


                                            <span
                                                class="badge
                                                       bg-success
                                                       ms-1"
                                            >

                                                Baseline

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>

                                        <?= formatNumberDashboard(
                                            $row[
                                                "population"
                                            ]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= formatNumberDashboard(
                                            $row[
                                                "male_population"
                                            ]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= formatNumberDashboard(
                                            $row[
                                                "female_population"
                                            ]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= formatNumberDashboard(
                                            $row[
                                                "family_count"
                                            ]
                                        ) ?>

                                    </td>


                                    <td>


                                        <?php if (
                                            $row_growth
                                            === null
                                        ): ?>

                                            <span
                                                class="text-muted"
                                            >

                                                -

                                            </span>


                                        <?php elseif (
                                            $row_growth > 0
                                        ): ?>

                                            <span
                                                class="text-success
                                                       fw-semibold"
                                            >

                                                +<?= formatNumberDashboard(
                                                    $row_growth
                                                ) ?>

                                            </span>


                                        <?php elseif (
                                            $row_growth < 0
                                        ): ?>

                                            <span
                                                class="text-danger
                                                       fw-semibold"
                                            >

                                                <?= formatNumberDashboard(
                                                    $row_growth
                                                ) ?>

                                            </span>


                                        <?php else: ?>

                                            <span
                                                class="text-muted
                                                       fw-semibold"
                                            >

                                                0

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td
                                        class="text-end"
                                    >


                                        <a
                                            href="detail.php?id=<?= (int) $row["id"] ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >

                                            <i
                                                class="bi bi-eye"
                                            ></i>

                                        </a>


                                        <a
                                            href="edit.php?id=<?= (int) $row["id"] ?>"
                                            class="btn btn-sm btn-outline-warning"
                                        >

                                            <i
                                                class="bi bi-pencil"
                                            ></i>

                                        </a>


                                    </td>


                                </tr>


                            <?php

                                $table_previous =
                                    $row;

                            endforeach;

                            ?>


                        </tbody>


                    </table>


                </div>


            </div>

        </div>


    <?php endif; ?>


</div>



<!-- =====================================================
     CHART JAVASCRIPT
===================================================== -->

<script>


// =========================================================
// DATA PHP → JAVASCRIPT
// =========================================================

const years =
    <?= $years_json ?>;


const populations =
    <?= $populations_json ?>;


const malePopulations =
    <?= $male_json ?>;


const femalePopulations =
    <?= $female_json ?>;


const familyCounts =
    <?= $family_json ?>;


const religionLabels =
    <?= $religion_labels_json ?>;


const religionValues =
    <?= $religion_values_json ?>;


const educationLabels =
    <?= $education_labels_json ?>;


const educationValues =
    <?= $education_values_json ?>;


const occupationLabels =
    <?= $occupation_labels_json ?>;


const occupationValues =
    <?= $occupation_values_json ?>;


const maritalLabels =
    <?= $marital_labels_json ?>;


const maritalValues =
    <?= $marital_values_json ?>;


const ageLabels =
    <?= $age_labels_json ?>;


const ageValues =
    <?= $age_values_json ?>;


const eventYears =
    <?= $event_years_json ?>;


const birthValues =
    <?= $birth_json ?>;


const deathValues =
    <?= $death_json ?>;


const inValues =
    <?= $in_json ?>;


const outValues =
    <?= $out_json ?>;


// =========================================================
// DEFAULT CHART OPTIONS
// =========================================================

const defaultScale = {

    responsive: true,

    maintainAspectRatio: false,

    interaction: {

        intersect: false,

        mode: "index"

    },

    plugins: {

        legend: {

            position: "top"

        }

    }

};


// =========================================================
// POPULATION CHART
// =========================================================

const populationCanvas =
    document.getElementById(
        "populationChart"
    );


if (
    populationCanvas
) {

    new Chart(
        populationCanvas,
        {

            type: "line",

            data: {

                labels: years,

                datasets: [

                    {

                        label:
                            "Jumlah Penduduk",

                        data:
                            populations,

                        tension:
                            0.3,

                        fill:
                            false

                    }

                ]

            },

            options: {

                ...defaultScale,

                scales: {

                    y: {

                        beginAtZero: false

                    }

                }

            }

        }
    );

}


// =========================================================
// GENDER CHART
// =========================================================

const genderCanvas =
    document.getElementById(
        "genderChart"
    );


if (
    genderCanvas
) {

    new Chart(
        genderCanvas,
        {

            type: "line",

            data: {

                labels: years,

                datasets: [

                    {

                        label:
                            "Laki-laki",

                        data:
                            malePopulations,

                        tension:
                            0.3

                    },

                    {

                        label:
                            "Perempuan",

                        data:
                            femalePopulations,

                        tension:
                            0.3

                    }

                ]

            },

            options: {

                ...defaultScale,

                scales: {

                    y: {

                        beginAtZero: true

                    }

                }

            }

        }
    );

}


// =========================================================
// FAMILY CHART
// =========================================================

const familyCanvas =
    document.getElementById(
        "familyChart"
    );


if (
    familyCanvas
) {

    new Chart(
        familyCanvas,
        {

            type: "bar",

            data: {

                labels: years,

                datasets: [

                    {

                        label:
                            "Kepala Keluarga",

                        data:
                            familyCounts

                    }

                ]

            },

            options: {

                ...defaultScale,

                scales: {

                    y: {

                        beginAtZero: true

                    }

                }

            }

        }
    );

}


// =========================================================
// RELIGION CHART
// =========================================================

const religionCanvas =
    document.getElementById(
        "religionChart"
    );


if (
    religionCanvas
) {

    new Chart(
        religionCanvas,
        {

            type: "doughnut",

            data: {

                labels:
                    religionLabels,

                datasets: [

                    {

                        data:
                            religionValues

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        position:
                            "right"

                    }

                }

            }

        }
    );

}


// =========================================================
// MARITAL CHART
// =========================================================

const maritalCanvas =
    document.getElementById(
        "maritalChart"
    );


if (
    maritalCanvas
) {

    new Chart(
        maritalCanvas,
        {

            type: "doughnut",

            data: {

                labels:
                    maritalLabels,

                datasets: [

                    {

                        data:
                            maritalValues

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        position:
                            "right"

                    }

                }

            }

        }
    );

}


// =========================================================
// EDUCATION CHART
// =========================================================

const educationCanvas =
    document.getElementById(
        "educationChart"
    );


if (
    educationCanvas
) {

    new Chart(
        educationCanvas,
        {

            type: "bar",

            data: {

                labels:
                    educationLabels,

                datasets: [

                    {

                        label:
                            "Penduduk",

                        data:
                            educationValues

                    }

                ]

            },

            options: {

                ...defaultScale,

                indexAxis:
                    "y",

                scales: {

                    x: {

                        beginAtZero: true

                    }

                }

            }

        }
    );

}


// =========================================================
// OCCUPATION CHART
// =========================================================

const occupationCanvas =
    document.getElementById(
        "occupationChart"
    );


if (
    occupationCanvas
) {

    new Chart(
        occupationCanvas,
        {

            type: "bar",

            data: {

                labels:
                    occupationLabels,

                datasets: [

                    {

                        label:
                            "Penduduk",

                        data:
                            occupationValues

                    }

                ]

            },

            options: {

                ...defaultScale,

                indexAxis:
                    "y",

                scales: {

                    x: {

                        beginAtZero: true

                    }

                }

            }

        }
    );

}


// =========================================================
// AGE CHART
// =========================================================

const ageCanvas =
    document.getElementById(
        "ageChart"
    );


if (
    ageCanvas
) {

    new Chart(
        ageCanvas,
        {

            type: "bar",

            data: {

                labels:
                    ageLabels,

                datasets: [

                    {

                        label:
                            "Penduduk",

                        data:
                            ageValues

                    }

                ]

            },

            options: {

                ...defaultScale,

                scales: {

                    y: {

                        beginAtZero: true

                    }

                }

            }

        }
    );

}


// =========================================================
// EVENT CHART
// =========================================================

const eventCanvas =
    document.getElementById(
        "eventChart"
    );


if (
    eventCanvas
) {

    new Chart(
        eventCanvas,
        {

            type: "line",

            data: {

                labels:
                    eventYears,

                datasets: [

                    {

                        label:
                            "Kelahiran",

                        data:
                            birthValues,

                        tension:
                            0.3

                    },

                    {

                        label:
                            "Kematian",

                        data:
                            deathValues,

                        tension:
                            0.3

                    },

                    {

                        label:
                            "Penduduk Masuk",

                        data:
                            inValues,

                        tension:
                            0.3

                    },

                    {

                        label:
                            "Penduduk Keluar",

                        data:
                            outValues,

                        tension:
                            0.3

                    }

                ]

            },

            options: {

                ...defaultScale,

                scales: {

                    y: {

                        beginAtZero: true

                    }

                }

            }

        }
    );

}


</script>



</body>

</html>