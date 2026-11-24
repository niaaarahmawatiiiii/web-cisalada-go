<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// DEFAULT
// =========================================================

$error = "";

$current_year = (int) date("Y");


// =========================================================
// PROSES FORM
// =========================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =====================================================
    // AMBIL INPUT
    // =====================================================

    $year = (int) ($_POST["year"] ?? 0);

    $population = trim(
        $_POST["population"] ?? ""
    );

    $male_population = trim(
        $_POST["male_population"] ?? ""
    );

    $female_population = trim(
        $_POST["female_population"] ?? ""
    );

    $family_count = trim(
        $_POST["family_count"] ?? ""
    );

    $hamlet_count = trim(
        $_POST["hamlet_count"] ?? ""
    );

    $rw_count = trim(
        $_POST["rw_count"] ?? ""
    );

    $rt_count = trim(
        $_POST["rt_count"] ?? ""
    );

    $area = trim(
        $_POST["area"] ?? ""
    );

    $source = trim(
        $_POST["source"] ?? ""
    );

    $notes = trim(
        $_POST["notes"] ?? ""
    );


    // =====================================================
    // KONVERSI KOSONG MENJADI NULL
    // =====================================================

    $population =
        $population === ""
            ? null
            : (int) $population;

    $male_population =
        $male_population === ""
            ? null
            : (int) $male_population;

    $female_population =
        $female_population === ""
            ? null
            : (int) $female_population;

    $family_count =
        $family_count === ""
            ? null
            : (int) $family_count;

    $hamlet_count =
        $hamlet_count === ""
            ? null
            : (int) $hamlet_count;

    $rw_count =
        $rw_count === ""
            ? null
            : (int) $rw_count;

    $rt_count =
        $rt_count === ""
            ? null
            : (int) $rt_count;

    $area =
        $area === ""
            ? null
            : (float) $area;


    // =====================================================
    // VALIDASI TAHUN
    // =====================================================

    if (
        $year < 2000 ||
        $year > 2100
    ) {

        $error =
            "Tahun harus berada antara 2000 sampai 2100.";

    }


    // =====================================================
    // CEK TAHUN SUDAH ADA
    // =====================================================

    if (empty($error)) {

        $check = mysqli_prepare(
            $conn,
            "
            SELECT id
            FROM village_yearly_data
            WHERE year = ?
            LIMIT 1
            "
        );

        mysqli_stmt_bind_param(
            $check,
            "i",
            $year
        );

        mysqli_stmt_execute($check);

        mysqli_stmt_store_result($check);

        if (
            mysqli_stmt_num_rows($check) > 0
        ) {

            $error =
                "Data untuk tahun "
                . $year
                . " sudah tersedia.";

        }

        mysqli_stmt_close($check);
    }


    // =====================================================
    // VALIDASI PENDUDUK
    // =====================================================

    if (
        empty($error) &&
        $population !== null &&
        $population < 0
    ) {

        $error =
            "Jumlah penduduk tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $male_population !== null &&
        $male_population < 0
    ) {

        $error =
            "Jumlah laki-laki tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $female_population !== null &&
        $female_population < 0
    ) {

        $error =
            "Jumlah perempuan tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $family_count !== null &&
        $family_count < 0
    ) {

        $error =
            "Jumlah kepala keluarga tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $hamlet_count !== null &&
        $hamlet_count < 0
    ) {

        $error =
            "Jumlah dusun tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $rw_count !== null &&
        $rw_count < 0
    ) {

        $error =
            "Jumlah RW tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $rt_count !== null &&
        $rt_count < 0
    ) {

        $error =
            "Jumlah RT tidak boleh negatif.";

    }


    if (
        empty($error) &&
        $area !== null &&
        $area < 0
    ) {

        $error =
            "Luas wilayah tidak boleh negatif.";

    }


    // =====================================================
    // VALIDASI LAKI + PEREMPUAN
    // =====================================================

    if (
        empty($error) &&
        $population !== null &&
        $male_population !== null &&
        $female_population !== null
    ) {

        $gender_total =
            $male_population +
            $female_population;


        if (
            $gender_total !== $population
        ) {

            $error =
                "Jumlah laki-laki + perempuan harus sama "
                . "dengan jumlah penduduk.";

        }

    }


    // =====================================================
    // HITUNG KEPADATAN
    // =====================================================

    $population_density = null;


    if (
        $population !== null &&
        $area !== null &&
        $area > 0
    ) {

        $population_density =
            round(
                $population / $area,
                2
            );

    }


    // =====================================================
    // SIMPAN DATA
    // =====================================================

    if (empty($error)) {

        $stmt = mysqli_prepare(
            $conn,
            "
            INSERT INTO village_yearly_data
            (
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
                is_baseline
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                0
            )
            "
        );


        /*
        =====================================================
        URUTAN TIPE DATA

        year                = i
        population          = i
        male_population     = i
        female_population   = i
        family_count        = i
        hamlet_count        = i
        rw_count            = i
        rt_count            = i
        area                = d
        population_density  = d
        source              = s
        notes               = s

        TOTAL:
        8 integer
        2 decimal
        2 string

        Jadi:
        iiiiii iiddss
        atau:
        iiiiiiiddss
        =====================================================
        */

        mysqli_stmt_bind_param(
            $stmt,
            "iiiiiiiiddss",
            $year,
            $population,
            $male_population,
            $female_population,
            $family_count,
            $hamlet_count,
            $rw_count,
            $rt_count,
            $area,
            $population_density,
            $source,
            $notes
        );


        // =================================================
        // EXECUTE
        // =================================================

        if (
            mysqli_stmt_execute($stmt)
        ) {

            $new_id =
                mysqli_insert_id($conn);


            mysqli_stmt_close($stmt);


            // =============================================
            // CATAT RIWAYAT
            // =============================================

            $history = mysqli_prepare(
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
                    'CREATE',
                    ?
                )
                "
            );


            $history_description =
                "Membuat rekap data desa tahun "
                . $year
                . ".";


            mysqli_stmt_bind_param(
                $history,
                "is",
                $new_id,
                $history_description
            );


            mysqli_stmt_execute($history);

            mysqli_stmt_close($history);


            // =============================================
            // REDIRECT KE DETAIL
            // =============================================

            header(
                "Location: detail.php?id="
                . $new_id
            );

            exit;

        } else {

            $error =
                "Gagal menyimpan data: "
                . mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);

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
        Tambah Data Tahunan - Cisalada GO
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


        .section-title {

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 18px;

        }


        .form-label {

            font-weight: 600;

        }


        .required {

            color: #dc3545;

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

</nav>



<!-- =====================================================
     CONTENT
===================================================== -->

<div class="container py-5">


    <div class="row justify-content-center">


        <div class="col-xl-9">


            <div class="card shadow-sm">


                <div class="card-body p-4 p-lg-5">


                    <!-- =================================
                         HEADER
                    ================================== -->

                    <div class="mb-4">


                        <h2
                            class="fw-bold mb-2"
                        >

                            Tambah Data Tahunan

                        </h2>


                        <p
                            class="text-muted mb-0"
                        >

                            Tambahkan rekap Data Desa
                            untuk satu tahun tertentu.

                            Data tahun sebelumnya
                            tidak akan berubah.

                        </p>


                    </div>



                    <!-- =================================
                         ERROR
                    ================================== -->

                    <?php if (
                        !empty($error)
                    ): ?>


                        <div
                            class="alert alert-danger"
                        >

                            <i
                                class="bi bi-exclamation-circle-fill me-2"
                            ></i>

                            <?= clean($error) ?>

                        </div>


                    <?php endif; ?>



                    <!-- =================================
                         FORM
                    ================================== -->

                    <form method="POST">


                        <!-- ==============================
                             TAHUN
                        =============================== -->

                        <div class="section-title">

                            <i
                                class="bi bi-calendar3
                                       text-primary
                                       me-2"
                            ></i>

                            Informasi Tahun

                        </div>


                        <div class="row g-3 mb-5">


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Tahun

                                    <span
                                        class="required"
                                    >
                                        *
                                    </span>

                                </label>


                                <input
                                    type="number"
                                    name="year"
                                    class="form-control"
                                    min="2000"
                                    max="2100"
                                    value="<?= isset(
                                        $_POST["year"]
                                    )
                                        ? clean(
                                            $_POST["year"]
                                        )
                                        : $current_year ?>"
                                    required
                                >


                                <div
                                    class="form-text"
                                >

                                    Contoh:
                                    2025, 2026, 2027.

                                </div>


                            </div>


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Sumber Data

                                </label>


                                <input
                                    type="text"
                                    name="source"
                                    class="form-control"
                                    value="<?= isset(
                                        $_POST["source"]
                                    )
                                        ? clean(
                                            $_POST["source"]
                                        )
                                        : "" ?>"
                                    placeholder="Contoh: Pemerintah Desa Cisalada"
                                >


                            </div>


                        </div>



                        <!-- ==============================
                             KEPENDUDUKAN
                        =============================== -->

                        <div class="section-title">

                            <i
                                class="bi bi-people-fill
                                       text-primary
                                       me-2"
                            ></i>

                            Data Kependudukan

                        </div>


                        <div class="row g-3 mb-5">


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Jumlah Penduduk

                                </label>


                                <input
                                    type="number"
                                    name="population"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST["population"]
                                    )
                                        ? clean(
                                            $_POST["population"]
                                        )
                                        : "" ?>"
                                    placeholder="Contoh: 8287"
                                >


                            </div>


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Laki-laki

                                </label>


                                <input
                                    type="number"
                                    name="male_population"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST[
                                            "male_population"
                                        ]
                                    )
                                        ? clean(
                                            $_POST[
                                                "male_population"
                                            ]
                                        )
                                        : "" ?>"
                                    placeholder="Contoh: 4260"
                                >


                            </div>


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Perempuan

                                </label>


                                <input
                                    type="number"
                                    name="female_population"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST[
                                            "female_population"
                                        ]
                                    )
                                        ? clean(
                                            $_POST[
                                                "female_population"
                                            ]
                                        )
                                        : "" ?>"
                                    placeholder="Contoh: 4027"
                                >


                            </div>


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Kepala Keluarga

                                </label>


                                <input
                                    type="number"
                                    name="family_count"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST[
                                            "family_count"
                                        ]
                                    )
                                        ? clean(
                                            $_POST[
                                                "family_count"
                                            ]
                                        )
                                        : "" ?>"
                                    placeholder="Contoh: 2580"
                                >


                            </div>


                        </div>



                        <!-- ==============================
                             WILAYAH
                        =============================== -->

                        <div class="section-title">

                            <i
                                class="bi bi-geo-alt-fill
                                       text-primary
                                       me-2"
                            ></i>

                            Data Wilayah

                        </div>


                        <div class="row g-3 mb-5">


                            <div class="col-md-4">


                                <label
                                    class="form-label"
                                >

                                    Dusun

                                </label>


                                <input
                                    type="number"
                                    name="hamlet_count"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST[
                                            "hamlet_count"
                                        ]
                                    )
                                        ? clean(
                                            $_POST[
                                                "hamlet_count"
                                            ]
                                        )
                                        : "" ?>"
                                    placeholder="Belum tersedia"
                                >


                            </div>


                            <div class="col-md-4">


                                <label
                                    class="form-label"
                                >

                                    RW

                                </label>


                                <input
                                    type="number"
                                    name="rw_count"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST[
                                            "rw_count"
                                        ]
                                    )
                                        ? clean(
                                            $_POST[
                                                "rw_count"
                                            ]
                                        )
                                        : "" ?>"
                                    placeholder="Belum tersedia"
                                >


                            </div>


                            <div class="col-md-4">


                                <label
                                    class="form-label"
                                >

                                    RT

                                </label>


                                <input
                                    type="number"
                                    name="rt_count"
                                    class="form-control"
                                    min="0"
                                    value="<?= isset(
                                        $_POST[
                                            "rt_count"
                                        ]
                                    )
                                        ? clean(
                                            $_POST[
                                                "rt_count"
                                            ]
                                        )
                                        : "" ?>"
                                    placeholder="Belum tersedia"
                                >


                            </div>


                            <div class="col-md-6">


                                <label
                                    class="form-label"
                                >

                                    Luas Wilayah (Ha)

                                </label>


                                <input
                                    type="number"
                                    name="area"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= isset(
                                        $_POST["area"]
                                    )
                                        ? clean(
                                            $_POST["area"]
                                        )
                                        : "214.18" ?>"
                                    placeholder="Contoh: 214.18"
                                >


                                <div
                                    class="form-text"
                                >

                                    Luas wilayah Desa
                                    Cisalada:
                                    214,18 Ha.

                                </div>


                            </div>


                        </div>



                        <!-- ==============================
                             CATATAN
                        =============================== -->

                        <div class="section-title">

                            <i
                                class="bi bi-journal-text
                                       text-primary
                                       me-2"
                            ></i>

                            Catatan

                        </div>


                        <div class="mb-4">


                            <label
                                class="form-label"
                            >

                                Catatan / Keterangan

                            </label>


                            <textarea
                                name="notes"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan catatan atau keterangan sumber data..."
                            ><?= isset(
                                $_POST["notes"]
                            )
                                ? clean(
                                    $_POST["notes"]
                                )
                                : "" ?></textarea>


                        </div>



                        <!-- ==============================
                             INFORMASI
                        =============================== -->

                        <div
                            class="alert
                                   alert-warning
                                   border-0"
                        >

                            <i
                                class="bi bi-exclamation-triangle me-2"
                            ></i>


                            <strong>Perhatian:</strong>

                            Kosongkan data yang belum
                            tersedia.

                            Jangan masukkan angka
                            <strong>0</strong>
                            jika data sebenarnya
                            belum diketahui.

                        </div>



                        <!-- ==============================
                             BUTTON
                        =============================== -->

                        <div
                            class="d-flex
                                   justify-content-end
                                   gap-2
                                   mt-4"
                        >


                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >

                                <i
                                    class="bi bi-arrow-left me-1"
                                ></i>

                                Batal

                            </a>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i
                                    class="bi bi-save me-1"
                                ></i>

                                Simpan Data Tahun

                            </button>


                        </div>


                    </form>


                </div>

            </div>


        </div>

    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>