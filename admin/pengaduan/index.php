<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA PENGADUAN
// ======================================================

$result =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            name,
            contact,
            category,
            title,
            complaint,
            attachment,
            status,
            created_at
        FROM complaints
        ORDER BY
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
        Pengaduan Warga - Admin Cisalada GO
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


        .status-baru {

            background:
                #cff4fc;

            color:
                #055160;

        }


        .status-diproses {

            background:
                #fff3cd;

            color:
                #664d03;

        }


        .status-selesai {

            background:
                #d1e7dd;

            color:
                #0f5132;

        }


        .complaint-title {

            font-weight:
                600;

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


    <div
        class="mb-4"
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


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Pengaduan Warga

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Lihat dan kelola pengaduan
            masyarakat Desa Cisalada.

        </p>


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
                "status"
            ) {

                echo
                    "Status pengaduan berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                ===
                "hapus"
            ) {

                echo
                    "Pengaduan berhasil dihapus.";

            } else {

                echo
                    "Proses berhasil.";

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
                    "ID pengaduan tidak valid.";

            } elseif (
                $_GET["error"]
                ===
                "not_found"
            ) {

                echo
                    "Pengaduan tidak ditemukan.";

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

                                Pengadu

                            </th>


                            <th>

                                Pengaduan

                            </th>


                            <th>

                                Kategori

                            </th>


                            <th>

                                Status

                            </th>


                            <th>

                                Tanggal

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
                        )
                        >
                        0
                    ): ?>


                        <?php while (
                            $row =
                                mysqli_fetch_assoc(
                                    $result
                                )
                        ): ?>


                            <?php

                            $status =
                                strtolower(
                                    trim(
                                        $row["status"]
                                        ??
                                        "baru"
                                    )
                                );


                            $status_class =
                                "status-baru";


                            if (
                                $status === "diproses"
                            ) {

                                $status_class =
                                    "status-diproses";

                            } elseif (
                                $status === "selesai"
                            ) {

                                $status_class =
                                    "status-selesai";

                            }

                            ?>


                            <tr>


                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>



                                <td>


                                    <div
                                        class="fw-semibold"
                                    >

                                        <?= clean(
                                            $row["name"]
                                        ) ?>


                                    </div>


                                    <?php if (
                                        !empty(
                                            $row["contact"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-muted
                                                   mt-1"
                                        >

                                            <?= clean(
                                                $row["contact"]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>


                                </td>



                                <td>


                                    <div
                                        class="complaint-title"
                                    >

                                        <?= clean(
                                            $row["title"]
                                            ??
                                            "-"
                                        ) ?>


                                    </div>


                                    <div
                                        class="small
                                               text-muted
                                               mt-1"
                                    >

                                        <?= clean(
                                            mb_strimwidth(
                                                $row["complaint"],
                                                0,
                                                100,
                                                "..."
                                            )
                                        ) ?>


                                    </div>


                                </td>



                                <td>


                                    <?php if (
                                        !empty(
                                            $row["category"]
                                        )
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-primary-subtle
                                                   text-primary"
                                        >

                                            <?= clean(
                                                $row["category"]
                                            ) ?>


                                        </span>


                                    <?php else: ?>


                                        -


                                    <?php endif; ?>


                                </td>



                                <td>


                                    <span
                                        class="badge
                                               <?= $status_class ?>"
                                    >

                                        <?= ucfirst(
                                            clean(
                                                $status
                                            )
                                        ) ?>


                                    </span>


                                </td>



                                <td>


                                    <span
                                        class="small
                                               text-muted"
                                    >

                                        <?= date(
                                            "d M Y",
                                            strtotime(
                                                $row["created_at"]
                                            )
                                        ) ?>


                                    </span>


                                </td>



                                <td>


                                    <a
                                        href="detail.php?id=<?= (int) $row["id"] ?>"
                                        class="btn
                                               btn-primary
                                               btn-sm"
                                        title="Detail"
                                    >

                                        <i
                                            class="bi bi-eye"
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
                                                'Yakin ingin menghapus pengaduan ini?'
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
                                    class="bi bi-chat-left-text
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Pengaduan

                                </h5>


                                <p
                                    class="text-muted
                                           mb-0"
                                >

                                    Belum ada pengaduan
                                    yang masuk.

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



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>