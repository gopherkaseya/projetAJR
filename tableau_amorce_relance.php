
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
		<meta name="fragment" content="!">
		<title>RELANCE</title>
		<link href="maia.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">
	</head>
	<?php  
	session_start();
	include("model/connexion.php");
	require_once("model/fonctions_sup.php");
	$conx = Connexion::GetConnexion();
	if(isset($_GET["id_ass"]))
		$where = " and a.id = '$_GET[id_ass]' ";
	else{
		$list_num = explode(",",$_POST["liste_num_note"]);
		foreach($list_num as $num)
			echo "<script>window.open('tableau_relance.php?liste_num_note=$num');</script>"
	}
	
	