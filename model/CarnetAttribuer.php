<?php
	require_once("chado.php");
	class CarnetAttribuer extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array();
		public static $table 	= 't_carnet_attribuer';
		public static $marqueur = "<input type='hidden' name='class' value='CarnetAttribuer' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_CarnetAttribuer';
		public static $id_div_crud = 'id_div_crud_CarnetAttribuer';
		public static $retourHtmlRapConfig = 'div_form_rapp_CarnetAttribuer';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
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
		
		function detail($id){
			$ch_id = "c_at.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, car.num_debut ,crl.date_lot ,crl.lot_description ,car.souche ,car.date_epuisement ,co.commune ,ser.service ,c_at.date_attribution FROM ".self::$table ." c_at ";
			$req = "$Select 
				INNER JOIN t_carnet car ON  c_at.id_carnet = car.id  
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id 
				LEFT JOIN t_commune co ON  c_at.id_commune = co.id 
				LEFT JOIN t_service ser ON  c_at.id_service = ser.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table><caption style='".self::$style_caption."'><b>Détails sur CarnetAttribuer</b></caption>
					<tbody style='text-align:left'>
					<tr><th>N°Début</th><td>:</td><td>$row[num_debut]</td></tr>
					<tr><th>Lot du</th><td>:</td><td>$row[date_lot]</td></tr>
					<tr><th>Lot description</th><td>:</td><td>$row[lot_description]</td></tr>
					<tr><th>Souche</th><td>:</td><td>$row[souche]</td></tr>
					<tr><th>Epuisé le</th><td>:</td><td>$row[date_epuisement]</td></tr>
					<tr><th>Antenne</th><td>:</td><td>$row[commune]</td></tr>
					<tr><th>Service</th><td>:</td><td>$row[service]</td></tr>
					<tr><th>Attribué le</th><td>:</td><td>$row[date_attribution]</td></tr>
					</tbody>
				</table>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier CarnetAttribuer ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de CarnetAttribuer</label>
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
			$ch_id = "c_at.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, car.num_debut ,crl.date_lot ,crl.lot_description ,car.souche ,car.date_epuisement ,co.commune ,ser.service ,c_at.date_attribution FROM ".self::$table ." c_at ";
			$req = "$Select 
				INNER JOIN t_carnet car ON  c_at.id_carnet = car.id  
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id 
				LEFT JOIN t_commune co ON  c_at.id_commune = co.id 
				LEFT JOIN t_service ser ON  c_at.id_service = ser.id  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur CarnetAttribuer</b></caption>N°Début : $row[num_debut] Lot du : $row[date_lot] Lot description : $row[lot_description] Souche : $row[souche] Epuisé le : $row[date_epuisement] Antenne : $row[commune] Service : $row[service] Attribué le : $row[date_attribution] 
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier CarnetAttribuer ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de CarnetAttribuer</label>";
				
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
				echo "
				<form method='POST' action='traitement_sup.php' target='iframe' style='border:solid 0px;margin:0;display:inline'>
					<input type='text' name='num_note' class='form-control' title='N° Note'  placeholder='N° Debut Carnet' required style='padding: 8px;width:100px' />
					<input type='submit' name='bt_note_restantes' class='btn maia-button' style='height:35px' value='Note Non depossées' />
				</form>";
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
				<input style='width:100px' type='text' name='car_num_debut' title=\"N°Début\" placeholder=\"N°Début\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='crl_date_lot' title=\"Lot du\" placeholder=\"Lot du\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='crl_lot_description' title=\"Lot description\" placeholder=\"Lot description\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='car_souche' title=\"Souche\" placeholder=\"Souche\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='car_date_epuisement' title=\"Epuisé le\" placeholder=\"Epuisé le\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='co_commune' title=\"Antenne\" placeholder=\"Antenne\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='ser_service' title=\"Service\" placeholder=\"Service\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='c_at_date_attribution' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Attribué le\" placeholder=\"Attribué le\"  class='".self::$input_class."'/>
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
			$Select = "SELECT c_at.id ,c_at.is_deleted ,car.num_debut ,crl.date_lot ,crl.lot_description ,car.souche ,car.date_epuisement ,co.commune ,ser.service ,c_at.date_attribution ";
			$FROM = " FROM ".self::$table ." c_at ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or car.num_debut like '%$w%' or  crl.date_lot like '%$w%' or  crl.lot_description like '%$w%' or  car.souche like '%$w%' or  car.date_epuisement like '%$w%' or  co.commune like '%$w%' or  ser.service like '%$w%' or  c_at.date_attribution like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t = $_POST["car_num_debut"])!=''?(" AND car.num_debut LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["crl_date_lot"])!=''?(" AND crl.date_lot LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["crl_lot_description"])!=''?(" AND crl.lot_description LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["car_souche"])!=''?(" AND car.souche LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["car_date_epuisement"])!=''?(" AND car.date_epuisement LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["co_commune"])!=''?(" AND co.commune LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["ser_service"])!=''?(" AND ser.service LIKE ". self::$conx->quote("%$t%")):"") 
			.(((($count=(count($t=explode(':',($trim = trim($_POST["c_at_date_attribution"]))))))==1and $trim!='') or $count==2)?(" AND c_at.date_attribution BETWEEN ".self::$conx->quote($t[0])." AND ".self::$conx->quote($t[count($t)-1])):"" )
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= " 
				INNER JOIN t_carnet car ON  c_at.id_carnet = car.id  
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id 
				LEFT JOIN t_commune co ON  c_at.id_commune = co.id 
				LEFT JOIN t_service ser ON  c_at.id_service = ser.id  $Filtre ";
			$order = " order by c_at.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["CarnetAttribuer"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["CarnetAttribuer"]["t"])?$_SESSION["CarnetAttribuer"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de CarnetAttribuers</b></caption><tr><th></th><th>N°</th><th>N°Début</th><th>Lot du</th><th>Lot description</th><th>Souche</th><th>Epuisé le</th><th>Antenne</th><th>Service</th><th>Attribué le</th>".
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
					echo "<td>".($i++)."</td><td>$row[num_debut]</td><td>$row[date_lot]</td><td>$row[lot_description]</td><td>$row[souche]</td><td>$row[date_epuisement]</td><td>$row[commune]</td><td>$row[service]</td><td>$row[date_attribution]</td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'CarnetAttribuer',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($id_carnet,$id_commune,$id_service,$date_attribution){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "SELECT * FROM ".self::$table." WHERE 
			id_carnet=" .self::$conx->quote($id_carnet).' AND 
			id_commune='.self::$conx->quote($id_commune).' AND 
			id_service='.self::$conx->quote($id_service);
			if($pdo_result = self::$conx->query($req)){
				if($rows = $pdo_result->fetchAll(PDO::FETCH_ASSOC)){
					if(count($rows))
					self::$notification .="CET ENREGISTREMENT EXISTE DEJA DANS LA BASE DE DONNEE";
					return false;
				}
				//self::$count = $row['nbre'];
			}
			$req = "INSERT INTO ".self::$table."(id_carnet,id_commune,id_service,date_attribution,id_visite) VALUES(" .self::$conx->quote($id_carnet).','.self::$conx->quote($id_commune).','.self::$conx->quote($id_service).','.self::$conx->quote($date_attribution). ",'$id_visite');";
			// vérification de nombre de carnet en cours
			$nbr_limit = 1;$nbr_carnet_encour = 0;$rows=array();
			if($id_service=="")
				$req_nbr_car_limit="select co.commune nom, co.c_car_nbr_limit l,num_debut n,date_attribution d from t_commune co 
				inner join t_carnet_attribuer ca on co.id=ca.id_commune
				inner join t_carnet c on c.id = ca.id_carnet
				where co.id=$id_commune and souche=0 ";
			else $req_nbr_car_limit="select co.service nom,co.s_car_nbr_limit l,num_debut n,date_attribution d from t_service co 
				inner join t_carnet_attribuer ca on co.id=ca.id_service
				inner join t_carnet c on c.id = ca.id_carnet
				where co.id=$id_service and souche=0 ";
			if($pdo_result = self::$conx->query($req_nbr_car_limit)){
				if($rows = $pdo_result->fetchAll(PDO::FETCH_ASSOC)){
					$nbr_limit = $rows[0]["l"];
					$nbr_carnet_encour = count($rows);
				}
				//self::$count = $row['nbre'];
			}
			
			if($nbr_carnet_encour<$nbr_limit){
				if(self::$conx->exec($req)){
					$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
					return true; 
				}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			}else{
				$msg="<h3 style='color:red;margin:0'>Erreur</h3><h5 style='color:red;margin:0'>Nombre carnet limit pour ".$rows[0]["nom"].": $nbr_limit.</h5><br>Il y a $nbr_carnet_encour carnet(s) en cours: ";
				$carnets="";
				for($i=0;$i!=$nbr_carnet_encour;$i++){
					$carnets.=($carnets!=""?",":"").$rows[$i]["n"];
					$msg.="<br>".($i+1).".&nbsp; ".$rows[$i]["n"]." attribué le ".$rows[$i]["d"].", ";
				}
				self::$notification .= $msg."<br>Utilisez la(les) série(s) suivante(s) pour voir la(les) note(s) en cours<br><p style='font-size:18px;font-weight:bold'>$carnets</p>";
			}
						
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and (''!=($_POST["id_carnet"]=($_POST["id_carnet"]==''?(isset($_SESSION['t_carnet']['id'])?$_SESSION['t_carnet']['id']:''):$_POST["id_carnet"]))) and (''!=($_POST["id_commune"]=($_POST["id_commune"]==''?(isset($_SESSION['t_commune']['id'])?$_SESSION['t_commune']['id']:''):$_POST["id_commune"]))) and ''!=$_POST["date_attribution"]){
				if(self::insert($_POST["id_carnet"],$_POST["id_commune"],$_POST["id_service"],$_POST["date_attribution"]))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) CarnetAttribuer' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
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
				if( true  and (''!=($_POST["id_carnet"]=($_POST["id_carnet"]==''?(isset($_SESSION['t_carnet']['id'])?$_SESSION['t_carnet']['id']:''):$_POST["id_carnet"]))) and ''!=$_POST["date_attribution"]){
					$req = ("UPDATE ".self::$table." SET id_carnet = ".self::$conx->quote($_POST["id_carnet"])." ,id_commune = ".self::$conx->quote($_POST["id_commune"])." ,id_service = ".self::$conx->quote($_POST["id_service"])." ,date_attribution = ".self::$conx->quote($_POST["date_attribution"])."  WHERE id = $id;");
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
			if(isset($_POST['class'])and $_POST['class']!='CarnetAttribuer')return false;
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
					echo"<script>". self::autoCompleter('id_carnet').self::autoCompleter('id_commune').self::autoCompleter('id_service'). "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='id_carnet';$id_carnet=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='id_commune';$id_commune=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='id_service';$id_service=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='date_attribution';$date_attribution=isset($array["$cle"])?$array["$cle"]:date("Y-m-d"); 
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
						"<table><caption style='".self::$style_caption."'><b>Suppression CarnetAttribuer</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = "<div id='".Carnet::$id_div_crud."' ></div><br/>".Carnet::bt_ajout(Carnet::$bt_class,'Ajouter Carnet',false)."<div id='".Commune::$id_div_crud."' ></div><br/>".Commune::bt_ajout(Commune::$bt_class,'Ajouter Commune',false)."<div id='".Service::$id_div_crud."' ></div><br/>".Service::bt_ajout(Service::$bt_class,'Ajouter Service',false).
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." CarnetAttribuer</caption>".
					"<tr>".
						"<td><label for='id_carnet' >Carnet</label></td><td>:</td>".
						"<td><select id='id_carnet' name='id_carnet'  class='".self::$input_class."' style='".self::$input_style."' required ><option value=''>Choisir</option><option value='0'>Aucun</option>" .Carnet::options('',$id_carnet)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='id_commune' >Antenne</label></td><td>:</td>".
						"<td><select id='id_commune' name='id_commune'  class='".self::$input_class."' style='".self::$input_style."' required ><option value=''>Choisir</option>" .Commune::options('',$id_commune)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='id_service' >Service</label></td><td>:</td>".
						"<td><select id='id_service' name='id_service'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Tous les sersvices</option>" .Service::options('',$id_service)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='date_attribution' >Attribué le</label></td><td>:</td>".
						"<td><input type='text' value=\"$date_attribution\" title=\"Attribué le\" placeholder=\"Attribué le\" id='date_attribution' name='date_attribution' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
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
			$T_CH = self::$T_CH = array("id_carnet","num_debut","date_lot","lot_description","souche","date_epuisement","id_commune","commune","id_service","service","date_attribution");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"c_at.id_carnet id_carnet",$label=>"Carnet",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"car.num_debut num_debut",$label=>"N°Début",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"crl.date_lot date_lot",$label=>"Lot du",$type=>"date"),
			$T_CH[$l++]=>array($ch_alias=>"crl.lot_description lot_description",$label=>"Lot description",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"car.souche souche",$label=>"Souche",$type=>"check"),
			$T_CH[$l++]=>array($ch_alias=>"car.date_epuisement date_epuisement",$label=>"Epuisé le",$type=>"date"),
			$T_CH[$l++]=>array($ch_alias=>"c_at.id_commune id_commune",$label=>"Antenne",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"co.commune commune",$label=>"Antenne",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"c_at.id_service id_service",$label=>"Service",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"ser.service service",$label=>"Service",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"c_at.date_attribution date_attribution",$label=>"Attribué le",$type=>"text"));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[0]=>1,$T_SGO[3]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>2,$T_SGO[0]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>3,$T_SGO[0]=>3),
			$T_CH[$l++]=>array($T_SGO[3]=>4,$T_SGO[0]=>4),
			$T_CH[$l++]=>array($T_SGO[3]=>5,$T_SGO[0]=>5),
			$T_CH[$l++]=>array($T_SGO[0]=>7,$T_SGO[3]=>7),
			$T_CH[$l++]=>array($T_SGO[3]=>7,$T_SGO[1]=>7,$T_SGO[0]=>7),
			$T_CH[$l++]=>array($T_SGO[0]=>9,$T_SGO[3]=>9),
			$T_CH[$l++]=>array($T_SGO[3]=>9,$T_SGO[1]=>9,$T_SGO[0]=>9),
			$T_CH[$l++]=>array($T_SGO[3]=>11,$T_SGO[0]=>11));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport CarnetAttribuer')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT c_at.id $CONSTR[Ch_Select] FROM ".self::$table ." c_at 
				INNER JOIN t_carnet car ON  c_at.id_carnet = car.id  
				INNER JOIN t_carnet_lot crl ON  car.id_lot = crl.id 
				LEFT JOIN t_commune co ON  c_at.id_commune = co.id 
				LEFT JOIN t_service ser ON  c_at.id_service = ser.id  ";
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
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['id_carnet'][$T_SGO[3]])and self::$CHAMPS['id_carnet'][$T_SGO[3]])?"":"<select title=\"Carnet\" id='c_at_id_carnet' name='c_at_id_carnet'  class='".self::$input_class."'><option value=''>Choisir</option>".Carnet::options('',0)."</select>").
				(!(isset(self::$CHAMPS['num_debut'][$T_SGO[3]])and self::$CHAMPS['num_debut'][$T_SGO[3]])?"":"<input type='text' name='car_num_debut' title=\"N°Début\" placeholder=\"N°Début\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_lot'][$T_SGO[3]])and self::$CHAMPS['date_lot'][$T_SGO[3]])?"":"<input type='text' name='crl_date_lot' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Lot du\" placeholder=\"Lot du\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['lot_description'][$T_SGO[3]])and self::$CHAMPS['lot_description'][$T_SGO[3]])?"":"<input type='text' name='crl_lot_description' title=\"Lot description\" placeholder=\"Lot description\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['souche'][$T_SGO[3]])and self::$CHAMPS['souche'][$T_SGO[3]])?"":"<input type='text' name='car_souche' title=\"Souche\" placeholder=\"Souche\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_epuisement'][$T_SGO[3]])and self::$CHAMPS['date_epuisement'][$T_SGO[3]])?"":"<input type='text' name='car_date_epuisement' title=\"  AAAA-MM-JJ:AAAA-MM-JJ Epuisé le\" placeholder=\"Epuisé le\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['id_commune'][$T_SGO[3]])and self::$CHAMPS['id_commune'][$T_SGO[3]])?"":"<select title=\"Antenne\" id='c_at_id_commune' name='c_at_id_commune'  class='".self::$input_class."'><option value=''>Choisir</option>".Commune::options('',0)."</select>").
				(!(isset(self::$CHAMPS['commune'][$T_SGO[3]])and self::$CHAMPS['commune'][$T_SGO[3]])?"":"<input type='text' name='co_commune' title=\"Antenne\" placeholder=\"Antenne\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['id_service'][$T_SGO[3]])and self::$CHAMPS['id_service'][$T_SGO[3]])?"":"<select title=\"Service\" id='c_at_id_service' name='c_at_id_service'  class='".self::$input_class."'><option value=''>Choisir</option>".Service::options('',0)."</select>").
				(!(isset(self::$CHAMPS['service'][$T_SGO[3]])and self::$CHAMPS['service'][$T_SGO[3]])?"":"<input type='text' name='ser_service' title=\"Service\" placeholder=\"Service\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['date_attribution'][$T_SGO[3]])and self::$CHAMPS['date_attribution'][$T_SGO[3]])?"":"<input type='text' name='c_at_date_attribution' title=\"Attribué le\" placeholder=\"Attribué le\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config CarnetAttribuer')."' onClick=\"".self::codeOnCallConfig()."\"/>
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
	