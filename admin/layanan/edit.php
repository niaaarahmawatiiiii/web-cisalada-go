<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$id =
    isset(
        $_GET["id"]
    )
    ?
    (int)
    $_GET["id"]
    :
    0;


if (
    $id <= 0
) {

    header(
        "Location: index.php?error=id"
    );

    exit;

}


// ======================================================
// AMBIL DATA
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            category,
            requirements,
            service_procedure,
            service_time,
            cost,
            location
        FROM services
        WHERE
            id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: index.php?error=database"
    );

    exit;

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute(
    $stmt
);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


$service =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$service
) {

    header(
        "Location: index.php?error=not_found"
    );

    exit;

}


// ======================================================
// DATA FORM
// ======================================================

$name =
    $service["name"]
    ??
    "";


$category =
    $service["category"]
    ??
    "";


$requirements =
    $service["requirements"]
    ??
    "";


$service_procedure =
    $service["service_procedure"]
    ??
    "";


$service_time =
    $service["service_time"]
    ??
    "";


$cost =
    $service["cost"]
    ??
    "";


$location =
    $service["location"]
    ??
    "";


$error =
    "";


// ======================================================
// PROSES UPDATE
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $name =
        trim(
            $_POST["name"]
            ??
            ""
        );


    $category =
        trim(
            $_POST["category"]
            ??
            ""
        );


    $requirements =
        trim(
            $_POST["requirements"]
            ??
            ""
        );


    $service_procedure =
        trim(
            $_POST["service_procedure"]
            ??
            ""
        );


    $service_time =
        trim(
            $_POST["service_time"]
            ??
            ""
        );


    $cost =
        trim(
            $_POST["cost"]
            ??
            ""
        );


    $location =
        trim(
            $_POST["location"]
            ??
            ""
        );



    // ==================================================
    // VALIDASI
    // ==================================================

    if (
        $name === ""
    ) {

        $error =
            "Nama layanan wajib diisi.";

    }



    // ==================================================
    // UPDATE
    // ==================================================

    if (
        empty($error)
    ) {


        $stmt =
            mysqli_prepare(
                $conn,
                "
                UPDATE services

                SET
                    name = ?,
                    category = ?,
                    requirements = ?,
                    service_procedure = ?,
                    service_time = ?,
                    cost = ?,
                    location = ?

                WHERE
                    id = ?
                "
            );


        if (
            !$stmt
        ) {

            $error =
                "Gagal mempersiapkan database: "
                .
                mysqli_error(
                    $conn
                );

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "sssssssi",
                $name,
                $category,
                $requirements,
                $service_procedure,
                $service_time,
                $cost,
                $location,
                $id
            );


            if (
                mysqli_stmt_execute(
                    $stmt
                )
            ) {


                mysqli_stmt_close(
                    $stmt
                );


                header(
                    "Location: index.php?success=edit"
                );


                exit;

            }


            $error =
                "Gagal memperbarui layanan: "
                .
                mysqli_stmt_error(
                    $stmt
                );


            mysqli_stmt_close(
                $stmt
            );

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
        Edit Layanan - Cisalada GO
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


        .form-card {

            border:
                0;

            border-radius:
                16px;

        }


        textarea {

            resize:
                vertical;

        }

    </style>

</head>


<body>


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

</nav>



<div
    class="container
           py-4"
>


    <div
        class="mb-4"
    >


        <a
            href="index.php"
            class="text-decoration-none
                   text-muted"
        >


            <i
                class="bi bi-arrow-left
                       me-1"
            ></i>


            Kembali ke Layanan


        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Edit Layanan

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Perbarui informasi layanan
            Desa Cisalada.

        </p>


    </div>



    <?php if (
        !empty($error)
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


            <?= clean(
                $error
            ) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <div
        class="card
               form-card
               shadow-sm"
    >


        <div
            class="card-body
                   p-4
                   p-lg-5"
        >


            <form
                method="POST"
            >


                <!-- NAMA -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Nama Layanan

                        <span
                            class="text-danger"
                        >

                            *

                        </span>


                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= clean(
                            $name
                        ) ?>"
                        required
                    >


                </div>



                <!-- KATEGORI -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Kategori

                    </label>


                    <select
                        name="category"
                        class="form-select"
                    >


                        <option
                            value=""
                        >

                            -- Pilih Kategori --

                        </option>


                        <option
                            value="Administrasi Kependudukan"
                            <?= $category === "Administrasi Kependudukan"
                                ? "selected"
                                : "" ?>
                        >

                            Administrasi Kependudukan

                        </option>


                        <option
                            value="Surat Keterangan"
                            <?= $category === "Surat Keterangan"
                                ? "selected"
                                : "" ?>
                        >

                            Surat Keterangan

                        </option>


                        <option
                            value="Surat Pengantar"
                            <?= $category === "Surat Pengantar"
                                ? "selected"
                                : "" ?>
                        >

                            Surat Pengantar

                        </option>


                        <option
                            value="Sosial"
                            <?= $category === "Sosial"
                                ? "selected"
                                : "" ?>
                        >

                            Sosial

                        </option>


                        <option
                            value="Usaha"
                            <?= $category === "Usaha"
                                ? "selected"
                                : "" ?>
                        >

                            Usaha

                        </option>


                        <option
                            value="Lainnya"
                            <?= $category === "Lainnya"
                                ? "selected"
                                : "" ?>
                        >

                            Lainnya

                        </option>


                    </select>


                </div>



                <!-- PERSYARATAN -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Persyaratan

                    </label>


                    <textarea
                        name="requirements"
                        class="form-control"
                        rows="7"
                    ><?= clean(
                        $requirements
                    ) ?></textarea>


                    <div
                        class="form-text"
                    >

                        Tulis satu persyaratan
                        per baris.

                    </div>


                </div>



                <!-- PROSEDUR -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Prosedur

                    </label>


                    <textarea
                        name="service_procedure"
                        class="form-control"
                        rows="7"
                    ><?= clean(
                        $service_procedure
                    ) ?></textarea>


                    <div
                        class="form-text"
                    >

                        Tulis tahapan pelayanan
                        per baris.

                    </div>


                </div>



                <!-- WAKTU -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Waktu Pelayanan

                    </label>


                    <input
                        type="text"
                        name="service_time"
                        class="form-control"
                        value="<?= clean(
                            $service_time
                        ) ?>"
                    >


                </div>



                <!-- BIAYA -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Biaya

                    </label>


                    <input
                        type="text"
                        name="cost"
                        class="form-control"
                        value="<?= clean(
                            $cost
                        ) ?>"
                        placeholder="Contoh: Gratis atau Rp 10.000"
                    >


                </div>



                <!-- LOKASI -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Lokasi Pelayanan

                    </label>


                    <input
                        type="text"
                        name="location"
                        class="form-control"
                        value="<?= clean(
                            $location
                        ) ?>"
                        placeholder="Contoh: Kantor Desa Cisalada"
                    >


                </div>



                <!-- BUTTON -->

                <div
                    class="d-flex
                           gap-2"
                >


                    <a
                        href="index.php"
                        class="btn
                               btn-secondary"
                    >

                        <i
                            class="bi bi-arrow-left
                                   me-1"
                        ></i>


                        Batal

                    </a>


                    <button
                        type="submit"
                        class="btn
                               btn-primary"
                    >

                        <i
                            class="bi bi-save
                                   me-1"
                        ></i>


                        Simpan Perubahan

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>