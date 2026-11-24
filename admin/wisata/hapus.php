<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


$id =
    isset($_GET["id"])
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
// AMBIL FOTO
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            photo
        FROM tourism
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


$wisata =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$wisata
) {

    header(
        "Location: index.php?error=not_found"
    );

    exit;

}


// ======================================================
// HAPUS DATA
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM tourism
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


    // ==================================================
    // HAPUS FOTO
    // ==================================================

    if (
        !empty(
            $wisata["photo"]
        )
    ) {


        $photo_path =
            "../../assets/uploads/wisata/"
            .
            basename(
                $wisata["photo"]
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


    header(
        "Location: index.php?success=hapus"
    );


    exit;

}


mysqli_stmt_close(
    $stmt
);


header(
    "Location: index.php?error=database"
);


exit;