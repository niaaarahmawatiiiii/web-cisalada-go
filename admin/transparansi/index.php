<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA TRANSPARANSI
// ======================================================

$query =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            title,
            category,
            year,
            income,
            expenditure,
            financing,
            description,
            document,
            created_at
        FROM transparency
        ORDER BY
            year DESC,
            id DESC
        "
    );

?>

<!DOCTYPE html>

<html
    lang="id"
>

<head>

    <meta
        charset="UTF-8"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Kelola Transparansi - Cisalada GO
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

            background:
                #f5f7fa;

        }

        .page-card {

            border:
                0;

            border-radius:
                16px;

        }

        .document-badge {

            background:
                #fff3cd;

            color:
                #856404;

        }

        .money {

            font-weight:
                600;

            color:
                #198754;

        }

    </style>

</head>

<body>


<!-- ======================================================
     NAVBAR
====================================================== -->

<nav
    class="navbar
           navbar-dark
           bg-primary"
>

    <div
        class="container-fluid
               px-4"
    >

        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand
                   fw-bold"
        >

            CISALADA GO

        </a>


        <div
            class="d-flex
                   align-items-center"
        >

            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="btn
                       btn-light
                       btn-sm
                       me-2"
            >

                <i
                    class="bi bi-speedometer2
                           me-1"
                ></i>

                Dashboard

            </a>


            <a
                href="<?= BASE_URL ?>admin/logout.php"
                class="btn
                       btn-outline-light
                       btn-sm"
                onclick="
                    return confirm(
                        'Yakin ingin logout?'
                    );
                "
            >

                <i
                    class="bi bi-box-arrow-right
                           me-1"
                ></i>

                Logout

            </a>

        </div>

    </div>

</nav>



<!-- ======================================================
     CONTENT
====================================================== -->

<div
    class="container-fluid
           px-4
           py-4"
>


    <!-- HEADER -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               flex-wrap
               gap-3
               mb-4"
    >

        <div>

            <div
                class="mb-2"
            >

                <a
                    href="<?= BASE_URL ?>admin/index.php"
                    class="text-decoration-none
                           text-muted"
                >

                    <i
                        class="bi bi-arrow-left
                               me-1"
                    ></i>

                    Kembali ke Dashboard

                </a>

            </div>


            <h2
                class="fw-bold
                       mb-1"
            >

                Transparansi Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Kelola informasi transparansi,
                APBDes, pembangunan, dan dokumen publik.

            </p>

        </div>


        <a
            href="tambah.php"
            class="btn
                   btn-primary"
        >

            <i
                class="bi bi-plus-lg
                       me-1"
            ></i>

            Tambah Transparansi

        </a>

    </div>



    <!-- ==================================================
         ALERT
    =================================================== -->

    <?php if (
        isset(
            $_GET["success"]
        )
    ): ?>

        <div
            class="alert
                   alert-success
                   alert-dismissible
                   fade show"
        >

            <i
                class="bi bi-check-circle
                       me-1"
            ></i>


            <?php

            if (
                $_GET["success"]
                ===
                "tambah"
            ) {

                echo
                    "Data transparansi berhasil ditambahkan.";

            } elseif (
                $_GET["success"]
                ===
                "edit"
            ) {

                echo
                    "Data transparansi berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                ===
                "hapus"
            ) {

                echo
                    "Data transparansi berhasil dihapus.";

            } else {

                echo
                    "Proses berhasil dilakukan.";

            }

            ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>



    <?php if (
        isset(
            $_GET["error"]
        )
    ): ?>

        <div
            class="alert
                   alert-danger
                   alert-dismissible
                   fade show"
        >

            <i
                class="bi bi-exclamation-circle
                       me-1"
            ></i>


            <?php

            if (
                $_GET["error"]
                ===
                "id"
            ) {

                echo
                    "ID transparansi tidak valid.";

            } elseif (
                $_GET["error"]
                ===
                "not_found"
            ) {

                echo
                    "Data transparansi tidak ditemukan.";

            } else {

                echo
                    "Terjadi kesalahan.";

            }

            ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>



    <!-- ==================================================
         TABLE
    =================================================== -->

    <div
        class="card
               page-card
               shadow-sm"
    >

        <div
            class="card-body
                   p-0"
        >

            <div
                class="table-responsive"
            >

                <table
                    class="table
                           table-hover
                           align-middle
                           mb-0"
                >

                    <thead
                        class="table-light"
                    >

                        <tr>

                            <th
                                class="ps-4"
                            >
                                No
                            </th>

                            <th>
                                Judul
                            </th>

                            <th>
                                Kategori
                            </th>

                            <th>
                                Tahun
                            </th>

                            <th>
                                Pendapatan
                            </th>

                            <th>
                                Belanja
                            </th>

                            <th>
                                Pembiayaan
                            </th>

                            <th>
                                Dokumen
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    ?>


                    <?php if (
                        $query
                        &&
                        mysqli_num_rows(
                            $query
                        ) > 0
                    ): ?>


                        <?php while (
                            $row =
                                mysqli_fetch_assoc(
                                    $query
                                )
                        ): ?>

                            <tr>

                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>


                                <td>

                                    <strong>

                                        <?= clean(
                                            $row["title"]
                                        ) ?>

                                    </strong>


                                    <?php if (
                                        !empty(
                                            $row["description"]
                                        )
                                    ): ?>

                                        <div
                                            class="small
                                                   text-muted
                                                   mt-1"
                                        >

                                            <?= clean(
                                                mb_strimwidth(
                                                    $row["description"],
                                                    0,
                                                    70,
                                                    "..."
                                                )
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <span
                                        class="badge
                                               bg-primary"
                                    >

                                        <?= clean(
                                            $row["category"]
                                            ??
                                            "Umum"
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= !empty(
                                        $row["year"]
                                    )
                                        ?
                                        (int)
                                        $row["year"]
                                        :
                                        "-"
                                    ?>

                                </td>


                                <td>

                                    <span
                                        class="money"
                                    >

                                        Rp
                                        <?= number_format(
                                            (float)
                                            $row["income"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="money"
                                    >

                                        Rp
                                        <?= number_format(
                                            (float)
                                            $row["expenditure"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="money"
                                    >

                                        Rp
                                        <?= number_format(
                                            (float)
                                            $row["financing"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?php if (
                                        !empty(
                                            $row["document"]
                                        )
                                    ): ?>

                                        <a
                                            href="<?= BASE_URL ?>assets/uploads/transparansi/<?= rawurlencode(
                                                basename(
                                                    $row["document"]
                                                )
                                            ) ?>"
                                            target="_blank"
                                            class="badge
                                                   document-badge
                                                   text-decoration-none"
                                        >

                                            <i
                                                class="bi bi-file-earmark-pdf
                                                       me-1"
                                            ></i>

                                            PDF

                                        </a>

                                    <?php else: ?>

                                        <span
                                            class="text-muted
                                                   small"
                                        >

                                            Tidak ada

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <a
                                        href="edit.php?id=<?= (int) $row["id"] ?>"
                                        class="btn
                                               btn-warning
                                               btn-sm"
                                        title="Edit"
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </a>


                                    <a
                                        href="hapus.php?id=<?= (int) $row["id"] ?>"
                                        class="btn
                                               btn-danger
                                               btn-sm"
                                        title="Hapus"
                                        onclick="
                                            return confirm(
                                                'Yakin ingin menghapus data transparansi ini?'
                                            );
                                        "
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
                                colspan="9"
                                class="text-center
                                       py-5"
                            >

                                <i
                                    class="bi bi-bar-chart-line
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Data Transparansi

                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    Belum ada informasi
                                    transparansi yang ditambahkan.

                                </p>


                                <a
                                    href="tambah.php"
                                    class="btn
                                           btn-primary"
                                >

                                    <i
                                        class="bi bi-plus-lg
                                               me-1"
                                    ></i>

                                    Tambah Data

                                </a>

                            </td>

                        </tr>


                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>