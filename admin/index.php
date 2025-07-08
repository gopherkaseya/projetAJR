<?php
session_start();
// Connexion à la base de données
$host = 'localhost';
$db = 'authorized_user';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Compter les agents actifs (non supprimés)
    $stmt = $pdo->query("SELECT COUNT(*) FROM my_table");
    $nb_agents = $stmt->fetchColumn();
} catch (PDOException $e) {
    $nb_agents = '?';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a.active, .sidebar a:hover {
            background: #495057;
        }
        .dashboard-cards .card {
            min-height: 120px;
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
          <a class="nav-link active" href="index.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="users.php">Utilisateurs</a>
        </li>
      </ul>
      <form class="d-flex" method="post" action="login_face.php">
        <input type="hidden" name="bt_deconx" value="1">
        <button class="btn btn-outline-light" type="submit">Déconnexion</button>
      </form>
    </div>
  </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="position-sticky pt-3">
                <h4 class="text-center py-3">Admin</h4>
                <a href="index.php" class="active">Dashboard</a>
                <a href="users.php">Utilisateurs</a>
                <a href="../index.php" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Déconnexion</a>
                <form id="logout-form" method="post" action="login_face.php" style="display:none;">
                    <input type="hidden" name="bt_deconx" value="1">
                </form>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tableau de bord</h1>
            </div>
            <div class="row dashboard-cards mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Utilisateurs enregistrés</h5>
                            <p class="card-text display-6"><?php echo $nb_agents; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">Bienvenue sur le dashboard admin</div>
                <div class="card-body">
                    <p class="card-text">Vous pouvez gérer les utilisateurs, consulter les statistiques et modifier les paramètres du site depuis ce tableau de bord.</p>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
