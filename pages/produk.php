<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// ======================================================
// FILTER UMKM
// ======================================================

$umkm_id =
    isset($_GET["umkm_id"])
    ?
    (int)
    $_GET["umkm_id"]
    :
    0;


// ======================================================
// DATA UMKM UNTUK JUDUL FILTER
// ======================================================

$selected_umkm =
    null;


if (
    $umkm_id > 0
) {


    $umkm_stmt =
        mysqli_prepare(
            $conn,
            "
            SELECT
                id,
                name,
                owner
            FROM umkm
            WHERE id = ?
            LIMIT 1
            "
        );


    if (
        $umkm_stmt
    ) {


        mysqli_stmt_bind_param(
            $umkm_stmt,
            "i",
            $umkm_id
        );


        mysqli_stmt_execute(
            $umkm_stmt
        );


        $umkm_result =
            mysqli_stmt_get_result(
                $umkm_stmt
            );


        $selected_umkm =
            mysqli_fetch_assoc(
                $umkm_result
            );


        mysqli_stmt_close(
            $umkm_stmt
        );

    }

}


// ======================================================
// QUERY PRODUK
// ======================================================

if (
    $umkm_id > 0
    &&
    $selected_umkm
) {


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

                umkm.owner AS umkm_owner

            FROM products

            INNER JOIN umkm
                ON products.umkm_id =
                   umkm.id

            WHERE products.umkm_id = ?

            ORDER BY
                products.created_at DESC,
                products.id DESC
            "
        );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $umkm_id
    );


    mysqli_stmt_execute(
        $stmt
    );


    $result =
        mysqli_stmt_get_result(
            $stmt
        );


    $filter_active =
        true;


} else {


    $result =
        mysqli_query(
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

                umkm.owner AS umkm_owner

            FROM products

            INNER JOIN umkm
                ON products.umkm_id =
                   umkm.id

            ORDER BY
                products.created_at DESC,
                products.id DESC
            "
        );


    $filter_active =
        false;

}


// ======================================================
// PAGE TITLE
// ======================================================

if (
    $filter_active
) {

    $page_title =
        "Produk "
        .
        $selected_umkm["name"];

} else {

    $page_title =
        "Produk Desa";

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
                55px 0 45px;

        }



        /* ==================================================
           PRODUCT LINK
        ================================================== */

        .product-link {

            display:
                block;

            height:
                100%;

            color:
                inherit;

            text-decoration:
                none;

        }



        /* ==================================================
           PRODUCT CARD
        ================================================== */

        .product-card {

            height:
                100%;

            border:
                none;

            border-radius:
                16px;

            overflow:
                hidden;

            background:
                #ffffff;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .product-card:hover {

            transform:
                translateY(-5px);

            box-shadow:
                0 12px 30px
                rgba(
                    0,
                    0,
                    0,
                    0.09
                ) !important;

        }



        /* ==================================================
           PRODUCT IMAGE
        ================================================== */

        .product-image {

            width:
                100%;

            height:
                230px;

            object-fit:
                cover;

            display:
                block;

        }



        .product-placeholder {

            width:
                100%;

            height:
                230px;

            background:
                #eef1f5;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

        }


        .product-placeholder i {

            font-size:
                65px;

            color:
                #adb5bd;

        }



        /* ==================================================
           UMKM BADGE
        ================================================== */

        .umkm-badge {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                5px;

            font-size:
                0.78rem;

            padding:
                7px 10px;

            border-radius:
                8px;

        }



        /* ==================================================
           PRODUCT NAME
        ================================================== */

        .product-name {

            font-size:
                1.2rem;

            line-height:
                1.35;

        }



        /* ==================================================
           PRICE
        ================================================== */

        .product-price {

            color:
                #198754;

            font-size:
                1.05rem;

            font-weight:
                700;

        }



        /* ==================================================
           DESCRIPTION
        ================================================== */

        .product-description {

            color:
                #6c757d;

            font-size:
                0.9rem;

            line-height:
                1.6;

        }



        /* ==================================================
           DETAIL LINK
        ================================================== */

        .detail-link {

            color:
                #0d6efd;

            font-weight:
                600;

            font-size:
                0.9rem;

        }


        .detail-link i {

            transition:
                transform 0.2s ease;

        }


        .product-card:hover
        .detail-link i {

            transform:
                translateX(4px);

        }



        /* ==================================================
           FILTER INFO
        ================================================== */

        .filter-box {

            background:
                #ffffff;

            border:
                1px solid #e9ecef;

            border-radius:
                14px;

            padding:
                15px 18px;

        }



        /* ==================================================
           EMPTY
        ================================================== */

        .empty-icon {

            font-size:
                60px;

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
                aria-current="page"
            >

                Produk

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



        <?php if (
            $filter_active
        ): ?>


            <h1
                class="fw-bold
                       mb-3"
            >

                Produk
                <?= clean(
                    $selected_umkm["name"]
                ) ?>


            </h1>


            <p
                class="text-muted
                       mx-auto
                       mb-0"
                style="
                    max-width: 720px;
                "
            >

                Produk yang tersedia dari
                UMKM
                <?= clean(
                    $selected_umkm["name"]
                ) ?>.

            </p>


        <?php else: ?>


            <h1
                class="fw-bold
                       mb-3"
            >

                Produk Desa Cisalada

            </h1>


            <p
                class="text-muted
                       mx-auto
                       mb-0"
                style="
                    max-width: 720px;
                "
            >

                Temukan berbagai produk
                yang dihasilkan oleh UMKM
                masyarakat Desa Cisalada.

            </p>


        <?php endif; ?>


    </div>


</section>



<!-- ======================================================
     PRODUCT CONTENT
====================================================== -->

<section
    class="pb-5"
>


    <div
        class="container"
    >


        <!-- ==================================================
             FILTER INFO
        =================================================== -->

        <?php if (
            $filter_active
        ): ?>


            <div
                class="filter-box
                       mb-4"
            >


                <div
                    class="d-flex
                           justify-content-between
                           align-items-center
                           flex-wrap
                           gap-2"
                >


                    <div>


                        <div
                            class="small
                                   text-muted"
                        >

                            Menampilkan produk dari

                        </div>


                        <div
                            class="fw-bold"
                        >

                            <i
                                class="bi bi-shop
                                       text-primary
                                       me-1"
                            ></i>


                            <?= clean(
                                $selected_umkm["name"]
                            ) ?>


                        </div>


                    </div>



                    <a
                        href="produk.php"
                        class="btn
                               btn-outline-secondary
                               btn-sm"
                    >

                        <i
                            class="bi bi-grid
                                   me-1"
                        ></i>


                        Semua Produk

                    </a>


                </div>


            </div>


        <?php endif; ?>



        <?php if (
            $result
            &&
            mysqli_num_rows(
                $result
            ) > 0
        ): ?>


            <div
                class="row
                       g-4"
            >


                <?php while (
                    $product =
                        mysqli_fetch_assoc(
                            $result
                        )
                ): ?>


                    <div
                        class="col-md-6
                               col-lg-4"
                    >


                        <a
                            href="produk-detail.php?id=<?= (int) $product["id"] ?>"
                            class="product-link"
                        >


                            <div
                                class="card
                                       product-card
                                       shadow-sm"
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
                                        class="product-image"
                                    >


                                <?php else: ?>


                                    <div
                                        class="product-placeholder"
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
                                           d-flex
                                           flex-column"
                                >


                                    <!-- UMKM -->

                                    <div
                                        class="mb-3"
                                    >


                                        <span
                                            class="badge
                                                   bg-primary
                                                   umkm-badge"
                                        >

                                            <i
                                                class="bi bi-shop"
                                            ></i>


                                            <?= clean(
                                                $product["umkm_name"]
                                            ) ?>


                                        </span>


                                    </div>



                                    <!-- NAMA -->

                                    <h3
                                        class="product-name
                                               fw-bold
                                               mb-2"
                                    >

                                        <?= clean(
                                            $product["name"]
                                        ) ?>


                                    </h3>



                                    <!-- PEMILIK -->

                                    <?php if (
                                        !empty(
                                            $product["umkm_owner"]
                                        )
                                    ): ?>


                                        <div
                                            class="small
                                                   text-muted
                                                   mb-3"
                                        >


                                            <i
                                                class="bi bi-person
                                                       me-1"
                                            ></i>


                                            <?= clean(
                                                $product["umkm_owner"]
                                            ) ?>


                                        </div>


                                    <?php endif; ?>



                                    <!-- HARGA -->

                                    <?php if (
                                        (float)
                                        $product["price"]
                                        >
                                        0
                                    ): ?>


                                        <div
                                            class="product-price
                                                   mb-3"
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


                                    <?php else: ?>


                                        <div
                                            class="text-muted
                                                   small
                                                   mb-3"
                                        >

                                            Harga tidak dicantumkan.

                                        </div>


                                    <?php endif; ?>



                                    <!-- DESKRIPSI -->

                                    <?php if (
                                        !empty(
                                            $product["description"]
                                        )
                                    ): ?>


                                        <p
                                            class="product-description
                                                   mb-4"
                                        >

                                            <?= clean(
                                                mb_strimwidth(
                                                    $product["description"],
                                                    0,
                                                    115,
                                                    "..."
                                                )
                                            ) ?>


                                        </p>


                                    <?php else: ?>


                                        <p
                                            class="product-description
                                                   mb-4"
                                        >

                                            Informasi produk
                                            dari UMKM
                                            Desa Cisalada.

                                        </p>


                                    <?php endif; ?>



                                    <!-- DETAIL -->

                                    <div
                                        class="detail-link
                                               mt-auto
                                               d-flex
                                               align-items-center
                                               justify-content-between"
                                    >


                                        <span>

                                            Lihat Detail Produk

                                        </span>


                                        <i
                                            class="bi bi-arrow-right"
                                        ></i>


                                    </div>


                                </div>


                            </div>


                        </a>


                    </div>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <!-- ==================================================
                 EMPTY
            =================================================== -->

            <div
                class="text-center
                       py-5"
            >


                <i
                    class="bi bi-box-seam
                           empty-icon
                           text-muted"
                ></i>


                <?php if (
                    $filter_active
                ): ?>


                    <h4
                        class="fw-bold
                               mt-3"
                    >

                        Belum Ada Produk

                    </h4>


                    <p
                        class="text-muted"
                    >

                        UMKM
                        <?= clean(
                            $selected_umkm["name"]
                        ) ?>
                        belum memiliki produk
                        yang ditampilkan.

                    </p>


                    <a
                        href="umkm-detail.php?id=<?= $umkm_id ?>"
                        class="btn
                               btn-outline-primary"
                    >

                        <i
                            class="bi bi-arrow-left
                                   me-1"
                        ></i>


                        Kembali ke UMKM

                    </a>


                <?php else: ?>


                    <h4
                        class="fw-bold
                               mt-3"
                    >

                        Belum Ada Produk

                    </h4>


                    <p
                        class="text-muted"
                    >

                        Data produk UMKM
                        Desa Cisalada belum tersedia.

                    </p>


                    <a
                        href="<?= BASE_URL ?>"
                        class="btn
                               btn-outline-primary"
                    >

                        <i
                            class="bi bi-arrow-left
                                   me-1"
                        ></i>


                        Kembali ke Beranda

                    </a>


                <?php endif; ?>


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



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>