<?php
// Hämtar fordon från API och visar dem i en tabell

$MANUFACTURER_URL = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$VEHICLES_URL     = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/";
$IMAGE_BASE       = "https://wwwlab.webug.se/examples/XML/vehicleImages/";
?><?php
// Hämtar fordon från API och visar dem i en tabell

$MANUFACTURER_URL = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$VEHICLES_URL     = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/";
$IMAGE_BASE       = "https://wwwlab.webug.se/examples/XML/vehicleImages/";

// Hämtar tillverkarnamnen från API
$manufacturerJson = file_get_contents($MANUFACTURER_URL);
$manufacturers    = json_decode($manufacturerJson, true);

// Kollar vilket land användaren valde i formuläret
$selectedCountry = isset($_GET['country']) ? trim($_GET['country']) : '';

// Hämtar fordon om användaren valt ett land
$vehicles = [];
if ($selectedCountry !== '') {
    $vehicleJson = file_get_contents(
        $VEHICLES_URL . "?country=" . urlencode($selectedCountry)
    );
    if ($vehicleJson !== false) {
        $decoded = json_decode($vehicleJson, true);
        if (is_array($decoded)) {
            $vehicles = $decoded;
        }
    }
}
?><?php
// Hämtar fordon från API och visar dem i en tabell

$MANUFACTURER_URL = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$VEHICLES_URL     = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/";
$IMAGE_BASE       = "https://wwwlab.webug.se/examples/XML/vehicleImages/";

$manufacturerJson = file_get_contents($MANUFACTURER_URL);
$manufacturers    = json_decode($manufacturerJson, true);

$selectedCountry = isset($_GET['country']) ? trim($_GET['country']) : '';

$vehicles = [];
if ($selectedCountry !== '') {
    $vehicleJson = file_get_contents(
        $VEHICLES_URL . "?country=" . urlencode($selectedCountry)
    );
    if ($vehicleJson !== false) {
        $decoded = json_decode($vehicleJson, true);
        if (is_array($decoded)) {
            $vehicles = $decoded;
        }
    }
}

// Funktion som tar ut modell, hp, år och bild från varje fordon
function parseVehicle($vehicle) {
    $count = count($vehicle);
    $model = ($count > 0) ? trim($vehicle[0]) : '-';

    // Sista värdet är alltid bildfilnamnet
    $lastIndex = $count - 1;
    $imgFile   = ($count > 1 && stripos($vehicle[$lastIndex], '.png') !== false)
        ? trim($vehicle[$lastIndex])
        : '';

    $hp     = '0HP';
    $year   = '-';
    $extras = [];

    $endIndex = ($imgFile !== '') ? $lastIndex - 1 : $lastIndex;
    for ($i = 1; $i <= $endIndex; $i++) {
        $val = trim($vehicle[$i]);
        if ($val === '') continue;

        // Kolla om det är hp, år eller annan info
        if (preg_match('/^\d+\s*[Hh][Pp]$/', $val)) {
            $hp = $val;
        } elseif (preg_match('/\d{4}/', $val)) {
            $year = $val;
        } elseif ($val !== $model) {
            $extras[] = $val;
        }
    }

    return [
        'model'   => $model,
        'hp'      => $hp,
        'year'    => $year,
        'imgFile' => $imgFile,
        'info'    => !empty($extras) ? implode(', ', $extras) : '-',
    ];
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fordonsresultat</title>
    <style>
        /* Grundlayout */
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0;
            padding: 30px 20px;
        }
        .container {
            max-width: 1140px;
            margin: 0 auto;
        }
        h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1a3a5c;
            margin: 0 0 16px 0;
            padding-bottom: 12px;
            border-bottom: 3px solid #1a3a5c;
        }
        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #1a3a5c;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
        .info-box {
            background: #ffffff;
            padding: 20px 24px;
            border-radius: 8px;
            color: #666666;
            font-style: italic;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
/* Yttre tabell */
        table.mfr-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.10);
        }
        table.mfr-table > thead > tr > th {
            background-color: #1a3a5c;
            color: #ffffff;
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
        }
        table.mfr-table > tbody > tr > td {
            padding: 14px 16px;
            vertical-align: top;
            font-size: 14px;
        }
        table.mfr-table > tbody > tr:nth-child(even) { background-color: #f3f6fa; }
        table.mfr-table > tbody > tr:nth-child(odd)  { background-color: #ffffff; }

        /* Nästlad tabell */
        table.vehicle-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.vehicle-table > thead > tr > th {
            background-color: #3a6186;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 12px;
        }
        table.vehicle-table > tbody > tr > td {
            padding: 7px 10px;
            font-size: 12px;
            border-bottom: 1px solid #dde3ea;
            vertical-align: middle;
        }
        table.vehicle-table > tbody > tr:last-child > td { border-bottom: none; }
        table.vehicle-table > tbody > tr:nth-child(even) { background-color: #eaf0f8; }
        table.vehicle-table > tbody > tr:nth-child(odd)  { background-color: #f9fbfd; }

        /* HP-färg och bilder */
        .high-hp { color: red; font-weight: bold; }
        .normal-hp { color: black; }
        img.vehicle-img {
            width: 100px;
            height: auto;
            display: block;
            border-radius: 4px;
        }
