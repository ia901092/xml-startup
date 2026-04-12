<?php
// response.php 
$MANUFACTURER_URL = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$VEHICLES_URL     = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/";
$IMAGE_BASE       = "https://wwwlab.webug.se/examples/XML/vehicleImages/";

// Hämta tillverkarlista från manufacturer API
$manufacturerJson = file_get_contents($MANUFACTURER_URL);
$manufacturers    = json_decode($manufacturerJson, true);

//  valt land från GET
$selectedCountry = isset($_GET['country']) ? trim($_GET['country']) : '';

// Hämta fordon om ett land är valt
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

// Listar ut vilket fält som är vilket (bild, HP, år).

function parseVehicle($vehicle) {
    $count = count($vehicle);

    // Index 0 = alltid modellnamn
    $model = ($count > 0) ? trim($vehicle[0]) : '-';

    // Sista index = alltid bildfilnamn
    $lastIndex = $count - 1;
    $imgFile   = ($count > 1 && stripos($vehicle[$lastIndex], '.png') !== false)
        ? trim($vehicle[$lastIndex])
        : '';

    $hp     = '0HP';
    $year   = '-';
    $extras = [];

    // Loopa mellanliggande fält (index 1 t.o.m. näst-sista)
    $endIndex = ($imgFile !== '') ? $lastIndex - 1 : $lastIndex;
    for ($i = 1; $i <= $endIndex; $i++) {
        $val = trim($vehicle[$i]);
        if ($val === '') {
            continue;
        }

        if (preg_match('/^\d+\s*[Hh][Pp]$/', $val)) {
            // HP-värde: t.ex. "330Hp", "265HP"
            $hp = $val;
        } elseif (preg_match('/\d{4}/', $val)) {
            // Produktionsperiod: t.ex. "1997-2022", "1967-1982"
            $year = $val;
        } elseif ($val !== $model) {
            // Övriga fält: drivlina, fordonstyp, etc.
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
        /* === Grundlayout === */
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

        /* === Yttre tabell === */
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
        /* Alternerande rader — yttre tabell */
        table.mfr-table > tbody > tr:nth-child(even) {
            background-color: #f3f6fa;
        }
        table.mfr-table > tbody > tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* === Nästlad fordonstabll — Row Layout  
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
        table.vehicle-table > tbody > tr:last-child > td {
            border-bottom: none;
        }
        /* Alterntivrvt rader — fordonstabll (udda/jämt) */
        table.vehicle-table > tbody > tr:nth-child(even) {
            background-color: #eaf0f8;
        }
        table.vehicle-table > tbody > tr:nth-child(odd) {
            background-color: #f9fbfd;
        }

        /* === Villkorsstyling: HP > 300 = röd text, annars svart === */
        .high-hp {
            color: red;
            font-weight: bold;
        }
        .normal-hp {
            color: black;
        }

        /* === Fordonsbilder === */
        img.vehicle-img {
            width: 100px;
            height: auto;
            display: block;
            border-radius: 4px;
        }
.high-hp {
            color: red;
            font-weight: bold;
        }
        .normal-hp {
            color: black;
        }

        /* === Fordonsbilder === */
        img.vehicle-img {
            width: 100px;
            height: auto;
            display: block;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="container">

    <h1>Fordonsresultat</h1>
    <a class="back-link" href="form.php">&larr; Tillbaka</a>