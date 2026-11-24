<?php

require_once "../config/database.php";
require_once "../includes/functions.php";

$page_title = "Pemerintahan Desa";


// ======================================================
// AMBIL DATA PEMERINTAHAN DARI DATABASE
// ======================================================

$sql = "
    SELECT
        id,
        name,
        position,
        category,
        photo,
        description,
        display_order
    FROM government_officials
    WHERE is_active = 1
    ORDER BY
        display_order ASC,
        id ASC
";


$query = mysqli_query(
    $conn,
    $sql
);


// ======================================================
// CEK ERROR QUERY
// ======================================================

$query_error = "";

if (!$query) {

    $query_error =
        mysqli_error($conn);

}


// ======================================================
// SIMPAN DATA
// ======================================================

$officials = [];

if ($query) {

    while (
        $row =
            mysqli_fetch_assoc($query)
    ) {

        $officials[] =
            $row;

    }

}


// ======================================================
// KELOMPOKKAN BERDASARKAN KATEGORI
// ======================================================

$groups = [];


foreach (
    $officials
    as
    $official
) {

    $category =
        trim(
            $official["category"]
            ??
            ""
        );


    if (
        $category === ""
    ) {

        $category =
            "Pemerintah Desa";

    }


    if (
        !isset(
            $groups[$category]
        )
    ) {

        $groups[$category] = [];

    }


    $groups[$category][] =
        $official;

}

?>

<?php include "../includes/header.php"; ?>

<?php include "../includes/navbar.php"; ?>


<style>

/* ======================================================
   HERO
====================================================== */

.government-hero {

    background:
        #f8f9fa;

    padding:
        70px 20px;

}


/* ======================================================
   SECTION
====================================================== */

.government-section {

    padding:
        70px 0;

}


/* ======================================================
   CATEGORY
====================================================== */

.government-category {

    margin-bottom:
        55px;

}


.government-category-title {

    font-size:
        28px;

    font-weight:
        700;

    margin-bottom:
        25px;

}


.government-category-line {

    width:
        55px;

    height:
        4px;

    background:
        #0d6efd;

    border-radius:
        20px;

    margin-top:
        8px;

}


/* ======================================================
   CARD
====================================================== */

.government-card {

    border:
        0;

    border-radius:
        16px;

    overflow:
        hidden;

    background:
        #ffffff;

    height:
        100%;

    transition:
        all 0.2s ease;

}


.government-card:hover {

    transform:
        translateY(-5px);

    box-shadow:
        0 12px 30px
        rgba(
            0,
            0,
            0,
            0.10
        ) !important;

}


/* ======================================================
   FOTO
====================================================== */

.government-photo {

    width:
        100%;

    height:
        270px;

    object-fit:
        cover;

}


.government-photo-placeholder {

    width:
        100%;

    height:
        270px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        #f1f3f5;

}


.government-photo-placeholder i {

    font-size:
        80px;

    color:
        #adb5bd;

}


/* ======================================================
   CONTENT
====================================================== */

.government-position {

    color:
        #0d6efd;

    font-size:
        14px;

    font-weight:
        600;

    margin-bottom:
        8px;

}


.government-name {

    font-size:
        20px;

    font-weight:
        700;

    margin-bottom:
        8px;

}


/* ======================================================
   EMPTY
====================================================== */

.government-empty {

    text-align:
        center;

    padding:
        100px 20px;

}


.government-empty-icon {

    font-size:
        70px;

    color:
        #adb5bd;

}

</style>



<!-- ======================================================
     HERO
====================================================== -->

<section
    class="government-hero"
>

    <div
        class="container"
    >


        <div
            class="text-center"
        >


            <p
                class="text-primary
                       fw-semibold
                       mb-2"
            >

                PEMERINTAHAN DESA

            </p>


            <h1
                class="fw-bold
                       mb-3"
            >

                Pemerintahan Desa Cisalada

            </h1>


            <p
                class="text-muted
                       mb-0"
            >

                Struktur dan informasi perangkat
                Pemerintahan Desa Cisalada.

            </p>


        </div>


    </div>

</section>



<!-- ======================================================
     CONTENT
====================================================== -->

<section
    class="government-section"
>

    <div
        class="container"
    >


        <!-- ==================================================
             ERROR DATABASE
        =================================================== -->

        <?php if (
            !empty($query_error)
        ): ?>


            <div
                class="alert
                       alert-danger"
            >

                <strong>
                    Terjadi kesalahan database.
                </strong>

                <br>

                <?= htmlspecialchars(
                    $query_error,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>

            </div>


        <?php endif; ?>



        <!-- ==================================================
             DATA ADA
        =================================================== -->

        <?php if (
            count($officials) > 0
        ): ?>


            <?php foreach (
                $groups
                as
                $category =>
                $members
            ): ?>


                <div
                    class="government-category"
                >


                    <!-- CATEGORY TITLE -->

                    <div
                        class="mb-4"
                    >


                        <h2
                            class="government-category-title
                                   mb-1"
                        >

                            <?= htmlspecialchars(
                                $category,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>

                        </h2>


                        <div
                            class="government-category-line"
                        ></div>


                    </div>



                    <!-- MEMBERS -->

                    <div
                        class="row
                               g-4"
                    >


                        <?php foreach (
                            $members
                            as
                            $official
                        ): ?>


                            <div
                                class="col-12
                                       col-md-6
                                       col-lg-4
                                       col-xl-3"
                            >


                                <div
                                    class="card
                                           government-card
                                           shadow-sm"
                                >


                                    <!-- ==========================
                                         FOTO
                                    =========================== -->

                                    <?php if (
                                        !empty(
                                            $official["photo"]
                                        )
                                    ): ?>


                                        <img
                                            src="<?= BASE_URL ?>assets/uploads/pemerintahan/<?= htmlspecialchars(
                                                basename(
                                                    $official["photo"]
                                                ),
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>"
                                            alt="<?= htmlspecialchars(
                                                $official["name"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>"
                                            class="government-photo"
                                            loading="lazy"
                                        >


                                    <?php else: ?>


                                        <div
                                            class="government-photo-placeholder"
                                        >

                                            <i
                                                class="bi bi-person-circle"
                                            ></i>

                                        </div>


                                    <?php endif; ?>



                                    <!-- ==========================
                                         CONTENT
                                    =========================== -->

                                    <div
                                        class="card-body
                                               p-4"
                                    >


                                        <!-- JABATAN -->

                                        <div
                                            class="government-position"
                                        >

                                            <?= htmlspecialchars(
                                                $official["position"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </div>


                                        <!-- NAMA -->

                                        <div
                                            class="government-name"
                                        >

                                            <?= htmlspecialchars(
                                                $official["name"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </div>


                                        <!-- DESKRIPSI -->

                                        <?php if (
                                            !empty(
                                                $official["description"]
                                            )
                                        ): ?>


                                            <p
                                                class="text-muted
                                                       small
                                                       mb-0"
                                                style="
                                                    white-space: pre-line;
                                                "
                                            >

                                                <?= htmlspecialchars(
                                                    $official["description"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </p>


                                        <?php endif; ?>


                                    </div>


                                </div>


                            </div>


                        <?php endforeach; ?>


                    </div>


                </div>


            <?php endforeach; ?>


        <?php else: ?>


            <!-- ==================================================
                 DATA KOSONG
            =================================================== -->

            <div
                class="government-empty"
            >


                <i
                    class="bi bi-people
                           government-empty-icon"
                ></i>


                <h3
                    class="fw-bold
                           mt-4"
                >

                    Data Pemerintahan Belum Tersedia

                </h3>


                <p
                    class="text-muted
                           mb-0"
                >

                    Informasi perangkat Pemerintahan
                    Desa Cisalada belum tersedia.

                </p>


            </div>


        <?php endif; ?>


    </div>

</section>



<?php include "../includes/footer.php"; ?>