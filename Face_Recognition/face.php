<?php
if (isset($_POST['launch'])) {
    $output = shell_exec("python C:/xampp/htdocs/Face_Recognition/Utilisateur.py 2>&1");

    if (trim($output) === "OK") {
        echo '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Reconnaissance réussie</title>
            <meta http-equiv="refresh" content="10;url=../projetAJR/index.php">
        </head>
        <body>
            <h2 style="text-align:center; color:green; margin-top:50px;">✔ Visage reconnu avec succès !</h2>
            <p style="text-align:center;">Redirection dans 10 secondes...</p>
        </body>
        </html>';
        exit();
    } else {
        $error = "Échec de la reconnaissance faciale.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Lancer la reconnaissance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center bg-white p-5 rounded shadow">
            <h1 class="mb-4 text-primary">Bienvenue</h1>
            <p class="mb-4 fs-5">Cliquez ci-dessous pour lancer la reconnaissance faciale :</p>

            <form method="POST">
                <button type="submit" name="launch" class="btn btn-lg btn-success px-5">Lancer</button>
            </form>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mt-4" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
