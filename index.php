<?php
session_start();

$jsonContent = file_get_contents('citation.json');

if ($jsonContent === false) {
    die('Error: Unable to read file "citation.json"');
}

$data = json_decode($jsonContent, true);

if ($data === null) {
    die('Error: Unable to decode JSON');
}

function genererCitation($data)
{
    $index = rand(0, count($data) - 1);
    return $data[$index];
}

$citationsGenerees = [];

if (!isset($_SESSION['citationsGenerees']) ||
    ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) ||
    ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['generate']))) {
    $nombreDeCitations = $_POST['number'] ?? $_GET['number'] ?? 0;
    for ($i = 0; $i < $nombreDeCitations; $i++) {
        $citation = genererCitation($data['citations']);
        $citationsGenerees[] = $citation;
    }
    $_SESSION['citationsGenerees'] = $citationsGenerees;
} else {
    $citationsGenerees = $_SESSION['citationsGenerees'];
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
        foreach ($citationsGenerees as $index => $citationData) {
            $citation = $citationData['citation'];
            $auteur = $citationData['auteur'];
            echo "<blockquote>$citation<footer> - $auteur</footer></blockquote>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='index' value='$index'>";
            echo "<button type='submit' name='export' class='exportButton' 
            data-index='$index' data-citation='" . htmlspecialchars($citation, ENT_QUOTES) . "' data-auteur='$auteur'>Exporter cette citation</button>";
            echo "</form>";
        }
        ?>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>