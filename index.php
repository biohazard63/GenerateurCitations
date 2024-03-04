<?php
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$jsonContent = file_get_contents('citation.json');

if ($jsonContent === false) {
    die('Error: Unable to read file "citation.json"');
}

$data = json_decode($jsonContent, true);

if ($data === null) {
    die('Error: Unable to decode JSON');
}

$citations = array_column($data['citations'], 'citation');
function genererCitation($citations)
{
    $index = rand(0, count($citations) - 1);
    return $citations[$index];
}

$citationsGenerees = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $nombreDeCitations = $_POST['number'];
    for ($i = 0; $i < $nombreDeCitations; $i++) {
        $citation = genererCitation($citations);
        $citationsGenerees[] = $citation;
    }
    $_SESSION['citationsGenerees'] = $citationsGenerees;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['generate'])) {
    $nombreDeCitations = $_GET['number'];
    for ($i = 0; $i < $nombreDeCitations; $i++) {
        $citation = genererCitation($citations);
        $citationsGenerees[] = $citation;
    }
    $_SESSION['citationsGenerees'] = $citationsGenerees;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {
    $index = $_POST['index'];
    $citationsGenerees = $_SESSION['citationsGenerees'] ?? [];
    if (isset($citationsGenerees[$index])) {
        exporterCitationEnImage($citationsGenerees[$index], $index);
    } else {
        echo "No citation to export at index $index";
    }
}

function exporterCitationEnImage($citation, $index)
{
    if ($citation === null) {
        echo "Cannot export null citation";
        return;
    }
    $image = imagecreatetruecolor(500, 200);

    $color = imagecolorallocate($image, 255, 255, 255);

    $backgroundColor = imagecolorallocate($image, 0, 0, 0);
    imagefill($image, 0, 0, $backgroundColor);

    $fontPath = 'Brillant.ttf';

    imagettftext($image, 20, 0, 10, 50, $color, $fontPath, $citation);

    header('Content-Description: File Transfer');
    header('Content-Type: image/jpeg');
    header('Content-Disposition: attachment; filename=citation' . $index . '.jpg');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    imagejpeg($image);

    imagedestroy($image);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="style.css">

    <title>Generateur de Citations</title>
</head>
<body>
<h1>Generateur de Citations</h1>
<div class="content">
    <div class="form-container">
        <div class="form-wrapper">
            <h2>Generateur via POST</h2>
            <form method="post">
                <label for="number">Nombre de citations à générer :</label>
                <input type="number" id="number" name="number" min="1" max="10">
                <button type="submit" name="generate">Generer une citation</button>
            </form>
        </div>
        <div class="form-wrapper">
            <h2>Generateur via GET</h2>
            <form method="get">
                <label for="number">Nombre de citations à générer :</label>
                <input type="number" id="number" name="number" min="1" max="10">
                <button type="submit" name="generate">Generer une citation</button>
            </form>
        </div>
    </div>
    <div class="quote-container">
        <?php
        foreach ($citationsGenerees as $index => $citation) {
            echo "<blockquote>$citation</blockquote>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='index' value='$index'>";
            echo "<button type='submit' name='export'>Exporter cette citation</button>";
            echo "</form>";
            echo "<img src='citation" . $index . ".jpg' alt='Image de citation'>";
        }
        ?>
    </div>
</div>
<!--<script src="script.js"></script>-->
</body>
</html>