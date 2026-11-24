<?php

// ==========================================================
// CISALADA GO
// GLOBAL ADMIN AUTHENTICATION + SECURITY
// ==========================================================


// ==========================================================
// KONFIGURASI SESSION
// ==========================================================

if (
    session_status()
    ===
    PHP_SESSION_NONE
) {

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
// DATABASE
// ==========================================================

require_once
    __DIR__
    .
    "/../config/database.php";



// ==========================================================
// CEK LOGIN
// ==========================================================

if (
    !isset(
        $_SESSION["admin_id"]
    )
) {

    header(
        "Location: "
        .
        BASE_URL
        .
        "admin/login.php"
    );

    exit;

}



// ==========================================================
// CEK ROLE
// ==========================================================

if (
    !isset(
        $_SESSION["admin_role"]
    )
    ||
    $_SESSION["admin_role"]
    !==
    "admin"
) {

    $_SESSION = [];


    if (
        ini_get(
            "session.use_cookies"
        )
    ) {

        $params =
            session_get_cookie_params();


        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"] ?? "",
            $params["secure"],
            $params["httponly"]
        );

    }


    session_destroy();


    header(
        "Location: "
        .
        BASE_URL
        .
        "admin/login.php"
    );

    exit;

}



// ==========================================================
// SESSION TIMEOUT
// ==========================================================
//
// 30 menit tanpa aktivitas.
// ==========================================================

$session_timeout =
    30 * 60;


$current_time =
    time();


if (
    isset(
        $_SESSION["last_activity"]
    )
) {

    $inactive_time =
        $current_time
        -
        (int) $_SESSION[
            "last_activity"
        ];


    if (
        $inactive_time
        >
        $session_timeout
    ) {


        $_SESSION = [];


        if (
            ini_get(
                "session.use_cookies"
            )
        ) {

            $params =
                session_get_cookie_params();


            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"] ?? "",
                $params["secure"],
                $params["httponly"]
            );

        }


        session_destroy();


        header(
            "Location: "
            .
            BASE_URL
            .
            "admin/login.php?timeout=1"
        );

        exit;

    }

}


// ==========================================================
// PERBARUI AKTIVITAS
// ==========================================================

$_SESSION[
    "last_activity"
] =
    $current_time;



// ==========================================================
// REGENERASI SESSION BERKALA
// ==========================================================
//
// Maksimal setiap 15 menit selama session aktif.
// ==========================================================

$regenerate_interval =
    15 * 60;


if (
    !isset(
        $_SESSION["session_regenerated_at"]
    )
) {


    $_SESSION[
        "session_regenerated_at"
    ] =
        $current_time;


} elseif (
    (
        $current_time
        -
        (int) $_SESSION[
            "session_regenerated_at"
        ]
    )
    >
    $regenerate_interval
) {


    session_regenerate_id(
        true
    );


    $_SESSION[
        "session_regenerated_at"
    ] =
        $current_time;

}



// ==========================================================
// CSRF TOKEN
// ==========================================================

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



// ==========================================================
// VALIDASI CSRF UNTUK POST
// ==========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
) {


    $session_token =
        $_SESSION["csrf_token"]
        ??
        "";


    $submitted_token =
        $_POST["csrf_token"]
        ??
        "";


    if (
        $session_token === ""
        ||
        $submitted_token === ""
        ||
        !hash_equals(
            $session_token,
            $submitted_token
        )
    ) {


        http_response_code(
            403
        );


        exit(
            "Permintaan tidak valid. Token keamanan tidak sesuai."
        );

    }

}



// ==========================================================
// IDENTIFIKASI FILE
// ==========================================================

$request_path =
    parse_url(
        $_SERVER["REQUEST_URI"]
        ??
        "",
        PHP_URL_PATH
    );


$request_basename =
    strtolower(
        basename(
            $request_path
        )
    );



// ==========================================================
// BLOKIR GET KE HAPUS.PHP
// ==========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "GET"
    &&
    $request_basename
    ===
    "hapus.php"
) {

    http_response_code(
        405
    );


    exit(
        "Metode permintaan tidak diizinkan."
    );

}



// ==========================================================
// BLOKIR GET KE SYNC_RATING.PHP
// ==========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "GET"
    &&
    $request_basename
    ===
    "sync_rating.php"
) {

    http_response_code(
        405
    );


    exit(
        "Metode permintaan tidak diizinkan."
    );

}



// ==========================================================
// BACKWARD COMPATIBILITY
// ==========================================================

if (
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST"
    &&
    isset(
        $_POST["id"]
    )
    &&
    is_numeric(
        $_POST["id"]
    )
) {


    if (
        $request_basename
        ===
        "hapus.php"
        ||
        $request_basename
        ===
        "sync_rating.php"
    ) {

        $_GET["id"] =
            (int) $_POST["id"];

    }

}



// ==========================================================
// GLOBAL SECURITY OUTPUT
// ==========================================================

if (
    !defined(
        "CISALADA_ADMIN_SECURITY_HANDLER"
    )
) {


    define(
        "CISALADA_ADMIN_SECURITY_HANDLER",
        true
    );



    // ======================================================
    // JAVASCRIPT
    // ======================================================

    $csrf_script = <<<'HTML'

<script>
(function () {

    "use strict";


    // ==================================================
    // TOKEN
    // ==================================================

    const csrfToken =
        document.body.dataset.csrfToken || "";


    // ==================================================
    // FORM POST
    // ==================================================

    document
        .querySelectorAll("form")
        .forEach(
            function (form) {

                const method =
                    (
                        form.getAttribute("method")
                        ||
                        "GET"
                    ).toUpperCase();


                if (
                    method !== "POST"
                ) {

                    return;

                }


                if (
                    form.querySelector(
                        'input[name="csrf_token"]'
                    )
                ) {

                    return;

                }


                const input =
                    document.createElement(
                        "input"
                    );


                input.type =
                    "hidden";


                input.name =
                    "csrf_token";


                input.value =
                    csrfToken;


                form.appendChild(
                    input
                );

            }
        );



    // ==================================================
    // LINK HAPUS / SYNC
    // ==================================================

    document
        .querySelectorAll(
            'a[href*="hapus.php?id="], a[href*="/hapus.php?id="], a[href*="sync_rating.php?id="], a[href*="/sync_rating.php?id="]'
        )
        .forEach(
            function (link) {

                const href =
                    link.getAttribute(
                        "href"
                    );


                if (
                    !href
                ) {

                    return;

                }


                let parsedUrl;


                try {

                    parsedUrl =
                        new URL(
                            href,
                            window.location.href
                        );

                } catch (
                    error
                ) {

                    return;

                }


                const pathname =
                    parsedUrl.pathname
                        .toLowerCase();


                const isDelete =
                    pathname.endsWith(
                        "hapus.php"
                    );


                const isSync =
                    pathname.endsWith(
                        "sync_rating.php"
                    );


                if (
                    !isDelete
                    &&
                    !isSync
                ) {

                    return;

                }


                const id =
                    parsedUrl.searchParams.get(
                        "id"
                    );


                if (
                    !id
                ) {

                    return;

                }



                // ==============================================
                // FORM
                // ==============================================

                const form =
                    document.createElement(
                        "form"
                    );


                form.method =
                    "POST";


                form.action =
                    parsedUrl.href;


                form.style.display =
                    "inline";


                form.style.margin =
                    "0";


                form.style.padding =
                    "0";



                // ==============================================
                // ID
                // ==============================================

                const idInput =
                    document.createElement(
                        "input"
                    );


                idInput.type =
                    "hidden";


                idInput.name =
                    "id";


                idInput.value =
                    id;


                form.appendChild(
                    idInput
                );



                // ==============================================
                // CSRF
                // ==============================================

                const tokenInput =
                    document.createElement(
                        "input"
                    );


                tokenInput.type =
                    "hidden";


                tokenInput.name =
                    "csrf_token";


                tokenInput.value =
                    csrfToken;


                form.appendChild(
                    tokenInput
                );



                // ==============================================
                // BUTTON
                // ==============================================

                const button =
                    document.createElement(
                        "button"
                    );


                button.type =
                    "submit";


                button.className =
                    link.className
                    ||
                    "";


                button.title =
                    link.title
                    ||
                    "Aksi";


                button.innerHTML =
                    link.innerHTML;


                const originalStyle =
                    link.getAttribute(
                        "style"
                    );


                if (
                    originalStyle
                ) {

                    button.setAttribute(
                        "style",
                        originalStyle
                    );

                }



                // ==============================================
                // DATA ATTRIBUTE
                // ==============================================

                Array
                    .from(
                        link.attributes
                    )
                    .forEach(
                        function (
                            attribute
                        ) {

                            if (
                                attribute.name.startsWith(
                                    "data-"
                                )
                            ) {

                                button.setAttribute(
                                    attribute.name,
                                    attribute.value
                                );

                            }

                        }
                    );



                // ==============================================
                // KONFIRMASI
                // ==============================================

                button.addEventListener(
                    "click",
                    function (
                        event
                    ) {

                        const confirmation =
                            window.confirm(
                                isDelete
                                    ?
                                    "Yakin ingin menghapus data ini?"
                                    :
                                    "Yakin ingin melanjutkan tindakan ini?"
                            );


                        if (
                            !confirmation
                        ) {

                            event.preventDefault();

                        }

                    }
                );



                form.appendChild(
                    button
                );



                // ==============================================
                // GANTI LINK
                // ==============================================

                if (
                    link.parentNode
                ) {

                    link.parentNode.replaceChild(
                        form,
                        link
                    );

                }

            }
        );


})();
</script>

HTML;



    // ======================================================
    // OUTPUT BUFFER
    // ======================================================

    ob_start(
        function (
            $buffer
        ) use (
            $csrf_script
        ) {


            // ==================================================
            // FAVICON
            // ==================================================

            $favicon_url =
                BASE_URL
                .
                "assets/images/favicon.png";


            $safe_favicon =
                htmlspecialchars(
                    $favicon_url,
                    ENT_QUOTES,
                    "UTF-8"
                );


            $favicon_html =
                "";


            if (
                !preg_match(
                    '/<link[^>]+rel\s*=\s*["\'](?:shortcut\s+icon|icon)["\']/i',
                    $buffer
                )
            ) {


                $favicon_html = <<<HTML

<link
    rel="icon"
    type="image/png"
    sizes="512x512"
    href="{$safe_favicon}"
>

<link
    rel="shortcut icon"
    type="image/png"
    href="{$safe_favicon}"
>

<link
    rel="apple-touch-icon"
    sizes="180x180"
    href="{$safe_favicon}"
>

<meta
    name="theme-color"
    content="#0d6efd"
>

HTML;

            }



            // ==================================================
            // CSRF DATA KE BODY
            // ==================================================

            $csrf_token =
                htmlspecialchars(
                    $_SESSION["csrf_token"]
                    ??
                    "",
                    ENT_QUOTES,
                    "UTF-8"
                );


            if (
                preg_match(
                    '/<body([^>]*)>/i',
                    $buffer
                )
            ) {


                $buffer =
                    preg_replace(
                        '/<body([^>]*)>/i',
                        '<body$1 data-csrf-token="' .
                        $csrf_token .
                        '">',
                        $buffer,
                        1
                    );

            }



            // ==================================================
            // FAVICON KE HEAD
            // ==================================================

            if (
                $favicon_html !== ""
                &&
                stripos(
                    $buffer,
                    "</head>"
                )
                !==
                false
            ) {


                $buffer =
                    preg_replace(
                        '/<\/head>/i',
                        $favicon_html
                        .
                        "\n</head>",
                        $buffer,
                        1
                    );

            }



            // ==================================================
            // SECURITY SCRIPT KE BODY
            // ==================================================

            if (
                stripos(
                    $buffer,
                    "</body>"
                )
                !==
                false
            ) {


                $buffer =
                    preg_replace(
                        '/<\/body>/i',
                        $csrf_script
                        .
                        "\n</body>",
                        $buffer,
                        1
                    );

            }



            return
                $buffer;

        }
    );

}