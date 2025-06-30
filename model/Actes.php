<?php
	require_once("chado.php");
	class Actes extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array();
		public static $table 	= 't_acte';
		public static $marqueur = "<input type='hidden' name='class' value='Actes' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_Actes';
		public static $id_div_crud = 'id_div_crud_Actes';
		public static $retourHtmlRapConfig = 'div_form_rapp_Actes';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
		public static function liste_objet($where,$limit){
			$ch_id = "ac.id";
			$Select = "SELECT $ch_id, ac.acte,ser.service ,ac.art_bud,ac.date_arrete,ac.penalite,ac.coefficient,ac.date_role FROM ".self::$table ." ac ";
			$req = "$Select 
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." ac ";
			$req_count = "$count 
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  $where ";
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
		
		function detail($id){
			$ch_id = "ac.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, ac.acte,ser.service ,ac.art_bud,ac.date_arrete,ac.penalite,ac.coefficient,ac.date_role FROM ".self::$table ." ac ";
			$req = "$Select 
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table><caption style='".self::$style_caption."'><b>Détails sur Actes</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Acte</th><td>:</td><td>$row[acte]</td></tr>
					<tr><th>Service</th><td>:</td><td>$row[service]</td></tr>
					<tr><th>Art.B.</th><td>:</td><td>$row[art_bud]</td></tr>
					<tr><th>date Arrêté</th><td>:</td><td>$row[date_arrete]</td></tr>
					<tr><th>Pénalité</th><td>:</td><td>$row[penalite]</td></tr>
					<tr><th>Coeff.</th><td>:</td><td>$row[coefficient]</td></tr>
					<tr><th>Date Rôle</th><td>:</td><td>$row[date_role]</td></tr>
					</tbody>
				</table>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Actes ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de Actes</label>
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
			$ch_id = "ac.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, ac.acte,ser.service ,ac.art_bud,ac.date_arrete,ac.penalite,ac.coefficient,ac.date_role FROM ".self::$table ." ac ";
			$req = "$Select 
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur Actes</b></caption>Acte : $row[acte] Service : $row[service] Art.B. : $row[art_bud] date Arrêté : $row[date_arrete] Pénalité : $row[penalite] Coeff. : $row[coefficient] Date Rôle : $row[date_role] 
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Actes ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de Actes</label>";
				
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
				<input style='width:100px' type='text' name='ac_acte'  title=\"Acte\" placeholder=\"Acte\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ser_service' title=\"Service\" placeholder=\"Service\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ac_art_bud'  title=\"Art.B.\" placeholder=\"Art.B.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ac_date_arrete'  title=\"date Arrêté\" placeholder=\"date Arrêté\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ac_penalite'  title=\"Pénalité\" placeholder=\"Pénalité\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ac_coefficient'  title=\"Coeff.\" placeholder=\"Coeff.\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ac_date_role'  title=\"Date Rôle\" placeholder=\"Date Rôle\" class='".self::$input_class."'/>
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
			$Select = "SELECT ac.id ,ac.is_deleted ,ac.acte,ser.service ,ac.art_bud,ac.date_arrete,ac.penalite,ac.coefficient,ac.date_role ";
			$FROM = " FROM ".self::$table ." ac ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or ac.acte like '%$w%' or  ser.service like '%$w%' or  ac.art_bud like '%$w%' or  ac.date_arrete like '%$w%' or  ac.penalite like '%$w%' or  ac.coefficient like '%$w%' or  ac.date_role like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t=$_POST["ac_acte"])!=''?(" AND ac.acte LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t = $_POST["ser_service"])!=''?(" AND ser.service LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t=$_POST["ac_art_bud"])!=''?(" AND ac.art_bud LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["ac_date_arrete"])!=''?(" AND ac.date_arrete LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["ac_penalite"])!=''?(" AND ac.penalite LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["ac_coefficient"])!=''?(" AND ac.coefficient LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["ac_date_role"])!=''?(" AND ac.date_role LIKE ".self::$conx->quote("%$t%")):"" )
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= " 
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  $Filtre ";
			$order = " order by ac.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["Actes"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["Actes"]["t"])?$_SESSION["Actes"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de Actess</b></caption><tr><th></th><th>N°</th><th>Acte</th><th>Service</th><th>Art.B.</th><th>date Arrêté</th><th>Pénalité</th><th>Coeff.</th><th>Date Rôle</th>".
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
					echo "<td>".($i++)."</td><td>$row[acte]</td><td>$row[service]</td><td>$row[art_bud]</td><td>$row[date_arrete]</td><td>$row[penalite]</td><td>$row[coefficient]</td><td>$row[date_role]</td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'Actes',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($acte,$acte_id_service,$art_bud,$date_arrete,$penalite,$coefficient,$date_role){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "INSERT INTO ".self::$table."(acte,acte_id_service,art_bud,date_arrete,penalite,coefficient,date_role,id_visite) VALUES(" .self::$conx->quote($acte).','.self::$conx->quote($acte_id_service).','.self::$conx->quote($art_bud).','.self::$conx->quote($date_arrete).','.self::$conx->quote($penalite).','.self::$conx->quote($coefficient).','.self::$conx->quote($date_role). ",'$id_visite');";
			if(self::$conx->exec($req)){
				$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
				return true; 
			}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and ''!=$_POST["acte"] and (''!=($_POST["acte_id_service"]=($_POST["acte_id_service"]==''?(isset($_SESSION['t_service']['id'])?$_SESSION['t_service']['id']:''):$_POST["acte_id_service"]))) and ''!=$_POST["art_bud"] and ''!=$_POST["date_arrete"] and ''!=$_POST["penalite"] and ''!=$_POST["coefficient"] and ''!=$_POST["date_role"]){
				if(self::insert($_POST["acte"],$_POST["acte_id_service"],$_POST["art_bud"],$_POST["date_arrete"],$_POST["penalite"],$_POST["coefficient"],$_POST["date_role"]))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) Actes' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
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
				if( true  and ''!=$_POST["acte"] and (''!=($_POST["acte_id_service"]=($_POST["acte_id_service"]==''?(isset($_SESSION['t_service']['id'])?$_SESSION['t_service']['id']:''):$_POST["acte_id_service"]))) and ''!=$_POST["art_bud"] and ''!=$_POST["date_arrete"] and ''!=$_POST["penalite"] and ''!=$_POST["coefficient"] and ''!=$_POST["date_role"]){
					$req = ("UPDATE ".self::$table." SET acte = ".self::$conx->quote($_POST["acte"])." ,acte_id_service = ".self::$conx->quote($_POST["acte_id_service"])." ,art_bud = ".self::$conx->quote($_POST["art_bud"])." ,date_arrete = ".self::$conx->quote($_POST["date_arrete"])." ,penalite = ".self::$conx->quote($_POST["penalite"])." ,coefficient = ".self::$conx->quote($_POST["coefficient"])." ,date_role = ".self::$conx->quote($_POST["date_role"])."  WHERE id = $id;");
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
			if(isset($_POST['class'])and $_POST['class']!='Actes')return false;
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
					echo"<script>". self::autoCompleter('acte_id_service'). "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='acte';$acte=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='acte_id_service';$acte_id_service=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='art_bud';$art_bud=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='date_arrete';$date_arrete=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='penalite';$penalite=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='coefficient';$coefficient=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='date_role';$date_role=isset($array["$cle"])?$array["$cle"]:''; 
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
						"<table><caption style='".self::$style_caption."'><b>Suppression Actes</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = "<div id='".Service::$id_div_crud."' ></div><br/>".Service::bt_ajout(Service::$bt_class,'Ajouter Service',false).
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." Actes</caption>".
					"<tr>".
						"<td><label for='acte' >Acte</label></td><td>:</td>".
						"<td><input type='text' value=\"$acte\" title=\"Acte\" placeholder=\"Acte\" id='acte' name='acte' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='acte_id_service' >Service</label></td><td>:</td>".
						"<td><select id='acte_id_service' name='acte_id_service'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Service::options('',$acte_id_service)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='art_bud' >Art.B.</label></td><td>:</td>".
						"<td><input type='text' value=\"$art_bud\" title=\"Art.B.\" placeholder=\"Art.B.\" id='art_bud' name='art_bud' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_arrete' >date Arrêté</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_arrete\" title=\"date Arrêté\" placeholder=\"date Arrêté\" id='date_arrete' name='date_arrete' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='penalite' >Pénalité</label></td><td>:</td>".
						"<td><input type='text' value=\"$penalite\" title=\"Pénalité\" placeholder=\"Pénalité\" id='penalite' name='penalite' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='coefficient' >Coeff.</label></td><td>:</td>".
						"<td><input type='text' value=\"$coefficient\" title=\"Coeff.\" placeholder=\"Coeff.\" id='coefficient' name='coefficient' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_role' >Date Rôle</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_role\" title=\"Date Rôle\" placeholder=\"Date Rôle\" id='date_role' name='date_role' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
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
			$req = "SELECT ac.id Value ,concat(ac.acte,' ',ac.art_bud) DisplayText FROM ".self::$table ." ac  WHERE ac.is_deleted=0 $where ";
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
			$T_CH = self::$T_CH = array("acte","acte_id_service","service","art_bud","date_arrete","penalite","coefficient","date_role");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"ac.acte acte",$label=>"Acte",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"ac.acte_id_service acte_id_service",$label=>"Service",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"ser.service service",$label=>"Service",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"ac.art_bud art_bud",$label=>"Art.B.",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"ac.date_arrete date_arrete",$label=>"date Arrêté",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"ac.penalite penalite",$label=>"Pénalité",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"ac.coefficient coefficient",$label=>"Coeff.",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"ac.date_role date_role",$label=>"Date Rôle",$type=>"text"));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[1]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[0]=>2,$T_SGO[3]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>2,$T_SGO[1]=>2,$T_SGO[0]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>4,$T_SGO[0]=>4),
			$T_CH[$l++]=>array($T_SGO[3]=>5,$T_SGO[0]=>5),
			$T_CH[$l++]=>array($T_SGO[3]=>6,$T_SGO[0]=>6),
			$T_CH[$l++]=>array($T_SGO[3]=>7,$T_SGO[0]=>7),
			$T_CH[$l++]=>array($T_SGO[3]=>8,$T_SGO[0]=>8));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport Actes')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT ac.id $CONSTR[Ch_Select] FROM ".self::$table ." ac 
				INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  ";
			self::suite_rapport($req,$CONSTR,self::$table,self::$marqueur,self::$style_caption,5);
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
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['acte'][$T_SGO[3]])and self::$CHAMPS['acte'][$T_SGO[3]])?"":"<input type='text' name='ac_acte' title=\"Acte\" placeholder=\"Acte\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['acte_id_service'][$T_SGO[3]])and self::$CHAMPS['acte_id_service'][$T_SGO[3]])?"":"<select title=\"Service\" id='ac_acte_id_service' name='ac_acte_id_service'  class='".self::$input_class."'><option value=''>Choisir</option>".Service::options('',0)."</select>").
				(!(isset(self::$CHAMPS['service'][$T_SGO[3]])and self::$CHAMPS['service'][$T_SGO[3]])?"":"<input type='text' name='ser_service' title=\"Service\" placeholder=\"Service\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['art_bud'][$T_SGO[3]])and self::$CHAMPS['art_bud'][$T_SGO[3]])?"":"<input type='text' name='ac_art_bud' title=\"Art.B.\" placeholder=\"Art.B.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_arrete'][$T_SGO[3]])and self::$CHAMPS['date_arrete'][$T_SGO[3]])?"":"<input type='text' name='ac_date_arrete' title=\"date Arrêté\" placeholder=\"date Arrêté\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['penalite'][$T_SGO[3]])and self::$CHAMPS['penalite'][$T_SGO[3]])?"":"<input type='text' name='ac_penalite' title=\"Pénalité\" placeholder=\"Pénalité\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['coefficient'][$T_SGO[3]])and self::$CHAMPS['coefficient'][$T_SGO[3]])?"":"<input type='text' name='ac_coefficient' title=\"Coeff.\" placeholder=\"Coeff.\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_role'][$T_SGO[3]])and self::$CHAMPS['date_role'][$T_SGO[3]])?"":"<input type='text' name='ac_date_role' title=\"Date Rôle\" placeholder=\"Date Rôle\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config Actes')."' onClick=\"".self::codeOnCallConfig()."\"/>
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
	