<?php

require_once "../auth.php";

require_once "../../config/database.php";


// ==========================================
// CEK ID
// ==========================================

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {

    header(
        "Location: index.php"
    );

    exit;
}


$id =
    (int) $_GET["id"];


// ==========================================
// HAPUS DATA
// ==========================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        DELETE FROM announcements
        WHERE id = ?
        "
    );


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute($stmt);


// ==========================================
// KEMBALI
// ==========================================

header(
    "Location: index.php?success=hapus"
);

exit;