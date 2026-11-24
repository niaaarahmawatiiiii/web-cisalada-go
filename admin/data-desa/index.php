<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// AMBIL SEMUA DATA TAHUNAN
// =========================================================

$result = mysqli_query(
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

    ORDER BY year DESC
    "
);


if (!$result) {

    die(
        "Gagal mengambil data tahunan: "
        . mysqli_error($conn)
    );

}


// =========================================================
// STATISTIK UMUM
// =========================================================

$total_years = 0;

$latest_year = null;

$latest_population = null;

$baseline_year = null;

$baseline_population = null;


$yearly_data = [];


while (
    $row = mysqli_fetch_assoc($result)
) {

    $yearly_data[] = $row;

    $total_years++;


    // -----------------------------------------------------
    // TAHUN TERBARU
    // -----------------------------------------------------

    if (
        $latest_year === null
        ||
        (int) $row["year"] > $latest_year
    ) {

        $latest_year =
            (int) $row["year"];

        $latest_population =
            $row["population"];

    }


    // -----------------------------------------------------
    // BASELINE
    // -----------------------------------------------------

    if (
        (int) $row["is_baseline"] === 1
    ) {

        $baseline_year =
            (int) $row["year"];

        $baseline_population =
            $row["population"];

    }

}


// =========================================================
// PERTUMBUHAN TERBARU
// =========================================================

$latest_growth = null;

$latest_growth_percent = null;

$previous_year = null;

$previous_population = null;


if (
    count($yearly_data) >= 2
) {

    /*
     * Karena data sudah ORDER BY year DESC,
     * index 0 = tahun terbaru
     * index 1 = tahun sebelumnya.
     */

    $current =
        $yearly_data[0];

    $previous =
        $yearly_data[1];


    if (
        $current["population"] !== null
        &&
        $previous["population"] !== null
    ) {

        $latest_growth =
            (int) $current["population"]
            -
            (int) $previous["population"];


        if (
            (int) $previous["population"] !== 0
        ) {

            $latest_growth_percent =
                (
                    $latest_growth
                    /
                    (int) $previous["population"]
                )
                * 100;

        }

    }


    $previous_year =
        (int) $previous["year"];

    $previous_population =
        $previous["population"];

}


// =========================================================
// FUNGSI FORMAT ANGKA
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
// FUNGSI FORMAT DECIMAL
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
        Rekap Data Desa - Cisalada GO
    </title>


    <!-- =================================================
         BOOTSTRAP
    ================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =================================================
         BOOTSTRAP ICON
    ================================================= -->

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


        .stat-card {

            transition: all .2s ease;

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


        .table {

            vertical-align: middle;

        }


        .year-badge {

            font-size: 14px;

            padding: 7px 12px;

            border-radius: 8px;

        }


        .baseline-badge {

            font-size: 12px;

            padding: 6px 10px;

            border-radius: 8px;

        }


        .empty-value {

            color: #8a8f98;

            font-style: italic;

        }


        .page-header {

            background: #ffffff;

            border-radius: 16px;

            padding: 24px;

            margin-bottom: 24px;

        }


        .table-card {

            overflow: hidden;

        }


        .growth-positive {

            color: #198754;

            font-weight: 600;

        }


        .growth-negative {

            color: #dc3545;

            font-weight: 600;

        }


        .growth-zero {

            color: #6c757d;

            font-weight: 600;

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



<!-- =====================================================
     CONTENT
===================================================== -->

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
                        class="bi bi-bar-chart-line-fill
                               text-primary
                               fs-4"
                    ></i>


                    <h2
                        class="fw-bold
                               mb-0"
                    >

                        Rekap Data Desa

                    </h2>

                </div>


                <p
                    class="text-muted
                           mb-0"
                >

                    Kelola dan pantau data Desa Cisalada
                    berdasarkan tahun.

                </p>


            </div>


            <a
                href="tambah.php"
                class="btn btn-primary"
            >

                <i
                    class="bi bi-plus-lg me-1"
                ></i>

                Tambah Data Tahun

            </a>


        </div>


    </div>



    <!-- =================================================
         INFORMASI SISTEM
    ================================================= -->

    <div
        class="alert
               alert-info
               border-0
               shadow-sm
               mb-4"
    >


        <div
            class="d-flex
                   align-items-start"
        >

            <i
                class="bi bi-info-circle-fill
                       fs-5
                       me-3"
            ></i>


            <div>


                <strong>
                    Sistem Rekap Tahunan
                </strong>


                <div class="mt-1">

                    Setiap tahun disimpan sebagai data
                    tersendiri. Data tahun sebelumnya tidak
                    akan tertimpa ketika tahun baru ditambahkan.

                    Data tahun 2024 digunakan sebagai
                    <strong>baseline</strong>.

                </div>


            </div>

        </div>


    </div>



    <!-- =================================================
         STATISTIK RINGKAS
    ================================================= -->

    <div class="row g-4 mb-4">


        <!-- JUMLAH TAHUN -->

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
                               justify-content-between
                               align-items-center"
                    >


                        <div>


                            <p
                                class="text-muted
                                       mb-1"
                            >

                                Rekap Tersedia

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?= formatNumber(
                                    $total_years
                                ) ?>

                            </h2>


                            <small
                                class="text-muted"
                            >

                                Tahun

                            </small>


                        </div>


                        <div class="stat-icon">


                            <i
                                class="bi bi-calendar3
                                       text-primary
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>



        <!-- BASELINE -->

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
                               justify-content-between
                               align-items-center"
                    >


                        <div>


                            <p
                                class="text-muted
                                       mb-1"
                            >

                                Baseline

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?php if (
                                    $baseline_year !== null
                                ): ?>

                                    <?= $baseline_year ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>


                            </h2>


                            <small
                                class="text-muted"
                            >

                                Tahun awal data

                            </small>


                        </div>


                        <div class="stat-icon">


                            <i
                                class="bi bi-database-check
                                       text-primary
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>



        <!-- DATA TERBARU -->

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
                               justify-content-between
                               align-items-center"
                    >


                        <div>


                            <p
                                class="text-muted
                                       mb-1"
                            >

                                Data Terbaru

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?php if (
                                    $latest_year !== null
                                ): ?>

                                    <?= $latest_year ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>


                            </h2>


                            <small
                                class="text-muted"
                            >

                                Rekap terakhir

                            </small>


                        </div>


                        <div class="stat-icon">


                            <i
                                class="bi bi-clock-history
                                       text-primary
                                       fs-4"
                            ></i>


                        </div>


                    </div>


                </div>

            </div>


        </div>



        <!-- PENDUDUK TERBARU -->

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
                               justify-content-between
                               align-items-center"
                    >


                        <div>


                            <p
                                class="text-muted
                                       mb-1"
                            >

                                Penduduk Terbaru

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?php if (
                                    $latest_population !== null
                                ): ?>

                                    <?= formatNumber(
                                        $latest_population
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>


                            </h2>


                            <small
                                class="text-muted"
                            >

                                <?php if (
                                    $latest_year !== null
                                ): ?>

                                    Tahun <?= $latest_year ?>

                                <?php else: ?>

                                    Belum tersedia

                                <?php endif; ?>

                            </small>


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


    </div>



    <!-- =================================================
         PERTUMBUHAN TERBARU
    ================================================= -->

    <?php if (
        $latest_growth !== null
    ): ?>


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


                        <h5
                            class="fw-bold
                                   mb-1"
                        >

                            <i
                                class="bi bi-graph-up-arrow
                                       text-primary
                                       me-2"
                            ></i>

                            Pertumbuhan Penduduk Terbaru

                        </h5>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Perbandingan tahun
                            <?= $previous_year ?>
                            dengan
                            <?= $latest_year ?>.

                        </p>


                    </div>


                    <div
                        class="text-end"
                    >


                        <?php if (
                            $latest_growth > 0
                        ): ?>


                            <div
                                class="growth-positive
                                       fs-4"
                            >

                                +<?= formatNumber(
                                    $latest_growth
                                ) ?>

                                jiwa

                            </div>


                        <?php elseif (
                            $latest_growth < 0
                        ): ?>


                            <div
                                class="growth-negative
                                       fs-4"
                            >

                                <?= formatNumber(
                                    $latest_growth
                                ) ?>

                                jiwa

                            </div>


                        <?php else: ?>


                            <div
                                class="growth-zero
                                       fs-4"
                            >

                                0 jiwa

                            </div>


                        <?php endif; ?>


                        <?php if (
                            $latest_growth_percent !== null
                        ): ?>


                            <small
                                class="text-muted"
                            >

                                <?= number_format(
                                    $latest_growth_percent,
                                    2,
                                    ",",
                                    "."
                                ) ?>%

                            </small>


                        <?php endif; ?>


                    </div>


                </div>


            </div>

        </div>


    <?php endif; ?>



    <!-- =================================================
         REKAP TAHUNAN
    ================================================= -->

    <div
        class="card
               table-card
               shadow-sm"
    >


        <div
            class="card-header
                   bg-white
                   border-0
                   p-4"
        >


            <div
                class="d-flex
                       justify-content-between
                       align-items-center"
            >


                <div>


                    <h5
                        class="fw-bold
                               mb-1"
                    >

                        Rekap Data Tahunan

                    </h5>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Riwayat data Desa Cisalada
                        dari tahun ke tahun.

                    </p>


                </div>


                <span
                    class="badge
                           bg-primary-subtle
                           text-primary
                           px-3
                           py-2"
                >

                    <?= $total_years ?>
                    Tahun

                </span>


            </div>


        </div>



        <div class="card-body p-0">


            <div class="table-responsive">


                <table
                    class="table
                           table-hover
                           mb-0"
                >


                    <thead
                        class="table-light"
                    >

                        <tr>


                            <th
                                class="px-4"
                            >

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

                                Dusun

                            </th>


                            <th>

                                RW

                            </th>


                            <th>

                                RT

                            </th>


                            <th>

                                Status

                            </th>


                            <th
                                class="text-center"
                            >

                                Aksi

                            </th>


                        </tr>


                    </thead>


                    <tbody>


                    <?php if (
                        count($yearly_data) > 0
                    ): ?>


                        <?php foreach (
                            $yearly_data
                            as $row
                        ): ?>


                            <tr>


                                <!-- TAHUN -->

                                <td
                                    class="px-4"
                                >


                                    <span
                                        class="badge
                                               bg-primary-subtle
                                               text-primary
                                               year-badge"
                                    >

                                        <?= (int)
                                            $row["year"] ?>

                                    </span>


                                </td>



                                <!-- PENDUDUK -->

                                <td>


                                    <?php if (
                                        $row["population"]
                                        !== null
                                    ): ?>

                                        <strong>

                                            <?= formatNumber(
                                                $row["population"]
                                            ) ?>

                                        </strong>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            Belum tersedia

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- LAKI-LAKI -->

                                <td>


                                    <?php if (
                                        $row[
                                            "male_population"
                                        ]
                                        !== null
                                    ): ?>

                                        <?= formatNumber(
                                            $row[
                                                "male_population"
                                            ]
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            -

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- PEREMPUAN -->

                                <td>


                                    <?php if (
                                        $row[
                                            "female_population"
                                        ]
                                        !== null
                                    ): ?>

                                        <?= formatNumber(
                                            $row[
                                                "female_population"
                                            ]
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            -

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- KK -->

                                <td>


                                    <?php if (
                                        $row[
                                            "family_count"
                                        ]
                                        !== null
                                    ): ?>

                                        <?= formatNumber(
                                            $row[
                                                "family_count"
                                            ]
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            -

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- DUSUN -->

                                <td>


                                    <?php if (
                                        $row[
                                            "hamlet_count"
                                        ]
                                        !== null
                                    ): ?>

                                        <?= formatNumber(
                                            $row[
                                                "hamlet_count"
                                            ]
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            Belum tersedia

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- RW -->

                                <td>


                                    <?php if (
                                        $row[
                                            "rw_count"
                                        ]
                                        !== null
                                    ): ?>

                                        <?= formatNumber(
                                            $row[
                                                "rw_count"
                                            ]
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            Belum tersedia

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- RT -->

                                <td>


                                    <?php if (
                                        $row[
                                            "rt_count"
                                        ]
                                        !== null
                                    ): ?>

                                        <?= formatNumber(
                                            $row[
                                                "rt_count"
                                            ]
                                        ) ?>

                                    <?php else: ?>

                                        <span
                                            class="empty-value"
                                        >

                                            Belum tersedia

                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- STATUS -->

                                <td>


                                    <?php if (
                                        (int)
                                        $row[
                                            "is_baseline"
                                        ]
                                        === 1
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-success-subtle
                                                   text-success
                                                   baseline-badge"
                                        >

                                            <i
                                                class="bi bi-check-circle me-1"
                                            ></i>

                                            Baseline

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="badge
                                                   bg-light
                                                   text-dark
                                                   border
                                                   baseline-badge"
                                        >

                                            Data Tahunan

                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- AKSI -->

                                <td
                                    class="text-center"
                                >


                                    <a
                                        href="detail.php?id=<?= (int) $row["id"] ?>"
                                        class="btn btn-sm btn-info text-white me-1"
                                        title="Lihat detail"
                                    >

                                        <i
                                            class="bi bi-eye"
                                        ></i>

                                    </a>


                                    <a
                                        href="edit.php?id=<?= (int) $row["id"] ?>"
                                        class="btn btn-sm btn-warning"
                                        title="Edit data"
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>


                            <td
                                colspan="10"
                                class="text-center
                                       py-5"
                            >


                                <i
                                    class="bi bi-database
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Data

                                </h5>


                                <p
                                    class="text-muted
                                           mb-3"
                                >

                                    Belum ada rekap data
                                    tahunan.

                                </p>


                                <a
                                    href="tambah.php"
                                    class="btn btn-primary"
                                >

                                    <i
                                        class="bi bi-plus-lg me-1"
                                    ></i>

                                    Tambah Data Tahun

                                </a>


                            </td>


                        </tr>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>


    </div>



    <!-- =================================================
         KETERANGAN
    ================================================= -->

    <div
        class="mt-4
               small
               text-muted"
    >


        <i
            class="bi bi-info-circle me-1"
        ></i>

        <strong>Baseline</strong>
        adalah data awal yang digunakan sebagai
        acuan pertumbuhan. Data tahun berikutnya
        disimpan tanpa mengubah data tahun sebelumnya.


    </div>



    <!-- =================================================
         BACK
    ================================================= -->

    <div class="mt-4">


        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="btn btn-secondary"
        >

            <i
                class="bi bi-arrow-left me-1"
            ></i>

            Kembali ke Dashboard

        </a>


    </div>


</div>



<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>