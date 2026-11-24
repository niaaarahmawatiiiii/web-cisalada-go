<?php

require_once __DIR__ . "/../config/database.php";

?>

<!DOCTYPE html>

<html lang="id">

<head>


    <!-- ==================================================
         META
    =================================================== -->

    <meta
        charset="UTF-8"
    >


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >



    <!-- ==================================================
         TITLE
    =================================================== -->

    <title>

        <?= isset($page_title)
            ? $page_title . " - Cisalada GO"
            : "Cisalada GO"
        ?>

    </title>



    <!-- ==================================================
         DESCRIPTION
    =================================================== -->

    <meta
        name="description"
        content="Website informasi Desa Cisalada"
    >



    <!-- ==================================================
         FAVICON
    =================================================== -->

    <link
        rel="icon"
        type="image/png"
        sizes="512x512"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >


    <link
        rel="shortcut icon"
        type="image/png"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >


    <link
        rel="apple-touch-icon"
        href="<?= BASE_URL ?>assets/images/favicon.png"
    >



    <!-- ==================================================
         BOOTSTRAP
    =================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >



    <!-- ==================================================
         BOOTSTRAP ICONS
    =================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >



    <!-- ==================================================
         CSS WEBSITE
    =================================================== -->

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/css/style.css"
    >

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| GLOBAL PAGE LOADER
|--------------------------------------------------------------------------
*/

include
    __DIR__
    .
    "/loader.php";

?>