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