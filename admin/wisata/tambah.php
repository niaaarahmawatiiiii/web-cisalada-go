<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$error = "";


$name = "";

$description = "";

$location = "";

$latitude = "";

$longitude = "";

$facilities = "";

$operating_hours = "";

$ticket_price = "";

$contact = "";


// ======================================================
// PROSES TAMBAH
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {


    $name =
        trim(
            $_POST["name"]
            ??
            ""
        );


    $description =
        trim(
            $_POST["description"]
            ??
            ""
        );


    $location =
        trim(
            $_POST["location"]
            ??
            ""
        );


    $latitude =
        trim(
            $_POST["latitude"]
            ??
            ""
        );


    $longitude =
        trim(
            $_POST["longitude"]
            ??
            ""
        );


    $facilities =
        trim(
            $_POST["facilities"]
            ??
            ""
        );


    $operating_hours =
        trim(
            $_POST["operating_hours"]
            ??
            ""
        );


    $ticket_price =
        trim(
            $_POST["ticket_price"]
            ??
            ""
        );


    $contact =
        trim(
            $_POST["contact"]
            ??
            ""
        );



    // ==================================================
    // VALIDASI NAMA
    // ==================================================

    if (
        $name === ""
    ) {

        $error =
            "Nama wisata wajib diisi.";

    }



    // ==================================================
    // VALIDASI HARGA
    // ==================================================

    $ticket_price_value =
        0;


    if (
        empty($error)
        &&
        $ticket_price !== ""
    ) {


        if (
            !is_numeric(
                $ticket_price
            )
        ) {

            $error =
                "Harga tiket harus berupa angka.";

        } elseif (
            (float)
            $ticket_price
            <
            0
        ) {

            $error =
                "Harga tiket tidak boleh kurang dari 0.";

        } else {

            $ticket_price_value =
                (float)
                $ticket_price;

        }

    }



    // ==================================================
    // VALIDASI LATITUDE
    // ==================================================

    if (
        empty($error)
        &&
        $latitude !== ""
    ) {


        if (
            !is_numeric(
                $latitude
            )
        ) {

            $error =
                "Latitude harus berupa angka.";

        } elseif (
            (float)
            $latitude
            <
            -90
            ||
            (float)
            $latitude
            >
            90
        ) {

            $error =
                "Latitude harus berada antara -90 sampai 90.";

        }

    }



    // ==================================================
    // VALIDASI LONGITUDE
    // ==================================================

    if (
        empty($error)
        &&
        $longitude !== ""
    ) {


        if (
            !is_numeric(
                $longitude
            )
        ) {

            $error =
                "Longitude harus berupa angka.";

        } elseif (
            (float)
            $longitude
            <
            -180
            ||
            (float)
            $longitude
            >
            180
        ) {

            $error =
                "Longitude harus berada antara -180 sampai 180.";

        }

    }



    // ==================================================
    // VALIDASI PASANGAN KOORDINAT
    // ==================================================

    if (
        empty($error)
        &&
        (
            ($latitude === "")
            xor
            ($longitude === "")
        )
    ) {

        $error =
            "Latitude dan Longitude harus diisi bersama-sama.";

    }



    // ==================================================
    // UPLOAD FOTO
    // ==================================================

    $image_name =
        "";


    $upload_directory =
        "../../assets/uploads/wisata/";


    if (
        empty($error)
        &&
        isset(
            $_FILES["photo"]
        )
        &&
        $_FILES["photo"]["error"]
        !==
        UPLOAD_ERR_NO_FILE
    ) {


        $file =
            $_FILES["photo"];


        if (
            $file["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "Foto wisata gagal diupload.";

        }



        if (
            empty($error)
            &&
            $file["size"]
            >
            5 * 1024 * 1024
        ) {

            $error =
                "Ukuran foto maksimal 5 MB.";

        }



        if (
            empty($error)
        ) {


            $extension =
                strtolower(
                    pathinfo(
                        $file["name"],
                        PATHINFO_EXTENSION
                    )
                );


            $allowed_extensions = [

                "jpg",

                "jpeg",

                "png",

                "webp"

            ];


            if (
                !in_array(
                    $extension,
                    $allowed_extensions,
                    true
                )
            ) {

                $error =
                    "Format foto harus JPG, JPEG, PNG, atau WEBP.";

            }

        }



        if (
            empty($error)
        ) {


            $finfo =
                finfo_open(
                    FILEINFO_MIME_TYPE
                );


            $mime =
                finfo_file(
                    $finfo,
                    $file["tmp_name"]
                );


            finfo_close(
                $finfo
            );


            $allowed_mimes = [

                "image/jpeg",

                "image/png",

                "image/webp"

            ];


            if (
                !in_array(
                    $mime,
                    $allowed_mimes,
                    true
                )
            ) {

                $error =
                    "File yang diupload bukan gambar yang valid.";

            }

        }



        if (
            empty($error)
            &&
            !is_dir(
                $upload_directory
            )
        ) {

            mkdir(
                $upload_directory,
                0777,
                true
            );

        }



        if (
            empty($error)
        ) {


            $image_name =
                "wisata_"
                .
                date("YmdHis")
                .
                "_"
                .
                uniqid()
                .
                "."
                .
                $extension;


            $destination =
                $upload_directory
                .
                $image_name;


            if (
                !move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )
            ) {

                $error =
                    "Foto wisata gagal disimpan.";


                $image_name =
                    "";

            }

        }

    }



    // ==================================================
    // KOORDINAT
    // ==================================================
    // Simpan langsung sebagai string supaya presisi
    // desimal tidak dipaksa menjadi float terlebih dahulu.

    $latitude_value =
        $latitude !== ""
        ?
        $latitude
        :
        null;


    $longitude_value =
        $longitude !== ""
        ?
        $longitude
        :
        null;



    // ==================================================
    // SIMPAN DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $stmt =
            mysqli_prepare(
                $conn,
                "
                INSERT INTO tourism
                (
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
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
                "
            );


        if (
            !$stmt
        ) {


            if (
                !empty(
                    $image_name
                )
            ) {

                @unlink(
                    $upload_directory
                    .
                    $image_name
                );

            }


            $error =
                "Gagal mempersiapkan database: "
                .
                mysqli_error(
                    $conn
                );

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "ssssssdsss",
                $name,
                $description,
                $location,
                $latitude_value,
                $longitude_value,
                $facilities,
                $operating_hours,
                $ticket_price_value,
                $contact,
                $image_name
            );


            if (
                mysqli_stmt_execute(
                    $stmt
                )
            ) {


                mysqli_stmt_close(
                    $stmt
                );


                header(
                    "Location: index.php?success=tambah"
                );


                exit;

            } else {


                $error =
                    "Gagal menyimpan data wisata: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    !empty(
                        $image_name
                    )
                ) {

                    @unlink(
                        $upload_directory
                        .
                        $image_name
                    );

                }

            }

        }

    }

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
        Tambah Wisata - Cisalada GO
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {

            background:
                #f5f7fa;

        }


        .form-card {

            border:
                0;

            border-radius:
                16px;

        }


        .coordinate-info {

            background:
                #eef6ff;

            border:
                1px solid #cfe2ff;

            border-radius:
                12px;

        }

    </style>

</head>


<body>


<!-- ======================================================
     NAVBAR
====================================================== -->

<nav
    class="navbar
           navbar-dark
           bg-primary"
>


    <div
        class="container-fluid
               px-4"
    >


        <a
            href="<?= BASE_URL ?>admin/index.php"
            class="navbar-brand
                   fw-bold"
        >

            CISALADA GO

        </a>


        <a
            href="<?= BASE_URL ?>admin/logout.php"
            class="btn
                   btn-outline-light
                   btn-sm"
            onclick="
                return confirm(
                    'Yakin ingin logout?'
                );
            "
        >

            <i
                class="bi bi-box-arrow-right
                       me-1"
            ></i>


            Logout

        </a>


    </div>

</nav>



<!-- ======================================================
     CONTENT
====================================================== -->

<div
    class="container
           py-4"
>


    <div
        class="mb-4"
    >


        <a
            href="index.php"
            class="text-decoration-none
                   text-muted"
        >

            <i
                class="bi bi-arrow-left
                       me-1"
            ></i>


            Kembali ke Wisata

        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Tambah Wisata

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Tambahkan informasi objek wisata
            Desa Cisalada.

        </p>


    </div>



    <?php if (
        !empty($error)
    ): ?>


        <div
            class="alert
                   alert-danger
                   alert-dismissible
                   fade show"
        >


            <i
                class="bi bi-exclamation-circle
                       me-1"
            ></i>


            <?= clean(
                $error
            ) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ==================================================
         FORM CARD
    ================================================== -->

    <div
        class="card
               form-card
               shadow-sm"
    >


        <div
            class="card-body
                   p-4
                   p-lg-5"
        >


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- ==================================================
                     NAMA
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Nama Wisata

                        <span
                            class="text-danger"
                        >
                            *
                        </span>

                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= clean(
                            $name
                        ) ?>"
                        placeholder="Contoh: Curug Cisalada"
                        required
                    >


                </div>



                <!-- ==================================================
                     LOKASI / ALAMAT
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Lokasi / Alamat

                    </label>


                    <input
                        type="text"
                        name="location"
                        class="form-control"
                        value="<?= clean(
                            $location
                        ) ?>"
                        placeholder="Contoh: Desa Cisalada, Kecamatan Cigombong"
                    >


                </div>



                <!-- ==================================================
                     KOORDINAT
                =================================================== -->

                <div
                    class="coordinate-info
                           p-3
                           mb-4"
                >


                    <div
                        class="d-flex
                               gap-2
                               mb-2"
                    >


                        <i
                            class="bi bi-geo-alt-fill
                                   text-primary
                                   fs-5"
                        ></i>


                        <div
                            class="fw-semibold"
                        >

                            Koordinat Lokasi

                        </div>


                    </div>


                    <div
                        class="small
                               text-muted
                               mb-3"
                    >

                        Masukkan koordinat dari Google Maps.
                        Contoh:

                        <strong>
                            -6.73292622177123,
                            106.79310635054685
                        </strong>


                        <br>


                        Koordinat digunakan agar wisata
                        dapat tampil sebagai marker pada
                        Peta Potensi Desa.

                    </div>



                    <div
                        class="row
                               g-3"
                    >


                        <!-- LATITUDE -->

                        <div
                            class="col-md-6"
                        >


                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Latitude

                                <span
                                    class="text-muted
                                           fw-normal"
                                >

                                    (Opsional)

                                </span>

                            </label>


                            <input
                                type="text"
                                name="latitude"
                                class="form-control"
                                value="<?= clean(
                                    $latitude
                                ) ?>"
                                inputmode="decimal"
                                autocomplete="off"
                                placeholder="-6.73292622177123"
                            >


                        </div>



                        <!-- LONGITUDE -->

                        <div
                            class="col-md-6"
                        >


                            <label
                                class="form-label
                                       fw-semibold"
                            >

                                Longitude

                                <span
                                    class="text-muted
                                           fw-normal"
                                >

                                    (Opsional)

                                </span>

                            </label>


                            <input
                                type="text"
                                name="longitude"
                                class="form-control"
                                value="<?= clean(
                                    $longitude
                                ) ?>"
                                inputmode="decimal"
                                autocomplete="off"
                                placeholder="106.79310635054685"
                            >


                        </div>


                    </div>


                    <div
                        class="small
                               text-muted
                               mt-2"
                    >

                        Latitude dan Longitude harus diisi
                        bersamaan agar lokasi dapat ditampilkan
                        pada peta.

                    </div>


                </div>



                <!-- ==================================================
                     JAM OPERASIONAL
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Jam Operasional

                    </label>


                    <input
                        type="text"
                        name="operating_hours"
                        class="form-control"
                        value="<?= clean(
                            $operating_hours
                        ) ?>"
                        placeholder="Contoh: 08.00 - 17.00 WIB"
                    >


                </div>



                <!-- ==================================================
                     HARGA TIKET
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Harga Tiket

                    </label>


                    <div
                        class="input-group"
                    >


                        <span
                            class="input-group-text"
                        >

                            Rp

                        </span>


                        <input
                            type="number"
                            name="ticket_price"
                            class="form-control"
                            value="<?= clean(
                                $ticket_price
                            ) ?>"
                            min="0"
                            step="0.01"
                            placeholder="0"
                        >


                    </div>


                    <div
                        class="form-text"
                    >

                        Isi 0 jika gratis.

                    </div>


                </div>



                <!-- ==================================================
                     KONTAK
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Kontak

                    </label>


                    <input
                        type="text"
                        name="contact"
                        class="form-control"
                        value="<?= clean(
                            $contact
                        ) ?>"
                        placeholder="Nomor telepon / WhatsApp"
                    >


                </div>



                <!-- ==================================================
                     FASILITAS
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Fasilitas

                    </label>


                    <textarea
                        name="facilities"
                        class="form-control"
                        rows="4"
                        placeholder="Contoh:
Parkir
Toilet
Mushola
Warung"
                    ><?= clean(
                        $facilities
                    ) ?></textarea>


                    <div
                        class="form-text"
                    >

                        Pisahkan fasilitas dengan baris baru.

                    </div>


                </div>



                <!-- ==================================================
                     DESKRIPSI
                =================================================== -->

                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Deskripsi

                    </label>


                    <textarea
                        name="description"
                        class="form-control"
                        rows="6"
                        placeholder="Jelaskan objek wisata, daya tarik, suasana, dan informasi penting lainnya."
                    ><?= clean(
                        $description
                    ) ?></textarea>


                </div>



                <!-- ==================================================
                     FOTO
                =================================================== -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Foto Wisata

                    </label>


                    <input
                        type="file"
                        name="photo"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                    >


                    <div
                        class="form-text"
                    >

                        JPG, JPEG, PNG, WEBP.
                        Maksimal 5 MB.

                    </div>


                </div>



                <!-- ==================================================
                     BUTTON
                =================================================== -->

                <div
                    class="d-flex
                           gap-2"
                >


                    <a
                        href="index.php"
                        class="btn
                               btn-secondary"
                    >

                        <i
                            class="bi bi-arrow-left
                                   me-1"
                        ></i>


                        Batal

                    </a>


                    <button
                        type="submit"
                        class="btn
                               btn-primary"
                    >

                        <i
                            class="bi bi-save
                                   me-1"
                        ></i>


                        Simpan Wisata

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>