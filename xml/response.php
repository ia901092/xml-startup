<?php
// response.php
$MANUFACTURER_URL = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$VEHICLES_URL     = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/";
$IMAGE_BASE       = "https://wwwlab.webug.se/examples/XML/vehicleImages/";

// Hämta tillverkarlista från manufacturer API
$manufacturerJson = file_get_contents($MANUFACTURER_URL);
$manufacturers    = json_decode($manufacturerJson, true);

// Läs valt land från GET
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
        /* Alternerande rader */
        table.mfr-table > tbody > tr:nth-child(even) {
            background-color: #f3f6fa;
        }
        table.mfr-table > tbody > tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Nästlad fordonstabel */
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
        /* Alternerande rader i fordonstabellen */
        table.vehicle-table > tbody > tr:nth-child(even) {
            background-color: #eaf0f8;
        }
        table.vehicle-table > tbody > tr:nth-child(odd) {
            background-color: #f9fbfd;
        }

        /* HP över 300 = röd text */
        .high-hp {
            color: red;
            font-weight: bold;
        }
        .normal-hp {
            color: black;
        }

        /* Fordonsbilder */
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
    <a class="back-link" href="from.php">&larr; Tillbaka</a>

    <?php if ($selectedCountry === ''): ?>
        <!-- Inget val gjordes -->
        <div class="info-box">
            Inget val gjordes. Gå tillbaka och välj en tillverkare.
        </div>
    <?php elseif (empty($vehicles)): ?>
        <div class="info-box">
            Inga fordon hittades för
            <strong><?php echo htmlspecialchars($selectedCountry); ?></strong>.
        </div>
    <?php else: ?>
        <table class="mfr-table">
            <thead><tr>
                <th>Tillverkare</th>
                <th>Land</th>
                <th>Fordon</th>
            </tr></thead>
            <tbody>
                <?php foreach ($vehicles as $v): ?>
                    <?php
                    $mfrName     = isset($v[0]) ? $v[0] : $selectedCountry;
                    $mfrVehicles = isset($v[1]) ? $v[1] : [];
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($mfrName); ?></strong></td>
                        <td><?php echo htmlspecialchars($selectedCountry); ?></td>
                        <td>
                            <!-- Nästlad tabell med fordon -->
                            <table class="vehicle-table">
                                <thead><tr>
                                    <th>Bild</th>
                                    <th>Modell</th>
                                    <th>Info</th>
                                    <th>Effekt (HP)</th>
                                    <th>År</th>
                                </tr></thead>
                                <tbody>
                                    <?php foreach ($mfrVehicles as $vehicle): ?>
                                        <?php
                                        $f = parseVehicle($vehicle);
                                        $hpNumeric = intval(substr($f['hp'], 0, -2));
                                        if ($hpNumeric > 300) {
                                            $hpClass = 'high-hp';
                                        } else {
                                            $hpClass = 'normal-hp';
                                        }
                                        ?>
                                        <tr class="<?php echo $hpClass; ?>">
                                            <td>
                                                <?php if ($f['imgFile'] !== ''): ?>
                                                    <img class="vehicle-img"
                                                         src="<?php echo htmlspecialchars($IMAGE_BASE . $f['imgFile']); ?>"
                                                         alt="<?php echo htmlspecialchars($f['model']); ?>">
                                                <?php else: ?>
                                                    <em style="color:#aaa;">Ingen bild</em>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($f['model']); ?></td>
                                            <td><?php echo htmlspecialchars($f['info']); ?></td>
                                            <td class="<?php echo $hpClass; ?>">
                                                <?php echo htmlspecialchars($f['hp']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($f['year']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html> 