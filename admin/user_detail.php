<?php
session_start();
$host = 'localhost';
$db = 'authorized_user';
$user = 'root';
$pass = '';
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}
$id = intval($_GET['id']);
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM my_table WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header('Location: users.php');
        exit();
    }
} catch (PDOException $e) {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .back-link {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }
        .card {
            transition: box-shadow 0.3s;
        }
        .card:hover {
            box-shadow: 0 0 20px #007bff44;
        }
    </style>
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
          <a class="nav-link" href="users.php">Utilisateurs</a>
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
    <a href="users.php" class="back-link mb-3 d-inline-block">&larr; Retour à la liste</a>
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header bg-primary text-white">Détail de l'utilisateur</div>
        <div class="card-body">
            <?php if ($user): ?>
                <h5 class="card-title mb-3">Nom : <?php echo htmlspecialchars($user['name']); ?></h5>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item"><strong>ID :</strong> <?php echo htmlspecialchars($user['id']); ?></li>
                    <li class="list-group-item"><strong>Âge :</strong> <?php echo htmlspecialchars($user['age']); ?></li>
                    <li class="list-group-item"><strong>Adresse :</strong> <?php echo htmlspecialchars($user['address']); ?></li>
                </ul>
                <a href="users.php" class="btn btn-secondary">Retour à la liste</a>
                <a href="index.php" class="btn btn-outline-primary">Dashboard</a>
            <?php else: ?>
                <div class="alert alert-danger">Utilisateur introuvable.</div>
                <a href="users.php" class="btn btn-secondary">Retour à la liste</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
