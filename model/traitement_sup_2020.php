<?php 
	session_start();
	// print_r($_SESSION);
		require_once('model/connexion.php');
		$conx = Connexion::GetConnexion();
		// header("Content-type: text/javascript");
		if(isset($_GET["action"]) and $_GET["action"] == "new-assu"){
			$nom = $_GET["nom"];
			$nif = $_GET["nif"];
			$adr = $_GET["adr"];
			if($nom and $adr){
				$nom = $conx->quote($nom);$nif = $conx->quote($nif);$adr = $conx->quote($adr);	
				$req = "INSERT INTO `t_assujetti`( `nom_assujetti`, `adresse_assujetti`, `nif`, `is_deleted`,id_visite) VALUES ($nom,$adr,$nif,0,0)";						
				if($conx->exec($req)){
					$_SESSION['t_assujetti']['id'] = $id = $conx->lastInsertId();
					echo 'Assujetti enregistre avec succes!|success';
				}
				else if($nif){
					$req = "select * from t_assujetti where nif = $nif ";
					$t = $conx->query($req);
					$t = $t->fetch(PDO::FETCH_ASSOC);
					if($t){
						echo $t['nom_assujetti']." ".$t['adresse_assujetti']." ".$t['nif']."|existe|";
						$_SESSION['t_assujetti']['id'] = $t['id'];
					}
					
				} 
				if($conx->errorInfo()[2]){
					echo ($conx->errorInfo()[2]).'|error';
				}
			}
			else echo 'remplissez les champs vides svp!|vide';
			
		}
		else echo "nothing";
		
		
		