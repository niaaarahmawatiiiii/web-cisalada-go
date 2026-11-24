<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// AMBIL DATA PEMERINTAHAN
// ==========================================

$query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        name,
        position,
        category,
        photo,
        description,
        display_order,
        is_active,
        created_at
    FROM government_officials
    ORDER BY
        display_order ASC,
        id ASC
    "
);


// ==========================================
// PESAN
// ==========================================

$success = "";

if (
    isset($_GET["success"])
) {

    if (
        $_GET["success"] === "tambah"
    ) {

        $success =
            "Data perangkat desa berhasil ditambahkan.";

    } elseif (
        $_GET["success"] === "edit"
    ) {

        $success =
            "Data perangkat desa berhasil diperbarui.";

    } elseif (
        $_GET["success"] === "hapus"
    ) {

        $success =
            "Data perangkat desa berhasil dihapus.";

    }

}

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
        Pemerintahan Desa - Cisalada GO
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


<!-- ==========================================
     NAVBAR
========================================== -->

<nav
    class="navbar
           navbar-dark
           bg-primary"
>

    <div
        class="container"
    >


        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand
                   fw-bold"
        >

            CISALADA GO

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

</nav>



<!-- ==========================================
     CONTENT
========================================== -->

<div
    class="container
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

                Pemerintahan Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Kelola struktur dan perangkat
                Pemerintahan Desa Cisalada.

            </p>

        </div>


        <div
            class="d-flex
                   gap-2"
        >


            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="btn
                       btn-secondary"
            >

                <i
                    class="bi bi-arrow-left
                           me-1"
                ></i>

                Dashboard

            </a>


            <a
                href="tambah.php"
                class="btn
                       btn-primary"
            >

                <i
                    class="bi bi-plus-lg
                           me-1"
                ></i>

                Tambah Perangkat

            </a>


        </div>


    </div>



    <!-- ==========================================
         SUCCESS
    =========================================== -->

    <?php if (
        !empty($success)
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

            <?= clean($success) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>



    <!-- ==========================================
         DATA
    =========================================== -->

    <div
        class="card
               border-0
               shadow-sm"
    >


        <div
            class="card-body
                   p-4"
        >


            <?php if (
                $query
                &&
                mysqli_num_rows(
                    $query
                ) > 0
            ): ?>


                <div
                    class="table-responsive"
                >

                    <table
                        class="table
                               align-middle
                               mb-0"
                    >


                        <thead>

                            <tr>

                                <th>
                                    No
                                </th>

                                <th>
                                    Foto
                                </th>

                                <th>
                                    Nama
                                </th>

                                <th>
                                    Jabatan
                                </th>

                                <th>
                                    Kategori
                                </th>

                                <th>
                                    Urutan
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    class="text-end"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php

                            $no = 1;

                            ?>


                            <?php while (
                                $row =
                                    mysqli_fetch_assoc(
                                        $query
                                    )
                            ): ?>


                                <tr>


                                    <!-- NO -->

                                    <td>

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
                                                src="<?= BASE_URL ?>assets/uploads/pemerintahan/<?= clean($row["photo"]) ?>"
                                                alt="<?= clean($row["name"]) ?>"
                                                style="
                                                    width: 65px;
                                                    height: 65px;
                                                    object-fit: cover;
                                                    border-radius: 12px;
                                                "
                                            >


                                        <?php else: ?>


                                            <div
                                                style="
                                                    width: 65px;
                                                    height: 65px;
                                                    border-radius: 12px;
                                                    background: #f1f3f5;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                "
                                            >

                                                <i
                                                    class="bi bi-person
                                                           fs-3
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

                                    </td>



                                    <!-- JABATAN -->

                                    <td>

                                        <?= clean(
                                            $row["position"]
                                        ) ?>

                                    </td>



                                    <!-- KATEGORI -->

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



                                    <!-- URUTAN -->

                                    <td>

                                        <?= (int)
                                            $row[
                                                "display_order"
                                            ] ?>

                                    </td>



                                    <!-- STATUS -->

                                    <td>


                                        <?php if (
                                            (int)
                                            $row[
                                                "is_active"
                                            ]
                                            ===
                                            1
                                        ): ?>


                                            <span
                                                class="badge
                                                       bg-success"
                                            >

                                                Aktif

                                            </span>


                                        <?php else: ?>


                                            <span
                                                class="badge
                                                       bg-secondary"
                                            >

                                                Tidak Aktif

                                            </span>


                                        <?php endif; ?>


                                    </td>



                                    <!-- AKSI -->

                                    <td
                                        class="text-end"
                                    >


                                        <a
                                            href="edit.php?id=<?= (int) $row["id"] ?>"
                                            class="btn
                                                   btn-sm
                                                   btn-warning"
                                        >

                                            <i
                                                class="bi bi-pencil"
                                            ></i>

                                        </a>


                                        <a
                                            href="hapus.php?id=<?= (int) $row["id"] ?>"
                                            class="btn
                                                   btn-sm
                                                   btn-danger"
                                            onclick="
                                                return confirm(
                                                    'Yakin ingin menghapus data perangkat ini?'
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


                        </tbody>


                    </table>

                </div>


            <?php else: ?>


                <!-- EMPTY -->

                <div
                    class="text-center
                           py-5"
                >


                    <i
                        class="bi bi-people
                               fs-1
                               text-muted"
                    ></i>


                    <h5
                        class="fw-bold
                               mt-3"
                    >

                        Belum Ada Data Pemerintahan

                    </h5>


                    <p
                        class="text-muted"
                    >

                        Tambahkan perangkat pemerintahan
                        desa untuk ditampilkan di website.

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

                        Tambah Perangkat

                    </a>


                </div>


            <?php endif; ?>


        </div>

    </div>


</div>


</body>

</html>