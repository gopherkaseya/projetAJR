<?php
session_start();
$host = 'localhost';
$db = 'authorized_user';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM my_table");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-row:hover {
            background: #e9ecef;
            transition: background 0.3s;
            cursor: pointer;
        }
        .back-link {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #0056b3;
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="index.php" class="back-link d-inline-block">&larr; Retour au dashboard</a>
        <form method="post" action="add_user_face.php" style="margin:0;">
            <button type="submit" class="btn btn-success">+ Ajouter un utilisateur (par visage)</button>
        </form>
    </div>
    <h2 class="mb-4">Liste des utilisateurs</h2>
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Âge</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr class="user-row" onclick="window.location='user_detail.php?id=<?php echo $user['id']; ?>'">
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['Name']); ?></td>
                <td><?php echo htmlspecialchars($user['Age']); ?></td>
                <td><?php echo htmlspecialchars($user['Address']); ?></td>
                <td><a href="user_detail.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Détail</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
