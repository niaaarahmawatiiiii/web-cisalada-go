<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// SEARCH
// =========================================================

$search = trim(
    $_GET["search"] ?? ""
);


// =========================================================
// FILTER STATUS
// =========================================================

$status = trim(
    $_GET["status"] ?? "Aktif"
);


// =========================================================
// QUERY DATA WARGA
// =========================================================

$sql = "
    SELECT *
    FROM residents
    WHERE 1 = 1
";


$params = [];

$types = "";


// =========================================================
// SEARCH
// =========================================================

if ($search !== "") {

    $sql .= "
        AND (
            name LIKE ?
            OR nik LIKE ?
            OR family_card_number LIKE ?
        )
    ";

    $keyword = "%" . $search . "%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;

    $types .= "sss";

}


// =========================================================
// FILTER STATUS
// =========================================================

if (
    in_array(
        $status,
        [
            "Aktif",
            "Pindah Keluar",
            "Meninggal"
        ]
    )
) {

    $sql .= "
        AND population_status = ?
    ";

    $params[] = $status;

    $types .= "s";

}


// =========================================================
// ORDER
// =========================================================

$sql .= "
    ORDER BY name ASC
";


// =========================================================
// PREPARE
// =========================================================

$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!$stmt) {

    die(
        "Gagal menyiapkan query: "
        . mysqli_error($conn)
    );

}


// =========================================================
// BIND PARAMETER
// =========================================================

if (
    count($params) > 0
) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );

}


// =========================================================
// EXECUTE
// =========================================================

mysqli_stmt_execute(
    $stmt
);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


// =========================================================
// STATISTIK WARGA AKTIF
// =========================================================

$stat_result = mysqli_query(
    $conn,
    "
    SELECT

        COUNT(*) AS total,

        SUM(
            gender = 'Laki-laki'
        ) AS male,

        SUM(
            gender = 'Perempuan'
        ) AS female

    FROM residents

    WHERE population_status = 'Aktif'
    "
);


$stat =
    mysqli_fetch_assoc(
        $stat_result
    );


$total_residents =
    (int)(
        $stat["total"] ?? 0
    );


$total_male =
    (int)(
        $stat["male"] ?? 0
    );


$total_female =
    (int)(
        $stat["female"] ?? 0
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
        Data Penduduk - Cisalada GO
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

            border-radius: 14px;

        }


        .stat-card {

            transition: .2s;

        }


        .stat-card:hover {

            transform: translateY(-3px);

        }


        .table {

            vertical-align: middle;

        }


        .badge-status {

            padding: 7px 12px;

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

                <i class="bi bi-speedometer2 me-1"></i>

                Dashboard

            </a>


            <a
                href="<?= BASE_URL ?>admin/logout.php"
                class="btn btn-outline-light btn-sm"
                onclick="return confirm('Yakin ingin logout?');"
            >

                <i class="bi bi-box-arrow-right me-1"></i>

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
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>

            <h2 class="fw-bold mb-1">

                Data Penduduk

            </h2>


            <p class="text-muted mb-0">

                Kelola data kependudukan
                Desa Cisalada.

            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i class="bi bi-person-plus me-1"></i>

            Tambah Warga

        </a>


    </div>



    <!-- =================================================
         INFO
    ================================================= -->

    <div
        class="alert
               alert-info
               border-0
               shadow-sm"
    >

        <i class="bi bi-info-circle me-2"></i>

        Data warga dikelola oleh administrator
        dan digunakan sebagai dasar pembaruan
        statistik kependudukan.

    </div>



    <!-- =================================================
         STATISTIK
    ================================================= -->

    <div class="row g-4 mb-4">


        <!-- TOTAL -->

        <div
            class="col-md-4"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body
                           p-4"
                >


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

                                Warga Aktif

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?= number_format(
                                    $total_residents,
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </h2>

                        </div>


                        <i
                            class="bi bi-people
                                   fs-1
                                   text-primary"
                        ></i>

                    </div>


                </div>

            </div>

        </div>



        <!-- LAKI -->

        <div
            class="col-md-4"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body
                           p-4"
                >


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

                                Laki-laki

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?= number_format(
                                    $total_male,
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </h2>

                        </div>


                        <i
                            class="bi bi-gender-male
                                   fs-1
                                   text-primary"
                        ></i>

                    </div>


                </div>

            </div>

        </div>



        <!-- PEREMPUAN -->

        <div
            class="col-md-4"
        >

            <div
                class="card
                       stat-card
                       shadow-sm
                       h-100"
            >

                <div
                    class="card-body
                           p-4"
                >


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

                                Perempuan

                            </p>


                            <h2
                                class="fw-bold
                                       mb-0"
                            >

                                <?= number_format(
                                    $total_female,
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </h2>

                        </div>


                        <i
                            class="bi bi-gender-female
                                   fs-1
                                   text-danger"
                        ></i>

                    </div>


                </div>

            </div>

        </div>


    </div>



    <!-- =================================================
         FILTER
    ================================================= -->

    <div
        class="card
               shadow-sm
               mb-4"
    >

        <div class="card-body p-4">


            <form
                method="GET"
                class="row g-3"
            >


                <div class="col-md-6">


                    <label
                        class="form-label fw-semibold"
                    >

                        Cari Warga

                    </label>


                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="<?= clean($search) ?>"
                        placeholder="Nama atau NIK..."
                    >


                </div>



                <div class="col-md-4">


                    <label
                        class="form-label fw-semibold"
                    >

                        Status

                    </label>


                    <select
                        name="status"
                        class="form-select"
                    >

                        <option
                            value="Aktif"
                            <?= $status === "Aktif"
                                ? "selected"
                                : "" ?>
                        >

                            Aktif

                        </option>


                        <option
                            value="Pindah Keluar"
                            <?= $status === "Pindah Keluar"
                                ? "selected"
                                : "" ?>
                        >

                            Pindah Keluar

                        </option>


                        <option
                            value="Meninggal"
                            <?= $status === "Meninggal"
                                ? "selected"
                                : "" ?>
                        >

                            Meninggal

                        </option>

                    </select>


                </div>



                <div
                    class="col-md-2
                           d-flex
                           align-items-end"
                >


                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >

                        <i
                            class="bi bi-search me-1"
                        ></i>

                        Cari

                    </button>


                </div>


            </form>


        </div>

    </div>



    <!-- =================================================
         TABLE
    ================================================= -->

    <div
        class="card
               shadow-sm"
    >

        <div class="card-body p-4">


            <div
                class="table-responsive"
            >


                <table
                    class="table
                           table-hover"
                >


                    <thead
                        class="table-light"
                    >

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th>
                                NIK
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Jenis Kelamin
                            </th>

                            <th>
                                Agama
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="220">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    while (
                        $row =
                        mysqli_fetch_assoc(
                            $result
                        )
                    ):

                    ?>


                        <tr>


                            <td>

                                <?= $no++ ?>

                            </td>


                            <td>

                                <?php if (
                                    !empty(
                                        $row["nik"]
                                    )
                                ): ?>

                                    <?= clean(
                                        $row["nik"]
                                    ) ?>

                                <?php else: ?>

                                    <span
                                        class="text-muted"
                                    >

                                        Belum diisi

                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <strong>

                                    <?= clean(
                                        $row["name"]
                                    ) ?>

                                </strong>

                            </td>


                            <td>

                                <?= clean(
                                    $row["gender"]
                                ) ?>

                            </td>


                            <td>

                                <?= clean(
                                    $row["religion"]
                                    ?? "-"
                                ) ?>

                            </td>


                            <td>


                                <?php

                                $status_class =
                                    "bg-success";

                                if (
                                    $row[
                                        "population_status"
                                    ]
                                    ===
                                    "Pindah Keluar"
                                ) {

                                    $status_class =
                                        "bg-warning text-dark";

                                }


                                if (
                                    $row[
                                        "population_status"
                                    ]
                                    ===
                                    "Meninggal"
                                ) {

                                    $status_class =
                                        "bg-secondary";

                                }

                                ?>


                                <span
                                    class="badge
                                           badge-status
                                           <?= $status_class ?>"
                                >

                                    <?= clean(
                                        $row[
                                            "population_status"
                                        ]
                                    ) ?>

                                </span>


                            </td>


                            <td>


                                <!-- DETAIL -->

                                <a
                                    href="detail.php?id=<?= (int) $row["id"] ?>"
                                    class="btn btn-sm btn-info text-white"
                                    title="Detail"
                                >

                                    <i
                                        class="bi bi-eye"
                                    ></i>

                                </a>



                                <!-- EDIT -->

                                <a
                                    href="edit.php?id=<?= (int) $row["id"] ?>"
                                    class="btn btn-sm btn-warning"
                                    title="Edit"
                                >

                                    <i
                                        class="bi bi-pencil"
                                    ></i>

                                </a>



                                <!-- RIWAYAT -->

                                <a
                                    href="riwayat.php?id=<?= (int) $row["id"] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Riwayat"
                                >

                                    <i
                                        class="bi bi-clock-history"
                                    ></i>

                                </a>



                                <!-- STATUS -->

                                <a
                                    href="status.php?id=<?= (int) $row["id"] ?>"
                                    class="btn btn-sm btn-secondary"
                                    title="Ubah Status"
                                >

                                    <i
                                        class="bi bi-arrow-left-right"
                                    ></i>

                                </a>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                    <?php if (
                        mysqli_num_rows(
                            $result
                        ) === 0
                    ): ?>


                        <tr>

                            <td
                                colspan="7"
                                class="text-center
                                       py-5
                                       text-muted"
                            >

                                <i
                                    class="bi bi-people
                                           fs-1"
                                ></i>


                                <p
                                    class="mt-3
                                           mb-0"
                                >

                                    Belum ada data warga
                                    pada database.

                                </p>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>


            </div>


        </div>

    </div>



    <!-- =================================================
         FOOTER BUTTON
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


</body>

</html>