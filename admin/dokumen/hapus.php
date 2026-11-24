<?php

require_once "../auth.php";
require_once "../../config/database.php";


// ======================================================
// CEK ID
// ======================================================

if (
    !isset($_GET["id"])
    ||
    !is_numeric($_GET["id"])
) {

    header(
        "Location: index.php"
    );

    exit;

}


$id =
    (int) $_GET["id"];


// ======================================================
// AMBIL FILE
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT file
        FROM documents
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


$document =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$document
) {

    header(
        "Location: index.php"
    );

    exit;

}


// ======================================================
// DELETE DATABASE
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM documents
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


// ======================================================
// DELETE FILE
// ======================================================

if (
    $success
    &&
    !empty(
        $document["file"]
    )
) {


    $file_path =
        "../../assets/uploads/dokumen/"
        .
        basename(
            $document["file"]
        );


    if (
        file_exists(
            $file_path
        )
    ) {

        @unlink(
            $file_path
        );

    }

}


// ======================================================
// REDIRECT
// ======================================================

if (
    $success
) {

    header(
        "Location: index.php?success=hapus"
    );

} else {

    header(
        "Location: index.php?error=hapus"
    );

}

exit;