<?php

require_once "../config/database.php";
require_once "../includes/functions.php";


// =====================================================
// AMBIL PROFIL DESA
// =====================================================

$profile = null;


$profile_result =
    mysqli_query(
        $conn,
        "
        SELECT
            village_name,
            history,
            vision,
            mission,
            geography,
            address,
            email,
            phone,
            updated_at
        FROM village_profile
        ORDER BY id ASC
        LIMIT 1
        "
    );


if (
    $profile_result
) {

    $profile =
        mysqli_fetch_assoc(
            $profile_result
        );

}


// =====================================================
// AMBIL DATA DASAR DESA
// SEBAGAI FALLBACK
// =====================================================

$village = null;


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
            established_year,
            classification
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
// DATA KONTAK DARI SETTINGS
// FALLBACK JIKA PROFIL BELUM DIISI
// =====================================================

$settings = [];


$settings_result =
    mysqli_query(
        $conn,
        "
        SELECT
            setting_key,
            setting_value
        FROM settings
        WHERE
            setting_key IN
            (
                'contact_address',
                'contact_email',
                'contact_phone'
            )
        "
    );


if (
    $settings_result
) {

    while (
        $row =
            mysqli_fetch_assoc(
                $settings_result
            )
    ) {

        $settings[
            $row["setting_key"]
        ] =
            $row["setting_value"];

    }

}


// =====================================================
// NILAI
// =====================================================

$village_name =
    trim(
        $profile[
            "village_name"
        ]
        ??
        ""
    );


if (
    $village_name === ""
) {

    $village_name =
        $village[
            "village_name"
        ]
        ??
        "Desa Cisalada";

}


$history =
    trim(
        $profile[
            "history"
        ]
        ??
        ""
    );


$vision =
    trim(
        $profile[
            "vision"
        ]
        ??
        ""
    );


$mission =
    trim(
        $profile[
            "mission"
        ]
        ??
        ""
    );


$geography =
    trim(
        $profile[
            "geography"
        ]
        ??
        ""
    );


$address =
    trim(
        $profile[
            "address"
        ]
        ??
        ""
    );


$email =
    trim(
        $profile[
            "email"
        ]
        ??
        ""
    );


$phone =
    trim(
        $profile[
            "phone"
        ]
        ??
        ""
    );


// =====================================================
// FALLBACK KONTAK
// =====================================================

if (
    $address === ""
) {

    $address =
        trim(
            $settings[
                "contact_address"
            ]
            ??
            ""
        );

}


if (
    $email === ""
) {

    $email =
        trim(
            $settings[
                "contact_email"
            ]
            ??
            ""
        );

}


if (
    $phone === ""
) {

    $phone =
        trim(
            $settings[
                "contact_phone"
            ]
            ??
            ""
        );

}


?>

<?php

$page_title =
    "Profil Desa";

?>

<?php include "../includes/header.php"; ?>

<?php include "../includes/navbar.php"; ?>


<!-- =====================================================
     HERO
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


            <p
                class="text-primary
                       fw-semibold"
            >

                PROFIL DESA

            </p>


            <h1
                class="fw-bold"
            >

                Mengenal
                <?= clean(
                    $village_name
                ) ?>


            </h1>


            <p
                class="text-muted"
            >

                Informasi mengenai profil dan
                sejarah Desa Cisalada.

            </p>


        </div>



        <div
            class="row
                   g-5"
        >


            <!-- =================================================
                 CONTENT
            ================================================== -->

            <div
                class="col-lg-8"
            >


                <!-- TENTANG DESA -->

                <section>

                    <h3
                        class="fw-bold"
                    >

                        Tentang Desa

                    </h3>


                    <?php if (
                        $village
                    ): ?>


                        <p
                            class="text-muted
                                   lh-lg"
                        >

                            <?= clean(
                                $village_name
                            ) ?>
                            merupakan desa yang berada
                            di Kecamatan

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

                        </p>


                        <p
                            class="text-muted
                                   lh-lg"
                        >

                            Luas wilayah desa yang tercatat
                            sebesar

                            <strong>
                                <?= clean(
                                    $village[
                                        "area"
                                    ]
                                ) ?>
                            </strong>

                            dengan klasifikasi

                            <strong>
                                <?= clean(
                                    $village[
                                        "classification"
                                    ]
                                ) ?>
                            </strong>.

                        </p>


                    <?php endif; ?>


                    <?php if (
                        $history !== ""
                    ): ?>


                        <p
                            class="text-muted
                                   lh-lg"
                        >

                            <?= nl2br(
                                clean(
                                    $history
                                )
                            ) ?>


                        </p>


                    <?php else: ?>


                        <div
                            class="alert
                                   alert-light
                                   border"
                        >


                            <i
                                class="bi bi-info-circle
                                       me-2"
                            ></i>


                            Sejarah dan uraian lengkap
                            Desa Cisalada belum diisi
                            oleh pengelola desa.

                        </div>


                    <?php endif; ?>


                </section>



                <!-- =================================================
                     SEJARAH
                ================================================== -->

                <?php if (
                    $history !== ""
                ): ?>


                    <section
                        class="mt-5"
                    >


                        <h3
                            class="fw-bold"
                        >

                            Sejarah Desa

                        </h3>


                        <p
                            class="text-muted
                                   lh-lg"
                        >

                            <?= nl2br(
                                clean(
                                    $history
                                )
                            ) ?>


                        </p>


                    </section>


                <?php endif; ?>



                <!-- =================================================
                     VISI
                ================================================== -->

                <section
                    class="mt-5"
                >


                    <h3
                        class="fw-bold"
                    >

                        Visi

                    </h3>


                    <?php if (
                        $vision !== ""
                    ): ?>


                        <div
                            class="card
                                   border-0
                                   shadow-sm"
                        >


                            <div
                                class="card-body
                                       p-4"
                            >


                                <i
                                    class="bi bi-bullseye
                                           text-primary
                                           fs-2"
                                ></i>


                                <p
                                    class="mt-3
                                           mb-0
                                           text-muted
                                           lh-lg"
                                >

                                    <?= nl2br(
                                        clean(
                                            $vision
                                        )
                                    ) ?>


                                </p>


                            </div>


                        </div>


                    <?php else: ?>


                        <div
                            class="alert
                                   alert-light
                                   border"
                        >


                            Visi desa belum diisi.

                        </div>


                    <?php endif; ?>


                </section>



                <!-- =================================================
                     MISI
                ================================================== -->

                <section
                    class="mt-5"
                >


                    <h3
                        class="fw-bold"
                    >

                        Misi

                    </h3>


                    <?php if (
                        $mission !== ""
                    ): ?>


                        <div
                            class="card
                                   border-0
                                   shadow-sm"
                        >


                            <div
                                class="card-body
                                       p-4"
                            >


                                <i
                                    class="bi bi-list-check
                                           text-primary
                                           fs-2"
                                ></i>


                                <p
                                    class="mt-3
                                           mb-0
                                           text-muted
                                           lh-lg"
                                >

                                    <?= nl2br(
                                        clean(
                                            $mission
                                        )
                                    ) ?>


                                </p>


                            </div>


                        </div>


                    <?php else: ?>


                        <div
                            class="alert
                                   alert-light
                                   border"
                        >


                            Misi desa belum diisi.

                        </div>


                    <?php endif; ?>


                </section>



                <!-- =================================================
                     GEOGRAFI
                ================================================== -->

                <section
                    class="mt-5"
                >


                    <h3
                        class="fw-bold"
                    >

                        Kondisi Geografis

                    </h3>


                    <?php if (
                        $geography !== ""
                    ): ?>


                        <p
                            class="text-muted
                                   lh-lg"
                        >

                            <?= nl2br(
                                clean(
                                    $geography
                                )
                            ) ?>


                        </p>


                    <?php else: ?>


                        <div
                            class="card
                                   border-0
                                   shadow-sm"
                        >


                            <div
                                class="card-body
                                       p-4"
                            >


                                <?php if (
                                    $village
                                ): ?>


                                    <p
                                        class="text-muted
                                               lh-lg"
                                    >

                                        <?= clean(
                                            $village_name
                                        ) ?>

                                        berada di Kecamatan

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

                                    </p>


                                    <p
                                        class="text-muted
                                               mb-0"
                                    >

                                        Luas wilayah:

                                        <strong>
                                            <?= clean(
                                                $village[
                                                    "area"
                                                ]
                                            ) ?>
                                        </strong>

                                    </p>


                                <?php else: ?>


                                    <p
                                        class="text-muted
                                               mb-0"
                                    >

                                        Kondisi geografis
                                        belum tersedia.

                                    </p>


                                <?php endif; ?>


                            </div>


                        </div>


                    <?php endif; ?>


                </section>


            </div>



            <!-- =================================================
                 SIDEBAR INFO
            ================================================== -->

            <div
                class="col-lg-4"
            >


                <div
                    class="card
                           border-0
                           shadow-sm"
                    style="
                        position: sticky;
                        top: 20px;
                    "
                >


                    <div
                        class="card-body
                               p-4"
                    >


                        <h5
                            class="fw-bold"
                        >

                            Informasi Desa

                        </h5>


                        <hr>



                        <!-- NAMA -->

                        <p>

                            <strong>
                                Nama:
                            </strong>

                            <br>

                            <?= clean(
                                $village_name
                            ) ?>


                        </p>



                        <!-- KECAMATAN -->

                        <?php if (
                            $village
                        ): ?>


                            <p>

                                <strong>
                                    Kecamatan:
                                </strong>

                                <br>

                                <?= clean(
                                    $village[
                                        "district"
                                    ]
                                ) ?>


                            </p>


                            <p>

                                <strong>
                                    Kabupaten:
                                </strong>

                                <br>

                                <?= clean(
                                    $village[
                                        "regency"
                                    ]
                                ) ?>


                            </p>


                            <p>

                                <strong>
                                    Provinsi:
                                </strong>

                                <br>

                                <?= clean(
                                    $village[
                                        "province"
                                    ] )
                                ?>


                            </p>


                            <p>

                                <strong>
                                    Luas Wilayah:
                                </strong>

                                <br>

                                <?= clean(
                                    $village[
                                        "area"
                                    ] )
                                ?>


                            </p>


                            <p>

                                <strong>
                                    Klasifikasi:
                                </strong>

                                <br>

                                <?= clean(
                                    $village[
                                        "classification"
                                    ]
                                ) ?>


                            </p>


                            <p>

                                <strong>
                                    Tahun Berdiri:
                                </strong>

                                <br>

                                <?= clean(
                                    $village[
                                        "established_year"
                                    ]
                                ) ?>


                            </p>


                        <?php endif; ?>



                        <!-- ALAMAT -->

                        <p>

                            <strong>
                                Alamat:
                            </strong>

                            <br>


                            <?php if (
                                $address !== ""
                            ): ?>


                                <?= nl2br(
                                    clean(
                                        $address
                                    )
                                ) ?>


                            <?php else: ?>


                                <span
                                    class="text-muted"
                                >

                                    Belum tersedia

                                </span>


                            <?php endif; ?>


                        </p>



                        <!-- EMAIL -->

                        <p>

                            <strong>
                                Email:
                            </strong>

                            <br>


                            <?php if (
                                $email !== ""
                            ): ?>


                                <a
                                    href="mailto:<?= clean(
                                        $email
                                    ) ?>"
                                    class="text-decoration-none"
                                >

                                    <?= clean(
                                        $email
                                    ) ?>


                                </a>


                            <?php else: ?>


                                <span
                                    class="text-muted"
                                >

                                    Belum tersedia

                                </span>


                            <?php endif; ?>


                        </p>



                        <!-- TELEPON -->

                        <p
                            class="mb-0"
                        >

                            <strong>
                                Telepon:
                            </strong>

                            <br>


                            <?php if (
                                $phone !== ""
                            ): ?>


                                <a
                                    href="tel:<?= clean(
                                        $phone
                                    ) ?>"
                                    class="text-decoration-none"
                                >

                                    <?= clean(
                                        $phone
                                    ) ?>


                                </a>


                            <?php else: ?>


                                <span
                                    class="text-muted"
                                >

                                    Belum tersedia

                                </span>


                            <?php endif; ?>


                        </p>


                    </div>


                </div>


            </div>


        </div>


    </div>

</section>


<?php include "../includes/footer.php"; ?>