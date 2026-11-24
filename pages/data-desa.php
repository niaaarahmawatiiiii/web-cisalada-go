<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// =========================================================
// DATA DASAR DESA
// =========================================================

$result = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_data
    ORDER BY id ASC
    LIMIT 1
    "
);

$data = mysqli_fetch_assoc($result);


// =========================================================
// DATA TAHUNAN DESA
// =========================================================

// Ambil semua data tahunan
$yearly_data = [];

$result_yearly = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_yearly_data
    ORDER BY year ASC
    "
);

if ($result_yearly) {

    while ($row = mysqli_fetch_assoc($result_yearly)) {

        $yearly_data[] = $row;

    }

}


// =========================================================
// TENTUKAN TAHUN YANG DITAMPILKAN
// =========================================================

$selected_year = null;


// Jika user memilih tahun dari URL
if (
    isset($_GET["tahun"])
    &&
    is_numeric($_GET["tahun"])
) {

    $selected_year = (int) $_GET["tahun"];

}


// Jika tidak memilih tahun,
// gunakan tahun terbaru
if ($selected_year === null && !empty($yearly_data)) {

    $latest_index = count($yearly_data) - 1;

    $selected_year = (int) $yearly_data[$latest_index]["year"];

}


// =========================================================
// AMBIL DATA TAHUN TERPILIH
// =========================================================

$selected_year_data = null;

foreach ($yearly_data as $year_data) {

    if (
        (int) $year_data["year"]
        ===
        $selected_year
    ) {

        $selected_year_data = $year_data;

        break;

    }

}


// =========================================================
// FALLBACK
// =========================================================

// Jika data tahunan belum tersedia,
// gunakan data lama dari village_data.

if ($selected_year_data === null) {

    $selected_year_data = [

        "year" =>
            isset($data["year"])
                ? $data["year"]
                : date("Y"),

        "population" =>
            isset($data["population"])
                ? $data["population"]
                : 0,

        "male_population" =>
            isset($data["male_population"])
                ? $data["male_population"]
                : 0,

        "female_population" =>
            isset($data["female_population"])
                ? $data["female_population"]
                : 0,

        "family_count" =>
            isset($data["family_count"])
                ? $data["family_count"]
                : 0,

        "hamlet_count" =>
            isset($data["hamlet_count"])
                ? $data["hamlet_count"]
                : 0,

        "rw_count" =>
            isset($data["rw_count"])
                ? $data["rw_count"]
                : 0,

        "rt_count" =>
            isset($data["rt_count"])
                ? $data["rt_count"]
                : 0,

        "area" =>
            isset($data["area"])
                ? $data["area"]
                : 0,

        "population_density" =>
            isset($data["population_density"])
                ? $data["population_density"]
                : 0,

        "source" =>
            isset($data["source"])
                ? $data["source"]
                : "",

        "notes" =>
            isset($data["notes"])
                ? $data["notes"]
                : ""

    ];

}


// =========================================================
// DATA UTAMA UNTUK TAMPILAN
// =========================================================

$population =
    $selected_year_data["population"] ?? 0;

$male_population =
    $selected_year_data["male_population"] ?? 0;

$female_population =
    $selected_year_data["female_population"] ?? 0;

$family_count =
    $selected_year_data["family_count"] ?? 0;

$hamlet_count =
    $selected_year_data["hamlet_count"] ?? 0;

$rw_count =
    $selected_year_data["rw_count"] ?? 0;

$rt_count =
    $selected_year_data["rt_count"] ?? 0;

$area =
    $selected_year_data["area"] ?? 0;

$population_density =
    $selected_year_data["population_density"] ?? 0;

$source =
    $selected_year_data["source"] ?? "";


// =========================================================
// DATA AGAMA
// =========================================================

$religions = [];

$result = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_religions
    ORDER BY population DESC
    "
);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $religions[] = $row;

    }

}


// =========================================================
// DATA PERKAWINAN
// =========================================================

$marital = [];

$result = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_marital_status
    ORDER BY population DESC
    "
);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $marital[] = $row;

    }

}


// =========================================================
// DATA PENDIDIKAN
// =========================================================

$education = [];

$result = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_education
    ORDER BY total_population DESC
    "
);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $education[] = $row;

    }

}


// =========================================================
// DATA PEKERJAAN
// =========================================================

$occupations = [];

$result = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_occupations
    ORDER BY total_population DESC
    "
);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $occupations[] = $row;

    }

}


// =========================================================
// DATA USIA
// =========================================================

$age_groups = [];

$result = mysqli_query(
    $conn,
    "
    SELECT *
    FROM village_age_groups
    "
);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $age_groups[] = $row;

    }

}


// =========================================================
// DATA UNTUK GRAFIK PERTUMBUHAN
// =========================================================

$year_labels = [];
$population_history = [];
$male_history = [];
$female_history = [];

foreach ($yearly_data as $row) {

    $year_labels[] =
        (string) $row["year"];

    $population_history[] =
        (int) ($row["population"] ?? 0);

    $male_history[] =
        (int) ($row["male_population"] ?? 0);

    $female_history[] =
        (int) ($row["female_population"] ?? 0);

}


// =========================================================
// PAGE TITLE
// =========================================================

$page_title = "Data Desa";

?>


<?php include "../includes/header.php"; ?>

<?php include "../includes/navbar.php"; ?>


<!-- =====================================================
     HERO
===================================================== -->

<section
    class="py-5
           bg-light"
>

    <div class="container">

        <div class="text-center">

            <span
                class="badge
                       bg-primary-subtle
                       text-primary
                       px-3
                       py-2
                       mb-3"
            >

                PROFIL DESA

            </span>


            <h1 class="fw-bold">

                Data Desa Cisalada

            </h1>


            <p class="text-muted">

                Kecamatan
                <?= clean(
                    $data["district"] ?? "Cigombong"
                ) ?>,
                Kabupaten
                <?= clean(
                    $data["regency"] ?? "Bogor"
                ) ?>

            </p>


            <?php if (!empty($data["classification"])): ?>

                <span
                    class="badge bg-primary"
                >

                    <?= clean(
                        $data["classification"]
                    ) ?>

                </span>

            <?php endif; ?>

        </div>

    </div>

</section>


<!-- =====================================================
     PILIH TAHUN
===================================================== -->

<section class="py-4">

    <div class="container">

        <div
            class="card
                   border-0
                   shadow-sm"
        >

            <div class="card-body p-4">

                <div class="row align-items-center">

                    <div class="col-lg-7">

                        <h5 class="fw-bold mb-2">

                            <i
                                class="bi bi-calendar3
                                       text-primary
                                       me-2"
                            ></i>

                            Rekap Data Desa

                        </h5>

                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Pilih tahun untuk melihat data Desa Cisalada
                            berdasarkan rekap tahunan.

                        </p>

                    </div>


                    <div class="col-lg-5 mt-3 mt-lg-0">

                        <?php if (!empty($yearly_data)): ?>

                            <form
                                method="GET"
                                action=""
                            >

                                <div class="input-group">

                                    <span
                                        class="input-group-text"
                                    >

                                        Tahun

                                    </span>


                                    <select
                                        name="tahun"
                                        class="form-select"
                                        onchange="this.form.submit()"
                                    >

                                        <?php foreach (
                                            array_reverse(
                                                $yearly_data
                                            )
                                            as $year_option
                                        ): ?>

                                            <option
                                                value="<?= (int) $year_option["year"] ?>"
                                                <?= (
                                                    (int) $year_option["year"]
                                                    ===
                                                    (int) $selected_year
                                                )
                                                    ? "selected"
                                                    : ""
                                                ?>
                                            >

                                                <?= (int) $year_option["year"] ?>

                                                <?php if (
                                                    !empty(
                                                        $year_option[
                                                            "is_baseline"
                                                        ]
                                                    )
                                                ): ?>

                                                    - Baseline

                                                <?php endif; ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>


                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >

                                        Lihat

                                    </button>

                                </div>

                            </form>

                        <?php else: ?>

                            <div
                                class="alert
                                       alert-warning
                                       mb-0"
                            >

                                Data tahunan belum tersedia.

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     INFO TAHUN AKTIF
===================================================== -->

<section class="pb-2">

    <div class="container">

        <div
            class="alert
                   alert-primary
                   border-0
                   shadow-sm"
        >

            <div class="d-flex align-items-center">

                <i
                    class="bi bi-info-circle-fill
                           fs-4
                           me-3"
                ></i>

                <div>

                    <strong>
                        Data Tahun <?= (int) $selected_year ?>
                    </strong>

                    <br>

                    <small>

                        Statistik di bawah merupakan rekap data
                        Desa Cisalada pada tahun yang dipilih.

                    </small>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     STATISTIK UTAMA
===================================================== -->

<section class="py-5">

    <div class="container">

        <div class="row g-4">


            <!-- PENDUDUK -->

            <div
                class="col-md-6
                       col-lg-3"
            >

                <div
                    class="card
                           border-0
                           shadow-sm
                           h-100"
                >

                    <div
                        class="card-body
                               p-4"
                    >

                        <i
                            class="bi bi-people
                                   fs-1
                                   text-primary"
                        ></i>


                        <h2
                            class="fw-bold
                                   mt-3"
                        >

                            <?= number_format(
                                (int) $population,
                                0,
                                ",",
                                "."
                            ) ?>

                        </h2>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Total Penduduk

                        </p>

                    </div>

                </div>

            </div>


            <!-- KK -->

            <div
                class="col-md-6
                       col-lg-3"
            >

                <div
                    class="card
                           border-0
                           shadow-sm
                           h-100"
                >

                    <div
                        class="card-body
                               p-4"
                    >

                        <i
                            class="bi bi-house
                                   fs-1
                                   text-success"
                        ></i>


                        <h2
                            class="fw-bold
                                   mt-3"
                        >

                            <?= number_format(
                                (int) $family_count,
                                0,
                                ",",
                                "."
                            ) ?>

                        </h2>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Kepala Keluarga

                        </p>

                    </div>

                </div>

            </div>


            <!-- LUAS -->

            <div
                class="col-md-6
                       col-lg-3"
            >

                <div
                    class="card
                           border-0
                           shadow-sm
                           h-100"
                >

                    <div
                        class="card-body
                               p-4"
                    >

                        <i
                            class="bi bi-map
                                   fs-1
                                   text-warning"
                        ></i>


                        <h2
                            class="fw-bold
                                   mt-3"
                        >

                            <?= number_format(
                                (float) $area,
                                2,
                                ",",
                                "."
                            ) ?>

                        </h2>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Luas Wilayah (Ha)

                        </p>

                    </div>

                </div>

            </div>


            <!-- KEPADATAN -->

            <div
                class="col-md-6
                       col-lg-3"
            >

                <div
                    class="card
                           border-0
                           shadow-sm
                           h-100"
                >

                    <div
                        class="card-body
                               p-4"
                    >

                        <i
                            class="bi bi-bar-chart
                                   fs-1
                                   text-danger"
                        ></i>


                        <h2
                            class="fw-bold
                                   mt-3"
                        >

                            <?= number_format(
                                (float) $population_density,
                                2,
                                ",",
                                "."
                            ) ?>

                        </h2>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Jiwa / Ha

                        </p>

                    </div>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =====================================================
     WILAYAH ADMINISTRATIF
===================================================== -->

<section class="pb-5">

    <div class="container">

        <div
            class="card
                   border-0
                   shadow-sm"
        >

            <div class="card-body p-4">

                <h4 class="fw-bold mb-4">

                    <i
                        class="bi bi-diagram-3
                               text-primary
                               me-2"
                    ></i>

                    Wilayah Administratif

                </h4>


                <div class="row g-4">


                    <div class="col-md-4">

                        <div
                            class="border
                                   rounded
                                   p-3
                                   h-100"
                        >

                            <small
                                class="text-muted"
                            >

                                Dusun

                            </small>

                            <h4 class="fw-bold mb-0">

                                <?= number_format(
                                    (int) $hamlet_count,
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </h4>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div
                            class="border
                                   rounded
                                   p-3
                                   h-100"
                        >

                            <small
                                class="text-muted"
                            >

                                RW

                            </small>

                            <h4 class="fw-bold mb-0">

                                <?= number_format(
                                    (int) $rw_count,
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </h4>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div
                            class="border
                                   rounded
                                   p-3
                                   h-100"
                        >

                            <small
                                class="text-muted"
                            >

                                RT

                            </small>

                            <h4 class="fw-bold mb-0">

                                <?= number_format(
                                    (int) $rt_count,
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </h4>

                        </div>

                    </div>


                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     PERTUMBUHAN PENDUDUK
===================================================== -->

<section
    class="py-5
           bg-light"
>

    <div class="container">

        <div
            class="text-center
                   mb-5"
        >

            <h2 class="fw-bold">

                Pertumbuhan Penduduk

            </h2>

            <p class="text-muted">

                Perkembangan jumlah penduduk Desa Cisalada
                berdasarkan rekap data tahunan.

            </p>

        </div>


        <?php if (!empty($yearly_data)): ?>

            <div
                class="card
                       border-0
                       shadow-sm"
            >

                <div class="card-body p-4">

                    <canvas
                        id="populationGrowthChart"
                        height="120"
                    ></canvas>

                </div>

            </div>


            <!-- REKAP TAHUNAN -->

            <div class="mt-4">

                <div
                    class="card
                           border-0
                           shadow-sm"
                >

                    <div class="card-body p-4">

                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-center
                                   mb-4"
                        >

                            <h4 class="fw-bold mb-0">

                                <i
                                    class="bi bi-table
                                           text-primary
                                           me-2"
                                ></i>

                                Rekap Data Tahunan

                            </h4>

                            <span
                                class="badge
                                       bg-primary-subtle
                                       text-primary"
                            >

                                <?= count($yearly_data) ?> Tahun

                            </span>

                        </div>


                        <div
                            class="table-responsive"
                        >

                            <table
                                class="table
                                       table-hover
                                       align-middle"
                            >

                                <thead
                                    class="table-light"
                                >

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
                                            RT
                                        </th>

                                        <th>
                                            RW
                                        </th>

                                        <th>
                                            Luas (Ha)
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                        <th>
                                            Aksi
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php foreach (
                                        array_reverse(
                                            $yearly_data
                                        )
                                        as $row
                                    ): ?>

                                        <tr
                                            class="<?= (
                                                (int) $row["year"]
                                                ===
                                                (int) $selected_year
                                            )
                                                ? "table-primary"
                                                : ""
                                            ?>"
                                        >

                                            <td>

                                                <strong>

                                                    <?= (int) $row["year"] ?>

                                                </strong>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (int) (
                                                        $row[
                                                            "population"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    0,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (int) (
                                                        $row[
                                                            "male_population"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    0,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (int) (
                                                        $row[
                                                            "female_population"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    0,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (int) (
                                                        $row[
                                                            "family_count"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    0,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (int) (
                                                        $row[
                                                            "rt_count"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    0,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (int) (
                                                        $row[
                                                            "rw_count"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    0,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= number_format(
                                                    (float) (
                                                        $row[
                                                            "area"
                                                        ]
                                                        ??
                                                        0
                                                    ),
                                                    2,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </td>


                                            <td>

                                                <?php if (
                                                    !empty(
                                                        $row[
                                                            "is_baseline"
                                                        ]
                                                    )
                                                ): ?>

                                                    <span
                                                        class="badge
                                                               bg-success"
                                                    >

                                                        Baseline

                                                    </span>

                                                <?php else: ?>

                                                    <span
                                                        class="badge
                                                               bg-secondary"
                                                    >

                                                        Rekap

                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <a
                                                    href="?tahun=<?= (int) $row["year"] ?>"
                                                    class="btn
                                                           btn-sm
                                                           btn-outline-primary"
                                                >

                                                    <i
                                                        class="bi bi-eye
                                                               me-1"
                                                    ></i>

                                                    Lihat

                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        <?php else: ?>

            <div
                class="alert
                       alert-warning
                       text-center"
            >

                Belum ada data tahunan yang tersedia.

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- =====================================================
     JENIS KELAMIN
===================================================== -->

<section
    class="py-5
           bg-light"
>

    <div class="container">

        <div
            class="text-center
                   mb-5"
        >

            <h2 class="fw-bold">

                Komposisi Penduduk

            </h2>

            <p class="text-muted">

                Berdasarkan jenis kelamin tahun
                <?= (int) $selected_year ?>

            </p>

        </div>


        <div class="row justify-content-center">


            <!-- LAKI-LAKI -->

            <div class="col-md-5">

                <div
                    class="card
                           border-0
                           shadow-sm"
                >

                    <div
                        class="card-body
                               p-4
                               text-center"
                    >

                        <i
                            class="bi bi-gender-male
                                   fs-1
                                   text-primary"
                        ></i>


                        <h3
                            class="fw-bold
                                   mt-3"
                        >

                            <?= number_format(
                                (int) $male_population,
                                0,
                                ",",
                                "."
                            ) ?>

                        </h3>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Laki-laki

                        </p>

                    </div>

                </div>

            </div>


            <!-- PEREMPUAN -->

            <div class="col-md-5">

                <div
                    class="card
                           border-0
                           shadow-sm"
                >

                    <div
                        class="card-body
                               p-4
                               text-center"
                    >

                        <i
                            class="bi bi-gender-female
                                   fs-1
                                   text-danger"
                        ></i>


                        <h3
                            class="fw-bold
                                   mt-3"
                        >

                            <?= number_format(
                                (int) $female_population,
                                0,
                                ",",
                                "."
                            ) ?>

                        </h3>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Perempuan

                        </p>

                    </div>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =====================================================
     AGAMA + PERKAWINAN
===================================================== -->

<section class="py-5">

    <div class="container">

        <div class="row g-4">


            <!-- AGAMA -->

            <div class="col-lg-6">

                <div
                    class="card
                           border-0
                           shadow-sm
                           h-100"
                >

                    <div
                        class="card-body
                               p-4"
                    >

                        <h4
                            class="fw-bold
                                   mb-4"
                        >

                            <i
                                class="bi bi-moon-stars
                                       text-primary
                                       me-2"
                            ></i>

                            Agama

                        </h4>


                        <?php if (!empty($religions)): ?>

                            <canvas
                                id="religionChart"
                                height="280"
                            ></canvas>

                        <?php else: ?>

                            <div
                                class="alert
                                       alert-light
                                       border"
                            >

                                Data agama belum tersedia.

                            </div>

                        <?php endif; ?>


                    </div>

                </div>

            </div>


            <!-- PERKAWINAN -->

            <div class="col-lg-6">

                <div
                    class="card
                           border-0
                           shadow-sm
                           h-100"
                >

                    <div
                        class="card-body
                               p-4"
                    >

                        <h4
                            class="fw-bold
                                   mb-4"
                        >

                            <i
                                class="bi bi-heart
                                       text-danger
                                       me-2"
                            ></i>

                            Status Perkawinan

                        </h4>


                        <?php if (!empty($marital)): ?>

                            <canvas
                                id="maritalChart"
                                height="280"
                            ></canvas>

                        <?php else: ?>

                            <div
                                class="alert
                                       alert-light
                                       border"
                            >

                                Data status perkawinan belum tersedia.

                            </div>

                        <?php endif; ?>


                    </div>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =====================================================
     PENDIDIKAN
===================================================== -->

<section
    class="py-5
           bg-light"
>

    <div class="container">

        <div
            class="text-center
                   mb-5"
        >

            <h2 class="fw-bold">

                Pendidikan Penduduk

            </h2>

            <p class="text-muted">

                Distribusi penduduk berdasarkan
                tingkat pendidikan

            </p>

        </div>


        <div
            class="card
                   border-0
                   shadow-sm"
        >

            <div class="card-body p-4">

                <?php if (!empty($education)): ?>

                    <canvas
                        id="educationChart"
                        height="120"
                    ></canvas>

                <?php else: ?>

                    <div
                        class="alert
                               alert-light
                               border"
                    >

                        Data pendidikan belum tersedia.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     PEKERJAAN
===================================================== -->

<section class="py-5">

    <div class="container">

        <div
            class="text-center
                   mb-5"
        >

            <h2 class="fw-bold">

                Pekerjaan Penduduk

            </h2>

            <p class="text-muted">

                Distribusi berdasarkan pekerjaan

            </p>

        </div>


        <div
            class="card
                   border-0
                   shadow-sm"
        >

            <div class="card-body p-4">

                <?php if (!empty($occupations)): ?>

                    <canvas
                        id="occupationChart"
                        height="130"
                    ></canvas>

                <?php else: ?>

                    <div
                        class="alert
                               alert-light
                               border"
                    >

                        Data pekerjaan belum tersedia.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     USIA
===================================================== -->

<section
    class="py-5
           bg-light"
>

    <div class="container">

        <div
            class="text-center
                   mb-5"
        >

            <h2 class="fw-bold">

                Kelompok Usia Penduduk

            </h2>

            <p class="text-muted">

                Distribusi penduduk berdasarkan
                kelompok usia

            </p>

        </div>


        <div
            class="card
                   border-0
                   shadow-sm"
        >

            <div class="card-body p-4">

                <?php if (!empty($age_groups)): ?>

                    <canvas
                        id="ageChart"
                        height="140"
                    ></canvas>

                <?php else: ?>

                    <div
                        class="alert
                               alert-light
                               border"
                    >

                        Data kelompok usia belum tersedia.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     SUMBER DATA
===================================================== -->

<section class="py-4">

    <div class="container">

        <div
            class="alert
                   alert-light
                   border
                   text-center"
        >

            <i
                class="bi bi-info-circle
                       me-2"
            ></i>


            <strong>Sumber Data:</strong>

            <?php if (!empty($source)): ?>

                <?= clean($source) ?>

            <?php else: ?>

                Pemerintah Desa Cisalada

            <?php endif; ?>


            <?php if (!empty($selected_year_data["notes"])): ?>

                <br>

                <small
                    class="text-muted"
                >

                    <?= clean(
                        $selected_year_data["notes"]
                    ) ?>

                </small>

            <?php endif; ?>


            <br>

            <small
                class="text-muted"
            >

                Data ditampilkan berdasarkan
                rekap data Desa Cisalada
                tahun <?= (int) $selected_year ?>.

            </small>

        </div>

    </div>

</section>


<!-- =====================================================
     CHART.JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/chart.js"
></script>


<script>


// =====================================================
// PERTUMBUHAN PENDUDUK
// =====================================================

<?php if (!empty($yearly_data)): ?>

const yearLabels = <?= json_encode(
    $year_labels,
    JSON_UNESCAPED_UNICODE
) ?>;


const populationHistory = <?= json_encode(
    $population_history
) ?>;


const maleHistory = <?= json_encode(
    $male_history
) ?>;


const femaleHistory = <?= json_encode(
    $female_history
) ?>;


new Chart(

    document.getElementById(
        "populationGrowthChart"
    ),

    {

        type: "line",

        data: {

            labels: yearLabels,

            datasets: [

                {

                    label: "Total Penduduk",

                    data: populationHistory,

                    tension: 0.3,

                    fill: false

                },

                {

                    label: "Laki-laki",

                    data: maleHistory,

                    tension: 0.3,

                    fill: false

                },

                {

                    label: "Perempuan",

                    data: femaleHistory,

                    tension: 0.3,

                    fill: false

                }

            ]

        },

        options: {

            responsive: true,

            interaction: {

                mode: "index",

                intersect: false

            },

            plugins: {

                legend: {

                    position: "bottom"

                }

            },

            scales: {

                y: {

                    beginAtZero: true

                }

            }

        }

    }

);

<?php endif; ?>



// =====================================================
// AGAMA
// =====================================================

<?php if (!empty($religions)): ?>

const religionLabels = <?= json_encode(
    array_column(
        $religions,
        "religion_name"
    ),
    JSON_UNESCAPED_UNICODE
) ?>;


const religionData = <?= json_encode(
    array_map(
        "intval",
        array_column(
            $religions,
            "population"
        )
    )
) ?>;


new Chart(

    document.getElementById(
        "religionChart"
    ),

    {

        type: "doughnut",

        data: {

            labels: religionLabels,

            datasets: [{

                data: religionData

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    position: "bottom"

                }

            }

        }

    }

);

<?php endif; ?>



// =====================================================
// STATUS PERKAWINAN
// =====================================================

<?php if (!empty($marital)): ?>

const maritalLabels = <?= json_encode(
    array_column(
        $marital,
        "status_name"
    ),
    JSON_UNESCAPED_UNICODE
) ?>;


const maritalData = <?= json_encode(
    array_map(
        "intval",
        array_column(
            $marital,
            "population"
        )
    )
) ?>;


new Chart(

    document.getElementById(
        "maritalChart"
    ),

    {

        type: "doughnut",

        data: {

            labels: maritalLabels,

            datasets: [{

                data: maritalData

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    position: "bottom"

                }

            }

        }

    }

);

<?php endif; ?>



// =====================================================
// PENDIDIKAN
// =====================================================

<?php if (!empty($education)): ?>

const educationLabels = <?= json_encode(
    array_column(
        $education,
        "education_name"
    ),
    JSON_UNESCAPED_UNICODE
) ?>;


const educationData = <?= json_encode(
    array_map(
        "intval",
        array_column(
            $education,
            "total_population"
        )
    )
) ?>;


new Chart(

    document.getElementById(
        "educationChart"
    ),

    {

        type: "bar",

        data: {

            labels: educationLabels,

            datasets: [{

                label: "Jumlah Penduduk",

                data: educationData

            }]

        },

        options: {

            responsive: true,

            indexAxis: "y",

            plugins: {

                legend: {

                    display: false

                }

            },

            scales: {

                x: {

                    beginAtZero: true

                }

            }

        }

    }

);

<?php endif; ?>



// =====================================================
// PEKERJAAN
// =====================================================

<?php if (!empty($occupations)): ?>

const occupationLabels = <?= json_encode(
    array_column(
        $occupations,
        "occupation_name"
    ),
    JSON_UNESCAPED_UNICODE
) ?>;


const occupationData = <?= json_encode(
    array_map(
        "intval",
        array_column(
            $occupations,
            "total_population"
        )
    )
) ?>;


new Chart(

    document.getElementById(
        "occupationChart"
    ),

    {

        type: "bar",

        data: {

            labels: occupationLabels,

            datasets: [{

                label: "Jumlah Penduduk",

                data: occupationData

            }]

        },

        options: {

            responsive: true,

            indexAxis: "y",

            plugins: {

                legend: {

                    display: false

                }

            },

            scales: {

                x: {

                    beginAtZero: true

                }

            }

        }

    }

);

<?php endif; ?>



// =====================================================
// USIA
// =====================================================

<?php if (!empty($age_groups)): ?>

const ageLabels = <?= json_encode(
    array_column(
        $age_groups,
        "age_group"
    ),
    JSON_UNESCAPED_UNICODE
) ?>;


const ageMale = <?= json_encode(
    array_map(
        "intval",
        array_column(
            $age_groups,
            "male_population"
        )
    )
) ?>;


const ageFemale = <?= json_encode(
    array_map(
        "intval",
        array_column(
            $age_groups,
            "female_population"
        )
    )
) ?>;


new Chart(

    document.getElementById(
        "ageChart"
    ),

    {

        type: "bar",

        data: {

            labels: ageLabels,

            datasets: [

                {

                    label: "Laki-laki",

                    data: ageMale

                },

                {

                    label: "Perempuan",

                    data: ageFemale

                }

            ]

        },

        options: {

            responsive: true,

            scales: {

                x: {

                    stacked: false

                },

                y: {

                    beginAtZero: true

                }

            }

        }

    }

);

<?php endif; ?>


</script>


<?php include "../includes/footer.php"; ?>