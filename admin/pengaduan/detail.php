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
// AMBIL DATA
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            contact,
            category,
            title,
            complaint,
            attachment,
            status,
            created_at
        FROM complaints
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
        "Location: index.php?error=not_found"
    );

    exit;

}


$status =
    strtolower(
        trim(
            $data["status"]
            ??
            "baru"
        )
    );

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
        Detail Pengaduan - Cisalada GO
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


        .detail-card {

            border:
                0;

            border-radius:
                16px;

        }


        .detail-label {

            font-size:
                0.82rem;

            color:
                #6c757d;

            margin-bottom:
                5px;

        }


        .detail-value {

            font-weight:
                500;

        }


        .complaint-box {

            background:
                #f8f9fa;

            border-radius:
                12px;

            padding:
                18px;

            white-space:
                pre-line;

            line-height:
                1.7;

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


    <!-- HEADER -->


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


            Kembali ke Pengaduan


        </a>


        <h2
            class="fw-bold
                   mt-3
                   mb-1"
        >

            Detail Pengaduan

        </h2>


        <p
            class="text-muted
                   mb-0"
        >

            Informasi lengkap pengaduan warga.

        </p>


    </div>



    <div
        class="row
               g-4"
    >


        <!-- ==================================================
             DATA
        =================================================== -->

        <div
            class="col-lg-8"
        >


            <div
                class="card
                       detail-card
                       shadow-sm
                       h-100"
            >


                <div
                    class="card-body
                           p-4"
                >


                    <div
                        class="mb-4"
                    >


                        <div
                            class="detail-label"
                        >

                            Judul Pengaduan

                        </div>


                        <h4
                            class="fw-bold
                                   mb-0"
                        >

                            <?= clean(
                                $data["title"]
                                ??
                                "-"
                            ) ?>


                        </h4>


                    </div>



                    <div
                        class="row
                               g-4
                               mb-4"
                    >


                        <div
                            class="col-md-6"
                        >


                            <div
                                class="detail-label"
                            >

                                Nama

                            </div>


                            <div
                                class="detail-value"
                            >

                                <?= clean(
                                    $data["name"]
                                ) ?>


                            </div>


                        </div>



                        <div
                            class="col-md-6"
                        >


                            <div
                                class="detail-label"
                            >

                                Kontak

                            </div>


                            <div
                                class="detail-value"
                            >

                                <?php if (
                                    !empty(
                                        $data["contact"]
                                    )
                                ): ?>


                                    <?= clean(
                                        $data["contact"]
                                    ) ?>


                                <?php else: ?>


                                    Tidak dicantumkan


                                <?php endif; ?>


                            </div>


                        </div>



                        <div
                            class="col-md-6"
                        >


                            <div
                                class="detail-label"
                            >

                                Kategori

                            </div>


                            <div
                                class="detail-value"
                            >

                                <?= !empty(
                                    $data["category"]
                                )
                                    ?
                                    clean(
                                        $data["category"]
                                    )
                                    :
                                    "-"
                                ?>


                            </div>


                        </div>



                        <div
                            class="col-md-6"
                        >


                            <div
                                class="detail-label"
                            >

                                Tanggal

                            </div>


                            <div
                                class="detail-value"
                            >

                                <?= date(
                                    "d M Y H:i",
                                    strtotime(
                                        $data["created_at"]
                                    )
                                ) ?>


                            </div>


                        </div>


                    </div>



                    <div
                        class="mb-4"
                    >


                        <div
                            class="detail-label"
                        >

                            Isi Pengaduan

                        </div>


                        <div
                            class="complaint-box"
                        >

                            <?= clean(
                                $data["complaint"]
                            ) ?>


                        </div>


                    </div>



                    <?php if (
                        !empty(
                            $data["attachment"]
                        )
                    ): ?>


                        <div
                            class="mb-2"
                        >


                            <div
                                class="detail-label"
                            >

                                Lampiran

                            </div>


                            <a
                                href="<?= BASE_URL ?>assets/uploads/pengaduan/<?= rawurlencode(
                                    basename(
                                        $data["attachment"]
                                    )
                                ) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn
                                       btn-outline-primary
                                       btn-sm"
                            >

                                <i
                                    class="bi bi-paperclip
                                           me-1"
                                ></i>


                                Lihat Lampiran


                            </a>


                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </div>



        <!-- ==================================================
             STATUS
        =================================================== -->

        <div
            class="col-lg-4"
        >


            <div
                class="card
                       detail-card
                       shadow-sm"
            >


                <div
                    class="card-body
                           p-4"
                >


                    <h5
                        class="fw-bold
                               mb-4"
                    >

                        Status Pengaduan

                    </h5>


                    <form
                        action="status.php"
                        method="POST"
                    >


                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $data["id"] ?>"
                        >


                        <select
                            name="status"
                            class="form-select
                                   mb-3"
                            required
                        >


                            <option
                                value="baru"
                                <?= $status === "baru"
                                    ? "selected"
                                    : "" ?>
                            >

                                Baru

                            </option>


                            <option
                                value="diproses"
                                <?= $status === "diproses"
                                    ? "selected"
                                    : "" ?>
                            >

                                Diproses

                            </option>


                            <option
                                value="selesai"
                                <?= $status === "selesai"
                                    ? "selected"
                                    : "" ?>
                            >

                                Selesai

                            </option>


                        </select>


                        <button
                            type="submit"
                            class="btn
                                   btn-primary
                                   w-100"
                        >

                            <i
                                class="bi bi-save
                                       me-1"
                            ></i>


                            Simpan Status

                        </button>


                    </form>


                    <hr
                        class="my-4"
                    >


                    <a
                        href="hapus.php?id=<?= (int) $data["id"] ?>"
                        class="btn
                               btn-outline-danger
                               w-100"
                        onclick="
                            return confirm(
                                'Yakin ingin menghapus pengaduan ini?'
                            );
                        "
                    >

                        <i
                            class="bi bi-trash
                                   me-1"
                        ></i>


                        Hapus Pengaduan

                    </a>


                </div>


            </div>


        </div>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>