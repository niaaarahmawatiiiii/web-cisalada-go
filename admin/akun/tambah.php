<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// ERROR
// ==========================================

$error = "";


// ==========================================
// PROSES TAMBAH AKUN
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $name =
        trim(
            $_POST["name"] ?? ""
        );


    $username =
        trim(
            $_POST["username"] ?? ""
        );


    $password =
        $_POST["password"] ?? "";


    $password_confirm =
        $_POST["password_confirm"]
        ?? "";


    // ======================================
    // VALIDASI
    // ======================================

    if (
        empty($name) ||
        empty($username) ||
        empty($password) ||
        empty($password_confirm)
    ) {

        $error =
            "Semua field wajib diisi.";

    } elseif (
        strlen($username) < 4
    ) {

        $error =
            "Username minimal 4 karakter.";

    } elseif (
        strlen($password) < 6
    ) {

        $error =
            "Password minimal 6 karakter.";

    } elseif (
        $password !== $password_confirm
    ) {

        $error =
            "Konfirmasi password tidak cocok.";

    } else {


        // ==================================
        // CEK USERNAME
        // ==================================

        $stmt = mysqli_prepare(
            $conn,
            "
            SELECT id
            FROM users
            WHERE username = ?
            LIMIT 1
            "
        );


        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );


        mysqli_stmt_execute($stmt);


        $result =
            mysqli_stmt_get_result(
                $stmt
            );


        if (
            mysqli_num_rows($result)
            > 0
        ) {

            $error =
                "Username sudah digunakan.";

        } else {


            // ==================================
            // HASH PASSWORD
            // ==================================

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            // ==================================
            // INSERT
            // ==================================

            $role = "admin";


            $stmt = mysqli_prepare(
                $conn,
                "
                INSERT INTO users
                (
                    name,
                    username,
                    password,
                    role
                )

                VALUES (?, ?, ?, ?)
                "
            );


            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $name,
                $username,
                $hashed_password,
                $role
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {


                header(
                    "Location: index.php?success=tambah"
                );

                exit;


            } else {

                $error =
                    "Gagal membuat akun.";

            }

        }

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
        Tambah Akun - Cisalada GO
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold"
        >

            CISALADA GO

        </a>

    </div>

</nav>



<div class="container py-4">


    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>

            <h2 class="fw-bold">

                Tambah Akun Admin

            </h2>


            <p class="text-muted mb-0">

                Buat akun untuk perangkat Desa Cisalada.

            </p>

        </div>


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            Kembali

        </a>


    </div>



    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= clean($error) ?>

        </div>

    <?php endif; ?>



    <div
        class="card
               border-0
               shadow-sm"
    >


        <div class="card-body p-4">


            <form method="POST">


                <!-- NAMA -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Nama Lengkap

                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Contoh: Budi Santoso"
                        value="<?= isset(
                            $_POST["name"]
                        )
                            ? clean(
                                $_POST["name"]
                            )
                            : ""
                        ?>"
                        required
                    >

                </div>



                <!-- USERNAME -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Username

                    </label>


                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Minimal 4 karakter"
                        value="<?= isset(
                            $_POST["username"]
                        )
                            ? clean(
                                $_POST["username"]
                            )
                            : ""
                        ?>"
                        required
                    >

                </div>



                <!-- PASSWORD -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold"
                    >

                        Password

                    </label>


                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Minimal 6 karakter"
                        required
                    >

                </div>



                <!-- CONFIRM PASSWORD -->

                <div class="mb-4">

                    <label
                        class="form-label fw-semibold"
                    >

                        Konfirmasi Password

                    </label>


                    <input
                        type="password"
                        name="password_confirm"
                        class="form-control"
                        placeholder="Masukkan kembali password"
                        required
                    >

                </div>



                <!-- ROLE -->

                <div class="mb-4">

                    <label
                        class="form-label fw-semibold"
                    >

                        Role

                    </label>


                    <input
                        type="text"
                        class="form-control"
                        value="admin"
                        disabled
                    >


                    <small
                        class="text-muted"
                    >

                        Untuk tahap awal,
                        seluruh akun perangkat desa
                        menggunakan role admin.

                    </small>

                </div>



                <!-- BUTTON -->

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i
                        class="bi bi-person-plus me-1"
                    ></i>

                    Buat Akun

                </button>


                <a
                    href="index.php"
                    class="btn btn-secondary"
                >

                    Batal

                </a>


            </form>


        </div>

    </div>


</div>


</body>

</html>