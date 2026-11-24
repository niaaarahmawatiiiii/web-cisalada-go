<?php

// ==========================================================
// CISALADA GO
// ADMIN LOGOUT
// ==========================================================


// ==========================================================
// SESSION
// ==========================================================
//
// Session harus dibuka terlebih dahulu supaya seluruh
// data session dan cookie dapat dihancurkan dengan benar.
// ==========================================================

if (
    session_status()
    ===
    PHP_SESSION_NONE
) {

    // ------------------------------------------------------
    // Samakan konfigurasi cookie dengan auth.php
    // ------------------------------------------------------

    ini_set(
        "session.use_only_cookies",
        "1"
    );

    ini_set(
        "session.use_strict_mode",
        "1"
    );

    ini_set(
        "session.use_trans_sid",
        "0"
    );


    $session_cookie_secure =
        isset(
            $_SERVER["HTTPS"]
        )
        &&
        strtolower(
            $_SERVER["HTTPS"]
        )
        !==
        "off";


    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => $session_cookie_secure,
        "httponly" => true,
        "samesite" => "Lax"
    ]);


    session_start();

}



// ==========================================================
// SIMPAN PARAMETER COOKIE
// ==========================================================

$cookie_params =
    session_get_cookie_params();



// ==========================================================
// HAPUS SELURUH DATA SESSION
// ==========================================================

$_SESSION = [];



// ==========================================================
// HAPUS SESSION COOKIE
// ==========================================================

if (
    ini_get(
        "session.use_cookies"
    )
) {

    setcookie(
        session_name(),
        "",
        [
            "expires" =>
                time() - 42000,

            "path" =>
                $cookie_params["path"]
                ??
                "/",

            "domain" =>
                $cookie_params["domain"]
                ??
                "",

            "secure" =>
                $cookie_params["secure"]
                ?? 
                false,

            "httponly" =>
                $cookie_params["httponly"]
                ??
                true,

            "samesite" =>
                $cookie_params["samesite"]
                ??
                "Lax"
        ]
    );

}



// ==========================================================
// DESTROY SESSION
// ==========================================================

session_destroy();



// ==========================================================
// REDIRECT KE WEBSITE PUBLIC
// ==========================================================
//
// Jangan hardcode:
// /cisalada-go/index.php
//
// Karena ketika hosting nanti BASE_URL akan berbeda.
// ==========================================================

require_once
    __DIR__
    .
    "/../config/database.php";


header(
    "Location: "
    .
    BASE_URL
    .
    "index.php"
);

exit;