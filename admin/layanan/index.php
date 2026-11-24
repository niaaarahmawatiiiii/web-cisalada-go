<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA LAYANAN
// ======================================================

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            name,
            category,
            requirements,
            service_procedure,
            service_time,
            cost,
            location,
            created_at
        FROM services
        ORDER BY
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
        Kelola Layanan - Cisalada GO
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


        .service-title {

            font-weight:
                600;

        }


        .category-badge {

            background:
                #eaf2ff;

            color:
                #0d6efd;

        }


        .money {

            white-space:
                nowrap;

        }


        .procedure-preview {

            max-width:
                280px;

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

                Layanan Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Kelola informasi layanan dan
                persyaratan administrasi Desa Cisalada.

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

            Tambah Layanan

        </a>


    </div>



    <!-- ==================================================
         ALERT SUCCESS
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
                $_GET["success"] === "tambah"
            ) {

                echo
                    "Layanan berhasil ditambahkan.";

            } elseif (
                $_GET["success"] === "edit"
            ) {

                echo
                    "Layanan berhasil diperbarui.";

            } elseif (
                $_GET["success"] === "hapus"
            ) {

                echo
                    "Layanan berhasil dihapus.";

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



    <!-- ==================================================
         ALERT ERROR
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


            <?php

            if (
                $_GET["error"] === "id"
            ) {

                echo
                    "ID layanan tidak valid.";

            } elseif (
                $_GET["error"] === "not_found"
            ) {

                echo
                    "Data layanan tidak ditemukan.";

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

                                Nama Layanan

                            </th>


                            <th>

                                Kategori

                            </th>


                            <th>

                                Waktu

                            </th>


                            <th>

                                Biaya

                            </th>


                            <th>

                                Lokasi

                            </th>


                            <th>

                                Aksi

                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no =
                        1;

                    ?>


                    <?php if (
                        $result
                        &&
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


                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>



                                <td>


                                    <div
                                        class="service-title"
                                    >

                                        <?= clean(
                                            $row["name"]
                                        ) ?>

                                    </div>


                                    <?php if (
                                        !empty(
                                            $row["requirements"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-muted
                                                   mt-1
                                                   procedure-preview"
                                        >

                                            <?= clean(
                                                mb_strimwidth(
                                                    $row["requirements"],
                                                    0,
                                                    80,
                                                    "..."
                                                )
                                            ) ?>

                                        </div>


                                    <?php endif; ?>


                                </td>



                                <td>


                                    <?php if (
                                        !empty(
                                            $row["category"]
                                        )
                                    ): ?>


                                        <span
                                            class="badge
                                                   category-badge"
                                        >

                                            <?= clean(
                                                $row["category"]
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



                                <td>


                                    <?php if (
                                        !empty(
                                            $row["service_time"]
                                        )
                                    ): ?>


                                        <span
                                            class="small"
                                        >

                                            <i
                                                class="bi bi-clock
                                                       me-1"
                                            ></i>


                                            <?= clean(
                                                $row["service_time"]
                                            ) ?>


                                        </span>


                                    <?php else: ?>


                                        -


                                    <?php endif; ?>


                                </td>



                                <td
                                    class="money"
                                >


                                    <?php

                                    $cost_text =
                                        trim(
                                            $row["cost"]
                                            ??
                                            ""
                                        );

                                    ?>


                                    <?php if (
                                        $cost_text === ""
                                        ||
                                        $cost_text === "0"
                                    ): ?>


                                        <span
                                            class="text-success
                                                   fw-semibold"
                                        >

                                            Gratis

                                        </span>


                                    <?php else: ?>


                                        <?= clean(
                                            $cost_text
                                        ) ?>


                                    <?php endif; ?>


                                </td>



                                <td>


                                    <?php if (
                                        !empty(
                                            $row["location"]
                                        )
                                    ): ?>


                                        <span
                                            class="small"
                                        >

                                            <i
                                                class="bi bi-geo-alt
                                                       me-1"
                                            ></i>


                                            <?= clean(
                                                mb_strimwidth(
                                                    $row["location"],
                                                    0,
                                                    60,
                                                    "..."
                                                )
                                            ) ?>


                                        </span>


                                    <?php else: ?>


                                        -


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
                                                'Yakin ingin menghapus layanan ini?'
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
                                colspan="7"
                                class="text-center
                                       py-5"
                            >


                                <i
                                    class="bi bi-headset
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Layanan

                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    Belum ada layanan
                                    yang ditambahkan.

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

                                    Tambah Layanan

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