<?php
	require_once("chado.php");
	class Note extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array();
		public static $table 	= 't_note';
		public static $marqueur = "<input type='hidden' name='class' value='Note' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_Note';
		public static $id_div_crud = 'id_div_crud_Note';
		public static $retourHtmlRapConfig = 'div_form_rapp_Note';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
		public static function liste_objet($where,$limit){
			$ch_id = "no.id";
			$Select = "SELECT $ch_id, ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap,no.montant_bap,no.note_to,no.num_note,no.date_ordo,no.date_depot,no.date_invalidation,no.raison_invalidation,no.remplacement_de,no.raison_remplacage,no.date_save,no.observation FROM ".self::$table ." no ";
			$req = "$Select 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." no ";
			$req_count = "$count 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $where ";
			if($pdo_result = self::$conx->query($req_count)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			if($pdo_result = self::$conx->query($req)){
				$rows = array();
				while($row	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$row['check_note_to'] = "<input type='checkbox' readonly ".($row["note_to"]?'checked':'')." title=\"TO ?\" />"; 
					$rows[] = $row;
				}
				return $rows;
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		
		function detail($id){
			$ch_id = "no.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id,no.is_deleted, ass.nom_assujetti , ass.nif ,ass.id id_ass,ass.adresse_assujetti ,co.commune ,no.num_bap,no.montant_bap,no.note_to,no.num_note,no.date_ordo,no.date_depot,no.date_invalidation,no.raison_invalidation,no.remplacement_de,no.raison_remplacage,no.date_save,no.observation FROM ".self::$table ." no ";
			$req = "$Select 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $where ";
			$pdo_result = self::$conx->query($req);
			$row = $pdo_result->fetch(PDO::FETCH_ASSOC) ;
			var_dump($pdo_result,$row,isset($row["id"]));
			print_r($pdo_result,$row,isset($row["id"]));
			if( isset($row["id"])){
				// $row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				$id = $row["id"];
				$tr_actes="";
				$ccc = $req = "SELECT ac.acte ,ser.service ,ac.art_bud , nac.montant_acte,nac.freq,nac.ajouter_le FROM t_note_actes nac 
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  where nac.is_deleted=0 and nac.id_note='$id' ";//
				$pdo_result = self::$conx->query($req);$mont=0;
				while($r = $pdo_result->fetch()){
					$tr_actes .= "<tr><td>$r[service]&nbsp;&nbsp; <i style='float:right;color:#396bbc;font-weight:bold'> $r[acte] / Féq:$r[freq]; $r[art_bud]</i></td><td style='text-align:right'>$r[montant_acte]</td></tr>";
					$mont+=$r['montant_acte'];
				}
				
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px;width:100%' >
				<table style='' ><caption style='".self::$style_caption."background-color:#f8f8f8;color:#000;'><h4> $row[commune] / NP: $row[num_note] de <u>$row[nom_assujetti] $row[adresse_assujetti]</u><br/>NIF: <b style='color:red'>$row[nif]</b></h4></caption>
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
				
				if(isset($row["is_deleted"]) and $row["is_deleted"]){
					$r = self::liste_objet(" where remplacement_de='$row[num_note]' ","");
					if(count($r)){
						echo "<h3 style='color:red'>La note a été supprimée et ramplacée par celle-ci</h3>";
						self::detail_note_sup($r[0]["id"]);}
				}
				echo"<div style='text-align:right'>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='float:left;display:inline-block;margin-right:5px'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Note ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$id' />".self::$marqueur." </form>":"");
				
				echo "&nbsp; <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='float:left;display:inline;margin-right:5px' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='".(isset($row["is_deleted"])?$row['id']:0)."' />".self::$marqueur."&nbsp;</form> &nbsp;<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='float:left;display:inline;margin-right:5px' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='".(isset($row["is_deleted"])?$row['id']:0)."' />".self::$marqueur." </form> ";
				
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
			$Select = "SELECT observation,$ch_id,note_to,no.date_ordo,no.date_depot,ass.id id_ass,ass.nif ,ass.adresse_assujetti ,co.commune ,ass.nom_assujetti ,remplacement_de ,raison_remplacage ,no.num_note,no.num_bap,no.montant_bap,no.date_save FROM ".self::$table ." no ";
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
				$services="";
				$pdo_result = self::$conx->query($req);$mont=$mont_p=0;
				while($r = $pdo_result->fetch()){
					$services .= "$r[service], ";
					$fonc = "enreg_note_payee('$r[id]','".$_SESSION["snp"]["id_releve"]."','$r[montant_acte]','$num_note'),'$r[date_paiement]'";
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
				<table style='' ><caption style='".self::$style_caption."background-color:#f8f8f8;color:#000;'><h4>NP: $row[num_note] de <u>$row[nom_assujetti] $row[adresse_assujetti]</u> <br/>NIF: <b style='color:red'>$row[nif]</b></h4></caption>
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
				<tr><td colspan='3' style='text-align:left;' >Observation: 
				<br>Antenne: <span style='color:blue;font-weight:bold'>".($row["commune"]=="Centre"?"$row[commune]: $services":$row["commune"])."</span>
				<br><span style='color:red;font-weight:bold'>$row[observation]</span>
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
		/* function detail_note_paiement($id){
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
				while($r = $pdo_result->fetch()){
					$fonc = "enreg_note_payee('$r[id]','".$_SESSION["snp"]["id_releve"]."','$r[montant_acte]','$num_note')";
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
		 */
		//sélectionne un objet dans la base de données
		public static function objet($id) {
			$req = "select * from ".self::$table." where id = '$id'";
			if($pdo_result = self::$conx->query($req))
				return $pdo_result->fetch(PDO::FETCH_ASSOC);
		}
		
		function liste($ajout){
				echo "
				<div id='".self::$id_div_crud."' ></div>
				<div class='div_recherche' >";
						self::form_rech_rapide();
						if($ajout)echo self::bt_ajout(self::$bt_class,'Ajout',false);
						self::form_rech_avancee();
				echo '</div>';
				$this->table_liste();
		}
		// le code de cette fonction s'écrit dans un iframe d'où il est transférer ver la fenêtre principale droit direct 
		// dans un <div> qui doit avoir comme id self::$retourHtmlAjaxLIST et pour que les boutton détail mod et sup 
		// fonctionnent il faudra aussi leur prevor un <div> vide avec id=self::$id_div_crud <div id='".self::$id_div_crud."' ></div>
		function ajax_liste(){
				$input = "".self::$marqueur."";
				echo "<div id='".self::$retourHtmlAjaxLIST."' style='' >
				<!--div id='".self::$id_div_crud."' ></div-->
				<div class='div_recherche' >";
						self::form_rech_rapide();
						self::form_rech_avancee();
				echo '</div>';
				$this->table_liste();
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');
				</script>";
				echo '</div>';
		}
		public static function bt_ajax_liste($class,$val,$vider_crud){
			echo "
						<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
							<input type='submit' class='$class' id='bt_ajax_liste_".self::$table."' name='bt_ajax_liste_".self::$table."' value='".($val!=''?$val:'Charger Liste')."' onClick=\"".($vider_crud?"chargerHtml('".self::$id_div_crud."','');":"").self::codeOnCallAjaxListe()."\"/>
							".self::$marqueur."
						</form>";
		}
		
		public static function bt_ajout($class,$val,$vider_ajax_liste){
			if(!self::$readOnly){return "
						<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
							<input type='submit' class='$class' id='bt_lancer_ajt_".self::$table."' name='bt_lancer_ajt_".self::$table."' value='".($val!=''?$val:'Form Ajout')."' onClick=\"".($vider_ajax_liste?"chargerHtml('".self::$retourHtmlAjaxLIST."','');":"").self::codeOnCallAdd()."\"/>
							".self::$marqueur."
						</form>";}
		}
		function form_rech_avancee(){
			$input = "".self::$marqueur."";
			echo "
			<form id='form_rechAc_".self::$table."' method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:none' > 
				<input style='width:100px' type='text' name='ass_nom_assujetti' title=\"Assujetti\" placeholder=\"Assujetti\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ass_adresse_assujetti' title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='co_commune' title=\"Antenne\" placeholder=\"Antenne\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_num_bap'  title=\"N°Bap\" placeholder=\"N°Bap\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_montant_bap'  title=\"Mont.BAP\" placeholder=\"Mont.BAP\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_num_note'  title=\"N°N.P.\" placeholder=\"N°N.P.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_date_ordo' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Date Ordo.\" placeholder=\"Date Ordo.\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_date_depot' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Date Dépot\" placeholder=\"Date Dépot\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_date_invalidation' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Invalidée le\" placeholder=\"Invalidée le\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_raison_invalidation'  title=\"Invalidée pour Cause\" placeholder=\"Invalidée pour Cause\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_remplacement_de'  title=\"NP remplacée\" placeholder=\"NP remplacée\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_raison_remplacage'  title=\"Remplacée pour cause\" placeholder=\"Remplacée pour cause\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_date_save' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Enregistrée le\" placeholder=\"Enregistrée le\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_observation'  title=\"Obs\" placeholder=\"Obs\" class='".self::$input_class."'/>
				$input <select title='fixer le nombre de ligne par page'  name='taille_pg'><option value='10'>10</option><option value='25'>25</option><option value='50'>50</option><option value='100'>100</option><option value='500'>500</option></select> 
				<input type='submit' name='bt_lancer_actAv_".self::$table."' value='Rech.Avanc&eacute;e' class='".self::$bt_class." btn-primary' /> <input type='button' value='Recherche Rapide' onClick=\"cache1_et_affiche2('form_rechAc_".self::$table."','div_rechRap_".self::$table."');\" class='".self::$bt_class."' />
			</form>";
		}
		function form_rech_rapide(){
			$input = "".self::$marqueur."";
			echo "
				<div id='div_rechRap_".self::$table."' style='display:inline' >
					<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' >
						$input <select title='fixer le nombre de ligne par page' name='taille_pg'><option value='10'>10</option><option value='25'>25</option><option value='50'>50</option><option value='100'>100</option><option value='500'>500</option></select>
						<input type='text' name='Filtre' class='".self::$input_class."' placeholder=\"Rechercher rapide\" />
						<input type='submit'  name='bt_lancer_act_".self::$table."' value='Actualiser' class='".self::$bt_class." btn-primary'/>
					</form>
					<input type='button' value='Rech.Avanc&eacute;e' onClick=\"cache1_et_affiche2('div_rechRap_".self::$table."','form_rechAc_".self::$table."');\"  style='display:inline' class='".self::$bt_class."' />
				</div>";
		}
		function table_liste(){
			$Select = "SELECT no.id ,no.is_deleted ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap,no.montant_bap,no.note_to,no.num_note,no.date_ordo,no.date_depot,no.date_invalidation,no.raison_invalidation,no.remplacement_de,no.raison_remplacage,no.date_save,no.observation ";
			$FROM = " FROM ".self::$table ." no ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or ass.nom_assujetti like '%$w%' or  ass.adresse_assujetti like '%$w%' or  co.commune like '%$w%' or  no.num_bap like '%$w%' or  no.montant_bap like '%$w%' or  no.note_to like '%$w%' or  no.num_note like '%$w%' or  no.date_ordo like '%$w%' or  no.date_depot like '%$w%' or  no.date_invalidation like '%$w%' or  no.raison_invalidation like '%$w%' or  no.remplacement_de like '%$w%' or  no.raison_remplacage like '%$w%' or  no.date_save like '%$w%' or  no.observation like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t = $_POST["ass_nom_assujetti"])!=''?(" AND ass.nom_assujetti LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ass_adresse_assujetti"])!=''?(" AND ass.adresse_assujetti LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["co_commune"])!=''?(" AND co.commune LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t=$_POST["no_num_bap"])!=''?(" AND no.num_bap LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["no_montant_bap"])!=''?(" AND no.montant_bap LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["no_num_note"])!=''?(" AND no.num_note LIKE ".self::$conx->quote("%$t%")):"" )
			.(((($count=(count($t=explode(':',($trim = trim($_POST["no_date_ordo"]))))))==1and $trim!='') or $count==2)?(" AND no.date_ordo BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(((($count=(count($t=explode(':',($trim = trim($_POST["no_date_depot"]))))))==1and $trim!='') or $count==2)?(" AND no.date_depot BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(((($count=(count($t=explode(':',($trim = trim($_POST["no_date_invalidation"]))))))==1and $trim!='') or $count==2)?(" AND no.date_invalidation BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(trim($t=$_POST["no_raison_invalidation"])!=''?(" AND no.raison_invalidation LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["no_remplacement_de"])!=''?(" AND no.remplacement_de LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["no_raison_remplacage"])!=''?(" AND no.raison_remplacage LIKE ".self::$conx->quote("%$t%")):"" )
			.(((($count=(count($t=explode(':',($trim = trim($_POST["no_date_save"]))))))==1and $trim!='') or $count==2)?(" AND no.date_save BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(trim($t=$_POST["no_observation"])!=''?(" AND no.observation LIKE ".self::$conx->quote("%$t%")):"" )
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= " 
				Left JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				left JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  $Filtre ";
			$order = " order by no.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["Note"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["Note"]["t"])?$_SESSION["Note"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de Notes</b></caption><tr><th></th><th>N°</th><th>Assujetti</th><th>Adresse Assujetti</th><th>Antenne</th><th>N°Bap</th><th>Mont.BAP</th><th>TO ?</th><th>N°N.P.</th><th>Date Ordo.</th><th>Date Dépot</th><th>Invalidée le</th><th>Invalidée pour Cause</th><th>NP remplacée</th><th>Remplacée pour cause</th><th>Enregistrée le</th><th>Obs</th>".
					((!self::$readOnly and self::$liste_modifiable)?"<th colspan='2' >Actions</th>":"")."</tr>";
					
				if(!self::$readOnly and self::$liste_modifiable){$crud = " echo \"
						<td><form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Mod' /><input type='hidden' name='id' value='\$row[id]' />$input </form></td>
						<td><form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_lancer_sup_".self::$table."' value='Sup' /><input type='hidden' name='id' value='\$row[id]' />$input </form></td>\";";
				}
				else $crud = '';
				$detail = " echo \"
					<td><form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Detail' onClick=\\\"".self::codeOnCallDetail()."\\\" /><input type='hidden' name='id' value='\$row[id]' />$input </form></td>\";";
				// $i=(isset($_GET["d_pg"])?" $_GET[d_pg]":1);$i++;
				$i=$d;$i++;
				while($row		= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					echo"<tr ".($row["is_deleted"]?" class='tr_sup' ":"").">";eval($detail);
					echo "<td>".($i++)."</td><td>$row[nom_assujetti]</td><td>$row[adresse_assujetti]</td><td>$row[commune]</td><td>$row[num_bap]</td><td>$row[montant_bap]</td><td><input type='checkbox' readonly ".($row["note_to"]?'checked':'')." title=\"TO ?\" /></td><td>$row[num_note]</td><td>$row[date_ordo]</td><td>$row[date_depot]</td><td>$row[date_invalidation]</td><td>$row[raison_invalidation]</td><td>$row[remplacement_de]</td><td>$row[raison_remplacage]</td><td>$row[date_save]</td><td>$row[observation]</td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'Note',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($id_assujetti,$pr_cpt_de_id_com,$num_bap,$montant_bap,$note_to,$num_note,$date_ordo,$date_depot,$date_invalidation,$remplacement_de,$raison_remplacage,$date_save,$observation){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "INSERT INTO ".self::$table."(id_assujetti,pr_cpt_de_id_com,num_bap,montant_bap,note_to,num_note,date_ordo,date_depot,date_invalidation,remplacement_de,raison_remplacage,date_save,observation,id_visite) VALUES(" .self::$conx->quote($id_assujetti).','.self::$conx->quote($pr_cpt_de_id_com).','.self::$conx->quote($num_bap).','.self::$conx->quote($montant_bap).','.self::$conx->quote($note_to).','.self::$conx->quote($num_note).','.self::$conx->quote($date_ordo).','.self::$conx->quote($date_depot).','.self::$conx->quote($date_invalidation).','.self::$conx->quote($remplacement_de).','.self::$conx->quote($raison_remplacage).','.self::$conx->quote($date_save).','.self::$conx->quote($observation). ",'$id_visite');";
			if(self::$conx->exec($req)){
				$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
				return true; 
			}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and (''!=($_POST["id_assujetti"]=($_POST["id_assujetti"]==''?(isset($_SESSION['t_assujetti']['id'])?$_SESSION['t_assujetti']['id']:''):$_POST["id_assujetti"]))) and (''!=($_POST["pr_cpt_de_id_com"]=($_POST["pr_cpt_de_id_com"]==''?(isset($_SESSION['t_commune']['id'])?$_SESSION['t_commune']['id']:''):$_POST["pr_cpt_de_id_com"]))) and ''!=$_POST["num_note"] and ''!=$_POST["date_ordo"] and ''!=$_POST["date_depot"]){
				if(self::insert($_POST["id_assujetti"],$_POST["pr_cpt_de_id_com"],$_POST["num_bap"],$_POST["montant_bap"],"".(isset($_POST["note_to"])?1:0)."" ,$_POST["num_note"],$_POST["date_ordo"],$_POST["date_depot"],date('Y-m-d'),$_POST["remplacement_de"],$_POST["raison_remplacage"],date('Y-m-d H:i:s'),$_POST["observation"]))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) Note' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
			}else self::$notification .= self::$notifRemplir;
			if(self::$msgScriptActif)
				echo "<script>window.top.window.chargerHtml('".self::$id_zone_rslt_msgScript."',\"".self::$notification."\");</script>";
		}
		
		// modifie la valeur d'un champ dont on connait l'id
		public function mod_champ($val,$champ,$id){
			$req = ("UPDATE ".self::$table." SET $champ=".self::$conx->quote($val)." WHERE id = '$id';");
			if(self::$conx->exec($req) or self::$conx->errorInfo()[2]=='')return true;
			return false;
		}
		//modification dans la base de données
		function modifier(){
			if(isset($_POST['id'])and (($id = $_POST['id'])!='')){
				if( true  and (''!=($_POST["id_assujetti"]=($_POST["id_assujetti"]==''?(isset($_SESSION['t_assujetti']['id'])?$_SESSION['t_assujetti']['id']:''):$_POST["id_assujetti"]))) and (''!=($_POST["pr_cpt_de_id_com"]=($_POST["pr_cpt_de_id_com"]==''?(isset($_SESSION['t_commune']['id'])?$_SESSION['t_commune']['id']:''):$_POST["pr_cpt_de_id_com"]))) and ''!=$_POST["num_note"] and ''!=$_POST["date_ordo"] and ''!=$_POST["date_depot"] and ''!=$_POST["date_invalidation"]){
					$req = ("UPDATE ".self::$table." SET id_assujetti = ".self::$conx->quote($_POST["id_assujetti"])." ,pr_cpt_de_id_com = ".self::$conx->quote($_POST["pr_cpt_de_id_com"])." ,num_bap = ".self::$conx->quote($_POST["num_bap"])." ,montant_bap = ".self::$conx->quote($_POST["montant_bap"])." ,note_to = '".isset($_POST["note_to"])."' ,num_note = ".self::$conx->quote($_POST["num_note"])." ,date_ordo = ".self::$conx->quote($_POST["date_ordo"])." ,date_depot = ".self::$conx->quote($_POST["date_depot"])." ,date_invalidation = ".self::$conx->quote($_POST["date_invalidation"])." ,raison_invalidation = ".self::$conx->quote($_POST["raison_invalidation"])." ,remplacement_de = ".self::$conx->quote($_POST["remplacement_de"])." ,raison_remplacage = ".self::$conx->quote($_POST["raison_remplacage"])." ,observation = ".self::$conx->quote($_POST["observation"])."  WHERE id = $id;");
					if(self::$conx->exec($req) or self::$conx->errorInfo()[2]==''){
						self::$notification .= self::$notifUpdSucc;
					}else self::$notification .= self::$notifUpdErro.self::$conx->errorInfo()[2]; 
				}else self::$notification .= "Erreur!<br>Remplir les champs vides SVP!";
			}else self::$notification .= self::$notifNoSelct;
			if(self::$msgScriptActif)
				echo "<script>window.top.window.chargerHtml('".self::$id_zone_rslt_msgScript."',\"".self::$notification."\");</script>";
		}
		
		function supprimer(){
			if(isset($_POST['id'])and $_POST['id']!=''){
				// $req = "DELETE FROM ".self::$table." WHERE id = $_POST[id];";
				$req = "UPDATE ".self::$table." SET is_deleted=1 WHERE id = $_POST[id];";
				if(self::$conx->exec($req) or self::$conx->errorInfo()[2]==''){
					// unset($_SESSION["id_".self::$table]);
					self::$notification .= self::$notifDelSucc;
					// suppression des fichiers
					// if(file_exists('otes.bak'))
					// unlink('');
					
				}else self::$notification .= self::$notifDelErro.self::$conx->errorInfo()[2];
			}else self::$notification .= self::$notifNoSelct;
			if(self::$msgScriptActif){
				echo "<script>window.top.window.chargerHtml('".self::$id_zone_rslt_msgScript."',\"".self::$notification."\");</script>";
				self::vider(self::$id_div_crud);
			}
		}
		
		//insertion dans la base de données
		function traitement() {
			if(isset($_POST['class'])and $_POST['class']!='Note')return false;
			$retour = self::$id_div_crud;
			if(isset($_POST['bt_sup_'.self::$table]))$this->supprimer();
			else if(isset($_POST['bt_mod_'.self::$table]))$this->modifier();
			else if(isset($_POST['bt_ajt_'.self::$table]))$this->creer();
			else {$opt = '';
				if(isset($_POST['bt_detail_'.self::$table])or isset($_POST['bt_prev_detail_'.self::$table]) or isset($_POST['bt_next_detail_'.self::$table])){
					$opt = 'dtl';if(self::$useDetailStandard)$this->detail("$_POST[id]");
					else $this->detail_personnaliser("$_POST[id]");
				}
				else if(isset($_POST['bt_lancer_mod_'.self::$table])){
					$opt = 'mod';$array = self::objet("$_POST[id]");
					$this->form($opt,$array);
				}
				else if(isset($_POST['bt_lancer_ajt_'.self::$table])){
					$opt = 'ajt';$array = array();
					$this->form($opt,$array);
				}
				else if(isset($_POST['bt_lancer_sup_'.self::$table])){
					$opt = 'sup';$array = self::objet("$_POST[id]");
					$this->form($opt,$array);
				}
				else if(isset($_POST['bt_lancer_act_'.self::$table]) or isset($_POST['bt_lancer_actAv_'.self::$table])){
					$this->liste('');$opt = 'lst';
					$retour = self::$id_div_liste;
				}
				else if(isset($_POST["bt_ajax_liste_".self::$table])){
					$this->ajax_liste();$opt = 'aja';
					$retour = self::$retourHtmlAjaxLIST;
				}
				else if(isset($_POST["bt_rapport_".self::$table])){
					self::rapport();$opt = 'rap';
					$retour = self::$retourHtmlRap;
				}
				else if(isset($_POST["bt_rapport_complet_".self::$table])){
					self::rapport_complet();$opt = 'rap';
					$retour = self::$retourHtmlRap.'_complet';
				}
				else if(isset($_POST["bt_rapport_config_".self::$table]) or isset($_POST["bt_mod_contrainte_rapp_".self::$table.""])){
					if(!count(self::$CHAMPS))
						self::ini_champ();
					self::rapport_config_champ(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$retourHtmlRapConfig,self::$table,self::$marqueur,self::$ficherAction,self::$ficherIframe);$opt = 'rap';
					$retour = self::$retourHtmlRapConfig;
				}
				if($opt!='')
					self::chargerHtml($retour,$retour);
				if($opt=='ajt'or $opt=='mod')
					echo"<script>". self::autoCompleter('id_assujetti').self::autoCompleter('pr_cpt_de_id_com'). "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='id_assujetti';$id_assujetti=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='pr_cpt_de_id_com';$pr_cpt_de_id_com=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='num_bap';$num_bap=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='montant_bap';$montant_bap=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='note_to';$note_to=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='num_note';$num_note=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='date_ordo';$date_ordo=isset($array["$cle"])?$array["$cle"]:date('Y-m-d'); 
				$cle='date_depot';$date_depot=isset($array["$cle"])?$array["$cle"]:date('Y-m-d'); 
				$cle='date_invalidation';$date_invalidation=isset($array["$cle"])?$array["$cle"]:date('Y-m-d'); 
				$cle='raison_invalidation';$raison_invalidation=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='remplacement_de';$remplacement_de=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='raison_remplacage';$raison_remplacage=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='observation';$observation=isset($array["$cle"])?$array["$cle"]:''; 
			$ch_input = "".self::$marqueur."";
			if($opt=='mod' or $opt=='sup'){
				if(isset($_POST['id']))
					$ch_input .= "<input type='hidden' name='id' value='$_POST[id]' style='' />";
				else {self::$notification = self::$notifNoSelct;return ;}
			}
			$form = ''; $ok=false;
			if($opt=="sup"  ){
				if(isset($_POST['id'])){
					$form =  
					"<form id='' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='border:solid 0px;'>".
						"<table><caption style='".self::$style_caption."'><b>Suppression Note</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = "<div id='".Assujetti::$id_div_crud."' ></div><br/>".Assujetti::bt_ajout(Assujetti::$bt_class,'Ajouter Assujetti',false)."<div id='".Commune::$id_div_crud."' ></div><br/>".Commune::bt_ajout(Commune::$bt_class,'Ajouter Commune',false).
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." Note</caption>".
					"<tr>".
						"<td><label for='id_assujetti' >Assujetti</label></td><td>:</td>".
						"<td><select id='id_assujetti' name='id_assujetti'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Assujetti::options('',$id_assujetti)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='pr_cpt_de_id_com' >Pour Compte de</label></td><td>:</td>".
						"<td><select id='pr_cpt_de_id_com' name='pr_cpt_de_id_com'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Commune::options('',$pr_cpt_de_id_com)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='num_bap' >N°Bap</label></td><td>:</td>".
						"<td><input type='text' value=\"$num_bap\" title=\"N°Bap\" placeholder=\"N°Bap\" id='num_bap' name='num_bap'   class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='montant_bap' >Mont.BAP</label></td><td>:</td>".
						"<td><input type='text' value=\"$montant_bap\" title=\"Mont.BAP\" placeholder=\"Mont.BAP\" id='montant_bap' name='montant_bap'   class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='note_to' >TO ?</label></td><td>:</td>".
						"<td><input type='checkbox' ".($note_to?'checked':'')." title=\"TO ?\" id='note_to' name='note_to' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='num_note' >N°N.P.</label></td><td>:</td>".
						"<td><input type='text' value=\"$num_note\" title=\"N°N.P.\" placeholder=\"N°N.P.\" id='num_note' name='num_note' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_ordo' >Date Ordo.</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_ordo\" title=\"Date Ordo.\" placeholder=\"Date Ordo.\" id='date_ordo' name='date_ordo' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_depot' >Date Dépot</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_depot\" title=\"Date Dépot\" placeholder=\"Date Dépot\" id='date_depot' name='date_depot' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					($opt=='mod'?("<tr>".
						"<td><label for='date_invalidation' >Invalidée le</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_invalidation\" title=\"Invalidée le\" placeholder=\"Invalidée le\" id='date_invalidation' name='date_invalidation' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>"):''). 
					($opt=='mod'?("<tr>".
						"<td><label for='raison_invalidation' >Invalidée pour Cause</label></td><td>:</td>".
						"<td><input type='text' value=\"$raison_invalidation\" title=\"Invalidée pour Cause\" placeholder=\"Invalidée pour Cause\" id='raison_invalidation' name='raison_invalidation'   class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>"):''). 
					"<tr>".
						"<td><label for='remplacement_de' >NP remplacée</label></td><td>:</td>".
						"<td><input type='text' value=\"$remplacement_de\" title=\"NP remplacée\" placeholder=\"NP remplacée\" id='remplacement_de' name='remplacement_de'   class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='raison_remplacage' >Remplacée pour cause</label></td><td>:</td>".
						"<td><input type='text' value=\"$raison_remplacage\" title=\"Remplacée pour cause\" placeholder=\"Remplacée pour cause\" id='raison_remplacage' name='raison_remplacage'   class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='observation' >Obs</label></td><td>:</td>".
						"<td><input type='text' value=\"$observation\" title=\"Obs\" placeholder=\"Obs\" id='observation' name='observation'   class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>".  
					"<tr><td></td><td></td><td>".($opt=="ajt"?
					"<input type='submit' class='".self::$bt_class." btn-primary' name='bt_ajt_".self::$table."' value='Ajouter' 	style='' >":
					"<input type='submit' class='".self::$bt_class." btn-primary' name='bt_mod_".self::$table."' value='Modifier' 	style='' >").
					"<input type='reset' class='".self::$bt_class."' name='bt_resset_".self::$table."' value='Annuler' style='float:right' >
					<label class='".self::$bt_class." btn-danger'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\"  >Finir</label></td></tr>".
					"</table>".
				"</form>";$ok=true;
			}else self::$notification .= self::$notifNoForm;
			if($ok)
			echo "<div id='".self::$id_div_crud."' style='' >$form 
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>
			</div>";
			
		}
		public static function options($where,$id) {
			$where = str_replace('where','and',$where);
			$req = "SELECT no.id Value ,concat(ass.nom_assujetti ,' ',ass.adresse_assujetti ,' ',no.num_note) DisplayText FROM ".self::$table ." no 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  WHERE no.is_deleted=0 $where ";
			if($pdo_result = self::$conx->query($req)){
				$opt  ='';
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$selected = $id == $row['Value']?"selected":"";
					$opt .= "<option value='$row[Value]' $selected >$row[DisplayText]</option>";
				}
			}else echo self::$conx->errorInfo()[2];
			return $opt;
		}
		
		public static $T_SGO = array("show","group","order","search","sum");
		public static $T_CH = array();
		public static $TLBL = array();
		public static $CHAMPS = array();
		public static function ini_champ(){
			$T_CH = self::$T_CH = array("id_assujetti","nom_assujetti","adresse_assujetti","pr_cpt_de_id_com","commune","num_bap","montant_bap","note_to","num_note","date_ordo","date_depot","date_invalidation","raison_invalidation","remplacement_de","raison_remplacage","date_save","observation");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"no.id_assujetti id_assujetti",$label=>"Assujetti",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"ass.nom_assujetti nom_assujetti",$label=>"Assujetti",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ass.adresse_assujetti adresse_assujetti",$label=>"Adresse Assujetti",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"no.pr_cpt_de_id_com pr_cpt_de_id_com",$label=>"Pour Compte de",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"co.commune commune",$label=>"Antenne",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"no.num_bap num_bap",$label=>"N°Bap",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.montant_bap montant_bap",$label=>"Mont.BAP",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.note_to note_to",$label=>"TO ?",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.num_note num_note",$label=>"N°N.P.",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.date_ordo date_ordo",$label=>"Date Ordo.",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.date_depot date_depot",$label=>"Date Dépot",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.date_invalidation date_invalidation",$label=>"Invalidée le",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.raison_invalidation raison_invalidation",$label=>"Invalidée pour Cause",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.remplacement_de remplacement_de",$label=>"NP remplacée",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.raison_remplacage raison_remplacage",$label=>"Remplacée pour cause",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.date_save date_save",$label=>"Enregistrée le",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"no.observation observation",$label=>"Obs",$type=>"text"));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[0]=>1,$T_SGO[3]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>2,$T_SGO[0]=>2),
			$T_CH[$l++]=>array($T_SGO[0]=>4,$T_SGO[3]=>4),
			$T_CH[$l++]=>array($T_SGO[3]=>4,$T_SGO[1]=>4,$T_SGO[0]=>4),
			$T_CH[$l++]=>array($T_SGO[3]=>6,$T_SGO[0]=>6),
			$T_CH[$l++]=>array($T_SGO[3]=>7,$T_SGO[0]=>7),
			$T_CH[$l++]=>array($T_SGO[3]=>8,$T_SGO[0]=>8),
			$T_CH[$l++]=>array($T_SGO[3]=>9,$T_SGO[0]=>9),
			$T_CH[$l++]=>array($T_SGO[3]=>10,$T_SGO[0]=>10),
			$T_CH[$l++]=>array($T_SGO[3]=>11,$T_SGO[0]=>11),
			$T_CH[$l++]=>array($T_SGO[3]=>12,$T_SGO[0]=>12),
			$T_CH[$l++]=>array($T_SGO[3]=>13,$T_SGO[0]=>13),
			$T_CH[$l++]=>array($T_SGO[3]=>14,$T_SGO[0]=>14),
			$T_CH[$l++]=>array($T_SGO[3]=>15,$T_SGO[0]=>15),
			$T_CH[$l++]=>array($T_SGO[3]=>16,$T_SGO[0]=>16),
			$T_CH[$l++]=>array($T_SGO[3]=>17,$T_SGO[0]=>17));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport Note')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT no.id $CONSTR[Ch_Select] FROM ".self::$table ." no 
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id 
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id  ";
			self::suite_rapport($req,$CONSTR,self::$table,self::$marqueur,self::$style_caption,14);
		}
		
		public static function rapport_complet(){
				echo "
				<div id='".self::$retourHtmlRap."_complet' >";
				self::bt_rapport_config('','');
				self::div_html_rapport_config();
				self::form_rech_rapport();
				self::rapport();
				echo "</div>";
		}
		public static function form_rech_rapport(){
			self::ini_champ();
			if(isset($_COOKIE[self::$table]))
				self::$CHAMPS = self::recupere_contrainte_champs($_COOKIE[self::$table],self::$T_CH,self::$T_SGO);
			$T_SGO = self::$T_SGO;
			echo"
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['id_assujetti'][$T_SGO[3]])and self::$CHAMPS['id_assujetti'][$T_SGO[3]])?"":"<select title=\"Assujetti\" id='no_id_assujetti' name='no_id_assujetti'  class='".self::$input_class."'><option value=''>Choisir</option>".Assujetti::options('',0)."</select>").
				(!(isset(self::$CHAMPS['nom_assujetti'][$T_SGO[3]])and self::$CHAMPS['nom_assujetti'][$T_SGO[3]])?"":"<input type='text' name='ass_nom_assujetti' title=\"Assujetti\" placeholder=\"Assujetti\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['adresse_assujetti'][$T_SGO[3]])and self::$CHAMPS['adresse_assujetti'][$T_SGO[3]])?"":"<input type='text' name='ass_adresse_assujetti' title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['pr_cpt_de_id_com'][$T_SGO[3]])and self::$CHAMPS['pr_cpt_de_id_com'][$T_SGO[3]])?"":"<select title=\"Pour Compte de\" id='no_pr_cpt_de_id_com' name='no_pr_cpt_de_id_com'  class='".self::$input_class."'><option value=''>Choisir</option>".Commune::options('',0)."</select>").
				(!(isset(self::$CHAMPS['commune'][$T_SGO[3]])and self::$CHAMPS['commune'][$T_SGO[3]])?"":"<input type='text' name='co_commune' title=\"Antenne\" placeholder=\"Antenne\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['num_bap'][$T_SGO[3]])and self::$CHAMPS['num_bap'][$T_SGO[3]])?"":"<input type='text' name='no_num_bap' title=\"N°Bap\" placeholder=\"N°Bap\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['montant_bap'][$T_SGO[3]])and self::$CHAMPS['montant_bap'][$T_SGO[3]])?"":"<input type='text' name='no_montant_bap' title=\"Mont.BAP\" placeholder=\"Mont.BAP\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['note_to'][$T_SGO[3]])and self::$CHAMPS['note_to'][$T_SGO[3]])?"":"<input type='text' name='no_note_to' title=\"TO ?\" placeholder=\"TO ?\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['num_note'][$T_SGO[3]])and self::$CHAMPS['num_note'][$T_SGO[3]])?"":"<input type='text' name='no_num_note' title=\"N°N.P.\" placeholder=\"N°N.P.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_ordo'][$T_SGO[3]])and self::$CHAMPS['date_ordo'][$T_SGO[3]])?"":"<input type='text' name='no_date_ordo' title=\"Date Ordo.\" placeholder=\"Date Ordo.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_depot'][$T_SGO[3]])and self::$CHAMPS['date_depot'][$T_SGO[3]])?"":"<input type='text' name='no_date_depot' title=\"Date Dépot\" placeholder=\"Date Dépot\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_invalidation'][$T_SGO[3]])and self::$CHAMPS['date_invalidation'][$T_SGO[3]])?"":"<input type='text' name='no_date_invalidation' title=\"Invalidée le\" placeholder=\"Invalidée le\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['raison_invalidation'][$T_SGO[3]])and self::$CHAMPS['raison_invalidation'][$T_SGO[3]])?"":"<input type='text' name='no_raison_invalidation' title=\"Invalidée pour Cause\" placeholder=\"Invalidée pour Cause\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['remplacement_de'][$T_SGO[3]])and self::$CHAMPS['remplacement_de'][$T_SGO[3]])?"":"<input type='text' name='no_remplacement_de' title=\"NP remplacée\" placeholder=\"NP remplacée\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['raison_remplacage'][$T_SGO[3]])and self::$CHAMPS['raison_remplacage'][$T_SGO[3]])?"":"<input type='text' name='no_raison_remplacage' title=\"Remplacée pour cause\" placeholder=\"Remplacée pour cause\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_save'][$T_SGO[3]])and self::$CHAMPS['date_save'][$T_SGO[3]])?"":"<input type='text' name='no_date_save' title=\"Enregistrée le\" placeholder=\"Enregistrée le\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['observation'][$T_SGO[3]])and self::$CHAMPS['observation'][$T_SGO[3]])?"":"<input type='text' name='no_observation' title=\"Obs\" placeholder=\"Obs\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config Note')."' onClick=\"".self::codeOnCallConfig()."\"/>
				".self::$marqueur."
			</form>";
		}		
		
		
		
		
		public static function codeOnCallDetail(){
			return "";//chargerHtml('','');
		}
		public static function codeOnCallAdd(){
			return "";//chargerHtml('','');
		}
		public static function codeOnCallAjaxListe(){
			return "chargerHtml('id_zone_rslt_msgScript','');";
		}
		public static function codeOnCallRapport(){
			return "";//chargerHtml('id_zone_rslt_msgScript','');
		}
		public static function codeOnCallConfig(){
			return "";//chargerHtml('id_zone_rslt_msgScript','');
		}
		public static function div_html_rapport_config(){ echo"<div id='".self::$retourHtmlRapConfig."' ></div>"; }
		public static function div_html_rapport(){ echo"<div id='".self::$retourHtmlRap."_complet' ></div>"; }
		public static function div_html_liste(){ echo"<div id='".self::$id_div_liste."' ></div>"; }
		public static function div_html_crud() { echo"<div id='".self::$id_div_crud."' ></div>";}
	}
	