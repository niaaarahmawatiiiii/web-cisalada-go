<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// AMBIL DATA AKUN
// ==========================================

$result = mysqli_query(
    $conn,
    "
    SELECT
        id,
        name,
        username,
        role,
        created_at

    FROM users

    ORDER BY created_at DESC
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
        Akun Admin - Cisalada GO
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

<nav class="navbar navbar-dark bg-primary">

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
                    $_SESSION["admin_name"]
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
            >

                Logout

            </a>


        </div>

    </div>

</nav>



<!-- ==========================================
     CONTENT
========================================== -->

<div class="container-fluid px-4 py-4">


    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>

            <h2 class="fw-bold mb-1">

                Akun Admin

            </h2>


            <p class="text-muted mb-0">

                Kelola akun perangkat Desa Cisalada.

            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i
                class="bi bi-person-plus me-1"
            ></i>

            Tambah Akun

        </a>


    </div>



    <!-- ALERT -->

    <?php if (
        isset($_GET["success"])
    ): ?>

        <div
            class="alert alert-success
                   alert-dismissible fade show"
        >

            <?php

            if (
                $_GET["success"]
                === "tambah"
            ) {

                echo "Akun berhasil dibuat.";

            } elseif (
                $_GET["success"]
                === "hapus"
            ) {

                echo "Akun berhasil dihapus.";

            }

            ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>



    <!-- TABLE -->

    <div
        class="card
               border-0
               shadow-sm"
    >

        <div class="card-body p-0">


            <div class="table-responsive">


                <table
                    class="table
                           table-hover
                           align-middle
                           mb-0"
                >


                    <thead class="table-light">

                        <tr>

                            <th width="70">
                                No
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Username
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Dibuat
                            </th>

                            <th width="100">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    ?>


                    <?php while (
                        $user =
                            mysqli_fetch_assoc(
                                $result
                            )
                    ): ?>


                        <tr>


                            <td>

                                <?= $no++ ?>

                            </td>


                            <td>

                                <strong>

                                    <?= clean(
                                        $user["name"]
                                    ) ?>

                                </strong>

                            </td>


                            <td>

                                <?= clean(
                                    $user["username"]
                                ) ?>

                            </td>


                            <td>

                                <span
                                    class="badge
                                           bg-primary"
                                >

                                    <?= clean(
                                        $user["role"]
                                    ) ?>

                                </span>

                            </td>


                            <td>

                                <?= date(
                                    "d M Y H:i",
                                    strtotime(
                                        $user["created_at"]
                                    )
                                ) ?>

                            </td>


                            <td>


                                <?php if (
                                    $user["id"]
                                    !=
                                    $_SESSION[
                                        "admin_id"
                                    ]
                                ): ?>


                                    <a
                                        href="hapus.php?id=<?= $user["id"] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm(
                                            'Yakin ingin menghapus akun ini?'
                                        );"
                                    >

                                        <i
                                            class="bi bi-trash"
                                        ></i>

                                    </a>


                                <?php else: ?>


                                    <span
                                        class="text-muted"
                                        title="Akun yang sedang digunakan tidak dapat dihapus."
                                    >

                                        <i
                                            class="bi bi-lock"
                                        ></i>

                                    </span>


                                <?php endif; ?>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


</body>

</html>