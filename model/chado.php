<?php
	// require_once("../../Dgrad_SNP_2016/model/connexion.php");
	class Chado{
		public static $upload		= 'uploads';	// répertoire principal dans lequel stocker tous les fichiers uploader
		public static $notification = '';			// variable devant contenir tous les messages de notification renvoyer après traitement
		public static $ficherAction = 'traitement.php';// ficher où renvoi le submit de tous nos formulaires (là où devra se passer les traiment où l'on devra invoquer la méthode traitement de la classe)
		public static $ficherIframe = 'iframe';	// le nom de l'ifraime utilisé dans les target de nos formulaires pour éviter le rechargement de nos page
		
		public static $msgScriptActif= true;		// activer ou pas les notification en passant par la fonction java script
		public static $retourHtmlCRUD= 'retourHtmlCRUD';	// l'id de la zone html dans laquelle sont déposés les forumulaires de création modification et suppression
		public static $retourHtmlLIST= 'retourHtmlLIST';	// l'id de la zone html dans laquelle sont déposés la liste en cas de rechargement de la liste
		public static $retourHtmlRap= 'retourHtmlRap';	// l'id de la zone html dans laquelle est déposé le rapport
		public static $retourHtmlAjaxLIST= 'retourHtmlAjaxLIST';	// l'id de la zone html dans laquelle est déposé le rapport
		public static $style_caption= 'background-color: #829AA8;color: white;';	// le style de tous mes captions
		
		public static $id_zone_rslt_msgScript = 'id_zone_rslt_msgScript';
													// l'id de la zone où sont affichées les differentes notifications lors des traitements
		
		// les message renvoyer dans les notifications
		public static $notifNoForm  = "Aucun formulaire demandé!!!";
		public static $notifRemplir = "Remplir les champs vides SVP!";
		public static $notifNoSelct = "Aucune sélection en cours!";
		public static $notifInsSucc = "Enregistrement effectué avec succès!<br/>";
		public static $notifInsErro = "Echec enregistrement!<br/>";
		public static $notifUpdSucc = "Mise à jour effectuée!<br/>";
		public static $notifUpdErro = "Echec Mise à jour!<br/>";
		public static $notifDelSucc = "Suppression effectuée!<br/>";
		public static $notifDelErro = "Echec suppression!<br/>";
		
		public static $conx = null;
		public function __construct($con){
			self::$conx = $con;
			self::script();
		}
		// création du script devant prendre en charge le transport des message de l'iframe ver la fenetre principale
		public static function script(){
			echo "
			<script type='text/javascript'> 
				function chargerHtml(id,text){
					if(document.getElementById(id))
					document.getElementById(id).innerHTML = text;
					else console.log('Attention: '+id+' est non dispo');
				}
				function combo_auto(id){new autocomplete(document.getElementById(id));}
				function cache1_et_affiche2(id1,id2){
					if(document.getElementById(id1)==undefined)alert('Erreur: '+id1+' est undefined');
					else document.getElementById(id1).style.display='none';//setAttribute('style','display:none');
					if(document.getElementById(id2)==undefined)alert('Erreur: '+id2+' est undefined');
					else document.getElementById(id2).style.display='inline-block';//setAttribute('style','display:inline-block');
				}
				function affiche(id1){
					if(document.getElementById(id1)==undefined)alert('Erreur: '+id1+' est undefined');
					else document.getElementById(id1).style.display='inline-block';//setAttribute('style','display:inline-block');
				}
				function cache(id1){
					if(document.getElementById(id1)==undefined)alert('Erreur: '+id1+' est undefined');
					else document.getElementById(id1).style.display='none';//setAttribute('style','display:none');
				}
				
			</script> ";
		}
		public static function vider($id_window){
			echo "<script>window.top.window.chargerHtml('$id_window','');</script>";
		}
		public static function chargerHtml($id_frame,$id_window){
			echo "<script>var html = document.getElementById('$id_frame').innerHTML;window.top.window.chargerHtml('$id_window',html);</script>";
		}
		public static function autoCompleter($id_combo){
			return "window.top.window.combo_auto('$id_combo');";
		}
		public static function hide_show($hide,$show){
			echo "<script>window.top.window.cache1_et_affiche2('".$hide."','".$show."');</script>";
		}
		
		public static function div_notification(){echo"<div id='".self::$id_zone_rslt_msgScript."' ></div>";}
		
		public static function div_ajax_liste(){ echo"<div id='".self::$retourHtmlAjaxLIST."' ></div>";}
		
		
		public static function rapport_config_champ($TLBL,$T_CH,$T_SGO,$CHAMPS,$retourHtmlRapConfig,$table,$marqueur,$ficherAction,$ficherIframe){
			$l=0;$ch_alias="c";$label="l";$type="t";
			$deux_mois = time() + 3600*24 *60 ;
			// on modifie la $CHAMPS si necessaire
			$CHAMPS = self::ordonner_champs(($tt=self::mod_champs_si_necessaire($TLBL,$T_CH,$T_SGO,$CHAMPS,$table))?$tt:$CHAMPS);
			
			$retour = $retourHtmlRapConfig;
			echo "<div id='$retour' >";
			
			if( isset($_POST["bt_mod_contrainte_rapp_".$table.""])){
				echo"<form method='POST' action='' >
				<textarea name='champs'>".(self::convertir_champs_en_str($CHAMPS,$T_SGO,$table))."</textarea>
				<input type='hidden' name='table' value=\"$table\" >
				<input type='submit' name='bt_fixer_regle_rapport' value=\"Confirmer Règles\">
				</form>
				";
			}
			else echo self::FORM_GROUP_ORDER_BY($CHAMPS,$TLBL,$table,$marqueur,$ficherAction,$ficherIframe);
			echo "</div>";
			self::chargerHtml($retour,$retour);
			return $CHAMPS;
		}
		public static function ordonner_champs($CHAMPS){
			$t=$tt=array();
			foreach($CHAMPS as $c=>$val){
				$t[$c] = isset($val["show"])?$val["show"]:"";
			}
			$t= self::trier_val_assoc_array($t);
			foreach($t as $c=>$val){
				$tt[$c] = $CHAMPS[$c] ;
			}
			return $tt;
		}
		// tri des tableau afin de selectionner grouper et ordonner les champs selon leur priorités
		public static function trier_val_assoc_array($tab){
			$cle = $cles = array_keys($tab);
			for($i=0;$i!=count($cles);$i++){
				for($j=$i+1;$j!=count($cles);$j++){
					if($tab[$cles[$i]]>$tab[$cles[$j]]){
						$t= $cles[$i];
						$cles[$i]=$cles[$j];
						$cles[$j]=$t;
					}
				}
			}
			$t= array();foreach($cles as $cl)$t[$cl] = $tab[$cl];
			return $t;
		}
		// on modifie la $CHAMPS si necessaire
		public static function mod_champs_si_necessaire($TLBL,$T_CH,$T_SGO,$CHAMPS,$table){
			$ch_alias = "c";$deux_mois = time() + 3600*24 *60 ;
			if(isset($_POST["bt_mod_contrainte_rapp_".$table.""])){
				if(isset($_COOKIE[$table]))
					$CHAMPS = self::recupere_contrainte_champs($_COOKIE[$table],$T_CH,$T_SGO);
				$TabCh = $_POST;$l=0;$str= array();
				foreach($CHAMPS as $ch=>$tab){
					$t = explode(' ',$TLBL[$ch][$ch_alias]);
					$name = (count($t)==2?$t[1]:$ch);
					$tab[$T_SGO[0]]=$TabCh["$T_SGO[0]_".$name];
					$tab[$T_SGO[1]]=$TabCh["$T_SGO[1]_".$name];
					$tab[$T_SGO[2]]=$TabCh["$T_SGO[2]_".$name];
					$tab[$T_SGO[3]]=$TabCh["$T_SGO[3]_".$name];
					$CHAMPS[$ch] = $tab;
					$str[] = $TabCh["$T_SGO[0]_".$name]."-".$TabCh["$T_SGO[1]_".$name]."-".$TabCh["$T_SGO[2]_".$name]."-".$TabCh["$T_SGO[3]_".$name]."";
				}
				$str_champ = implode(",",$str);
				setcookie($table,$str_champ,$deux_mois);
				return $CHAMPS;
			}
		}
		
		public static function FORM_GROUP_ORDER_BY($CHAMPS,$TLBL,$table,$marqueur,$ficherAction,$ficherIframe){
			$input = "<tr><th>N°</th><th>Champs</th><th>Voir</th><th>Order</th><th>Group</th></tr>";
			$i=1 ;$ch_alias="c";$label="l";
			foreach($CHAMPS as $ch=>$tab){
				$t = explode(' ',$TLBL[$ch][$ch_alias]);
				$name = (count($t)==2?$t[1]:$ch);
				$input .= "<tr><td>".($i++)."</td><td>".$TLBL[$ch][$label]."</td>
				<td><input name='show_$name' value='".(isset($tab['show'])?$tab['show']:'')."'  style='width:50px' ></td>
				<td><input name='order_$name' value='".(isset($tab['order'])?$tab['order']:'')."' style='width:50px' ></td>
				<td><input name='group_$name' value='".(isset($tab['group'])?$tab['group']:'')."'  style='width:50px' ></td>
				<td><input name='search_$name' value='".(isset($tab['search'])?$tab['search']:'')."'  style='width:50px' ></td></tr>";
			}
			return "<form  method='POST' id='' action='".$ficherAction."' target='".$ficherIframe."' >".$marqueur."<table>$input<tr><td></td><td colspan='3' style='text-align:center;'>
			<input type='submit' name='bt_mod_contrainte_rapp_".$table."' value='Retenir' ><input type='reset' name='' value='Revenir' ></td></tr></table></form>";
		}
		public static function champ_rech($typ_ch,$input_ch,$table_ch){
			if(!isset($_POST[$input_ch]))return "";
			if($typ_ch=="select")return ($t=$_POST[$input_ch])!=''?" AND $table_ch = '$t'":"";
			else if($typ_ch=="date")return ((($count=(count($t=explode(':',($trim = trim($_POST[$input_ch]))))))==1 and $trim!='') or $count==2)?" AND $table_ch BETWEEN '$t[0]' AND '".$t[count($t)-1]."'":"" ;
			else return trim($t=$_POST[$input_ch])!=''?(" AND $table_ch LIKE '%$t%'"):"";
		}
		public static function convertir_champs_en_str($CHAMPS,$T_SGO,$table){
			$tabl = array();$ch_alias = "c";$deux_mois = time() + 3600*24 *60 ;
			foreach($CHAMPS as $ch=>$tab){
				$str = 		isset($tab[$T_SGO[0]])?$tab[$T_SGO[0]]:"";
				$str .= "-".(isset($tab[$T_SGO[1]])?$tab[$T_SGO[1]]:"");
				$str .= "-".(isset($tab[$T_SGO[2]])?$tab[$T_SGO[2]]:"");
				$str .= "-".(isset($tab[$T_SGO[3]])?$tab[$T_SGO[3]]:"");
				$str .= (isset($tab[$T_SGO[4]])?("-".$tab[$T_SGO[4]]):"");
				$tabl[] = $str;
			}
			$str_champ = implode(",",$tabl);
			setcookie($table,$str_champ,$deux_mois);
			return $str_champ;
		}
		public static function recupere_contrainte_champs($str,$T_CH,$T_SGO){
			$CHAMPS = array();
			$tab = explode(",",$str);
			for($i=0;$i!=count($tab);$i++){
				$cont = $tab[$i];
				$t = explode("-",$cont);
				$CHAMPS[$T_CH[$i]]=array(
				$T_SGO[0]=>isset($t[0])?$t[0]:'',
				$T_SGO[1]=>isset($t[1])?$t[1]:'',
				$T_SGO[2]=>isset($t[2])?$t[2]:'',
				$T_SGO[3]=>isset($t[3])?$t[3]:'');
				if(isset($t[4]))
					$CHAMPS[$T_CH[$i]][$T_SGO[4]]=$t[4];
			}return $CHAMPS;
		}
		
		// création des chaine de la requete (select (show),group et order)
		public static function creer_ch_select_group_order($TLBL,$T_CH,$T_SGO,$CHAMPS,$table){
			if(isset($_COOKIE[$table]))
				$CHAMPS = self::recupere_contrainte_champs($_COOKIE[$table],$T_CH,$T_SGO);
			$ch_alias="c";$type="t";$label="l";
			$SHOW_CH = $GROUP_BY = $ORDER_BY = $SEARCH_CH = $SUM_CH = array();
			foreach($CHAMPS as $ch=>$tab){
				$t = explode(" ",trim($TLBL[$ch][$ch_alias]));// "t.champ alias"
				if(isset($tab["$T_SGO[0]"]) and $tab["$T_SGO[0]"]){
					$SHOW_CH[$TLBL[$ch][$ch_alias]]=$tab["$T_SGO[0]"];
				}
				if(isset($tab["$T_SGO[1]"]) and $tab["$T_SGO[1]"]){$GROUP_BY[$t[0]]=$tab["$T_SGO[1]"];}
				if(isset($tab["$T_SGO[2]"]) and $tab["$T_SGO[2]"]){$ORDER_BY[$t[0]]=$tab["$T_SGO[2]"];}
				if(isset($tab["$T_SGO[4]"]) and $tab["$T_SGO[4]"]){$SUM_CH[$t[0]]=$tab["$T_SGO[4]"];} //un champ pour lequel on peut faire une somme
				if(isset($tab["$T_SGO[3]"]) and $tab["$T_SGO[3]"]){
					$ch_ = explode(" ",$TLBL[$ch][$ch_alias])[0];
					$t = explode(' ',$TLBL[$ch][$ch_alias]);
					$name = (count($t)==2?$t[1]:$ch);
					$SEARCH_CH[] = self::champ_rech((isset($TLBL[$ch][$type])?$TLBL[$ch][$type]:"text"),(str_replace(".","_",$ch_)),$ch_);
				}
			}
			$SHOW_CH 	= self::trier_val_assoc_array($SHOW_CH);
			$GROUP_BY 	= self::trier_val_assoc_array($GROUP_BY);
			$ORDER_BY 	= self::trier_val_assoc_array($ORDER_BY);
			// 
			$Ch_Search = (count($SEARCH_CH))? implode(" ",$SEARCH_CH):"";
			$Ch_Group  = (count($GROUP_BY))?" GROUP BY ".implode(",",array_keys($GROUP_BY)):"";
			$Ch_Order  = (count($ORDER_BY))?" ORDER BY ".implode(",",array_keys($ORDER_BY)):"";
			$Ch_Select = (count($SHOW_CH))?" ,".implode(",",array_keys($SHOW_CH)):"";
			
			$Str_SUM_INIT = $Str_SUM_INCR = $Str_SUM_AFFI = '';
			// préparation du titre du tableau ($Tab_Caption)
			// les cellule d'entete ($Tab_Tr_Th) et ceux à afficher ($Tab_Tr_Td)
			$Tab_And = array();$Tab_Caption = $GROUP_BY;$Tab_Tr_Th = $Tab_Tr_Td = $SHOW_CH;
			foreach($CHAMPS as $ch=>$tab){
				$alias = $TLBL[$ch][$ch_alias];// "t.champ alias"	
				$t = explode(' ',$alias);	
				if(isset($GROUP_BY[$t[0]])){
					$Tab_Caption[$t[0]] = "<span>".$TLBL[$ch][$label]." </span> <strong>\$row[$ch]</strong><br/>";
					$Tab_And[] = "\$row['$ch']==\$ex['$ch']";
				}// si ce champ est à afficher 
				else if(isset($SHOW_CH[$t[0]])){
					$Tab_Tr_Th[$t[0]] = "<th>".$TLBL[$ch][$label]."</th>";
					$Tab_Tr_Td[$t[0]] = "<td>\$row[$ch]</td>";
				}else if(isset($SHOW_CH[$alias])){
					$Tab_Tr_Th[$alias] = "<th>".$TLBL[$ch][$label]."</th>";
					$Tab_Tr_Td[$alias] = "<td>\$row[$ch]</td>";
				}
				if(isset($SUM_CH[$t[0]])){
					$Str_SUM_INIT .= "\$_sum_".$ch."=0;";
					$Str_SUM_INCR .= "\$_sum_".$ch."+=\$row[\"$ch\"];";
					$Str_SUM_AFFI .= "<span class='lbl_total'>".$TLBL[$ch][$label]." Total: </span> <strong class='val_mnt_total'>\$_sum_$ch</strong><br/>";
				}
			}
			//
			$Str_Caption = $Str_Tr_Th = $Str_Tr_Td = "";
			foreach($Tab_Caption as $ch=>$val)$Str_Caption .= $val;
			foreach($Tab_Tr_Th as $ch=>$val){
				if(strlen($val)>4){
					$Str_Tr_Th .= $val;
					$Str_Tr_Td .= $Tab_Tr_Td[$ch];
				}
			}
			// la chaine potentielle à stocker chez le client
			$CONSTR= array(
				'Ch_Select' => $Ch_Select,
				'Ch_Group' => $Ch_Group,
				'Ch_Order' => $Ch_Order,
				'Ch_Search' => $Ch_Search,
				'Tab_Caption' => $Str_Caption,
				'Tab_Tr_Th' => $Str_Tr_Th,
				'Str_SUM_INIT' => $Str_SUM_INIT,
				'Str_SUM_INCR' => $Str_SUM_INCR,
				'Str_SUM_AFFI' => $Str_SUM_AFFI,
				'Tab_And' => implode(" and ",$Tab_And),
				'Tab_Tr_Td' => $Str_Tr_Td
			);
			return $CONSTR;
		}
		
		public static function suite_rapport($req,$CONSTR,$table,$marqueur,$style_caption,$nbr_col=40){
			$Filtre = " WHERE 1=1 ";
			if(isset($_POST["bt_rapport_".$table])){
			$Filtre .= " AND (1=1 "; eval("\$Filtre .= \"$CONSTR[Ch_Search]\";");  $Filtre .= ")";
			}
			$req .= "$Filtre  $CONSTR[Ch_Order]" ;//$CONSTR[Ch_Group]
			if($pdo_result = self::$conx->query($req)){
				$input = $marqueur;
				echo "
				<div id='".self::$retourHtmlRap."' style='' >";
				$continuer = $row = $pdo_result->fetch(PDO::FETCH_ASSOC);
				while($continuer){
					
					eval("$CONSTR[Str_SUM_INIT]");
					echo "
					<table>
						<caption style='".$style_caption."' >";eval("echo\"$CONSTR[Tab_Caption]\";");echo"</caption>
						<tr><th>N°</th>$CONSTR[Tab_Tr_Th]</tr>";
						$i=1;
						
						do{
							echo "<tr><td>".($i++)."</td>";eval("echo\"$CONSTR[Tab_Tr_Td]\";");echo"</tr>";
							eval("$CONSTR[Str_SUM_INCR]");
							$ex = $row;
						}while($row	= $pdo_result->fetch(PDO::FETCH_ASSOC) and eval(" return($CONSTR[Tab_And])?true:false;"));
					echo "<tr style='text-align:left'><td></td><td colspan='$nbr_col' >";eval("echo \"$CONSTR[Str_SUM_AFFI]\";");echo "</td></tr>
					</table>";
					if(!$row)break;
				}
				echo "</div>";
			}else echo self::$conx->errorInfo()[2];
		}
		
	}
	