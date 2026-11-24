<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// AMBIL ID
// =========================================================

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
// AMBIL DATA WARGA
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
            notes,
            created_at,
            updated_at
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
// FORMAT TANGGAL
// =========================================================

function formatResidentDate(
    $date
) {

    if (
        empty($date)
    ) {

        return "-";

    }


    $timestamp =
        strtotime(
            $date
        );


    if (
        !$timestamp
    ) {

        return clean(
            $date
        );

    }


    return date(
        "d-m-Y",
        $timestamp
    );

}


// =========================================================
// UMUR
// =========================================================

$age =
    "-";


if (
    !empty(
        $resident["birth_date"]
    )
) {

    try {

        $birth =
            new DateTime(
                $resident["birth_date"]
            );


        $today =
            new DateTime();


        $age =
            $birth->diff(
                $today
            )->y
            .
            " tahun";

    } catch (
        Exception $e
    ) {

        $age =
            "-";

    }

}


// =========================================================
// STATUS CLASS
// =========================================================

$status =
    $resident[
        "population_status"
    ]
    ??
    "Aktif";


$status_class =
    "bg-success-subtle text-success";


if (
    $status ===
    "Pindah Keluar"
) {

    $status_class =
        "bg-warning-subtle text-warning-emphasis";

} elseif (
    $status ===
    "Meninggal"
) {

    $status_class =
        "bg-danger-subtle text-danger";

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

        Detail Warga -
        <?= clean(
            $resident["name"]
        ) ?>

        - Cisalada GO

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


        .page-card {

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
                18px;

        }


        .info-label {

            color:
                #6c757d;

            font-size:
                0.82rem;

            margin-bottom:
                4px;

        }


        .info-value {

            font-weight:
                500;

            word-break:
                break-word;

        }


        .info-item {

            margin-bottom:
                20px;

        }


        .notes-box {

            background:
                #f8f9fa;

            border-radius:
                12px;

            padding:
                16px;

            white-space:
                pre-line;

            line-height:
                1.7;

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


        <div
            class="d-flex
                   align-items-center"
        >


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
        class="d-flex
               justify-content-between
               align-items-start
               flex-wrap
               gap-3
               mb-4"
    >


        <div>


            <a
                href="index.php"
                class="text-decoration-none
                       text-muted"
            >

                <i
                    class="bi bi-arrow-left
                           me-1"
                ></i>

                Kembali ke Data Penduduk

            </a>


            <h2
                class="fw-bold
                       mt-3
                       mb-1"
            >

                Detail Warga

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Informasi lengkap warga Desa Cisalada.

            </p>


        </div>


        <div
            class="d-flex
                   gap-2
                   flex-wrap"
        >


            <a
                href="edit.php?id=<?= (int) $resident["id"] ?>"
                class="btn
                       btn-warning"
            >

                <i
                    class="bi bi-pencil
                           me-1"
                ></i>

                Edit

            </a>


            <a
                href="riwayat.php?id=<?= (int) $resident["id"] ?>"
                class="btn
                       btn-outline-primary"
            >

                <i
                    class="bi bi-clock-history
                           me-1"
                ></i>

                Riwayat

            </a>


        </div>


    </div>



    <!-- ==================================================
         STATUS
    =================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


            <div
                class="d-flex
                       justify-content-between
                       align-items-center
                       flex-wrap
                       gap-3"
            >


                <div>


                    <div
                        class="small
                               text-muted
                               mb-1"
                    >

                        Nama Warga

                    </div>


                    <h3
                        class="fw-bold
                               mb-0"
                    >

                        <?= clean(
                            $resident["name"]
                        ) ?>


                    </h3>


                </div>


                <div>


                    <span
                        class="badge
                               <?= $status_class ?>
                               fs-6
                               px-3
                               py-2"
                    >

                        <?= clean(
                            $status
                        ) ?>


                    </span>


                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         IDENTITAS
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


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
                class="row"
            >


                <div
                    class="col-md-6"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            NIK

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["nik"]
                            )
                                ?
                                clean(
                                    $resident["nik"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-6"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Nomor Kartu Keluarga

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["family_card_number"]
                            )
                                ?
                                clean(
                                    $resident["family_card_number"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-6"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Jenis Kelamin

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= clean(
                                $resident["gender"]
                            ) ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-6"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Tempat, Tanggal Lahir

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["birth_place"]
                            )
                                ?
                                clean(
                                    $resident["birth_place"]
                                )
                                :
                                "-"
                            ?>


                            ,


                            <?= formatResidentDate(
                                $resident["birth_date"]
                            ) ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-6"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Umur

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= clean(
                                $age
                            ) ?>


                        </div>

                    </div>


                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         ALAMAT
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


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
                class="row"
            >


                <div
                    class="col-12"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Alamat Lengkap

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["address"]
                            )
                                ?
                                nl2br(
                                    clean(
                                        $resident["address"]
                                    )
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-4"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Dusun

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["hamlet"]
                            )
                                ?
                                clean(
                                    $resident["hamlet"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-4"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            RW

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["rw"]
                            )
                                ?
                                clean(
                                    $resident["rw"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>


                </div>



                <div
                    class="col-md-4"
                >


                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            RT

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["rt"]
                            )
                                ?
                                clean(
                                    $resident["rt"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>


                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         DATA SOSIAL
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


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
                class="row"
            >


                <div
                    class="col-md-6"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Agama

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["religion"]
                            )
                                ?
                                clean(
                                    $resident["religion"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>

                </div>



                <div
                    class="col-md-6"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Status Perkawinan

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["marital_status"]
                            )
                                ?
                                clean(
                                    $resident["marital_status"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>

                </div>



                <div
                    class="col-md-6"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Pendidikan

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["education"]
                            )
                                ?
                                clean(
                                    $resident["education"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>

                </div>



                <div
                    class="col-md-6"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Pekerjaan

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= !empty(
                                $resident["occupation"]
                            )
                                ?
                                clean(
                                    $resident["occupation"]
                                )
                                :
                                "-"
                            ?>


                        </div>

                    </div>

                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         STATUS KEPENDUDUKAN
    ================================================== -->

    <div
        class="card
               page-card
               shadow-sm
               mb-4"
    >


        <div
            class="card-body
                   p-4"
        >


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
                class="row"
            >


                <div
                    class="col-md-4"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Status

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= clean(
                                $status
                            ) ?>


                        </div>

                    </div>

                </div>



                <div
                    class="col-md-4"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Tanggal Masuk

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= formatResidentDate(
                                $resident["entry_date"]
                            ) ?>


                        </div>

                    </div>

                </div>



                <div
                    class="col-md-4"
                >

                    <div
                        class="info-item"
                    >

                        <div
                            class="info-label"
                        >

                            Tanggal Keluar

                        </div>


                        <div
                            class="info-value"
                        >

                            <?= formatResidentDate(
                                $resident["exit_date"]
                            ) ?>


                        </div>

                    </div>

                </div>


            </div>


        </div>


    </div>



    <!-- ==================================================
         CATATAN
    ================================================== -->

    <?php if (
        !empty(
            $resident["notes"]
        )
    ): ?>


        <div
            class="card
                   page-card
                   shadow-sm
                   mb-4"
        >


            <div
                class="card-body
                       p-4"
            >


                <div
                    class="section-title"
                >

                    <i
                        class="bi bi-journal-text
                               text-primary
                               me-1"
                    ></i>


                    Catatan

                </div>


                <div
                    class="notes-box"
                >

                    <?= clean(
                        $resident["notes"]
                    ) ?>


                </div>


            </div>


        </div>


    <?php endif; ?>



    <!-- ==================================================
         FOOTER INFO
    ================================================== -->

    <div
        class="text-muted
               small
               mb-4"
    >

        Data dibuat:
        <?= date(
            "d-m-Y H:i",
            strtotime(
                $resident["created_at"]
            )
        ) ?>


        &nbsp;•&nbsp;


        Terakhir diperbarui:
        <?= date(
            "d-m-Y H:i",
            strtotime(
                $resident["updated_at"]
            )
        ) ?>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>