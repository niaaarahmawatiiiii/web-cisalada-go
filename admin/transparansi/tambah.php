<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$error = "";


$title = "";

$category = "";

$year = "";

$income = "";

$expenditure = "";

$financing = "";

$description = "";


$upload_directory =
    "../../assets/uploads/transparansi/";


// ======================================================
// PROSES TAMBAH
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $title =
        trim(
            $_POST["title"]
            ??
            ""
        );


    $category =
        trim(
            $_POST["category"]
            ??
            ""
        );


    $year =
        trim(
            $_POST["year"]
            ??
            ""
        );


    $income =
        trim(
            $_POST["income"]
            ??
            ""
        );


    $expenditure =
        trim(
            $_POST["expenditure"]
            ??
            ""
        );


    $financing =
        trim(
            $_POST["financing"]
            ??
            ""
        );


    $description =
        trim(
            $_POST["description"]
            ??
            ""
        );



    // ==================================================
    // VALIDASI JUDUL
    // ==================================================

    if (
        $title === ""
    ) {

        $error =
            "Judul transparansi wajib diisi.";

    }



    // ==================================================
    // VALIDASI TAHUN
    // ==================================================

    if (
        empty($error)
        &&
        $year !== ""
    ) {


        if (
            !ctype_digit(
                $year
            )
        ) {

            $error =
                "Tahun harus berupa angka.";

        } elseif (
            (int) $year < 2000
            ||
            (int) $year > 2100
        ) {

            $error =
                "Tahun harus berada antara 2000 sampai 2100.";

        }

    }



    // ==================================================
    // FUNGSI VALIDASI NOMINAL
    // ==================================================

    function parse_amount(
        $value
    ) {


        if (
            $value === ""
        ) {

            return 0;

        }


        /*
         * Mengizinkan input:
         *
         * 1000000
         * 1.000.000
         * 1,000,000
         */

        $value =
            str_replace(
                ".",
                "",
                $value
            );


        $value =
            str_replace(
                ",",
                "",
                $value
            );


        if (
            !is_numeric(
                $value
            )
        ) {

            return false;

        }


        if (
            (float) $value < 0
        ) {

            return false;

        }


        return (float) $value;

    }



    $income_value =
        parse_amount(
            $income
        );


    $expenditure_value =
        parse_amount(
            $expenditure
        );


    $financing_value =
        parse_amount(
            $financing
        );



    if (
        empty($error)
        &&
        $income_value === false
    ) {

        $error =
            "Pendapatan harus berupa angka.";

    }


    if (
        empty($error)
        &&
        $expenditure_value === false
    ) {

        $error =
            "Belanja harus berupa angka.";

    }


    if (
        empty($error)
        &&
        $financing_value === false
    ) {

        $error =
            "Pembiayaan harus berupa angka.";

    }



    // ==================================================
    // UPLOAD PDF
    // ==================================================

    $document_name =
        "";


    if (
        empty($error)
        &&
        isset(
            $_FILES["document"]
        )
        &&
        $_FILES["document"]["error"]
        !==
        UPLOAD_ERR_NO_FILE
    ) {


        $file =
            $_FILES["document"];


        if (
            $file["error"]
            !==
            UPLOAD_ERR_OK
        ) {

            $error =
                "Dokumen gagal diupload.";

        }


        if (
            empty($error)
            &&
            $file["size"]
            >
            10 * 1024 * 1024
        ) {

            $error =
                "Ukuran PDF maksimal 10 MB.";

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


            if (
                $extension
                !==
                "pdf"
            ) {

                $error =
                    "Dokumen harus berupa file PDF.";

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


            if (
                $mime
                !==
                "application/pdf"
            ) {

                $error =
                    "File yang diupload bukan PDF yang valid.";

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


            $document_name =
                "transparansi_"
                .
                date("YmdHis")
                .
                "_"
                .
                uniqid()
                .
                ".pdf";


            if (
                !move_uploaded_file(
                    $file["tmp_name"],
                    $upload_directory
                    .
                    $document_name
                )
            ) {

                $error =
                    "Dokumen gagal disimpan.";

                $document_name =
                    "";

            }

        }

    }



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
                INSERT INTO transparency
                (
                    title,
                    category,
                    year,
                    income,
                    expenditure,
                    financing,
                    description,
                    document
                )
                VALUES
                (
                    ?,
                    ?,
                    NULLIF(?, ''),
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
                    $document_name
                )
            ) {

                @unlink(
                    $upload_directory
                    .
                    $document_name
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
                "sssddds",
                $title,
                $category,
                $year,
                $income_value,
                $expenditure_value,
                $financing_value,
                $description,
                $document_name
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
                    "Gagal menyimpan data: "
                    .
                    mysqli_stmt_error(
                        $stmt
                    );


                mysqli_stmt_close(
                    $stmt
                );


                if (
                    !empty(
                        $document_name
                    )
                ) {

                    @unlink(
                        $upload_directory
                        .
                        $document_name
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
        Tambah Transparansi - Cisalada GO
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

    </style>

</head>

<body>


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

            Kembali ke Transparansi

        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Tambah Transparansi

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Tambahkan informasi APBDes,
            pembangunan, atau dokumen publik desa.

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


                <!-- JUDUL -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Judul

                        <span
                            class="text-danger"
                        >
                            *
                        </span>

                    </label>


                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?= clean(
                            $title
                        ) ?>"
                        placeholder="Contoh: APBDes Desa Cisalada Tahun 2026"
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
                            value="APBDes"
                            <?= $category === "APBDes"
                                ? "selected"
                                : "" ?>
                        >

                            APBDes

                        </option>


                        <option
                            value="Realisasi APBDes"
                            <?= $category === "Realisasi APBDes"
                                ? "selected"
                                : "" ?>
                        >

                            Realisasi APBDes

                        </option>


                        <option
                            value="Pembangunan"
                            <?= $category === "Pembangunan"
                                ? "selected"
                                : "" ?>
                        >

                            Pembangunan Desa

                        </option>


                        <option
                            value="Peraturan Desa"
                            <?= $category === "Peraturan Desa"
                                ? "selected"
                                : "" ?>
                        >

                            Peraturan Desa

                        </option>


                        <option
                            value="Dokumen Publik"
                            <?= $category === "Dokumen Publik"
                                ? "selected"
                                : "" ?>
                        >

                            Dokumen Publik

                        </option>


                        <option
                            value="IDM"
                            <?= $category === "IDM"
                                ? "selected"
                                : "" ?>
                        >

                            IDM / Indeks Desa

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



                <!-- TAHUN -->

                <div
                    class="mb-3"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Tahun

                    </label>


                    <input
                        type="number"
                        name="year"
                        class="form-control"
                        value="<?= clean(
                            $year
                        ) ?>"
                        min="2000"
                        max="2100"
                        placeholder="2026"
                    >

                </div>



                <!-- KEUANGAN -->

                <div
                    class="row
                           g-3
                           mb-3"
                >


                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Pendapatan

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
                                type="text"
                                name="income"
                                class="form-control"
                                value="<?= clean(
                                    $income
                                ) ?>"
                                placeholder="0"
                            >

                        </div>

                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Belanja

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
                                type="text"
                                name="expenditure"
                                class="form-control"
                                value="<?= clean(
                                    $expenditure
                                ) ?>"
                                placeholder="0"
                            >

                        </div>

                    </div>



                    <div
                        class="col-md-4"
                    >

                        <label
                            class="form-label
                                   fw-semibold"
                        >

                            Pembiayaan

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
                                type="text"
                                name="financing"
                                class="form-control"
                                value="<?= clean(
                                    $financing
                                ) ?>"
                                placeholder="0"
                            >

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
                        rows="6"
                        placeholder="Tambahkan penjelasan mengenai data transparansi."
                    ><?= clean(
                        $description
                    ) ?></textarea>

                </div>



                <!-- DOCUMENT -->

                <div
                    class="mb-4"
                >

                    <label
                        class="form-label
                               fw-semibold"
                    >

                        Dokumen PDF

                    </label>


                    <input
                        type="file"
                        name="document"
                        class="form-control"
                        accept=".pdf,application/pdf"
                    >


                    <div
                        class="form-text"
                    >

                        Opsional. Format PDF,
                        maksimal 10 MB.

                    </div>

                </div>



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

                        Simpan Transparansi

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