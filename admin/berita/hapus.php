<?php

require_once "../auth.php";

require_once "../../config/database.php";
require_once "../../includes/functions.php";


// ==========================================
// HANYA POST
// ==========================================

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


// ==========================================
// VALIDASI CSRF
// ==========================================

require_csrf();


// ==========================================
// CEK ID
// ==========================================

if (
    !isset(
        $_POST["id"]
    )
    ||
    !is_numeric(
        $_POST["id"]
    )
) {

    header(
        "Location: index.php"
    );

    exit;

}


$id =
    (int) $_POST["id"];



// ==========================================
// AMBIL GAMBAR
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "SELECT image FROM news WHERE id = ?"
    );


if (
    !$stmt
) {

    die(
        "Gagal menyiapkan query."
    );

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


$news =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);



if (
    !$news
) {

    header(
        "Location: index.php"
    );

    exit;

}



// ==========================================
// HAPUS GAMBAR
// ==========================================

if (
    !empty(
        $news["image"]
    )
) {


    $file =
        "../../assets/uploads/berita/"
        .
        $news["image"];


    if (
        file_exists(
            $file
        )
    ) {

        unlink(
            $file
        );

    }

}



// ==========================================
// HAPUS DATABASE
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "DELETE FROM news WHERE id = ?"
    );


if (
    !$stmt
) {

    die(
        "Gagal menyiapkan query."
    );

}


mysqli_stmt_bind_param(
    $stmt,
    "i",
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


    // Ganti token setelah operasi berhasil

    regenerate_csrf_token();


    header(
        "Location: index.php?success=hapus"
    );

    exit;


} else {


    mysqli_stmt_close(
        $stmt
    );


    die(
        "Gagal menghapus berita."
    );

}