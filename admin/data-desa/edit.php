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


// =========================================================
// VALIDASI ID
// =========================================================

if ($id <= 0) {

    header("Location: index.php");

    exit;

}


// =========================================================
// AMBIL DATA AWAL
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


$data = mysqli_fetch_assoc(
    $result
);


mysqli_stmt_close(
    $stmt
);


// =========================================================
// DATA TIDAK DITEMUKAN
// =========================================================

if (!$data) {

    header("Location: index.php");

    exit;

}


// =========================================================
// DEFAULT ERROR
// =========================================================

$error = "";


// =========================================================
// PROSES FORM
// =========================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {


    // =====================================================
    // AMBIL INPUT
    // =====================================================

    $population =
        trim(
            $_POST["population"] ?? ""
        );


    $male_population =
        trim(
            $_POST["male_population"] ?? ""
        );


    $female_population =
        trim(
            $_POST["female_population"] ?? ""
        );


    $family_count =
        trim(
            $_POST["family_count"] ?? ""
        );


    $hamlet_count =
        trim(
            $_POST["hamlet_count"] ?? ""
        );


    $rw_count =
        trim(
            $_POST["rw_count"] ?? ""
        );


    $rt_count =
        trim(
            $_POST["rt_count"] ?? ""
        );


    $area =
        trim(
            $_POST["area"] ?? ""
        );


    $source =
        trim(
            $_POST["source"] ?? ""
        );


    $notes =
        trim(
            $_POST["notes"] ?? ""
        );


    // =====================================================
    // KONVERSI KOSONG → NULL
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
    // VALIDASI NEGATIF
    // =====================================================

    if (
        $population !== null
        &&
        $population < 0
    ) {

        $error =
            "Jumlah penduduk tidak boleh negatif.";

    }


    if (
        empty($error)
        &&
        $male_population !== null
        &&
        $male_population < 0
    ) {

        $error =
            "Jumlah laki-laki tidak boleh negatif.";

    }


    if (
        empty($error)
        &&
        $female_population !== null
        &&
        $female_population < 0
    ) {

        $error =
            "Jumlah perempuan tidak boleh negatif.";

    }


    if (
        empty($error)
        &&
        $family_count !== null
        &&
        $family_count < 0
    ) {

        $error =
            "Jumlah kepala keluarga tidak boleh negatif.";

    }


    if (
        empty($error)
        &&
        $hamlet_count !== null
        &&
        $hamlet_count < 0
    ) {

        $error =
            "Jumlah dusun tidak boleh negatif.";

    }


    if (
        empty($error)
        &&
        $rw_count !== null
        &&
        $rw_count < 0
    ) {

        $error =
            "Jumlah RW tidak boleh negatif.";

    }


    if (
        empty($error)
        &&
        $rt_count !== null
        &&
        $rt_count < 0
    ) {

        $error =
            "Jumlah RT tidak boleh negatif.";

    }


    // =====================================================
    // VALIDASI JENIS KELAMIN
    // =====================================================

    if (
        empty($error)
        &&
        $population !== null
        &&
        $male_population !== null
        &&
        $female_population !== null
    ) {


        $gender_total =
            $male_population
            +
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
        $population !== null
        &&
        $area !== null
        &&
        $area > 0
    ) {

        $population_density =
            round(
                $population / $area,
                2
            );

    }


    // =====================================================
    // UPDATE
    // =====================================================

    if (
        empty($error)
    ) {


        $stmt_update =
            mysqli_prepare(
                $conn,
                "
                UPDATE village_yearly_data

                SET
                    population = ?,
                    male_population = ?,
                    female_population = ?,
                    family_count = ?,
                    hamlet_count = ?,
                    rw_count = ?,
                    rt_count = ?,
                    area = ?,
                    population_density = ?,
                    source = ?,
                    notes = ?

                WHERE id = ?
                "
            );


        mysqli_stmt_bind_param(
            $stmt_update,
            "iiiiiiiddssi",
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
            $notes,
            $id
        );


        if (
            mysqli_stmt_execute(
                $stmt_update
            )
        ) {


            mysqli_stmt_close(
                $stmt_update
            );


            // =============================================
            // CATAT RIWAYAT
            // =============================================

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
                "Memperbarui rekap data desa tahun "
                . $data["year"]
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


            // =============================================
            // REDIRECT
            // =============================================

            header(
                "Location: detail.php?id="
                . $id
            );

            exit;


        } else {


            $error =
                "Gagal memperbarui data: "
                . mysqli_stmt_error(
                    $stmt_update
                );


            mysqli_stmt_close(
                $stmt_update
            );

        }

    }


} else {


    // =====================================================
    // TAMPILKAN DATA LAMA
    // =====================================================

    $population =
        $data["population"];


    $male_population =
        $data["male_population"];


    $female_population =
        $data["female_population"];


    $family_count =
        $data["family_count"];


    $hamlet_count =
        $data["hamlet_count"];


    $rw_count =
        $data["rw_count"];


    $rt_count =
        $data["rt_count"];


    $area =
        $data["area"];


    $source =
        $data["source"];


    $notes =
        $data["notes"];

}


// =========================================================
// FUNGSI NILAI FORM
// =========================================================

function formValue($value)
{

    if (
        $value === null
    ) {

        return "";

    }


    return clean(
        $value
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
        Edit Data Tahun <?= (int) $data["year"] ?>
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


        .section-title {

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 18px;

        }


        .form-label {

            font-weight: 600;

        }


        .baseline-info {

            background: #eaf7ee;

            border: 1px solid #b9e2c5;

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


            <div
                class="card
                       shadow-sm"
            >


                <div class="card-body p-4 p-lg-5">


                    <!-- =================================
                         HEADER
                    ================================== -->

                    <div
                        class="d-flex
                               flex-wrap
                               justify-content-between
                               align-items-start
                               gap-3
                               mb-4"
                    >


                        <div>


                            <h2
                                class="fw-bold
                                       mb-2"
                            >

                                Edit Data Desa

                            </h2>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Mengubah data rekap tahun
                                <?= (int) $data["year"] ?>.

                            </p>


                        </div>


                        <?php if (
                            (int)
                            $data["is_baseline"]
                            === 1
                        ): ?>


                            <span
                                class="badge
                                       bg-success
                                       fs-6
                                       px-3
                                       py-2"
                            >

                                <i
                                    class="bi bi-database-check me-1"
                                ></i>

                                Baseline <?= (int) $data["year"] ?>

                            </span>


                        <?php endif; ?>


                    </div>



                    <!-- =================================
                         INFO BASELINE
                    ================================== -->

                    <?php if (
                        (int)
                        $data["is_baseline"]
                        === 1
                    ): ?>


                        <div
                            class="alert
                                   baseline-info
                                   border-0
                                   mb-4"
                        >

                            <i
                                class="bi bi-info-circle-fill me-2"
                            ></i>

                            Data ini merupakan
                            <strong>baseline tahun
                            <?= (int) $data["year"] ?></strong>.
                            Perubahan hanya akan memperbarui
                            data tahun ini dan tidak akan
                            menghapus riwayat tahun lainnya.


                        </div>


                    <?php endif; ?>



                    <!-- =================================
                         ERROR
                    ================================== -->

                    <?php if (
                        !empty($error)
                    ): ?>


                        <div
                            class="alert
                                   alert-danger"
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

                    <form
                        method="POST"
                    >


                        <!-- ==============================
                             TAHUN
                        =============================== -->

                        <div
                            class="section-title"
                        >

                            <i
                                class="bi bi-calendar3
                                       text-primary
                                       me-2"
                            ></i>

                            Periode Data

                        </div>


                        <div class="row g-3 mb-5">


                            <div
                                class="col-md-6"
                            >


                                <label
                                    class="form-label"
                                >

                                    Tahun

                                </label>


                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= (int) $data["year"] ?>"
                                    readonly
                                >


                                <div
                                    class="form-text"
                                >

                                    Tahun tidak dapat
                                    diubah dari halaman ini.

                                </div>


                            </div>


                            <div
                                class="col-md-6"
                            >


                                <label
                                    class="form-label"
                                >

                                    Sumber Data

                                </label>


                                <input
                                    type="text"
                                    name="source"
                                    class="form-control"
                                    value="<?= formValue(
                                        $source
                                    ) ?>"
                                    placeholder="Contoh: Pemerintah Desa Cisalada"
                                >


                            </div>


                        </div>



                        <!-- ==============================
                             KEPENDUDUKAN
                        =============================== -->

                        <div
                            class="section-title"
                        >

                            <i
                                class="bi bi-people-fill
                                       text-primary
                                       me-2"
                            ></i>

                            Data Kependudukan

                        </div>


                        <div class="row g-3 mb-5">


                            <div
                                class="col-md-6"
                            >


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
                                    value="<?= formValue(
                                        $population
                                    ) ?>"
                                >


                            </div>


                            <div
                                class="col-md-6"
                            >


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
                                    value="<?= formValue(
                                        $male_population
                                    ) ?>"
                                >


                            </div>


                            <div
                                class="col-md-6"
                            >


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
                                    value="<?= formValue(
                                        $female_population
                                    ) ?>"
                                >


                            </div>


                            <div
                                class="col-md-6"
                            >


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
                                    value="<?= formValue(
                                        $family_count
                                    ) ?>"
                                >


                            </div>


                        </div>



                        <!-- ==============================
                             WILAYAH
                        =============================== -->

                        <div
                            class="section-title"
                        >

                            <i
                                class="bi bi-geo-alt-fill
                                       text-primary
                                       me-2"
                            ></i>

                            Data Wilayah

                        </div>


                        <div class="row g-3 mb-5">


                            <div
                                class="col-md-4"
                            >


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
                                    value="<?= formValue(
                                        $hamlet_count
                                    ) ?>"
                                    placeholder="Belum tersedia"
                                >


                            </div>


                            <div
                                class="col-md-4"
                            >


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
                                    value="<?= formValue(
                                        $rw_count
                                    ) ?>"
                                    placeholder="Belum tersedia"
                                >


                            </div>


                            <div
                                class="col-md-4"
                            >


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
                                    value="<?= formValue(
                                        $rt_count
                                    ) ?>"
                                    placeholder="Belum tersedia"
                                >


                            </div>


                            <div
                                class="col-md-6"
                            >


                                <label
                                    class="form-label"
                                >

                                    Luas Wilayah
                                    (Ha)

                                </label>


                                <input
                                    type="number"
                                    name="area"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= formValue(
                                        $area
                                    ) ?>"
                                >


                            </div>


                        </div>



                        <!-- ==============================
                             CATATAN
                        =============================== -->

                        <div
                            class="section-title"
                        >

                            <i
                                class="bi bi-journal-text
                                       text-primary
                                       me-2"
                            ></i>

                            Catatan

                        </div>


                        <div class="mb-4">


                            <textarea
                                name="notes"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan catatan atau keterangan..."
                            ><?= formValue(
                                $notes
                            ) ?></textarea>


                        </div>



                        <!-- ==============================
                             PERINGATAN
                        =============================== -->

                        <div
                            class="alert
                                   alert-warning
                                   border-0"
                        >

                            <i
                                class="bi bi-exclamation-triangle-fill me-2"
                            ></i>

                            Perubahan hanya berlaku untuk
                            data tahun
                            <strong>
                                <?= (int) $data["year"] ?>
                            </strong>.

                            Data tahun lain tetap tersimpan.

                        </div>



                        <!-- ==============================
                             BUTTON
                        =============================== -->

                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-center
                                   mt-4"
                        >


                            <a
                                href="detail.php?id=<?= (int) $data["id"] ?>"
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

                                Simpan Perubahan

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