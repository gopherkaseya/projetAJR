<?php 
	session_start();
	// print_r($_SESSION);
		require_once('model/connexion.php');
		require_once('model/chado.php');
		require_once('model/fonctions_sup.php');
		Chado::$conx = Connexion::GetConnexion();
		
		$select_style = "style='display:inline'";
		
		if(isset($_GET['operation']) and $_GET['operation']=="sup_note_acte"){
			$id_acte_note = $_GET['id_note_acte'];
			$id_note = $_GET['id_note'];
			echo "
			<form method='POST' action='traitement_sup.php' target='iframe' >
			Voulez-vous réellement supprimé?
			<input type='submit' name='bt_sup_acte_note_et_detailler' value='Supprimer ?' class='btn btn-danger'/>
			<input type='button' onClick='chargerHtml('htmk_form_sup_note_acte','');' value='Annuler ?' class='btn btn-primary' />
			
			<input type='hidden' name='id_note_acte' value='$id_acte_note' />
			<input type='hidden' name='id_note' value='$id_note' />
			</form>";
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="enreg_note_payee"){
			if($_GET["montant_a_payer"]>=$_GET['montant']){
					// if(Chado::$conx->query($req) and ($count = count($r = $pdo_result->fetchAll())))
				$req = "SELECT num_note, r.date_paiement
					FROM t_note_actes_payer np
					INNER JOIN t_note_actes na ON np.id_noteacte = na.id
					INNER JOIN t_note no ON na.id_note = no.id
					INNER JOIN t_releve r ON r.id = np.id_releve
					WHERE no.num_note not like '$_GET[num_note]'
					AND id_releve = '".$_SESSION["snp"]["id_releve"]."'
					GROUP BY no.num_note
					UNION SELECT paie_exp_num_note
					FROM t_note_actes_payer
					WHERE paie_exp_num_note not like '$_GET[num_note]'
					AND id_releve = '".$_SESSION["snp"]["id_releve"]."'
					GROUP BY paie_exp_num_note
				";
				$count=0;$max = $_SESSION["snp"]["nbr_note_max"];
				if(($pdo_result = Chado::$conx->query($req) and ($count = count($r = $pdo_result->fetchAll())) and ($count==0 or ($count<=$max or ($max==1 and $count==1 and $r[0]['num_note']=="")))));
				if($count==0 or $count<=$max or ($max==1 and $count==1 and $r[0]['num_note']=="")){
					$req = "INSERT INTO t_note_actes_payer (id_noteacte,id_releve,montant_payer,date_paie) VALUES('$_GET[id_noteacte]','$_GET[id_releve]','$_GET[montant]','$_GET[date_releve]')";
					// echo $req;
					if(Chado::$conx->exec($req)){
						$id = Chado::$conx->lastInsertId();
						echo "<b style='color:green'>OK</b>";
					}else echo "Erreur"; /* */
					
					if(Chado::$conx->errorInfo()[2]){
						echo (Chado::$conx->errorInfo()[2]).'|error';
					}
				}else echo "<div id='reslt' ><b style='color:red'>Nombre de note à enregistrer sur ce relevé est atteint. </b> $count notes ont été enregistrées /$max</div>";
			}else echo "<b style='color:red'>Erreur: Montant Invalid</b>";
			
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="return_extrai_role"){
			$tab = return_extrai_role(Chado::$conx,$_GET["list_num_note"]);
			tracer_extrait_role($tab);
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="extrait_role"){
			$tab = tableau_relance(Chado::$conx,$_GET["Liste_id"]);
			tracer_extrait_role($tab);
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="remplacer_note"){
			
			$req = "SELECT count(*) nbr FROM t_note where num_note='$_GET[num_note]' and is_deleted=0 ";
			if($pdo_result=Chado::$conx->query($req) and $row	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
				if($row['nbr']!=1)echo "Erreur: le N° $_GET[num_note] renvoi à $row[nbr] notes dans le système";
			}else echo "Erreur: le N° $_GET[num_note] ne renvoi à aucune note dans le système";		
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="filtrer_acte_ordo"){
			$opt  =""; 
			if($_GET["src_fonc"]=="opt_actes_antenne")
			$req = "SELECT a.acte,art_bud, a.id FROM t_com_serv cs 
			INNER JOIN t_acte a ON a.acte_id_service = cs.id_serv 
			WHERE cs.id_com = '$_GET[src_parm]' ";
			else $req = "SELECT a.id,a.acte,a.art_bud FROM t_acte a WHERE a.acte_id_service = '$_GET[src_parm]' ";
			$req .= " and (a.acte LIKE ".Chado::$conx->quote("%".str_replace(" ","%","$_GET[filtre]")."%")." or a.art_bud LIKE ".Chado::$conx->quote("%".str_replace(" ","%","$_GET[filtre]")."%").")";
			// echo $req;
			if($pdo_result = Chado::$conx->query($req)){
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$opt .= "<option value='$row[id]' >$row[acte] / $row[art_bud]</option>";
					// $optArray["$row[id]"] = "$row[acte] / $row[art_bud]";
				}
			}
			echo $opt;		
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="filtrer_assuj_ordo"){
			$req = "SELECT a.id,a.nom_assujetti,a.adresse_assujetti FROM t_assujetti a WHERE (a.nom_assujetti LIKE ".Chado::$conx->quote("%".str_replace(" ","%","$_GET[filtre_n]")."%")." AND a.adresse_assujetti LIKE ".Chado::$conx->quote("%".str_replace(" ","%","$_GET[filtre_a]")."%").")";
			if($pdo_result = Chado::$conx->query($req)){
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					echo "<option value='$row[id]' >$row[nom_assujetti] $row[adresse_assujetti]</option>";
				}
			}		
		}
		else if(isset($_GET['operation']) and $_GET['operation']=="form_annuler_note"){
			echo "<textarea placeholder='Annuler pour quelle raisson?'></textarea><input type='submit' value='Annuler'/>";
		}
		else if(isset($_GET["id_service"])){
			echo traiter_id_service($_GET["id_service"]);
		}
		else if(isset($_GET["id_antenne"])){
			$id_antenne = $_GET["id_antenne"];
			$opt		="";
			$id			=" id='opt_serv_antenne' ";
			$name		=" name='id_com_serv_ordo' ";
			$fonc	 	=" getOptions('id_service','opt_serv_antenne','div_opt_actes') ";
			$onChange 	=" onChange=\"$fonc\" ";
			$t = opt_serv_antenne($id_antenne);
			foreach($t as $id_com_serv=>$service)
				$opt.="<option value='$id_com_serv' >$service</option>";
			
			if(count($t)>1)
				echo "<p  style='margin:0px;' class='acte_p' >
					<label for='opt_serv_antenne'>Service</label>: <select $id $name $onChange $select_style>$opt</select><a href='#xx'><img onClick=\"$fonc\" src='nike-xxl.png' alt='OK' /></a></p></p> 
				<div id='div_opt_actes'></div>";
			else {
				echo "<input type='hidden' value='$id_com_serv' $name />
				<p  style='margin:0px;' class='acte_p' ><label for='opt_com_cbordo'>Service</label>:  $service </p> ";
				echo traiter_id_service($id_com_serv);
			}
		}