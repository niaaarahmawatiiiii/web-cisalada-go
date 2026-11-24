<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA UMKM
// ======================================================

$query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        name,
        owner,
        category,
        description,
        photo,
        phone,
        address,
        maps_url,
        google_place_id,
        google_rating,
        google_rating_count,
        rating_synced_at,
        created_at

    FROM umkm

    ORDER BY
        created_at DESC,
        id DESC
    "
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

        UMKM Desa
        - Admin Cisalada GO

    </title>


    <!-- ======================================================
         BOOTSTRAP
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ======================================================
         BOOTSTRAP ICON
    ======================================================= -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {

            background-color:
                #f5f7fa;

        }


        .umkm-card {

            border:
                none;

            border-radius:
                14px;

        }


        .umkm-photo {

            width:
                80px;

            height:
                65px;

            object-fit:
                cover;

            border-radius:
                10px;

        }


        .empty-photo {

            width:
                80px;

            height:
                65px;

            border-radius:
                10px;

            background-color:
                #f1f3f5;


            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .table th {

            white-space:
                nowrap;

        }


        .rating-box {

            min-width:
                125px;

        }


        .rating-number {

            font-size:
                18px;

            font-weight:
                700;

        }


        .rating-stars {

            color:
                #f5b301;

            letter-spacing:
                1px;

            font-size:
                14px;

        }


        .rating-count {

            font-size:
                12px;

            color:
                #6c757d;

        }


        .sync-time {

            font-size:
                11px;

            color:
                #6c757d;

        }


        .action-group {

            display:
                flex;

            gap:
                5px;

            flex-wrap:
                wrap;

        }


        .map-status {

            font-size:
                12px;

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


            <!-- USER -->

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
                    ??
                    "Admin"
                ) ?>


            </span>



            <!-- WEBSITE -->

            <a
                href="<?= BASE_URL ?>"
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


                Website


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

                    UMKM Desa


                </h2>


                <p
                    class="text-muted
                           mb-0"
                >

                    Kelola data UMKM masyarakat
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


                Tambah UMKM


            </a>


        </div>


    </div>



    <!-- ==================================================
         SUCCESS
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
                    "Data UMKM berhasil ditambahkan.";

            } elseif (
                $_GET["success"]
                ===
                "edit"
            ) {

                echo
                    "Data UMKM berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                ===
                "hapus"
            ) {

                echo
                    "Data UMKM berhasil dihapus.";

            } elseif (
                $_GET["success"]
                ===
                "rating"
            ) {

                echo
                    "Rating Google berhasil diperbarui.";

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
         ERROR
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


            $error_message =
                "Terjadi kesalahan.";


            switch (
                $_GET["error"]
            ) {


                case "api_key":

                    $error_message =
                        "Google Places API Key belum dikonfigurasi.";

                    break;


                case "id":

                    $error_message =
                        "ID UMKM tidak valid.";

                    break;


                case "database":

                    $error_message =
                        "Terjadi kesalahan pada database.";

                    break;


                case "not_found":

                    $error_message =
                        "Data UMKM tidak ditemukan.";

                    break;


                case "no_location":

                    $error_message =
                        "UMKM belum memiliki alamat atau lokasi.";

                    break;


                case "place_not_found":

                    $error_message =
                        "Lokasi UMKM tidak ditemukan di Google. Periksa nama dan alamat UMKM.";

                    break;


                case "google_request":

                    $error_message =
                        "Gagal menghubungi Google Places API.";

                    break;


                case "google_response":

                    $error_message =
                        "Respons dari Google tidak dapat dibaca.";

                    break;


                case "update":

                    $error_message =
                        "Data rating gagal disimpan ke database.";

                    break;


                default:

                    $error_message =
                        "Rating Google gagal diperbarui.";

                    break;


            }


            echo
                clean(
                    $error_message
                );


            ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ==================================================
         INFO RATING
    =================================================== -->

    <div
        class="alert
               alert-light
               border
               mb-4"
    >


        <div
            class="d-flex
                   gap-2"
        >


            <i
                class="bi bi-info-circle
                       text-primary
                       fs-5"
            ></i>


            <div>


                <div
                    class="fw-semibold"
                >

                    Rating Google


                </div>


                <div
                    class="small
                           text-muted"
                >

                    Rating di bawah ini berasal dari
                    Google Places dan tidak dapat diedit
                    secara manual. Gunakan tombol
                    <strong>
                        Sinkronkan Rating
                    </strong>
                    untuk memperbarui data.


                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         TABLE
    =================================================== -->

    <div
        class="card
               umkm-card
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

                                Foto


                            </th>


                            <th>

                                Nama UMKM


                            </th>


                            <th>

                                Pemilik


                            </th>


                            <th>

                                Kategori


                            </th>


                            <th>

                                Rating Google


                            </th>


                            <th>

                                Lokasi


                            </th>


                            <th
                                width="220"
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


                            <?php


                            $google_rating =
                                $row[
                                    "google_rating"
                                ];


                            $google_rating_count =
                                $row[
                                    "google_rating_count"
                                ];


                            $rating_synced_at =
                                $row[
                                    "rating_synced_at"
                                ];


                            ?>


                            <tr>


                                <!-- ==================================
                                     NO
                                =================================== -->

                                <td
                                    class="ps-4"
                                >

                                    <?= $no++ ?>

                                </td>



                                <!-- ==================================
                                     FOTO
                                =================================== -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row["photo"]
                                        )
                                    ): ?>


                                        <img
                                            src="<?= BASE_URL ?>assets/uploads/umkm/<?= rawurlencode(basename($row["photo"])) ?>"
                                            alt="Foto <?= clean($row["name"]) ?>"
                                            class="umkm-photo"
                                        >


                                    <?php else: ?>


                                        <div
                                            class="empty-photo"
                                        >

                                            <i
                                                class="bi bi-shop
                                                       fs-4
                                                       text-muted"
                                            ></i>

                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- ==================================
                                     NAMA
                                =================================== -->

                                <td>


                                    <strong>

                                        <?= clean(
                                            $row["name"]
                                        ) ?>


                                    </strong>



                                    <?php if (
                                        !empty(
                                            $row["address"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-muted
                                                   mt-1"
                                        >

                                            <?= mb_strimwidth(
                                                clean(
                                                    $row["address"]
                                                ),
                                                0,
                                                40,
                                                "..."
                                            ) ?>


                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- ==================================
                                     PEMILIK
                                =================================== -->

                                <td>

                                    <?= !empty(
                                        $row["owner"]
                                    )
                                        ?
                                        clean(
                                            $row["owner"]
                                        )
                                        :
                                        "-"
                                    ?>


                                </td>



                                <!-- ==================================
                                     KATEGORI
                                =================================== -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row["category"]
                                        )
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-primary"
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



                                <!-- ==================================
                                     RATING GOOGLE
                                =================================== -->

                                <td>


                                    <div
                                        class="rating-box"
                                    >


                                        <?php if (
                                            $google_rating
                                            !==
                                            null
                                        ): ?>


                                            <div
                                                class="d-flex
                                                       align-items-center
                                                       gap-2"
                                            >


                                                <span
                                                    class="rating-number"
                                                >

                                                    <?= number_format(
                                                        (float)
                                                        $google_rating,
                                                        1
                                                    ) ?>


                                                </span>


                                                <span
                                                    class="rating-stars"
                                                >

                                                    <?php


                                                    $rounded_rating =
                                                        (int)
                                                        round(
                                                            (float)
                                                            $google_rating
                                                        );


                                                    for (
                                                        $i = 1;
                                                        $i <= 5;
                                                        $i++
                                                    ) {


                                                        if (
                                                            $i
                                                            <=
                                                            $rounded_rating
                                                        ) {

                                                            echo
                                                                "★";

                                                        } else {

                                                            echo
                                                                "☆";

                                                        }

                                                    }


                                                    ?>

                                                </span>


                                            </div>



                                            <?php if (
                                                $google_rating_count
                                                !==
                                                null
                                            ): ?>


                                                <div
                                                    class="rating-count"
                                                >

                                                    <?= number_format(
                                                        (int)
                                                        $google_rating_count
                                                    ) ?>

                                                    ulasan


                                                </div>


                                            <?php endif; ?>



                                            <?php if (
                                                !empty(
                                                    $rating_synced_at
                                                )
                                            ): ?>


                                                <div
                                                    class="sync-time
                                                           mt-1"
                                                >

                                                    Diperbarui:

                                                    <?= date(
                                                        "d-m-Y H:i",
                                                        strtotime(
                                                            $rating_synced_at
                                                        )
                                                    ) ?>


                                                </div>


                                            <?php endif; ?>


                                        <?php else: ?>


                                            <span
                                                class="text-muted
                                                       small"
                                            >

                                                Belum tersedia


                                            </span>


                                        <?php endif; ?>


                                    </div>


                                </td>



                                <!-- ==================================
                                     LOKASI
                                =================================== -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row["maps_url"]
                                        )
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-success
                                                   map-status"
                                        >

                                            <i
                                                class="bi bi-geo-alt-fill
                                                       me-1"
                                            ></i>


                                            Tersedia


                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="badge
                                                   bg-secondary
                                                   map-status"
                                        >

                                            Belum ada


                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- ==================================
                                     AKSI
                                =================================== -->

                                <td>


                                    <div
                                        class="action-group"
                                    >


                                        <!-- EDIT KOORDINAT -->

                                        <a
                                            href="edit-koordinat.php?id=<?= (int) $row["id"] ?>"
                                            class="btn
                                                   btn-info
                                                   btn-sm"
                                            title="Edit Koordinat (Peta)"
                                        >

                                            <i
                                                class="bi bi-geo-alt"
                                            ></i>


                                        </a>


                                        <!-- EDIT -->

                                        <a
                                            href="edit.php?id=<?= (int) $row["id"] ?>"
                                            class="btn
                                                   btn-warning
                                                   btn-sm"
                                            title="Edit UMKM"
                                        >

                                            <i
                                                class="bi bi-pencil"
                                            ></i>


                                        </a>



                                        <!-- HAPUS -->

                                        <a
                                            href="hapus.php?id=<?= (int) $row["id"] ?>"
                                            class="btn
                                                   btn-danger
                                                   btn-sm"
                                            title="Hapus UMKM"
                                            onclick="
                                                return confirm(
                                                    'Yakin ingin menghapus data UMKM ini?'
                                                );
                                            "
                                        >

                                            <i
                                                class="bi bi-trash"
                                            ></i>


                                        </a>



                                        <!-- GOOGLE MAPS -->

                                        <?php if (
                                            !empty(
                                                $row["maps_url"]
                                            )
                                        ): ?>


                                            <a
                                                href="<?= clean($row["maps_url"]) ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn
                                                       btn-success
                                                       btn-sm"
                                                title="Buka Google Maps"
                                            >

                                                <i
                                                    class="bi bi-geo-alt"
                                                ></i>


                                            </a>


                                        <?php endif; ?>



                                        <!-- SYNC RATING -->

                                        <?php if (
                                            !empty(
                                                $row["maps_url"]
                                            )
                                            ||
                                            !empty(
                                                $row["address"]
                                            )
                                        ): ?>


                                            <a
                                                href="sync_rating.php?id=<?= (int) $row["id"] ?>"
                                                class="btn
                                                       btn-primary
                                                       btn-sm"
                                                title="Sinkronkan Rating Google"
                                                onclick="
                                                    return confirm(
                                                        'Sinkronkan rating UMKM ini dengan Google sekarang?'
                                                    );
                                                "
                                            >

                                                <i
                                                    class="bi bi-arrow-repeat"
                                                ></i>

                                                <span
                                                    class="d-none d-lg-inline"
                                                >

                                                    Sync

                                                </span>


                                            </a>


                                        <?php endif; ?>


                                    </div>


                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <!-- ==========================================
                             EMPTY
                        =========================================== -->

                        <tr>


                            <td
                                colspan="8"
                                class="text-center
                                       py-5"
                            >


                                <i
                                    class="bi bi-shop
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Data UMKM


                                </h5>


                                <p
                                    class="text-muted"
                                >

                                    Belum ada data UMKM
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


                                    Tambah UMKM


                                </a>


                            </td>


                        </tr>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>


    </div>



    <!-- ==================================================
         FOOTER INFO
    =================================================== -->

    <div
        class="mt-3
               small
               text-muted"
    >

        <i
            class="bi bi-lightbulb
                   me-1"
        ></i>


        Rating yang ditampilkan merupakan
        data yang terakhir berhasil disinkronkan
        dari Google.


    </div>


</div>



<!-- ======================================================
     BOOTSTRAP JS
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>