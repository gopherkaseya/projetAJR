<?php 
	session_start();
	if(isset($_POST["bt_fixer_regle_rapport"])){
		$deux_mois = time() + 3600*24 *60 ;
		setcookie($_POST["table"],$_POST["champs"],$deux_mois);
		$_COOKIE[$_POST["table"]] = $_POST["champs"];
	}
	require_once("model/classes.php");new Chado(Connexion::GetConnexion());
	$_ROLE = isset($_SESSION["snp"]['user'])?$_SESSION["snp"]['user']['role']:"";
	if(isset($_POST["bt_login"])){
		$where = " where us.password = ".Chado::$conx->quote($_POST["password"])." ";
		if($u = User::liste_objet($where,"")){
			$u = $u[0];
			$_SESSION["snp"]['user'] = $u;
			$_SESSION["snp"]['conx'] = true;
			$_ROLE = $u["role"];
			
		}else{
			$_ROLE = $_SESSION["snp"]['user'] = false;
			$_SESSION["snp"]['conx'] = false;
			$_SESSION["snp"]['erreur'] = "Erreur Authentification";
		} 
	}
	// echo"<pre>";print_r($_COOKIE);echo"</pre>";
	// require_once("num_pages.php");
	if(isset($_POST["bt_attribuer_carnet"]))attribuer_carnet();
?>
