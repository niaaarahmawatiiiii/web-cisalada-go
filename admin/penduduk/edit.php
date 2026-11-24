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


// =========================================================
// AMBIL DATA LAMA
// =========================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            nik,
            family_card_number,
            name,
            gender,
            birth_place,
            birth_date,
            address,
            hamlet,
            rw,
            rt,
            religion,
            marital_status,
            education,
            occupation,
            population_status,
            entry_date,
            exit_date,
            notes
        FROM residents
        WHERE id = ?
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


$resident =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$resident
) {

    header(
        "Location: index.php?error=not_found"
    );

    exit;

}


// =========================================================
// DATA FORM
// =========================================================

$nik =
    $resident["nik"]
    ??
    "";


$family_card_number =
    $resident["family_card_number"]
    ??
    "";


$name =
    $resident["name"]
    ??
    "";


$gender =
    $resident["gender"]
    ??
    "";


$birth_place =
    $resident["birth_place"]
    ??
    "";


$birth_date =
    $resident["birth_date"]
    ??
    "";


$address =
    $resident["address"]
    ??
    "";


$hamlet =
    $resident["hamlet"]
    ??
    "";


$rw =
    $resident["rw"]
    ??
    "";


$rt =
    $resident["rt"]
    ??
    "";


$religion =
    $resident["religion"]
    ??
    "";


$marital_status =
    $resident["marital_status"]
    ??
    "";


$education =
    $resident["education"]
    ??
    "";


$occupation =
    $resident["occupation"]
    ??
    "";


$population_status =
    $resident["population_status"]
    ??
    "Aktif";


$entry_date =
    $resident["entry_date"]
    ??
    "";


$exit_date =
    $resident["exit_date"]
    ??
    "";


$notes =
    $resident["notes"]
    ??
    "";


$error =
    "";


// =========================================================
// PROSES UPDATE
// =========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $nik =
        trim(
            $_POST["nik"]
            ??
            ""
        );


    $family_card_number =
        trim(
            $_POST["family_card_number"]
            ??
            ""
        );


    $name =
        trim(
            $_POST["name"]
            ??
            ""
        );


    $gender =
        trim(
            $_POST["gender"]
            ??
            ""
        );


    $birth_place =
        trim(
            $_POST["birth_place"]
            ??
            ""
        );


    $birth_date =
        trim(
            $_POST["birth_date"]
            ??
            ""
        );


    $address =
        trim(
            $_POST["address"]
            ??
            ""
        );


    $hamlet =
        trim(
            $_POST["hamlet"]
            ??
            ""
        );


    $rw =
        trim(
            $_POST["rw"]
            ??
            ""
        );


    $rt =
        trim(
            $_POST["rt"]
            ??
            ""
        );


    $religion =
        trim(
            $_POST["religion"]
            ??
            ""
        );


    $marital_status =
        trim(
            $_POST["marital_status"]
            ??
            ""
        );


    $education =
        trim(
            $_POST["education"]
            ??
            ""
        );


    $occupation =
        trim(
            $_POST["occupation"]
            ??
            ""
        );


    $population_status =
        trim(
            $_POST["population_status"]
            ??
            "Aktif"
        );


    $entry_date =
        trim(
            $_POST["entry_date"]
            ??
            ""
        );


    $exit_date =
        trim(
            $_POST["exit_date"]
            ??
            ""
        );


    $notes =
        trim(
            $_POST["notes"]
            ??
            ""
        );



    // =====================================================
    // VALIDASI
    // =====================================================

    if (
        $name === ""
    ) {

        $error =
            "Nama warga wajib diisi.";

    } elseif (
        !in_array(
            $gender,
            [
                "Laki-laki",
                "Perempuan"
            ],
            true
        )
    ) {

        $error =
            "Jenis kelamin tidak valid.";

    } elseif (
        !in_array(
            $population_status,
            [
                "Aktif",
                "Pindah Keluar",
                "Meninggal"
            ],
            true
        )
    ) {

        $error =
            "Status penduduk tidak valid.";

    }



    // =====================================================
    // VALIDASI NIK
    // =====================================================

    if (
        empty($error)
        &&
        $nik !== ""
        &&
        !preg_match(
            '/^[0-9]{1,20}$/',
            $nik
        )
    ) {

        $error =
            "NIK harus berupa angka dengan maksimal 20 digit.";

    }



    // =====================================================
    // VALIDASI KK
    // =====================================================

    if (
        empty($error)
        &&
        $family_card_number !== ""
        &&
        !preg_match(
            '/^[0-9]{1,20}$/',
            $family_card_number
        )
    ) {

        $error =
            "Nomor KK harus berupa angka dengan maksimal 20 digit.";

    }



    // =====================================================
    // VALIDASI TANGGAL
    // =====================================================

    $date_fields = [

        "Tanggal lahir" =>
            $birth_date,

        "Tanggal masuk" =>
            $entry_date,

        "Tanggal keluar" =>
            $exit_date

    ];


    foreach (
        $date_fields
        as $label =>
        $value
    ) {


        if (
            empty($error)
            &&
            $value !== ""
        ) {


            $date_object =
                DateTime::createFromFormat(
                    "Y-m-d",
                    $value
                );


            if (
                !$date_object
                ||
                $date_object->format(
                    "Y-m-d"
                )
                !==
                $value
            ) {

                $error =
                    $label
                    .
                    " tidak valid.";

            }

        }

    }



    // =====================================================
    // VALIDASI STATUS
    // =====================================================

    if (
        empty($error)
        &&
        $population_status === "Aktif"
        &&
        $exit_date !== ""
    ) {

        $error =
            "Warga dengan status Aktif tidak perlu memiliki tanggal keluar.";

    }



    // =====================================================
    // NORMALISASI
    // =====================================================

    $nik_db =
        $nik !== ""
            ? $nik
            : null;


    $family_card_db =
        $family_card_number !== ""
            ? $family_card_number
            : null;


    $birth_place_db =
        $birth_place !== ""
            ? $birth_place
            : null;


    $birth_date_db =
        $birth_date !== ""
            ? $birth_date
            : null;


    $address_db =
        $address !== ""
            ? $address
            : null;


    $hamlet_db =
        $hamlet !== ""
            ? $hamlet
            : null;


    $rw_db =
        $rw !== ""
            ? $rw
            : null;


    $rt_db =
        $rt !== ""
            ? $rt
            : null;


    $religion_db =
        $religion !== ""
            ? $religion
            : null;


    $marital_status_db =
        $marital_status !== ""
            ? $marital_status
            : null;


    $education_db =
        $education !== ""
            ? $education
            : null;


    $occupation_db =
        $occupation !== ""
            ? $occupation
            : null;


    $entry_date_db =
        $entry_date !== ""
            ? $entry_date
            : null;


    $exit_date_db =
        $exit_date !== ""
            ? $exit_date
            : null;


    $notes_db =
        $notes !== ""
            ? $notes
            : null;



    // =====================================================
    // UPDATE
    // =====================================================

    if (
        empty($error)
    ) {


        $stmt =
            mysqli_prepare(
                $conn,
                "
                UPDATE residents

                SET
                    nik = ?,
                    family_card_number = ?,
                    name = ?,
                    gender = ?,
                    birth_place = ?,
                    birth_date = ?,
                    address = ?,
                    hamlet = ?,
                    rw = ?,
                    rt = ?,
                    religion = ?,
                    marital_status = ?,
                    education = ?,
                    occupation = ?,
                    population_status = ?,
                    entry_date = ?,
                    exit_date = ?,
                    notes = ?

                WHERE
                    id = ?
                "
            );


        if (
            !$stmt
        ) {

            $error =
                "Gagal menyiapkan query: "
                .
                mysqli_error(
                    $conn
                );

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssssssssssi",
                $nik_db,
                $family_card_db,
                $name,
                $gender,
                $birth_place_db,
                $birth_date_db,
                $address_db,
                $hamlet_db,
                $rw_db,
                $rt_db,
                $religion_db,
                $marital_status_db,
                $education_db,
                $occupation_db,
                $population_status,
                $entry_date_db,
                $exit_date_db,
                $notes_db,
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


                // =================================================
                // RIWAYAT EDIT
                // =================================================

                $history =
                    mysqli_prepare(
                        $conn,
                        "
                        INSERT INTO resident_history
                        (
                            resident_id,
                            change_type,
                            old_value,
                            new_value,
                            description
                        )
                        VALUES
                        (
                            ?,
                            'Edit Data',
                            ?,
                            ?,
                            ?
                        )
                        "
                    );


                if (
                    $history
                ) {


                    $old_value =
                        json_encode(
                            $resident,
                            JSON_UNESCAPED_UNICODE
                        );


                    $new_value =
                        json_encode(
                            [
                                "nik" =>
                                    $nik,

                                "family_card_number" =>
                                    $family_card_number,

                                "name" =>
                                    $name,

                                "gender" =>
                                    $gender,

                                "birth_place" =>
                                    $birth_place,

                                "birth_date" =>
                                    $birth_date,

                                "address" =>
                                    $address,

                                "hamlet" =>
                                    $hamlet,

                                "rw" =>
                                    $rw,

                                "rt" =>
                                    $rt,

                                "religion" =>
                                    $religion,

                                "marital_status" =>
                                    $marital_status,

                                "education" =>
                                    $education,

                                "occupation" =>
                                    $occupation,

                                "population_status" =>
                                    $population_status,

                                "entry_date" =>
                                    $entry_date,

                                "exit_date" =>
                                    $exit_date,

                                "notes" =>
                                    $notes

                            ],
                            JSON_UNESCAPED_UNICODE
                        );


                    $history_description =
                        "Perubahan data warga "
                        .
                        $name
                        .
                        ".";


                    mysqli_stmt_bind_param(
                        $history,
                        "isss",
                        $id,
                        $old_value,
                        $new_value,
                        $history_description
                    );


                    mysqli_stmt_execute(
                        $history
                    );


                    mysqli_stmt_close(
                        $history
                    );

                }



                header(
                    "Location: detail.php?id="
                    .
                    $id
                );


                exit;

            }


            $error =
                "Gagal memperbarui data warga: "
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
        Edit Warga - Cisalada GO
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


        .section-title {

            font-size:
                1rem;

            font-weight:
                700;

            margin-bottom:
                15px;

        }


        .required {

            color:
                #dc3545;

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
            href="detail.php?id=<?= (int) $id ?>"
            class="text-decoration-none
                   text-muted"
        >

            <i
                class="bi bi-arrow-left
                       me-1"
            ></i>

            Kembali ke Detail Warga

        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Edit Warga

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Perbarui data
            <?= clean(
                $resident["name"]
            ) ?>.

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


                <!-- IDENTITAS -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-person-vcard
                               text-primary
                               me-1"
                    ></i>

                    Identitas Warga

                </div>


                <div
                    class="row
                           g-3"
                >


                    <div
                        class="col-md-8"
                    >

                        <label
                            class="form-label"
                        >

                            Nama Lengkap

                            <span
                                class="required"
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



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            Jenis Kelamin

                            <span
                                class="required"
                            >

                                *

                            </span>

                        </label>


                        <select
                            name="gender"
                            class="form-select"
                            required
                        >


                            <option
                                value=""
                            >

                                -- Pilih --

                            </option>


                            <option
                                value="Laki-laki"
                                <?= $gender === "Laki-laki"
                                    ? "selected"
                                    : "" ?>
                            >

                                Laki-laki

                            </option>


                            <option
                                value="Perempuan"
                                <?= $gender === "Perempuan"
                                    ? "selected"
                                    : "" ?>
                            >

                                Perempuan

                            </option>


                        </select>

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            NIK

                        </label>


                        <input
                            type="text"
                            name="nik"
                            class="form-control"
                            value="<?= clean(
                                $nik
                            ) ?>"
                            maxlength="20"
                            inputmode="numeric"
                        >

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Nomor KK

                        </label>


                        <input
                            type="text"
                            name="family_card_number"
                            class="form-control"
                            value="<?= clean(
                                $family_card_number
                            ) ?>"
                            maxlength="20"
                            inputmode="numeric"
                        >

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Tempat Lahir

                        </label>


                        <input
                            type="text"
                            name="birth_place"
                            class="form-control"
                            value="<?= clean(
                                $birth_place
                            ) ?>"
                        >

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Tanggal Lahir

                        </label>


                        <input
                            type="date"
                            name="birth_date"
                            class="form-control"
                            value="<?= clean(
                                $birth_date
                            ) ?>"
                        >

                    </div>


                </div>



                <hr
                    class="my-4"
                >



                <!-- ALAMAT -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-geo-alt
                               text-primary
                               me-1"
                    ></i>

                    Alamat

                </div>


                <div
                    class="row
                           g-3"
                >


                    <div
                        class="col-12"
                    >

                        <label
                            class="form-label"
                        >

                            Alamat

                        </label>


                        <textarea
                            name="address"
                            class="form-control"
                            rows="3"
                        ><?= clean(
                            $address
                        ) ?></textarea>


                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            Dusun

                        </label>


                        <input
                            type="text"
                            name="hamlet"
                            class="form-control"
                            value="<?= clean(
                                $hamlet
                            ) ?>"
                        >

                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            RW

                        </label>


                        <input
                            type="text"
                            name="rw"
                            class="form-control"
                            value="<?= clean(
                                $rw
                            ) ?>"
                        >

                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            RT

                        </label>


                        <input
                            type="text"
                            name="rt"
                            class="form-control"
                            value="<?= clean(
                                $rt
                            ) ?>"
                        >

                    </div>


                </div>



                <hr
                    class="my-4"
                >



                <!-- DATA SOSIAL -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-people
                               text-primary
                               me-1"
                    ></i>

                    Data Sosial

                </div>


                <div
                    class="row
                           g-3"
                >


                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Agama

                        </label>


                        <select
                            name="religion"
                            class="form-select"
                        >


                            <option
                                value=""
                            >

                                -- Pilih Agama --

                            </option>


                            <option
                                value="Islam"
                                <?= $religion === "Islam"
                                    ? "selected"
                                    : "" ?>
                            >

                                Islam

                            </option>


                            <option
                                value="Kristen"
                                <?= $religion === "Kristen"
                                    ? "selected"
                                    : "" ?>
                            >

                                Kristen

                            </option>


                            <option
                                value="Katolik"
                                <?= $religion === "Katolik"
                                    ? "selected"
                                    : "" ?>
                            >

                                Katolik

                            </option>


                            <option
                                value="Hindu"
                                <?= $religion === "Hindu"
                                    ? "selected"
                                    : "" ?>
                            >

                                Hindu

                            </option>


                            <option
                                value="Buddha"
                                <?= $religion === "Buddha"
                                    ? "selected"
                                    : "" ?>
                            >

                                Buddha

                            </option>


                            <option
                                value="Konghucu"
                                <?= $religion === "Konghucu"
                                    ? "selected"
                                    : "" ?>
                            >

                                Konghucu

                            </option>


                            <option
                                value="Lainnya"
                                <?= $religion === "Lainnya"
                                    ? "selected"
                                    : "" ?>
                            >

                                Lainnya

                            </option>


                        </select>

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Status Perkawinan

                        </label>


                        <select
                            name="marital_status"
                            class="form-select"
                        >


                            <option
                                value=""
                            >

                                -- Pilih Status --

                            </option>


                            <option
                                value="Belum Kawin"
                                <?= $marital_status === "Belum Kawin"
                                    ? "selected"
                                    : "" ?>
                            >

                                Belum Kawin

                            </option>


                            <option
                                value="Kawin"
                                <?= $marital_status === "Kawin"
                                    ? "selected"
                                    : "" ?>
                            >

                                Kawin

                            </option>


                            <option
                                value="Cerai Hidup"
                                <?= $marital_status === "Cerai Hidup"
                                    ? "selected"
                                    : "" ?>
                            >

                                Cerai Hidup

                            </option>


                            <option
                                value="Cerai Mati"
                                <?= $marital_status === "Cerai Mati"
                                    ? "selected"
                                    : "" ?>
                            >

                                Cerai Mati

                            </option>


                        </select>

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Pendidikan

                        </label>


                        <select
                            name="education"
                            class="form-select"
                        >


                            <option
                                value=""
                            >

                                -- Pilih Pendidikan --

                            </option>


                            <option
                                value="Tidak/Belum Sekolah"
                                <?= $education === "Tidak/Belum Sekolah"
                                    ? "selected"
                                    : "" ?>
                            >

                                Tidak/Belum Sekolah

                            </option>


                            <option
                                value="SD"
                                <?= $education === "SD"
                                    ? "selected"
                                    : "" ?>
                            >

                                SD

                            </option>


                            <option
                                value="SMP"
                                <?= $education === "SMP"
                                    ? "selected"
                                    : "" ?>
                            >

                                SMP

                            </option>


                            <option
                                value="SMA/SMK"
                                <?= $education === "SMA/SMK"
                                    ? "selected"
                                    : "" ?>
                            >

                                SMA/SMK

                            </option>


                            <option
                                value="Diploma"
                                <?= $education === "Diploma"
                                    ? "selected"
                                    : "" ?>
                            >

                                Diploma

                            </option>


                            <option
                                value="S1"
                                <?= $education === "S1"
                                    ? "selected"
                                    : "" ?>
                            >

                                S1

                            </option>


                            <option
                                value="S2"
                                <?= $education === "S2"
                                    ? "selected"
                                    : "" ?>
                            >

                                S2

                            </option>


                            <option
                                value="S3"
                                <?= $education === "S3"
                                    ? "selected"
                                    : "" ?>
                            >

                                S3

                            </option>


                        </select>

                    </div>



                    <div
                        class="col-md-6"
                    >

                        <label
                            class="form-label"
                        >

                            Pekerjaan

                        </label>


                        <input
                            type="text"
                            name="occupation"
                            class="form-control"
                            value="<?= clean(
                                $occupation
                            ) ?>"
                        >

                    </div>


                </div>



                <hr
                    class="my-4"
                >



                <!-- STATUS -->

                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-arrow-left-right
                               text-primary
                               me-1"
                    ></i>

                    Status Kependudukan

                </div>


                <div
                    class="row
                           g-3"
                >


                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            Status

                            <span
                                class="required"
                            >

                                *

                            </span>

                        </label>


                        <select
                            name="population_status"
                            class="form-select"
                            required
                        >


                            <option
                                value="Aktif"
                                <?= $population_status === "Aktif"
                                    ? "selected"
                                    : "" ?>
                            >

                                Aktif

                            </option>


                            <option
                                value="Pindah Keluar"
                                <?= $population_status === "Pindah Keluar"
                                    ? "selected"
                                    : "" ?>
                            >

                                Pindah Keluar

                            </option>


                            <option
                                value="Meninggal"
                                <?= $population_status === "Meninggal"
                                    ? "selected"
                                    : "" ?>
                            >

                                Meninggal

                            </option>


                        </select>

                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            Tanggal Masuk

                        </label>


                        <input
                            type="date"
                            name="entry_date"
                            class="form-control"
                            value="<?= clean(
                                $entry_date
                            ) ?>"
                        >

                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label"
                        >

                            Tanggal Keluar

                        </label>


                        <input
                            type="date"
                            name="exit_date"
                            class="form-control"
                            value="<?= clean(
                                $exit_date
                            ) ?>"
                        >

                    </div>


                </div>



                <!-- CATATAN -->

                <div
                    class="mt-3"
                >


                    <label
                        class="form-label"
                    >

                        Catatan

                    </label>


                    <textarea
                        name="notes"
                        class="form-control"
                        rows="4"
                    ><?= clean(
                        $notes
                    ) ?></textarea>


                </div>



                <!-- BUTTON -->

                <div
                    class="d-flex
                           gap-2
                           mt-4"
                >


                    <a
                        href="detail.php?id=<?= (int) $id ?>"
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