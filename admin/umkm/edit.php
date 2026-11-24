<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


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
        "Location: index.php"
    );

    exit;

}


// ======================================================
// AMBIL DATA
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
            maps_url,
            latitude,
            longitude
        FROM umkm
        WHERE id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: index.php"
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
        "Location: index.php"
    );

    exit;

}


$error = "";


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


$latitude =
    $umkm["latitude"]
    ??
    "";


$longitude =
    $umkm["longitude"]
    ??
    "";


$old_photo =
    $umkm["photo"]
    ??
    "";


// ======================================================
// PROSES UPDATE
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
    // VALIDASI LINK GOOGLE MAPS
    // ==================================================

    if (
        !empty($maps_url)
        &&
        !filter_var(
            $maps_url,
            FILTER_VALIDATE_URL
        )
    ) {

        $error =
            "Link Google Maps tidak valid.";

    }



    // ==================================================
    // VALIDASI NAMA
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
            !is_numeric(
                $longitude
            )
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
    // FOTO BARU
    // ==================================================

    $new_photo =
        $old_photo;


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
    // UPDATE DATABASE
    // ==================================================

    if (
        empty($error)
    ) {


        $sql = "
            UPDATE umkm

            SET
                name = ?,
                owner = ?,
                category = ?,
                description = ?,
                photo = ?,
                phone = ?,
                address = ?,
                maps_url = ?,
                latitude = ?,
                longitude = ?

            WHERE id = ?
        ";


        $stmt =
            mysqli_prepare(
                $conn,
                $sql
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
                "ssssssssssi",
                $name,
                $owner,
                $category,
                $description,
                $new_photo,
                $phone,
                $address,
                $maps_url,
                $latitude_value,
                $longitude_value,
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
                    $new_photo !== $old_photo
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
                    "Gagal memperbarui data UMKM: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                // ==========================================
                // HAPUS FOTO BARU JIKA UPDATE GAGAL
                // ==========================================

                if (
                    $new_photo !== $old_photo
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

<html lang="id">

<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>

        Edit UMKM - Cisalada GO

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


            Edit UMKM


        </h2>



        <p
            class="text-muted
                   mb-0"
        >


            Perbarui informasi UMKM
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

                        Kosongkan jika lokasi Google Maps
                        belum tersedia.


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

                                Koordinat digunakan untuk
                                menampilkan lokasi UMKM
                                pada peta interaktif publik.


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

                                Rating tidak disimpan sebagai
                                input manual. Pengunjung dapat
                                melihat rating dan ulasan terbaru
                                langsung melalui Google Maps.


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
                    ><?= clean($description) ?></textarea>


                </div>



                <!-- FOTO LAMA -->


                <?php if (
                    !empty($old_photo)
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
                                src="<?= BASE_URL ?>assets/uploads/umkm/<?= rawurlencode(basename($old_photo)) ?>"
                                alt="Foto UMKM"
                                style="
                                    width: 180px;
                                    height: 130px;
                                    object-fit: cover;
                                    border-radius: 12px;
                                "
                            >


                        </div>


                    </div>


                <?php endif; ?>



                <!-- FOTO BARU -->


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