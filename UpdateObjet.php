<?php 		
		require_once('model/classes.php');
		Chado::$conx = Connexion::GetConnexion();
		// print_r($_GET);
		if(isset($_GET["class"]) and (($classe = $_GET["class"])!="")){
			eval("\$o = new $classe();");
			switch($_GET["opt"]){
				case "voir":
					$o->detail($_POST['id']=$_GET['id']);
				break;
				case "detail_perso":
					$o->detail_personnaliser($_POST['id']=$_GET['id']);
				break;
				case "ajt":
					$o->form($_GET["opt"],array());
				break;
			}
		}