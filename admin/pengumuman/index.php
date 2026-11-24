<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// AMBIL DATA PENGUMUMAN
// ==========================================

$sql = "
    SELECT *
    FROM announcements
    ORDER BY
        published_at DESC,
        created_at DESC
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
        Kelola Pengumuman - Cisalada GO
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

    <div
        class="container-fluid px-4"
    >


        <!-- LOGO -->

        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>


        <!-- RIGHT MENU -->

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

                Kelola Pengumuman

            </h2>


            <p class="text-muted mb-0">

                Kelola informasi dan pengumuman
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

            Tambah Pengumuman

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

                echo "Pengumuman berhasil ditambahkan.";

            } elseif (
                $_GET["success"]
                === "edit"
            ) {

                echo "Pengumuman berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                === "hapus"
            ) {

                echo "Pengumuman berhasil dihapus.";

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
                                Judul
                            </th>

                            <th>
                                Jenis
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th width="160">
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



                                <!-- JUDUL -->

                                <td>

                                    <strong>

                                        <?= clean(
                                            $row["title"]
                                        ) ?>

                                    </strong>


                                    <div
                                        class="text-muted small mt-1"
                                    >

                                        <?php

                                        $excerpt =
                                            strip_tags(
                                                $row["content"]
                                            );

                                        echo clean(
                                            mb_substr(
                                                $excerpt,
                                                0,
                                                100
                                            )
                                        );

                                        ?>

                                        <?php if (
                                            strlen(
                                                $excerpt
                                            ) > 100
                                        ): ?>

                                            ...

                                        <?php endif; ?>

                                    </div>

                                </td>



                                <!-- JENIS -->

                                <td>


                                    <?php

                                    $badge_class =
                                        "bg-primary";


                                    if (
                                        $row["type"]
                                        ===
                                        "Penting"
                                    ) {

                                        $badge_class =
                                            "bg-danger";

                                    } elseif (
                                        $row["type"]
                                        ===
                                        "Kegiatan"
                                    ) {

                                        $badge_class =
                                            "bg-success";

                                    } elseif (
                                        $row["type"]
                                        ===
                                        "Layanan"
                                    ) {

                                        $badge_class =
                                            "bg-info text-dark";

                                    }

                                    ?>


                                    <span
                                        class="badge
                                               <?= $badge_class ?>"
                                    >

                                        <?= clean(
                                            $row["type"]
                                        ) ?>

                                    </span>


                                </td>



                                <!-- TANGGAL -->

                                <td>

                                    <?= date(
                                        "d M Y",
                                        strtotime(
                                            $row[
                                                "published_at"
                                            ]
                                        )
                                    ) ?>

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
                                            'Yakin ingin menghapus pengumuman ini?'
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
                                colspan="5"
                                class="text-center py-5"
                            >


                                <i
                                    class="bi bi-megaphone
                                           fs-1
                                           text-muted"
                                ></i>


                                <p
                                    class="text-muted
                                           mt-3
                                           mb-0"
                                >

                                    Belum ada pengumuman.

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


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>