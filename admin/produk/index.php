<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA PRODUK
// ======================================================

$sql = "
    SELECT
        products.*,
        umkm.name AS umkm_name
    FROM products

    INNER JOIN umkm
        ON products.umkm_id = umkm.id

    ORDER BY
        products.created_at DESC,
        products.id DESC
";


$result =
    mysqli_query(
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

        Kelola Produk
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


        .product-image {

            width:
                75px;

            height:
                60px;

            object-fit:
                cover;

            border-radius:
                10px;

        }


        .product-placeholder {

            width:
                75px;

            height:
                60px;

            border-radius:
                10px;

            background-color:
                #eef1f5;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .price {

            color:
                #198754;

            font-weight:
                700;

        }


        .action-group {

            display:
                flex;

            gap:
                5px;

            flex-wrap:
                wrap;

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


    <!-- ==================================================
         HEADER
    ================================================== -->

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

                    Kelola Produk


                </h2>


                <p
                    class="text-muted
                           mb-0"
                >

                    Kelola produk yang dimiliki
                    oleh UMKM Desa Cisalada.


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


                Tambah Produk


            </a>


        </div>


    </div>



    <!-- ==================================================
         ALERT SUCCESS
    ================================================== -->

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
                        "Produk berhasil ditambahkan.";

                    break;


                case "edit":

                    echo
                        "Produk berhasil diperbarui.";

                    break;


                case "hapus":

                    echo
                        "Produk berhasil dihapus.";

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



    <!-- ==================================================
         ALERT ERROR
    ================================================== -->

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
                        "ID produk tidak valid.";

                    break;


                case "not_found":

                    echo
                        "Produk tidak ditemukan.";

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

                                Produk


                            </th>


                            <th>

                                UMKM


                            </th>


                            <th>

                                Harga


                            </th>


                            <th>

                                Deskripsi


                            </th>


                            <th
                                width="150"
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


                                <!-- NO -->

                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>



                                <!-- FOTO -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row["image"]
                                        )
                                    ): ?>


                                        <img
                                            src="<?= BASE_URL ?>assets/uploads/products/<?= rawurlencode(basename($row["image"])) ?>"
                                            alt="<?= clean($row["name"]) ?>"
                                            class="product-image"
                                        >


                                    <?php else: ?>


                                        <div
                                            class="product-placeholder"
                                        >


                                            <i
                                                class="bi bi-box-seam
                                                       fs-4
                                                       text-muted"
                                            ></i>


                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- PRODUK -->

                                <td>


                                    <strong>

                                        <?= clean(
                                            $row["name"]
                                        ) ?>


                                    </strong>


                                </td>



                                <!-- UMKM -->

                                <td>


                                    <span
                                        class="badge
                                               bg-primary"
                                    >

                                        <?= clean(
                                            $row["umkm_name"]
                                        ) ?>


                                    </span>


                                </td>



                                <!-- HARGA -->

                                <td>


                                    <?php if (
                                        (float)
                                        $row["price"]
                                        >
                                        0
                                    ): ?>


                                        <span
                                            class="price"
                                        >

                                            Rp
                                            <?= number_format(
                                                (float)
                                                $row["price"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>


                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="text-muted"
                                        >

                                            Tidak dicantumkan

                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- DESKRIPSI -->

                                <td>


                                    <span
                                        class="text-muted
                                               small"
                                    >

                                        <?= clean(
                                            mb_strimwidth(
                                                $row["description"]
                                                ??
                                                "",
                                                0,
                                                80,
                                                "..."
                                            )
                                        ) ?>


                                    </span>


                                </td>



                                <!-- AKSI -->

                                <td>


                                    <div
                                        class="action-group"
                                    >


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
                                                    'Yakin ingin menghapus produk ini?'
                                                );
                                            "
                                        >


                                            <i
                                                class="bi bi-trash"
                                            ></i>


                                        </a>


                                    </div>


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
                                    class="bi bi-box-seam
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Produk


                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    Belum ada produk
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


                                    Tambah Produk


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