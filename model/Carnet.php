<?php
	require_once("chado.php");
	class Carnet extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array();
		public static $table 	= 't_carnet';
		public static $marqueur = "<input type='hidden' name='class' value='Carnet' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_Carnet';
		public static $id_div_crud = 'id_div_crud_Carnet';
		public static $retourHtmlRapConfig = 'div_form_rapp_Carnet';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
		public static function liste_objet($where,$limit){
			$ch_id = "car.id";
			$Select = "SELECT $ch_id, car.num_debut,crl.date_lot ,crl.lot_description ,car.souche,car.date_epuisement,car.observ_carnet FROM ".self::$table ." car ";
			$req = "$Select 
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." car ";
			$req_count = "$count 
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id  $where ";
			if($pdo_result = self::$conx->query($req_count)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			if($pdo_result = self::$conx->query($req)){
				$rows = array();
				while($row	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$row['check_souche'] = "<input type='checkbox' readonly ".($row["souche"]?'checked':'')." title=\"Souche\" />"; 
					$rows[] = $row;
				}
				return $rows;
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		function detail($id){
			$ch_id = "car.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, car.num_debut,crl.date_lot ,crl.lot_description ,car.souche,car.date_epuisement,car.observ_carnet FROM ".self::$table ." car ";
			$req = "$Select 
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table><caption style='".self::$style_caption."'><b>Détails sur Carnet</b></caption>
					<tbody style='text-align:left'>
					<tr><th>N°Début</th><td>:</td><td>$row[num_debut]</td></tr>
					<tr><th>Lot du</th><td>:</td><td>$row[date_lot]</td></tr>
					<tr><th>Lot description</th><td>:</td><td>$row[lot_description]</td></tr>
					<tr><th>Souche</th><td>:</td><td><input type='checkbox' readonly ".($row["souche"]?'checked':'')." title=\"Souche\" /></td></tr>
					<tr><th>Epuisé le</th><td>:</td><td>$row[date_epuisement]</td></tr>
					<tr><th>Observation</th><td>:</td><td>$row[observ_carnet]</td></tr>
					</tbody>
				</table>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Carnet ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de Carnet</label>
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
			$ch_id = "car.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, car.num_debut,crl.date_lot ,crl.lot_description ,car.souche,car.date_epuisement,car.observ_carnet FROM ".self::$table ." car ";
			$req = "$Select 
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur Carnet</b></caption>N°Début : $row[num_debut] Lot du : $row[date_lot] Lot description : $row[lot_description] Souche : <input type='checkbox' readonly ".($row["souche"]?'checked':'')." title=\"Souche\" /> Epuisé le : $row[date_epuisement] Observation : $row[observ_carnet] 
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier Carnet ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de Carnet</label>";
				
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
				<input style='width:100px' type='text' name='car_num_debut'  title=\"N°Début\" placeholder=\"N°Début\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='crl_date_lot' title=\"Lot du\" placeholder=\"Lot du\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='crl_lot_description' title=\"Lot description\" placeholder=\"Lot description\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='car_date_epuisement' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Epuisé le\" placeholder=\"Epuisé le\"  class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='car_observ_carnet'  title=\"Observation\" placeholder=\"Observation\" class='".self::$input_class."'/>
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
			$Select = "SELECT car.id ,car.is_deleted ,car.num_debut,crl.date_lot ,crl.lot_description ,car.souche,car.date_epuisement,car.observ_carnet ";
			$FROM = " FROM ".self::$table ." car ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or car.num_debut like '%$w%' or  crl.date_lot like '%$w%' or  crl.lot_description like '%$w%' or  car.souche like '%$w%' or  car.date_epuisement like '%$w%' or  car.observ_carnet like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t=$_POST["car_num_debut"])!=''?(" AND car.num_debut LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t = $_POST["crl_date_lot"])!=''?(" AND crl.date_lot LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["crl_lot_description"])!=''?(" AND crl.lot_description LIKE ". self::$conx->quote("%$t%")):"") 
			.(((($count=(count($t=explode(':',($trim = trim($_POST["car_date_epuisement"]))))))==1and $trim!='') or $count==2)?(" AND car.date_epuisement BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.(trim($t=$_POST["car_observ_carnet"])!=''?(" AND car.observ_carnet LIKE ".self::$conx->quote("%$t%")):"" )
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= " 
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id  $Filtre ";
			$order = " order by car.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["Carnet"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["Carnet"]["t"])?$_SESSION["Carnet"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de Carnets</b></caption><tr><th></th><th>N°</th><th>N°Début</th><th>Lot du</th><th>Lot description</th><th>Souche</th><th>Epuisé le</th><th>Observation</th>".
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
					echo "<td>".($i++)."</td><td>$row[num_debut]</td><td>$row[date_lot]</td><td>$row[lot_description]</td><td><input type='checkbox' readonly ".($row["souche"]?'checked':'')." title=\"Souche\" /></td><td>$row[date_epuisement]</td><td>$row[observ_carnet]</td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'Carnet',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($num_debut,$id_lot,$souche,$date_epuisement,$observ_carnet){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "INSERT INTO ".self::$table."(num_debut,id_lot,souche,date_epuisement,observ_carnet,id_visite) VALUES(" .self::$conx->quote($num_debut).','.self::$conx->quote($id_lot).','.self::$conx->quote($souche).','.self::$conx->quote($date_epuisement).','.self::$conx->quote($observ_carnet). ",'$id_visite');";
			if(self::$conx->exec($req)){
				$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
				return true; 
			}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and ''!=$_POST["num_debut"] and (''!=($_POST["id_lot"]=($_POST["id_lot"]==''?(isset($_SESSION['t_carnet_lot']['id'])?$_SESSION['t_carnet_lot']['id']:''):$_POST["id_lot"]))) and ''!=$_POST["date_epuisement"] and ''!=$_POST["observ_carnet"]){
				if(self::insert($_POST["num_debut"],$_POST["id_lot"],"".(isset($_POST["souche"])?1:0)."" ,$_POST["date_epuisement"],$_POST["observ_carnet"]))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) Carnet' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
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
				if( true  and ''!=$_POST["num_debut"] and (''!=($_POST["id_lot"]=($_POST["id_lot"]==''?(isset($_SESSION['t_carnet_lot']['id'])?$_SESSION['t_carnet_lot']['id']:''):$_POST["id_lot"]))) and ''!=$_POST["date_epuisement"] and ''!=$_POST["observ_carnet"]){
					$req = ("UPDATE ".self::$table." SET num_debut = ".self::$conx->quote($_POST["num_debut"])." ,id_lot = ".self::$conx->quote($_POST["id_lot"])." ,souche = '".isset($_POST["souche"])."' ,date_epuisement = ".self::$conx->quote($_POST["date_epuisement"])." ,observ_carnet = ".self::$conx->quote($_POST["observ_carnet"])."  WHERE id = $id;");
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
			if(isset($_POST['class'])and $_POST['class']!='Carnet')return false;
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
					echo"<script>". self::autoCompleter('id_lot'). "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='num_debut';$num_debut=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='id_lot';$id_lot=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='souche';$souche=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='date_epuisement';$date_epuisement=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='observ_carnet';$observ_carnet=isset($array["$cle"])?$array["$cle"]:''; 
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
						"<table><caption style='".self::$style_caption."'><b>Suppression Carnet</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = "<div id='".CarnetLot::$id_div_crud."' ></div><br/>".CarnetLot::bt_ajout(CarnetLot::$bt_class,'Ajouter CarnetLot',false).
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." Carnet</caption>".
					"<tr>".
						"<td><label for='num_debut' >N°Début</label></td><td>:</td>".
						"<td><input type='text' value=\"$num_debut\" title=\"N°Début\" placeholder=\"N°Début\" id='num_debut' name='num_debut' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='id_lot' >Lot</label></td><td>:</td>".
						"<td><select id='id_lot' name='id_lot'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .CarnetLot::options('',$id_lot)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='souche' >Souche</label></td><td>:</td>".
						"<td><input type='checkbox' ".($souche?'checked':'')." title=\"Souche\" id='souche' name='souche' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_epuisement' >Epuisé le</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_epuisement\" title=\"Epuisé le\" placeholder=\"Epuisé le\" id='date_epuisement' name='date_epuisement' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='observ_carnet' >Observation</label></td><td>:</td>".
						"<td><input type='text' value=\"$observ_carnet\" title=\"Observation\" placeholder=\"Observation\" id='observ_carnet' name='observ_carnet' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
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
			$req = "SELECT car.id Value ,concat(car.num_debut) DisplayText FROM ".self::$table ." car  WHERE car.is_deleted=0 $where order by car.id desc ";
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
			$T_CH = self::$T_CH = array("num_debut","id_lot","date_lot","lot_description","souche","date_epuisement","observ_carnet");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"car.num_debut num_debut",$label=>"N°Début",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"car.id_lot id_lot",$label=>"Lot",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"crl.date_lot date_lot",$label=>"Lot du",$type=>"date"),
			$T_CH[$l++]=>array($ch_alias=>"crl.lot_description lot_description",$label=>"Lot description",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"car.souche souche",$label=>"Souche",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"car.date_epuisement date_epuisement",$label=>"Epuisé le",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"car.observ_carnet observ_carnet",$label=>"Observation",$type=>"text"));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[0]=>2,$T_SGO[3]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>2,$T_SGO[0]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>3,$T_SGO[0]=>3),
			$T_CH[$l++]=>array($T_SGO[3]=>5,$T_SGO[0]=>5),
			$T_CH[$l++]=>array($T_SGO[3]=>6,$T_SGO[0]=>6),
			$T_CH[$l++]=>array($T_SGO[3]=>7,$T_SGO[0]=>7));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport Carnet')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT car.id $CONSTR[Ch_Select] FROM ".self::$table ." car 
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id  ";
			self::suite_rapport($req,$CONSTR,self::$table,self::$marqueur,self::$style_caption,6);
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
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['num_debut'][$T_SGO[3]])and self::$CHAMPS['num_debut'][$T_SGO[3]])?"":"<input type='text' name='car_num_debut' title=\"N°Début\" placeholder=\"N°Début\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['id_lot'][$T_SGO[3]])and self::$CHAMPS['id_lot'][$T_SGO[3]])?"":"<select title=\"Lot\" id='car_id_lot' name='car_id_lot'  class='".self::$input_class."'><option value=''>Choisir</option>".CarnetLot::options('',0)."</select>").
				(!(isset(self::$CHAMPS['date_lot'][$T_SGO[3]])and self::$CHAMPS['date_lot'][$T_SGO[3]])?"":"<input type='text' name='crl_date_lot' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Lot du\" placeholder=\"Lot du\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['lot_description'][$T_SGO[3]])and self::$CHAMPS['lot_description'][$T_SGO[3]])?"":"<input type='text' name='crl_lot_description' title=\"Lot description\" placeholder=\"Lot description\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['souche'][$T_SGO[3]])and self::$CHAMPS['souche'][$T_SGO[3]])?"":"<input type='text' name='car_souche' title=\"Souche\" placeholder=\"Souche\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_epuisement'][$T_SGO[3]])and self::$CHAMPS['date_epuisement'][$T_SGO[3]])?"":"<input type='text' name='car_date_epuisement' title=\"Epuisé le\" placeholder=\"Epuisé le\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['observ_carnet'][$T_SGO[3]])and self::$CHAMPS['observ_carnet'][$T_SGO[3]])?"":"<input type='text' name='car_observ_carnet' title=\"Observation\" placeholder=\"Observation\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config Carnet')."' onClick=\"".self::codeOnCallConfig()."\"/>
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
	