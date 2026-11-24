<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


if (
    $_SERVER["REQUEST_METHOD"]
    !==
    "POST"
) {

    header(
        "Location: index.php"
    );

    exit;

}


$id =
    isset(
        $_POST["id"]
    )
    ?
    (int)
    $_POST["id"]
    :
    0;


$status =
    strtolower(
        trim(
            $_POST["status"]
            ??
            ""
        )
    );


$allowed_status = [

    "baru",
    "diproses",
    "selesai"

];


if (
    $id <= 0
    ||
    !in_array(
        $status,
        $allowed_status,
        true
    )
) {

    header(
        "Location: index.php?error=id"
    );

    exit;

}


// ======================================================
// UPDATE STATUS
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        UPDATE complaints
        SET status = ?
        WHERE id = ?
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
    "si",
    $status,
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


    header(
        "Location: detail.php?id="
        .
        $id
        .
        "&success=status"
    );


    exit;

}


mysqli_stmt_close(
    $stmt
);


header(
    "Location: detail.php?id="
    .
    $id
    .
    "&error=database"
);

exit;