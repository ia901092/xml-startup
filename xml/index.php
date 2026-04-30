<?php
$url      = "https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer";
$jsontext = file_get_contents($url);
$arr      = json_decode($jsontext, true);
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
            echo "        <option value=\"" . htmlspecialchars($country) . "\">" . htmlspecialchars($name) . "</option>\n";
        }
        ?>
    </select>
    <input type="submit" value="Visa fordon">
</form>

</body>
</html>
