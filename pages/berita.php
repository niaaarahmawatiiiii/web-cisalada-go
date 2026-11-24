<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// =========================================================
// PENGATURAN HALAMAN
// =========================================================

$page_title = "Berita Desa";


// =========================================================
// MODE DETAIL BERITA
// =========================================================

$detail_id = 0;

if (
    isset($_GET["id"])
    &&
    is_numeric($_GET["id"])
) {

    $detail_id =
        (int) $_GET["id"];

}


// =========================================================
// DATA DETAIL BERITA
// =========================================================

$detail_news = null;


if (
    $detail_id > 0
) {

    $stmt =
        mysqli_prepare(
            $conn,
            "
            SELECT
                news.*,
                news_categories.name AS category_name

            FROM news

            LEFT JOIN news_categories
                ON news.category_id =
                   news_categories.id

            WHERE news.id = ?

            LIMIT 1
            "
        );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $detail_id
    );


    mysqli_stmt_execute(
        $stmt
    );


    $detail_result =
        mysqli_stmt_get_result(
            $stmt
        );


    $detail_news =
        mysqli_fetch_assoc(
            $detail_result
        );


    mysqli_stmt_close(
        $stmt
    );


    if (
        $detail_news
    ) {

        $page_title =
            $detail_news["title"];

    }

}


// =========================================================
// DATA DAFTAR BERITA
// =========================================================

$news_list = [];


$list_query = mysqli_query(
    $conn,
    "
    SELECT
        news.*,
        news_categories.name AS category_name

    FROM news

    LEFT JOIN news_categories
        ON news.category_id =
           news_categories.id

    ORDER BY
        COALESCE(
            news.published_at,
            news.created_at
        ) DESC
    "
);


if (
    $list_query
) {

    while (
        $row =
        mysqli_fetch_assoc(
            $list_query
        )
    ) {

        $news_list[] =
            $row;

    }

}


// =========================================================
// FUNGSI TANGGAL
// =========================================================

function formatBeritaDate(
    $date
) {

    if (
        empty($date)
    ) {

        return "-";

    }


    $timestamp =
        strtotime($date);


    if (
        !$timestamp
    ) {

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


// =========================================================
// FUNGSI RINGKASAN BERITA
// =========================================================

function getBeritaExcerpt(
    $content,
    $length = 160
) {

    $text =
        strip_tags(
            $content ?? ""
        );


    $text =
        trim(
            preg_replace(
                '/\s+/',
                ' ',
                $text
            )
        );


    if (
        $text === ""
    ) {

        return
            "Belum ada ringkasan berita.";

    }


    if (
        function_exists(
            "mb_strlen"
        )
        &&
        mb_strlen(
            $text
        ) > $length
    ) {

        return
            mb_substr(
                $text,
                0,
                $length
            )
            . "...";

    }


    if (
        strlen($text) > $length
    ) {

        return
            substr(
                $text,
                0,
                $length
            )
            . "...";

    }


    return $text;

}


// =========================================================
// HEADER PUBLIC
// =========================================================

include "../includes/header.php";


// =========================================================
// NAVBAR PUBLIC
// =========================================================

include "../includes/navbar.php";

?>

<style>

    /* =====================================================
       HALAMAN BERITA
    ===================================================== */

    .berita-hero {

        background:
            linear-gradient(
                180deg,
                #f8faff 0%,
                #ffffff 100%
            );

        border-bottom:
            1px solid #eef1f5;

    }


    .berita-card {

        border: 0;

        border-radius: 16px;

        overflow: hidden;

        background: #ffffff;

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;

    }


    .berita-card:hover {

        transform:
            translateY(-4px);

        box-shadow:
            0 12px 28px
            rgba(
                0,
                0,
                0,
                0.08
            ) !important;

    }


    .berita-image {

        width: 100%;

        height: 220px;

        object-fit: cover;

        display: block;

    }


    .berita-image-placeholder {

        width: 100%;

        height: 220px;

        background: #f1f4f8;

        display: flex;

        align-items: center;

        justify-content: center;

        color: #9aa4b2;

    }


    .berita-category {

        display: inline-block;

        padding:
            6px
            10px;

        border-radius: 8px;

        background:
            #eaf1ff;

        color:
            #0d6efd;

        font-size: 12px;

        font-weight: 600;

    }


    .berita-title {

        color:
            #172033;

        line-height:
            1.4;

        display:
            -webkit-box;

        -webkit-line-clamp:
            2;

        -webkit-box-orient:
            vertical;

        overflow:
            hidden;

    }


    .berita-excerpt {

        color:
            #6c757d;

        line-height:
            1.7;

    }


    .berita-meta {

        color:
            #8a94a6;

        font-size:
            13px;

    }


    .berita-detail-image {

        width: 100%;

        max-height: 520px;

        object-fit: cover;

        border-radius: 16px;

    }


    .berita-detail-content {

        color:
            #343a40;

        font-size:
            16px;

        line-height:
            1.9;

    }


    .berita-detail-content p {

        margin-bottom:
            1rem;

    }


    .berita-empty {

        border-radius:
            16px;

        background:
            #ffffff;

        padding:
            70px
            20px;

    }


    .back-button {

        border-radius:
            10px;

    }

</style>


<!-- =====================================================
     HERO
===================================================== -->

<section
    class="berita-hero
           py-5"
>

    <div class="container">


        <div class="text-center">


            <span
                class="badge
                       bg-primary-subtle
                       text-primary
                       px-3
                       py-2
                       mb-3"
            >

                INFORMASI DESA

            </span>


            <?php if (
                $detail_news
            ): ?>


                <h1
                    class="fw-bold
                           mb-2"
                >

                    Berita Desa

                </h1>


                <p
                    class="text-muted
                           mb-0"
                >

                    Informasi terbaru
                    dari Desa Cisalada.

                </p>


            <?php else: ?>


                <h1
                    class="fw-bold
                           mb-2"
                >

                    Berita Desa Cisalada

                </h1>


                <p
                    class="text-muted
                           mb-0"
                >

                    Informasi, kegiatan, dan kabar
                    terbaru dari Desa Cisalada.

                </p>


            <?php endif; ?>


        </div>


    </div>

</section>



<!-- =====================================================
     MODE DETAIL
===================================================== -->

<?php if (
    $detail_id > 0
): ?>


    <?php if (
        $detail_news
    ): ?>


        <section class="py-5">


            <div class="container">


                <div
                    class="row
                           justify-content-center"
                >


                    <div
                        class="col-lg-9"
                    >


                        <!-- KEMBALI -->

                        <div
                            class="mb-4"
                        >

                            <a
                                href="berita.php"
                                class="btn
                                       btn-outline-primary
                                       btn-sm
                                       back-button"
                            >

                                <i
                                    class="bi bi-arrow-left
                                           me-1"
                                ></i>

                                Kembali ke Berita

                            </a>

                        </div>



                        <!-- BERITA DETAIL -->

                        <article
                            class="card
                                   border-0
                                   shadow-sm
                                   overflow-hidden"
                        >

                            <div
                                class="card-body
                                       p-4
                                       p-lg-5"
                            >


                                <!-- KATEGORI -->

                                <div
                                    class="mb-3"
                                >

                                    <span
                                        class="berita-category"
                                    >

                                        <?= clean(
                                            $detail_news[
                                                "category_name"
                                            ]
                                            ??
                                            "Berita Desa"
                                        ) ?>

                                    </span>

                                </div>



                                <!-- JUDUL -->

                                <h1
                                    class="fw-bold
                                           mb-3"
                                >

                                    <?= clean(
                                        $detail_news[
                                            "title"
                                        ]
                                    ) ?>

                                </h1>



                                <!-- META -->

                                <div
                                    class="berita-meta
                                           mb-4"
                                >


                                    <i
                                        class="bi bi-calendar3
                                               me-1"
                                    ></i>


                                    <?= formatBeritaDate(
                                        !empty(
                                            $detail_news[
                                                "published_at"
                                            ]
                                        )
                                        ?
                                        $detail_news[
                                            "published_at"
                                        ]
                                        :
                                        $detail_news[
                                            "created_at"
                                        ]
                                    ) ?>


                                    <?php if (
                                        !empty(
                                            $detail_news[
                                                "author"
                                            ]
                                        )
                                    ): ?>


                                        <span
                                            class="mx-2"
                                        >
                                            •
                                        </span>


                                        <i
                                            class="bi bi-person
                                                   me-1"
                                        ></i>


                                        <?= clean(
                                            $detail_news[
                                                "author"
                                            ]
                                        ) ?>


                                    <?php endif; ?>


                                </div>



                                <!-- GAMBAR -->

                                <?php if (
                                    !empty(
                                        $detail_news[
                                            "image"
                                        ]
                                    )
                                ): ?>


                                    <div
                                        class="mb-4"
                                    >

                                        <img
                                            src="<?= BASE_URL ?>assets/uploads/berita/<?= clean($detail_news["image"]) ?>"
                                            class="berita-detail-image"
                                            alt="<?= clean(
                                                $detail_news[
                                                    "title"
                                                ]
                                            ) ?>"
                                        >

                                    </div>


                                <?php endif; ?>



                                <!-- ISI -->

                                <div
                                    class="berita-detail-content"
                                >

                                    <?php

                                    $content =
                                        $detail_news[
                                            "content"
                                        ]
                                        ??
                                        "";

                                    ?>


                                    <?php if (
                                        trim(
                                            strip_tags(
                                                $content
                                            )
                                        ) !== ""
                                    ): ?>


                                        <?= nl2br(
                                            clean(
                                                $content
                                            )
                                        ) ?>


                                    <?php else: ?>


                                        <p
                                            class="text-muted"
                                        >

                                            Isi berita
                                            belum tersedia.

                                        </p>


                                    <?php endif; ?>


                                </div>


                            </div>

                        </article>


                    </div>


                </div>


            </div>


        </section>


    <?php else: ?>


        <!-- =================================================
             BERITA TIDAK DITEMUKAN
        ================================================= -->

        <section
            class="py-5"
        >

            <div class="container">


                <div
                    class="berita-empty
                           shadow-sm
                           text-center"
                >


                    <i
                        class="bi bi-newspaper
                               fs-1
                               text-muted"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        Berita Tidak Ditemukan

                    </h3>


                    <p
                        class="text-muted"
                    >

                        Berita yang kamu cari
                        tidak tersedia.

                    </p>


                    <a
                        href="berita.php"
                        class="btn btn-primary"
                    >

                        <i
                            class="bi bi-arrow-left
                                   me-1"
                        ></i>

                        Kembali ke Berita

                    </a>


                </div>


            </div>

        </section>


    <?php endif; ?>


<?php else: ?>


    <!-- =====================================================
         MODE DAFTAR BERITA
    ===================================================== -->

    <section
        class="py-5
               bg-light"
    >

        <div class="container">


            <?php if (
                count(
                    $news_list
                ) > 0
            ): ?>


                <!-- HEADER DAFTAR -->

                <div
                    class="d-flex
                           flex-wrap
                           justify-content-between
                           align-items-center
                           mb-4"
                >


                    <div>


                        <h2
                            class="fw-bold
                                   mb-1"
                        >

                            Berita Terbaru

                        </h2>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Ikuti informasi dan kegiatan
                            terbaru Desa Cisalada.

                        </p>


                    </div>


                    <span
                        class="text-muted
                               small"
                    >

                        <?= count(
                            $news_list
                        ) ?>

                        berita tersedia

                    </span>


                </div>



                <!-- GRID BERITA -->

                <div
                    class="row
                           g-4"
                >


                    <?php foreach (
                        $news_list
                        as $news
                    ): ?>


                        <div
                            class="col-md-6
                                   col-lg-4"
                        >


                            <article
                                class="card
                                       berita-card
                                       shadow-sm
                                       h-100"
                            >


                                <!-- GAMBAR -->

                                <?php if (
                                    !empty(
                                        $news[
                                            "image"
                                        ]
                                    )
                                ): ?>


                                    <img
                                        src="<?= BASE_URL ?>assets/uploads/berita/<?= clean($news["image"]) ?>"
                                        class="berita-image"
                                        alt="<?= clean(
                                            $news[
                                                "title"
                                            ]
                                        ) ?>"
                                    >


                                <?php else: ?>


                                    <div
                                        class="berita-image-placeholder"
                                    >

                                        <i
                                            class="bi bi-newspaper
                                                   fs-1"
                                        ></i>

                                    </div>


                                <?php endif; ?>



                                <!-- ISI CARD -->

                                <div
                                    class="card-body
                                           p-4
                                           d-flex
                                           flex-column"
                                >


                                    <!-- KATEGORI -->

                                    <div
                                        class="mb-3"
                                    >

                                        <span
                                            class="berita-category"
                                        >

                                            <?= clean(
                                                $news[
                                                    "category_name"
                                                ]
                                                ??
                                                "Berita Desa"
                                            ) ?>

                                        </span>

                                    </div>



                                    <!-- JUDUL -->

                                    <h5
                                        class="berita-title
                                               fw-bold
                                               mb-2"
                                    >

                                        <?= clean(
                                            $news[
                                                "title"
                                            ]
                                        ) ?>

                                    </h5>



                                    <!-- META -->

                                    <div
                                        class="berita-meta
                                               mb-3"
                                    >

                                        <i
                                            class="bi bi-calendar3
                                                   me-1"
                                        ></i>


                                        <?= formatBeritaDate(
                                            !empty(
                                                $news[
                                                    "published_at"
                                                ]
                                            )
                                            ?
                                            $news[
                                                "published_at"
                                            ]
                                            :
                                            $news[
                                                "created_at"
                                            ]
                                        ) ?>


                                        <?php if (
                                            !empty(
                                                $news[
                                                    "author"
                                                ]
                                            )
                                        ): ?>


                                            <span
                                                class="mx-1"
                                            >
                                                •
                                            </span>


                                            <?= clean(
                                                $news[
                                                    "author"
                                                ]
                                            ) ?>


                                        <?php endif; ?>


                                    </div>



                                    <!-- RINGKASAN -->

                                    <p
                                        class="berita-excerpt
                                               mb-4"
                                    >

                                        <?= clean(
                                            getBeritaExcerpt(
                                                $news[
                                                    "content"
                                                ]
                                                ??
                                                ""
                                            )
                                        ) ?>

                                    </p>



                                    <!-- TOMBOL -->

                                    <div
                                        class="mt-auto"
                                    >

                                        <a
                                            href="berita.php?id=<?= (int) $news["id"] ?>"
                                            class="btn
                                                   btn-outline-primary
                                                   w-100"
                                        >

                                            Baca Selengkapnya

                                            <i
                                                class="bi bi-arrow-right
                                                       ms-1"
                                            ></i>

                                        </a>

                                    </div>


                                </div>


                            </article>


                        </div>


                    <?php endforeach; ?>


                </div>


            <?php else: ?>


                <!-- =================================================
                     BELUM ADA BERITA
                ================================================= -->

                <div
                    class="berita-empty
                           shadow-sm
                           text-center"
                >


                    <i
                        class="bi bi-newspaper
                               fs-1
                               text-muted"
                    ></i>


                    <h3
                        class="fw-bold
                               mt-3"
                    >

                        Belum Ada Berita

                    </h3>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        Saat ini belum ada berita yang
                        dipublikasikan oleh Desa Cisalada.

                    </p>


                </div>


            <?php endif; ?>


        </div>

    </section>


<?php endif; ?>



<?php

// =========================================================
// FOOTER PUBLIC
// =========================================================

include "../includes/footer.php";

?>