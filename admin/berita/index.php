<?php

require_once "../auth.php";

require_once "../../config/database.php";

require_once "../../includes/functions.php";


// ==========================================
// AMBIL DATA BERITA
// ==========================================

$sql = "
    SELECT
        news.*,
        news_categories.name AS category_name

    FROM news

    LEFT JOIN news_categories
        ON news.category_id =
           news_categories.id

    ORDER BY
        news.created_at DESC
";


$result =
    mysqli_query(
        $conn,
        $sql
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

        Kelola Berita - Cisalada GO

    </title>


    <link
        rel="icon"
        type="image/png"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body
    class="bg-light"
>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav
    class="navbar navbar-dark bg-primary"
>

    <div
        class="container-fluid px-4"
    >


        <a
            href="../index.php"
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
                    $_SESSION[
                        "admin_name"
                    ]
                ) ?>

            </span>


            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="btn btn-light btn-sm me-2"
            >

                Dashboard

            </a>


            <a
                href="../logout.php"
                class="btn btn-outline-light btn-sm"
                onclick="
                    return confirm(
                        'Yakin ingin logout?'
                    );
                "
            >

                Logout

            </a>


        </div>

    </div>

</nav>



<!-- ==========================================
     CONTENT
========================================== -->

<div
    class="container-fluid px-4 py-4"
>


    <!-- HEADER -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>

            <h2
                class="fw-bold mb-1"
            >

                Kelola Berita

            </h2>


            <p
                class="text-muted mb-0"
            >

                Tambah, edit, dan hapus berita
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

            Tambah Berita

        </a>


    </div>



    <!-- ==========================================
         ALERT
    =========================================== -->

    <?php if (
        isset(
            $_GET["success"]
        )
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
                ===
                "tambah"
            ) {


                echo
                    "Berita berhasil ditambahkan.";


            } elseif (
                $_GET["success"]
                ===
                "edit"
            ) {


                echo
                    "Berita berhasil diperbarui.";


            } elseif (
                $_GET["success"]
                ===
                "hapus"
            ) {


                echo
                    "Berita berhasil dihapus.";


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
    =========================================== -->

    <div
        class="card
               border-0
               shadow-sm"
    >


        <div
            class="card-body p-0"
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
                                width="100"
                            >

                                Gambar

                            </th>


                            <th>

                                Judul

                            </th>


                            <th>

                                Kategori

                            </th>


                            <th>

                                Penulis

                            </th>


                            <th>

                                Tanggal

                            </th>


                            <th
                                width="180"
                            >

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


                            <tr>


                                <!-- NO -->

                                <td>

                                    <?= $no++ ?>

                                </td>



                                <!-- GAMBAR -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $row[
                                                "image"
                                            ]
                                        )
                                    ): ?>


                                        <img
                                            src="<?= BASE_URL ?>assets/uploads/berita/<?= clean(
                                                $row[
                                                    "image"
                                                ]
                                            ) ?>"
                                            width="70"
                                            height="50"
                                            style="
                                                object-fit:cover;
                                            "
                                            class="rounded"
                                            alt="Gambar berita"
                                        >


                                    <?php else: ?>


                                        <div
                                            class="bg-light
                                                   d-flex
                                                   align-items-center
                                                   justify-content-center
                                                   rounded"
                                            style="
                                                width:70px;
                                                height:50px;
                                            "
                                        >


                                            <i
                                                class="bi bi-image
                                                       text-muted"
                                            ></i>


                                        </div>


                                    <?php endif; ?>


                                </td>



                                <!-- JUDUL -->

                                <td>


                                    <strong>

                                        <?= clean(
                                            $row[
                                                "title"
                                            ]
                                        ) ?>

                                    </strong>


                                </td>



                                <!-- KATEGORI -->

                                <td>


                                    <span
                                        class="badge
                                               bg-primary"
                                    >


                                        <?= clean(
                                            $row[
                                                "category_name"
                                            ]
                                            ??
                                            "Tanpa kategori"
                                        ) ?>


                                    </span>


                                </td>



                                <!-- PENULIS -->

                                <td>

                                    <?= clean(
                                        $row[
                                            "author"
                                        ]
                                    ) ?>

                                </td>



                                <!-- TANGGAL -->

                                <td>


                                    <?= !empty(
                                        $row[
                                            "published_at"
                                        ]
                                    )
                                        ? date(
                                            "d M Y",
                                            strtotime(
                                                $row[
                                                    "published_at"
                                                ]
                                            )
                                        )
                                        : "-"
                                    ?>


                                </td>



                                <!-- AKSI -->

                                <td>


                                    <!-- EDIT -->

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



                                    <!-- HAPUS -->

                                    <form
                                        method="POST"
                                        action="hapus.php"
                                        class="d-inline"
                                        onsubmit="
                                            return confirm(
                                                'Yakin ingin menghapus berita ini?'
                                            );
                                        "
                                    >


                                        <?= csrf_field() ?>


                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $row["id"] ?>"
                                        >


                                        <button
                                            type="submit"
                                            class="btn
                                                   btn-danger
                                                   btn-sm"
                                            title="Hapus"
                                        >

                                            <i
                                                class="bi bi-trash"
                                            ></i>

                                        </button>


                                    </form>


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
                                    class="bi bi-newspaper
                                           fs-1
                                           text-muted"
                                ></i>


                                <p
                                    class="text-muted
                                           mt-3
                                           mb-0"
                                >

                                    Belum ada berita.

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