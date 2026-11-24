<?php

require_once "../auth.php";

require_once "../../config/database.php";

require_once "../../includes/functions.php";


// ======================================================
// HANYA IZINKAN REQUEST POST
// ======================================================

if (
    $_SERVER["REQUEST_METHOD"]
    !==
    "POST"
) {

    http_response_code(
        405
    );

    exit(
        "Metode permintaan tidak diizinkan."
    );

}


// ======================================================
// VALIDASI CSRF
// ======================================================

require_csrf();


// ======================================================
// AMBIL ID DARI POST
// ======================================================

$id =
    isset(
        $_POST["id"]
    )
    &&
    is_numeric(
        $_POST["id"]
    )
    ?
    (int) $_POST["id"]
    :
    0;


if (
    $id <= 0
) {

    header(
        "Location: index.php?error=hapus"
    );

    exit;

}



// ======================================================
// AMBIL FOTO
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            photo
        FROM umkm
        WHERE id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: index.php?error=hapus"
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
        "Location: index.php?error=hapus"
    );

    exit;

}



// ======================================================
// HAPUS DATABASE
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM umkm
        WHERE id = ?
        "
    );


if (
    !$stmt
) {

    header(
        "Location: index.php?error=hapus"
    );

    exit;

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


$success =
    mysqli_stmt_execute(
        $stmt
    );


mysqli_stmt_close(
    $stmt
);



if (
    $success
) {


    // ==================================================
    // HAPUS FOTO
    // ==================================================

    if (
        !empty(
            $data["photo"]
        )
    ) {


        $photo_path =
            "../../assets/uploads/umkm/"
            .
            basename(
                $data["photo"]
            );


        if (
            file_exists(
                $photo_path
            )
        ) {

            @unlink(
                $photo_path
            );

        }

    }



    // ==================================================
    // BUAT TOKEN CSRF BARU
    // ==================================================

    regenerate_csrf_token();



    // ==================================================
    // KEMBALI
    // ==================================================

    header(
        "Location: index.php?success=hapus"
    );

    exit;


} else {


    header(
        "Location: index.php?error=hapus"
    );

    exit;

}