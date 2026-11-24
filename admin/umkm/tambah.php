<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$error = "";


$name = "";

$owner = "";

$category = "";

$description = "";

$phone = "";

$address = "";

$maps_url = "";

$latitude = "";

$longitude = "";


// ======================================================
// PROSES TAMBAH
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {


    $name =
        trim(
            $_POST["name"] ?? ""
        );


    $owner =
        trim(
            $_POST["owner"] ?? ""
        );


    $category =
        trim(
            $_POST["category"] ?? ""
        );


    $description =
        trim(
            $_POST["description"] ?? ""
        );


    $phone =
        trim(
            $_POST["phone"] ?? ""
        );


    $address =
        trim(
            $_POST["address"] ?? ""
        );


    $maps_url =
        trim(
            $_POST["maps_url"] ?? ""
        );


    $latitude =
        trim(
            $_POST["latitude"] ?? ""
        );


    $longitude =
        trim(
            $_POST["longitude"] ?? ""
        );


    // ==================================================
    // VALIDASI
    // ==================================================

    if (
        empty($name)
    ) {

        $error =
            "Nama UMKM wajib diisi.";

    } elseif (
        empty($owner)
    ) {

        $error =
            "Nama pemilik wajib diisi.";

    }


    // ==================================================
    // VALIDASI LINK GOOGLE MAPS
    // ==================================================

    if (
        empty($error)
        &&
        !empty($maps_url)
    ) {


        if (
            !filter_var(
                $maps_url,
                FILTER_VALIDATE_URL
            )
        ) {

            $error =
                "Link Google Maps tidak valid.";

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
            !is_numeric($latitude)
        ) {

            $error =
                "Latitude harus berupa angka.";

        } elseif (
            (float) $latitude < -90
            ||
            (float) $latitude > 90
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
            !is_numeric($longitude)
        ) {

            $error =
                "Longitude harus berupa angka.";

        } elseif (
            (float) $longitude < -180
            ||
            (float) $longitude > 180
        ) {

            $error =
                "Longitude harus berada antara -180 sampai 180.";

        }

    }



    // ==================================================
    // VALIDASI KOORDINAT
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

    $photo_name = "";


    $upload_directory =
        "../../assets/uploads/umkm/";


    if (
        empty($error)
        &&
        isset($_FILES["photo"])
        &&
        $_FILES["photo"]["error"]
        !== UPLOAD_ERR_NO_FILE
    ) {


        $file =
            $_FILES["photo"];


        if (
            $file["error"]
            !== UPLOAD_ERR_OK
        ) {

            $error =
                "Foto gagal diupload.";

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


            $photo_name =
                "umkm_"
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
                $photo_name;


            if (
                !move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )
            ) {

                $error =
                    "Foto gagal disimpan.";

                $photo_name = "";

            }

        }

    }



    // ==================================================
    // KOORDINAT
    // ==================================================
    // Simpan langsung sebagai string agar angka desimal
    // presisi tidak dibulatkan oleh PHP float sebelum
    // disimpan ke DECIMAL MySQL.

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


        $sql = "
            INSERT INTO umkm
            (
                name,
                owner,
                category,
                description,
                photo,
                phone,
                address,
                maps_url,
                latitude,
                longitude
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
        ";


        $stmt =
            mysqli_prepare(
                $conn,
                $sql
            );


        if (
            !$stmt
        ) {


            if (
                !empty($photo_name)
            ) {

                @unlink(
                    $upload_directory
                    .
                    $photo_name
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
                "ssssssssss",
                $name,
                $owner,
                $category,
                $description,
                $photo_name,
                $phone,
                $address,
                $maps_url,
                $latitude_value,
                $longitude_value
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
                    "Gagal menyimpan data UMKM: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    !empty($photo_name)
                ) {

                    @unlink(
                        $upload_directory
                        .
                        $photo_name
                    );

                }

            }

        }

    }


}
?>


<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>

        Tambah UMKM - Cisalada GO

    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body
    class="bg-light"
>


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
        >

            <i
                class="bi bi-box-arrow-right me-1"
            ></i>


            Logout


        </a>


    </div>


</nav>



<div
    class="container
           py-4"
>


    <!-- ==================================================
         HEADER
    ================================================== -->


    <div
        class="mb-4"
    >


        <div
            class="mb-3"
        >


            <a
                href="index.php"
                class="text-decoration-none
                       text-muted"
            >


                <i
                    class="bi bi-arrow-left me-1"
                ></i>


                Kembali ke UMKM


            </a>


        </div>



        <h2
            class="fw-bold
                   mb-1"
        >


            Tambah UMKM


        </h2>



        <p
            class="text-muted
                   mb-0"
        >


            Tambahkan data UMKM masyarakat
            Desa Cisalada.


        </p>


    </div>



    <!-- ==================================================
         ERROR
    ================================================== -->


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


            <?= clean($error) ?>



            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>


        </div>


    <?php endif; ?>



    <!-- ==================================================
         FORM
    ================================================== -->


    <div
        class="card
               border-0
               shadow-sm"
    >


        <div
            class="card-body
                   p-4"
        >


            <form
                method="POST"
                enctype="multipart/form-data"
            >



                <!-- NAMA -->


                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        Nama UMKM

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
                        value="<?= clean($name) ?>"
                        placeholder="Contoh: Keripik Pisang Cisalada"
                        required
                    >


                </div>



                <!-- PEMILIK -->


                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        Nama Pemilik

                        <span
                            class="text-danger"
                        >

                            *

                        </span>


                    </label>



                    <input
                        type="text"
                        name="owner"
                        class="form-control"
                        value="<?= clean($owner) ?>"
                        placeholder="Nama pemilik UMKM"
                        required
                    >


                </div>



                <!-- KATEGORI -->


                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        Kategori


                    </label>



                    <select
                        name="category"
                        class="form-select"
                    >


                        <option
                            value=""
                        >

                            -- Pilih Kategori --

                        </option>



                        <option
                            value="Kuliner"
                            <?= $category === "Kuliner"
                                ? "selected"
                                : "" ?>
                        >

                            Kuliner

                        </option>



                        <option
                            value="Kerajinan"
                            <?= $category === "Kerajinan"
                                ? "selected"
                                : "" ?>
                        >

                            Kerajinan

                        </option>



                        <option
                            value="Fashion"
                            <?= $category === "Fashion"
                                ? "selected"
                                : "" ?>
                        >

                            Fashion

                        </option>



                        <option
                            value="Jasa"
                            <?= $category === "Jasa"
                                ? "selected"
                                : "" ?>
                        >

                            Jasa

                        </option>



                        <option
                            value="Pertanian"
                            <?= $category === "Pertanian"
                                ? "selected"
                                : "" ?>
                        >

                            Pertanian

                        </option>



                        <option
                            value="Perdagangan"
                            <?= $category === "Perdagangan"
                                ? "selected"
                                : "" ?>
                        >

                            Perdagangan

                        </option>



                        <option
                            value="Lainnya"
                            <?= $category === "Lainnya"
                                ? "selected"
                                : "" ?>
                        >

                            Lainnya

                        </option>


                    </select>


                </div>



                <!-- TELEPON -->


                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        Nomor Telepon


                    </label>



                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="<?= clean($phone) ?>"
                        placeholder="08xxxxxxxxxx"
                    >


                </div>



                <!-- ALAMAT -->


                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        Alamat


                    </label>



                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                        placeholder="Alamat tempat usaha"
                    ><?= clean($address) ?></textarea>


                </div>



                <!-- GOOGLE MAPS -->


                <div
                    class="mb-3"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        <i
                            class="bi bi-geo-alt-fill
                                   text-danger
                                   me-1"
                        ></i>


                        Link Google Maps


                        <span
                            class="text-muted
                                   fw-normal"
                        >


                            (Opsional)


                        </span>


                    </label>



                    <input
                        type="url"
                        name="maps_url"
                        class="form-control"
                        value="<?= clean($maps_url) ?>"
                        placeholder="https://maps.google.com/..."
                    >


                    <div
                        class="form-text"
                    >


                        Buka Google Maps,
                        cari lokasi UMKM,
                        klik <strong>Bagikan</strong>,
                        lalu <strong>Salin link</strong>.


                    </div>


                </div>



                <!-- KOORDINAT -->


                <div
                    class="row
                           g-3
                           mb-3"
                >


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
                            value="<?= clean($latitude) ?>"
                            inputmode="decimal"
                            autocomplete="off"
                            placeholder="Contoh: -6.73292622177123"
                        >


                    </div>



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
                            value="<?= clean($longitude) ?>"
                            inputmode="decimal"
                            autocomplete="off"
                            placeholder="Contoh: 106.79310635054685"
                        >


                    </div>


                </div>



                <!-- INFO LOKASI -->


                <div
                    class="alert
                           alert-primary
                           border-0
                           mb-3"
                >


                    <div
                        class="d-flex
                               gap-2"
                    >


                        <i
                            class="bi bi-info-circle-fill
                                   fs-5"
                        ></i>


                        <div>


                            <div
                                class="fw-semibold"
                            >

                                Lokasi UMKM


                            </div>


                            <div
                                class="small
                                       mt-1"
                            >

                                Link Google Maps dan koordinat
                                tidak wajib. Koordinat digunakan
                                untuk menampilkan lokasi UMKM
                                pada peta interaktif di halaman
                                publik.


                            </div>


                        </div>


                    </div>


                </div>



                <!-- RATING -->


                <div
                    class="alert
                           alert-light
                           border
                           mb-3"
                >


                    <div
                        class="d-flex
                               gap-2"
                    >


                        <i
                            class="bi bi-star-fill
                                   text-warning
                                   fs-5"
                        ></i>


                        <div>


                            <div
                                class="fw-semibold"
                            >

                                Rating Google


                            </div>


                            <div
                                class="small
                                       text-muted
                                       mt-1"
                            >

                                Rating tidak dimasukkan secara
                                manual. Pengunjung dapat melihat
                                rating dan ulasan terbaru melalui
                                Google Maps.


                            </div>


                        </div>


                    </div>


                </div>



                <!-- DESKRIPSI -->


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
                        rows="5"
                        placeholder="Jelaskan secara singkat mengenai usaha ini"
                    ><?= clean($description) ?></textarea>


                </div>



                <!-- FOTO -->


                <div
                    class="mb-4"
                >


                    <label
                        class="form-label
                               fw-semibold"
                    >


                        Foto UMKM


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


                        Format JPG, JPEG, PNG, atau WEBP.
                        Maksimal 5 MB.


                    </div>


                </div>



                <!-- BUTTON -->


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
                            class="bi bi-arrow-left me-1"
                        ></i>


                        Batal


                    </a>



                    <button
                        type="submit"
                        class="btn
                               btn-primary"
                    >


                        <i
                            class="bi bi-save me-1"
                        ></i>


                        Simpan UMKM


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