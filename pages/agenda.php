<?php

require_once "../config/database.php";
require_once "../includes/functions.php";

$page_title = "Agenda Desa";


// ==========================================
// AMBIL DATA AGENDA
// ==========================================

$query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        title,
        event_date,
        event_time,
        location,
        description,
        status,
        created_at
    FROM agendas
    ORDER BY
        event_date ASC,
        event_time ASC,
        created_at DESC
    "
);


// ==========================================
// FUNGSI FORMAT TANGGAL
// ==========================================

function formatAgendaDate($date)
{

    if (empty($date)) {

        return "-";

    }


    $timestamp = strtotime($date);

    if (!$timestamp) {

        return "-";

    }


    $months = [

        1 => "Januari",
        2 => "Februari",
        3 => "Maret",
        4 => "April",
        5 => "Mei",
        6 => "Juni",
        7 => "Juli",
        8 => "Agustus",
        9 => "September",
        10 => "Oktober",
        11 => "November",
        12 => "Desember"

    ];


    return date(
        "d",
        $timestamp
    )
    . " "
    . $months[
        (int) date(
            "n",
            $timestamp
        )
    ]
    . " "
    . date(
        "Y",
        $timestamp
    );

}


// ==========================================
// FUNGSI FORMAT WAKTU
// ==========================================

function formatAgendaTime($time)
{

    if (empty($time)) {

        return "-";

    }


    $timestamp = strtotime($time);

    if (!$timestamp) {

        return $time;

    }


    return date(
        "H:i",
        $timestamp
    ) . " WIB";

}

?>

<?php include "../includes/header.php"; ?>

<?php include "../includes/navbar.php"; ?>


<style>

/* ==========================================
   AGENDA
========================================== */

.agenda-card {

    border: none;

    border-radius: 16px;

    background: #ffffff;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;

}


.agenda-card:hover {

    transform:
        translateY(-3px);

    box-shadow:
        0 10px 25px
        rgba(
            0,
            0,
            0,
            0.08
        ) !important;

}


/* ==========================================
   TANGGAL AGENDA
========================================== */

.agenda-date {

    width: 72px;

    min-width: 72px;

    height: 82px;

    border-radius: 14px;

    background: #eaf1ff;

    color: #0d6efd;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

}


.agenda-date-day {

    font-size: 26px;

    font-weight: 700;

    line-height: 1;

}


.agenda-date-month {

    font-size: 12px;

    font-weight: 600;

    margin-top: 5px;

}


.agenda-date-year {

    font-size: 11px;

    color: #6c757d;

    margin-top: 2px;

}


/* ==========================================
   DETAIL AGENDA
========================================== */

.agenda-info {

    color: #6c757d;

    font-size: 14px;

}


.agenda-info i {

    width: 18px;

}


/* ==========================================
   STATUS
========================================== */

.agenda-status {

    font-size: 12px;

    padding:
        6px
        10px;

    border-radius: 8px;

}


.agenda-status.akan-datang {

    background:
        #e8f5e9;

    color:
        #198754;

}


.agenda-status.selesai {

    background:
        #eef1f5;

    color:
        #6c757d;

}


.agenda-status.dibatalkan {

    background:
        #fdecec;

    color:
        #dc3545;

}


.agenda-status.default {

    background:
        #eaf1ff;

    color:
        #0d6efd;

}


/* ==========================================
   EMPTY STATE
========================================== */

.agenda-empty {

    padding:
        70px
        20px;

    text-align: center;

}

</style>



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

                KEGIATAN DESA

            </p>


            <h1
                class="fw-bold"
            >

                Agenda Desa

            </h1>


            <p
                class="text-muted
                       mb-0"
            >

                Informasi jadwal kegiatan dan agenda
                Desa Cisalada.

            </p>

        </div>

    </div>

</section>



<!-- ==========================================
     DAFTAR AGENDA
========================================== -->

<section
    class="section-padding"
>

    <div class="container">


        <?php if (
            $query
            &&
            mysqli_num_rows(
                $query
            ) > 0
        ): ?>


            <div
                class="row
                       g-4"
            >


                <?php while (
                    $agenda =
                        mysqli_fetch_assoc(
                            $query
                        )
                ): ?>


                    <?php

                    // ==================================
                    // STATUS
                    // ==================================

                    $status =
                        trim(
                            $agenda[
                                "status"
                            ]
                            ??
                            ""
                        );


                    $status_lower =
                        strtolower(
                            $status
                        );


                    $status_class =
                        "default";


                    if (
                        $status_lower
                        ===
                        "akan datang"
                    ) {

                        $status_class =
                            "akan-datang";

                    } elseif (
                        $status_lower
                        ===
                        "selesai"
                    ) {

                        $status_class =
                            "selesai";

                    } elseif (
                        $status_lower
                        ===
                        "dibatalkan"
                    ) {

                        $status_class =
                            "dibatalkan";

                    }


                    // ==================================
                    // TANGGAL
                    // ==================================

                    $date_timestamp =
                        strtotime(
                            $agenda[
                                "event_date"
                            ]
                        );


                    $day =
                        $date_timestamp
                        ?
                        date(
                            "d",
                            $date_timestamp
                        )
                        :
                        "-";


                    $month_names = [

                        1 => "Jan",
                        2 => "Feb",
                        3 => "Mar",
                        4 => "Apr",
                        5 => "Mei",
                        6 => "Jun",
                        7 => "Jul",
                        8 => "Agu",
                        9 => "Sep",
                        10 => "Okt",
                        11 => "Nov",
                        12 => "Des"

                    ];


                    $month =
                        $date_timestamp
                        ?
                        $month_names[
                            (int) date(
                                "n",
                                $date_timestamp
                            )
                        ]
                        :
                        "-";


                    $year =
                        $date_timestamp
                        ?
                        date(
                            "Y",
                            $date_timestamp
                        )
                        :
                        "-";

                    ?>


                    <div
                        class="col-12"
                    >


                        <div
                            class="card
                                   agenda-card
                                   shadow-sm
                                   h-100"
                        >


                            <div
                                class="card-body
                                       p-4"
                            >


                                <div
                                    class="d-flex
                                           gap-4"
                                >


                                    <!-- ==================================
                                         TANGGAL
                                    =================================== -->

                                    <div
                                        class="agenda-date"
                                    >


                                        <div
                                            class="agenda-date-day"
                                        >

                                            <?= clean(
                                                $day
                                            ) ?>

                                        </div>


                                        <div
                                            class="agenda-date-month"
                                        >

                                            <?= clean(
                                                $month
                                            ) ?>

                                        </div>


                                        <div
                                            class="agenda-date-year"
                                        >

                                            <?= clean(
                                                $year
                                            ) ?>

                                        </div>


                                    </div>



                                    <!-- ==================================
                                         KONTEN
                                    =================================== -->

                                    <div
                                        class="flex-grow-1"
                                    >


                                        <div
                                            class="d-flex
                                                   justify-content-between
                                                   align-items-start
                                                   gap-3
                                                   flex-wrap"
                                        >


                                            <h4
                                                class="fw-bold
                                                       mb-2"
                                            >

                                                <?= clean(
                                                    $agenda[
                                                        "title"
                                                    ]
                                                ) ?>

                                            </h4>


                                            <?php if (
                                                !empty(
                                                    $status
                                                )
                                            ): ?>


                                                <span
                                                    class="agenda-status
                                                           <?= $status_class ?>"
                                                >

                                                    <?= clean(
                                                        $status
                                                    ) ?>

                                                </span>


                                            <?php endif; ?>


                                        </div>



                                        <!-- ==================================
                                             WAKTU
                                        =================================== -->

                                        <?php if (
                                            !empty(
                                                $agenda[
                                                    "event_time"
                                                ]
                                            )
                                        ): ?>


                                            <div
                                                class="agenda-info
                                                       mb-2"
                                            >

                                                <i
                                                    class="bi bi-clock
                                                           me-1"
                                                ></i>


                                                <?= formatAgendaTime(
                                                    $agenda[
                                                        "event_time"
                                                    ]
                                                ) ?>


                                            </div>


                                        <?php endif; ?>



                                        <!-- ==================================
                                             LOKASI
                                        =================================== -->

                                        <?php if (
                                            !empty(
                                                $agenda[
                                                    "location"
                                                ]
                                            )
                                        ): ?>


                                            <div
                                                class="agenda-info
                                                       mb-2"
                                            >

                                                <i
                                                    class="bi bi-geo-alt
                                                           me-1"
                                                ></i>


                                                <?= clean(
                                                    $agenda[
                                                        "location"
                                                    ]
                                                ) ?>


                                            </div>


                                        <?php endif; ?>



                                        <!-- ==================================
                                             DESKRIPSI
                                        =================================== -->

                                        <?php if (
                                            !empty(
                                                $agenda[
                                                    "description"
                                                ]
                                            )
                                        ): ?>


                                            <div
                                                class="text-muted
                                                       mt-3"
                                                style="
                                                    white-space: pre-line;
                                                    line-height: 1.7;
                                                "
                                            >

                                                <?= clean(
                                                    $agenda[
                                                        "description"
                                                    ]
                                                ) ?>

                                            </div>


                                        <?php endif; ?>


                                    </div>


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
                class="agenda-empty"
            >


                <i
                    class="bi bi-calendar-event
                           fs-1
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Agenda

                </h4>


                <p
                    class="text-muted"
                >

                    Belum ada agenda atau kegiatan
                    Desa Cisalada yang tersedia saat ini.

                </p>


            </div>


        <?php endif; ?>


    </div>

</section>



<?php include "../includes/footer.php"; ?>