<?php
  session_start();
  if(isset($_SESSION['username'])){
    header('Location:http://localhost/projetAJR/');
  }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion - application</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .login-container {
      height: 100vh;
    }
    .login-image {
      object-fit: cover;
      height: 100%;
      width: 100%;
    }
    .login-text h2 {
      font-weight: 700;
      font-size: 2.5rem;
      color: #0d6efd;
    }
    .login-text p {
      font-size: 1.1rem;
      color: #6c757d;
    }
  </style>
</head>
<!-- ... reste du head inchangé ... -->

<body>
  <div class="container-fluid login-container d-flex p-0">
    <div class="col-md-6 d-none d-md-block p-0">
      <img src="../asset/images/face-recognition-personal-identification-collage.jpg" alt="Authentification" class="login-image">
    </div>

    <div class="col-md-6 d-flex align-items-center justify-content-center">
      <div class="w-75 login-text text-center">
        <h2 class="mb-3">Bienvenue</h2>
        <p class="mb-4">
          La <strong>reconnaissance faciale</strong> nous aide à confirmer que vous êtes bien la personne autorisée.
        </p>

        <!-- Supprimer method="post" -->
        <form id="authForm">
          <button type="submit" id="authBtn" class="btn btn-primary w-100">S'authentifier</button>
        </form>

        <!-- Affichage du message -->
        <div id="authMessage" class="mt-3"></div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('authForm');
    const messageDiv = document.getElementById('authMessage');

    form.addEventListener('submit', function(e) {
      e.preventDefault(); // Empêche le rechargement

      fetch('auth_face.php')
        .then(response => response.json())
        .then(data => {
          if (data.status === "success") {
            window.location.href = "http://localhost/projetAJR/";
          } else {
            messageDiv.innerHTML = "<p class='text-danger'>Authentification échouée. Veuillez réessayer.</p>";
          }
        })
        .catch(error => {
          console.error(error);
          messageDiv.innerHTML = "<p class='text-danger'>Erreur lors de la tentative d'authentification.</p>";
        });
    });
  </script>
</body>

