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
// AMBIL DOCUMENT
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            document
        FROM transparency
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


// ======================================================
// DELETE
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM transparency
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
    // HAPUS PDF
    // ==================================================

    if (
        !empty(
            $data["document"]
        )
    ) {


        $document_path =
            "../../assets/uploads/transparansi/"
            .
            basename(
                $data["document"]
            );


        if (
            file_exists(
                $document_path
            )
        ) {

            @unlink(
                $document_path
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