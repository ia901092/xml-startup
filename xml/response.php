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
?>