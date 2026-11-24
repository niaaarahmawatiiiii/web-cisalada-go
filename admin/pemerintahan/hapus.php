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
// AMBIL FOTO
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            photo
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
// HAPUS DATABASE
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM government_officials
        WHERE id = ?
        "
    );


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


// ==========================================
// HAPUS FOTO
// ==========================================

if (
    $success
    &&
    !empty(
        $data["photo"]
    )
) {


    $photo_path =
        "../../assets/uploads/pemerintahan/"
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


// ==========================================
// REDIRECT
// ==========================================

header(
    "Location: index.php?success=hapus"
);

exit;