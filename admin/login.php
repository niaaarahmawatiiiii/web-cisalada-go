<?php

// ==========================================================
// CISALADA GO
// ADMIN LOGIN
// ==========================================================


// ==========================================================
// SESSION CONFIGURATION
// ==========================================================

if (
    session_status()
    ===
    PHP_SESSION_NONE
) {

    ini_set(
        "session.use_only_cookies",
        "1"
    );

    ini_set(
        "session.use_strict_mode",
        "1"
    );

    ini_set(
        "session.use_trans_sid",
        "0"
    );


    $session_cookie_secure =
        isset(
            $_SERVER["HTTPS"]
        )
        &&
        strtolower(
            $_SERVER["HTTPS"]
        )
        !==
        "off";


    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => $session_cookie_secure,
        "httponly" => true,
        "samesite" => "Lax"
    ]);


    session_start();

}


// ==========================================================
// DATABASE
// ==========================================================

require_once
    "../config/database.php";

require_once
    "../includes/functions.php";


// ==========================================================
// JIKA SUDAH LOGIN
// ==========================================================

if (
    isset(
        $_SESSION["admin_id"]
    )
) {

    header(
        "Location: "
        .
        BASE_URL
        .
        "admin/index.php"
    );

    exit;

}


// ==========================================================
// KONFIGURASI LOGIN
// ==========================================================

$max_login_attempts =
    5;


$login_lockout_seconds =
    15 * 60;



// ==========================================================
// INISIALISASI SESSION LOGIN ATTEMPT
// ==========================================================

if (
    !isset(
        $_SESSION[
            "login_attempts"
        ]
    )
) {

    $_SESSION[
        "login_attempts"
    ] =
        0;

}


if (
    !isset(
        $_SESSION[
            "login_locked_until"
        ]
    )
) {

    $_SESSION[
        "login_locked_until"
    ] =
        0;

}



// ==========================================================
// CEK LOCKOUT
// ==========================================================

$current_time =
    time();


$login_locked_until =
    (int) $_SESSION[
        "login_locked_until"
    ];


$is_login_locked =
    $login_locked_until
    >
    $current_time;


if (
    !$is_login_locked
    &&
    $login_locked_until
    >
    0
) {

    $_SESSION[
        "login_locked_until"
    ] =
        0;

}



// ==========================================================
// VARIABLE ERROR
// ==========================================================

$error =
    "";



// ==========================================================
// PROSES LOGIN
// ==========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    // ======================================================
    // CEK LOCKOUT
    // ======================================================

    if (
        $is_login_locked
    ) {


        $remaining =
            $login_locked_until
            -
            $current_time;


        $remaining_minutes =
            max(
                1,
                (int) ceil(
                    $remaining / 60
                )
            );


        $error =
            "Terlalu banyak percobaan login. "
            .
            "Silakan coba lagi dalam "
            .
            $remaining_minutes
            .
            " menit.";


    } else {


        // ==================================================
        // AMBIL INPUT
        // ==================================================

        $username =
            trim(
                $_POST[
                    "username"
                ]
                ??
                ""
            );


        $password =
            $_POST[
                "password"
            ]
            ??
            "";



        // ==================================================
        // VALIDASI INPUT
        // ==================================================

        if (
            $username === ""
            ||
            $password === ""
        ) {


            $error =
                "Username atau password salah.";


        } else {


            // ==============================================
            // CARI ADMIN
            // ==============================================

            $sql = "
                SELECT
                    *
                FROM users
                WHERE username = ?
                AND role = 'admin'
                LIMIT 1
            ";


            $stmt =
                mysqli_prepare(
                    $conn,
                    $sql
                );


            if (
                !$stmt
            ) {


                $error =
                    "Terjadi kesalahan sistem. Silakan coba lagi.";


            } else {


                mysqli_stmt_bind_param(
                    $stmt,
                    "s",
                    $username
                );


                mysqli_stmt_execute(
                    $stmt
                );


                $result =
                    mysqli_stmt_get_result(
                        $stmt
                    );



                // ==========================================
                // USER DITEMUKAN
                // ==========================================

                if (
                    mysqli_num_rows(
                        $result
                    )
                    ===
                    1
                ) {


                    $user =
                        mysqli_fetch_assoc(
                            $result
                        );


                    // ======================================
                    // VERIFIKASI PASSWORD
                    // ======================================

                    $password_valid =
                        password_verify(
                            $password,
                            $user[
                                "password"
                            ]
                        );


                } else {


                    $password_valid =
                        false;

                }



                mysqli_stmt_close(
                    $stmt
                );



                // ==========================================
                // LOGIN BERHASIL
                // ==========================================

                if (
                    $password_valid
                ) {


                    // --------------------------------------
                    // REGENERATE SESSION ID
                    // --------------------------------------

                    session_regenerate_id(
                        true
                    );


                    // --------------------------------------
                    // SESSION ADMIN
                    // --------------------------------------

                    $_SESSION[
                        "admin_id"
                    ] =
                        $user[
                            "id"
                        ];


                    $_SESSION[
                        "admin_name"
                    ] =
                        $user[
                            "name"
                        ];


                    $_SESSION[
                        "admin_username"
                    ] =
                        $user[
                            "username"
                        ];


                    $_SESSION[
                        "admin_role"
                    ] =
                        $user[
                            "role"
                        ];


                    // --------------------------------------
                    // SESSION ACTIVITY
                    // --------------------------------------

                    $_SESSION[
                        "last_activity"
                    ] =
                        time();


                    $_SESSION[
                        "session_regenerated_at"
                    ] =
                        time();



                    // --------------------------------------
                    // RESET LOGIN ATTEMPT
                    // --------------------------------------

                    $_SESSION[
                        "login_attempts"
                    ] =
                        0;


                    $_SESSION[
                        "login_locked_until"
                    ] =
                        0;



                    // --------------------------------------
                    // RESET CSRF
                    // --------------------------------------

                    $_SESSION[
                        "csrf_token"
                    ] =
                        bin2hex(
                            random_bytes(32)
                        );



                    // --------------------------------------
                    // REDIRECT
                    // --------------------------------------

                    header(
                        "Location: "
                        .
                        BASE_URL
                        .
                        "admin/index.php"
                    );


                    exit;


                } else {


                    // ======================================
                    // LOGIN GAGAL
                    // ======================================

                    $_SESSION[
                        "login_attempts"
                    ]++;


                    $attempts =
                        (int) $_SESSION[
                            "login_attempts"
                        ];



                    // ======================================
                    // AKTIFKAN LOCKOUT
                    // ======================================

                    if (
                        $attempts
                        >=
                        $max_login_attempts
                    ) {


                        $_SESSION[
                            "login_locked_until"
                        ] =
                            time()
                            +
                            $login_lockout_seconds;


                        $_SESSION[
                            "login_attempts"
                        ] =
                            0;


                        $error =
                            "Terlalu banyak percobaan login. "
                            .
                            "Silakan coba lagi dalam 15 menit.";


                    } else {


                        $remaining_attempts =
                            $max_login_attempts
                            -
                            $attempts;


                        $error =
                            "Username atau password salah.";


                        /*
                         * Jangan memberitahu apakah username
                         * atau password yang salah.
                         */

                    }

                }

            }

        }

    }

}

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
        Login Admin - Cisalada GO
    </title>


    <!-- FAVICON -->

    <link
        rel="icon"
        type="image/png"
        sizes="512x512"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >


    <link
        rel="shortcut icon"
        type="image/png"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- BOOTSTRAP ICONS -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {

            background-color:
                #f5f7fa;

        }


        .login-wrapper {

            min-height:
                100vh;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .login-card {

            width:
                100%;

            max-width:
                430px;

            border:
                none;

            border-radius:
                16px;

        }


        .login-icon {

            width:
                70px;

            height:
                70px;

            border-radius:
                50%;

            background-color:
                #e8f0ff;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            margin:
                0 auto;

        }


        .lock-alert {

            font-size:
                0.9rem;

        }

    </style>


</head>


<body>


<div
    class="container"
>


    <div
        class="login-wrapper"
    >


        <div
            class="card
                   login-card
                   shadow-sm"
        >


            <div
                class="card-body
                       p-4
                       p-md-5"
            >


                <!-- =====================================
                     ICON
                ====================================== -->

                <div
                    class="login-icon
                           mb-4"
                >

                    <i
                        class="bi
                               bi-shield-lock-fill
                               fs-2
                               text-primary"
                    ></i>

                </div>



                <!-- =====================================
                     TITLE
                ====================================== -->

                <div
                    class="text-center
                           mb-4"
                >

                    <h3
                        class="fw-bold"
                    >

                        Login Admin

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Panel Administrasi
                        Desa Cisalada

                    </p>

                </div>



                <!-- =====================================
                     ERROR
                ====================================== -->

                <?php if (
                    !empty(
                        $error
                    )
                ): ?>


                    <div
                        class="alert
                               alert-danger
                               lock-alert"
                    >

                        <i
                            class="bi
                                   bi-exclamation-circle
                                   me-1"
                        ></i>


                        <?= clean(
                            $error
                        ) ?>


                    </div>


                <?php endif; ?>



                <!-- =====================================
                     FORM
                ====================================== -->

                <form
                    method="POST"
                >


                    <!-- USERNAME -->

                    <div
                        class="mb-3"
                    >

                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Username

                        </label>


                        <div
                            class="input-group"
                        >

                            <span
                                class="input-group-text"
                            >

                                <i
                                    class="bi bi-person"
                                ></i>

                            </span>


                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                maxlength="50"
                                required
                                <?= $is_login_locked
                                    ? "disabled"
                                    : ""
                                ?>
                            >

                        </div>

                    </div>



                    <!-- PASSWORD -->

                    <div
                        class="mb-4"
                    >

                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Password

                        </label>


                        <div
                            class="input-group"
                        >

                            <span
                                class="input-group-text"
                            >

                                <i
                                    class="bi bi-lock"
                                ></i>

                            </span>


                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                                <?= $is_login_locked
                                    ? "disabled"
                                    : ""
                                ?>
                            >


                            <button
                                type="button"
                                class="btn
                                       btn-outline-secondary"
                                onclick="togglePassword()"
                                <?= $is_login_locked
                                    ? "disabled"
                                    : ""
                                ?>
                            >

                                <i
                                    class="bi bi-eye"
                                    id="passwordIcon"
                                ></i>

                            </button>

                        </div>

                    </div>



                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="btn
                               btn-primary
                               w-100
                               py-2"
                        <?= $is_login_locked
                            ? "disabled"
                            : ""
                        ?>
                    >

                        <i
                            class="bi
                                   bi-box-arrow-in-right
                                   me-1"
                        ></i>

                        Login

                    </button>


                </form>



                <!-- =====================================
                     BACK PUBLIC
                ====================================== -->

                <div
                    class="text-center
                           mt-4"
                >

                    <a
                        href="<?= BASE_URL ?>index.php"
                        class="text-decoration-none"
                    >

                        <i
                            class="bi
                                   bi-arrow-left"
                        ></i>

                        Kembali ke Website

                    </a>

                </div>


            </div>

        </div>

    </div>

</div>



<script>

function togglePassword()
{

    const password =
        document.getElementById(
            "password"
        );


    const icon =
        document.getElementById(
            "passwordIcon"
        );


    if (
        password.type
        ===
        "password"
    ) {


        password.type =
            "text";


        icon.className =
            "bi bi-eye-slash";


    } else {


        password.type =
            "password";


        icon.className =
            "bi bi-eye";

    }

}

</script>


</body>

</html>