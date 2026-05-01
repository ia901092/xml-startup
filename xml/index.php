<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicles</title>
    <style>
        body {
            font-family: Arial;
        }
        table {
            border: 1px solid black;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td, th {
            border: 1px solid black;
            padding: 5px;
        }
        th {
            background-color: #bbdefb;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
        img {
            width: 80px;
        }
        .red {
            color: red;
        }
    </style>
</head>
<body>

<?php
$selected = "";
if (isset($_GET['country'])) {
    $selected = $_GET['country'];
}

$jsontext = file_get_contents("https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer");
$manufacturers = json_decode($jsontext, true);
?>

<form action="" method="get">
    <select name="country">
        <option value="">-- Select --</option>
<?php
foreach ($manufacturers as $m) {
    echo "<option value=\"" . $m[1] . "\">" . $m[0] . "</option>";
}
?>
    </select>
    <input type="submit" value="Show vehicles">
</form>

<?php
if ($selected != "") {
    $url = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/?country=" . $selected;
    $jsontext = file_get_contents($url);
    $data = json_decode($jsontext, true);

    foreach ($data as $man) {
        echo "<table>";
        echo "<tr><td>" . $man[0] . "</td><td>" . $selected . "</td></tr>";
        echo "<tr><td colspan=\"2\">";

        echo "<table>";
        echo "<tr><th>Image</th><th>Model</th><th>Type</th><th>HP</th><th>Year</th></tr>";

        foreach ($man[1] as $v) {
            $hp = $v[2];
            if (intval(substr($hp, 0, -2)) > 300) {
                $color = "red";
            } else {
                $color = "black";
            }

            echo "<tr>";
            echo "<td><img src=\"https://wwwlab.webug.se/examples/XML/vehicleImages/" . $v[4] . "\"></td>";
            echo "<td>" . $v[0] . "</td>";
            echo "<td>" . $v[1] . "</td>";
            echo "<td style=\"color:" . $color . "\">" . $v[2] . "</td>";
            echo "<td>" . $v[3] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</td></tr>";
        echo "</table>";
    }
}
?>

</body>
</html>