<?php

// =====================================================
// DATA KONTAK FOOTER
// =====================================================

$footer_settings =
    [];


if (
    isset(
        $conn
    )
) {


    $footer_result =
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
        $footer_result
    ) {


        while (
            $footer_row =
                mysqli_fetch_assoc(
                    $footer_result
                )
        ) {


            $footer_settings[
                $footer_row[
                    "setting_key"
                ]
            ] =
                $footer_row[
                    "setting_value"
                ];

        }

    }

}


// =====================================================
// FALLBACK DATA
// =====================================================

$footer_address =
    trim(
        $footer_settings[
            "contact_address"
        ]
        ??
        ""
    );


$footer_email =
    trim(
        $footer_settings[
            "contact_email"
        ]
        ??
        ""
    );


$footer_phone =
    trim(
        $footer_settings[
            "contact_phone"
        ]
        ??
        ""
    );


// =====================================================
// FALLBACK KONTAK
// =====================================================

if (
    $footer_address === ""
) {

    $footer_address =
        "Desa Cisalada, "
        .
        "Kecamatan Cigombong, "
        .
        "Kabupaten Bogor, "
        .
        "Jawa Barat";

}


if (
    $footer_email === ""
) {

    $footer_email =
        "Belum tersedia";

}


if (
    $footer_phone === ""
) {

    $footer_phone =
        "Belum tersedia";

}

?>


<!-- =====================================================
     GLOBAL FAVICON FALLBACK
===================================================== -->

<script>

(function () {

    "use strict";


    /*
     * ==================================================
     * CEK FAVICON YANG SUDAH ADA
     * ==================================================
     *
     * Halaman yang menggunakan header.php sudah
     * memiliki favicon.
     *
     * Halaman yang tidak menggunakan header.php
     * akan dibuatkan favicon di sini.
     *
     */


    const existingFavicon =
        document.querySelector(
            'link[rel="icon"], link[rel="shortcut icon"]'
        );


    if (
        existingFavicon
    ) {

        return;

    }



    /*
     * ==================================================
     * BASE URL
     * ==================================================
     */

    const faviconUrl =
        "<?= BASE_URL ?>assets/images/favicon.png";



    /*
     * ==================================================
     * FAVICON UTAMA
     * ==================================================
     */

    const favicon =
        document.createElement(
            "link"
        );


    favicon.rel =
        "icon";


    favicon.type =
        "image/png";


    favicon.sizes =
        "512x512";


    favicon.href =
        faviconUrl;


    document.head.appendChild(
        favicon
    );



    /*
     * ==================================================
     * SHORTCUT ICON
     * ==================================================
     */

    const shortcut =
        document.createElement(
            "link"
        );


    shortcut.rel =
        "shortcut icon";


    shortcut.type =
        "image/png";


    shortcut.href =
        faviconUrl;


    document.head.appendChild(
        shortcut
    );



    /*
     * ==================================================
     * APPLE TOUCH ICON
     * ==================================================
     */

    const apple =
        document.createElement(
            "link"
        );


    apple.rel =
        "apple-touch-icon";


    apple.href =
        faviconUrl;


    document.head.appendChild(
        apple
    );



    /*
     * ==================================================
     * THEME COLOR
     * ==================================================
     */

    const theme =
        document.createElement(
            "meta"
        );


    theme.name =
        "theme-color";


    theme.content =
        "#0d6efd";


    document.head.appendChild(
        theme
    );


})();

</script>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer
    class="bg-dark
           text-white
           mt-5"
>

    <div
        class="container
               py-5"
    >


        <div
            class="row"
        >


            <!-- =================================================
                 TENTANG
            ================================================== -->

            <div
                class="col-md-6"
            >


                <h5
                    class="fw-bold"
                >

                    CISALADA GO

                </h5>


                <p
                    class="text-white-50"
                >

                    Website informasi digital
                    Desa Cisalada untuk masyarakat.

                </p>


            </div>



            <!-- =================================================
                 NAVIGASI
            ================================================== -->

            <div
                class="col-md-3"
            >


                <h6>

                    Navigasi

                </h6>


                <ul
                    class="list-unstyled"
                >


                    <li
                        class="mb-2"
                    >

                        <a
                            href="<?= BASE_URL ?>index.php"
                            class="text-white-50
                                   text-decoration-none"
                        >

                            Beranda

                        </a>

                    </li>



                    <li
                        class="mb-2"
                    >

                        <a
                            href="<?= BASE_URL ?>pages/profil.php"
                            class="text-white-50
                                   text-decoration-none"
                        >

                            Profil

                        </a>

                    </li>



                    <li
                        class="mb-2"
                    >

                        <a
                            href="<?= BASE_URL ?>pages/berita.php"
                            class="text-white-50
                                   text-decoration-none"
                        >

                            Berita

                        </a>

                    </li>



                    <li
                        class="mb-2"
                    >

                        <a
                            href="<?= BASE_URL ?>pages/umkm.php"
                            class="text-white-50
                                   text-decoration-none"
                        >

                            UMKM

                        </a>

                    </li>



                    <li
                        class="mb-2"
                    >

                        <a
                            href="<?= BASE_URL ?>pages/kontak.php"
                            class="text-white-50
                                   text-decoration-none"
                        >

                            Kontak

                        </a>

                    </li>


                </ul>


            </div>



            <!-- =================================================
                 KONTAK
            ================================================== -->

            <div
                class="col-md-3"
            >


                <h6>

                    Kontak

                </h6>


                <p
                    class="text-white-50
                           mb-2"
                >

                    <i
                        class="bi
                               bi-geo-alt
                               me-1"
                    ></i>


                    <?= nl2br(
                        clean(
                            $footer_address
                        )
                    ) ?>


                </p>


                <p
                    class="text-white-50
                           mb-2"
                >

                    <i
                        class="bi
                               bi-envelope
                               me-1"
                    ></i>


                    <?= clean(
                        $footer_email
                    ) ?>


                </p>


                <p
                    class="text-white-50"
                >

                    <i
                        class="bi
                               bi-telephone
                               me-1"
                    ></i>


                    <?= clean(
                        $footer_phone
                    ) ?>


                </p>


            </div>


        </div>



        <hr
            class="border-secondary"
        >



        <div
            class="text-center
                   text-white-50"
        >

            © <?= date("Y") ?>

            Cisalada GO.

            Semua hak dilindungi.

        </div>


    </div>

</footer>



<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<!-- =====================================================
     JAVASCRIPT WEBSITE
===================================================== -->

<script
    src="<?= BASE_URL ?>assets/js/script.js"
></script>


<!-- =====================================================
     ANIMATION
===================================================== -->

<script
    src="<?= BASE_URL ?>assets/js/animation.js"
></script>


</body>

</html>