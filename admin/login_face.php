<?php
session_start();
$BASE_DIR = realpath(__DIR__ . '/../Face_Recognition');
$json_file = $BASE_DIR . '/recognized_user.json';
$message = '';

if (isset($_POST['face_login'])) {
    // Réinitialiser le fichier JSON
    file_put_contents($json_file, json_encode(["status" => "pending"]));
    // Lancer le script Python (en arrière-plan)
    $cmd = 'python "' . $BASE_DIR . '/Utilisateur.py"';
    // Sous Windows, utiliser start /B pour ne pas bloquer
    pclose(popen('start /B ' . $cmd, 'r'));
    // Attendre la reconnaissance (max 12s)
    $wait = 0;
    while ($wait < 12) {
        sleep(1);
        $data = json_decode(@file_get_contents($json_file), true);
        if ($data && $data['status'] === 'success') {
            $_SESSION['admin_face'] = true;
            $_SESSION['admin_name'] = $data['name'];
            header('Location: index.php');
            exit();
        } elseif ($data && $data['status'] === 'fail') {
            $message = 'Reconnaissance faciale échouée. Veuillez réessayer.';
            break;
        }
        $wait++;
    }
    if ($wait >= 12) {
        $message = 'Temps de reconnaissance dépassé. Veuillez réessayer.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin - Reconnaissance Faciale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #eef2f5; }
        .login-box { max-width: 400px; margin: 80px auto; background: #fff; border-radius: 10px; box-shadow: 0 0 20px #3f51b522; padding: 30px; }
        .btn-face { background: #4caf50; color: #fff; font-weight: bold; transition: background 0.3s; }
        .btn-face:hover { background: #388e3c; }
    </style>
</head>
<body>
<div class="login-box">
    <h3 class="mb-4 text-center">Connexion Admin par Reconnaissance Faciale</h3>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post">
        <button type="submit" name="face_login" class="btn btn-face w-100 py-2 mb-2">Se connecter par reconnaissance faciale</button>
    </form>
    <a href="../index.php" class="btn btn-link w-100">Retour au site</a>
</div>
</body>
</html>
