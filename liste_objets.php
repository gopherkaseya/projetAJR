<div class="maia-col-12" style="position:relative"> 
<?php
	$LINE_GRAPH = "//dgrad/snp/snp_rapport.php?danut=";
	$LINE_GRAPH = "//dgradru.delardc.com/snp_rapport.php?danut=";
	
	require_once("liste_Classes.php");
	$defautl_taille = 10;
	if(isset($_GET["class"])){
		$taille 	= isset($_GET["t_pg"])?$_GET['t_pg']:$defautl_taille;
		$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
		$id_liste 	= "id_liste_classes";
		$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
		$limit 	= " LIMIT $d,$taille ";
		$classe = $_GET["class"];
		$where 	= " where is_deleted = 0 ";
		eval("$classe::\$limit = \$limit;\$u = new $classe();\$u->liste(true);");
		// eval("paginner($classe::\$count,\$active_pg,\$taille,\$classe,\$id_liste);");
		
	}
	else if(isset($_GET["page"]) and $_GET["page"]!=""){
		require_once("$_GET[page].php");
	}
	else if(isset($_GET["autre_class"]) and $_GET["liste"]!=""){
		$taille 	= isset($_GET["t_pg"])?$_GET['t_pg']:$defautl_taille;
		$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
		$id_liste 	= "id_liste_classes";
		$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
		$limit 	= " LIMIT $d,$taille ";
		$classe = $_GET["autre_class"];
		$where 	= " where is_deleted = 0 ";
		switch($classe){
			case "CarnetAttribuer":
				$url = "action=liste&amp;class=$classe&amp;active_pg=1&amp;d_pg=0&amp;t_pg=10";
				$onClick = " onClick=\"askServeur('liste_objets_ajax.php?$url','$id_liste');\" ";
				echo "<a $onClick href='#xx'>Retourner à la liste des Attributions Détaillées</a>";
				$where 	= " where car_att.is_deleted = 0 ";
				eval("\$liste = $classe::liste_objet(\$where,\" order by car.num_debut \");");
				$classe="CarnetAttribuerResum";
			break;
		}
		eval("liste_$classe(\$liste,\$classe,\$d);");
	}
	else {
		?>
		<?php echo "<h3 style='color:red;text-align:center'>".((isset($_SESSION["snp"]['conx'])and$_SESSION["snp"]['conx'])?"":((isset($_SESSION["snp"]['erreur'])and isset($_POST["bt_login"]))?$_SESSION["snp"]['erreur']:""))."</h3>";
		if(isset($_SESSION["snp"]['user']))echo "<p style='text-align:center' ><img src='".$_SESSION["snp"]['user']["src_str_img"]."' style='width:150px;margin:auto;'></p>";?>
		<form method="POST" style="width:300px;margin:auto;margin-top:75px;padding:20px;border:solid 1px;text-align:center" action="" class="maia-search ng-pristine ng-valid">
			<h3>Authentifiez-vous SVP!</h3>
			<input name="login" value="login" title="Login" placeholder="Login" type="text" required style="width:100px">
			<input name="password" title="Mot de passe" placeholder="Mot de passe" type="password" required style="width:100px;display:inline-block">
			<button name="bt_login" style="width:auto;margin:auto" class="maia-button">
			  <span class="" style="display:inline-block" >Se Connecter</span>
            </button>
          </form>
		<?php 
	}

?>

</div>
<div class="maia-col-4" id="zone_update_objet"></div>