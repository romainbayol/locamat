<?php
session_start();

define('MAX_ATTEMPTS', 5); // Nombre maximum de tentatives
define('LOCKOUT_TIME', 120); // Temps de blocage en secondes

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS) {
    $remaining_time = ($_SESSION['last_attempt_time'] + LOCKOUT_TIME) - time();
    if ($remaining_time > 0) {
        die("Trop de tentatives échouées. Réessayez dans " . ceil($remaining_time / 60) . " minutes.");
    } else {

        $_SESSION['login_attempts'] = 0;
    }
}

require_once 'db.php';


// Clé secrète pour AES-256-GCM
define('AES_KEY', 'e3f3a7b1c91d45a2843c78b2df3e902f');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
        error_log("Erreur : champs vides.");
    } else {
        try {
   
            $stmt = $pdo->prepare("SELECT id_utilisateur, password, iv, tag, role, prenom FROM utilisateurs WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                error_log("Utilisateur trouvé : " . print_r($user, true));

                $ciphertext = $user['password'];
                $iv = base64_decode($user['iv']);
                $tag = base64_decode($user['tag']);

                // Log IV et tag pour vérifier leur contenu
                error_log("IV décodé : " . bin2hex($iv));
                error_log("Tag décodé : " . bin2hex($tag));

                // Déchiffrer le mot de passe pour vérification
                $decrypted_password = openssl_decrypt(base64_decode($ciphertext), 'aes-256-gcm', AES_KEY, 0, $iv, $tag);

                if (!$decrypted_password) {
                    error_log("Erreur de déchiffrement : " . openssl_error_string());
                } else {
                    error_log("Mot de passe déchiffré : $decrypted_password");
                }

                if ($decrypted_password && hash_equals($decrypted_password, $password)) {
                    error_log("Connexion réussie pour l'utilisateur : $email");

                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['last_attempt_time'] = 0;

                    $_SESSION['user'] = [
                        'email' => $email,
                        'id_utilisateur' => $user['id_utilisateur'],
                        'role' => (int) $user['role'],
                        'prenom' => $user['prenom']
                    ];
                    header('Location: index.php');
                    exit();
                } else {

                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt_time'] = time();

                    error_log("Mot de passe incorrect. Tentative numéro : " . $_SESSION['login_attempts']);
                    $error = "Adresse email ou mot de passe incorrect.";
                }
            } else {
                error_log("Adresse email ou mot de passe incorrect.");
                $error = "Adresse email ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            error_log("Erreur de connexion à la base de données.");
            $error = "Erreur interne, veuillez réessayer plus tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Connexion</title>
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
    </style>
</head>
<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <form method="POST" action="login.php">
            <img class="mb-4" src="img/logo.png" alt="Logo" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Connectez-vous</h1>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="form-floating mb-2">
                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
                <label for="floatingInput">Adresse mail</label>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Mot de passe" required>
                <label for="floatingPassword">Mot de passe</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Connexion</button>
            <a href="index.php" class="btn btn-secondary mt-3">Retour à l'accueil</a>
        </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
