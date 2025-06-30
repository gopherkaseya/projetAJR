<?php 
	session_start();
	// print_r($_SESSION);
		require_once('model/classes.php');
		new Chado(Connexion::GetConnexion());
		// var_dump(Chado::$conx);
		// traitement du numéro de la note en cas d'un encaissement
		if(isset($_POST["bt_num_note_releve"])){
			$num = $_POST["num_note"];$service = "";
			$req = "select * from t_note where num_note LIKE '$num' and is_deleted=0 ";$row=array();
			// on vérifie si la note a déjà été ordonnancée
			// 1. si la note n'a pas encore été ordonnancée (alors on l'enregistre sous un nouveau formulaire)
			if(!($pdo_result = Chado::$conx->query($req) and $row = $pdo_result->fetch())){
				
				echo "<div id='form' >";
					echo "
					<h2 style='text-align:lfet;padding-bottom:0;margin-bottom:0'>Recouvrement de la note $num </h2>
					<em style='color:red' >NB. Cette note n'a pas encore été enregistrée au bureau Ordo.</em><br><br>
					<input type='hidden' value='$num' name='paie_exp_num_note' />
					<input type='hidden' value='0' name='id_noteacte' />
					<input type='hidden' value='".($_SESSION["snp"]["id_releve"])."' name='id_releve' />
					
					<label style='width:100px;float:left' for='' >Assujetti</label>: <select style='width:300px' name='paie_exp_id_assujetti' >".Assujetti::options("",0)."</select><br/><br/>
					<label style='width:100px;display:inline-block;' >Acte Géné</label>: <select style='width:300px' name='paie_exp_id_acte' >".Actes::options("",0)."</select>
					
				<p style='margin:0px;display:block' class='acte_p' >
				<label style='width:100px' for='paie_exp_date_ordo' >Date Ordo</label>:
				<input type='text' title=\"Date Ordo\" placeholder=\"Date Ordo\" value=\"".date("Y-m-d")."\" id='paie_exp_date_ordo' name='paie_exp_date_ordo' required  class='' style='width:300px;' />
				</p>
				<p style='margin:0px;display:block' class='acte_p' >
				<label style='width:100px' for='montant_acte' >Mont. Acte</label>:
				<input type='text' title=\"Mont. Acte\" placeholder=\"Mont. Acte\" id='montant_acte' name='montant_payer' required  class='' style='width:300px;' />
				</p>
				<p  style='margin:0px;' class='acte_p' >
					<label style='width:100px' for='frequence_acte'>Fréqence.</label>:
					<input type='text' title=\"Fréq.\" placeholder=\"Fréq.\" value='1'  id='frequence_acte' name='frequence_acte' required  class='' style='width:300px;' />
				</p>
				<p style='margin:0px;width:300px;' class='acte_p' ><label style='width:100px' for='montant_acte' >&nbsp;</label>&nbsp;
				<input type='submit' id='' name='bt_valider_paiement_exp_note' value=\"Valider Paiement Acte\" class='maia-button' /></p>
				<p id='id_rapport_save_acte_ordo'></p>";
				echo "</div>";
				Chado::chargerHtml('form','div_operation_releve');
				
			}
			else{
				$n = new Note();
				$n->detail_note_paiement($row['id']);
				Note::chargerHtml(Note::$id_div_crud,'div_operation_releve');
				echo Note::$notification ;
			}
		}
		
		else if(isset($_POST["bt_num_note"])){
			$num = $_POST["num_note"];$service = "";
			$req = "select * from t_note where num_note LIKE '$num' ";$row=array();
			if(!($pdo_result = Chado::$conx->query($req) and $row = $pdo_result->fetch())){
				echo "<div id='form1' ></div>";
				Chado::chargerHtml('form1',Note::$id_div_crud);
				
				echo "<div id='reslt' >";
					$form = traiter_ordo_num_note($num);
					if($form)echo "
					<div style='margin-bottom:5px;padding:5px 20px;border:solid 1px blue;heightt:60px'>
						<p>Ordonnancement en série, Cochez la case et</br>indiquez le N° de début de la série:</p>
						<label for='ordo_serie' style='display:inline'>Ordo série?</label> <input type='checkbox' title='ordonnancer en série' name='ordo_serie' id='ordo_serie' />
						<input placeholder='nombre notes' title='nombre note à ordonnancer en série' name='nbr_ordo_serie' style='width:100px' />
					</div>
					<input type='submit' name='bt_valider_ajt_note_ordo' value='Ordonnancer la note' class='btn btn-primary  maia-button' >";
				echo "</div>";
				Chado::chargerHtml('reslt','div_form_ordo');
				//echo "<script>".Chado::autoCompleter('opt_actes_ordo')."</script>";
				
				echo "<div id='formi' >";
					echo "<style >#bt_valider_ajt_acte_ordo{display:none}</style>";
					echo "".($form?("<h2 style='text-align:center'>Ordonnancement de la note $num </h2><input type='hidden' value='$num' name='num_note' />".form_note_ordo()):"");
				    
				echo "
				</div>
				";
				Chado::chargerHtml('formi','div_form_ordo_notee');echo "
				    <script>
				        window.top.window.select_2_assujetti();
				        //$('#id_assujetti_ordo').select2(); console.log('TOUT VA BIEN');
				        "/*.Chado::autoCompleter('id_assujetti_ordo')*/."
				    </script>";
				
			}else{
				echo "<div id='form' ></div>";
				Chado::chargerHtml('form','div_form_ordo');
				Chado::chargerHtml('form','div_form_ordo_notee');
				Chado::chargerHtml('form','div_form_ordo_note');
				$n = new Note();
				$n->detail($row['id']);
				Note::chargerHtml(Note::$id_div_crud,Note::$id_div_crud);
			}
		}
		
		else if(isset($_POST['bt_sup_acte_note_et_detailler'])){
			$req = "UPDATE ".NoteActes::$table." SET is_deleted=1 WHERE id = '$_POST[id_note_acte]';";
			Chado::$conx->exec($req);
			$n = new Note();$n->detail($_POST['id_note']);
			Note::chargerHtml(Note::$id_div_crud,Note::$id_div_crud);
			exit();
		}
		// demande d'ajout d'un acte à une note déjà ordonnancée
		else if(isset($_POST["bt_ajt_acte_ordo"])){
			$num_note = $_POST["num_note"];$service = "";
			echo "<div id='reslt' >";
			$where = " where '$num_note' BETWEEN car.num_debut AND (car.num_debut+49) ";
			$Rslt = CarnetAttribuer::liste_objet($where," order by co.commune");
			// Analyse du résultat
			{
				echo "<style >#bt_valider_ajt_acte_ordo{display:block}</style>";
				$pour_compte_de="";
				if(count($Rslt)==0){
					echo"<b style='color:red'>Cette note n'appartient à aucun carnet déjà attribué.</b><br/>Début du carnet: ".(((int)$num_note)-($num_note%50)+1);
					return false;
				}
				else if(count($Rslt)==1 ){
					// 1.
					if($Rslt[0]["commune"]=="Centre"){
						$endroit = " du Centre ".$Rslt[0]["service"];
						$opt_actes = opt_actes_service($Rslt[0]["id_service"]);
						$pour_compte_de = "<input type='hidden' name='pr_cpt_de_id_com' value='1' />";
						$src_fonc= "opt_actes_service";
						$src_parm= $Rslt[0]["id_service"];
					}
					// 2.
					else if($Rslt[0]["commune"]=="CB.ORDO"){
						$endroit = "du CB.Ordo.";
						$opt_actes = opt_actes_antenne($Rslt[0]["id_commune"]);
						$pour_compte_de = "Ordonnancer pour compte de: <select name='pr_cpt_de_id_com' style='' title='Ordonnancer pour compte de:' >". Commune::options(" ",22)."</select>";
						$src_fonc= "opt_actes_antenne";
						$src_parm= $Rslt[0]["id_commune"];
					}
					// 3.
					else {//print_r($Rslt);
						$endroit = "de l'antenne ".$Rslt[0]["commune"];
						$opt_actes = opt_actes_antenne($Rslt[0]["id_commune"]);
						$pour_compte_de = "<input type='hidden' name='pr_cpt_de_id_com' value='".$Rslt[0]["id_commune"]."' />";
						$src_fonc= "opt_actes_antenne";
						$src_parm= $Rslt[0]["id_commune"];
					}
					echo"<h2 style='text-align:center'><b style='color:blue'>Note $endroit</b></h2>
					$pour_compte_de
					<input type='hidden' id='source_opt_acte_ordo_fonc' value='$src_fonc' />
					<input type='hidden' id='source_opt_acte_ordo_parm' value='$src_parm' />
					".show_ordo_combo_acte($opt_actes);
					
				}
				// 4. on parcourt les service et antenne concernet par le carnet et on les affiche
				else {
					echo "<h2 style='text-align:center'><b style='color:blue'>La note vient d'un carnet attribué à: </b></h2>";
					foreach($Rslt as $c)
						echo $c["commune"]=="Centre"?" Centre $c[service]; ":($c["commune"]=="CB.ORDO"?" CB.ORDO; ":(" l'antenne ".$c["commune"]."; "));
					echo "Ordonnancer pour compte de: <select name='pr_cpt_de_id_com' style='' title='Ordonnancer pour compte de:' >". Commune::options(" ",1)."</select>";
					
				}
			}
			echo "</div>";
			Chado::chargerHtml('reslt','div_form_ajt_acte_ordo');
		}
		// validation d'ajout d'un acte sur une note déjà ordonnancée
		else if(isset($_POST["bt_valider_ajt_acte_ordo"])){
			echo "<div id='reslt' >";
				if(isset($_POST["id_note"])and$_POST["id_note"]!=""){
					$id_ass = $_POST["id_ass"];$note = $_POST["num_note"];
					$dt_ord = $_POST["date_ordo"];
					if($id_noteacte = enregistrer_note_acte($_POST["id_note"])){
						$id_acte = $_POST["id_acte"];
						$req = " UPDATE t_note_actes_payer SET id_noteacte='$id_noteacte'
						where is_deleted=0 and paie_exp_num_note='$note' and paie_exp_id_acte='$id_acte' and paie_exp_id_assujetti='$id_ass' and paie_exp_date_ordo='$dt_ord' ";
						if(!Chado::$conx->exec($req)){
							$req = "select * from t_note_actes_payer where is_deleted=0 and paie_exp_num_note='$note' ";
							if($pdo_result = Chado::$conx->query($req) and ($row = $pdo_result->fetch(PDO::FETCH_ASSOC))){
								echo "<br/>La note $note est déjà apurée, mais avec des éléments contradictoire.<br/>L'apurement automatique a échoué.";
							}
						}
					}/*  */
				}
				else echo "<b style='color:red'> Cet Acte n'est lié à aucune Note!</b>";
			echo "</div>";
			Chado::chargerHtml('reslt','id_rapport_save_acte_ordo');
		}
		// validation d'ajout d'une note ainsi que le premier acte de la note
		else if(isset($_POST["bt_valider_ajt_note_ordo"])){
			$note=$_POST["num_note"];$erreur ="";
			if(isset($_POST["ordo_serie"])and !empty($_POST["nbr_ordo_serie"])and ($nbr=$_POST["nbr_ordo_serie"])){
				if(((int)$nbr)>0){
					if(!empty($_POST["id_assujetti"]))enregistrer_note_enserie($note,$nbr,$_POST["date_ordo"],$_POST["date_depot"],$_POST["pr_cpt_de_id_com"],$_POST["montant_acte"],$_POST["id_acte"],$_POST["id_assujetti"],$_POST["frequence_acte"]);
					else  $erreur =  "<div id='reslt1' ></div><b style='color:red'>Assujetti invalid!.</b></div>";
				}
				else $erreur =  "<div id='reslt1' ></div><b style='color:red'>Nombre de notes invalid!.</b></div>";
			}
			else if(!isset($_POST["ordo_serie"]) and (""==$_POST["nbr_ordo_serie"])){
				$req = "select * from t_note where num_note LIKE '$note' and is_deleted=0 ";$row=array();
				if(!($pdo_result = Chado::$conx->query($req) and $row = $pdo_result->fetch())){
						if(!isset($_POST["id_note"])or $_POST["id_note"]==""){
							$id_assujetti=$_POST["id_assujetti"];
							$dt_ord=$_POST["date_ordo"];
							$dt_dpt=$_POST["date_depot"];
							$num_bap=$_POST["num_bap"];
							$mnt_bap=$_POST["montant_bap"];
							$_POST["id_note"] = enregistrer_note();
							if($_POST["remplacement_de"]!=""){
								
								$req = "update t_note set is_deleted='1' where num_note LIKE ".$_POST["remplacement_de"];
								$req .= ";update t_note set raison_remplacage=".Chado::$conx->quote($_POST["raison_remplacage"])." where id = ".$_POST["id_note"];
								Chado::$conx->exec($req);
							}
						}
						// enregistrement de l'acte
						if($id_noteacte = enregistrer_note_acte($_POST["id_note"])){
							$id_acte = $_POST["id_acte"];
							$req = "UPDATE t_note_actes_payer SET id_noteacte='$id_noteacte'
							where is_deleted=0 and paie_exp_num_note='$note' and paie_exp_id_acte='$id_acte' and paie_exp_id_assujetti='$id_assujetti' and paie_exp_date_ordo='$dt_ord' ";
							if(!Chado::$conx->exec($req)){
								$req = "select * from t_note_actes_payer where is_deleted=0 and paie_exp_num_note='$note' and paie_exp_id_acte='$id_acte' and paie_exp_id_assujetti='$id_assujetti' and paie_exp_date_ordo='$dt_ord' ";
								if($pdo_result = Chado::$conx->query($req) and ($row = $pdo_result->fetch(PDO::FETCH_ASSOC))){
									echo "<br/>La note $note est déjà apurée, mais avec des éléments contradictoire.<br/>L'apurement automatique a échoué.";
								}
							}
						}
						// else echo "<b style='color:red'> Cet Acte n'est lié à aucune Note!</b>";
				}
				else {
					echo "<div id='reslt1' ></div><b style='color:red'>Cette note a déjà été ordonnancée.</b></div>";$_POST["id_note"]=$row['id'];
					echo "<div id='reslt' ><div id='reslt' ><b style='color:red'>Cette note a déjà été ordonnancée.</b></div>";$_POST["id_note"]=$row['id'];
					Chado::chargerHtml('reslt','div_form_ordo_note');
					Chado::chargerHtml('reslt1','div_form_ordo');
				}
			}
			else $erreur = "<div id='reslt1' ></div><b style='color:red'>Que voulez-vous faire?<br>Pour un ordo en série, vous cochez la case 'Ordo série?' et indiquer le nombre de notes;<br> sinon vous décochez et videz le champ nombre  notes.</b></div>";
				
			echo "<div id='form' ></div>";
			Chado::chargerHtml('form','div_form_ordo_notee');
			Chado::chargerHtml('form','div_form_ordo_note');
			if($erreur){
				echo $erreur;
				Chado::chargerHtml('reslt1','div_form_ordo');
				Chado::chargerHtml('reslt1','div_form_ordo_notee');
			}else{
				Chado::chargerHtml('form','div_form_ordo');
				$n = new Note();$n->detail($_POST['id_note']);
				Note::chargerHtml(Note::$id_div_crud,Note::$id_div_crud);
			}
			
		}
		
		// enregistrement d'un paiement pour une note qui n'a pas encore été ordonnancée fait à l'avance
		else if(isset($_POST["bt_valider_paiement_exp_note"])){
			$req = "select * from t_note_actes_payer where id_releve=".Chado::$conx->quote($_POST["id_releve"])." 
			and paie_exp_num_note=".Chado::$conx->quote($_POST["paie_exp_num_note"])."
			and paie_exp_id_acte=".Chado::$conx->quote($_POST["paie_exp_id_acte"])."
			and paie_exp_id_assujetti=".Chado::$conx->quote($_POST["paie_exp_id_assujetti"])."
			";$row=array();
			if(!($pdo_result = Chado::$conx->query($req) and $row = $pdo_result->fetch())){
				$req = "SELECT num_note
					FROM t_note_actes_payer np
					INNER JOIN t_note_actes na ON np.id_noteacte = na.id
					INNER JOIN t_note no ON na.id_note = no.id
					INNER JOIN t_releve r ON r.id = np.id_releve
					WHERE no.num_note != '$_POST[paie_exp_num_note]'
					AND id_releve = '$_POST[id_releve]'
					GROUP BY no.num_note
					UNION SELECT paie_exp_num_note
					FROM t_note_actes_payer
					WHERE paie_exp_num_note != '$_POST[paie_exp_num_note]'
					AND id_releve = '$_POST[id_releve]'
					GROUP BY paie_exp_num_note 
				";
				$count=0;$max = $_SESSION["snp"]["nbr_note_max"];
				if(($pdo_result = Chado::$conx->query($req) and ($count = count($r = $pdo_result->fetchAll())) and ($count==0 or ($count<=$max or ($max==1 and $count==1 and $r[0]['num_note']=="")))));
				if($count==0 or $count<=$max or ($max==1 and $count==1 and $r[0]['num_note']=="")){
					$req = "INSERT INTO t_note_actes_payer (id_releve,montant_payer,paie_exp_num_note,paie_exp_id_acte,paie_exp_id_assujetti,paie_exp_date_ordo) VALUES(".Chado::$conx->quote($_POST["id_releve"]).",".Chado::$conx->quote($_POST["montant_payer"]).",".Chado::$conx->quote($_POST["paie_exp_num_note"]).",".Chado::$conx->quote($_POST["paie_exp_id_acte"]).",".Chado::$conx->quote($_POST["paie_exp_id_assujetti"]).",".Chado::$conx->quote($_POST["paie_exp_date_ordo"]). ");";
					if(Chado::$conx->exec($req)){
						// Chado::$id = Chado::$conx->lastInsertId();
						echo "<div id='reslt' ><b style='color:blue'>Paiement effectué!</b></div>";
					} 
					else echo "<div id='reslt' ><b style='color:red'>Erreur Paiement!</b></div>";
					/* */
				}else echo "<div id='reslt' ><b style='color:red'>Nombre de note à enregistrer sur ce relevé est atteint. </b> $count notes ont été enregistrées</div>";
			}
			else {
				echo "<div id='reslt' ><b style='color:red'>Ce paiement a déjà été enregistré!</b></div>";
			}
			Chado::chargerHtml('reslt','id_rapport_save_acte_ordo');
		}
		// enregistrement d'un paiement pour une note qui n'a pas encore été ordonnancée fait à l'avance
		else if(isset($_POST["bt_dform_annuler_note"])){
			$label = "";$num_note = "$_POST[num_note]";
			$pdo_result = Chado::$conx->query("select * from t_note where num_note LIKE $num_note ");
			if($r	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
				$dat = $r["date_invalidation"];
				$exp = $r["raison_invalidation"];
				$label = "Note $num_note; Invalidée le $dat Raison: $exp";
			}else echo "ça ne marche pas!!!";
			echo "<div id='reslt'><form id='id_form_annuler_note_' method='POST' action='traitement_sup.php' target='iframe' style='height:100px;border:solid 0px;margin:0;display:inline;'>$label<br/>
			<textarea name='explication' placeholder='Annuler la note $_POST[num_note] pour quelle raisson?' style='height:50px' required></textarea><input type='hidden' name='num_note' value='$_POST[num_note]'/><br/>
			<input type='submit' name='bt_annuler_note' class='btn maia-button' style='width:210px;height:35px;' value='Confirmer Annulation Note' /><input type='reset' class='".Note::$bt_class."' value='Terminer' style='margin-left:5px' onClick=\"cache1_et_affiche2('id_form_annuler_note_','id_form_annuler_note');\"  >
		</form></div>";
			Chado::chargerHtml('reslt','id_form_annuler_note');
		}
		else if(isset($_POST["bt_form_annuler_note"])){
			$label = "";$num_note = "$_POST[num_note]";
			$pdo_result = Chado::$conx->query("select * from t_note where num_note LIKE $num_note ");
			print_r($_POST);var_dump($_POST);
			if($r	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
				$dat = $r["date_invalidation"];
				$dat_ord = $r["date_ordo"];
				$exp = $r["raison_invalidation"];
				$label = "Note $num_note; Ordonnancée $dat_ord le Invalidée le $dat Raison: $exp";
			}else { echo "ça ne marche pas!!!//";
				if(isset($_POST['recouvrement']))
				$label = "CETTE NOTE $num_note N'EXISTE PAS <style>#id_form_annuler_note_{display:none !important;}</style>";
			
			}
			echo "<div id='reslt'>$label<form id='id_form_annuler_note_' method='POST' action='traitement_sup.php' target='iframe' style='height:100px;border:solid 0px;margin:0;display:inline;'>
			<br/>
			<textarea name='explication' placeholder='Annuler la note $_POST[num_note] pour quelle raisson?' style='height:50px' required></textarea><input type='hidden' name='num_note' value='$_POST[num_note]'/><br/>
			<input type='submit' name='bt_annuler_note' class='btn maia-button' style='width:210px;height:35px;' value='Confirmer Annulation Note' /><input type='reset' class='".Note::$bt_class."' value='Terminer' style='margin-left:5px' onClick=\"cache1_et_affiche2('id_form_annuler_note_','id_form_annuler_note');\"  >
		</form></div>";
			Chado::chargerHtml('reslt','id_form_annuler_note');
		}
		else if(isset($_POST["bt_annuler_note"])){
			$conx = Chado::$conx;
			echo "<div id='reslt'>";
			if($_POST["explication"]!="" and $_POST["num_note"]!=""){
				$exp = $_POST["explication"];$num_note = $_POST["num_note"];
				$pdo_result = $conx->query("select * from t_note where num_note LIKE $num_note ");
				// if(count($r = Note::liste_objet(" where no.num_note = $num_note ",""))){
				if($r	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$id_user_delete = isset($_SESSION['id_utilisateur'])?$_SESSION['id_utilisateur']:"";
					$req = "update t_note set date_invalidation=now(),is_deleted=1, id_user_delete='$id_user_delete' raison_invalidation=".$conx->quote($exp)." where num_note LIKE $num_note";
					if($conx->exec($req))echo "<h4 style='color:green'>Note annulée avec succès.</h4>";
					else echo "<h4 style='color:red'>Echec opération (1)!!!</h4>";
				}
				else {
					$req = "insert into t_note (is_deleted,num_note,date_invalidation,raison_invalidation) values(1,$num_note,now(),".$conx->quote($exp).") ";
					if($conx->exec($req))echo "<h4 style='color:green'>Note enregistrée et annulée avec succès.</h4>";
					else echo "<h4 style='color:red'>Echec opération (2)!!!</h4>";
				}
			}else echo "<h4 style='color:red'>Echec opération, Vous devez fournir la raison d'annulation.</h4>";
			echo "</div>";
			
			
			Chado::chargerHtml('reslt','id_form_annuler_note');
		}
		else if(isset($_POST["bt_note_restantes"])){
			$conx = Chado::$conx;
			echo "<div id='reslt'>";
			$note_encours=$note_deposee="";
			$tab_num = explode(",",$_POST["num_note"]);
			$message ="";
			foreach($tab_num as $num_note){
				if(($num_note)!="" ){
					$req = "SELECT * FROM `t_carnet` c  inner join t_carnet_attribuer ca on c.id = ca.id_carnet where num_debut = $num_note";
					$pdo_result = $conx->query($req);
					if($r = $pdo_result->fetch(PDO::FETCH_ASSOC)){
						if ($num_note%50==1){
							$str_num = "";$str_num1 = "";
							for($i=$num_note;$i!=50+$num_note;$i++)
								$str_num .= "$i - ";
							$req = "SELECT num_note n from t_note where num_note between $num_note and ".($num_note+50)." ";
							$pdo_result = $conx->query($req);$j=0;
							while($r	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
								$str_num1 .= "$r[n] - " ;
								$str_num = str_replace("$r[n] - ","",$str_num) ;
								$j++;
							}
							$note_encours.="<h2 style='color:green' >".(50-$j)." Notes En cours dans le carnet $num_note: </h2> $str_num";
							$note_deposee.="<h2 style='color:green' >$j Notes deposées du carnet $num_note: </h2>$str_num1<br/>";
							
						}else  {$message = "<h4 style='color:red'>N° Carnet Incorrecte.</h4>"; }
					}
					else {$message = "<h2 style='color:red'>Carnet non attribué Série: '$num_note'.</h2>"; }
				}else {$message = "<h4 style='color:red'>Donner le numéro du début de carnet SVP.</h4>"; }
			}
			
			echo "<div id='list_note_restantes' style='width:500px' >".($message?$message :("
			$note_encours $note_deposee
			<input type='reset' class='".Note::$bt_class."' value='Terminer, Merci' style='margin-left:5px' onClick=\"cache1_et_affiche2('list_note_restantes','id_zone_rslt_msgScript');\"  >"))."
			</div>";
			echo "</div>";
			
			
			Chado::chargerHtml('reslt','id_zone_rslt_msgScript');
		}
		 
		
		
		