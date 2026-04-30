<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicles</title>
</head>
<body>

<?php
$selected = "";
if (isset($_GET['country'])) {
    $selected = $_GET['country'];
}
?>

<form action="" method="get">
    <input type="text" name="country">
    <input type="submit" value="Show vehicles">
</form>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicles</title>
</head>
<body>

<?php
$selected = "";
if (isset($_GET['country'])) {
    $selected = $_GET['country'];
}

$jsontext = file_get_contents("https://wwwlab.webug.se/examples/XML/vehiclesservice/manufacturer");
$manufacturers = json_decode($jsontext, true);

print_r($manufacturers);
?>

<form action="" method="get">
    <input type="text" name="country">
    <input type="submit" value="Show vehicles">
</form>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicles</title>
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

</body>
</html>
<?php
if ($selected != "") {
    $url = "https://wwwlab.webug.se/examples/XML/vehiclesservice/vehicles/?country=" . $selected;
    $jsontext = file_get_contents($url);
    $data = json_decode($jsontext, true);

    print_r($data);
}
?>