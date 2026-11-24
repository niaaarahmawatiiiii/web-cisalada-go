<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// ======================================================
// AMBIL DATA UMKM
// ======================================================

$umkm_locations = [];


$umkm_stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            owner,
            category,
            address,
            phone,
            maps_url,
            latitude,
            longitude,
            photo
        FROM umkm
        WHERE
            latitude IS NOT NULL
            AND longitude IS NOT NULL
            AND latitude <> ''
            AND longitude <> ''
        ORDER BY
            name ASC
        "
    );


if (
    $umkm_stmt
) {

    mysqli_stmt_execute(
        $umkm_stmt
    );


    $umkm_result =
        mysqli_stmt_get_result(
            $umkm_stmt
        );


    while (
        $row =
            mysqli_fetch_assoc(
                $umkm_result
            )
    ) {


        if (
            is_numeric(
                $row["latitude"]
            )
            &&
            is_numeric(
                $row["longitude"]
            )
        ) {


            $latitude =
                (float)
                $row["latitude"];


            $longitude =
                (float)
                $row["longitude"];


            if (
                $latitude >= -90
                &&
                $latitude <= 90
                &&
                $longitude >= -180
                &&
                $longitude <= 180
            ) {


                $row["latitude"] =
                    $latitude;


                $row["longitude"] =
                    $longitude;


                $umkm_locations[] =
                    $row;

            }

        }

    }


    mysqli_stmt_close(
        $umkm_stmt
    );

}


// ======================================================
// AMBIL DATA WISATA
// ======================================================

$tourism_locations = [];


$tourism_stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            description,
            location,
            latitude,
            longitude,
            facilities,
            operating_hours,
            ticket_price,
            contact,
            photo
        FROM tourism
        WHERE
            latitude IS NOT NULL
            AND longitude IS NOT NULL
            AND latitude <> ''
            AND longitude <> ''
        ORDER BY
            name ASC
        "
    );


if (
    $tourism_stmt
) {

    mysqli_stmt_execute(
        $tourism_stmt
    );


    $tourism_result =
        mysqli_stmt_get_result(
            $tourism_stmt
        );


    while (
        $row =
            mysqli_fetch_assoc(
                $tourism_result
            )
    ) {


        if (
            is_numeric(
                $row["latitude"]
            )
            &&
            is_numeric(
                $row["longitude"]
            )
        ) {


            $latitude =
                (float)
                $row["latitude"];


            $longitude =
                (float)
                $row["longitude"];


            if (
                $latitude >= -90
                &&
                $latitude <= 90
                &&
                $longitude >= -180
                &&
                $longitude <= 180
            ) {


                $row["latitude"] =
                    $latitude;


                $row["longitude"] =
                    $longitude;


                $tourism_locations[] =
                    $row;

            }

        }

    }


    mysqli_stmt_close(
        $tourism_stmt
    );

}


// ======================================================
// TOTAL LOKASI
// ======================================================

$total_umkm_locations =
    count(
        $umkm_locations
    );


$total_tourism_locations =
    count(
        $tourism_locations
    );


$total_locations =
    $total_umkm_locations
    +
    $total_tourism_locations;


// ======================================================
// DEFAULT CENTER
// ======================================================

$default_latitude =
    -6.73292622177123;


$default_longitude =
    106.79310635054685;


if (
    $total_umkm_locations > 0
) {


    $default_latitude =
        (float)
        $umkm_locations[0]["latitude"];


    $default_longitude =
        (float)
        $umkm_locations[0]["longitude"];


} elseif (
    $total_tourism_locations > 0
) {


    $default_latitude =
        (float)
        $tourism_locations[0]["latitude"];


    $default_longitude =
        (float)
        $tourism_locations[0]["longitude"];

}


$page_title =
    "Peta Potensi Desa";

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

        <?= clean(
            $page_title
        ) ?>

        - Cisalada GO

    </title>



    <!-- ==================================================
         BOOTSTRAP
    ================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >



    <!-- ==================================================
         BOOTSTRAP ICON
    ================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >



    <!-- ==================================================
         LEAFLET CSS
    ================================================== -->

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >



    <style>

        /* ==================================================
           BODY
        ================================================== */

        body {

            background:
                #f8f9fa;

            color:
                #212529;

        }



        /* ==================================================
           HERO
        ================================================== */

        .page-hero {

            background:
                #f8f9fa;

            padding:
                50px 0 40px;

        }



        /* ==================================================
           MAP CARD
        ================================================== */

        .map-card {

            border:
                none;

            border-radius:
                18px;

            overflow:
                hidden;

        }



        /* ==================================================
           MAP WRAPPER
        ================================================== */

        .map-wrapper {

            position:
                relative;

            width:
                100%;

            height:
                600px;

            background:
                #d0e8f2;

        }



        #mapContainer {

            width:
                100%;

            height:
                100%;

        }



        .leaflet-container {

            background:
                #c5dde7 !important;

        }



        /* ==================================================
           LOADING
        ================================================== */

        .map-loading {

            position:
                absolute;

            top:
                20px;

            left:
                50%;

            transform:
                translateX(-50%);

            z-index:
                1000;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.96
                );

            padding:
                10px 18px;

            border-radius:
                8px;

            font-size:
                13px;

            box-shadow:
                0 4px 15px
                rgba(
                    0,
                    0,
                    0,
                    0.10
                );

        }



        /* ==================================================
           LEGEND
        ================================================== */

        .map-legend {

            position:
                absolute;

            right:
                15px;

            top:
                15px;

            z-index:
                500;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.96
                );

            border:
                1px solid #e9ecef;

            border-radius:
                10px;

            padding:
                10px 12px;

            box-shadow:
                0 4px 14px
                rgba(
                    0,
                    0,
                    0,
                    0.10
                );

            font-size:
                12px;

        }



        .legend-item {

            display:
                flex;

            align-items:
                center;

            gap:
                7px;

            margin-bottom:
                6px;

        }


        .legend-item:last-child {

            margin-bottom:
                0;

        }



        .legend-marker {

            width:
                14px;

            height:
                14px;

            border-radius:
                50%;

        }


        .legend-umkm {

            background:
                #0d6efd;

        }


        .legend-wisata {

            background:
                #198754;

        }



        /* ==================================================
           CUSTOM MARKER
        ================================================== */

        .custom-map-marker {

            width:
                38px;

            height:
                38px;

            border-radius:
                50% 50% 50% 0;

            transform:
                rotate(
                    -45deg
                );

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            box-shadow:
                0 3px 8px
                rgba(
                    0,
                    0,
                    0,
                    0.28
                );

            border:
                3px solid
                #ffffff;

        }


        .custom-map-marker i {

            transform:
                rotate(
                    45deg
                );

            color:
                #ffffff;

            font-size:
                17px;

        }



        .marker-umkm {

            background:
                #0d6efd;

        }


        .marker-wisata {

            background:
                #198754;

        }



        /* ==================================================
           POPUP
        ================================================== */

        .map-popup {

            width:
                275px;

        }



        .map-popup-photo {

            width:
                100%;

            height:
                125px;

            object-fit:
                cover;

            border-radius:
                8px;

            display:
                block;

            margin-bottom:
                10px;

            background:
                #eef1f5;

        }



        .map-popup-photo-placeholder {

            width:
                100%;

            height:
                125px;

            border-radius:
                8px;

            background:
                #eef1f5;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            margin-bottom:
                10px;

        }



        .map-popup-photo-placeholder i {

            font-size:
                42px;

            color:
                #adb5bd;

        }



        .map-popup-badge {

            display:
                inline-block;

            padding:
                4px 8px;

            border-radius:
                6px;

            font-size:
                10px;

            font-weight:
                600;

            margin-bottom:
                6px;

        }


        .popup-badge-umkm {

            background:
                #eaf2ff;

            color:
                #0d6efd;

        }


        .popup-badge-wisata {

            background:
                #e8f7ef;

            color:
                #198754;

        }



        .map-popup-title {

            font-size:
                16px;

            font-weight:
                700;

            margin-bottom:
                5px;

        }



        .map-popup-info {

            font-size:
                12px;

            color:
                #6c757d;

            margin-bottom:
                4px;

        }



        .map-popup-link {

            display:
                inline-block;

            margin-top:
                7px;

            color:
                #0d6efd;

            font-size:
                12px;

            font-weight:
                600;

            text-decoration:
                none;

        }



        /* ==================================================
           LOCATION CARD
        ================================================== */

        .location-card {

            height:
                100%;

            border:
                1px solid #e9ecef;

            border-radius:
                14px;

            background:
                #ffffff;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

            overflow:
                hidden;

        }


        .location-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 8px 20px
                rgba(
                    0,
                    0,
                    0,
                    0.07
                );

        }



        .location-top {

            display:
                flex;

            gap:
                12px;

        }



        /* ==================================================
           LOCATION PHOTO
        ================================================== */

        .location-photo {

            width:
                82px;

            height:
                82px;

            min-width:
                82px;

            object-fit:
                cover;

            border-radius:
                12px;

            display:
                block;

            background:
                #eef1f5;

        }



        .location-photo-placeholder {

            width:
                82px;

            height:
                82px;

            min-width:
                82px;

            border-radius:
                12px;

            background:
                #eef1f5;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .location-photo-placeholder i {

            font-size:
                30px;

            color:
                #adb5bd;

        }



        /* ==================================================
           LOCATION CONTENT
        ================================================== */

        .location-content {

            min-width:
                0;

            flex-grow:
                1;

        }



        .location-title {

            word-break:
                break-word;

        }



        .location-type {

            font-size:
                11px;

            font-weight:
                600;

            margin-bottom:
                5px;

        }


        .location-type-umkm {

            color:
                #0d6efd;

        }


        .location-type-wisata {

            color:
                #198754;

        }



        .coordinate-text {

            font-family:
                monospace;

            font-size:
                0.76rem;

            color:
                #6c757d;

        }



        /* ==================================================
           BUTTON
        ================================================== */

        .action-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                7px;

            border-radius:
                9px;

        }



        /* ==================================================
           EMPTY
        ================================================== */

        .empty-box {

            padding:
                60px 20px;

            text-align:
                center;

            background:
                #ffffff;

            border:
                1px solid #e9ecef;

            border-radius:
                16px;

        }



        /* ==================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 767.98px
        ) {

            .map-wrapper {

                height:
                    480px;

            }


            .map-legend {

                top:
                    10px;

                right:
                    10px;

            }


            .location-photo {

                width:
                    65px;

                height:
                    65px;

                min-width:
                    65px;

            }


            .location-photo-placeholder {

                width:
                    65px;

                height:
                    65px;

                min-width:
                    65px;

            }

        }

    </style>

</head>


<body>


<!-- ======================================================
     NAVBAR
====================================================== -->

<?php

include "../includes/navbar.php";

?>



<!-- ======================================================
     BREADCRUMB
====================================================== -->

<div
    class="container
           pt-4"
>


    <nav
        aria-label="breadcrumb"
    >


        <ol
            class="breadcrumb
                   mb-0"
        >


            <li
                class="breadcrumb-item"
            >

                <a
                    href="<?= BASE_URL ?>"
                    class="text-decoration-none"
                >

                    Beranda

                </a>

            </li>


            <li
                class="breadcrumb-item"
            >

                Potensi Desa

            </li>


            <li
                class="breadcrumb-item
                       active"
            >

                Peta

            </li>


        </ol>


    </nav>


</div>



<!-- ======================================================
     HERO
====================================================== -->

<section
    class="page-hero"
>


    <div
        class="container
               text-center"
    >


        <div
            class="text-primary
                   fw-semibold
                   mb-2"
        >

            POTENSI DESA

        </div>


        <h1
            class="fw-bold
                   mb-3"
        >

            Peta Potensi Desa

        </h1>


        <p
            class="text-muted
                   mx-auto
                   mb-0"
            style="
                max-width: 720px;
            "
        >

            Temukan lokasi UMKM dan wisata
            Desa Cisalada dalam satu peta.

        </p>


    </div>


</section>



<!-- ======================================================
     MAP
====================================================== -->

<section
    class="pb-4"
>


    <div
        class="container"
    >


        <div
            class="card
                   map-card
                   shadow-sm"
        >


            <?php if (
                $total_locations > 0
            ): ?>


                <div
                    class="map-wrapper"
                >


                    <div
                        id="mapLoading"
                        class="map-loading"
                    >

                        <i
                            class="bi bi-arrow-repeat
                                   me-1"
                        ></i>


                        Memuat Peta...


                    </div>



                    <div
                        id="mapContainer"
                    ></div>



                    <!-- ==================================================
                         LEGEND
                    =================================================== -->

                    <div
                        class="map-legend"
                    >


                        <div
                            class="legend-item"
                        >

                            <span
                                class="legend-marker
                                       legend-umkm"
                            ></span>


                            <span>

                                UMKM

                            </span>

                        </div>


                        <div
                            class="legend-item"
                        >

                            <span
                                class="legend-marker
                                       legend-wisata"
                            ></span>


                            <span>

                                Wisata

                            </span>

                        </div>


                    </div>


                </div>


            <?php else: ?>


                <div
                    class="empty-box
                           m-3"
                >


                    <i
                        class="bi bi-map
                               fs-1
                               text-muted"
                    ></i>


                    <h4
                        class="fw-bold
                               mt-3"
                    >

                        Belum Ada Lokasi

                    </h4>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Belum ada UMKM atau wisata
                        yang memiliki koordinat.

                    </p>


                </div>


            <?php endif; ?>


        </div>


    </div>


</section>



<!-- ======================================================
     DAFTAR POTENSI
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <div
            class="mb-4"
        >


            <h3
                class="fw-bold
                       mb-1"
            >

                Lokasi Potensi Desa

            </h3>


            <p
                class="text-muted
                       mb-0"
            >

                <?= $total_locations ?>

                lokasi tersedia,
                terdiri dari

                <?= $total_umkm_locations ?>

                UMKM dan

                <?= $total_tourism_locations ?>

                wisata.

            </p>


        </div>



        <?php if (
            $total_locations > 0
        ): ?>


            <div
                class="row
                       g-3"
            >


                <!-- ==================================================
                     UMKM
                =================================================== -->

                <?php foreach (
                    $umkm_locations
                    as $location
                ): ?>


                    <?php

                    $photo_url =
                        "";


                    if (
                        !empty(
                            $location["photo"]
                        )
                    ) {


                        $photo_url =
                            BASE_URL
                            .
                            "assets/uploads/umkm/"
                            .
                            rawurlencode(
                                basename(
                                    $location["photo"]
                                )
                            );

                    }


                    ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <div
                            class="location-card
                                   p-3"
                        >


                            <div
                                class="location-top"
                            >


                                <!-- FOTO -->

                                <?php if (
                                    !empty(
                                        $photo_url
                                    )
                                ): ?>


                                    <img
                                        src="<?= clean($photo_url) ?>"
                                        alt="<?= clean($location["name"]) ?>"
                                        class="location-photo"
                                    >


                                <?php else: ?>


                                    <div
                                        class="location-photo-placeholder"
                                    >

                                        <i
                                            class="bi bi-shop"
                                        ></i>

                                    </div>


                                <?php endif; ?>



                                <!-- CONTENT -->

                                <div
                                    class="location-content"
                                >


                                    <div
                                        class="location-type
                                               location-type-umkm"
                                    >

                                        UMKM

                                    </div>


                                    <h5
                                        class="fw-bold
                                               location-title
                                               mb-1"
                                    >

                                        <?= clean(
                                            $location["name"]
                                        ) ?>


                                    </h5>


                                    <?php if (
                                        !empty(
                                            $location["category"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-primary
                                                   mb-2"
                                        >

                                            <?= clean(
                                                $location["category"]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>


                                    <?php if (
                                        !empty(
                                            $location["owner"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-muted
                                                   mb-2"
                                        >

                                            <i
                                                class="bi bi-person
                                                       me-1"
                                            ></i>


                                            <?= clean(
                                                $location["owner"]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>


                                </div>


                            </div>



                            <?php if (
                                !empty(
                                    $location["address"]
                                )
                            ): ?>


                                <div
                                    class="small
                                           text-muted
                                           mt-3
                                           mb-2"
                                >

                                    <i
                                        class="bi bi-geo-alt
                                               me-1"
                                    ></i>


                                    <?= clean(
                                        mb_strimwidth(
                                            $location["address"],
                                            0,
                                            90,
                                            "..."
                                        )
                                    ) ?>


                                </div>


                            <?php endif; ?>



                            <div
                                class="coordinate-text
                                       mb-3"
                            >

                                <?= number_format(
                                    (float)
                                    $location["latitude"],
                                    8,
                                    ".",
                                    ""
                                ) ?>


                                ,


                                <?= number_format(
                                    (float)
                                    $location["longitude"],
                                    8,
                                    ".",
                                    ""
                                ) ?>


                            </div>



                            <div
                                class="d-flex
                                       gap-2"
                            >


                                <a
                                    href="umkm-detail.php?id=<?= (int) $location["id"] ?>"
                                    class="btn
                                           btn-primary
                                           btn-sm
                                           action-button"
                                >

                                    <i
                                        class="bi bi-shop"
                                    ></i>


                                    Detail

                                </a>


                                <a
                                    href="https://www.google.com/maps/search/?api=1&query=<?= (float) $location["latitude"] ?>,<?= (float) $location["longitude"] ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn
                                           btn-outline-danger
                                           btn-sm
                                           action-button"
                                >

                                    <i
                                        class="bi bi-google"
                                    ></i>


                                    Maps

                                </a>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>



                <!-- ==================================================
                     WISATA
                =================================================== -->

                <?php foreach (
                    $tourism_locations
                    as $location
                ): ?>


                    <?php

                    $photo_url =
                        "";


                    if (
                        !empty(
                            $location["photo"]
                        )
                    ) {


                        $photo_url =
                            BASE_URL
                            .
                            "assets/uploads/wisata/"
                            .
                            rawurlencode(
                                basename(
                                    $location["photo"]
                                )
                            );

                    }


                    ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <div
                            class="location-card
                                   p-3"
                        >


                            <div
                                class="location-top"
                            >


                                <!-- FOTO -->

                                <?php if (
                                    !empty(
                                        $photo_url
                                    )
                                ): ?>


                                    <img
                                        src="<?= clean($photo_url) ?>"
                                        alt="<?= clean($location["name"]) ?>"
                                        class="location-photo"
                                    >


                                <?php else: ?>


                                    <div
                                        class="location-photo-placeholder"
                                    >

                                        <i
                                            class="bi bi-tree"
                                        ></i>

                                    </div>


                                <?php endif; ?>



                                <!-- CONTENT -->

                                <div
                                    class="location-content"
                                >


                                    <div
                                        class="location-type
                                               location-type-wisata"
                                    >

                                        WISATA

                                    </div>


                                    <h5
                                        class="fw-bold
                                               location-title
                                               mb-1"
                                    >

                                        <?= clean(
                                            $location["name"]
                                        ) ?>


                                    </h5>


                                    <?php if (
                                        !empty(
                                            $location["operating_hours"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-muted
                                                   mb-2"
                                        >

                                            <i
                                                class="bi bi-clock
                                                       me-1"
                                            ></i>


                                            <?= clean(
                                                $location["operating_hours"]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>


                                </div>


                            </div>



                            <?php if (
                                !empty(
                                    $location["location"]
                                )
                            ): ?>


                                <div
                                    class="small
                                           text-muted
                                           mt-3
                                           mb-2"
                                >

                                    <i
                                        class="bi bi-geo-alt
                                               me-1"
                                    ></i>


                                    <?= clean(
                                        mb_strimwidth(
                                            $location["location"],
                                            0,
                                            90,
                                            "..."
                                        )
                                    ) ?>


                                </div>


                            <?php endif; ?>



                            <div
                                class="coordinate-text
                                       mb-3"
                            >

                                <?= number_format(
                                    (float)
                                    $location["latitude"],
                                    8,
                                    ".",
                                    ""
                                ) ?>


                                ,


                                <?= number_format(
                                    (float)
                                    $location["longitude"],
                                    8,
                                    ".",
                                    ""
                                ) ?>


                            </div>



                            <div
                                class="d-flex
                                       gap-2"
                            >


                                <a
                                    href="wisata-detail.php?id=<?= (int) $location["id"] ?>"
                                    class="btn
                                           btn-success
                                           btn-sm
                                           action-button"
                                >

                                    <i
                                        class="bi bi-tree"
                                    ></i>


                                    Detail

                                </a>


                                <a
                                    href="https://www.google.com/maps/search/?api=1&query=<?= (float) $location["latitude"] ?>,<?= (float) $location["longitude"] ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn
                                           btn-outline-danger
                                           btn-sm
                                           action-button"
                                >

                                    <i
                                        class="bi bi-google"
                                    ></i>


                                    Maps

                                </a>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </div>


</section>



<!-- ======================================================
     FOOTER
====================================================== -->

<?php

include "../includes/footer.php";

?>



<!-- ======================================================
     LEAFLET JS
====================================================== -->

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>



<!-- ======================================================
     BOOTSTRAP JS
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<?php if (
    $total_locations > 0
): ?>


<script>

    // ======================================================
    // START MAP
    // ======================================================

    if (
        document.readyState ===
        "loading"
    ) {

        document.addEventListener(
            "DOMContentLoaded",
            initMap
        );

    } else {

        initMap();

    }



    // ======================================================
    // ESCAPE HTML
    // ======================================================

    function escapeHtml(
        value
    ) {

        return String(
            value ?? ""
        )
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );

    }



    // ======================================================
    // MAP
    // ======================================================

    function initMap() {


        const loading =
            document.getElementById(
                "mapLoading"
            );


        const map =
            L.map(
                "mapContainer",
                {

                    preferCanvas:
                        true,

                    zoomControl:
                        true

                }
            )
            .setView(
                [
                    <?= $default_latitude ?>,
                    <?= $default_longitude ?>
                ],
                14
            );



        // ==================================================
        // TILE LAYERS
        // VERSI YANG SUDAH BERHASIL
        // ==================================================

        const tileLayers = [

            {

                name:
                    "Wikimedia",

                url:
                    "https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png"

            },


            {

                name:
                    "Esri",

                url:
                    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}"

            },


            {

                name:
                    "OpenStreetMap",

                url:
                    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"

            }

        ];



        tileLayers.forEach(
            (
                layer,
                idx
            ) => {


                setTimeout(
                    () => {


                        L.tileLayer(
                            layer.url,
                            {

                                attribution:
                                    layer.name,

                                maxZoom:
                                    19,

                                minZoom:
                                    1

                            }
                        )
                        .addTo(
                            map
                        );


                    },
                    idx
                    *
                    1500
                );

            }
        );



        // ==================================================
        // DATA
        // ==================================================

        const umkmLocations =
            <?= json_encode(
                $umkm_locations,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_NUMERIC_CHECK
            ) ?>;


        const tourismLocations =
            <?= json_encode(
                $tourism_locations,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_NUMERIC_CHECK
            ) ?>;



        // ==================================================
        // GROUP
        // ==================================================

        const allMarkers =
            L.featureGroup()
            .addTo(
                map
            );



        // ==================================================
        // CUSTOM ICON UMKM
        // ==================================================

        const umkmIcon =
            L.divIcon(
                {

                    className:
                        "",

                    html:
                        `
                        <div class="custom-map-marker marker-umkm">
                            <i class="bi bi-shop"></i>
                        </div>
                        `,

                    iconSize:
                        [
                            38,
                            38
                        ],

                    iconAnchor:
                        [
                            19,
                            38
                        ],

                    popupAnchor:
                        [
                            0,
                            -38
                        ]

                }
            );



        // ==================================================
        // CUSTOM ICON WISATA
        // ==================================================

        const wisataIcon =
            L.divIcon(
                {

                    className:
                        "",

                    html:
                        `
                        <div class="custom-map-marker marker-wisata">
                            <i class="bi bi-tree"></i>
                        </div>
                        `,

                    iconSize:
                        [
                            38,
                            38
                        ],

                    iconAnchor:
                        [
                            19,
                            38
                        ],

                    popupAnchor:
                        [
                            0,
                            -38
                        ]

                }
            );



        // ==================================================
        // MARKER UMKM
        // ==================================================

        umkmLocations.forEach(
            function (
                loc
            ) {


                const marker =
                    L.marker(
                        [
                            loc.latitude,
                            loc.longitude
                        ],
                        {
                            icon:
                                umkmIcon
                        }
                    );



                // ==========================================
                // FOTO
                // ==========================================

                let photoHtml =
                    "";


                if (
                    loc.photo
                    &&
                    String(
                        loc.photo
                    ).trim()
                    !==
                    ""
                ) {


                    const photoFile =
                        String(
                            loc.photo
                        )
                        .split(
                            "/"
                        )
                        .pop();


                    const photoUrl =
                        "<?= BASE_URL ?>assets/uploads/umkm/"
                        +
                        encodeURIComponent(
                            photoFile
                        );


                    photoHtml =

                        "<img "
                        +

                        "src='"
                        +
                        photoUrl
                        +
                        "' "
                        +

                        "alt='"
                        +
                        escapeHtml(
                            loc.name
                        )
                        +
                        "' "
                        +

                        "class='map-popup-photo' "
                        +

                        "onerror=\"this.style.display='none';\" "
                        +

                        ">";


                } else {


                    photoHtml =

                        "<div class='map-popup-photo-placeholder'>"

                        +

                        "<i class='bi bi-shop'></i>"

                        +

                        "</div>";

                }



                // ==========================================
                // POPUP
                // ==========================================

                let popup =

                    "<div class='map-popup'>"

                    +

                    photoHtml

                    +

                    "<div class='map-popup-badge popup-badge-umkm'>"

                    +

                    "UMKM"

                    +

                    "</div>"

                    +

                    "<div class='map-popup-title'>"

                    +

                    escapeHtml(
                        loc.name
                    )

                    +

                    "</div>";



                if (
                    loc.category
                ) {

                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-tag me-1'></i>"

                        +

                        escapeHtml(
                            loc.category
                        )

                        +

                        "</div>";

                }



                if (
                    loc.owner
                ) {

                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-person me-1'></i>"

                        +

                        escapeHtml(
                            loc.owner
                        )

                        +

                        "</div>";

                }



                if (
                    loc.address
                ) {

                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-geo-alt me-1'></i>"

                        +

                        escapeHtml(
                            String(
                                loc.address
                            )
                            .substring(
                                0,
                                85
                            )
                        )

                        +

                        "</div>";

                }



                popup +=

                    "<div class='map-popup-info'>"

                    +

                    "<i class='bi bi-crosshair me-1'></i>"

                    +

                    Number(
                        loc.latitude
                    )
                    .toFixed(
                        8
                    )

                    +

                    ", "

                    +

                    Number(
                        loc.longitude
                    )
                    .toFixed(
                        8
                    )

                    +

                    "</div>";


                popup +=

                    "<a "
                    +

                    "href='umkm-detail.php?id="
                    +
                    encodeURIComponent(
                        loc.id
                    )
                    +
                    "' "
                    +

                    "class='map-popup-link'>"

                    +

                    "Lihat Detail UMKM"

                    +

                    "</a>";


                popup +=
                    "</div>";



                marker
                    .bindPopup(
                        popup
                    );


                marker.addTo(
                    allMarkers
                );

            }
        );



        // ==================================================
        // MARKER WISATA
        // ==================================================

        tourismLocations.forEach(
            function (
                loc
            ) {


                const marker =
                    L.marker(
                        [
                            loc.latitude,
                            loc.longitude
                        ],
                        {
                            icon:
                                wisataIcon
                        }
                    );



                // ==========================================
                // FOTO
                // ==========================================

                let photoHtml =
                    "";


                if (
                    loc.photo
                    &&
                    String(
                        loc.photo
                    ).trim()
                    !==
                    ""
                ) {


                    const photoFile =
                        String(
                            loc.photo
                        )
                        .split(
                            "/"
                        )
                        .pop();


                    const photoUrl =
                        "<?= BASE_URL ?>assets/uploads/wisata/"
                        +
                        encodeURIComponent(
                            photoFile
                        );


                    photoHtml =

                        "<img "
                        +

                        "src='"
                        +
                        photoUrl
                        +
                        "' "
                        +

                        "alt='"
                        +
                        escapeHtml(
                            loc.name
                        )
                        +
                        "' "
                        +

                        "class='map-popup-photo' "
                        +

                        "onerror=\"this.style.display='none';\" "
                        +

                        ">";


                } else {


                    photoHtml =

                        "<div class='map-popup-photo-placeholder'>"

                        +

                        "<i class='bi bi-tree'></i>"

                        +

                        "</div>";

                }



                // ==========================================
                // POPUP
                // ==========================================

                let popup =

                    "<div class='map-popup'>"

                    +

                    photoHtml

                    +

                    "<div class='map-popup-badge popup-badge-wisata'>"

                    +

                    "WISATA"

                    +

                    "</div>"

                    +

                    "<div class='map-popup-title'>"

                    +

                    escapeHtml(
                        loc.name
                    )

                    +

                    "</div>";



                if (
                    loc.location
                ) {

                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-geo-alt me-1'></i>"

                        +

                        escapeHtml(
                            String(
                                loc.location
                            )
                            .substring(
                                0,
                                85
                            )
                        )

                        +

                        "</div>";

                }



                if (
                    loc.operating_hours
                ) {

                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-clock me-1'></i>"

                        +

                        escapeHtml(
                            loc.operating_hours
                        )

                        +

                        "</div>";

                }



                if (
                    Number(
                        loc.ticket_price
                    )
                    >
                    0
                ) {

                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-ticket-perforated me-1'></i>"

                        +

                        "Rp "
                        +
                        Number(
                            loc.ticket_price
                        )
                        .toLocaleString(
                            "id-ID"
                        )

                        +

                        "</div>";


                } else {


                    popup +=

                        "<div class='map-popup-info'>"

                        +

                        "<i class='bi bi-ticket-perforated me-1'></i>"

                        +

                        "Gratis"

                        +

                        "</div>";

                }



                popup +=

                    "<div class='map-popup-info'>"

                    +

                    "<i class='bi bi-crosshair me-1'></i>"

                    +

                    Number(
                        loc.latitude
                    )
                    .toFixed(
                        8
                    )

                    +

                    ", "

                    +

                    Number(
                        loc.longitude
                    )
                    .toFixed(
                        8
                    )

                    +

                    "</div>";


                popup +=

                    "<a "
                    +

                    "href='wisata-detail.php?id="
                    +
                    encodeURIComponent(
                        loc.id
                    )
                    +
                    "' "
                    +

                    "class='map-popup-link'>"

                    +

                    "Lihat Detail Wisata"

                    +

                    "</a>";


                popup +=
                    "</div>";



                marker
                    .bindPopup(
                        popup
                    );


                marker.addTo(
                    allMarkers
                );

            }
        );



        // ==================================================
        // SESUAIKAN PETA KE SEMUA MARKER
        // ==================================================

        if (
            allMarkers.getLayers().length
            >
            0
        ) {


            setTimeout(
                function () {


                    const bounds =
                        allMarkers.getBounds();


                    if (
                        bounds.isValid()
                    ) {


                        if (
                            allMarkers.getLayers().length
                            ===
                            1
                        ) {


                            map.setView(
                                allMarkers
                                .getLayers()[0]
                                .getLatLng(),
                                16
                            );


                        } else {


                            map.fitBounds(
                                bounds.pad(
                                    0.12
                                )
                            );

                        }

                    }


                },
                600
            );

        }



        // ==================================================
        // INVALIDATE SIZE
        // ==================================================

        setTimeout(
            function () {

                map.invalidateSize();

            },
            300
        );


        setTimeout(
            function () {

                map.invalidateSize();

            },
            1000
        );


        setTimeout(
            function () {

                map.invalidateSize();

            },
            2000
        );



        window.addEventListener(
            "resize",
            function () {

                map.invalidateSize();

            }
        );



        // ==================================================
        // HIDE LOADING
        // ==================================================

        if (
            loading
        ) {


            setTimeout(
                function () {

                    loading.style.display =
                        "none";

                },
                2500
            );

        }


    }

</script>


<?php endif; ?>


</body>

</html>