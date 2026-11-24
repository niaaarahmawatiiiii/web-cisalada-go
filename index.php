<?php

require_once "config/database.php";
require_once "includes/functions.php";


$page_title =
    "Beranda";


// =====================================================
// DATA DESA
// =====================================================

$village =
    null;


$village_result =
    mysqli_query(
        $conn,
        "
        SELECT
            village_name,
            district,
            regency,
            province,
            area,
            classification,
            population,
            family_count,
            rt_count,
            rw_count
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

}


// =====================================================
// HITUNG RT/RW DARI DATA WARGA
// Hanya digunakan jika data village_data masih 0
// =====================================================

$calculated_rt =
    0;


$calculated_rw =
    0;


$resident_area_result =
    mysqli_query(
        $conn,
        "
        SELECT
            COUNT(
                DISTINCT
                NULLIF(
                    TRIM(rt),
                    ''
                )
            ) AS total_rt,

            COUNT(
                DISTINCT
                NULLIF(
                    TRIM(rw),
                    ''
                )
            ) AS total_rw

        FROM residents

        WHERE
            population_status = 'Aktif'
        "
    );


if (
    $resident_area_result
) {

    $resident_area =
        mysqli_fetch_assoc(
            $resident_area_result
        );


    $calculated_rt =
        (int)(
            $resident_area[
                "total_rt"
            ]
            ??
            0
        );


    $calculated_rw =
        (int)(
            $resident_area[
                "total_rw"
            ]
            ??
            0
        );

}


// =====================================================
// BERITA TERBARU
// =====================================================

$latest_news =
    [];


$news_result =
    mysqli_query(
        $conn,
        "
        SELECT
            news.id,
            news.title,
            news.content,
            news.image,
            news.author,
            news.published_at,
            news.created_at,
            news_categories.name AS category_name

        FROM news

        LEFT JOIN news_categories
            ON news.category_id =
               news_categories.id

        ORDER BY
            COALESCE(
                news.published_at,
                news.created_at
            ) DESC,

            news.id DESC

        LIMIT 3
        "
    );


if (
    $news_result
) {

    while (
        $row =
            mysqli_fetch_assoc(
                $news_result
            )
    ) {

        $latest_news[] =
            $row;

    }

}


// =====================================================
// FUNGSI FORMAT
// =====================================================

function home_format_date(
    $date
) {

    if (
        empty(
            $date
        )
    ) {

        return
            "-";

    }


    $timestamp =
        strtotime(
            $date
        );


    if (
        !$timestamp
    ) {

        return
            "-";

    }


    return
        date(
            "d M Y",
            $timestamp
        );

}


// =====================================================
// FORMAT STATISTIK
// =====================================================

function home_stat_value(
    $value
) {

    $value =
        (int)
        $value;


    if (
        $value <= 0
    ) {

        return
            "Belum tersedia";

    }


    return
        number_format(
            $value,
            0,
            ",",
            "."
        );

}


// =====================================================
// SIAPKAN NILAI RT / RW
// =====================================================

$rt_value =
    (int)(
        $village[
            "rt_count"
        ]
        ??
        0
    );


$rw_value =
    (int)(
        $village[
            "rw_count"
        ]
        ??
        0
    );


if (
    $rt_value <= 0
    &&
    $calculated_rt > 0
) {

    $rt_value =
        $calculated_rt;

}


if (
    $rw_value <= 0
    &&
    $calculated_rw > 0
) {

    $rw_value =
        $calculated_rw;

}


?>

<?php

include
    "includes/header.php";

?>

<?php

include
    "includes/navbar.php";

?>



<!-- =====================================================
     HERO
===================================================== -->

<section
    class="hero"
>

    <div
        class="container"
    >

        <div
            class="row"
        >

            <div
                class="col-lg-7
                       hero-content"
            >


                <p
                    class="text-uppercase
                           fw-semibold"
                >

                    Website Informasi Desa

                </p>


                <h1
                    class="display-4
                           fw-bold"
                >

                    Selamat Datang di

                    <br>

                    <?= clean(
                        $village[
                            "village_name"
                        ]
                        ??
                        "Desa Cisalada"
                    ) ?>


                </h1>


                <p
                    class="lead"
                >

                    Temukan informasi desa,
                    potensi lokal, layanan,
                    berita, dan berbagai informasi
                    mengenai Desa Cisalada.

                </p>


                <a
                    href="pages/profil.php"
                    class="btn
                           btn-primary
                           btn-lg"
                >

                    Kenali Desa Kami

                </a>


            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     QUICK ACCESS
===================================================== -->

<section
    class="section-padding"
>

    <div
        class="container"
    >


        <div
            class="text-center
                   mb-5"
        >

            <h2
                class="section-title"
            >

                Akses Cepat

            </h2>


            <p
                class="section-subtitle"
            >

                Informasi yang sering dibutuhkan masyarakat

            </p>

        </div>



        <div
            class="row
                   g-4"
        >


            <!-- DATA DESA -->

            <div
                class="col-md-3"
            >

                <a
                    href="pages/data-desa.php"
                    class="text-decoration-none
                           text-dark"
                >

                    <div
                        class="card
                               quick-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body
                                   text-center
                                   p-4"
                        >

                            <i
                                class="bi
                                       bi-bar-chart-fill
                                       fs-1
                                       text-primary"
                            ></i>


                            <h5
                                class="mt-3"
                            >

                                Data Desa

                            </h5>


                            <p
                                class="text-muted"
                            >

                                Statistik dan data
                                agregat desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- LAYANAN -->

            <div
                class="col-md-3"
            >

                <a
                    href="pages/layanan.php"
                    class="text-decoration-none
                           text-dark"
                >

                    <div
                        class="card
                               quick-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body
                                   text-center
                                   p-4"
                        >

                            <i
                                class="bi
                                       bi-file-earmark-text
                                       fs-1
                                       text-primary"
                            ></i>


                            <h5
                                class="mt-3"
                            >

                                Layanan

                            </h5>


                            <p
                                class="text-muted"
                            >

                                Informasi persyaratan
                                pelayanan desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- UMKM -->

            <div
                class="col-md-3"
            >

                <a
                    href="pages/umkm.php"
                    class="text-decoration-none
                           text-dark"
                >

                    <div
                        class="card
                               quick-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body
                                   text-center
                                   p-4"
                        >

                            <i
                                class="bi
                                       bi-shop
                                       fs-1
                                       text-primary"
                            ></i>


                            <h5
                                class="mt-3"
                            >

                                UMKM

                            </h5>


                            <p
                                class="text-muted"
                            >

                                Kenali produk lokal
                                Desa Cisalada.

                            </p>


                        </div>

                    </div>

                </a>

            </div>



            <!-- KONTAK -->

            <div
                class="col-md-3"
            >

                <a
                    href="pages/kontak.php"
                    class="text-decoration-none
                           text-dark"
                >

                    <div
                        class="card
                               quick-card
                               shadow-sm
                               h-100"
                    >

                        <div
                            class="card-body
                                   text-center
                                   p-4"
                        >

                            <i
                                class="bi
                                       bi-geo-alt-fill
                                       fs-1
                                       text-primary"
                            ></i>


                            <h5
                                class="mt-3"
                            >

                                Kontak

                            </h5>


                            <p
                                class="text-muted"
                            >

                                Hubungi dan temukan
                                lokasi desa.

                            </p>


                        </div>

                    </div>

                </a>

            </div>


        </div>

    </div>

</section>



<!-- =====================================================
     STATISTIK
===================================================== -->

<section
    class="section-padding
           bg-light"
>

    <div
        class="container"
    >


        <div
            class="text-center
                   mb-5"
        >

            <h2
                class="section-title"
            >

                Statistik Desa

            </h2>


            <p
                class="section-subtitle"
            >

                Data agregat Desa Cisalada

            </p>

        </div>



        <div
            class="row
                   g-4"
        >


            <!-- PENDUDUK -->

            <div
                class="col-md-3"
            >

                <div
                    class="card
                           stat-card
                           shadow-sm"
                >

                    <div
                        class="card-body
                               text-center
                               p-4"
                    >

                        <i
                            class="bi
                                   bi-people-fill
                                   fs-1
                                   text-primary"
                        ></i>


                        <h3
                            class="fw-bold
                                   mt-3"
                        >

                            <?= home_stat_value(
                                $village[
                                    "population"
                                ]
                                ??
                                0
                            ) ?>


                        </h3>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Penduduk

                        </p>


                    </div>

                </div>

            </div>



            <!-- KK -->

            <div
                class="col-md-3"
            >

                <div
                    class="card
                           stat-card
                           shadow-sm"
                >

                    <div
                        class="card-body
                               text-center
                               p-4"
                    >

                        <i
                            class="bi
                                   bi-house-fill
                                   fs-1
                                   text-primary"
                        ></i>


                        <h3
                            class="fw-bold
                                   mt-3"
                        >

                            <?= home_stat_value(
                                $village[
                                    "family_count"
                                ]
                                ??
                                0
                            ) ?>


                        </h3>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            Kepala Keluarga

                        </p>


                    </div>

                </div>

            </div>



            <!-- RT -->

            <div
                class="col-md-3"
            >

                <div
                    class="card
                           stat-card
                           shadow-sm"
                >

                    <div
                        class="card-body
                               text-center
                               p-4"
                    >

                        <i
                            class="bi
                                   bi-diagram-3-fill
                                   fs-1
                                   text-primary"
                        ></i>


                        <h3
                            class="fw-bold
                                   mt-3"
                        >

                            <?= home_stat_value(
                                $rt_value
                            ) ?>


                        </h3>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            RT

                        </p>


                    </div>

                </div>

            </div>



            <!-- RW -->

            <div
                class="col-md-3"
            >

                <div
                    class="card
                           stat-card
                           shadow-sm"
                >

                    <div
                        class="card-body
                               text-center
                               p-4"
                    >

                        <i
                            class="bi
                                   bi-diagram-2-fill
                                   fs-1
                                   text-primary"
                        ></i>


                        <h3
                            class="fw-bold
                                   mt-3"
                        >

                            <?= home_stat_value(
                                $rw_value
                            ) ?>


                        </h3>


                        <p
                            class="text-muted
                                   mb-0"
                        >

                            RW

                        </p>


                    </div>

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =====================================================
     TENTANG DESA
===================================================== -->

<section
    class="section-padding"
>

    <div
        class="container"
    >

        <div
            class="row
                   align-items-center"
        >


            <div
                class="col-lg-6"
            >


                <p
                    class="text-primary
                           fw-semibold"
                >

                    TENTANG CISALADA

                </p>


                <h2
                    class="fw-bold
                           mb-4"
                >

                    Mengenal Desa Cisalada

                </h2>


                <p>

                    <?php if (
                        $village
                    ): ?>


                        Desa Cisalada merupakan desa
                        yang berada di Kecamatan

                        <strong>
                            <?= clean(
                                $village[
                                    "district"
                                ]
                            ) ?>
                        </strong>,

                        Kabupaten

                        <strong>
                            <?= clean(
                                $village[
                                    "regency"
                                ]
                            ) ?>
                        </strong>,

                        Provinsi

                        <strong>
                            <?= clean(
                                $village[
                                    "province"
                                ]
                            ) ?>
                        </strong>.


                        Luas wilayah yang tercatat
                        sebesar

                        <strong>
                            <?= clean(
                                $village[
                                    "area"
                                ]
                            ) ?>
                        </strong>.


                    <?php else: ?>


                        Informasi Desa Cisalada
                        belum tersedia.

                    <?php endif; ?>


                </p>


                <p>

                    Informasi mengenai desa akan terus
                    diperbarui berdasarkan data resmi
                    yang dikelola Pemerintah Desa Cisalada.

                </p>


                <a
                    href="pages/profil.php"
                    class="btn
                           btn-outline-primary"
                >

                    Selengkapnya

                </a>


            </div>



            <div
                class="col-lg-6
                       mt-4
                       mt-lg-0"
            >


                <div
                    class="bg-light
                           rounded
                           p-5
                           text-center"
                >


                    <i
                        class="bi
                               bi-building
                               fs-1
                               text-primary"
                    ></i>


                    <h4
                        class="mt-3"
                    >

                        <?= clean(
                            $village[
                                "village_name"
                            ]
                            ??
                            "Desa Cisalada"
                        ) ?>


                    </h4>


                    <p
                        class="text-muted
                               mb-0"
                    >

                        <?= clean(
                            $village[
                                "classification"
                            ]
                            ??
                            "Informasi Desa Cisalada"
                        ) ?>


                    </p>


                </div>


            </div>


        </div>

    </div>

</section>



<!-- =====================================================
     BERITA TERBARU
===================================================== -->

<section
    class="section-padding
           bg-light"
>

    <div
        class="container"
    >


        <div
            class="d-flex
                   justify-content-between
                   align-items-center
                   mb-4"
        >


            <div>

                <h2
                    class="section-title"
                >

                    Berita Terbaru

                </h2>


                <p
                    class="section-subtitle
                           mb-0"
                >

                    Informasi terbaru
                    dari Desa Cisalada

                </p>

            </div>


            <a
                href="pages/berita.php"
                class="btn
                       btn-outline-primary"
            >

                Lihat Semua

            </a>


        </div>



        <div
            class="row
                   g-4"
        >


            <?php if (
                count(
                    $latest_news
                )
                >
                0
            ): ?>


                <?php foreach (
                    $latest_news
                    as $news
                ): ?>


                    <div
                        class="col-md-4"
                    >


                        <div
                            class="card
                                   info-card
                                   h-100"
                        >


                            <?php if (
                                !empty(
                                    $news[
                                        "image"
                                    ]
                                )
                            ): ?>


                                <img
                                    src="<?= BASE_URL ?>assets/uploads/berita/<?= clean(
                                        $news["image"]
                                    ) ?>"
                                    class="card-img-top"
                                    alt="Gambar berita"
                                    style="
                                        height:190px;
                                        object-fit:cover;
                                    "
                                >


                            <?php endif; ?>



                            <div
                                class="card-body"
                            >


                                <div
                                    class="d-flex
                                           justify-content-between
                                           align-items-center
                                           gap-2
                                           mb-2"
                                >


                                    <small
                                        class="text-muted"
                                    >

                                        <?= home_format_date(
                                            $news[
                                                "published_at"
                                            ]
                                            ??
                                            $news[
                                                "created_at"
                                            ]
                                        ) ?>


                                    </small>



                                    <?php if (
                                        !empty(
                                            $news[
                                                "category_name"
                                            ]
                                        )
                                    ): ?>


                                        <span
                                            class="badge
                                                   bg-primary-subtle
                                                   text-primary"
                                        >

                                            <?= clean(
                                                $news[
                                                    "category_name"
                                                ]
                                            ) ?>


                                        </span>


                                    <?php endif; ?>


                                </div>



                                <h5
                                    class="fw-bold
                                           mt-2"
                                >

                                    <?= clean(
                                        $news[
                                            "title"
                                        ]
                                    ) ?>


                                </h5>



                                <p
                                    class="text-muted"
                                >

                                    <?= clean(
                                        mb_strimwidth(
                                            strip_tags(
                                                $news[
                                                    "content"
                                                ]
                                            ),
                                            0,
                                            130,
                                            "..."
                                        )
                                    ) ?>


                                </p>



                                <a
                                    href="pages/berita.php?id=<?= (int) $news["id"] ?>"
                                    class="text-primary
                                           text-decoration-none"
                                >

                                    Baca selengkapnya
                                    →

                                </a>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <div
                    class="col-12"
                >


                    <div
                        class="card
                               info-card"
                    >


                        <div
                            class="card-body
                                   text-center
                                   py-5"
                        >


                            <i
                                class="bi
                                       bi-newspaper
                                       fs-1
                                       text-muted"
                            ></i>


                            <h5
                                class="fw-bold
                                       mt-3"
                            >

                                Belum Ada Berita

                            </h5>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Belum ada berita terbaru
                                yang dipublikasikan.

                            </p>


                        </div>


                    </div>


                </div>


            <?php endif; ?>


        </div>


    </div>

</section>



<!-- =====================================================
     CTA
===================================================== -->

<section
    class="section-padding"
>

    <div
        class="container"
    >


        <div
            class="bg-primary
                   text-white
                   rounded
                   p-5
                   text-center"
        >


            <h2
                class="fw-bold"
            >

                Butuh Informasi Desa?

            </h2>


            <p
                class="mb-4"
            >

                Temukan berbagai informasi,
                layanan, dan potensi
                Desa Cisalada.

            </p>


            <a
                href="pages/kontak.php"
                class="btn
                       btn-light"
            >

                Hubungi Desa

            </a>


        </div>


    </div>

</section>



<?php

include
    "includes/footer.php";

?>