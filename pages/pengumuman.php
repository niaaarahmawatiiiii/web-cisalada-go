<?php

require_once "../config/database.php";
require_once "../includes/functions.php";

$page_title = "Pengumuman";


// ==========================================
// AMBIL DATA PENGUMUMAN
// ==========================================

$query =
    mysqli_query(
        $conn,
        "
        SELECT
            id,
            title,
            content,
            type,
            image,
            published_at,
            created_at

        FROM announcements

        ORDER BY
            published_at DESC,
            created_at DESC
        "
    );

?>

<?php include "../includes/header.php"; ?>

<?php include "../includes/navbar.php"; ?>


<!-- ==========================================
     HERO
========================================== -->

<section
    class="py-5 bg-light"
>

    <div class="container">

        <div class="text-center">

            <p
                class="text-primary
                       fw-semibold
                       mb-2"
            >

                INFORMASI DESA

            </p>


            <h1 class="fw-bold">

                Pengumuman

            </h1>


            <p class="text-muted mb-0">

                Informasi dan pengumuman resmi
                Desa Cisalada.

            </p>

        </div>

    </div>

</section>



<!-- ==========================================
     DAFTAR PENGUMUMAN
========================================== -->

<section class="section-padding">

    <div class="container">


        <?php if (
            $query
            &&
            mysqli_num_rows(
                $query
            ) > 0
        ): ?>


            <div class="row g-4">


                <?php while (
                    $announcement =
                        mysqli_fetch_assoc(
                            $query
                        )
                ): ?>


                    <div
                        class="col-12"
                    >


                        <div
                            class="card
                                   border-0
                                   shadow-sm
                                   overflow-hidden"
                        >


                            <!-- ==================================
                                 GAMBAR
                            =================================== -->

                            <?php if (
                                !empty(
                                    $announcement[
                                        "image"
                                    ]
                                )
                            ): ?>


                                <img
                                    src="<?= BASE_URL ?>assets/uploads/pengumuman/<?= clean($announcement["image"]) ?>"
                                    alt="<?= clean($announcement["title"]) ?>"
                                    class="w-100"
                                    style="
                                        height: 260px;
                                        object-fit: cover;
                                    "
                                >


                            <?php endif; ?>



                            <div
                                class="card-body p-4"
                            >


                                <div
                                    class="d-flex
                                           justify-content-between
                                           align-items-start
                                           gap-3"
                                >


                                    <div
                                        class="w-100"
                                    >


                                        <!-- ==================================
                                             BADGE
                                        =================================== -->

                                        <?php

                                        $badge_class =
                                            "bg-primary";


                                        if (
                                            $announcement[
                                                "type"
                                            ]
                                            ===
                                            "Penting"
                                        ) {

                                            $badge_class =
                                                "bg-danger";

                                        } elseif (
                                            $announcement[
                                                "type"
                                            ]
                                            ===
                                            "Kegiatan"
                                        ) {

                                            $badge_class =
                                                "bg-success";

                                        } elseif (
                                            $announcement[
                                                "type"
                                            ]
                                            ===
                                            "Layanan"
                                        ) {

                                            $badge_class =
                                                "bg-info text-dark";

                                        }

                                        ?>


                                        <span
                                            class="badge
                                                   <?= $badge_class ?>
                                                   mb-3"
                                        >

                                            <?= clean(
                                                $announcement[
                                                    "type"
                                                ]
                                            ) ?>

                                        </span>



                                        <!-- ==================================
                                             JUDUL
                                        =================================== -->

                                        <h4
                                            class="fw-bold
                                                   mb-2"
                                        >

                                            <?= clean(
                                                $announcement[
                                                    "title"
                                                ]
                                            ) ?>

                                        </h4>



                                        <!-- ==================================
                                             TANGGAL
                                        =================================== -->

                                        <p
                                            class="text-muted
                                                   small
                                                   mb-3"
                                        >

                                            <i
                                                class="bi bi-calendar3 me-1"
                                            ></i>


                                            <?php

                                            if (
                                                !empty(
                                                    $announcement[
                                                        "published_at"
                                                    ]
                                                )
                                            ) {

                                                echo date(
                                                    "d F Y",
                                                    strtotime(
                                                        $announcement[
                                                            "published_at"
                                                        ]
                                                    )
                                                );

                                            } else {

                                                echo date(
                                                    "d F Y",
                                                    strtotime(
                                                        $announcement[
                                                            "created_at"
                                                        ]
                                                    )
                                                );

                                            }

                                            ?>


                                        </p>



                                    </div>


                                </div>



                                <!-- ==================================
                                     ISI
                                =================================== -->

                                <div
                                    class="text-muted
                                           mt-2"
                                    style="
                                        white-space: pre-line;
                                        line-height: 1.8;
                                    "
                                >

                                    <?= clean(
                                        $announcement[
                                            "content"
                                        ]
                                    ) ?>

                                </div>


                            </div>


                        </div>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- ==========================================
                 EMPTY STATE
            =========================================== -->

            <div
                class="text-center
                       py-5"
            >


                <i
                    class="bi bi-megaphone
                           fs-1
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Pengumuman

                </h4>


                <p class="text-muted">

                    Belum ada pengumuman
                    yang tersedia saat ini.

                </p>


            </div>


        <?php endif; ?>


    </div>

</section>



<?php include "../includes/footer.php"; ?>