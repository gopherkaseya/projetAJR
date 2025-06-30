<?php
session_start();

$output = shell_exec("python ../Face_Recognition/Utilisateur.py");

$resultPath = '../Face_Recognition/recognized_user.json';
if (file_exists($resultPath)) {
    $data = json_decode(file_get_contents($resultPath), true);
    unlink($resultPath); // Très important pour éviter les lectures futures incorrectes

    if ($data && $data["status"] === "success") {
        $_SESSION['username'] = $data["name"];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "fail"]);
    }
} else {
    echo json_encode(["status" => "error"]);
}

