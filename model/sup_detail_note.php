<?php
	// carnet attribuer
	function liste($ajout){
				echo "
				<div id='".self::$id_div_crud."' ></div>
				<div class='div_recherche' >";
						self::form_rech_rapide();
						if($ajout)echo self::bt_ajout(self::$bt_class,'Ajout',false);
						self::form_rech_avancee();
				echo "
				<form method='POST' action='traitement_sup.php' target='iframe' style='border:solid 0px;margin:0;display:inline'>
					<input type='text' name='num_note' class='form-control' title='N° Note'  placeholder='N° Debut Carnet' required style='padding: 8px;width:100px' />
					<input type='submit' name='bt_note_restantes' class='btn maia-button' style='height:35px' value='Note Non depossées' />
				</form>";
				echo '</div>';
				$this->table_liste();
		}
	public static function liste_objet($where,$limit){
			$ch_id = "c_at.id";
			$Select = "SELECT $ch_id, car.num_debut ,crl.date_lot ,crl.lot_description ,car.souche ,car.date_epuisement ,co.commune ,ser.service ,c_at.date_attribution,ser.id id_service,co.id id_commune FROM ".self::$table ." c_at ";
			$req = "$Select 
				INNER JOIN t_carnet car ON  c_at.id_carnet = car.id  
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id 
				LEFT JOIN t_commune co ON  c_at.id_commune = co.id 
				LEFT JOIN t_service ser ON  c_at.id_service = ser.id  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." c_at ";
			$req_count = "$count 
				INNER JOIN t_carnet car ON  c_at.id_carnet = car.id  
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id 
				LEFT JOIN t_commune co ON  c_at.id_commune = co.id 
				LEFT JOIN t_service ser ON  c_at.id_service = ser.id  $where ";
			if($pdo_result = self::$conx->query($req_count)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			if($pdo_result = self::$conx->query($req)){
				$rows = array();
				while($row	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$rows[] = $row;
				}
				return $rows;
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
			
	// Note
	function detail($id){
			$ch_id = "no.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id,no.is_deleted, ass.nom_assujetti ,ass.id id_ass,ass.adresse_assujetti ,co.commune ,no.num_bap,no.montant_bap,no.note_to,no.num_note,no.date_ordo,no.date_depot,no.date_invalidation,no.raison_invalidation,no.remplacement_de,no.raison_remplacage,no.date_save,no.observation FROM ".self::$table ." no ";
			$req = "$Select 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				$id = $row["id"];
				$tr_actes="";
				$req = "SELECT ac.acte ,ser.service ,ac.art_bud , nac.montant_acte,nac.freq,nac.ajouter_le FROM t_note_actes nac 
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  where nac.id_note='$id' ";//echo $req;
				$pdo_result = self::$conx->query($req);$mont=0;
				while($r = $pdo_result->fetch()){
					$tr_actes .= "<tr><td>$r[service]&nbsp;&nbsp; <i style='float:right;color:#396bbc;font-weight:bold'> $r[acte] / Féq:$r[freq]; $r[art_bud]</i></td><td style='text-align:right'>$r[montant_acte]</td></tr>";
					$mont+=$r['montant_acte'];
				}
				
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px;width:100%' >
				<table style='' ><caption style='".self::$style_caption."background-color:#f8f8f8;color:#000;'><h4>$row[commune] / NP: $row[num_note] de <u>$row[nom_assujetti] $row[adresse_assujetti]</u></h4></caption>
				<tr><td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>Dates</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Ordonnancée le</th><td>:</td><td>$row[date_ordo]</td></tr>
					<tr><th>Déposée le</th><td>:</td><td>$row[date_depot]</td></tr>
					<tr><th>Enregistrée le:</th><td>:</td><td>$row[date_save]</td></tr>
					</tbody>
				</table>
				</td>".("$row[num_bap]"==""?"":"<td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>BAP</b></caption>
					<tbody style='text-align:left'>
					<tr><th>N°Bap</th><td>:</td><td>$row[num_bap]</td></tr>
					<tr><th>Mont.BAP</th><td>:</td><td>$row[montant_bap]</td></tr>
					</tbody>
				</table>
				</td>")."<td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>Actes</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Service & Acte Gén.</th><th>Montant</th></tr>
					$tr_actes
					<tr><td colspan='3'>
						<form method='POST' action='traitement_sup.php' target='iframe' > 
							<input type='hidden' value='$id' name='id_note' />
							<input type='hidden' value='$row[num_note]' name='num_note' />
							<input type='hidden' value='$row[date_ordo]' name='date_ordo' />
							<input type='hidden' value='$row[id_ass]' name='id_ass' />
							<div id='div_form_ajt_acte_ordo' ></div>
						</form>
						<form method='POST' action='traitement_sup.php' target='iframe' style=''>
							<input type='hidden' name='num_note' value='$row[num_note]' />
							<input type='submit' name='bt_ajt_acte_ordo' value='Ajouter Acte' />
						</form>
					</td></tr>
					</tbody>
				</table>
				</td></tr>
				<tr><td colspan='3' style='text-align:left;' >Observation: $row[observation]
				".($row["note_to"]!=""?"<br>- Taxation d'office":"")."
				".($row["remplacement_de"]!=""?"<br>- A remplacé la note $row[remplacement_de]; cause: $row[raison_remplacage]":"")."".($row["is_deleted"]?("<br>- A été supprimée"):"")."
				<br>- Montant de la note: $mont Fc
				</td></tr>
				</table>";
				
				if($row["is_deleted"]){
					$r = self::liste_objet(" where remplacement_de='$row[num_note]' ","");
					if(count($r)){
						echo "<h3 style='color:red'>La note a été supprimée et ramplacée par celle-ci</h3>";
						self::detail_note_sup($r[0]["id"]);}
				}
				echo"<div style='text-align:right'>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='float:left;display:inline-block;margin-right:5px'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Note ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$id' />".self::$marqueur." </form>":"");
				
				echo "&nbsp; <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='float:left;display:inline;margin-right:5px' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur."&nbsp;</form> &nbsp;<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='float:left;display:inline;margin-right:5px' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo "&nbsp;<label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='float:left;display:inline-block' >Retourner à la liste de Note.</label>
				<br/><br/>
				</div>
				";
				
				echo"</div></div>";
				
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>";
				echo "</div>";
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		function detail_note_sup($id){
			$ch_id = "no.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id,no.is_deleted, ass.nom_assujetti ,ass.id id_ass,ass.adresse_assujetti ,co.commune ,no.num_bap,no.montant_bap,no.note_to,no.num_note,no.date_ordo,no.date_depot,no.date_invalidation,no.raison_invalidation,no.remplacement_de,no.raison_remplacage,no.date_save,no.observation FROM ".self::$table ." no ";
			$req = "$Select 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				$id = $row["id"];
				$tr_actes="";
				$req = "SELECT ac.acte ,ser.service ,ac.art_bud , nac.montant_acte,nac.freq,nac.ajouter_le FROM t_note_actes nac 
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  where nac.id_note='$id' ";//echo $req;
				$pdo_result = self::$conx->query($req);$mont=0;
				while($r = $pdo_result->fetch()){
					$tr_actes .= "<tr><td>$r[service]&nbsp;&nbsp; <i style='float:right;color:#396bbc;font-weight:bold'>$r[acte] / $r[art_bud]</i></td><td style='text-align:right'>$r[montant_acte]</td></tr>";
					$mont+=$r['montant_acte'];
				}
				
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px;width:100%' >
				<table style='' ><caption style='".self::$style_caption."background-color:#f8f8f8;color:#000;'><h4>$row[commune] / NP: $row[num_note] de <u>$row[nom_assujetti] $row[adresse_assujetti]</u></h4></caption>
				<tr><td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>Dates</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Ordonnancée le</th><td>:</td><td>$row[date_ordo]</td></tr>
					<tr><th>Déposée le</th><td>:</td><td>$row[date_depot]</td></tr>
					<tr><th>Enregistrée le:</th><td>:</td><td>$row[date_save]</td></tr>
					</tbody>
				</table>
				</td>".("$row[num_bap]"==""?"":"<td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>BAP</b></caption>
					<tbody style='text-align:left'>
					<tr><th>N°Bap</th><td>:</td><td>$row[num_bap]</td></tr>
					<tr><th>Mont.BAP</th><td>:</td><td>$row[montant_bap]</td></tr>
					</tbody>
				</table>
				</td>")."<td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>Actes</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Service & Acte Gén.</th><th>Montant</th></tr>
					$tr_actes
					<tr><td colspan='3'>
						<form method='POST' action='traitement_sup.php' target='iframe' > 
							<input type='hidden' value='$id' name='id_note' />
							<input type='hidden' value='$row[num_note]' name='num_note' />
							<input type='hidden' value='$row[date_ordo]' name='date_ordo' />
							<input type='hidden' value='$row[id_ass]' name='id_ass' />
							<div id='div_form_ajt_acte_ordo' ></div>
						</form>
						<form method='POST' action='traitement_sup.php' target='iframe' style=''>
							<input type='hidden' name='num_note' value='$row[num_note]' />
							<input type='submit' name='bt_ajt_acte_ordo' value='Ajouter Acte' />
						</form>
					</td></tr>
					</tbody>
				</table>
				</td></tr>
				<tr><td colspan='3' style='text-align:left;' >Observation: $row[observation]
				".($row["note_to"]!=""?"<br>- Taxation d'office":"")."
				".($row["remplacement_de"]!=""?"<br>- A remplacé la note $row[remplacement_de]; cause: $row[raison_remplacage]":"")."".($row["is_deleted"]?("<br>- A été supprimée"):"")."
				<br>- Montant de la note: $mont Fc
				</td></tr>
				</table>";
				
				echo"</div></div>";
				if($row["is_deleted"]){
					$r = self::liste_objet(" where remplacement_de='$row[num_note]' ","");
					if(count($r))self::detail_note_sup($r[0]["id"]);
					// "<br>- A été supprimée".(  " cause: $row[raison_remplacage]")):""
				}
				
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>";
				echo "</div>";
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		function detail_personnaliser($id){
			$ch_id = "no.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap,no.montant_bap,no.note_to,no.num_note,no.date_ordo,no.date_depot,no.date_invalidation,no.raison_invalidation,no.remplacement_de,no.raison_remplacage,no.date_save,no.observation FROM ".self::$table ." no ";
			$req = "$Select 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur Note</b></caption>Assujetti : $row[nom_assujetti] Adresse Assujetti : $row[adresse_assujetti] Antenne : $row[commune] N°Bap : $row[num_bap] Mont.BAP : $row[montant_bap] TO ? : <input type='checkbox' readonly ".($row["note_to"]?'checked':'')." title=\"TO ?\" /> N°N.P. : $row[num_note] Date Ordo. : $row[date_ordo] Date Dépot : $row[date_depot] Invalidée le : $row[date_invalidation] Invalidée pour Cause : $row[raison_invalidation] NP remplacée : $row[remplacement_de] Remplacée pour cause : $row[raison_remplacage] Enregistrée le : $row[date_save] 
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Note ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de Note</label>";
				
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>";
				echo "</div>";
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		function detail_note_paiement($id){
			$ch_id = "no.id";
			$where = "WHERE  $ch_id = '$id'";
			$Select = "SELECT $ch_id,note_to,no.date_ordo,no.date_depot,ass.id id_ass,ass.adresse_assujetti ,co.commune ,ass.nom_assujetti ,remplacement_de ,raison_remplacage ,no.num_note,no.num_bap,no.montant_bap,no.date_save FROM ".self::$table ." no ";
			$req = "$Select 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id $where ";
			
			echo"<div id='".self::$id_div_crud."' style='' >";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				$id = $row["id"];	
				$num_note = $row["num_note"];
				$tr_actes="";
				$req = "SELECT nac.id, ac.acte ,ser.service ,ac.art_bud , nac.montant_acte,nac.freq,nac.ajouter_le, date_paiement,nom_banque,montant_payer
				FROM t_note_actes nac 
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				Left join t_note_actes_payer n_ap on n_ap.id_noteacte = nac.id
				left join t_releve rlv on rlv.id = n_ap.id_releve
				left join t_banque b on b.id = rlv.id_banque
				where nac.id_note = '$id'
				";
				// echo $req;
				$pdo_result = self::$conx->query($req);$mont=$mont_p=0;
				
				$relv = self::$conx->query(" select * from t_releve  where id = ".$_SESSION["snp"]["id_releve"]);
				$date_paiement = $relv->fetch()['date_paiement'];
				while($r = $pdo_result->fetch()){
					$fonc = "enreg_note_payee('$r[id]','".$_SESSION["snp"]["id_releve"]."','$r[montant_acte]','$num_note','$date_paiement')";
					$rest = $r['montant_acte']-$r['montant_payer'];
					$tr_actes .= "<tr><td>$r[service]&nbsp;&nbsp; <i style='float:right;color:#396bbc;font-weight:bold'>$r[acte]</i></td>
					".($r["montant_payer"]?
					"<td colspan='2' >".($rest==0?"<b style='color:green'>$r[montant_payer] <span style='color:#000'>payé à la</span> $r[nom_banque], <span style='color:#000'> le</span> $r[date_paiement]</b>":("$r[montant_acte] - $r[montant_payer] = <a href='#xx' title='Acte payé à moitié, reste: $rest'><b style='color:red' >$rest</b></a> payé à la $r[nom_banque], le $r[date_paiement]"))."</td>":
					"<td style='text-align:right'>
					<input name='montant_$r[id]' value='$r[montant_acte]' id='montant_acte_payer_$r[id]' style='width:75px;margin:0;padding:0' />
					</td>
					<td id='link_bt_payer_acte_$r[id]'><a href='#xx'><img onClick=\"$fonc\" title='valider paiement' src='nike-xxl.png' alt='OK' /></a></td>")."</tr>";
					$mont_p+=$r['montant_payer'];
					$mont+=$r['montant_acte'];
				}
				echo"
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table style='' ><caption style='".self::$style_caption."background-color:#f8f8f8;color:#000;'><h4>NP: $row[num_note] de <u>$row[nom_assujetti] $row[adresse_assujetti]</u></h4></caption>
				<tr><td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>Dates</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Ordonnancée le</th><td>:</td><td>$row[date_ordo]</td></tr>
					<tr><th>Déposée le</th><td>:</td><td>$row[date_depot]</td></tr>
					<tr><th>Enregistrée le:</th><td>:</td><td>$row[date_save]</td></tr>
					</tbody>
				</table>
				</td>".("$row[num_bap]"==""?"":"<td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>BAP</b></caption>
					<tbody style='text-align:left'>
					<tr><th>N°Bap</th><td>:</td><td>$row[num_bap]</td></tr>
					<tr><th>Mont.BAP</th><td>:</td><td>$row[montant_bap]</td></tr>
					</tbody>
				</table>
				</td>")."<td style='vertical-align: top;'>
				<table><caption style='".self::$style_caption."'><b>Actes</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Service & Acte Gén.</th><th>Montant</th><th>Valider</th></tr>
					$tr_actes
					<tr><td colspan='3'>
						.".($mont_p == $mont?"":"<form method='POST' action='traitement_sup.php' target='iframe' > 
						<input type='hidden' value='$id' name='id_note' />
						<input type='hidden' value='$row[num_note]' name='num_note' />
						<input type='hidden' value='$row[date_ordo]' name='date_ordo' />
						<input type='hidden' value='$row[id_ass]' name='id_ass' />
						<div id='div_form_ajt_acte_ordo' ></div>
						</form>
						<form method='POST' action='traitement_sup.php' target='iframe' style=''>
							<input type='hidden' name='num_note' value='$row[num_note]' />
							<input type='submit' name='bt_ajt_acte_ordo' value='Ajouter Acte' />
						</form>")."
					</td></tr>
					</tbody>
				</table>
				</td></tr>
				<tr><td colspan='3' style='text-align:left;' >Observation: <br>
				".($row["note_to"]!=""?"<br>- Taxation d'office":"")."
				".($row["remplacement_de"]!=""?"<br>- A remplacé la note $row[remplacement_de]; cause: $row[raison_remplacage]":"")."
				<br>- Montant payé: <b style='font-size:18px'>$mont_p / $mont</b> F
				</td></tr></table>
				";
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>";
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
			echo "</div>";
			
		}
		