<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =====================================================
// AMBIL DATA PROFIL
// =====================================================

$profile = null;


$profile_result =
    mysqli_query(
        $conn,
        "
        SELECT *
        FROM village_profile
        ORDER BY id ASC
        LIMIT 1
        "
    );


if (
    $profile_result
) {

    $profile =
        mysqli_fetch_assoc(
            $profile_result
        );

}


// =====================================================
// AMBIL DATA DASAR DESA
// =====================================================

$village_data = null;


$village_result =
    mysqli_query(
        $conn,
        "
        SELECT *
        FROM village_data
        ORDER BY id ASC
        LIMIT 1
        "
    );


if (
    $village_result
) {

    $village_data =
        mysqli_fetch_assoc(
            $village_result
        );

}


// =====================================================
// NILAI FORM - DATA DASAR DESA
// =====================================================

$village_name =
    $village_data[
        "village_name"
    ]
    ??
    (
        $profile[
            "village_name"
        ]
        ??
        ""
    );


$district =
    $village_data[
        "district"
    ]
    ??
    "";


$regency =
    $village_data[
        "regency"
    ]
    ??
    "";


$province =
    $village_data[
        "province"
    ]
    ??
    "";


$area =
    $village_data[
        "area"
    ]
    ??
    "";


$classification =
    $village_data[
        "classification"
    ]
    ??
    "";


$established_year =
    $village_data[
        "established_year"
    ]
    ??
    "";


// =====================================================
// NILAI FORM - PROFIL
// =====================================================

$history =
    $profile[
        "history"
    ]
    ??
    "";


$vision =
    $profile[
        "vision"
    ]
    ??
    "";


$mission =
    $profile[
        "mission"
    ]
    ??
    "";


$geography =
    $profile[
        "geography"
    ]
    ??
    "";


$address =
    $profile[
        "address"
    ]
    ??
    "";


$email =
    $profile[
        "email"
    ]
    ??
    "";


$phone =
    $profile[
        "phone"
    ]
    ??
    "";


$error =
    "";


// =====================================================
// PROSES SIMPAN
// =====================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    // =================================================
    // DATA DASAR DESA
    // =================================================

    $village_name =
        trim(
            $_POST[
                "village_name"
            ]
            ??
            ""
        );


    $district =
        trim(
            $_POST[
                "district"
            ]
            ??
            ""
        );


    $regency =
        trim(
            $_POST[
                "regency"
            ]
            ??
            ""
        );


    $province =
        trim(
            $_POST[
                "province"
            ]
            ??
            ""
        );


    $area =
        trim(
            $_POST[
                "area"
            ]
            ??
            ""
        );


    $classification =
        trim(
            $_POST[
                "classification"
            ]
            ??
            ""
        );


    $established_year =
        trim(
            $_POST[
                "established_year"
            ]
            ??
            ""
        );


    // =================================================
    // PROFIL
    // =================================================

    $history =
        trim(
            $_POST[
                "history"
            ]
            ??
            ""
        );


    $vision =
        trim(
            $_POST[
                "vision"
            ]
            ??
            ""
        );


    $mission =
        trim(
            $_POST[
                "mission"
            ]
            ??
            ""
        );


    $geography =
        trim(
            $_POST[
                "geography"
            ]
            ??
            ""
        );


    $address =
        trim(
            $_POST[
                "address"
            ]
            ??
            ""
        );


    $email =
        trim(
            $_POST[
                "email"
            ]
            ??
            ""
        );


    $phone =
        trim(
            $_POST[
                "phone"
            ]
            ??
            ""
        );



    // =================================================
    // VALIDASI NAMA
    // =================================================

    if (
        $village_name === ""
    ) {

        $error =
            "Nama desa wajib diisi.";

    }



    // =================================================
    // VALIDASI TAHUN
    // =================================================

    if (
        empty($error)
        &&
        $established_year !== ""
    ) {


        if (
            !preg_match(
                '/^[0-9]{4}$/',
                $established_year
            )
        ) {

            $error =
                "Tahun berdiri harus berupa 4 digit.";

        }

    }



    // =================================================
    // VALIDASI EMAIL
    // =================================================

    if (
        empty($error)
        &&
        $email !== ""
        &&
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Format email tidak valid.";

    }



    // =================================================
    // NORMALISASI TAHUN
    // =================================================

    $established_year_db =
        $established_year !== ""
        ?
        $established_year
        :
        null;



    // =================================================
    // SIMPAN DATA DESA
    // =================================================

    if (
        empty($error)
    ) {


        if (
            $village_data
        ) {


            $village_stmt =
                mysqli_prepare(
                    $conn,
                    "
                    UPDATE village_data

                    SET
                        village_name = ?,
                        district = ?,
                        regency = ?,
                        province = ?,
                        area = ?,
                        classification = ?,
                        established_year = ?

                    WHERE id = ?
                    "
                );


            if (
                !$village_stmt
            ) {

                $error =
                    "Gagal menyiapkan data desa: "
                    .
                    mysqli_error(
                        $conn
                    );

            } else {


                mysqli_stmt_bind_param(
                    $village_stmt,
                    "sssssssi",
                    $village_name,
                    $district,
                    $regency,
                    $province,
                    $area,
                    $classification,
                    $established_year_db,
                    $village_data[
                        "id"
                    ]
                );


                if (
                    !mysqli_stmt_execute(
                        $village_stmt
                    )
                ) {

                    $error =
                        "Gagal memperbarui data desa: "
                        .
                        mysqli_stmt_error(
                            $village_stmt
                        );

                }


                mysqli_stmt_close(
                    $village_stmt
                );

            }

        } else {


            $village_stmt =
                mysqli_prepare(
                    $conn,
                    "
                    INSERT INTO village_data
                    (
                        village_name,
                        district,
                        regency,
                        province,
                        area,
                        classification,
                        established_year
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                    "
                );


            if (
                !$village_stmt
            ) {

                $error =
                    "Gagal menyiapkan data desa: "
                    .
                    mysqli_error(
                        $conn
                    );

            } else {


                mysqli_stmt_bind_param(
                    $village_stmt,
                    "sssssss",
                    $village_name,
                    $district,
                    $regency,
                    $province,
                    $area,
                    $classification,
                    $established_year_db
                );


                if (
                    !mysqli_stmt_execute(
                        $village_stmt
                    )
                ) {

                    $error =
                        "Gagal menambahkan data desa: "
                        .
                        mysqli_stmt_error(
                            $village_stmt
                        );

                }


                mysqli_stmt_close(
                    $village_stmt
                );

            }

        }

    }



    // =================================================
    // SIMPAN PROFIL DESA
    // =================================================

    if (
        empty($error)
    ) {


        if (
            $profile
        ) {


            $profile_stmt =
                mysqli_prepare(
                    $conn,
                    "
                    UPDATE village_profile

                    SET
                        village_name = ?,
                        history = ?,
                        vision = ?,
                        mission = ?,
                        geography = ?,
                        address = ?,
                        email = ?,
                        phone = ?

                    WHERE id = ?
                    "
                );


            if (
                !$profile_stmt
            ) {

                $error =
                    "Gagal menyiapkan profil desa: "
                    .
                    mysqli_error(
                        $conn
                    );

            } else {


                mysqli_stmt_bind_param(
                    $profile_stmt,
                    "ssssssssi",
                    $village_name,
                    $history,
                    $vision,
                    $mission,
                    $geography,
                    $address,
                    $email,
                    $phone,
                    $profile[
                        "id"
                    ]
                );


                if (
                    !mysqli_stmt_execute(
                        $profile_stmt
                    )
                ) {

                    $error =
                        "Gagal memperbarui profil desa: "
                        .
                        mysqli_stmt_error(
                            $profile_stmt
                        );

                }


                mysqli_stmt_close(
                    $profile_stmt
                );

            }

        } else {


            $profile_stmt =
                mysqli_prepare(
                    $conn,
                    "
                    INSERT INTO village_profile
                    (
                        village_name,
                        history,
                        vision,
                        mission,
                        geography,
                        address,
                        email,
                        phone
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                    "
                );


            if (
                !$profile_stmt
            ) {

                $error =
                    "Gagal menyiapkan profil desa: "
                    .
                    mysqli_error(
                        $conn
                    );

            } else {


                mysqli_stmt_bind_param(
                    $profile_stmt,
                    "ssssssss",
                    $village_name,
                    $history,
                    $vision,
                    $mission,
                    $geography,
                    $address,
                    $email,
                    $phone
                );


                if (
                    !mysqli_stmt_execute(
                        $profile_stmt
                    )
                ) {

                    $error =
                        "Gagal menambahkan profil desa: "
                        .
                        mysqli_stmt_error(
                            $profile_stmt
                        );

                }


                mysqli_stmt_close(
                    $profile_stmt
                );

            }

        }

    }



    // =================================================
    // BERHASIL
    // =================================================

    if (
        empty($error)
    ) {


        header(
            "Location: index.php?success=edit"
        );


        exit;

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

        Edit Profil Desa -
        Cisalada GO

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


        .card {

            border:
                0;

            border-radius:
                16px;

        }


        textarea {

            resize:
                vertical;

        }


        .section-title {

            font-size:
                1.05rem;

            font-weight:
                700;

            margin-bottom:
                18px;

        }


        .section-box {

            background:
                #f8f9fa;

            border-radius:
                12px;

            padding:
                20px;

            margin-bottom:
                30px;

        }


        .form-text {

            font-size:
                0.8rem;

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

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


        <div>


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



<!-- =====================================================
     CONTENT
===================================================== -->

<div
    class="container
           py-4"
>


    <!-- HEADER -->

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

            Kembali ke Profil Desa

        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Edit Profil Desa

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Kelola seluruh informasi yang tampil
            pada halaman Profil Desa.

        </p>


    </div>



    <!-- ERROR -->

    <?php if (
        !empty(
            $error
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



                <!-- =================================================
                     INFORMASI DASAR DESA
                ================================================== -->

                <div
                    class="section-box"
                >


                    <div
                        class="section-title"
                    >

                        <i
                            class="bi bi-building
                                   text-primary
                                   me-1"
                        ></i>


                        Informasi Dasar Desa

                    </div>


                    <div
                        class="row
                               g-3"
                    >


                        <!-- NAMA -->

                        <div
                            class="col-12"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Nama Desa

                                <span
                                    class="text-danger"
                                >

                                    *

                                </span>

                            </label>


                            <input
                                type="text"
                                name="village_name"
                                class="form-control"
                                value="<?= clean(
                                    $village_name
                                ) ?>"
                                required
                            >

                        </div>



                        <!-- KECAMATAN -->

                        <div
                            class="col-md-6"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Kecamatan

                            </label>


                            <input
                                type="text"
                                name="district"
                                class="form-control"
                                value="<?= clean(
                                    $district
                                ) ?>"
                                placeholder="Contoh: Cigombong"
                            >

                        </div>



                        <!-- KABUPATEN -->

                        <div
                            class="col-md-6"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Kabupaten

                            </label>


                            <input
                                type="text"
                                name="regency"
                                class="form-control"
                                value="<?= clean(
                                    $regency
                                ) ?>"
                                placeholder="Contoh: Bogor"
                            >

                        </div>



                        <!-- PROVINSI -->

                        <div
                            class="col-md-6"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Provinsi

                            </label>


                            <input
                                type="text"
                                name="province"
                                class="form-control"
                                value="<?= clean(
                                    $province
                                ) ?>"
                                placeholder="Contoh: Jawa Barat"
                            >

                        </div>



                        <!-- LUAS -->

                        <div
                            class="col-md-6"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Luas Wilayah

                            </label>


                            <input
                                type="text"
                                name="area"
                                class="form-control"
                                value="<?= clean(
                                    $area
                                ) ?>"
                                placeholder="Contoh: 214.18 Ha"
                            >


                            <div
                                class="form-text"
                            >

                                Contoh:
                                214.18 Ha

                            </div>


                        </div>



                        <!-- KLASIFIKASI -->

                        <div
                            class="col-md-6"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Klasifikasi Desa

                            </label>


                            <input
                                type="text"
                                name="classification"
                                class="form-control"
                                value="<?= clean(
                                    $classification
                                ) ?>"
                                placeholder="Contoh: Desa Mandiri"
                            >

                        </div>



                        <!-- TAHUN -->

                        <div
                            class="col-md-6"
                        >

                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Tahun Berdiri

                            </label>


                            <input
                                type="number"
                                name="established_year"
                                class="form-control"
                                value="<?= clean(
                                    $established_year
                                ) ?>"
                                min="1000"
                                max="9999"
                                placeholder="Contoh: 1984"
                            >

                        </div>


                    </div>


                </div>



                <!-- =================================================
                     SEJARAH
                ================================================== -->

                <div
                    class="section-box"
                >


                    <div
                        class="section-title"
                    >

                        <i
                            class="bi bi-clock-history
                                   text-primary
                                   me-1"
                        ></i>


                        Sejarah Desa

                    </div>


                    <textarea
                        name="history"
                        class="form-control"
                        rows="8"
                        placeholder="Tulis sejarah atau uraian mengenai Desa Cisalada."
                    ><?= clean(
                        $history
                    ) ?></textarea>


                </div>



                <!-- =================================================
                     VISI
                ================================================== -->

                <div
                    class="section-box"
                >


                    <div
                        class="section-title"
                    >

                        <i
                            class="bi bi-bullseye
                                   text-primary
                                   me-1"
                        ></i>


                        Visi Desa

                    </div>


                    <textarea
                        name="vision"
                        class="form-control"
                        rows="5"
                        placeholder="Tuliskan visi Desa Cisalada."
                    ><?= clean(
                        $vision
                    ) ?></textarea>


                </div>



                <!-- =================================================
                     MISI
                ================================================== -->

                <div
                    class="section-box"
                >


                    <div
                        class="section-title"
                    >

                        <i
                            class="bi bi-list-check
                                   text-primary
                                   me-1"
                        ></i>


                        Misi Desa

                    </div>


                    <textarea
                        name="mission"
                        class="form-control"
                        rows="7"
                        placeholder="Tuliskan misi Desa Cisalada."
                    ><?= clean(
                        $mission
                    ) ?></textarea>


                </div>



                <!-- =================================================
                     GEOGRAFI
                ================================================== -->

                <div
                    class="section-box"
                >


                    <div
                        class="section-title"
                    >

                        <i
                            class="bi bi-map
                                   text-primary
                                   me-1"
                        ></i>


                        Kondisi Geografis

                    </div>


                    <textarea
                        name="geography"
                        class="form-control"
                        rows="8"
                        placeholder="Tuliskan kondisi geografis, batas wilayah, topografi, dan informasi geografis lainnya."
                    ><?= clean(
                        $geography
                    ) ?></textarea>


                </div>



                <!-- =================================================
                     KONTAK
                ================================================== -->

                <div
                    class="section-box"
                >


                    <div
                        class="section-title"
                    >

                        <i
                            class="bi bi-telephone
                                   text-primary
                                   me-1"
                        ></i>


                        Informasi Kontak Desa

                    </div>


                    <div
                        class="row
                               g-3"
                    >


                        <!-- ALAMAT -->

                        <div
                            class="col-12"
                        >


                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Alamat

                            </label>


                            <textarea
                                name="address"
                                class="form-control"
                                rows="4"
                                placeholder="Alamat lengkap Kantor Desa Cisalada."
                            ><?= clean(
                                $address
                            ) ?></textarea>


                        </div>



                        <!-- EMAIL -->

                        <div
                            class="col-md-6"
                        >


                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Email

                            </label>


                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= clean(
                                    $email
                                ) ?>"
                                placeholder="Email resmi desa."
                            >


                        </div>



                        <!-- TELEPON -->

                        <div
                            class="col-md-6"
                        >


                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Nomor Telepon

                            </label>


                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?= clean(
                                    $phone
                                ) ?>"
                                placeholder="Nomor telepon desa."
                            >


                        </div>


                    </div>


                </div>



                <!-- =================================================
                     INFO
                ================================================== -->

                <div
                    class="alert
                           alert-info"
                >


                    <i
                        class="bi bi-info-circle
                               me-1"
                    ></i>


                    Bagian
                    <strong>Informasi Dasar Desa</strong>
                    akan memperbarui data seperti
                    Kecamatan, Kabupaten, Provinsi,
                    Luas Wilayah, Klasifikasi,
                    dan Tahun Berdiri yang tampil
                    pada halaman Profil Desa.


                </div>



                <!-- =================================================
                     BUTTON
                ================================================== -->

                <div
                    class="d-flex
                           gap-2
                           flex-wrap"
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

                        Simpan Profil

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>



<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>