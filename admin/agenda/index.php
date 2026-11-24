<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// AMBIL DATA AGENDA
// ==========================================

$sql = "
    SELECT *
    FROM agendas
    ORDER BY
        event_date ASC,
        event_time ASC
";

$result = mysqli_query(
    $conn,
    $sql
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
        Kelola Agenda - Cisalada GO
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

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

    <div class="container-fluid px-4">


        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>


        <div
            class="d-flex
                   align-items-center
                   text-white"
        >


            <span
                class="d-none d-md-inline me-3"
            >

                <i
                    class="bi bi-person-circle me-1"
                ></i>

                <?= clean(
                    $_SESSION["admin_name"]
                ) ?>

            </span>


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

    </div>

</nav>



<!-- ==========================================
     CONTENT
========================================== -->

<div class="container-fluid px-4 py-4">


    <!-- HEADER -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>

            <h2 class="fw-bold mb-1">

                Agenda Desa

            </h2>


            <p class="text-muted mb-0">

                Kelola agenda dan kegiatan
                Desa Cisalada.

            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i
                class="bi bi-plus-lg me-1"
            ></i>

            Tambah Agenda

        </a>


    </div>



    <!-- ==========================================
         ALERT
    ========================================== -->

    <?php if (
        isset($_GET["success"])
    ): ?>


        <div
            class="alert alert-success
                   alert-dismissible fade show"
        >


            <i
                class="bi bi-check-circle me-1"
            ></i>


            <?php

            if (
                $_GET["success"]
                === "tambah"
            ) {

                echo "Agenda berhasil ditambahkan.";

            } elseif (
                $_GET["success"]
                === "edit"
            ) {

                echo "Agenda berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                === "hapus"
            ) {

                echo "Agenda berhasil dihapus.";

            }

            ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ==========================================
         TABLE
========================================== -->

    <div
        class="card
               border-0
               shadow-sm"
    >


        <div class="card-body p-0">


            <div class="table-responsive">


                <table
                    class="table
                           table-hover
                           align-middle
                           mb-0"
                >


                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th>
                                Agenda
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Waktu
                            </th>

                            <th>
                                Lokasi
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="150">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    ?>


                    <?php if (
                        mysqli_num_rows(
                            $result
                        ) > 0
                    ): ?>


                        <?php while (
                            $row =
                                mysqli_fetch_assoc(
                                    $result
                                )
                        ): ?>


                            <tr>


                                <!-- NO -->

                                <td>

                                    <?= $no++ ?>

                                </td>



                                <!-- AGENDA -->

                                <td>

                                    <strong>

                                        <?= clean(
                                            $row["title"]
                                        ) ?>

                                    </strong>


                                    <div
                                        class="small
                                               text-muted
                                               mt-1"
                                    >

                                        <?php

                                        $description =
                                            strip_tags(
                                                $row[
                                                    "description"
                                                ]
                                            );

                                        echo clean(
                                            mb_substr(
                                                $description,
                                                0,
                                                80
                                            )
                                        );

                                        ?>

                                        <?php if (
                                            strlen(
                                                $description
                                            ) > 80
                                        ): ?>

                                            ...

                                        <?php endif; ?>

                                    </div>

                                </td>



                                <!-- TANGGAL -->

                                <td>

                                    <i
                                        class="bi bi-calendar3
                                               me-1"
                                    ></i>

                                    <?= date(
                                        "d M Y",
                                        strtotime(
                                            $row[
                                                "event_date"
                                            ]
                                        )
                                    ) ?>

                                </td>



                                <!-- WAKTU -->

                                <td>

                                    <i
                                        class="bi bi-clock
                                               me-1"
                                    ></i>

                                    <?= date(
                                        "H:i",
                                        strtotime(
                                            $row[
                                                "event_time"
                                            ]
                                        )
                                    ) ?>

                                    WIB

                                </td>



                                <!-- LOKASI -->

                                <td>

                                    <i
                                        class="bi bi-geo-alt
                                               me-1"
                                    ></i>

                                    <?= clean(
                                        $row[
                                            "location"
                                        ]
                                    ) ?>

                                </td>



                                <!-- STATUS -->

                                <td>


                                    <?php

                                    $badge =
                                        "bg-primary";


                                    if (
                                        $row["status"]
                                        ===
                                        "Selesai"
                                    ) {

                                        $badge =
                                            "bg-secondary";

                                    } elseif (
                                        $row["status"]
                                        ===
                                        "Dibatalkan"
                                    ) {

                                        $badge =
                                            "bg-danger";

                                    } elseif (
                                        $row["status"]
                                        ===
                                        "Sedang Berlangsung"
                                    ) {

                                        $badge =
                                            "bg-success";

                                    }

                                    ?>


                                    <span
                                        class="badge
                                               <?= $badge ?>"
                                    >

                                        <?= clean(
                                            $row[
                                                "status"
                                            ]
                                        ) ?>

                                    </span>


                                </td>



                                <!-- AKSI -->

                                <td>


                                    <a
                                        href="edit.php?id=<?= $row["id"] ?>"
                                        class="btn btn-warning btn-sm"
                                        title="Edit"
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </a>


                                    <a
                                        href="hapus.php?id=<?= $row["id"] ?>"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus"
                                        onclick="return confirm(
                                            'Yakin ingin menghapus agenda ini?'
                                        );"
                                    >

                                        <i
                                            class="bi bi-trash"
                                        ></i>

                                    </a>


                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >


                                <i
                                    class="bi bi-calendar-event
                                           fs-1
                                           text-muted"
                                ></i>


                                <p
                                    class="text-muted
                                           mt-3
                                           mb-0"
                                >

                                    Belum ada agenda.

                                </p>


                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


</body>

</html>