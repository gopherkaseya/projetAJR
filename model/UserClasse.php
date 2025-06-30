<?php
	require_once("chado.php");
	class UserClasse extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array();
		public static $table 	= 'sys_user_classe';
		public static $marqueur = "<input type='hidden' name='class' value='UserClasse' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_UserClasse';
		public static $id_div_crud = 'id_div_crud_UserClasse';
		public static $retourHtmlRapConfig = 'div_form_rapp_UserClasse';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
		public static function liste_objet($where,$limit){
			$ch_id = "sys_user_classe.id";
			$Select = "SELECT $ch_id, sys_utilisateur.identite ,sys_classe_in_code.class_nom  FROM ".self::$table ." sys_user_classe ";
			$req = "$Select 
				INNER JOIN sys_utilisateur sys_utilisateur ON  sys_user_classe.id_utilisateur = sys_utilisateur.id 
				INNER JOIN sys_classe_in_code sys_classe_in_code ON  sys_user_classe.id_classe_in_code = sys_classe_in_code.id  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." sys_user_classe ";
			$req_count = "$count 
				INNER JOIN sys_utilisateur sys_utilisateur ON  sys_user_classe.id_utilisateur = sys_utilisateur.id 
				INNER JOIN sys_classe_in_code sys_classe_in_code ON  sys_user_classe.id_classe_in_code = sys_classe_in_code.id  $where ";
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
			$ch_id = "sys_user_classe.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, sys_utilisateur.identite ,sys_classe_in_code.class_nom  FROM ".self::$table ." sys_user_classe ";
			$req = "$Select 
				INNER JOIN sys_utilisateur sys_utilisateur ON  sys_user_classe.id_utilisateur = sys_utilisateur.id 
				INNER JOIN sys_classe_in_code sys_classe_in_code ON  sys_user_classe.id_classe_in_code = sys_classe_in_code.id  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table><caption style='".self::$style_caption."'><b>Détails sur UserClasse</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Utilisateur</th><td>:</td><td>$row[identite]</td></tr>
					<tr><th>Classe In Code</th><td>:</td><td>$row[class_nom]</td></tr>
					</tbody>
				</table>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier UserClasse ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de UserClasse</label>
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
			$ch_id = "sys_user_classe.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, sys_utilisateur.identite ,sys_classe_in_code.class_nom  FROM ".self::$table ." sys_user_classe ";
			$req = "$Select 
				INNER JOIN sys_utilisateur sys_utilisateur ON  sys_user_classe.id_utilisateur = sys_utilisateur.id 
				INNER JOIN sys_classe_in_code sys_classe_in_code ON  sys_user_classe.id_classe_in_code = sys_classe_in_code.id  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur UserClasse</b></caption>Utilisateur : $row[identite] Classe In Code : $row[class_nom] 
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier UserClasse ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de UserClasse</label>";
				
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
				<input style='width:100px' type='text' name='sys_utilisateur_identite' title=\"Utilisateur\" placeholder=\"Utilisateur\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='sys_classe_in_code_class_nom' title=\"Classe In Code\" placeholder=\"Classe In Code\" class='".self::$input_class."'/>
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
			$Select = "SELECT sys_user_classe.id ,sys_user_classe.is_deleted ,sys_utilisateur.identite ,sys_classe_in_code.class_nom  ";
			$FROM = " FROM ".self::$table ." sys_user_classe ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or sys_utilisateur.identite like '%$w%' or  sys_classe_in_code.class_nom like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t = $_POST["sys_utilisateur_identite"])!=''?(" AND sys_utilisateur.identite LIKE ". self::$conx->quote("%$t%")):"") 
			.(trim($t = $_POST["sys_classe_in_code_class_nom"])!=''?(" AND sys_classe_in_code.class_nom LIKE ". self::$conx->quote("%$t%")):"") 
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= " 
				INNER JOIN sys_utilisateur sys_utilisateur ON  sys_user_classe.id_utilisateur = sys_utilisateur.id 
				INNER JOIN sys_classe_in_code sys_classe_in_code ON  sys_user_classe.id_classe_in_code = sys_classe_in_code.id  $Filtre ";
			$order = " order by sys_user_classe.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["UserClasse"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["UserClasse"]["t"])?$_SESSION["UserClasse"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de UserClasses</b></caption><tr><th></th><th>N°</th><th>Utilisateur</th><th>Classe In Code</th>".
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
					echo "<td>".($i++)."</td><td>$row[identite]</td><td>$row[class_nom]</td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'UserClasse',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($id_utilisateur,$id_classe_in_code){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "INSERT INTO ".self::$table."(id_utilisateur,id_classe_in_code,id_visite) VALUES(" .self::$conx->quote($id_utilisateur).','.self::$conx->quote($id_classe_in_code). ",'$id_visite');";
			if(self::$conx->exec($req)){
				$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
				return true; 
			}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and (''!=($_POST["id_utilisateur"]=($_POST["id_utilisateur"]==''?(isset($_SESSION['sys_utilisateur']['id'])?$_SESSION['sys_utilisateur']['id']:''):$_POST["id_utilisateur"]))) and (''!=($_POST["id_classe_in_code"]=($_POST["id_classe_in_code"]==''?(isset($_SESSION['sys_classe_in_code']['id'])?$_SESSION['sys_classe_in_code']['id']:''):$_POST["id_classe_in_code"])))){
				if(self::insert($_POST["id_utilisateur"],$_POST["id_classe_in_code"]))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) UserClasse' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
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
				if( true  and (''!=($_POST["id_utilisateur"]=($_POST["id_utilisateur"]==''?(isset($_SESSION['sys_utilisateur']['id'])?$_SESSION['sys_utilisateur']['id']:''):$_POST["id_utilisateur"]))) and (''!=($_POST["id_classe_in_code"]=($_POST["id_classe_in_code"]==''?(isset($_SESSION['sys_classe_in_code']['id'])?$_SESSION['sys_classe_in_code']['id']:''):$_POST["id_classe_in_code"])))){
					$req = ("UPDATE ".self::$table." SET id_utilisateur = ".self::$conx->quote($_POST["id_utilisateur"])." ,id_classe_in_code = ".self::$conx->quote($_POST["id_classe_in_code"])."  WHERE id = $id;");
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
			if(isset($_POST['class'])and $_POST['class']!='UserClasse')return false;
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
					echo"<script>". self::autoCompleter('id_utilisateur').self::autoCompleter('id_classe_in_code'). "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='id_utilisateur';$id_utilisateur=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='id_classe_in_code';$id_classe_in_code=isset($array["$cle"])?$array["$cle"]:''; 
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
						"<table><caption style='".self::$style_caption."'><b>Suppression UserClasse</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = "<div id='".Utilisateur::$id_div_crud."' ></div><br/>".Utilisateur::bt_ajout(Utilisateur::$bt_class,'Ajouter Utilisateur',false)."<div id='".Classe_in_Code::$id_div_crud."' ></div><br/>".Classe_in_Code::bt_ajout(Classe_in_Code::$bt_class,'Ajouter Classe_in_Code',false).
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." UserClasse</caption>".
					"<tr>".
						"<td><label for='id_utilisateur' >Utilisateur</label></td><td>:</td>".
						"<td><select id='id_utilisateur' name='id_utilisateur'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Utilisateur::options('',$id_utilisateur)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='id_classe_in_code' >Classe In Code</label></td><td>:</td>".
						"<td><select id='id_classe_in_code' name='id_classe_in_code'  class='".self::$input_class."' style='".self::$input_style."' ><option value=''>Choisir</option>" .Classe_in_Code::options('',$id_classe_in_code)."</select></td>".
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
			$T_CH = self::$T_CH = array("id_utilisateur","identite","id_classe_in_code","class_nom");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"sys_user_classe.id_utilisateur id_utilisateur",$label=>"Utilisateur",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"sys_utilisateur.identite identite",$label=>"Utilisateur",$type=>""),
			$T_CH[$l++]=>array($ch_alias=>"sys_user_classe.id_classe_in_code id_classe_in_code",$label=>"Classe In Code",$type=>"select"),
			$T_CH[$l++]=>array($ch_alias=>"sys_classe_in_code.class_nom class_nom",$label=>"Classe In Code",$type=>""));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[0]=>1,$T_SGO[3]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[1]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[0]=>3,$T_SGO[3]=>3),
			$T_CH[$l++]=>array($T_SGO[3]=>3,$T_SGO[0]=>3));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport UserClasse')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT sys_user_classe.id $CONSTR[Ch_Select] FROM ".self::$table ." sys_user_classe 
				INNER JOIN sys_utilisateur sys_utilisateur ON  sys_user_classe.id_utilisateur = sys_utilisateur.id 
				INNER JOIN sys_classe_in_code sys_classe_in_code ON  sys_user_classe.id_classe_in_code = sys_classe_in_code.id  ";
			self::suite_rapport($req,$CONSTR,self::$table,self::$marqueur,self::$style_caption,1);
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
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['id_utilisateur'][$T_SGO[3]])and self::$CHAMPS['id_utilisateur'][$T_SGO[3]])?"":"<select title=\"Utilisateur\" id='sys_user_classe_id_utilisateur' name='sys_user_classe_id_utilisateur'  class='".self::$input_class."'><option value=''>Choisir</option>".Utilisateur::options('',0)."</select>").
				(!(isset(self::$CHAMPS['identite'][$T_SGO[3]])and self::$CHAMPS['identite'][$T_SGO[3]])?"":"<input type='text' name='sys_utilisateur_identite' title=\"Utilisateur\" placeholder=\"Utilisateur\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['id_classe_in_code'][$T_SGO[3]])and self::$CHAMPS['id_classe_in_code'][$T_SGO[3]])?"":"<select title=\"Classe In Code\" id='sys_user_classe_id_classe_in_code' name='sys_user_classe_id_classe_in_code'  class='".self::$input_class."'><option value=''>Choisir</option>".Classe_in_Code::options('',0)."</select>").
				(!(isset(self::$CHAMPS['class_nom'][$T_SGO[3]])and self::$CHAMPS['class_nom'][$T_SGO[3]])?"":"<input type='text' name='sys_classe_in_code_class_nom' title=\"Classe In Code\" placeholder=\"Classe In Code\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config UserClasse')."' onClick=\"".self::codeOnCallConfig()."\"/>
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
	