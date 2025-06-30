<?php
	require_once("chado.php");
	class NoteActesPayer extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array();
		public static $table 	= 't_note_actes_payer';
		public static $marqueur = "<input type='hidden' name='class' value='NoteActesPayer' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_NoteActesPayer';
		public static $id_div_crud = 'id_div_crud_NoteActesPayer';
		public static $retourHtmlRapConfig = 'div_form_rapp_NoteActesPayer';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
		public static function liste_objet($where,$limit){
			$ch_id = "nacp.id";
			$Select = "SELECT $ch_id, ac.acte ,ser.service ,ac.art_bud ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte ,nac.freq ,bq.nom_banque ,rlv.date_paiement ,us.nom ,nacp.montant_payer,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte,nacp.paie_exp_date_ordo,ass_1.nom_assujetti nom_assujetti_1,ass_1.adresse_assujetti adresse_assujetti_1 FROM ".self::$table ." nacp ";
			$req = "$Select 
				INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				INNER JOIN t_note no ON  nac.id_note = no.id  
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
				INNER JOIN t_banque bq ON  rlv.id_banque = bq.id  
				INNER JOIN t_user us ON  rlv.id_user = us.id 
				LEFT JOIN t_assujetti ass_1 ON  nacp.paie_exp_id_assujetti = ass_1.id  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." nacp ";
			$req_count = "$count 
				INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				INNER JOIN t_note no ON  nac.id_note = no.id  
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
				INNER JOIN t_banque bq ON  rlv.id_banque = bq.id  
				INNER JOIN t_user us ON  rlv.id_user = us.id 
				LEFT JOIN t_assujetti ass_1 ON  nacp.paie_exp_id_assujetti = ass_1.id  $where ";
			if($pdo_result = self::$conx->query($req_count)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			if($pdo_result = self::$conx->query($req)){
				$rows = array();
				while($row	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$row['check_montant_payer'] = "<input type='checkbox' readonly ".($row["montant_payer"]?'checked':'')." title=\"Mont. Payé\" />"; 
					$row['check_paie_exp_id_acte'] = "<input type='checkbox' readonly ".($row["paie_exp_id_acte"]?'checked':'')." title=\"Exp-Acte\" />"; 
					$rows[] = $row;
				}
				return $rows;
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		function detail($id){
			$ch_id = "nacp.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, ac.acte ,ser.service ,ac.art_bud ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte ,nac.freq ,bq.nom_banque ,rlv.date_paiement ,us.nom ,nacp.montant_payer,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte,nacp.paie_exp_date_ordo,ass_1.nom_assujetti nom_assujetti_1,ass_1.adresse_assujetti adresse_assujetti_1 FROM ".self::$table ." nacp ";
			$req = "$Select 
				INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				INNER JOIN t_note no ON  nac.id_note = no.id  
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
				INNER JOIN t_banque bq ON  rlv.id_banque = bq.id  
				INNER JOIN t_user us ON  rlv.id_user = us.id 
				LEFT JOIN t_assujetti ass_1 ON  nacp.paie_exp_id_assujetti = ass_1.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table><caption style='".self::$style_caption."'><b>Détails sur NoteActesPayer</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Acte</th><td>:</td><td>$row[acte]</td></tr>
					<tr><th>Service</th><td>:</td><td>$row[service]</td></tr>
					<tr><th>Art.B.</th><td>:</td><td>$row[art_bud]</td></tr>
					<tr><th>Assujetti</th><td>:</td><td>$row[nom_assujetti]</td></tr>
					<tr><th>Adresse Assujetti</th><td>:</td><td>$row[adresse_assujetti]</td></tr>
					<tr><th>Antenne</th><td>:</td><td>$row[commune]</td></tr>
					<tr><th>N°Bap</th><td>:</td><td>$row[num_bap]</td></tr>
					<tr><th>Mont.BAP</th><td>:</td><td>$row[montant_bap]</td></tr>
					<tr><th>TO ?</th><td>:</td><td>$row[note_to]</td></tr>
					<tr><th>N°N.P.</th><td>:</td><td>$row[num_note]</td></tr>
					<tr><th>Date Ordo.</th><td>:</td><td>$row[date_ordo]</td></tr>
					<tr><th>Date Dépot</th><td>:</td><td>$row[date_depot]</td></tr>
					<tr><th>Obs</th><td>:</td><td>$row[observation]</td></tr>
					<tr><th>Mont. Acte</th><td>:</td><td>$row[montant_acte]</td></tr>
					<tr><th>Fréq.</th><td>:</td><td>$row[freq]</td></tr>
					<tr><th>Banque</th><td>:</td><td>$row[nom_banque]</td></tr>
					<tr><th>Payé le</th><td>:</td><td>$row[date_paiement]</td></tr>
					<tr><th>Utilisateur</th><td>:</td><td>$row[nom]</td></tr>
					<tr><th>Mont. Payé</th><td>:</td><td>$row[montant_payer]</td></tr>
					<tr><th>Enregistrée le</th><td>:</td><td>$row[date_enreg]</td></tr>
					<tr><th>Exp-N.P.</th><td>:</td><td>$row[paie_exp_num_note]</td></tr>
					<tr><th>Exp-Acte</th><td>:</td><td><input type='checkbox' readonly ".($row["paie_exp_id_acte"]?'checked':'')." title=\"Exp-Acte\" /></td></tr>
					<tr><th>Exp-Date Ordo</th><td>:</td><td>$row[paie_exp_date_ordo]</td></tr>
					<tr><th>Assujetti</th><td>:</td><td>$row[nom_assujetti_1]</td></tr>
					<tr><th>Adresse Assujetti</th><td>:</td><td>$row[adresse_assujetti_1]</td></tr>
					</tbody>
				</table>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier NoteActesPayer ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de NoteActesPayer</label>
				</div></div>";
				
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>";
				echo "</div>";
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		function detail_personnaliser($id){
			$ch_id = "nacp.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, ac.acte ,ser.service ,ac.art_bud ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte ,nac.freq ,bq.nom_banque ,rlv.date_paiement ,us.nom ,nacp.montant_payer,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte,nacp.paie_exp_date_ordo,ass_1.nom_assujetti nom_assujetti_1,ass_1.adresse_assujetti adresse_assujetti_1 FROM ".self::$table ." nacp ";
			$req = "$Select 
				INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				INNER JOIN t_note no ON  nac.id_note = no.id  
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
				INNER JOIN t_banque bq ON  rlv.id_banque = bq.id  
				INNER JOIN t_user us ON  rlv.id_user = us.id 
				LEFT JOIN t_assujetti ass_1 ON  nacp.paie_exp_id_assujetti = ass_1.id  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur NoteActesPayer</b></caption>Acte : $row[acte] Service : $row[service] Art.B. : $row[art_bud] Assujetti : $row[nom_assujetti] Adresse Assujetti : $row[adresse_assujetti] Antenne : $row[commune] N°Bap : $row[num_bap] Mont.BAP : $row[montant_bap] TO ? : $row[note_to] N°N.P. : $row[num_note] Date Ordo. : $row[date_ordo] Date Dépot : $row[date_depot] Obs : $row[observation] Mont. Acte : $row[montant_acte] Fréq. : $row[freq] Banque : $row[nom_banque] Payé le : $row[date_paiement] Utilisateur : $row[nom] Mont. Payé :  ".($row["montant_payer"])." Enregistrée le : $row[date_enreg] Exp-N.P. : $row[paie_exp_num_note] Exp-Acte : <input type='checkbox' readonly ".($row["paie_exp_id_acte"]?'checked':'')." title=\"Exp-Acte\" /> Exp-Date Ordo : $row[paie_exp_date_ordo] Assujetti : $row[nom_assujetti_1] Adresse Assujetti : $row[adresse_assujetti_1] 
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier NoteActesPayer ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de NoteActesPayer</label>";
				
				echo "
				<script>
					window.top.window.cache1_et_affiche2('".self::$retourHtmlAjaxLIST."','".self::$id_div_crud."');
				</script>";
				echo "</div>";
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		
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
				<input style='width:100px' type='text' name='ac_acte' title=\"Acte\" placeholder=\"Acte\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ser_service' title=\"Service\" placeholder=\"Service\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ac_art_bud' title=\"Art.B.\" placeholder=\"Art.B.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ass_nom_assujetti' title=\"Assujetti\" placeholder=\"Assujetti\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ass_adresse_assujetti' title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='co_commune' title=\"Antenne\" placeholder=\"Antenne\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_num_bap' title=\"N°Bap\" placeholder=\"N°Bap\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_montant_bap' title=\"Mont.BAP\" placeholder=\"Mont.BAP\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_note_to' title=\"TO ?\" placeholder=\"TO ?\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_num_note' title=\"N°N.P.\" placeholder=\"N°N.P.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_date_ordo' title=\"Date Ordo.\" placeholder=\"Date Ordo.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_date_depot' title=\"Date Dépot\" placeholder=\"Date Dépot\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='no_observation' title=\"Obs\" placeholder=\"Obs\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='nac_montant_acte' title=\"Mont. Acte\" placeholder=\"Mont. Acte\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='nac_freq' title=\"Fréq.\" placeholder=\"Fréq.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='bq_nom_banque' title=\"Banque\" placeholder=\"Banque\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='rlv_date_paiement' title=\"Payé le\" placeholder=\"Payé le\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='us_nom' title=\"Utilisateur\" placeholder=\"Utilisateur\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='nacp_date_enreg' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Enregistrée le\" placeholder=\"Enregistrée le\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='nacp_paie_exp_num_note'  title=\"Exp-N.P.\" placeholder=\"Exp-N.P.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='nacp_paie_exp_date_ordo' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Exp-Date Ordo\" placeholder=\"Exp-Date Ordo\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ass_1_nom_assujetti' title=\"Assujetti\" placeholder=\"Assujetti\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ass_1_adresse_assujetti' title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" class='".self::$input_class."'/>
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
			$Select = "SELECT nacp.id ,nacp.is_deleted ,ac.acte ,ser.service ,ac.art_bud ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte ,nac.freq ,bq.nom_banque ,rlv.date_paiement ,us.nom ,nacp.montant_payer,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte,nacp.paie_exp_date_ordo,ass_1.nom_assujetti nom_assujetti_1,ass_1.adresse_assujetti adresse_assujetti_1 ";
			$FROM = " FROM ".self::$table ." nacp ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or ac.acte like '%$w%' or  ser.service like '%$w%' or  ac.art_bud like '%$w%' or  ass.nom_assujetti like '%$w%' or  ass.adresse_assujetti like '%$w%' or  co.commune like '%$w%' or  no.num_bap like '%$w%' or  no.montant_bap like '%$w%' or  no.note_to like '%$w%' or  no.num_note like '%$w%' or  no.date_ordo like '%$w%' or  no.date_depot like '%$w%' or  no.observation like '%$w%' or  nac.montant_acte like '%$w%' or  nac.freq like '%$w%' or  bq.nom_banque like '%$w%' or  rlv.date_paiement like '%$w%' or  us.nom like '%$w%' or  nacp.montant_payer like '%$w%' or  nacp.date_enreg like '%$w%' or  nacp.paie_exp_num_note like '%$w%' or  nacp.paie_exp_id_acte like '%$w%' or  nacp.paie_exp_date_ordo like '%$w%' or  ass_1.nom_assujetti like '%$w%' or  ass_1.adresse_assujetti like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t = $_POST["ac_acte"])!=''?(" AND ac.acte LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ser_service"])!=''?(" AND ser.service LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ac_art_bud"])!=''?(" AND ac.art_bud LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ass_nom_assujetti"])!=''?(" AND ass.nom_assujetti LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ass_adresse_assujetti"])!=''?(" AND ass.adresse_assujetti LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["co_commune"])!=''?(" AND co.commune LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_num_bap"])!=''?(" AND no.num_bap LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_montant_bap"])!=''?(" AND no.montant_bap LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_note_to"])!=''?(" AND no.note_to LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_num_note"])!=''?(" AND no.num_note LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_date_ordo"])!=''?(" AND no.date_ordo LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_date_depot"])!=''?(" AND no.date_depot LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["no_observation"])!=''?(" AND no.observation LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["nac_montant_acte"])!=''?(" AND nac.montant_acte LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["nac_freq"])!=''?(" AND nac.freq LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["bq_nom_banque"])!=''?(" AND bq.nom_banque LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["rlv_date_paiement"])!=''?(" AND rlv.date_paiement LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["us_nom"])!=''?(" AND us.nom LIKE ". self::$conx->quote("%$t%")):"") 
			.(((($count=(count($t=explode(':',($trim = trim($_POST["nacp_date_enreg"]))))))==1and $trim!='') or $count==2)?(" AND nacp.date_enreg BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(trim($t=$_POST["nacp_paie_exp_num_note"])!=''?(" AND nacp.paie_exp_num_note LIKE ".self::$conx->quote("%$t%")):"" )
			.(((($count=(count($t=explode(':',($trim = trim($_POST["nacp_paie_exp_date_ordo"]))))))==1and $trim!='') or $count==2)?(" AND nacp.paie_exp_date_ordo BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(trim($t = $_POST["ass_1_nom_assujetti"])!=''?(" AND ass_1.nom_assujetti LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ass_1_adresse_assujetti"])!=''?(" AND ass_1.adresse_assujetti LIKE ". self::$conx->quote("%$t%")):"") 
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= " 
				INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				INNER JOIN t_note no ON  nac.id_note = no.id  
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
				INNER JOIN t_banque bq ON  rlv.id_banque = bq.id  
				INNER JOIN t_user us ON  rlv.id_user = us.id 
				LEFT JOIN t_assujetti ass_1 ON  nacp.paie_exp_id_assujetti = ass_1.id  $Filtre ";
			$order = " order by nacp.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["NoteActesPayer"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["NoteActesPayer"]["t"])?$_SESSION["NoteActesPayer"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de NoteActesPayers</b></caption><tr><th></th><th>N°</th><th>Acte</th><th>Service</th><th>Art.B.</th><th>Assujetti</th><th>Adresse Assujetti</th><th>Antenne</th><th>N°Bap</th><th>Mont.BAP</th><th>TO ?</th><th>N°N.P.</th><th>Date Ordo.</th><th>Date Dépot</th><th>Obs</th><th>Mont. Acte</th><th>Fréq.</th><th>Banque</th><th>Payé le</th><th>Utilisateur</th><th>Mont. Payé</th><th>Enregistrée le</th><th>Exp-N.P.</th><th>Exp-Acte</th><th>Exp-Date Ordo</th><th>Assujetti</th><th>Adresse Assujetti</th>".
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
					echo "<td>".($i++)."</td><td>$row[acte]</td><td>$row[service]</td><td>$row[art_bud]</td><td>$row[nom_assujetti]</td><td>$row[adresse_assujetti]</td><td>$row[commune]</td><td>$row[num_bap]</td><td>$row[montant_bap]</td><td>$row[note_to]</td><td>$row[num_note]</td><td>$row[date_ordo]</td><td>$row[date_depot]</td><td>$row[observation]</td><td>$row[montant_acte]</td><td>$row[freq]</td><td>$row[nom_banque]</td><td>$row[date_paiement]</td><td>$row[nom]</td><td>".($row["montant_payer"])."</td><td>$row[date_enreg]</td><td>$row[paie_exp_num_note]</td><td><input type='checkbox' readonly ".($row["paie_exp_id_acte"]?'checked':'')." title=\"Exp-Acte\" /></td><td>$row[paie_exp_date_ordo]</td><td>$row[nom_assujetti_1]</td><td>$row[adresse_assujetti_1]</td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'NoteActesPayer',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($id_noteacte,$id_releve,$montant_payer,$date_enreg,$paie_exp_num_note,$paie_exp_id_acte,$paie_exp_date_ordo,$paie_exp_id_assujetti){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "INSERT INTO ".self::$table."(id_noteacte,id_releve,montant_payer,date_enreg,paie_exp_num_note,paie_exp_id_acte,paie_exp_date_ordo,paie_exp_id_assujetti,id_visite) VALUES(" .self::$conx->quote($id_noteacte).','.self::$conx->quote($id_releve).','.self::$conx->quote($montant_payer).','.self::$conx->quote($date_enreg).','.self::$conx->quote($paie_exp_num_note).','.self::$conx->quote($paie_exp_id_acte).','.self::$conx->quote($paie_exp_date_ordo).','.self::$conx->quote($paie_exp_id_assujetti). ",'$id_visite');";
			if(self::$conx->exec($req)){
				$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
				return true; 
			}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and (''!=($_POST["id_noteacte"]=($_POST["id_noteacte"]==''?(isset($_SESSION['t_note_actes']['id'])?$_SESSION['t_note_actes']['id']:''):$_POST["id_noteacte"]))) and (''!=($_POST["id_releve"]=($_POST["id_releve"]==''?(isset($_SESSION['t_releve']['id'])?$_SESSION['t_releve']['id']:''):$_POST["id_releve"]))) and ''!=$_POST["date_enreg"] and ''!=$_POST["paie_exp_num_note"] and ''!=$_POST["paie_exp_date_ordo"]){
				if(self::insert($_POST["id_noteacte"],$_POST["id_releve"],"".(isset($_POST["montant_payer"])?1:0)."" ,$_POST["date_enreg"],$_POST["paie_exp_num_note"],"".(isset($_POST["paie_exp_id_acte"])?1:0)."" ,$_POST["paie_exp_date_ordo"],$_POST["paie_exp_id_assujetti"]))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) NoteActesPayer' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
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
				if( true  and (''!=($_POST["id_noteacte"]=($_POST["id_noteacte"]==''?(isset($_SESSION['t_note_actes']['id'])?$_SESSION['t_note_actes']['id']:''):$_POST["id_noteacte"]))) and (''!=($_POST["id_releve"]=($_POST["id_releve"]==''?(isset($_SESSION['t_releve']['id'])?$_SESSION['t_releve']['id']:''):$_POST["id_releve"]))) and ''!=$_POST["date_enreg"] and ''!=$_POST["paie_exp_num_note"] and ''!=$_POST["paie_exp_date_ordo"]){
					$req = ("UPDATE ".self::$table." SET id_noteacte = ".self::$conx->quote($_POST["id_noteacte"])." ,id_releve = ".self::$conx->quote($_POST["id_releve"])." ,montant_payer = '".($_POST["montant_payer"])."' ,date_enreg = ".self::$conx->quote($_POST["date_enreg"])." ,paie_exp_num_note = ".self::$conx->quote($_POST["paie_exp_num_note"])." ,paie_exp_id_acte = '".isset($_POST["paie_exp_id_acte"])."' ,paie_exp_date_ordo = ".self::$conx->quote($_POST["paie_exp_date_ordo"])." ,paie_exp_id_assujetti = ".self::$conx->quote($_POST["paie_exp_id_assujetti"])."  WHERE id = $id;");
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
			if(isset($_POST['class'])and $_POST['class']!='NoteActesPayer')return false;
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
					echo"<script>". self::autoCompleter('id_noteacte').self::autoCompleter('id_releve').self::autoCompleter('paie_exp_id_assujetti'). "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='id_noteacte';$id_noteacte=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='id_releve';$id_releve=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='montant_payer';$montant_payer=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='date_enreg';$date_enreg=isset($array["$cle"])?$array["$cle"]:date('Y-m-d H:i:s'); 
				$cle='paie_exp_num_note';$paie_exp_num_note=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='paie_exp_id_acte';$paie_exp_id_acte=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='paie_exp_date_ordo';$paie_exp_date_ordo=isset($array["$cle"])?$array["$cle"]:date('Y-m-d'); 
				$cle='paie_exp_id_assujetti';$paie_exp_id_assujetti=isset($array["$cle"])?$array["$cle"]:''; 
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
						"<table><caption style='".self::$style_caption."'><b>Suppression NoteActesPayer</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = "<div id='".NoteActe::$id_div_crud."' ></div><br/>".NoteActe::bt_ajout(NoteActe::$bt_class,'Ajouter NoteActe',false)."<div id='".Releve::$id_div_crud."' ></div><br/>".Releve::bt_ajout(Releve::$bt_class,'Ajouter Releve',false)."<div id='".Assujetti::$id_div_crud."' ></div><br/>".Assujetti::bt_ajout(Assujetti::$bt_class,'Ajouter Assujetti',false).
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." NoteActesPayer</caption>".
					"<tr>".
						"<td><label for='id_noteacte' >Acte.NP</label></td><td>:</td>".
						"<td><select id='id_noteacte' name='id_noteacte'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .NoteActe::options('',$id_noteacte)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='id_releve' >Relevé</label></td><td>:</td>".
						"<td><select id='id_releve' name='id_releve'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Releve::options('',$id_releve)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='montant_payer' >Mont. Payé</label></td><td>:</td>".
						"<td><input type='' value=\"".($montant_payer)."\" title=\"Mont. Payé\" id='montant_payer' name='montant_payer' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_enreg' >Enregistrée le</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_enreg\" title=\"Enregistrée le\" placeholder=\"Enregistrée le\" id='date_enreg' name='date_enreg' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_num_note' >Exp-N.P.</label></td><td>:</td>".
						"<td><input type='text' value=\"$paie_exp_num_note\" title=\"Exp-N.P.\" placeholder=\"Exp-N.P.\" id='paie_exp_num_note' name='paie_exp_num_note' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_id_acte' >Exp-Acte</label></td><td>:</td>".
						"<td><input type='checkbox' ".($paie_exp_id_acte?'checked':'')." title=\"Exp-Acte\" id='paie_exp_id_acte' name='paie_exp_id_acte' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_date_ordo' >Exp-Date Ordo</label></td><td>:</td>".
						"<td><input type='text' value=\"$paie_exp_date_ordo\" title=\"Exp-Date Ordo\" placeholder=\"Exp-Date Ordo\" id='paie_exp_date_ordo' name='paie_exp_date_ordo' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_id_assujetti' >Exp-Assujetti</label></td><td>:</td>".
						"<td><select id='paie_exp_id_assujetti' name='paie_exp_id_assujetti'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Assujetti::options('',$paie_exp_id_assujetti)."</select></td>".
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
		public static $T_SGO = array("show","group","order","search","sum");
		public static $T_CH = array();
		public static $TLBL = array();
		public static $CHAMPS = array();
		public static function ini_champ(){
			$T_CH = self::$T_CH = array("id_noteacte","acte","service","art_bud","nom_assujetti","adresse_assujetti","commune","num_bap","montant_bap","note_to","num_note","date_ordo","date_depot","observation","montant_acte","freq","id_releve","nom_banque","date_paiement","nom","montant_payer","date_enreg","paie_exp_num_note","paie_exp_id_acte","paie_exp_date_ordo","paie_exp_id_assujetti","nom_assujetti_1","adresse_assujetti_1");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"nacp.id_noteacte id_noteacte",$label=>"Acte.NP",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"ac.acte acte",$label=>"Acte",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ser.service service",$label=>"Service",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ac.art_bud art_bud",$label=>"Art.B.",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ass.nom_assujetti nom_assujetti",$label=>"Assujetti",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ass.adresse_assujetti adresse_assujetti",$label=>"Adresse Assujetti",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"co.commune commune",$label=>"Antenne",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"no.num_bap num_bap",$label=>"N°Bap",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"no.montant_bap montant_bap",$label=>"Mont.BAP",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"no.note_to note_to",$label=>"TO ?",$type=>"check"),
			$T_CH[$l++]=>array($ch_alias=>"no.num_note num_note",$label=>"N°N.P.",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"no.date_ordo date_ordo",$label=>"Date Ordo.",$type=>"date"),
			$T_CH[$l++]=>array($ch_alias=>"no.date_depot date_depot",$label=>"Date Dépot",$type=>"date"),
			$T_CH[$l++]=>array($ch_alias=>"no.observation observation",$label=>"Obs",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"nac.montant_acte montant_acte",$label=>"Mont. Acte",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"nac.freq freq",$label=>"Fréq.",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"nacp.id_releve id_releve",$label=>"Relevé",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"bq.nom_banque nom_banque",$label=>"Banque",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"rlv.date_paiement date_paiement",$label=>"Payé le",$type=>"date"),
			$T_CH[$l++]=>array($ch_alias=>"us.nom nom",$label=>"Utilisateur",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"nacp.montant_payer montant_payer",$label=>"Mont. Payé",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"nacp.date_enreg date_enreg",$label=>"Enregistrée le",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"nacp.paie_exp_num_note paie_exp_num_note",$label=>"Exp-N.P.",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"nacp.paie_exp_id_acte paie_exp_id_acte",$label=>"Exp-Acte",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"nacp.paie_exp_date_ordo paie_exp_date_ordo",$label=>"Exp-Date Ordo",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"nacp.paie_exp_id_assujetti paie_exp_id_assujetti",$label=>"Exp-Assujetti",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"ass_1.nom_assujetti nom_assujetti_1",$label=>"Assujetti",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ass_1.adresse_assujetti adresse_assujetti_1",$label=>"Adresse Assujetti",$type=>""));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[0]=>1,$T_SGO[3]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[1]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>2,$T_SGO[1]=>2,$T_SGO[0]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>3,$T_SGO[0]=>3),
			$T_CH[$l++]=>array($T_SGO[3]=>4,$T_SGO[0]=>4),
			$T_CH[$l++]=>array($T_SGO[3]=>5,$T_SGO[0]=>5),
			$T_CH[$l++]=>array($T_SGO[3]=>6,$T_SGO[1]=>6,$T_SGO[0]=>6),
			$T_CH[$l++]=>array($T_SGO[3]=>7,$T_SGO[0]=>7),
			$T_CH[$l++]=>array($T_SGO[3]=>8,$T_SGO[0]=>8),
			$T_CH[$l++]=>array($T_SGO[3]=>9,$T_SGO[0]=>9),
			$T_CH[$l++]=>array($T_SGO[3]=>10,$T_SGO[0]=>10),
			$T_CH[$l++]=>array($T_SGO[3]=>11,$T_SGO[0]=>11),
			$T_CH[$l++]=>array($T_SGO[3]=>12,$T_SGO[0]=>12),
			$T_CH[$l++]=>array($T_SGO[3]=>13,$T_SGO[0]=>13),
			$T_CH[$l++]=>array($T_SGO[3]=>14,$T_SGO[0]=>14),
			$T_CH[$l++]=>array($T_SGO[3]=>15,$T_SGO[0]=>15),
			$T_CH[$l++]=>array($T_SGO[0]=>17,$T_SGO[3]=>17),
			$T_CH[$l++]=>array($T_SGO[3]=>17,$T_SGO[0]=>17),
			$T_CH[$l++]=>array($T_SGO[3]=>18,$T_SGO[0]=>18),
			$T_CH[$l++]=>array($T_SGO[3]=>19,$T_SGO[0]=>19),
			$T_CH[$l++]=>array($T_SGO[3]=>21,$T_SGO[4]=>21,$T_SGO[0]=>21),
			$T_CH[$l++]=>array($T_SGO[3]=>22,$T_SGO[0]=>22),
			$T_CH[$l++]=>array($T_SGO[3]=>23,$T_SGO[0]=>23),
			$T_CH[$l++]=>array($T_SGO[3]=>24,$T_SGO[0]=>24),
			$T_CH[$l++]=>array($T_SGO[3]=>25,$T_SGO[0]=>25),
			$T_CH[$l++]=>array($T_SGO[0]=>26,$T_SGO[3]=>26),
			$T_CH[$l++]=>array($T_SGO[3]=>26,$T_SGO[0]=>26),
			$T_CH[$l++]=>array($T_SGO[3]=>27,$T_SGO[0]=>27));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport NoteActesPayer')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT nacp.id $CONSTR[Ch_Select] FROM ".self::$table ." nacp 
				INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
				INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
				INNER JOIN t_note no ON  nac.id_note = no.id  
				INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
				INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
				INNER JOIN t_banque bq ON  rlv.id_banque = bq.id  
				INNER JOIN t_user us ON  rlv.id_user = us.id 
				LEFT JOIN t_assujetti ass_1 ON  nacp.paie_exp_id_assujetti = ass_1.id  ";
			self::suite_rapport($req,$CONSTR,self::$table,self::$marqueur,self::$style_caption,22);
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
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['id_noteacte'][$T_SGO[3]])and self::$CHAMPS['id_noteacte'][$T_SGO[3]])?"":"<select title=\"Acte.NP\" id='nacp_id_noteacte' name='nacp_id_noteacte'  class='".self::$input_class."'><option value=''>Choisir</option>".NoteActe::options('',0)."</select>").
				(!(isset(self::$CHAMPS['acte'][$T_SGO[3]])and self::$CHAMPS['acte'][$T_SGO[3]])?"":"<input type='text' name='ac_acte' title=\"Acte\" placeholder=\"Acte\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['service'][$T_SGO[3]])and self::$CHAMPS['service'][$T_SGO[3]])?"":"<input type='text' name='ser_service' title=\"Service\" placeholder=\"Service\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['art_bud'][$T_SGO[3]])and self::$CHAMPS['art_bud'][$T_SGO[3]])?"":"<input type='text' name='ac_art_bud' title=\"Art.B.\" placeholder=\"Art.B.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['nom_assujetti'][$T_SGO[3]])and self::$CHAMPS['nom_assujetti'][$T_SGO[3]])?"":"<input type='text' name='ass_nom_assujetti' title=\"Assujetti\" placeholder=\"Assujetti\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['adresse_assujetti'][$T_SGO[3]])and self::$CHAMPS['adresse_assujetti'][$T_SGO[3]])?"":"<input type='text' name='ass_adresse_assujetti' title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['commune'][$T_SGO[3]])and self::$CHAMPS['commune'][$T_SGO[3]])?"":"<input type='text' name='co_commune' title=\"Antenne\" placeholder=\"Antenne\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['num_bap'][$T_SGO[3]])and self::$CHAMPS['num_bap'][$T_SGO[3]])?"":"<input type='text' name='no_num_bap' title=\"N°Bap\" placeholder=\"N°Bap\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['montant_bap'][$T_SGO[3]])and self::$CHAMPS['montant_bap'][$T_SGO[3]])?"":"<input type='text' name='no_montant_bap' title=\"Mont.BAP\" placeholder=\"Mont.BAP\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['note_to'][$T_SGO[3]])and self::$CHAMPS['note_to'][$T_SGO[3]])?"":"<input type='text' name='no_note_to' title=\"TO ?\" placeholder=\"TO ?\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['num_note'][$T_SGO[3]])and self::$CHAMPS['num_note'][$T_SGO[3]])?"":"<input type='text' name='no_num_note' title=\"N°N.P.\" placeholder=\"N°N.P.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_ordo'][$T_SGO[3]])and self::$CHAMPS['date_ordo'][$T_SGO[3]])?"":"<input type='text' name='no_date_ordo' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Date Ordo.\" placeholder=\"Date Ordo.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_depot'][$T_SGO[3]])and self::$CHAMPS['date_depot'][$T_SGO[3]])?"":"<input type='text' name='no_date_depot' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Date Dépot\" placeholder=\"Date Dépot\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['observation'][$T_SGO[3]])and self::$CHAMPS['observation'][$T_SGO[3]])?"":"<input type='text' name='no_observation' title=\"Obs\" placeholder=\"Obs\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['montant_acte'][$T_SGO[3]])and self::$CHAMPS['montant_acte'][$T_SGO[3]])?"":"<input type='text' name='nac_montant_acte' title=\"Mont. Acte\" placeholder=\"Mont. Acte\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['freq'][$T_SGO[3]])and self::$CHAMPS['freq'][$T_SGO[3]])?"":"<input type='text' name='nac_freq' title=\"Fréq.\" placeholder=\"Fréq.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['id_releve'][$T_SGO[3]])and self::$CHAMPS['id_releve'][$T_SGO[3]])?"":"<select title=\"Relevé\" id='nacp_id_releve' name='nacp_id_releve'  class='".self::$input_class."'><option value=''>Choisir</option>".Releve::options('',0)."</select>").
				(!(isset(self::$CHAMPS['nom_banque'][$T_SGO[3]])and self::$CHAMPS['nom_banque'][$T_SGO[3]])?"":"<input type='text' name='bq_nom_banque' title=\"Banque\" placeholder=\"Banque\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_paiement'][$T_SGO[3]])and self::$CHAMPS['date_paiement'][$T_SGO[3]])?"":"<input type='text' name='rlv_date_paiement' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Payé le\" placeholder=\"Payé le\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['nom'][$T_SGO[3]])and self::$CHAMPS['nom'][$T_SGO[3]])?"":"<input type='text' name='us_nom' title=\"Utilisateur\" placeholder=\"Utilisateur\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['montant_payer'][$T_SGO[3]])and self::$CHAMPS['montant_payer'][$T_SGO[3]])?"":"<input type='text' name='nacp_montant_payer' title=\"Mont. Payé\" placeholder=\"Mont. Payé\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_enreg'][$T_SGO[3]])and self::$CHAMPS['date_enreg'][$T_SGO[3]])?"":"<input type='text' name='nacp_date_enreg' title=\"Enregistrée le\" placeholder=\"Enregistrée le\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['paie_exp_num_note'][$T_SGO[3]])and self::$CHAMPS['paie_exp_num_note'][$T_SGO[3]])?"":"<input type='text' name='nacp_paie_exp_num_note' title=\"Exp-N.P.\" placeholder=\"Exp-N.P.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['paie_exp_id_acte'][$T_SGO[3]])and self::$CHAMPS['paie_exp_id_acte'][$T_SGO[3]])?"":"<input type='text' name='nacp_paie_exp_id_acte' title=\"Exp-Acte\" placeholder=\"Exp-Acte\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['paie_exp_date_ordo'][$T_SGO[3]])and self::$CHAMPS['paie_exp_date_ordo'][$T_SGO[3]])?"":"<input type='text' name='nacp_paie_exp_date_ordo' title=\"Exp-Date Ordo\" placeholder=\"Exp-Date Ordo\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['paie_exp_id_assujetti'][$T_SGO[3]])and self::$CHAMPS['paie_exp_id_assujetti'][$T_SGO[3]])?"":"<select title=\"Exp-Assujetti\" id='nacp_paie_exp_id_assujetti' name='nacp_paie_exp_id_assujetti'  class='".self::$input_class."'><option value=''>Choisir</option>".Assujetti::options('',0)."</select>").
				(!(isset(self::$CHAMPS['nom_assujetti_1'][$T_SGO[3]])and self::$CHAMPS['nom_assujetti_1'][$T_SGO[3]])?"":"<input type='text' name='ass_1_nom_assujetti' title=\"Assujetti\" placeholder=\"Assujetti\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['adresse_assujetti_1'][$T_SGO[3]])and self::$CHAMPS['adresse_assujetti_1'][$T_SGO[3]])?"":"<input type='text' name='ass_1_adresse_assujetti' title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config NoteActesPayer')."' onClick=\"".self::codeOnCallConfig()."\"/>
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
	