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
// AMBIL FILE LAMPIRAN
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            attachment
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


// ======================================================
// DELETE DATABASE
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM complaints
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


    // ==============================================
    // HAPUS FILE
    // ==============================================

    if (
        !empty(
            $data["attachment"]
        )
    ) {


        $file_path =
            "../../assets/uploads/pengaduan/"
            .
            basename(
                $data["attachment"]
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