<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// VARIABEL
// ==========================================

$error = "";

$name = "";
$position = "";
$category = "";
$description = "";
$display_order = "0";
$is_active = 1;


// ==========================================
// PROSES FORM
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");

    $position = trim($_POST["position"] ?? "");

    $category = trim($_POST["category"] ?? "");

    $description = trim($_POST["description"] ?? "");

    $display_order = trim($_POST["display_order"] ?? "0");

    $is_active = isset($_POST["is_active"]) ? 1 : 0;


    // ======================================
    // VALIDASI
    // ======================================

    if ($name === "") {

        $error = "Nama perangkat wajib diisi.";

    } elseif ($position === "") {

        $error = "Jabatan wajib diisi.";

    } elseif ($display_order === "" || !is_numeric($display_order)) {

        $error = "Urutan tampilan harus berupa angka.";

    }


    // ======================================
    // UPLOAD FOTO
    // ======================================

    $photo_name = null;

    $upload_directory =
        "../../assets/uploads/pemerintahan/";


    if (
        empty($error)
        &&
        isset($_FILES["photo"])
        &&
        $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        $photo = $_FILES["photo"];


        // ----------------------------------
        // CEK ERROR UPLOAD
        // ----------------------------------

        if ($photo["error"] !== UPLOAD_ERR_OK) {

            $error = "Foto gagal diupload.";

        }


        // ----------------------------------
        // CEK UKURAN
        // ----------------------------------

        if (
            empty($error)
            &&
            $photo["size"] > (5 * 1024 * 1024)
        ) {

            $error = "Ukuran foto maksimal 5 MB.";

        }


        // ----------------------------------
        // CEK EKSTENSI
        // ----------------------------------

        if (empty($error)) {

            $extension =
                strtolower(
                    pathinfo(
                        $photo["name"],
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
                    $allowed_extensions
                )
            ) {

                $error =
                    "Format foto harus JPG, JPEG, PNG, atau WEBP.";

            }

        }


        // ----------------------------------
        // BUAT FOLDER
        // ----------------------------------

        if (
            empty($error)
            &&
            !is_dir($upload_directory)
        ) {

            if (
                !mkdir(
                    $upload_directory,
                    0777,
                    true
                )
            ) {

                $error =
                    "Folder upload foto gagal dibuat.";

            }

        }


        // ----------------------------------
        // SIMPAN FILE
        // ----------------------------------

        if (empty($error)) {

            $photo_name =
                uniqid("official_")
                . "."
                . $extension;


            $upload_path =
                $upload_directory
                . $photo_name;


            if (
                !move_uploaded_file(
                    $photo["tmp_name"],
                    $upload_path
                )
            ) {

                $error =
                    "Gagal menyimpan foto.";

                $photo_name = null;

            }

        }

    }


    // ======================================
    // INSERT DATABASE
    // ======================================

    if (empty($error)) {

        $display_order_int =
            (int) $display_order;


        $sql = "
            INSERT INTO government_officials
            (
                name,
                position,
                category,
                photo,
                description,
                display_order,
                is_active
            )
            VALUES
            (
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


        if (!$stmt) {

            if (
                !empty($photo_name)
            ) {

                @unlink(
                    $upload_directory
                    . $photo_name
                );

            }


            $error =
                "Gagal mempersiapkan database: "
                . mysqli_error($conn);

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "sssssii",
                $name,
                $position,
                $category,
                $photo_name,
                $description,
                $display_order_int,
                $is_active
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {

                mysqli_stmt_close($stmt);


                header(
                    "Location: index.php?success=tambah"
                );

                exit;

            } else {

                if (
                    !empty($photo_name)
                ) {

                    @unlink(
                        $upload_directory
                        . $photo_name
                    );

                }


                $error =
                    "Gagal menyimpan data: "
                    . mysqli_stmt_error($stmt);


                mysqli_stmt_close($stmt);

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
        Tambah Perangkat - Cisalada GO
    </title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- BOOTSTRAP ICON -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="bg-light">


<!-- ==========================================
     NAVBAR
========================================== -->

<nav
    class="navbar
           navbar-dark
           bg-primary"
>

    <div
        class="container"
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



<!-- ==========================================
     CONTENT
========================================== -->

<div
    class="container
           py-4"
>


    <!-- HEADER -->

    <div
        class="d-flex
               justify-content-between
               align-items-center
               flex-wrap
               gap-3
               mb-4"
    >


        <div>

            <h2
                class="fw-bold
                       mb-1"
            >

                Tambah Perangkat Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Tambahkan informasi perangkat
                Pemerintahan Desa Cisalada.

            </p>

        </div>


        <a
            href="index.php"
            class="btn
                   btn-secondary"
        >

            <i
                class="bi bi-arrow-left
                       me-1"
            ></i>

            Kembali

        </a>


    </div>



    <!-- ==========================================
         ERROR
    =========================================== -->

    <?php if (!empty($error)): ?>

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



    <!-- ==========================================
         FORM
    =========================================== -->

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
                autocomplete="off"
            >


                <!-- ==================================
                     NAMA
                =================================== -->

                <div
                    class="mb-3"
                >

                    <label
                        for="name"
                        class="form-label
                               fw-semibold"
                    >

                        Nama Perangkat

                    </label>


                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        placeholder="Contoh: Budi Santoso"
                        value="<?= clean($name) ?>"
                        required
                    >

                </div>



                <!-- ==================================
                     JABATAN
                =================================== -->

                <div
                    class="mb-3"
                >

                    <label
                        for="position"
                        class="form-label
                               fw-semibold"
                    >

                        Jabatan

                    </label>


                    <input
                        type="text"
                        id="position"
                        name="position"
                        class="form-control"
                        placeholder="Contoh: Kepala Desa"
                        value="<?= clean($position) ?>"
                        required
                    >

                </div>



                <!-- ==================================
                     KATEGORI
                =================================== -->

                <div
                    class="mb-3"
                >

                    <label
                        for="category"
                        class="form-label
                               fw-semibold"
                    >

                        Kategori

                    </label>


                    <select
                        name="category"
                        id="category"
                        class="form-select"
                    >


                        <option
                            value=""
                            <?= $category === ""
                                ? "selected"
                                : ""
                            ?>
                        >

                            Pilih Kategori

                        </option>


                        <option
                            value="Pemerintah Desa"
                            <?= $category === "Pemerintah Desa"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Pemerintah Desa

                        </option>


                        <option
                            value="Sekretariat Desa"
                            <?= $category === "Sekretariat Desa"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Sekretariat Desa

                        </option>


                        <option
                            value="Kepala Urusan"
                            <?= $category === "Kepala Urusan"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Kepala Urusan

                        </option>


                        <option
                            value="Kepala Seksi"
                            <?= $category === "Kepala Seksi"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Kepala Seksi

                        </option>


                        <option
                            value="Kepala Dusun"
                            <?= $category === "Kepala Dusun"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Kepala Dusun

                        </option>


                        <option
                            value="Lainnya"
                            <?= $category === "Lainnya"
                                ? "selected"
                                : ""
                            ?>
                        >

                            Lainnya

                        </option>


                    </select>


                    <div
                        class="form-text"
                    >

                        Pilih kategori yang sesuai
                        dengan jabatan perangkat desa.

                    </div>

                </div>



                <!-- ==================================
                     FOTO
                =================================== -->

                <div
                    class="mb-3"
                >

                    <label
                        for="photo"
                        class="form-label
                               fw-semibold"
                    >

                        Foto

                        <span
                            class="text-muted
                                   fw-normal"
                        >

                            (Opsional)

                        </span>

                    </label>


                    <input
                        type="file"
                        id="photo"
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



                <!-- ==================================
                     KETERANGAN
                =================================== -->

                <div
                    class="mb-3"
                >

                    <label
                        for="description"
                        class="form-label
                               fw-semibold"
                    >

                        Keterangan

                    </label>


                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="4"
                        placeholder="Keterangan tambahan mengenai perangkat desa..."
                    ><?= clean($description) ?></textarea>

                </div>



                <!-- ==================================
                     URUTAN
                =================================== -->

                <div
                    class="mb-3"
                >

                    <label
                        for="display_order"
                        class="form-label
                               fw-semibold"
                    >

                        Urutan Tampilan

                    </label>


                    <input
                        type="number"
                        id="display_order"
                        name="display_order"
                        class="form-control"
                        min="0"
                        value="<?= clean($display_order) ?>"
                    >


                    <div
                        class="form-text"
                    >

                        Angka lebih kecil akan tampil lebih dahulu.

                    </div>

                </div>



                <!-- ==================================
                     STATUS
                =================================== -->

                <div
                    class="form-check
                           mb-4"
                >

                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="is_active"
                        name="is_active"
                        value="1"
                        <?= $is_active === 1
                            ? "checked"
                            : ""
                        ?>
                    >


                    <label
                        class="form-check-label"
                        for="is_active"
                    >

                        Tampilkan di website public

                    </label>

                </div>



                <!-- ==================================
                     BUTTON
                =================================== -->

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
                            class="bi bi-x-lg
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

                        Simpan Perangkat

                    </button>


                </div>


            </form>


        </div>

    </div>


</div>


<!-- ==========================================
     BOOTSTRAP JS
========================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>