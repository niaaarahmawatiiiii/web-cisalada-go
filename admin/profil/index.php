<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =====================================================
// AMBIL DATA
// =====================================================

$profile =
    null;


$result =
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
    $result
) {

    $profile =
        mysqli_fetch_assoc(
            $result
        );

}


// =====================================================
// FALLBACK NAMA DESA
// =====================================================

$village_name =
    $profile[
        "village_name"
    ]
    ??
    "";


if (
    $village_name === ""
) {

    $village_result =
        mysqli_query(
            $conn,
            "
            SELECT
                village_name
            FROM village_data
            ORDER BY id ASC
            LIMIT 1
            "
        );


    if (
        $village_result
    ) {

        $village =
            mysqli_fetch_assoc(
                $village_result
            );


        $village_name =
            $village[
                "village_name"
            ]
            ??
            "";

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

        Profil Desa - Admin Cisalada GO

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


        .profile-item {

            padding:
                20px;

            border:
                1px solid
                #e9ecef;

            border-radius:
                12px;

            height:
                100%;

        }


        .profile-label {

            font-size:
                13px;

            color:
                #6c757d;

            margin-bottom:
                6px;

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



<div
    class="container
           py-4"
>


    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-4"
    >


        <div>


            <a
                href="<?= BASE_URL ?>admin/index.php"
                class="text-decoration-none
                       text-muted"
            >

                <i
                    class="bi bi-arrow-left
                           me-1"
                ></i>

                Kembali ke Dashboard

            </a>


            <h2
                class="fw-bold
                       mt-3
                       mb-1"
            >

                Profil Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Kelola informasi profil,
                sejarah, visi, misi, dan geografis
                Desa Cisalada.

            </p>


        </div>


        <a
            href="edit.php"
            class="btn
                   btn-primary"
        >

            <i
                class="bi bi-pencil
                       me-1"
            ></i>

            Edit Profil

        </a>


    </div>



    <div
        class="card
               shadow-sm"
    >


        <div
            class="card-body
                   p-4"
        >


            <!-- NAMA -->

            <div
                class="mb-4"
            >


                <div
                    class="profile-item"
                >

                    <div
                        class="profile-label"
                    >

                        Nama Desa

                    </div>


                    <h5
                        class="fw-bold
                               mb-0"
                    >

                        <?= clean(
                            $village_name
                        ) ?>


                    </h5>


                </div>


            </div>



            <div
                class="row
                       g-4"
            >


                <!-- SEJARAH -->

                <div
                    class="col-lg-6"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Sejarah Desa

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?php if (
                                !empty(
                                    $profile[
                                        "history"
                                    ]
                                )
                            ): ?>


                                <?= nl2br(
                                    clean(
                                        $profile[
                                            "history"
                                        ]
                                    )
                                ) ?>


                            <?php else: ?>


                                Belum diisi.

                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- GEOGRAFI -->

                <div
                    class="col-lg-6"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Kondisi Geografis

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?php if (
                                !empty(
                                    $profile[
                                        "geography"
                                    ]
                                )
                            ): ?>


                                <?= nl2br(
                                    clean(
                                        $profile[
                                            "geography"
                                        ]
                                    )
                                ) ?>


                            <?php else: ?>


                                Belum diisi.

                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- VISI -->

                <div
                    class="col-lg-6"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Visi

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?php if (
                                !empty(
                                    $profile[
                                        "vision"
                                    ]
                                )
                            ): ?>


                                <?= nl2br(
                                    clean(
                                        $profile[
                                            "vision"
                                        ]
                                    )
                                ) ?>


                            <?php else: ?>


                                Belum diisi.

                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- MISI -->

                <div
                    class="col-lg-6"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Misi

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?php if (
                                !empty(
                                    $profile[
                                        "mission"
                                    ]
                                )
                            ): ?>


                                <?= nl2br(
                                    clean(
                                        $profile[
                                            "mission"
                                        ]
                                    )
                                ) ?>


                            <?php else: ?>


                                Belum diisi.

                            <?php endif; ?>


                        </div>


                    </div>


                </div>



                <!-- ALAMAT -->

                <div
                    class="col-lg-4"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Alamat

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?= !empty(
                                $profile[
                                    "address"
                                ]
                            )
                                ?
                                nl2br(
                                    clean(
                                        $profile[
                                            "address"
                                        ]
                                    )
                                )
                                :
                                "Belum diisi."
                            ?>


                        </div>


                    </div>


                </div>



                <!-- EMAIL -->

                <div
                    class="col-lg-4"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Email

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?= !empty(
                                $profile[
                                    "email"
                                ]
                            )
                                ?
                                clean(
                                    $profile[
                                        "email"
                                    ]
                                )
                                :
                                "Belum diisi."
                            ?>


                        </div>


                    </div>


                </div>



                <!-- TELEPON -->

                <div
                    class="col-lg-4"
                >


                    <div
                        class="profile-item"
                    >


                        <div
                            class="profile-label"
                        >

                            Telepon

                        </div>


                        <div
                            class="text-muted"
                        >

                            <?= !empty(
                                $profile[
                                    "phone"
                                ]
                            )
                                ?
                                clean(
                                    $profile[
                                        "phone"
                                    ]
                                )
                                :
                                "Belum diisi."
                            ?>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>