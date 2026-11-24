<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA WISATA
// ======================================================

$query =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            name,
            description,
            location,
            facilities,
            operating_hours,
            ticket_price,
            contact,
            photo,
            created_at
        FROM tourism
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
        Kelola Wisata
        - Cisalada GO
    </title>


    <!-- ==================================================
         BOOTSTRAP
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ==================================================
         BOOTSTRAP ICON
    ================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {

            background-color:
                #f5f7fa;

        }


        .page-card {

            border:
                none;

            border-radius:
                14px;

        }


        .wisata-image {

            width:
                90px;

            height:
                65px;

            object-fit:
                cover;

            border-radius:
                10px;

        }


        .wisata-placeholder {

            width:
                90px;

            height:
                65px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef1f5;

            border-radius:
                10px;

        }


        .wisata-description {

            max-width:
                280px;

        }


        .price {

            color:
                #198754;

            font-weight:
                700;

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
                    $_SESSION["admin_name"]
                    ??
                    "Admin"
                ) ?>


            </span>


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
        class="mb-4"
    >


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

                    Kelola Wisata

                </h2>


                <p
                    class="text-muted
                           mb-0"
                >

                    Kelola informasi objek wisata
                    Desa Cisalada.

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


                Tambah Wisata

            </a>


        </div>


    </div>



    <!-- ALERT SUCCESS -->

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

            switch (
                $_GET["success"]
            ) {

                case "tambah":

                    echo
                        "Data wisata berhasil ditambahkan.";

                    break;


                case "edit":

                    echo
                        "Data wisata berhasil diperbarui.";

                    break;


                case "hapus":

                    echo
                        "Data wisata berhasil dihapus.";

                    break;


                default:

                    echo
                        "Proses berhasil dilakukan.";

                    break;

            }

            ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ALERT ERROR -->

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

            switch (
                $_GET["error"]
            ) {

                case "database":

                    echo
                        "Terjadi kesalahan pada database.";

                    break;


                case "id":

                    echo
                        "ID wisata tidak valid.";

                    break;


                case "not_found":

                    echo
                        "Data wisata tidak ditemukan.";

                    break;


                default:

                    echo
                        "Proses gagal dilakukan.";

                    break;

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
    ================================================== -->

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
                                width="60"
                            >

                                No

                            </th>


                            <th>
                                Foto
                            </th>


                            <th>
                                Nama Wisata
                            </th>


                            <th>
                                Lokasi
                            </th>


                            <th>
                                Jam
                            </th>


                            <th>
                                Harga
                            </th>


                            <th>
                                Kontak
                            </th>


                            <th
                                width="120"
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


                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>



                                <!-- FOTO -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row["photo"]
                                        )
                                    ): ?>


                                        <img
                                            src="<?= BASE_URL ?>assets/uploads/wisata/<?= rawurlencode(basename($row["photo"])) ?>"
                                            alt="<?= clean($row["name"]) ?>"
                                            class="wisata-image"
                                        >


                                    <?php else: ?>


                                        <div
                                            class="wisata-placeholder"
                                        >

                                            <i
                                                class="bi bi-image
                                                       fs-4
                                                       text-muted"
                                            ></i>

                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- NAMA -->

                                <td>


                                    <strong>

                                        <?= clean(
                                            $row["name"]
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
                                                   mt-1
                                                   wisata-description"
                                        >

                                            <?= clean(
                                                mb_strimwidth(
                                                    $row["description"],
                                                    0,
                                                    80,
                                                    "..."
                                                )
                                            ) ?>

                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- LOKASI -->

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
                                                       text-primary
                                                       me-1"
                                            ></i>


                                            <?= clean(
                                                mb_strimwidth(
                                                    $row["location"],
                                                    0,
                                                    70,
                                                    "..."
                                                )
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



                                <!-- JAM -->

                                <td>


                                    <?= !empty(
                                        $row["operating_hours"]
                                    )
                                        ?
                                        clean(
                                            $row["operating_hours"]
                                        )
                                        :
                                        "-"
                                    ?>


                                </td>



                                <!-- HARGA -->

                                <td>


                                    <?php if (
                                        (float)
                                        $row["ticket_price"]
                                        >
                                        0
                                    ): ?>


                                        <span
                                            class="price"
                                        >

                                            Rp
                                            <?= number_format(
                                                (float)
                                                $row["ticket_price"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>


                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="text-muted"
                                        >

                                            Gratis

                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- KONTAK -->

                                <td>


                                    <?= !empty(
                                        $row["contact"]
                                    )
                                        ?
                                        clean(
                                            $row["contact"]
                                        )
                                        :
                                        "-"
                                    ?>


                                </td>



                                <!-- AKSI -->

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
                                                'Yakin ingin menghapus wisata ini?'
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
                                colspan="8"
                                class="text-center
                                       py-5"
                            >


                                <i
                                    class="bi bi-geo-alt
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Data Wisata

                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    Belum ada objek wisata
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


                                    Tambah Wisata

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