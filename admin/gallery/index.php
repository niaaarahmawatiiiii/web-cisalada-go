<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ======================================================
// AMBIL DATA GALERI
// ======================================================

$sql = "
    SELECT
        *
    FROM gallery
    ORDER BY
        year DESC,
        created_at DESC,
        id DESC
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
        Kelola Galeri - Cisalada GO
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="bg-light">


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
                   align-items-center
                   text-white"
        >


            <span
                class="d-none
                       d-md-inline
                       me-3"
            >

                <i
                    class="bi bi-person-circle
                           me-1"
                ></i>

                <?= clean(
                    $_SESSION["admin_name"]
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
        class="d-flex
               justify-content-between
               align-items-center
               flex-wrap
               gap-3
               mb-4"
    >


        <div>

            <h2
                class="fw-bold
                       mb-1"
            >

                Galeri Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Kelola foto dan video dokumentasi
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

            Tambah Galeri

        </a>


    </div>



    <!-- ==================================================
         ALERT
    =================================================== -->

    <?php if (
        isset($_GET["success"])
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

                echo "Galeri berhasil ditambahkan.";

            } elseif (
                $_GET["success"]
                ===
                "edit"
            ) {

                echo "Galeri berhasil diperbarui.";

            } elseif (
                $_GET["success"]
                ===
                "hapus"
            ) {

                echo "Galeri berhasil dihapus.";

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

    <?php if (!$result): ?>


        <div
            class="alert
                   alert-danger"
        >

            <i
                class="bi bi-exclamation-circle
                       me-1"
            ></i>

            Gagal mengambil data galeri:

            <?= clean(
                mysqli_error($conn)
            ) ?>

        </div>


    <?php endif; ?>



    <!-- ==================================================
         TABLE
    =================================================== -->

    <div
        class="card
               border-0
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
                            >
                                No
                            </th>

                            <th
                                width="140"
                            >
                                Media
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
                                Tipe
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

                                <td>

                                    <?= $no++ ?>

                                </td>



                                <!-- MEDIA -->

                                <td>


                                    <?php

                                    $media_type =
                                        strtolower(
                                            trim(
                                                $row[
                                                    "media_type"
                                                ]
                                                ??
                                                "image"
                                            )
                                        );


                                    $file =
                                        trim(
                                            $row[
                                                "file"
                                            ]
                                            ??
                                            ""
                                        );


                                    $file_url =
                                        BASE_URL
                                        . "assets/uploads/gallery/"
                                        . rawurlencode(
                                            basename(
                                                $file
                                            )
                                        );

                                    ?>


                                    <?php if (
                                        !empty($file)
                                        &&
                                        $media_type
                                        ===
                                        "image"
                                    ): ?>


                                        <img
                                            src="<?= clean($file_url) ?>"
                                            alt="Preview"
                                            style="
                                                width: 100px;
                                                height: 70px;
                                                object-fit: cover;
                                                border-radius: 8px;
                                            "
                                        >


                                    <?php elseif (
                                        $media_type
                                        ===
                                        "video"
                                    ): ?>


                                        <div
                                            class="d-flex
                                                   align-items-center
                                                   justify-content-center
                                                   bg-dark
                                                   text-white"
                                            style="
                                                width: 100px;
                                                height: 70px;
                                                border-radius: 8px;
                                            "
                                        >

                                            <i
                                                class="bi bi-camera-video
                                                       fs-3"
                                            ></i>

                                        </div>


                                    <?php else: ?>


                                        <div
                                            class="d-flex
                                                   align-items-center
                                                   justify-content-center
                                                   bg-light
                                                   text-muted"
                                            style="
                                                width: 100px;
                                                height: 70px;
                                                border-radius: 8px;
                                            "
                                        >

                                            <i
                                                class="bi bi-image
                                                       fs-3"
                                            ></i>

                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- TITLE -->

                                <td>

                                    <strong>

                                        <?= clean(
                                            $row["title"]
                                            ??
                                            "Tanpa Judul"
                                        ) ?>

                                    </strong>


                                    <?php if (
                                        !empty(
                                            $row["file"]
                                        )
                                    ): ?>

                                        <div
                                            class="small
                                                   text-muted
                                                   mt-1"
                                        >

                                            <?= clean(
                                                $row["file"]
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </td>



                                <!-- CATEGORY -->

                                <td>

                                    <?php if (
                                        !empty(
                                            $row["category"]
                                        )
                                    ): ?>

                                        <span
                                            class="badge
                                                   bg-light
                                                   text-dark
                                                   border"
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



                                <!-- YEAR -->

                                <td>

                                    <?= !empty(
                                        $row["year"]
                                    )
                                        ? clean(
                                            $row["year"]
                                        )
                                        : "-"
                                    ?>

                                </td>



                                <!-- TYPE -->

                                <td>


                                    <?php if (
                                        $media_type
                                        ===
                                        "video"
                                    ): ?>

                                        <span
                                            class="badge
                                                   bg-danger"
                                        >

                                            <i
                                                class="bi bi-camera-video
                                                       me-1"
                                            ></i>

                                            Video

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge
                                                   bg-primary"
                                        >

                                            <i
                                                class="bi bi-image
                                                       me-1"
                                            ></i>

                                            Foto

                                        </span>

                                    <?php endif; ?>


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
                                                'Yakin ingin menghapus media ini?'
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
                                    class="bi bi-images
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Galeri

                                </h5>


                                <p
                                    class="text-muted
                                           mb-3"
                                >

                                    Belum ada foto atau video
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

                                    Tambah Galeri

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


</body>

</html>