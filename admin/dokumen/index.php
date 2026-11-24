<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA DOKUMEN
// ======================================================

$query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        name,
        category,
        year,
        file,
        created_at

    FROM documents

    ORDER BY
        year DESC,
        created_at DESC,
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
        Dokumen Desa - Admin Cisalada GO
    </title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ICON -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {
            background-color: #f5f7fa;
        }


        .document-card {
            border: none;
            border-radius: 14px;
        }


        .table thead th {
            white-space: nowrap;
        }


        .file-icon {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            background-color: #fff0f0;

            border-radius: 10px;
        }

    </style>

</head>


<body>


<!-- ======================================================
     NAVBAR ADMIN
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


        <!-- LOGO -->

        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand
                   fw-bold"
        >

            CISALADA GO

        </a>



        <!-- RIGHT MENU -->

        <div
            class="d-flex
                   align-items-center"
        >


            <span
                class="text-white
                       d-none
                       d-md-inline
                       me-3"
            >

                <i
                    class="bi bi-person-circle
                           me-1"
                ></i>

                <?= clean(
                    $_SESSION[
                        "admin_name"
                    ]
                ) ?>

            </span>


            <!-- PUBLIC -->

            <a
                href="<?= BASE_URL ?>pages/dokumen.php"
                target="_blank"
                class="btn
                       btn-outline-light
                       btn-sm
                       me-2"
            >

                <i
                    class="bi bi-globe2
                           me-1"
                ></i>

                Lihat Public

            </a>


            <!-- DASHBOARD -->

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


            <!-- LOGOUT -->

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


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div
        class="mb-4"
    >


        <!-- BACK -->

        <div
            class="mb-3"
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



        <div
            class="d-flex
                   justify-content-between
                   align-items-center
                   flex-wrap
                   gap-3"
        >


            <div>

                <h2
                    class="fw-bold
                           mb-1"
                >

                    Dokumen Desa

                </h2>


                <p
                    class="text-muted
                           mb-0"
                >

                    Kelola dokumen publik Desa Cisalada.

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

                Tambah Dokumen

            </a>


        </div>


    </div>



    <!-- ==================================================
         SUCCESS MESSAGE
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

                echo "Dokumen berhasil ditambahkan.";

            } elseif (
                $_GET["success"]
                ===
                "edit"
            ) {

                echo "Dokumen berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                ===
                "hapus"
            ) {

                echo "Dokumen berhasil dihapus.";

            } else {

                echo "Proses berhasil dilakukan.";

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
         ERROR MESSAGE
    =================================================== -->

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

            Terjadi kesalahan saat memproses
            dokumen.


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
               document-card
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
                                width="60"
                                class="ps-4"
                            >

                                No

                            </th>

                            <th>

                                Dokumen

                            </th>

                            <th>

                                Kategori

                            </th>

                            <th>

                                Tahun

                            </th>

                            <th>

                                File

                            </th>

                            <th
                                width="170"
                            >

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


                                <!-- NO -->

                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>



                                <!-- DOKUMEN -->

                                <td>


                                    <div
                                        class="d-flex
                                               align-items-center
                                               gap-3"
                                    >


                                        <div
                                            class="file-icon
                                                   flex-shrink-0"
                                        >

                                            <i
                                                class="bi bi-file-earmark-pdf
                                                       text-danger
                                                       fs-5"
                                            ></i>

                                        </div>


                                        <div>

                                            <div
                                                class="fw-semibold"
                                            >

                                                <?= clean(
                                                    $row[
                                                        "name"
                                                    ]
                                                ) ?>

                                            </div>


                                            <small
                                                class="text-muted"
                                            >

                                                ID:
                                                <?= (int) $row[
                                                    "id"
                                                ] ?>

                                            </small>

                                        </div>


                                    </div>


                                </td>



                                <!-- KATEGORI -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row[
                                                "category"
                                            ]
                                        )
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-primary"
                                        >

                                            <?= clean(
                                                $row[
                                                    "category"
                                                ]
                                            ) ?>

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="text-muted"
                                        >

                                            -

                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- TAHUN -->

                                <td>

                                    <?= !empty(
                                        $row[
                                            "year"
                                        ]
                                    )
                                        ? clean(
                                            $row[
                                                "year"
                                            ]
                                        )
                                        : "-"
                                    ?>

                                </td>



                                <!-- FILE -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row[
                                                "file"
                                            ]
                                        )
                                    ): ?>


                                        <span
                                            class="text-success"
                                        >

                                            <i
                                                class="bi bi-check-circle
                                                       me-1"
                                            ></i>

                                            Tersedia

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="text-muted"
                                        >

                                            Tidak tersedia

                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- AKSI -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row[
                                                "file"
                                            ]
                                        )
                                    ): ?>


                                        <a
                                            href="<?= BASE_URL ?>assets/uploads/dokumen/<?= rawurlencode(basename($row["file"])) ?>"
                                            target="_blank"
                                            class="btn
                                                   btn-info
                                                   btn-sm"
                                            title="Lihat Dokumen"
                                        >

                                            <i
                                                class="bi bi-eye"
                                            ></i>

                                        </a>


                                    <?php endif; ?>


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
                                                'Yakin ingin menghapus dokumen ini?'
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


                        <!-- ==================================================
                             EMPTY STATE
                        =================================================== -->

                        <tr>

                            <td
                                colspan="6"
                                class="text-center
                                       py-5"
                            >


                                <i
                                    class="bi bi-file-earmark-text
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Dokumen

                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    Belum ada dokumen yang
                                    ditambahkan.

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

                                    Tambah Dokumen

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



<!-- BOOTSTRAP JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>