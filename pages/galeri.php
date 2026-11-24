<?php

require_once "../config/database.php";
require_once "../includes/functions.php";

$page_title = "Galeri";


// ==========================================
// AMBIL DATA GALERI
// ==========================================

$query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        title,
        media_type,
        file,
        category,
        year,
        created_at
    FROM gallery
    ORDER BY
        year DESC,
        created_at DESC
    "
);

?>

<?php include "../includes/header.php"; ?>

<?php include "../includes/navbar.php"; ?>


<style>

.gallery-card {

    border: none;

    border-radius: 16px;

    overflow: hidden;

    background: #ffffff;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;

}


.gallery-card:hover {

    transform:
        translateY(-4px);

    box-shadow:
        0 12px 28px
        rgba(
            0,
            0,
            0,
            0.10
        ) !important;

}


.gallery-media {

    position: relative;

    width: 100%;

    height: 240px;

    overflow: hidden;

    background: #f1f3f5;

}


.gallery-media img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


.gallery-video {

    width: 100%;

    height: 240px;

    object-fit: cover;

    background: #000;

}


.gallery-badge {

    position: absolute;

    top: 14px;

    left: 14px;

    z-index: 2;

}


.gallery-year {

    position: absolute;

    top: 14px;

    right: 14px;

    z-index: 2;

    background:
        rgba(
            0,
            0,
            0,
            0.65
        );

    color: #fff;

    padding:
        5px
        10px;

    border-radius: 8px;

    font-size: 12px;

}


.gallery-title {

    font-size: 18px;

    font-weight: 700;

    line-height: 1.4;

}


.gallery-category {

    font-size: 13px;

    color: #6c757d;

}


.gallery-empty {

    padding:
        80px
        20px;

    text-align: center;

}


@media (
    max-width: 767.98px
) {

    .gallery-media {

        height: 220px;

    }

    .gallery-video {

        height: 220px;

    }

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

                DOKUMENTASI DESA

            </p>


            <h1
                class="fw-bold"
            >

                Galeri Desa

            </h1>


            <p
                class="text-muted
                       mb-0"
            >

                Dokumentasi kegiatan dan berbagai
                momen Desa Cisalada.

            </p>

        </div>

    </div>

</section>



<!-- ==========================================
     GALERI
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
                    $gallery =
                        mysqli_fetch_assoc(
                            $query
                        )
                ): ?>


                    <?php

                    $media_type =
                        strtolower(
                            trim(
                                $gallery[
                                    "media_type"
                                ]
                                ??
                                "image"
                            )
                        );


                    $title =
                        trim(
                            $gallery[
                                "title"
                            ]
                            ??
                            ""
                        );


                    $file =
                        trim(
                            $gallery[
                                "file"
                            ]
                            ??
                            ""
                        );


                    $category =
                        trim(
                            $gallery[
                                "category"
                            ]
                            ??
                            ""
                        );


                    $year =
                        trim(
                            (string)
                            (
                                $gallery[
                                    "year"
                                ]
                                ??
                                ""
                            )
                        );


                    $file_url =
                        BASE_URL
                        .
                        "assets/uploads/gallery/"
                        .
                        rawurlencode(
                            basename(
                                $file
                            )
                        );

                    ?>


                    <div
                        class="col-12
                               col-md-6
                               col-lg-4"
                    >


                        <div
                            class="card
                                   gallery-card
                                   shadow-sm
                                   h-100"
                        >


                            <!-- MEDIA -->

                            <div
                                class="gallery-media"
                            >


                                <?php if (
                                    $media_type
                                    ===
                                    "video"
                                ): ?>


                                    <?php if (
                                        !empty(
                                            $file
                                        )
                                    ): ?>


                                        <video
                                            class="gallery-video"
                                            controls
                                            preload="metadata"
                                        >

                                            <source
                                                src="<?= clean($file_url) ?>"
                                            >

                                            Browser tidak
                                            mendukung video.

                                        </video>


                                    <?php else: ?>


                                        <div
                                            class="w-100
                                                   h-100
                                                   d-flex
                                                   align-items-center
                                                   justify-content-center
                                                   text-muted"
                                        >

                                            <i
                                                class="bi bi-camera-video
                                                       fs-1"
                                            ></i>

                                        </div>


                                    <?php endif; ?>


                                <?php else: ?>


                                    <?php if (
                                        !empty(
                                            $file
                                        )
                                    ): ?>


                                        <img
                                            src="<?= clean($file_url) ?>"
                                            alt="<?= clean(
                                                $title
                                                ?:
                                                "Galeri Desa Cisalada"
                                            ) ?>"
                                            loading="lazy"
                                        >


                                    <?php else: ?>


                                        <div
                                            class="w-100
                                                   h-100
                                                   d-flex
                                                   align-items-center
                                                   justify-content-center
                                                   text-muted"
                                        >

                                            <i
                                                class="bi bi-image
                                                       fs-1"
                                            ></i>

                                        </div>


                                    <?php endif; ?>


                                <?php endif; ?>



                                <!-- TYPE -->

                                <div
                                    class="gallery-badge"
                                >


                                    <?php if (
                                        $media_type
                                        ===
                                        "video"
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-danger"
                                        >

                                            <i
                                                class="bi bi-camera-video
                                                       me-1"
                                            ></i>

                                            Video

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="badge
                                                   bg-primary"
                                        >

                                            <i
                                                class="bi bi-image
                                                       me-1"
                                            ></i>

                                            Foto

                                        </span>


                                    <?php endif; ?>


                                </div>



                                <!-- YEAR -->

                                <?php if (
                                    !empty(
                                        $year
                                    )
                                ): ?>


                                    <div
                                        class="gallery-year"
                                    >

                                        <?= clean(
                                            $year
                                        ) ?>

                                    </div>


                                <?php endif; ?>


                            </div>



                            <!-- CONTENT -->

                            <div
                                class="card-body
                                       p-4"
                            >


                                <div
                                    class="gallery-title
                                           mb-2"
                                >

                                    <?= clean(
                                        $title
                                        ?:
                                        "Dokumentasi Desa"
                                    ) ?>

                                </div>


                                <?php if (
                                    !empty(
                                        $category
                                    )
                                ): ?>


                                    <div
                                        class="gallery-category"
                                    >

                                        <i
                                            class="bi bi-folder2-open
                                                   me-1"
                                        ></i>

                                        <?= clean(
                                            $category
                                        ) ?>

                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- EMPTY -->

            <div
                class="gallery-empty"
            >


                <i
                    class="bi bi-images
                           fs-1
                           text-muted"
                ></i>


                <h4
                    class="fw-bold
                           mt-3"
                >

                    Belum Ada Galeri

                </h4>


                <p
                    class="text-muted
                           mb-0"
                >

                    Belum ada dokumentasi foto atau video
                    Desa Cisalada yang tersedia saat ini.

                </p>


            </div>


        <?php endif; ?>


    </div>

</section>



<?php include "../includes/footer.php"; ?>