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
        "Location: produk.php"
    );

    exit;

}


// ======================================================
// AMBIL DATA PRODUK
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            products.id,
            products.umkm_id,
            products.name,
            products.description,
            products.price,
            products.image,
            products.created_at,

            umkm.name AS umkm_name,

            umkm.owner AS umkm_owner,

            umkm.phone AS umkm_phone,

            umkm.maps_url AS umkm_maps_url

        FROM products

        INNER JOIN umkm
            ON products.umkm_id =
               umkm.id

        WHERE products.id = ?

        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: produk.php"
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


$product =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$product
) {

    header(
        "Location: produk.php"
    );

    exit;

}


// ======================================================
// DATA PRODUK
// ======================================================

$product_name =
    $product["name"]
    ??
    "";


$product_description =
    $product["description"]
    ??
    "";


$product_price =
    $product["price"]
    ??
    0;


$product_image =
    $product["image"]
    ??
    "";


$umkm_id =
    (int)
    (
        $product["umkm_id"]
        ??
        0
    );


$umkm_name =
    $product["umkm_name"]
    ??
    "";


$umkm_owner =
    $product["umkm_owner"]
    ??
    "";


$umkm_phone =
    $product["umkm_phone"]
    ??
    "";


$umkm_maps_url =
    $product["umkm_maps_url"]
    ??
    "";


// ======================================================
// WHATSAPP UMKM
// ======================================================

$whatsapp_number = "";


if (
    !empty($umkm_phone)
) {


    $whatsapp_number =
        preg_replace(
            "/[^0-9]/",
            "",
            $umkm_phone
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
// PAGE TITLE
// ======================================================

$page_title =
    $product_name
    .
    " - Produk Desa Cisalada";

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
                none;

            border-radius:
                18px;

            overflow:
                hidden;

        }



        /* ==================================================
           PRODUCT IMAGE
        ================================================== */

        .product-detail-image {

            width:
                100%;

            height:
                460px;

            object-fit:
                cover;

            display:
                block;

        }



        /* ==================================================
           PLACEHOLDER
        ================================================== */

        .product-detail-placeholder {

            width:
                100%;

            height:
                460px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #eef1f5;

        }


        .product-detail-placeholder i {

            font-size:
                100px;

            color:
                #adb5bd;

        }



        /* ==================================================
           CATEGORY LABEL
        ================================================== */

        .product-label {

            color:
                #0d6efd;

            font-size:
                0.85rem;

            font-weight:
                700;

            letter-spacing:
                0.04em;

        }



        /* ==================================================
           PRICE
        ================================================== */

        .product-detail-price {

            color:
                #198754;

            font-size:
                1.5rem;

            font-weight:
                700;

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
           UMKM CARD
        ================================================== */

        .umkm-box {

            background:
                #f8f9fa;

            border:
                1px solid #e9ecef;

            border-radius:
                14px;

            padding:
                16px;

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
           UMKM BUTTON
        ================================================== */

        .umkm-button {

            background:
                #ffffff;

            border:
                1px solid #0d6efd;

            color:
                #0d6efd;

        }


        .umkm-button:hover {

            background:
                #0d6efd;

            border-color:
                #0d6efd;

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


            .product-detail-image {

                height:
                    300px;

            }


            .product-detail-placeholder {

                height:
                    300px;

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
                    href="produk.php"
                    class="text-decoration-none"
                >

                    Produk

                </a>

            </li>


            <li
                class="breadcrumb-item
                       active"
                aria-current="page"
            >

                <?= clean($product_name) ?>

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
            class="product-label
                   mb-2"
        >

            DETAIL PRODUK

        </div>


        <h1
            class="fw-bold
                   mb-2"
        >

            <?= clean($product_name) ?>

        </h1>


        <div
            class="text-muted"
        >

            Produk dari
            <strong>

                <?= clean($umkm_name) ?>

            </strong>

        </div>


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


        <div
            class="row
                   g-4"
        >


            <!-- ==================================================
                 LEFT
            =================================================== -->

            <div
                class="col-lg-7"
            >


                <div
                    class="card
                           detail-card
                           shadow-sm"
                >


                    <!-- FOTO -->

                    <?php if (
                        !empty(
                            $product_image
                        )
                    ): ?>


                        <img
                            src="<?= BASE_URL ?>assets/uploads/products/<?= rawurlencode(basename($product_image)) ?>"
                            alt="<?= clean($product_name) ?>"
                            class="product-detail-image"
                        >


                    <?php else: ?>


                        <div
                            class="product-detail-placeholder"
                        >

                            <i
                                class="bi bi-box-seam"
                            ></i>

                        </div>


                    <?php endif; ?>



                    <!-- BODY -->

                    <div
                        class="card-body
                               p-4
                               p-lg-5"
                    >


                        <h2
                            class="fw-bold
                                   mb-3"
                        >

                            <?= clean($product_name) ?>

                        </h2>



                        <!-- HARGA -->

                        <?php if (
                            (float)
                            $product_price
                            >
                            0
                        ): ?>


                            <div
                                class="product-detail-price
                                       mb-4"
                            >

                                Rp
                                <?= number_format(
                                    (float)
                                    $product_price,
                                    0,
                                    ",",
                                    "."
                                ) ?>


                            </div>


                        <?php endif; ?>



                        <!-- DESKRIPSI -->

                        <h5
                            class="fw-bold
                                   mb-3"
                        >

                            Tentang Produk

                        </h5>


                        <?php if (
                            !empty(
                                $product_description
                            )
                        ): ?>


                            <div
                                class="text-muted
                                       lh-lg"
                                style="
                                    white-space: pre-line;
                                "
                            >

                                <?= clean(
                                    $product_description
                                ) ?>


                            </div>


                        <?php else: ?>


                            <p
                                class="text-muted
                                       mb-0"
                            >

                                Belum ada deskripsi
                                produk yang tersedia.

                            </p>


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

                            Informasi Produk

                        </h4>



                        <!-- ==========================================
                             UMKM
                        =========================================== -->

                        <div
                            class="info-item
                                   d-flex
                                   gap-3"
                        >


                            <div
                                class="info-icon"
                            >

                                <i
                                    class="bi bi-shop"
                                ></i>

                            </div>


                            <div
                                class="flex-grow-1"
                            >


                                <div
                                    class="small
                                           text-muted"
                                >

                                    UMKM

                                </div>


                                <div
                                    class="fw-semibold"
                                >

                                    <?= clean($umkm_name) ?>

                                </div>


                                <?php if (
                                    !empty(
                                        $umkm_owner
                                    )
                                ): ?>


                                    <div
                                        class="small
                                               text-muted
                                               mt-1"
                                    >

                                        Pemilik:
                                        <?= clean(
                                            $umkm_owner
                                        ) ?>


                                    </div>


                                <?php endif; ?>


                            </div>


                        </div>



                        <!-- ==========================================
                             HARGA
                        =========================================== -->

                        <div
                            class="info-item
                                   d-flex
                                   gap-3"
                        >


                            <div
                                class="info-icon"
                            >

                                <i
                                    class="bi bi-tag"
                                ></i>

                            </div>


                            <div>


                                <div
                                    class="small
                                           text-muted"
                                >

                                    Harga

                                </div>


                                <div
                                    class="fw-semibold
                                           text-success"
                                >


                                    <?php if (
                                        (float)
                                        $product_price
                                        >
                                        0
                                    ): ?>


                                        Rp
                                        <?= number_format(
                                            (float)
                                            $product_price,
                                            0,
                                            ",",
                                            "."
                                        ) ?>


                                    <?php else: ?>


                                        Tidak dicantumkan


                                    <?php endif; ?>


                                </div>


                            </div>


                        </div>



                        <!-- ==========================================
                             KONTAK UMKM
                        =========================================== -->

                        <?php if (
                            !empty(
                                $umkm_phone
                            )
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


                                <div>


                                    <div
                                        class="small
                                               text-muted"
                                    >

                                        Kontak UMKM

                                    </div>


                                    <a
                                        href="tel:<?= clean($umkm_phone) ?>"
                                        class="fw-semibold
                                               text-decoration-none"
                                    >

                                        <?= clean($umkm_phone) ?>

                                    </a>


                                </div>


                            </div>


                        <?php endif; ?>



                        <!-- ==========================================
                             ACTION
                        =========================================== -->

                        <div
                            class="pt-4"
                        >


                            <!-- WHATSAPP -->

                            <?php if (
                                !empty(
                                    $whatsapp_number
                                )
                            ): ?>


                                <a
                                    href="https://wa.me/<?= clean($whatsapp_number) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn
                                           whatsapp-button
                                           action-button
                                           w-100
                                           mb-2"
                                >

                                    <i
                                        class="bi bi-whatsapp
                                               fs-5"
                                    ></i>


                                    Chat via WhatsApp

                                </a>


                            <?php endif; ?>



                            <!-- GOOGLE MAPS -->

                            <?php if (
                                !empty(
                                    $umkm_maps_url
                                )
                            ): ?>


                                <a
                                    href="<?= clean($umkm_maps_url) ?>"
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



                            <!-- LIHAT UMKM -->

                            <a
                                href="umkm-detail.php?id=<?= $umkm_id ?>"
                                class="btn
                                       umkm-button
                                       action-button
                                       w-100
                                       mb-2"
                            >

                                <i
                                    class="bi bi-shop
                                           fs-5"
                                ></i>


                                Lihat Profil UMKM

                            </a>



                            <!-- KEMBALI -->

                            <a
                                href="produk.php"
                                class="btn
                                       back-button
                                       action-button
                                       w-100"
                            >

                                <i
                                    class="bi bi-arrow-left"
                                ></i>


                                Kembali ke Produk

                            </a>


                        </div>


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