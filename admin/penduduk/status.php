<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";


// =========================================================
// HANYA POST
// =========================================================

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


// =========================================================
// AMBIL DATA
// =========================================================

$id =
    isset(
        $_POST["id"]
    )
    ?
    (int)
    $_POST["id"]
    :
    0;


$new_status =
    trim(
        $_POST["population_status"]
        ??
        ""
    );


$event_date =
    trim(
        $_POST["event_date"]
        ??
        ""
    );


$notes =
    trim(
        $_POST["notes"]
        ??
        ""
    );


// =========================================================
// VALIDASI
// =========================================================

$allowed_status = [

    "Aktif",
    "Pindah Keluar",
    "Meninggal"

];


if (
    $id <= 0
) {

    header(
        "Location: index.php?error=id"
    );

    exit;

}


if (
    !in_array(
        $new_status,
        $allowed_status,
        true
    )
) {

    header(
        "Location: detail.php?id="
        .
        $id
        .
        "&error=status"
    );

    exit;

}


// =========================================================
// AMBIL DATA LAMA
// =========================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            population_status,
            entry_date,
            exit_date,
            notes
        FROM residents
        WHERE id = ?
        LIMIT 1
        "
    );


if (
    !$stmt
) {

    header(
        "Location: detail.php?id="
        .
        $id
        .
        "&error=database"
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


$resident =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$resident
) {

    header(
        "Location: index.php?error=not_found"
    );

    exit;

}


// =========================================================
// VALIDASI PERUBAHAN STATUS
// =========================================================

$old_status =
    $resident[
        "population_status"
    ];


if (
    $old_status
    ===
    $new_status
    &&
    $event_date === ""
    &&
    $notes === ""
) {

    header(
        "Location: detail.php?id="
        .
        $id
    );

    exit;

}


// =========================================================
// TANGGAL EVENT
// =========================================================

if (
    $event_date !== ""
) {


    $date_object =
        DateTime::createFromFormat(
            "Y-m-d",
            $event_date
        );


    if (
        !$date_object
        ||
        $date_object->format(
            "Y-m-d"
        )
        !==
        $event_date
    ) {


        header(
            "Location: detail.php?id="
            .
            $id
            .
            "&error=date"
        );


        exit;

    }

} else {


    $event_date =
        date(
            "Y-m-d"
        );

}


// =========================================================
// TENTUKAN TANGGAL MASUK / KELUAR
// =========================================================

$entry_date =
    $resident[
        "entry_date"
    ]
    ??
    null;


$exit_date =
    $resident[
        "exit_date"
    ]
    ??
    null;


// Jika menjadi Aktif dan tanggal event diberikan,
// tanggal tersebut menjadi tanggal masuk jika
// sebelumnya belum tersedia.

if (
    $new_status
    ===
    "Aktif"
) {


    if (
        empty(
            $entry_date
        )
    ) {

        $entry_date =
            $event_date;

    }


    $exit_date =
        null;

}


// Jika Pindah Keluar atau Meninggal,
// tanggal event menjadi tanggal keluar.

if (
    $new_status
    ===
    "Pindah Keluar"
    ||
    $new_status
    ===
    "Meninggal"
) {


    $exit_date =
        $event_date;

}


// =========================================================
// CATATAN
// =========================================================

$new_notes =
    trim(
        $notes
    );


if (
    $new_notes === ""
) {

    $new_notes =
        $resident[
            "notes"
        ]
        ??
        null;

}


// =========================================================
// OLD VALUE
// =========================================================

$old_value =
    json_encode(
        [
            "population_status" =>
                $resident[
                    "population_status"
                ],

            "entry_date" =>
                $resident[
                    "entry_date"
                ],

            "exit_date" =>
                $resident[
                    "exit_date"
                ],

            "notes" =>
                $resident[
                    "notes"
                ]

        ],
        JSON_UNESCAPED_UNICODE
    );


// =========================================================
// NEW VALUE
// =========================================================

$new_value =
    json_encode(
        [
            "population_status" =>
                $new_status,

            "entry_date" =>
                $entry_date,

            "exit_date" =>
                $exit_date,

            "notes" =>
                $new_notes

        ],
        JSON_UNESCAPED_UNICODE
    );


// =========================================================
// CHANGE TYPE
// =========================================================

$change_type =
    "Edit Data";


if (
    $old_status !==
    $new_status
) {


    if (
        $new_status
        ===
        "Pindah Keluar"
    ) {

        $change_type =
            "Pindah Keluar";

    } elseif (
        $new_status
        ===
        "Meninggal"
    ) {

        $change_type =
            "Meninggal";

    } elseif (
        $new_status
        ===
        "Aktif"
    ) {


        if (
            $old_status
            ===
            "Pindah Keluar"
            ||
            $old_status
            ===
            "Meninggal"
        ) {

            $change_type =
                "Pindah Masuk";

        }

    }

}


// =========================================================
// DESCRIPTION
// =========================================================

$description =
    "Status warga "
    .
    $resident["name"]
    .
    " diubah dari \""
    .
    $old_status
    .
    "\" menjadi \""
    .
    $new_status
    .
    "\".";


if (
    !empty($notes)
) {

    $description .=
        " Catatan: "
        .
        $notes;

}


// =========================================================
// UPDATE RESIDENT
// =========================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        UPDATE residents

        SET
            population_status = ?,
            entry_date = ?,
            exit_date = ?,
            notes = ?

        WHERE
            id = ?
        "
    );


if (
    !$stmt
) {

    header(
        "Location: detail.php?id="
        .
        $id
        .
        "&error=database"
    );

    exit;

}


mysqli_stmt_bind_param(
    $stmt,
    "ssssi",
    $new_status,
    $entry_date,
    $exit_date,
    $new_notes,
    $id
);


if (
    !mysqli_stmt_execute(
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
        "&error=database"
    );


    exit;

}


mysqli_stmt_close(
    $stmt
);


// =========================================================
// CATAT HISTORY
// =========================================================

$history =
    mysqli_prepare(
        $conn,
        "
        INSERT INTO resident_history
        (
            resident_id,
            change_type,
            old_value,
            new_value,
            description
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
        "
    );


if (
    $history
) {


    mysqli_stmt_bind_param(
        $history,
        "issss",
        $id,
        $change_type,
        $old_value,
        $new_value,
        $description
    );


    mysqli_stmt_execute(
        $history
    );


    mysqli_stmt_close(
        $history
    );

}


// =========================================================
// REDIRECT
// =========================================================

header(
    "Location: detail.php?id="
    .
    $id
    .
    "&success=status"
);


exit;