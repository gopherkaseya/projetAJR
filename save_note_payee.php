<?php 
	session_start();
	header("Content-type: text/javascript");
	// print_r($_SESSION);
	require_once('model/classes.php');
	// traitement du numéro de la note en cas d'un encaissement
	if(isset($_POST["img"]) and isset($_POST["banque"]) and isset($_POST["date"])){
		$r["result"]="fail";
		$conx = Connexion::GetConnexion();
		$banque = $_POST["banque"];
		$id_user = $_SESSION['snp']['user']['id'];
		$id_visite = isset($_SESSION['user']['id_visite'])?$_SESSION['user']['id_visite']:0;
		$req = "SELECT `id` FROM `t_banque` WHERE  `is_deleted`=0 and nom_banque LIKE '$banque' ";
		if(($pdo_result = $conx->query($req) and $row = $pdo_result->fetch())){
			$id_banque = $row["id"];
			$date = $_POST["date"];
			$img = $_POST["img"];
			$tt = explode(".",$img);
			$ext =  end($tt);
			$copie="bq_$id_banque"."_$date.$ext";
			if(copy("$img","releves_traiter/$copie")){
			unlink("$img");
			$req="INSERT INTO `t_releve`(`id_banque`, `date_paiement`, `id_user`, `valide`, `img_releve`, `en_cours_dedition`, `is_deleted`, `id_visite`) VALUES ($id_banque,'$date',$id_user,1,'$copie',0,0,$id_visite)";
			if($conx->exec($req)){ // enregistrement du rélevé
				$id_releve = $conx->lastInsertId();
			
				// on apure un acte se trouvant sur une note
				// on recupère toutes les notes
				for($i=1;$i!=11;$i++){
					if($_POST["n$i"]=="")continue;
					$num = $_POST["n$i"];
					$req="SELECT na.id , id_note, montant_acte m FROM `t_note_actes` na INNER JOIN t_note n on na.id_note=n.id WHERE n.is_deleted=0 and na.is_deleted=0 and n.num_note LIKE '$num' and (date_invalidation='' or date_invalidation is null) ";
					if($pdo_result_nt_acte = $conx->query($req)){
						// vérifie si la note a due être payée
						$req="SELECT nap.id,nap.id_releve,date_paiement dp,nom_banque b FROM `t_note_actes_payer` nap
						inner join t_note_actes na on na.id=nap.id_noteacte
						inner join t_note n on n.id=na.id_note
						inner join t_releve r on r.id = nap.id_releve
						inner join t_banque bq on bq.id=r.id_banque
						where n.num_note between $num and $num ";
						if($pdo_result = $conx->query($req)){
							$Id=array();
							while($row = $pdo_result->fetch()){$id_rel=$row["id_releve"]; $Id[]=$row["id"];}
							if($Id and $id_rel!=$id_releve){
								$req="update t_note_actes_payer set id_releve=$id_releve where id in (".implode(",",$Id).") ";
								if(!$conx->exec($req)){
									$r["message"]="<br>La note $num est non encaissée!";
								}else $r["result"]="success";
							}							
						}
						else{
							$values=array();
							while($row = $pdo_result_nt_acte->fetch()){
								$id_noteacte = $row["id"];
								$montant = $row["m"];
								$values[]="($id_noteacte,$id_releve,'$montant',now(),0,$id_visite)";
							}						
							$req="INSERT INTO `t_note_actes_payer`(`id_noteacte`, `id_releve`, `montant_payer`, `date_enreg`, `is_deleted`, `id_visite`) VALUES ".implode(",",$values)."; ";
							if(!$conx->exec($req)){
								$r["message"]="<br>La note $num est non encaissée!";
							}else $r["result"]="success";
						}						
					}else $r["message"]="<br>La note $num est non enregistrée!";
				}				
			}else $r["message"]="Rélevé non enregistré!";
			}else $r["message"]="$img";
			
		}else $r["message"]="Banque non enregistrée!";
		echo json_encode($r);
	}//else echo"erreur";
	//print_r($_POST);
	/* SELECT nap.id,nap.id_releve,date_paiement dp,nom_banque b FROM `t_note_actes_payer` nap
inner join t_note_actes na on na.id=nap.id_noteacte
inner join t_note n on n.id=na.id_note
inner join t_releve r on r.id = nap.id_releve
inner join t_banque bq on bq.id=r.id_banque
where n.num_note between 6349774 and 6349774 */
	