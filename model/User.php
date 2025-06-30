<?php
	require_once("chado.php");
	class User extends Chado
	{
		public static $id;
		public static $bt_class = 'btn ';
		public static $input_style = 'width:250px';
		public static $input_class = 'form-control ';
		public static $cham_img= array('str_img');
		public static $table 	= 't_user';
		public static $marqueur = "<input type='hidden' name='class' value='User' >";
		public static $liste_modifiable= true;
		public static $useDetailStandard= true;
		public static $id_div_liste = 'id_div_liste_User';
		public static $id_div_crud = 'id_div_crud_User';
		public static $retourHtmlRapConfig = 'div_form_rapp_User';
		public static $readOnly= false;
		public static $limit = '';
		
		public function __construct(){
			// self::div_html();
		}
		
		public static $count = 0;
		public static function liste_objet($where,$limit){
			$ch_id = "us.id";
			$Select = "SELECT $ch_id, us.nom,us.password,us.login,us.role,us.titre,us.genre_user,us.valide,us.str_img FROM ".self::$table ." us ";
			$req = "$Select  $where $limit";
			
			$count = "SELECT count(*) nbre FROM ".self::$table ." us ";
			$req_count = "$count  $where ";
			if($pdo_result = self::$conx->query($req_count)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			if($pdo_result = self::$conx->query($req)){
				$rows = array();
				while($row	= $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$row['check_valide'] = "<input type='checkbox' readonly ".($row["valide"]?'checked':'')." title=\"Valide ?\" />"; 
					$row['src_str_img'] = self::$upload."/User/str_img/$row[str_img]";
					$row['str_img'] = "<img src='".self::$upload."/User/str_img/$row[str_img]' style='width:100px;display:block' />";
					$rows[] = $row;
				}
				return $rows;
			}
			else if(self::$conx->errorInfo()[2]!="") {self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		function detail($id){
			$ch_id = "us.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, us.nom,us.password,us.login,us.role,us.titre,us.genre_user,us.valide,us.str_img FROM ".self::$table ." us ";
			$req = "$Select  $where ";
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<div align='center' style='margin-bottom:3px;padding:5px ;background-color:#f8f8f8;border: 1px solid #ccc;border-radius:2px' >
				<table><caption style='".self::$style_caption."'><b>Détails sur User</b></caption>
					<tbody style='text-align:left'>
					<tr><th>Utilisateur</th><td>:</td><td>$row[nom]</td></tr>
					<tr><th>Mot De passe</th><td>:</td><td>$row[password]</td></tr>
					<tr><th>Login</th><td>:</td><td>$row[login]</td></tr>
					<tr><th>Rôle</th><td>:</td><td>$row[role]</td></tr>
					<tr><th>Titre</th><td>:</td><td>$row[titre]</td></tr>
					<tr><th>Genre</th><td>:</td><td>$row[genre_user]</td></tr>
					<tr><th>Valide ?</th><td>:</td><td><input type='checkbox' readonly ".($row["valide"]?'checked':'')." title=\"Valide ?\" /></td></tr>
					<tr><th>Image</th><td>:</td><td><img src='".self::$upload."/User/str_img/$row[str_img]' style='width:100px;display:block' /></td></tr>
					</tbody>
				</table>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier User ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de User</label>
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
			$ch_id = "us.id";
			$where = "WHERE  $ch_id ";
			if(isset($_POST['bt_next_detail_'.self::$table])) $where .= " < '$id' ORDER BY $ch_id ASC limit 0,1 ";
			else if(isset($_POST['bt_prev_detail_'.self::$table])) $where .= " > '$id' ORDER BY $ch_id DESC limit 0,1 ";
			else $where .= " = '$id'";
			$Select = "SELECT $ch_id, us.nom,us.password,us.login,us.role,us.titre,us.genre_user,us.valide,us.str_img FROM ".self::$table ." us ";
			$req = "$Select  $where ";
			
			if($pdo_result = self::$conx->query($req)){
				$row		= $pdo_result->fetch(PDO::FETCH_ASSOC);
				echo"<div id='".self::$id_div_crud."' style='' >
				<p><caption style='".self::$style_caption."'><b>Détails sur User</b></caption>Utilisateur : $row[nom] Mot De passe : $row[password] Login : $row[login] Rôle : $row[role] Titre : $row[titre] Genre : $row[genre_user] Valide ? : <input type='checkbox' readonly ".($row["valide"]?'checked':'')." title=\"Valide ?\" /> Image : <img src='".self::$upload."/User/str_img/$row[str_img]' style='width:100px;display:block' />
				</p>
				".((!self::$readOnly)?
				"<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block'  ><input type='submit'  name='bt_lancer_mod_".self::$table."' value='Modifier User ?' class='".self::$bt_class." btn-primary' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>":"");
				
				echo " <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_prev_detail_".self::$table."' value='Préc.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form>  <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline' ><input type='submit'  name='bt_next_detail_".self::$table."' value='Suiv.' class='".self::$bt_class."' /><input type='hidden' name='id' value='$row[id]' />".self::$marqueur." </form> ";
				
				echo " <label class='".self::$bt_class."'  id='bt_termier_".self::$table."'  onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" style='display:inline-block' >Retourner à la liste de User</label>";
				
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
				<input style='width:100px' type='text' name='us_nom'  title=\"Utilisateur\" placeholder=\"Utilisateur\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='us_password'  title=\"Mot De passe\" placeholder=\"Mot De passe\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='us_login'  title=\"Login\" placeholder=\"Login\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='us_role'  title=\"Rôle\" placeholder=\"Rôle\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='us_titre'  title=\"Titre\" placeholder=\"Titre\" class='".self::$input_class."'/>
				<input style='width:100px' type='text' name='us_genre_user'  title=\"Genre\" placeholder=\"Genre\" class='".self::$input_class."'/>
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
			$Select = "SELECT us.id ,us.is_deleted ,us.nom,us.password,us.login,us.role,us.titre,us.genre_user,us.valide ";
			$FROM = " FROM ".self::$table ." us ";
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["Filtre"]) and "" != $_POST["Filtre"]){
				$w = $_POST["Filtre"];
				$Filtre .= " AND (1=2 or us.nom like '%$w%' or  us.password like '%$w%' or  us.login like '%$w%' or  us.role like '%$w%' or  us.titre like '%$w%' or  us.genre_user like '%$w%' or  us.valide like '%$w%') ";
			}
			if(isset($_POST["bt_lancer_actAv_".self::$table])){
			$Filtre .= " AND (1=1 " 
			.(trim($t=$_POST["us_nom"])!=''?(" AND us.nom LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["us_password"])!=''?(" AND us.password LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["us_login"])!=''?(" AND us.login LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["us_role"])!=''?(" AND us.role LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["us_titre"])!=''?(" AND us.titre LIKE ".self::$conx->quote("%$t%")):"" )
			.(trim($t=$_POST["us_genre_user"])!=''?(" AND us.genre_user LIKE ".self::$conx->quote("%$t%")):"" )
			.")";
			}
			$rows= array(); $recordCount= 0;
			$FROM .= "  $Filtre ";
			$order = " order by us.id DESC";
			
			$req_c = "SELECT count(*) nbre $FROM ";
			if($pdo_result = self::$conx->query($req_c)){
				$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				self::$count = $row['nbre'];
			}
			
			$taille = $_SESSION["User"]["t"] = (isset($_POST["taille_pg"])and $_POST["taille_pg"]!="")?$_POST["taille_pg"]:(isset($_SESSION["User"]["t"])?$_SESSION["User"]["t"]:10);
			$active_pg 	= isset($_GET["active_pg"])?$_GET['active_pg']:1;
			$d 		= (isset($_GET["d_pg"])?" $_GET[d_pg]":"0");
			self::$limit = " limit $d,".$taille;
			
			if($pdo_result = self::$conx->query($Select.$FROM.$order.self::$limit)){
				$input = "".self::$marqueur."";
				echo"<div id='".self::$id_div_liste."' style='' >
				<table style='margin-top:4px'>
					<caption style='".self::$style_caption."'><b>Liste de Users</b></caption><tr><th></th><th>N°</th><th>Utilisateur</th><th>Mot De passe</th><th>Login</th><th>Rôle</th><th>Titre</th><th>Genre</th><th>Valide ?</th>".
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
					echo "<td>".($i++)."</td><td>$row[nom]</td><td>$row[password]</td><td>$row[login]</td><td>$row[role]</td><td>$row[titre]</td><td>$row[genre_user]</td><td><input type='checkbox' readonly ".($row["valide"]?'checked':'')." title=\"Valide ?\" /></td>";
					eval($crud);
					echo "</tr>";
				}
				echo '</table>';			
				paginner(self::$count,$active_pg,$taille,'User',self::$id_div_liste,false);
				echo'</div>';
			}
			else if(self::$conx->errorInfo()[2]!="") {echo self::$notification .= self::$conx->errorInfo()[2];}
		}
		
		public static function insert($nom,$password,$login,$role,$titre,$genre_user,$valide){
			$id_visite = isset($_SESSION['sys_visites']['id'])?$_SESSION['sys_visites']['id']:0;
			$req = "INSERT INTO ".self::$table."(nom,password,login,role,titre,genre_user,valide,id_visite) VALUES(" .self::$conx->quote($nom).','.self::$conx->quote($password).','.self::$conx->quote($login).','.self::$conx->quote($role).','.self::$conx->quote($titre).','.self::$conx->quote($genre_user).','.self::$conx->quote($valide). ",'$id_visite');";
			if(self::$conx->exec($req)){
				$_SESSION[self::$table]['id'] = self::$id = self::$conx->lastInsertId();
				return true;
				// enregistrement des images potentielles
				$this->enregistrer_fichier(self::$id); 
			}else self::$notification .= self::$notifInsErro.self::$conx->errorInfo()[2];
			return false;
		}
		//insertion dans la base de données
		function creer() {
			if( true  and ''!=$_POST["nom"] and ''!=$_POST["password"] and ''!=$_POST["login"] and ''!=$_POST["role"] and ''!=$_POST["titre"] and ''!=$_POST["genre_user"]){
				if(self::insert($_POST["nom"],$_POST["password"],$_POST["login"],$_POST["role"],$_POST["titre"],$_POST["genre_user"],"".(isset($_POST["valide"])?1:0)."" ))
					self::$notification .= self::$notifInsSucc." <form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."' ><input type='submit'  name='bt_detail_".self::$table."' value='Détailler dernier(ère) User' class='".self::$bt_class."' /><input type='hidden' name='id' value='".self::$id."' />".self::$marqueur." </form> "; 
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
				if( true  and ''!=$_POST["nom"] and ''!=$_POST["password"] and ''!=$_POST["login"] and ''!=$_POST["role"] and ''!=$_POST["titre"] and ''!=$_POST["genre_user"]){
					$req = ("UPDATE ".self::$table." SET nom = ".self::$conx->quote($_POST["nom"])." ,password = ".self::$conx->quote($_POST["password"])." ,login = ".self::$conx->quote($_POST["login"])." ,role = ".self::$conx->quote($_POST["role"])." ,titre = ".self::$conx->quote($_POST["titre"])." ,genre_user = ".self::$conx->quote($_POST["genre_user"])." ,valide = '".isset($_POST["valide"])."'  WHERE id = $id;");
					if(self::$conx->exec($req) or self::$conx->errorInfo()[2]==''){
						self::$notification .= self::$notifUpdSucc;
					}else self::$notification .= self::$notifUpdErro.self::$conx->errorInfo()[2];
					// modification des images potentielles
					$this->enregistrer_fichier($id); // si la classe à créer contient un champ img  
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
			if(isset($_POST['class'])and $_POST['class']!='User')return false;
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
					echo"<script>".  "</script>";
				
				if(isset($_POST["id"]))self::$id = $_POST["id"];
			}return true;
		}
		
		function form($opt,$array){
			
				$cle='nom';$nom=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='password';$password=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='login';$login=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='role';$role=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='titre';$titre=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='genre_user';$genre_user=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='valide';$valide=isset($array["$cle"])?$array["$cle"]:''; 
				$cle='str_img';$str_img=isset($array["$cle"])?$array["$cle"]:''; 
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
						"<table><caption style='".self::$style_caption."'><b>Suppression User</caption><tr><td >Voulez-vous réellement supprimer ? $ch_input<tr><td style='text-align:center'>
						<input type='submit' id='' name='bt_sup_".self::$table."' value='Oui' 	style='' class='".self::$bt_class." btn-danger' > ".
						"<input type='reset'  id='' name='bt_resset_".self::$table."' value='Non' style='' onClick=\"cache1_et_affiche2('".self::$id_div_crud."','".self::$retourHtmlAjaxLIST."');\" class='".self::$bt_class."' ></td></tr></table>".
					"</form>";$ok=true;
				} else self::$notification .= self::$notifNoSelct;
			}
			else  if( $opt=='ajt' or $opt=='mod') {
				$form = 
				"<form enctype='multipart/form-data' method='post' action='".self::$ficherAction."' target='".self::$ficherIframe."' style=''>".
					"$ch_input".
					"<table>
						<caption style='".self::$style_caption."'><b>".($opt=='ajt'?'Enregistrement':'Modification')." User</caption>".
					"<tr>".
						"<td><label for='nom' >Utilisateur</label></td><td>:</td>".
						"<td><input type='text' value=\"$nom\" title=\"Utilisateur\" placeholder=\"Utilisateur\" id='nom' name='nom' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='password' >Mot De passe</label></td><td>:</td>".
						"<td><input type='text' value=\"$password\" title=\"Mot De passe\" placeholder=\"Mot De passe\" id='password' name='password' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='login' >Login</label></td><td>:</td>".
						"<td><input type='text' value=\"$login\" title=\"Login\" placeholder=\"Login\" id='login' name='login' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='role' >Rôle</label></td><td>:</td>".
						"<td><input type='text' value=\"$role\" title=\"Rôle\" placeholder=\"Rôle\" id='role' name='role' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='titre' >Titre</label></td><td>:</td>".
						"<td><input type='text' value=\"$titre\" title=\"Titre\" placeholder=\"Titre\" id='titre' name='titre' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='genre_user' >Genre</label></td><td>:</td>".
						"<td><input type='text' value=\"$genre_user\" title=\"Genre\" placeholder=\"Genre\" id='genre_user' name='genre_user' required  class='".self::$input_class."' style='".self::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='valide' >Valide ?</label></td><td>:</td>".
						"<td><input type='checkbox' ".($valide?'checked':'')." title=\"Valide ?\" id='valide' name='valide' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='str_img' >Image</label></td><td>:</td>".
						"<td>".($str_img!=''?
							"<img src='".self::$upload."/User/str_img/$str_img' style='width:100px;display:block' />":'')."<input type='file' value=\"$str_img\" title=\"Image\" id='str_img' name='str_img' class='".self::$input_class."' style='".self::$input_style."' /></td>".
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
		// les fichier seront toujours enregistré dans un répertoire portant le nom de la classe et dans un sous répertoire portant le nom du champ et ayant un de l'id de l'enregistrement en fin de l'indice (l'indice permettra de différencier plusieurs fichier sauvegarder pour un même enregistrement
		// sauvegarde des fichier
		// parcourir la liste des champs qui sont censé stocké des fichers et essayer d'enregistrer pour chacun d'eux
		public function sauver_img($champ,$id){
			$I = new Image($champ);
			if($I->sauver(self::$upload."/User/$champ",$id)){
				// self::$notification .= $I->message.'<br/>';
				if(!($this->mod_champ($id.'.'.$I->extension,$champ,$id))){
					self::$notification .= 'Erreur Image affectation<br/>';
					return false;
				}
				return true;// else self::$notification .= 'Image affect&eacute;e<br/>';
			}else self::$notification .= $I->erreur.'<br/>';
			return false;
		}
		public function enregistrer_fichier($id){
			// on essaie d'enregistrer pour un enregistrement toutes les image pour tous les champs devant recevoir des img
			foreach(self::$cham_img as $cham){
				$this->sauver_img($cham,$id);
			}
			// et si pour le même enregistrement on devra stocker d'autre fichier on le ferait juste ici en bas
			// ...
		}
		public static function options($where,$id) {
			$where = str_replace('where','and',$where);
			$req = "SELECT us.id Value ,concat(us.nom) DisplayText FROM ".self::$table ." us  WHERE us.is_deleted=0 $where ";
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
			$T_CH = self::$T_CH = array("nom","password","login","role","titre","genre_user","valide","str_img");
			$l=0;$ch_alias="c";$label="l";$type="t";
			self::$TLBL = array(
			$T_CH[$l++]=>array($ch_alias=>"us.nom nom",$label=>"Utilisateur",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.password password",$label=>"Mot De passe",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.login login",$label=>"Login",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.role role",$label=>"Rôle",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.titre titre",$label=>"Titre",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.genre_user genre_user",$label=>"Genre",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.valide valide",$label=>"Valide ?",$type=>"text"),
			$T_CH[$l++]=>array($ch_alias=>"us.str_img str_img",$label=>"Image",$type=>"text"));
		
			$l=0;$T_SGO = self::$T_SGO;
			self::$CHAMPS = array(
			$T_CH[$l++]=>array($T_SGO[3]=>1,$T_SGO[0]=>1),
			$T_CH[$l++]=>array($T_SGO[3]=>2,$T_SGO[0]=>2),
			$T_CH[$l++]=>array($T_SGO[3]=>3,$T_SGO[0]=>3),
			$T_CH[$l++]=>array($T_SGO[3]=>4,$T_SGO[0]=>4),
			$T_CH[$l++]=>array($T_SGO[3]=>5,$T_SGO[0]=>5),
			$T_CH[$l++]=>array($T_SGO[3]=>6,$T_SGO[0]=>6),
			$T_CH[$l++]=>array($T_SGO[3]=>7,$T_SGO[0]=>7),
			$T_CH[$l++]=>array($T_SGO[3]=>8,$T_SGO[0]=>8));
		}
		public static function bt_rapport($class,$val){
			if(!self::$readOnly){echo "
				<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
					<input type='submit' class='$class' id='bt_rapport_complet_".self::$table."' name='bt_rapport_complet_".self::$table."' value='".($val!=''?$val:'Rapport User')."' onClick=\"".self::codeOnCallRapport()."\"/>
					".self::$marqueur."
				</form>";
			}
		}
		public static function rapport() {
			if(!count(self::$CHAMPS))
				self::ini_champ();
			$CONSTR = self::creer_ch_select_group_order(self::$TLBL,self::$T_CH,self::$T_SGO,self::$CHAMPS,self::$table);
			
			$req = "SELECT us.id $CONSTR[Ch_Select] FROM ".self::$table ." us  ";
			self::suite_rapport($req,$CONSTR,self::$table,self::$marqueur,self::$style_caption,8);
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
			<form method='POST' action='".self::$ficherAction."' target='".self::$ficherIframe."'>".(!(isset(self::$CHAMPS['nom'][$T_SGO[3]])and self::$CHAMPS['nom'][$T_SGO[3]])?"":"<input type='text' name='us_nom' title=\"Utilisateur\" placeholder=\"Utilisateur\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['password'][$T_SGO[3]])and self::$CHAMPS['password'][$T_SGO[3]])?"":"<input type='text' name='us_password' title=\"Mot De passe\" placeholder=\"Mot De passe\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['login'][$T_SGO[3]])and self::$CHAMPS['login'][$T_SGO[3]])?"":"<input type='text' name='us_login' title=\"Login\" placeholder=\"Login\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['role'][$T_SGO[3]])and self::$CHAMPS['role'][$T_SGO[3]])?"":"<input type='text' name='us_role' title=\"Rôle\" placeholder=\"Rôle\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['titre'][$T_SGO[3]])and self::$CHAMPS['titre'][$T_SGO[3]])?"":"<input type='text' name='us_titre' title=\"Titre\" placeholder=\"Titre\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['genre_user'][$T_SGO[3]])and self::$CHAMPS['genre_user'][$T_SGO[3]])?"":"<input type='text' name='us_genre_user' title=\"Genre\" placeholder=\"Genre\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['valide'][$T_SGO[3]])and self::$CHAMPS['valide'][$T_SGO[3]])?"":"<input type='text' name='us_valide' title=\"Valide ?\" placeholder=\"Valide ?\" class='".self::$input_class."' />").
				(!(isset(self::$CHAMPS['str_img'][$T_SGO[3]])and self::$CHAMPS['str_img'][$T_SGO[3]])?"":"<input type='text' name='us_str_img' title=\"Image\" placeholder=\"Image\" class='".self::$input_class."' />")
				.self::$marqueur." <input type='submit' name='bt_rapport_".self::$table."' value='Recherche'  class='".self::$bt_class." btn-primary'/>
			</form>
				";
		}
		public static function bt_rapport_config($class,$val){
			echo "
			<form method='POST' id='' action='".self::$ficherAction."' target='".self::$ficherIframe."' style='display:inline-block' >
				<input type='submit' class='$class' id='bt_rapport_config_".self::$table."' name='bt_rapport_config_".self::$table."' value='".($val!=''?$val:'Rapport Config User')."' onClick=\"".self::codeOnCallConfig()."\"/>
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
	