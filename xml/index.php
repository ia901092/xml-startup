<?php
$url      = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$jsontext = file_get_contents($url);
$arr      = json_decode($jsontext, true);

$vehicles = array();
$selected_country = "";

if (isset($_GET['country']) && $_GET['country'] != "") {
    $selected_country = $_GET['country'];
    $vehicle_url = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/?country=" . urlencode($selected_country);
    $vehicle_json = file_get_contents($vehicle_url);
    $vehicles = json_decode($vehicle_json, true);
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fordonsfilter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 20px;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        select {
            padding: 8px;
            font-size: 14px;
            margin-bottom: 12px;
        }
        input[type="submit"] {
            padding: 8px 16px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<h1>Fordonsfilter</h1>

<form method="get">
    <label for="country">Välj tillverkare:</label>
    <select name="country" id="country">
        <option value="">-- Välj tillverkare --</option>
        <?php
        foreach ($arr as $manufacturer) {
            $name    = $manufacturer[0];
            $country = $manufacturer[1];
            $selected = ($selected_country == $country) ? "selected" : "";
            echo "        <option value=\"" . htmlspecialchars($country) . "\" " . $selected . ">" . htmlspecialchars($name) . "</option>\n";
        }
        ?>
    </select>
    <input type="submit" value="Visa fordon">
</form>

<?php
if ($selected_country != "" && count($vehicles) > 0) {
    echo "        <h2>Fordon för " . htmlspecialchars($selected_country) . "</h2>\n";
    echo "        <table border=\"1\">\n";
    echo "            <tr>\n";
    echo "                <th>Tillverkare</th>\n";
    echo "                <th>Fordon</th>\n";
    echo "            </tr>\n";
    
    $current_manufacturer = "";
    
    foreach ($vehicles as $vehicle) {
        $manufacturer = $vehicle[0];
        $model        = $vehicle[1];
        $hp           = $vehicle[2];
        $year         = $vehicle[3];
        $image        = $vehicle[4];
        
        if ($manufacturer != $current_manufacturer) {
            if ($current_manufacturer != "") {
                echo "                </table>\n";
                echo "            </td>\n";
                echo "        </tr>\n";
            }
            
            $current_manufacturer = $manufacturer;
            echo "        <tr>\n";
            echo "            <td>" . htmlspecialchars($manufacturer) . "</td>\n";
            echo "            <td>\n";
            echo "                <table border=\"1\">\n";
        }
        
        echo "                    <tr>\n";
        echo "                        <td>" . htmlspecialchars($model) . "</td>\n";
        
        // Get HP value and apply conditional styling
        $hp_numeric = intval(substr($hp, 0, -3));
        $hp_color = ($hp_numeric > 300) ? "red" : "black";
        echo "                        <td style=\"color: " . $hp_color . ";\">" . htmlspecialchars($hp) . "</td>\n";
        
        echo "                        <td>" . htmlspecialchars($year) . "</td>\n";
        
        // Generate image tag
        $img_url = "https://wwwlab.webug.se/examples/XML/vehicleImages/" . $image;
        echo "                        <td><img src=\"" . htmlspecialchars($img_url) . "\" alt=\"" . htmlspecialchars($model) . "\" style=\"height: 50px;\"></td>\n";
        
        echo "                    </tr>\n";
    }
    
    if ($current_manufacturer != "") {
        echo "                </table>\n";
        echo "            </td>\n";
        echo "        </tr>\n";
    }
    
    echo "        </table>\n";
}
?>

</body>
</html>
