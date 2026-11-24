<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


$id =
    isset($_GET["id"])
    ?
    (int) $_GET["id"]
    :
    0;


if (
    $id <= 0
) {

    header(
        "Location: umkm.php"
    );

    exit;

}


// ======================================================
// AMBIL DATA UMKM
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            owner,
            category,
            description,
            photo,
            phone,
            address,
            maps_url
        FROM umkm
        WHERE id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: umkm.php"
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


$umkm =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$umkm
) {

    header(
        "Location: umkm.php"
    );

    exit;

}


// ======================================================
// DATA UMKM
// ======================================================

$name =
    $umkm["name"]
    ??
    "";


$owner =
    $umkm["owner"]
    ??
    "";


$category =
    $umkm["category"]
    ??
    "";


$description =
    $umkm["description"]
    ??
    "";


$photo =
    $umkm["photo"]
    ??
    "";


$phone =
    $umkm["phone"]
    ??
    "";


$address =
    $umkm["address"]
    ??
    "";


$maps_url =
    $umkm["maps_url"]
    ??
    "";


// ======================================================
// WHATSAPP
// ======================================================

$whatsapp_number = "";


if (
    !empty($phone)
) {


    $whatsapp_number =
        preg_replace(
            "/[^0-9]/",
            "",
            $phone
        );


    if (
        substr(
            $whatsapp_number,
            0,
            1
        )
        ===
        "0"
    ) {

        $whatsapp_number =
            "62"
            .
            substr(
                $whatsapp_number,
                1
            );

    }


    if (
        substr(
            $whatsapp_number,
            0,
            2
        )
        !==
        "62"
    ) {

        $whatsapp_number =
            "62"
            .
            $whatsapp_number;

    }

}


// ======================================================
// AMBIL PRODUK MILIK UMKM
// ======================================================

$product_stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            description,
            price,
            image
        FROM products
        WHERE umkm_id = ?
        ORDER BY
            created_at DESC,
            id DESC
        "
    );


$products = [];


if (
    $product_stmt
) {


    mysqli_stmt_bind_param(
        $product_stmt,
        "i",
        $id
    );


    mysqli_stmt_execute(
        $product_stmt
    );


    $product_result =
        mysqli_stmt_get_result(
            $product_stmt
        );


    while (
        $product_row =
            mysqli_fetch_assoc(
                $product_result
            )
    ) {

        $products[] =
            $product_row;

    }


    mysqli_stmt_close(
        $product_stmt
    );

}


$total_products =
    count(
        $products
    );


// ======================================================
// PAGE TITLE
// ======================================================

$page_title =
    $name
    .
    " - UMKM Desa Cisalada";

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

        <?= clean($page_title) ?>
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

        .detail-hero {

            background:
                #f8f9fa;

            padding:
                45px 0 35px;

        }



        /* ==================================================
           CARD
        ================================================== */

        .detail-card {

            border:
                0;

            border-radius:
                18px;

            overflow:
                hidden;

        }



        /* ==================================================
           DETAIL IMAGE
        ================================================== */

        .detail-image {

            width:
                100%;

            height:
                430px;

            object-fit:
                cover;

            display:
                block;

        }



        /* ==================================================
           IMAGE PLACEHOLDER
        ================================================== */

        .detail-placeholder {

            width:
                100%;

            height:
                430px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef1f5;

        }


        .detail-placeholder i {

            font-size:
                90px;

            color:
                #adb5bd;

        }



        /* ==================================================
           CATEGORY
        ================================================== */

        .category-badge {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                6px;

            padding:
                8px 12px;

            border-radius:
                8px;

        }



        /* ==================================================
           INFO ITEM
        ================================================== */

        .info-item {

            padding:
                15px 0;

            border-bottom:
                1px solid #eef0f2;

        }


        .info-item:last-child {

            border-bottom:
                none;

        }



        /* ==================================================
           INFO ICON
        ================================================== */

        .info-icon {

            width:
                42px;

            height:
                42px;

            min-width:
                42px;

            border-radius:
                10px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef4ff;

            color:
                #0d6efd;

        }



        /* ==================================================
           RATING
        ================================================== */

        .rating-box {

            padding:
                14px 16px;

            background:
                #fffaf0;

            border:
                1px solid #f3e4b8;

            border-radius:
                12px;

        }



        /* ==================================================
           ACTION BUTTON
        ================================================== */

        .action-button {

            min-height:
                46px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                8px;

            border-radius:
                10px;

        }



        /* ==================================================
           WHATSAPP
        ================================================== */

        .whatsapp-button {

            background:
                #198754;

            border:
                1px solid #198754;

            color:
                #ffffff;

        }


        .whatsapp-button:hover {

            background:
                #157347;

            border-color:
                #157347;

            color:
                #ffffff;

        }



        /* ==================================================
           GOOGLE MAPS
        ================================================== */

        .maps-button {

            background:
                #ffffff;

            border:
                1px solid #dc3545;

            color:
                #dc3545;

        }


        .maps-button:hover {

            background:
                #dc3545;

            border-color:
                #dc3545;

            color:
                #ffffff;

        }



        /* ==================================================
           PRODUCT SECTION
        ================================================== */

        .product-section-card {

            border:
                0;

            border-radius:
                18px;

        }



        .product-mini-card {

            height:
                100%;

            border:
                1px solid #e9ecef;

            border-radius:
                14px;

            overflow:
                hidden;

            background:
                #ffffff;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .product-mini-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 8px 20px
                rgba(
                    0,
                    0,
                    0,
                    0.08
                );

        }



        .product-mini-image {

            width:
                100%;

            height:
                170px;

            object-fit:
                cover;

        }



        .product-mini-placeholder {

            width:
                100%;

            height:
                170px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef1f5;

        }


        .product-mini-placeholder i {

            font-size:
                50px;

            color:
                #adb5bd;

        }



        .product-price {

            color:
                #198754;

            font-weight:
                700;

        }



        .product-link {

            text-decoration:
                none;

            color:
                inherit;

        }



        /* ==================================================
           PRODUCT BUTTON
        ================================================== */

        .product-button {

            background:
                #ffffff;

            border:
                1px solid #198754;

            color:
                #198754;

        }


        .product-button:hover {

            background:
                #198754;

            border-color:
                #198754;

            color:
                #ffffff;

        }



        /* ==================================================
           BACK
        ================================================== */

        .back-button {

            background:
                #ffffff;

            border:
                1px solid #dee2e6;

            color:
                #495057;

        }


        .back-button:hover {

            background:
                #f1f3f5;

            color:
                #212529;

        }



        /* ==================================================
           MOBILE
        ================================================== */

        @media (
            max-width: 767.98px
        ) {


            .detail-image {

                height:
                    280px;

            }


            .detail-placeholder {

                height:
                    280px;

            }


            .product-mini-image {

                height:
                    190px;

            }


            .product-mini-placeholder {

                height:
                    190px;

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

                <a
                    href="umkm.php"
                    class="text-decoration-none"
                >

                    UMKM

                </a>

            </li>


            <li
                class="breadcrumb-item
                       active"
                aria-current="page"
            >

                <?= clean($name) ?>

            </li>


        </ol>


    </nav>


</div>



<!-- ======================================================
     HERO
====================================================== -->

<section
    class="detail-hero"
>


    <div
        class="container"
    >


        <div
            class="mb-2"
        >

            <span
                class="text-primary
                       fw-semibold"
            >

                DETAIL UMKM

            </span>


        </div>


        <h1
            class="fw-bold
                   mb-2"
        >

            <?= clean($name) ?>

        </h1>


        <?php if (
            !empty($category)
        ): ?>


            <span
                class="badge
                       bg-primary
                       category-badge"
            >

                <i
                    class="bi bi-tag-fill"
                ></i>


                <?= clean($category) ?>


            </span>


        <?php endif; ?>


    </div>


</section>



<!-- ======================================================
     CONTENT
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <!-- ==================================================
             UMKM DETAIL
        =================================================== -->

        <div
            class="row
                   g-4"
        >


            <!-- LEFT -->

            <div
                class="col-lg-7"
            >


                <div
                    class="card
                           detail-card
                           shadow-sm
                           mb-4"
                >


                    <!-- FOTO -->

                    <?php if (
                        !empty($photo)
                    ): ?>


                        <img
                            src="<?= BASE_URL ?>assets/uploads/umkm/<?= rawurlencode(basename($photo)) ?>"
                            alt="<?= clean($name) ?>"
                            class="detail-image"
                        >


                    <?php else: ?>


                        <div
                            class="detail-placeholder"
                        >

                            <i
                                class="bi bi-shop"
                            ></i>

                        </div>


                    <?php endif; ?>



                    <div
                        class="card-body
                               p-4
                               p-lg-5"
                    >


                        <h2
                            class="fw-bold
                                   mb-2"
                        >

                            <?= clean($name) ?>

                        </h2>



                        <?php if (
                            !empty($owner)
                        ): ?>


                            <div
                                class="text-muted
                                       mb-4"
                            >

                                <i
                                    class="bi bi-person
                                           me-1"
                                ></i>


                                <?= clean($owner) ?>


                            </div>


                        <?php endif; ?>



                        <h5
                            class="fw-bold
                                   mb-3"
                        >

                            Tentang UMKM

                        </h5>



                        <?php if (
                            !empty($description)
                        ): ?>


                            <div
                                class="text-muted
                                       lh-lg"
                                style="
                                    white-space: pre-line;
                                "
                            >

                                <?= clean($description) ?>

                            </div>


                        <?php else: ?>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Belum ada deskripsi
                                yang tersedia.

                            </p>


                        <?php endif; ?>


                    </div>


                </div>



                <!-- ==================================================
                     PRODUK UMKM
                =================================================== -->

                <div
                    class="card
                           product-section-card
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
                                   gap-2
                                   mb-4"
                        >


                            <div>


                                <h4
                                    class="fw-bold
                                           mb-1"
                                >

                                    Produk UMKM

                                </h4>


                                <p
                                    class="text-muted
                                           small
                                           mb-0"
                                >

                                    Produk yang tersedia
                                    dari <?= clean($name) ?>.

                                </p>


                            </div>


                            <span
                                class="badge
                                       bg-success"
                            >

                                <?= $total_products ?>

                                Produk

                            </span>


                        </div>



                        <?php if (
                            $total_products > 0
                        ): ?>


                            <div
                                class="row
                                       g-3"
                            >


                                <?php foreach (
                                    $products
                                    as $product
                                ): ?>


                                    <div
                                        class="col-md-6"
                                    >


                                        <a
                                            href="produk-detail.php?id=<?= (int) $product["id"] ?>"
                                            class="product-link"
                                        >


                                            <div
                                                class="product-mini-card"
                                            >


                                                <!-- FOTO -->

                                                <?php if (
                                                    !empty(
                                                        $product["image"]
                                                    )
                                                ): ?>


                                                    <img
                                                        src="<?= BASE_URL ?>assets/uploads/products/<?= rawurlencode(basename($product["image"])) ?>"
                                                        alt="<?= clean($product["name"]) ?>"
                                                        class="product-mini-image"
                                                    >


                                                <?php else: ?>


                                                    <div
                                                        class="product-mini-placeholder"
                                                    >

                                                        <i
                                                            class="bi bi-box-seam"
                                                        ></i>

                                                    </div>


                                                <?php endif; ?>



                                                <!-- BODY -->

                                                <div
                                                    class="p-3"
                                                >


                                                    <h5
                                                        class="fw-bold
                                                               mb-2"
                                                    >

                                                        <?= clean(
                                                            $product["name"]
                                                        ) ?>


                                                    </h5>



                                                    <?php if (
                                                        (float)
                                                        $product["price"]
                                                        >
                                                        0
                                                    ): ?>


                                                        <div
                                                            class="product-price
                                                                   mb-2"
                                                        >

                                                            Rp
                                                            <?= number_format(
                                                                (float)
                                                                $product["price"],
                                                                0,
                                                                ",",
                                                                "."
                                                            ) ?>


                                                        </div>


                                                    <?php endif; ?>



                                                    <?php if (
                                                        !empty(
                                                            $product["description"]
                                                        )
                                                    ): ?>


                                                        <p
                                                            class="small
                                                                   text-muted
                                                                   mb-0"
                                                        >

                                                            <?= clean(
                                                                mb_strimwidth(
                                                                    $product["description"],
                                                                    0,
                                                                    85,
                                                                    "..."
                                                                )
                                                            ) ?>


                                                        </p>


                                                    <?php endif; ?>


                                                </div>


                                            </div>


                                        </a>


                                    </div>


                                <?php endforeach; ?>


                            </div>



                            <div
                                class="mt-4"
                            >


                                <a
                                    href="produk.php?umkm_id=<?= $id ?>"
                                    class="btn
                                           product-button
                                           action-button
                                           w-100"
                                >

                                    <i
                                        class="bi bi-box-seam"
                                    ></i>


                                    Lihat Semua Produk
                                    UMKM Ini

                                </a>


                            </div>


                        <?php else: ?>


                            <div
                                class="text-center
                                       py-4"
                            >


                                <i
                                    class="bi bi-box-seam
                                           fs-1
                                           text-muted"
                                ></i>


                                <h5
                                    class="fw-bold
                                           mt-3"
                                >

                                    Belum Ada Produk

                                </h5>


                                <p
                                    class="text-muted
                                           mb-0"
                                >

                                    UMKM ini belum memiliki
                                    produk yang ditampilkan.

                                </p>


                            </div>


                        <?php endif; ?>


                    </div>


                </div>


            </div>



            <!-- ==================================================
                 RIGHT
            =================================================== -->

            <div
                class="col-lg-5"
            >


                <div
                    class="card
                           detail-card
                           shadow-sm"
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <h4
                            class="fw-bold
                                   mb-3"
                        >

                            Informasi UMKM

                        </h4>



                        <!-- PEMILIK -->

                        <?php if (
                            !empty($owner)
                        ): ?>


                            <div
                                class="info-item
                                       d-flex
                                       gap-3"
                            >


                                <div
                                    class="info-icon"
                                >

                                    <i
                                        class="bi bi-person"
                                    ></i>

                                </div>


                                <div>


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Pemilik

                                    </div>


                                    <div
                                        class="fw-semibold"
                                    >

                                        <?= clean($owner) ?>

                                    </div>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- NOMOR TELEPON -->

                        <?php if (
                            !empty($phone)
                        ): ?>


                            <div
                                class="info-item
                                       d-flex
                                       gap-3"
                            >


                                <div
                                    class="info-icon"
                                >

                                    <i
                                        class="bi bi-telephone"
                                    ></i>

                                </div>


                                <div
                                    class="flex-grow-1"
                                >


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Nomor Telepon

                                    </div>


                                    <a
                                        href="tel:<?= clean($phone) ?>"
                                        class="fw-semibold
                                               text-decoration-none"
                                    >

                                        <?= clean($phone) ?>

                                    </a>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- ALAMAT -->

                        <?php if (
                            !empty($address)
                        ): ?>


                            <div
                                class="info-item
                                       d-flex
                                       gap-3"
                            >


                                <div
                                    class="info-icon"
                                >

                                    <i
                                        class="bi bi-geo-alt"
                                    ></i>

                                </div>


                                <div
                                    class="flex-grow-1"
                                >


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Alamat

                                    </div>


                                    <div
                                        class="fw-semibold"
                                    >

                                        <?= clean($address) ?>

                                    </div>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- RATING -->

                        <div
                            class="info-item"
                        >


                            <div
                                class="rating-box"
                            >


                                <div
                                    class="d-flex
                                           align-items-start
                                           gap-3"
                                >


                                    <i
                                        class="bi bi-star-fill
                                               text-warning
                                               fs-4"
                                    ></i>


                                    <div>


                                        <div
                                            class="fw-semibold"
                                        >

                                            Rating & Ulasan

                                        </div>


                                        <?php if (
                                            !empty($maps_url)
                                        ): ?>


                                            <div
                                                class="small
                                                       text-muted
                                                       mt-1"
                                            >

                                                Lihat rating dan
                                                ulasan terbaru
                                                langsung di
                                                Google Maps.

                                            </div>


                                        <?php else: ?>


                                            <div
                                                class="small
                                                       text-muted
                                                       mt-1"
                                            >

                                                Link Google Maps
                                                belum tersedia.

                                            </div>


                                        <?php endif; ?>


                                    </div>


                                </div>


                            </div>


                        </div>



                        <!-- ==========================================
                             WHATSAPP
                        =========================================== -->

                        <?php if (
                            !empty($whatsapp_number)
                        ): ?>


                            <div
                                class="pt-4
                                       mb-2"
                            >


                                <a
                                    href="https://wa.me/<?= clean($whatsapp_number) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn
                                           whatsapp-button
                                           action-button
                                           w-100"
                                >

                                    <i
                                        class="bi bi-whatsapp
                                               fs-5"
                                    ></i>


                                    Chat via WhatsApp

                                </a>


                            </div>


                        <?php endif; ?>



                        <!-- ==========================================
                             GOOGLE MAPS
                        =========================================== -->

                        <?php if (
                            !empty($maps_url)
                        ): ?>


                            <a
                                href="<?= clean($maps_url) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn
                                       maps-button
                                       action-button
                                       w-100
                                       mb-2"
                            >

                                <i
                                    class="bi bi-google
                                           fs-5"
                                ></i>


                                Buka Lokasi di Google Maps

                            </a>


                        <?php endif; ?>



                        <!-- ==========================================
                             PRODUCT BUTTON
                        =========================================== -->

                        <?php if (
                            $total_products > 0
                        ): ?>


                            <a
                                href="produk.php?umkm_id=<?= $id ?>"
                                class="btn
                                       product-button
                                       action-button
                                       w-100
                                       mb-2"
                            >

                                <i
                                    class="bi bi-box-seam
                                           fs-5"
                                ></i>


                                Lihat Produk UMKM Ini

                            </a>


                        <?php endif; ?>



                        <!-- ==========================================
                             BACK
                        =========================================== -->

                        <a
                            href="umkm.php"
                            class="btn
                                   back-button
                                   action-button
                                   w-100"
                        >

                            <i
                                class="bi bi-arrow-left"
                            ></i>


                            Kembali ke UMKM

                        </a>


                    </div>


                </div>


            </div>


        </div>


    </div>


</section>



<!-- ======================================================
     FOOTER
====================================================== -->

<?php

include "../includes/footer.php";

?>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>