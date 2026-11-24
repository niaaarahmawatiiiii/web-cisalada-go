<?php


// ==========================================
// CLEAN OUTPUT
// ==========================================

function clean($data)
{
    return htmlspecialchars(
        $data ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


// ==========================================
// REDIRECT
// ==========================================

function redirect($url)
{
    header(
        "Location: " . $url
    );

    exit;
}


// ==========================================
// CEK LOGIN ADMIN
// ==========================================

function is_logged_in()
{
    return isset(
        $_SESSION["admin_id"]
    );
}


// ==========================================
// FORMAT RUPIAH
// ==========================================

function format_rupiah($number)
{
    return "Rp " . number_format(
        $number,
        0,
        ',',
        '.'
    );
}


// ==========================================
// CSRF TOKEN
// ==========================================
// Membuat token CSRF untuk form admin.
// ==========================================

function csrf_token()
{
    if (
        session_status()
        ===
        PHP_SESSION_NONE
    ) {

        session_start();

    }


    if (
        empty(
            $_SESSION["csrf_token"]
        )
    ) {

        $_SESSION["csrf_token"] =
            bin2hex(
                random_bytes(32)
            );

    }


    return $_SESSION["csrf_token"];
}


// ==========================================
// CSRF FIELD
// ==========================================
// Menghasilkan input hidden untuk form.
// ==========================================

function csrf_field()
{
    return
        '<input
            type="hidden"
            name="csrf_token"
            value="' .
        clean(
            csrf_token()
        ) .
        '"
        >';
}


// ==========================================
// VALIDASI CSRF
// ==========================================
// Mengecek token dari form dengan token session.
// ==========================================

function verify_csrf()
{
    if (
        session_status()
        ===
        PHP_SESSION_NONE
    ) {

        session_start();

    }


    $session_token =
        $_SESSION[
            "csrf_token"
        ]
        ??
        "";


    $submitted_token =
        $_POST[
            "csrf_token"
        ]
        ??
        "";


    if (
        $session_token === ""
        ||
        $submitted_token === ""
    ) {

        return false;

    }


    return hash_equals(
        $session_token,
        $submitted_token
    );
}


// ==========================================
// REQUIRE / WAJIB VALIDASI CSRF
// ==========================================
// Gunakan di proses POST admin sebelum
// menjalankan INSERT / UPDATE / DELETE.
// ==========================================

function require_csrf()
{
    if (
        !verify_csrf()
    ) {

        http_response_code(
            403
        );


        die(
            "Permintaan tidak valid atau token keamanan telah kedaluwarsa."
        );

    }
}


// ==========================================
// REFRESH CSRF TOKEN
// ==========================================
// Membuat token baru setelah operasi penting.
// ==========================================

function regenerate_csrf_token()
{
    if (
        session_status()
        ===
        PHP_SESSION_NONE
    ) {

        session_start();

    }


    $_SESSION["csrf_token"] =
        bin2hex(
            random_bytes(32)
        );


    return $_SESSION["csrf_token"];
}