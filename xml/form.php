<?php
// Hämtar tillverkare från API och visar i formulär
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
            background-color: #eef2f7;
            margin: 0;
            padding: 60px 20px;
        }
        .card {
            max-width: 460px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px 44px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
        }
        h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1a3a5c;
            margin: 0 0 28px 0;
            padding-bottom: 14px;
            border-bottom: 3px solid #1a3a5c;
        }
        label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            color: #333333;
        }
        select {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            border: 1px solid #bbbbbb;
            border-radius: 6px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }
        select:focus {
            outline: none;
            border-color: #1a3a5c;
        }
        input[type="submit"] {
            margin-top: 22px;
            width: 100%;
            padding: 13px;
            font-size: 15px;
            font-weight: bold;
            background-color: #1a3a5c;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #122b44;
        }
        a.home-link {
            display: block;
            margin-bottom: 20px;
            color: #1a3a5c;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
        }
        a.home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <a class="home-link" href="index.html">&larr; Till startsidan</a>
        <h1>Välj fordonstillverkare</h1>
        <form action="response.php" method="get">
            <label for="country">Tillverkare:</label>
            <select name="country" id="country">
                <option value="">-- Välj tillverkare --</option>
                <?php
                foreach ($arr as $manufacturer) {
                    $name    = htmlspecialchars($manufacturer[0]);
                    $country = htmlspecialchars($manufacturer[1]);
                    echo "                <option value=\"{$country}\">{$name}</option>\n";
                }
                ?>
            </select>
            <input type="submit" value="Visa fordon">
        </form>
    </div>
</body>
</html>