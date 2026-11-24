<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// CEK ID
// ==========================================

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


// ==========================================
// AMBIL DATA
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            *
        FROM government_officials
        WHERE id = ?
        LIMIT 1
        "
    );


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


$data =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$data
) {

    header(
        "Location: index.php"
    );

    exit;

}


// ==========================================
// ERROR
// ==========================================

$error = "";


// ==========================================
// PROSES UPDATE
// ==========================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $name =
        trim(
            $_POST["name"]
            ??
            ""
        );


    $position =
        trim(
            $_POST["position"]
            ??
            ""
        );


    $category =
        trim(
            $_POST["category"]
            ??
            ""
        );


    $description =
        trim(
            $_POST["description"]
            ??
            ""
        );


    $display_order =
        trim(
            $_POST["display_order"]
            ??
            "0"
        );


    $is_active =
        isset(
            $_POST["is_active"]
        )
        ?
        1
        :
        0;


    // ======================================
    // VALIDASI
    // ======================================

    if (
        empty($name)
    ) {

        $error =
            "Nama perangkat wajib diisi.";

    } elseif (
        empty($position)
    ) {

        $error =
            "Jabatan wajib diisi.";

    } elseif (
        !is_numeric(
            $display_order
        )
    ) {

        $error =
            "Urutan tampilan harus berupa angka.";

    }


    // ======================================
    // FILE
    // ======================================

    $old_photo =
        $data["photo"];


    $new_photo =
        $old_photo;


    $new_upload_path =
        null;


    // ======================================
    // UPLOAD FOTO BARU
    // ======================================

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


        $photo =
            $_FILES["photo"];


        if (
            $photo["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "Foto gagal diupload.";

        }


        if (
            empty($error)
            &&
            $photo["size"]
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


        $upload_directory =
            "../../assets/uploads/pemerintahan/";


        if (
            empty($error)
            &&
            !is_dir(
                $upload_directory
            )
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


        if (
            empty($error)
        ) {


            $new_photo =
                uniqid(
                    "official_"
                )
                .
                "."
                .
                $extension;


            $new_upload_path =
                $upload_directory
                .
                $new_photo;


            if (
                !move_uploaded_file(
                    $photo["tmp_name"],
                    $new_upload_path
                )
            ) {

                $error =
                    "Gagal menyimpan foto baru.";

                $new_photo =
                    $old_photo;

                $new_upload_path =
                    null;

            }

        }

    }


    // ======================================
    // UPDATE DATABASE
    // ======================================

    if (
        empty($error)
    ) {


        $display_order_int =
            (int)
            $display_order;


        $stmt =
            mysqli_prepare(
                $conn,
                "
                UPDATE government_officials

                SET
                    name = ?,
                    position = ?,
                    category = ?,
                    photo = ?,
                    description = ?,
                    display_order = ?,
                    is_active = ?

                WHERE id = ?
                "
            );


        mysqli_stmt_bind_param(
            $stmt,
            "sssssiii",
            $name,
            $position,
            $category,
            $new_photo,
            $description,
            $display_order_int,
            $is_active,
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


            // ==============================
            // HAPUS FOTO LAMA
            // ==============================

            if (
                $new_upload_path
                !==
                null
                &&
                !empty($old_photo)
                &&
                $old_photo
                !==
                $new_photo
            ) {


                $old_path =
                    "../../assets/uploads/pemerintahan/"
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


            if (
                $new_upload_path
                !==
                null
            ) {

                @unlink(
                    $new_upload_path
                );

            }


            $error =
                "Gagal memperbarui data: "
                .
                mysqli_stmt_error(
                    $stmt
                );


            mysqli_stmt_close(
                $stmt
            );

        }

    }


    // ======================================
    // SIMPAN INPUT
    // ======================================

    $data["name"] =
        $name;


    $data["position"] =
        $position;


    $data["category"] =
        $category;


    $data["description"] =
        $description;


    $data["display_order"] =
        $display_order;


    $data["is_active"] =
        $is_active;

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
        Edit Perangkat - Cisalada GO
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


<body class="bg-light">


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
        >

            <i
                class="bi bi-box-arrow-right
                       me-1"
            ></i>

            Logout

        </a>

    </div>

</nav>



<div
    class="container
           py-4"
>


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

                Edit Perangkat Desa

            </h2>


            <p
                class="text-muted
                       mb-0"
            >

                Perbarui informasi perangkat
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



    <?php if (
        !empty($error)
    ): ?>

        <div
            class="alert
                   alert-danger"
        >

            <?= clean($error) ?>

        </div>

    <?php endif; ?>



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

                        Nama Perangkat

                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= clean(
                            $data["name"]
                        ) ?>"
                        required
                    >

                </div>



                <!-- JABATAN -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Jabatan

                    </label>


                    <input
                        type="text"
                        name="position"
                        class="form-control"
                        value="<?= clean(
                            $data["position"]
                        ) ?>"
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

                            Pilih Kategori

                        </option>


                        <?php

                        $categories = [

                            "Pemerintah Desa",
                            "Sekretariat Desa",
                            "Kepala Urusan",
                            "Kepala Seksi",
                            "Kepala Dusun",
                            "Lainnya"

                        ];

                        ?>


                        <?php foreach (
                            $categories
                            as
                            $category
                        ): ?>


                            <option
                                value="<?= clean($category) ?>"
                                <?= (
                                    $data[
                                        "category"
                                    ]
                                    ===
                                    $category
                                )
                                    ? "selected"
                                    : ""
                                ?>
                            >

                                <?= clean(
                                    $category
                                ) ?>

                            </option>


                        <?php endforeach; ?>


                    </select>

                </div>



                <!-- FOTO -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Ganti Foto

                        <span
                            class="text-muted
                                   fw-normal"
                        >

                            (opsional)

                        </span>

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

                        Kosongkan jika ingin
                        mempertahankan foto lama.

                    </div>

                </div>



                <!-- FOTO LAMA -->

                <?php if (
                    !empty(
                        $data["photo"]
                    )
                ): ?>


                    <div
                        class="mb-4"
                    >

                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Foto Saat Ini

                        </label>


                        <br>


                        <img
                            src="<?= BASE_URL ?>assets/uploads/pemerintahan/<?= clean($data["photo"]) ?>"
                            alt="<?= clean($data["name"]) ?>"
                            style="
                                width: 150px;
                                height: 150px;
                                object-fit: cover;
                                border-radius: 14px;
                            "
                        >

                    </div>


                <?php endif; ?>



                <!-- KETERANGAN -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Keterangan

                    </label>


                    <textarea
                        name="description"
                        class="form-control"
                        rows="4"
                    ><?= clean(
                        $data["description"]
                    ) ?></textarea>

                </div>



                <!-- URUTAN -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Urutan Tampilan

                    </label>


                    <input
                        type="number"
                        name="display_order"
                        class="form-control"
                        min="0"
                        value="<?= (int)
                            $data[
                                "display_order"
                            ] ?>"
                    >

                </div>



                <!-- STATUS -->

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
                        <?= (
                            (int)
                            $data[
                                "is_active"
                            ]
                            ===
                            1
                        )
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


</body>

</html>