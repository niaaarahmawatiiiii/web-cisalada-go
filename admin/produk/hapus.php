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
// AMBIL FOTO
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            image
        FROM products
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


$product =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$product
) {

    header(
        "Location: index.php?error=not_found"
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
        DELETE FROM products
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
            $product["image"]
        )
    ) {


        $image_path =
            "../../assets/uploads/products/"
            .
            basename(
                $product["image"]
            );


        if (
            file_exists(
                $image_path
            )
        ) {

            @unlink(
                $image_path
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