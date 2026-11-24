<?php

require_once "../auth.php";
require_once "../../config/database.php";
require_once "../../includes/functions.php";
require_once "../../config/google_maps.php";


// ======================================================
// CEK API KEY
// ======================================================

if (
    GOOGLE_MAPS_API_KEY ===
    "MASUKKAN_API_KEY_GOOGLE_DISINI"
) {

    header(
        "Location: index.php?error=api_key"
    );

    exit;

}


// ======================================================
// CEK ID
// ======================================================

$id =
    isset($_GET["id"])
    ?
    (int) $_GET["id"]
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
// AMBIL DATA UMKM
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        SELECT
            id,
            name,
            category,
            address,
            maps_url,
            google_place_id
        FROM umkm
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


$umkm =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
);


if (
    !$umkm
) {

    header(
        "Location: index.php?error=not_found"
    );

    exit;

}


// ======================================================
// VALIDASI LINK / LOKASI
// ======================================================

$name =
    trim(
        $umkm["name"] ?? ""
    );


$address =
    trim(
        $umkm["address"] ?? ""
    );


$category =
    trim(
        $umkm["category"] ?? ""
    );


$maps_url =
    trim(
        $umkm["maps_url"] ?? ""
    );


if (
    $maps_url === ""
    &&
    $address === ""
) {

    header(
        "Location: index.php?error=no_location"
    );

    exit;

}


// ======================================================
// FUNGSI REQUEST GOOGLE
// ======================================================

function google_request(
    $url,
    $method,
    $api_key,
    $body = null
) {


    $ch =
        curl_init();


    if (
        $ch === false
    ) {

        return null;

    }


    $headers = [
        "Content-Type: application/json",
        "X-Goog-Api-Key: " . $api_key
    ];


    curl_setopt(
        $ch,
        CURLOPT_URL,
        $url
    );


    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_TIMEOUT,
        20
    );


    curl_setopt(
        $ch,
        CURLOPT_CUSTOMREQUEST,
        $method
    );


    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        $headers
    );


    if (
        $body !== null
    ) {

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode(
                $body
            )
        );

    }


    $response =
        curl_exec(
            $ch
        );


    $http_code =
        curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


    curl_close(
        $ch
    );


    if (
        $response === false
        ||
        $http_code < 200
        ||
        $http_code >= 300
    ) {

        return null;

    }


    return json_decode(
        $response,
        true
    );

}


// ======================================================
// CARI PLACE ID
// ======================================================

$place_id =
    trim(
        $umkm["google_place_id"] ?? ""
    );


// ------------------------------------------------------
// KALAU SUDAH PUNYA PLACE ID
// TIDAK PERLU SEARCH LAGI
// ------------------------------------------------------

if (
    $place_id === ""
) {


    $text_query =
        $name;


    if (
        $category !== ""
    ) {

        $text_query .=
            " "
            .
            $category;

    }


    if (
        $address !== ""
    ) {

        $text_query .=
            " "
            .
            $address;

    }


    $text_query .=
        " Desa Cisalada Cigombong Bogor";


    $search_url =
        "https://places.googleapis.com/v1/places:searchText";


    $search_body = [

        "textQuery" =>
            $text_query,

        "languageCode" =>
            "id",

        "regionCode" =>
            "ID"

    ];


    $search_result =
        google_request(
            $search_url,
            "POST",
            GOOGLE_MAPS_API_KEY,
            $search_body
        );


    if (
        !$search_result
        ||
        empty(
            $search_result["places"]
        )
    ) {

        header(
            "Location: index.php?error=place_not_found"
        );

        exit;

    }


    $first_place =
        $search_result["places"][0];


    $place_id =
        trim(
            $first_place["id"]
            ??
            ""
        );


    if (
        $place_id === ""
    ) {

        header(
            "Location: index.php?error=place_not_found"
        );

        exit;

    }


}


// ======================================================
// AMBIL PLACE DETAILS
// ======================================================

$details_url =
    "https://places.googleapis.com/v1/places/"
    .
    rawurlencode(
        $place_id
    );


$details_headers = [
    "Content-Type: application/json",
    "X-Goog-Api-Key: " . GOOGLE_MAPS_API_KEY,
    "X-Goog-FieldMask: id,displayName,rating,userRatingCount,location,googleMapsUri,formattedAddress"
];


$ch =
    curl_init();


curl_setopt(
    $ch,
    CURLOPT_URL,
    $details_url
);


curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);


curl_setopt(
    $ch,
    CURLOPT_TIMEOUT,
    20
);


curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    $details_headers
);


$response =
    curl_exec(
        $ch
    );


$http_code =
    curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );


curl_close(
    $ch
);


if (
    $response === false
    ||
    $http_code < 200
    ||
    $http_code >= 300
) {

    header(
        "Location: index.php?error=google_request"
    );

    exit;

}


$data =
    json_decode(
        $response,
        true
    );


if (
    !is_array($data)
) {

    header(
        "Location: index.php?error=google_response"
    );

    exit;

}


// ======================================================
// AMBIL DATA GOOGLE
// ======================================================

$google_rating =
    isset(
        $data["rating"]
    )
    ?
    (float) $data["rating"]
    :
    null;


$google_rating_count =
    isset(
        $data["userRatingCount"]
    )
    ?
    (int) $data["userRatingCount"]
    :
    null;


$google_maps_uri =
    trim(
        $data["googleMapsUri"]
        ??
        ""
    );


$google_address =
    trim(
        $data["formattedAddress"]
        ??
        ""
    );


// ======================================================
// AMBIL KOORDINAT
// ======================================================

$latitude =
    isset(
        $data["location"]["latitude"]
    )
    ?
    (float)
    $data["location"]["latitude"]
    :
    null;


$longitude =
    isset(
        $data["location"]["longitude"]
    )
    ?
    (float)
    $data["location"]["longitude"]
    :
    null;


// ======================================================
// UPDATE DATABASE
// ======================================================

$stmt =
    mysqli_prepare(
        $conn,
        "
        UPDATE umkm

        SET
            google_place_id = ?,
            google_rating = ?,
            google_rating_count = ?,
            rating_synced_at = NOW()

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


$rating_value =
    $google_rating;


$rating_count_value =
    $google_rating_count;


mysqli_stmt_bind_param(
    $stmt,
    "sdii",
    $place_id,
    $rating_value,
    $rating_count_value,
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
        "Location: index.php?error=update"
    );

    exit;

}


mysqli_stmt_close(
    $stmt
);


// ======================================================
// OPTIONAL:
// SIMPAN GOOGLE MAPS URI JIKA LINK BELUM ADA
// ======================================================

if (
    $maps_url === ""
    &&
    $google_maps_uri !== ""
) {


    $stmt =
        mysqli_prepare(
            $conn,
            "
            UPDATE umkm
            SET maps_url = ?
            WHERE id = ?
            "
        );


    if (
        $stmt
    ) {


        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $google_maps_uri,
            $id
        );


        mysqli_stmt_execute(
            $stmt
        );


        mysqli_stmt_close(
            $stmt
        );

    }

}


// ======================================================
// REDIRECT BERHASIL
// ======================================================

header(
    "Location: index.php?success=rating"
);


exit;