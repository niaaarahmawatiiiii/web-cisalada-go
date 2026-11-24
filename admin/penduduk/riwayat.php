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
// DATA WARGA
// =========================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            population_status
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
// RIWAYAT
// =========================================================

$history_result =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            resident_id,
            change_type,
            old_value,
            new_value,
            description,
            changed_at
        FROM resident_history
        WHERE resident_id = "
        .
        $id
        .
        "
        ORDER BY
            changed_at DESC,
            id DESC
        "
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

        Riwayat Warga -
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


        .timeline {

            position:
                relative;

            padding-left:
                34px;

        }


        .timeline::before {

            content:
                "";

            position:
                absolute;

            left:
                10px;

            top:
                5px;

            bottom:
                5px;

            width:
                2px;

            background:
                #dee2e6;

        }


        .timeline-item {

            position:
                relative;

            margin-bottom:
                25px;

        }


        .timeline-item:last-child {

            margin-bottom:
                0;

        }


        .timeline-dot {

            position:
                absolute;

            left:
                -30px;

            top:
                4px;

            width:
                22px;

            height:
                22px;

            border-radius:
                50%;

            background:
                #0d6efd;

            border:
                4px solid
                #ffffff;

            box-shadow:
                0 0 0 1px
                #dee2e6;

        }


        .history-card {

            background:
                #ffffff;

            border:
                1px solid #e9ecef;

            border-radius:
                13px;

            padding:
                18px;

        }


        .history-date {

            font-size:
                0.8rem;

            color:
                #6c757d;

        }


        .json-box {

            background:
                #f8f9fa;

            border-radius:
                10px;

            padding:
                12px;

            font-size:
                0.78rem;

            white-space:
                pre-wrap;

            word-break:
                break-word;

            max-height:
                180px;

            overflow:
                auto;

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

            Riwayat Warga

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Riwayat perubahan data
            <?= clean(
                $resident["name"]
            ) ?>.

        </p>


    </div>



    <div
        class="card
               page-card
               shadow-sm"
    >


        <div
            class="card-body
                   p-4
                   p-lg-5"
        >


            <?php if (
                $history_result
                &&
                mysqli_num_rows(
                    $history_result
                )
                >
                0
            ): ?>


                <div
                    class="timeline"
                >


                    <?php while (
                        $history =
                            mysqli_fetch_assoc(
                                $history_result
                            )
                    ): ?>


                        <?php

                        $change_type =
                            $history[
                                "change_type"
                            ];

                        $badge_class =
                            "bg-primary-subtle text-primary";


                        if (
                            $change_type ===
                            "Tambah Warga"
                        ) {

                            $badge_class =
                                "bg-success-subtle text-success";

                        } elseif (
                            $change_type ===
                            "Pindah Masuk"
                        ) {

                            $badge_class =
                                "bg-info-subtle text-info-emphasis";

                        } elseif (
                            $change_type ===
                            "Pindah Keluar"
                        ) {

                            $badge_class =
                                "bg-warning-subtle text-warning-emphasis";

                        } elseif (
                            $change_type ===
                            "Meninggal"
                        ) {

                            $badge_class =
                                "bg-danger-subtle text-danger";

                        }

                        ?>


                        <div
                            class="timeline-item"
                        >


                            <div
                                class="timeline-dot"
                            ></div>


                            <div
                                class="history-card"
                            >


                                <div
                                    class="d-flex
                                           justify-content-between
                                           align-items-start
                                           gap-3
                                           flex-wrap
                                           mb-2"
                                >


                                    <span
                                        class="badge
                                               <?= $badge_class ?>"
                                    >

                                        <?= clean(
                                            $change_type
                                        ) ?>


                                    </span>


                                    <span
                                        class="history-date"
                                    >

                                        <?= date(
                                            "d-m-Y H:i:s",
                                            strtotime(
                                                $history["changed_at"]
                                            )
                                        ) ?>


                                    </span>


                                </div>



                                <?php if (
                                    !empty(
                                        $history["description"]
                                    )
                                ): ?>


                                    <div
                                        class="fw-semibold
                                               mb-3"
                                    >

                                        <?= clean(
                                            $history["description"]
                                        ) ?>


                                    </div>


                                <?php endif; ?>



                                <?php if (
                                    !empty(
                                        $history["old_value"]
                                    )
                                ): ?>


                                    <div
                                        class="mb-3"
                                    >


                                        <div
                                            class="small
                                                   fw-semibold
                                                   text-muted
                                                   mb-1"
                                        >

                                            Data Sebelumnya

                                        </div>


                                        <div
                                            class="json-box"
                                        >

                                            <?php

                                            $old_data =
                                                json_decode(
                                                    $history["old_value"],
                                                    true
                                                );


                                            if (
                                                json_last_error()
                                                ===
                                                JSON_ERROR_NONE
                                            ) {

                                                echo
                                                    clean(
                                                        json_encode(
                                                            $old_data,
                                                            JSON_PRETTY_PRINT |
                                                            JSON_UNESCAPED_UNICODE
                                                        )
                                                    );

                                            } else {

                                                echo
                                                    clean(
                                                        $history["old_value"]
                                                    );

                                            }

                                            ?>


                                        </div>


                                    </div>


                                <?php endif; ?>



                                <?php if (
                                    !empty(
                                        $history["new_value"]
                                    )
                                ): ?>


                                    <div>


                                        <div
                                            class="small
                                                   fw-semibold
                                                   text-muted
                                                   mb-1"
                                        >

                                            Data Baru

                                        </div>


                                        <div
                                            class="json-box"
                                        >

                                            <?php

                                            $new_data =
                                                json_decode(
                                                    $history["new_value"],
                                                    true
                                                );


                                            if (
                                                json_last_error()
                                                ===
                                                JSON_ERROR_NONE
                                            ) {

                                                echo
                                                    clean(
                                                        json_encode(
                                                            $new_data,
                                                            JSON_PRETTY_PRINT |
                                                            JSON_UNESCAPED_UNICODE
                                                        )
                                                    );

                                            } else {

                                                echo
                                                    clean(
                                                        $history["new_value"]
                                                    );

                                            }

                                            ?>


                                        </div>


                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <div
                    class="text-center
                           py-5"
                >


                    <i
                        class="bi bi-clock-history
                               fs-1
                               text-muted"
                    ></i>


                    <h5
                        class="fw-bold
                               mt-3"
                    >

                        Belum Ada Riwayat

                    </h5>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Belum ada perubahan data
                        yang tercatat untuk warga ini.

                    </p>


                </div>


            <?php endif; ?>


        </div>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>