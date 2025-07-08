<?php
session_start();
$host = 'localhost';
$db = 'authorized_user';
$user = 'root';
$pass = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $address = trim($_POST['address'] ?? '');
    if ($name && $age && $address) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("INSERT INTO my_table (name, age, address) VALUES (?, ?, ?)");
            $stmt->execute([$name, $age, $address]);
            header('Location: users.php');
            exit();
        } catch (PDOException $e) {
            $message = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
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
    <h2 class="mb-4">Ajouter un utilisateur</h2>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post" class="card p-4 shadow-sm" style="max-width:500px;margin:auto;">
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="age" class="form-label">Âge</label>
            <input type="number" class="form-control" id="age" name="age" min="1" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Ajouter</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
