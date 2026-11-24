<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$id =
    isset(
        $_GET["id"]
    )
    ?
    (int)
    $_GET["id"]
    :
    0;


if (
    $id <= 0
) {

    header(
        "Location: index.php?error=id"
    );

    exit;

}


// ======================================================
// AMBIL DATA WISATA
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
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
        FROM tourism
        WHERE id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: index.php?error=database"
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


$wisata =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$wisata
) {

    header(
        "Location: index.php?error=not_found"
    );

    exit;

}


// ======================================================
// ISI DATA
// ======================================================

$name =
    $wisata["name"]
    ??
    "";


$description =
    $wisata["description"]
    ??
    "";


$location =
    $wisata["location"]
    ??
    "";


$latitude =
    $wisata["latitude"]
    ??
    "";


$longitude =
    $wisata["longitude"]
    ??
    "";


$facilities =
    $wisata["facilities"]
    ??
    "";


$operating_hours =
    $wisata["operating_hours"]
    ??
    "";


$ticket_price =
    $wisata["ticket_price"]
    ??
    "";


$contact =
    $wisata["contact"]
    ??
    "";


$old_photo =
    $wisata["photo"]
    ??
    "";


$error =
    "";


// ======================================================
// PROSES UPDATE
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
    // FOTO BARU
    // ==================================================

    $new_photo =
        $old_photo;


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
                "Foto baru gagal diupload.";

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


            $new_photo =
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
                $new_photo;


            if (
                !move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )
            ) {

                $error =
                    "Foto baru gagal disimpan.";

                $new_photo =
                    $old_photo;

            }

        }

    }



    // ==================================================
    // KOORDINAT
    // ==================================================

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
    // UPDATE DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $stmt =
            mysqli_prepare(
                $conn,
                "
                UPDATE tourism

                SET
                    name = ?,
                    description = ?,
                    location = ?,
                    latitude = ?,
                    longitude = ?,
                    facilities = ?,
                    operating_hours = ?,
                    ticket_price = ?,
                    contact = ?,
                    photo = ?

                WHERE id = ?
                "
            );


        if (
            !$stmt
        ) {

            $error =
                "Gagal mempersiapkan database: "
                .
                mysqli_error(
                    $conn
                );

        } else {


            mysqli_stmt_bind_param(
                $stmt,
                "sssssssdssi",
                $name,
                $description,
                $location,
                $latitude_value,
                $longitude_value,
                $facilities,
                $operating_hours,
                $ticket_price_value,
                $contact,
                $new_photo,
                $id
            );


            if (
                mysqli_stmt_execute(
                    $stmt
                )
            ) {


                mysqli_stmt_close(
                    $stmt
                );


                // ==========================================
                // HAPUS FOTO LAMA
                // ==========================================

                if (
                    $new_photo
                    !==
                    $old_photo
                    &&
                    !empty(
                        $old_photo
                    )
                ) {


                    $old_path =
                        $upload_directory
                        .
                        basename(
                            $old_photo
                        );


                    if (
                        file_exists(
                            $old_path
                        )
                    ) {

                        @unlink(
                            $old_path
                        );

                    }

                }


                header(
                    "Location: index.php?success=edit"
                );


                exit;

            } else {


                $error =
                    "Gagal memperbarui data wisata: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    $new_photo
                    !==
                    $old_photo
                    &&
                    !empty(
                        $new_photo
                    )
                ) {


                    $new_photo_path =
                        $upload_directory
                        .
                        basename(
                            $new_photo
                        );


                    if (
                        file_exists(
                            $new_photo_path
                        )
                    ) {

                        @unlink(
                            $new_photo_path
                        );

                    }

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
        Edit Wisata - Cisalada GO
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


        .current-photo {

            width:
                220px;

            height:
                150px;

            object-fit:
                cover;

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

            Edit Wisata

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Perbarui informasi objek wisata
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
                        required
                    >


                </div>



                <!-- ==================================================
                     LOKASI
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

                        Koordinat digunakan untuk menampilkan
                        marker wisata pada Peta Potensi Desa.

                        Contoh:

                        <strong>
                            -6.73292622177123,
                            106.79310635054685
                        </strong>

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

                        Kosongkan keduanya jika wisata
                        belum memiliki koordinat.

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
                    ><?= clean(
                        $facilities
                    ) ?></textarea>


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
                    ><?= clean(
                        $description
                    ) ?></textarea>


                </div>



                <!-- ==================================================
                     FOTO LAMA
                =================================================== -->

                <?php if (
                    !empty(
                        $old_photo
                    )
                ): ?>


                    <div
                        class="mb-3"
                    >


                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Foto Saat Ini

                        </label>


                        <div>


                            <img
                                src="<?= BASE_URL ?>assets/uploads/wisata/<?= rawurlencode(
                                    basename(
                                        $old_photo
                                    )
                                ) ?>"
                                alt="<?= clean(
                                    $name
                                ) ?>"
                                class="current-photo"
                            >


                        </div>


                    </div>


                <?php endif; ?>



                <!-- ==================================================
                     FOTO BARU
                =================================================== -->

                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Ganti Foto

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

                        Kosongkan jika tidak ingin
                        mengganti foto.

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


                        Simpan Perubahan

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