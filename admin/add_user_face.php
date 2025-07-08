<?php
// Cette page tente de lancer le script Python GUI pour ajouter un utilisateur par reconnaissance faciale
$BASE_DIR = realpath(__DIR__ . '/../Face_Recognition');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cmd = 'python "' . $BASE_DIR . '/GUI_Face_recognition.py"';
    // Sous Windows, utiliser start pour lancer le GUI sans bloquer
    pclose(popen('start "" ' . $cmd, 'r'));
    $message = 'L\'interface de capture faciale a été lancée. Veuillez compléter l\'ajout dans la fenêtre qui s\'ouvre.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout utilisateur par visage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">AdminPanel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="users.php">Utilisateurs</a>
        </li>
      </ul>
      <form class="d-flex" method="post" action="../index.php">
        <input type="hidden" name="bt_deconx" value="1">
        <button class="btn btn-outline-light" type="submit">Déconnexion</button>
      </form>
    </div>
  </div>
</nav>
<div class="container py-4">
    <a href="users.php" class="btn btn-link mb-3">&larr; Retour à la liste</a>
    <h2 class="mb-4">Ajouter un utilisateur par reconnaissance faciale</h2>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php else: ?>
        <form method="post">
            <button type="submit" class="btn btn-success">Lancer l'interface de capture faciale</button>
        </form>
        <p class="mt-3 text-muted">Ce bouton ouvrira l'interface graphique Python pour capturer le visage et enregistrer l'utilisateur.<br>Assurez-vous que le serveur et le poste d'administration sont les mêmes.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
