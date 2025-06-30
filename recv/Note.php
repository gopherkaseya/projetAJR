<?php
	/* le bap suit le cours d'évolution mais sans 40% */
	session_start();
	include("../model/connexion.php");
	$conx = Connexion::GetConnexion();
	try
	{
		$Select = "SELECT id ,id_assujetti,id_acte,id_secteur,num_bap,montant_bap,note_to,num_note,date_ordo,date_depot,montant,frequence,apurer,date_recouvrement,banque,relancer,date_relance,enroler,note_date_role,invalide,date_enregistrement,id_secteur_detenteur,type_invalidation,explication_invalidation,date_invalidation,nom_assujetti,adresse_assujetti FROM t_note ";
		if($_GET["action"] == "tableauRelance")
		{
			$var = "";
			$Liste_id = isset($_GET["id"])?$_GET["id"]:"0";
			$req = "SELECT n.id,id_assujetti,id_secteur,na.id_acte,date_relance,banque, n.num_note,
			date_ordo,date_depot,na.frequence,SUM(na.montant_acte) montant,note_date_role,num_bap, montant_bap, 
			note_to, n.nom_assujetti,n.adresse_assujetti, 
			n.nom_assujetti,ac.nom, ac.art_bud, ac.penalite ,ac.coefficient , s.service
			,(DATEDIFF(NOW(),date_depot)-8) AS nbr_jr_retard,
			DATEDIFF(now(),MAKEDATE( EXTRACT(DAY FROM date_enregistrement ),DAYOFYEAR(ac.date_role))) nbr_j_pen_a,
			EXTRACT(DAY FROM ac.date_role) db_j,EXTRACT(MONTH FROM ac.date_role) db_m,EXTRACT(YEAR FROM date_enregistrement) db_y
			
			FROM t_note n inner join 
			t_note_actes na on n.num_note = na.num_note inner join 
			t_acte ac on na.id_acte = ac.id inner join 
			t_service_gen s on s.id = ac.acte_id_secteur 
			where (ADDDATE(date_depot,8)) <= NOW() and 
			( n.id = '$Liste_id' ) ";
			
			// $Mnt_Princ = 0;
			$T_mt = 0;$T_pa=0;$T_pr=0;$T_tot_p=0;$T_p_60=0;$T_p_40=0;$T_tot_en=0;
			$lign_td = ''; $i=0; $TOTAL = 0;
			$ligne = "";$i=0;
			$invi = "style='color:transparent'";
				
			$TabRelance = array();
			$pdo_result = $conx->query($req);//echo "$req";
			while($ligne = $pdo_result->fetch())
			{
			// echo "<pre>";print_r($ligne);echo "</pre><br><br>";
				$nbrMoi_a = ceil($ligne["nbr_j_pen_a"]/30);
				$n = ($ligne["nbr_jr_retard"]?$ligne["nbr_jr_retard"]:1);
				$nbrMoi_r = ceil($n/30)+1;
			
				{	$mt_pri = $ligne["montant"];
					$mt_bap = $ligne["montant_bap"];
					$bap_existe = ($mt_bap == "" or $mt_bap == "0")?false:true;
					$ligne["coefficient"] = str_replace(",",".",$_GET["coeff"]);
							
					
					$coeff = "$ligne[coefficient]";
					if(($ligne["db_y"] <= date("Y"))and ($ligne["db_m"] <= date("m"))and ($ligne["db_j"] <= date("d")))
					{
						$p_acciette = $bap_existe?0:($mt_pri*$coeff);
						$p_acciette_bap = $bap_existe?0:($mt_bap*$coeff);
					}
					else {
						$p_acciette = 0;
						$p_acciette_bap = 0;
					}
					
					$p_recou = $nbrMoi_r*(($mt_pri*4)/100);
					$p_recou_bap = $nbrMoi_r*(($mt_bap*4)/100);
							
					$tot_p = $p_acciette+$p_recou;
					$p_60 = ((60*$tot_p)/100) + $mt_pri;
					$p_40 = (40*($tot_p)/100);
					$tot_en = $p_40+$p_60;
					
					$tot_p_bap = $p_acciette_bap + $p_recou_bap;
					$p_60_bap = 0;//$bap_existe?((60*($tot_p_bap)/100)+$mt_bap):0;
					// $p_60_bap = ((60*($tot_p_bap)/100)+$mt_bap);
					$p_40_bap = $tot_p_bap + $mt_bap;//(40*($tot_p_bap)/100);
					$tot_en_bap = $p_40_bap+$p_60_bap;
				}	
			
				{$T_mt += $mt_pri+$mt_bap;
				$T_pa += $p_acciette+$p_acciette_bap;
				$T_pr += $p_recou+$p_recou_bap;
				$T_tot_p += $tot_p+$tot_p_bap;
				$T_p_60 += $p_60+$p_60_bap;
				$T_p_40 += $p_40+$p_40_bap;
				$T_tot_en += ($tot_en+$tot_en_bap);
				}
				
				{$i++;
				$id = $ligne["id"];
				$date_art = $ligne["note_date_role"]=="0000-00-00"?date("Y-m-d"):$ligne["note_date_role"];
				$apuer = "Ext.";
				$name = "$ligne[id]_$ligne[num_note]";
				$assuj = "$ligne[nom_assujetti]";
				$adrss = "$ligne[adresse_assujetti]";
				}
				
				$fc = "Fc";
				{$TabRel = array();
				$TabRel["assujetti"] = "$ligne[nom_assujetti]";
				$TabRel["service"] = "$ligne[service]";
				$TabRel["service"] = "$ligne[service]";
				$TabRel["date_depot"] = "$ligne[date_depot]";
				$t = explode("-","$ligne[date_ordo]");
				$date = count($t)==3?"$t[2]/$t[1]/$t[0]":"00/00/0000";
				$TabRel["date_ordo"] = $date;
				
				$TabRel["acte"] = "$ligne[nom]";
				$TabRel["num_note"] = sprintf("%07d", "$ligne[num_note]");
				$TabRel["BapOUnon"] = "Princ";
				$TabRel["mt_pri"] = chiffre($mt_pri)."$fc";
				$TabRel["p_acciette"] = chiffre($p_acciette)."$fc";
				$TabRel["p_recou"] = chiffre($p_recou)."$fc";
				$TabRel["tot_p"] = chiffre($tot_p)."$fc";
				$TabRel["p_60"] = chiffre($p_60)."$fc";
				$TabRel["p_40"] = chiffre($p_40)."$fc";
				$TabRel["nbr_j_pen_a"] = "$ligne[nbr_j_pen_a]";
				$TabRel["tot_en"] = chiffre($tot_en)."$fc";
				$TabRelance[] = $TabRel;
				}
				// print_r($TabRel);
				// if(!$bap_existe){
					$TabRel = array();
					$TabRel["BapOUnon"] = "BAP";
					$TabRel["num_note"] = "BAP";
					$TabRel["acte"] = "A.T";
					$TabRel["mt_pri"] = chiffre($mt_bap)."$fc";
					$TabRel["p_acciette"] = chiffre($p_acciette_bap)."$fc";
					$TabRel["p_recou"] = chiffre($p_recou_bap)."$fc";
					$TabRel["tot_p"] = chiffre($tot_p_bap)."$fc";
					$TabRel["p_60"] = chiffre($p_60_bap)."$fc";
					$TabRel["p_40"] = chiffre($p_40_bap)."$fc";
					$TabRel["tot_en"] = chiffre($tot_en_bap)."$fc";
					$TabRelance[] = $TabRel;
				// }
				// print_r($TabRel);
				$ds = "<b>";$fs = "</b>";
				$TabRel = array();
				$TabRel["assujetti"] = $ds."TOTAUX$fs";
				$TabRel["mt_pri"] = $ds.chiffre($T_mt)."$fs$fc";
				$TabRel["p_acciette"] = $ds.chiffre($T_pa)."$fs$fc";
				$TabRel["p_recou"] = $ds.chiffre($T_pr)."$fs$fc";
				$TabRel["tot_p"] = $ds.chiffre($T_tot_p)."$fs$fc";
				$TabRel["p_60"] = $ds.chiffre($T_p_60)."$fs$fc";
				$TabRel["p_40"] = $ds.chiffre($T_p_40)."$fs$fc";
				$TabRel["tot_en"] = $ds.chiffre($T_tot_en)."$fs$fc";
				$TabRelance[] = $TabRel;
			}	// print_r($TabRel);
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			// $jTableResult['Message'] 	= "$req";
			$jTableResult['Records'] 	= $TabRelance;
			print json_encode($jTableResult); 
			
			$req = "update t_note set relancer = relancer+1, date_relance = '".(date("Y-m-d"))."' where id = '$Liste_id' ;";
			$conx->exec($req);
			
		}
		else if($_GET["action"] == "listRole")
		{
			
			$var = "";
			// require_once("fonction.php");
			// $TabRelance = tr_note_a_enroler($var,"$_GET[id]");
			$Liste_id = isset($_GET["id"])?$_GET["id"]:"0";
			
			{$from = "
			FROM t_note n inner join 
			t_note_actes na on n.num_note = na.num_note inner join 
			t_acte ac on na.id_acte = ac.id inner join 
			t_service_gen s on s.id = ac.acte_id_secteur
			where invalide = 1 and apurer = 0 and
			(ADDDATE(date_depot,8)) <= NOW()  group by na.num_note ";
			
			$Select = "SELECT n.id,id_assujetti,id_secteur,na.id_acte,date_relance,banque,n.num_note,
			date_ordo,date_depot,na.frequence,SUM(na.montant_acte) montant,note_to,note_date_role,num_bap,
			montant_bap,
			-- (n.nom_assujetti,' / ',n.adresse_assujetti)assujetti,
			n.nom_assujetti,n.adresse_assujetti,
			ac.nom, ac.art_bud, ac.penalite,ac.coefficient ,
			s.service ,(DATEDIFF(NOW(),date_depot)-8) AS nbr_jr_retard,
			DATEDIFF(now(),MAKEDATE( EXTRACT(DAY FROM date_enregistrement ),DAYOFYEAR(ac.date_role))) nbr_j_pen_a,
			EXTRACT(DAY FROM ac.date_role) db_j,EXTRACT(MONTH FROM ac.date_role) db_m,EXTRACT(YEAR FROM date_enregistrement) db_y
			 $from ";
			 // $from = " FROM t_note n inner join 
	            // t_note_actes na on  n.num_note = na.num_note inner join 
				// t_acte ac on na.id_acte = ac.id inner join 
				// t_service_gen s on s.id = ac.acte_id_secteur
				// where invalide = 1 and apurer = 0 and enroler = 0 and
				// (ADDDATE(date_depot,8)) <= NOW() 
				// group by n.id
				// ";
			// $Select = "SELECT n.id,id_assujetti,id_secteur,na.id_acte,date_relance,banque,
				// n.num_note,date_ordo,date_depot,na.frequence,SUM(na.montant_acte) montant,num_bap,
				// montant_bap, note_to,
				// (n.nom_assujetti,' / ',n.adresse_assujetti)assujetti,n.nom_assujetti,n.adresse_assujetti,
				// concat(n.nom_assujetti,' -- ',n.adresse_assujetti) assujetti,
				// ac.nom, ac.art_bud, ac.penalite ,ac.coefficient ,
				// s.service $from ";
			 
			$req = "SELECT COUNT(*) AS RecordCount $from";
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$recordCount = count($row);
			
			$rows = array();
			$req = $Select/* ." ORDER BY " . $_GET["jtSorting"] */ . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			// $req = "SELECT id,nom_assujetti,adresse_assujetti FROM t_note where id_secteur_detenteur = 3 group by nom_assujetti,adresse_assujetti order by nom_assujetti";
			
			$num = $_GET["jtStartIndex"]+1;
			
			}
			
			// $Mnt_Princ = 0;
			$T_mt = 0;$T_pa=0;$T_pr=0;$T_tot_p=0;$T_p_60=0;$T_p_40=0;$T_tot_en=0;
			$lign_td = ''; $i=0; $TOTAL = 0;$coeff=1;
			$ligne = "";$i=0;
			$invi = "style='color:transparent'";
				
			$TabRelance = $rows = array();
			$pdo_result = $conx->query($req);//echo "$req";
			while($ligne = $pdo_result->fetch())
			{
				$ligne["num"] = $num++;
				$nbrMoi_a = ceil($ligne["nbr_j_pen_a"]/30);
				$nbrMoi_r = ceil($ligne["nbr_jr_retard"]/30);
				$ligne["nbrMoi_r"] = $nbrMoi_r;
				{	$mt_pri = $ligne["montant"];
					$mt_bap = (int)$ligne["montant_bap"];
					$bap_existe = ($mt_bap == "" or $mt_bap == "0" or $mt_bap == 0)?false:true;
					$ligne["coefficient"] = $coeff;
					$ligne["assujetti"] = "$ligne[nom_assujetti] / $ligne[nom_assujetti]";
							
					
					$coeff = "$ligne[coefficient]";
					if(($ligne["db_y"] <= date("Y"))and ($ligne["db_m"] <= date("m"))and ($ligne["db_j"] <= date("d")))
					{
						$p_acciette = $bap_existe?0:($mt_pri*$coeff);
						$p_acciette_bap = $bap_existe?0:($mt_bap*$coeff);
					}
					else {
						$p_acciette = 0;
						$p_acciette_bap = 0;
					}
					
					$p_recou = $nbrMoi_r*(($mt_pri*4)/100);
					$p_recou_bap = $nbrMoi_r*(($mt_bap*4)/100);
							
					$tot_p = $p_acciette+$p_recou;
					$p_60 = ((60*($tot_p)/100)+$mt_pri);
					$p_40 = (40*($tot_p)/100);
					$tot_en = $p_40+$p_60;
					
					$tot_p_bap = $p_acciette_bap+$p_recou_bap;
					$p_60_bap = $bap_existe?0:((60*($tot_p_bap)/100)+$mt_bap);
					// $p_60_bap = ((60*($tot_p_bap)/100)+$mt_bap);
					$p_40_bap = (40*($tot_p_bap)/100);
					$tot_en_bap = $p_40_bap+$p_60_bap;
				}	
			
				{$T_mt += $mt_pri+$mt_bap;
				$T_pa += $p_acciette+$p_acciette_bap;
				$T_pr += $p_recou+$p_recou_bap;
				$T_tot_p += $tot_p;
				$T_p_60 += $p_60;
				$T_p_40 += $p_40;
				$T_tot_en += ($tot_en+$tot_en_bap);
				}
				
				$i++;
				$id = $ligne["id"];
				$date_art = $ligne["note_date_role"]=="0000-00-00"?date("Y-m-d"):$ligne["note_date_role"];
				
				
				$fc = "";
				{
				$t = explode("-","$ligne[date_ordo]");
				$ligne["date_ordo"] = "$t[2]/$t[1]/$t[0]";
				
				
				$ligne["mt_pri"] = chiffre($mt_pri)."$fc";
				$ligne["p_acciette"] = chiffre($p_acciette)."$fc";
				$ligne["p_recou"] = chiffre($p_recou)."$fc";
				$ligne["tot_p"] = chiffre($tot_p)."$fc";
				$ligne["p_60"] = chiffre($p_60)."$fc";
				$ligne["p_40"] = chiffre($p_40)."$fc";
				$ligne["tot_en"] = chiffre($tot_en)."$fc";
				
				$rows[] = $ligne;
				
				if($bap_existe){
					$ligne["num_note"] = "BAP";
					$ligne["num"] = "";
					$ligne["assujetti"] = "";
					$ligne["service"] = "";
					$ligne["nom"] = "";
					$ligne["mt_pri"] = chiffre($mt_bap)."$fc";
					$ligne["p_acciette"] = chiffre($p_acciette_bap)."$fc";
					$ligne["p_recou"] = chiffre($p_recou_bap)."$fc";
					$ligne["tot_p"] = chiffre($tot_p_bap)."$fc";
					$ligne["p_60"] = chiffre($p_60_bap)."$fc";
					$ligne["p_40"] = chiffre($p_40_bap)."$fc";
					$ligne["tot_en"] = chiffre($tot_en_bap)."$fc";
					$rows[] = $ligne;
				}
				
				
				
				}
			}
			
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			// $jTableResult['Result'] 	= "OKz";
			// $jTableResult['Message'] 	= "$req";
			$jTableResult['Records'] 	= $rows;
			$jTableResult['TotalRecordCount'] = $recordCount;
			print json_encode($jTableResult); 
		}
		else if($_GET["action"] == "updateRole")
		{
			$tab = array();
			$tab["num"] = $_POST["num"];
			$tab["assujetti"] = $_POST["assujetti"];
			$tab["num_note"] = $_POST["num_note"];
			$tab["date_ordo"] = $_POST["date_ordo"];
			$tab["service"] = $_POST["service"];
			$tab["nom"] = $_POST["nom"];
			$tab["mt_pri"] = $_POST["mt_pri"];
			$pa = (double)(str_replace( ",", ".",str_replace( ".", "",$_POST["p_acciette"])));
			$tab["p_acciette"] = chiffre($pa*$_POST["coef"]);
			$tab["p_recou"] = $_POST["p_recou"];
			$tab["tot_p"] = $_POST["tot_p"];
			$tab["p_60"] = $_POST["p_60"];
			$tab["p_40"] = $_POST["p_40"];
			$tab["tot_en"] = $_POST["tot_en"];
			$tab["nbrMoi_r"] = $_POST["nbrMoi_r"];
			$tab["coef"] = $_POST["coef"];
			
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = $tab;
			print json_encode($jTableResult);
		}
		else if($_GET["action"] == "Repertoire")
		{
			$whereId_secteur = isset($_GET["id"])?" and id_secteur_detenteur = $_GET[id]":"";
			$from = " FROM t_note
				WHERE invalide = 1 $whereId_secteur
				GROUP BY nom_assujetti,adresse_assujetti 
				";
			/*  */
			$req = "SELECT COUNT(*) AS RecordCount $from";
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$recordCount = count($row);
			//Get records from database
			$Select = "SELECT id,num_note,nom_assujetti,adresse_assujetti,date_ordo,apurer $from ";
			
			$req = $Select." ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			// $req = "SELECT id,nom_assujetti,adresse_assujetti FROM t_note where id_secteur_detenteur = 3 group by nom_assujetti,adresse_assujetti order by nom_assujetti";
			$pdo_result = $conx->query($req);
			$rows		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		else if($_GET["action"] == "JournalOrdo")
		{
			$whereDateOrd = (isset($_POST["dateOrd1"])and isset($_POST["dateOrd2"])and$_POST["dateOrd1"]!=""and$_POST["dateOrd2"]!="")?
			" and  date_ordo between '$_POST[dateOrd1]' and '$_POST[dateOrd2]'":"";
			$secteur = (isset($_POST["secteur"])and $_POST["secteur"]!="")?" and s.service LIKE ".$conx->quote("%$_POST[secteur]%"):"";
			$antenne = (isset($_POST["antenne"])and $_POST["antenne"]!="")?" and s1.service LIKE ".$conx->quote("%$_POST[antenne]%"):"";
			$Filtre = (isset($_POST["Filtre"])and $_POST["Filtre"]!="")?" and n.num_note LIKE ".$conx->quote("%$_POST[Filtre]%"):"";
			$assujetti = (isset($_POST["assujetti"])and $_POST["assujetti"]!="")?" and concat(n.nom_assujetti,' ',n.adresse_assujetti) LIKE ".$conx->quote("%$_POST[assujetti]%"):"";
	
			$from = " FROM t_note n inner join 
					t_note_actes na on  n.num_note = na.num_note inner join 
					t_acte ac on na.id_acte = ac.id inner join 
					t_service_gen s on s.id = ac.acte_id_secteur inner join
					t_service_gen s1 on s1.id = n.id_secteur_detenteur
					where invalide = 1 $whereDateOrd $secteur $antenne $assujetti $Filtre
					GROUP BY na.num_note ";
					
			/* ,SUM(montant_acte) as montant,SUM(montant_bap) as montant_bap */
			$req = "SELECT COUNT(*) AS RecordCount $from";
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$recordCount = count($row);
			// $recordCount = 0;
			
			//Get records from database
			$Select = "SELECT n.id,id_assujetti,id_secteur,na.id_acte,n.num_note,date_ordo,
			date_depot,SUM(na.frequence)frequence,SUM(montant_acte) as montant,note_to,num_bap,montant_bap,
			n.nom_assujetti,n.adresse_assujetti,ac.nom, ac.art_bud, ac.penalite ,
			s.service,s1.service Antenne,s1.type_serv_gen $from ";
			$rows = array();
			$req = $Select." ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			// $req = "SELECT id,nom_assujetti,adresse_assujetti FROM t_note where id_secteur_detenteur = 3 group by nom_assujetti,adresse_assujetti order by nom_assujetti";
			$pdo_result = $conx->query($req);
			$i=$_GET["jtStartIndex"]+1;
			$mntTotal = $mntBapTotal = 0;
			while($row	= $pdo_result->fetch())
			{
				$row["num"] = $i++;///chiffre($row["montant"]);
				$mntTotal += $row["montant"];
				$mntBapTotal += $row["montant_bap"];
				$row["montant"] = chiffre($row["montant"]);
				$row["montant_bap"] = chiffre($row["montant_bap"]);
				$rows[]		= $row;
			}
			$rows[] = array("adresse_assujetti"=>"Somme <b style='color:red'>Partielle</b>","montant"=>"<b style='color:red'>".chiffre($mntTotal)."</b>","montant_bap"=>"<b style='color:red'>".chiffre($mntBapTotal)."</b>");//$row;
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			// $jTableResult['Result'] 	= "OKz";
			// $jTableResult['Message'] 	= "$req";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		else if($_GET["action"] == "listNoteRepertoire")
		{
			$Filtre = "";
			if(isset($_POST["Filtre"]) and $_POST["Filtre"]!="") {
				$w = $_POST["Filtre"];
				$Filtre = " and ( concat(num_note,' ',nom_assujetti,' ',adresse_assujetti,' ',date_ordo,' ',service) LIKE '%$w%' ) ";
			}
			//$whereId_secteur
			// $whereId_secteur = isset($_GET["id"])?" and id_secteur_detenteur = $_GET[id]":"";
			$from = " FROM t_note n inner join t_service_gen s 
			-- on n.id_secteur_detenteur=s.id
			on n.id_secteur=s.id
					WHERE invalide = 1 $Filtre
					";
				
			$req = "SELECT COUNT(*) AS RecordCount $from";
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetch();
			$recordCount= $row["RecordCount"];
			// $row		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			// $recordCount = count($row);
			//Get records from database
			$Select = "SELECT n.id,n.num_note,n.nom_assujetti,n.adresse_assujetti,n.date_ordo,n.apurer,service $from ";
			
			$req = $Select." ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			// $req = "SELECT id,nom_assujetti,adresse_assujetti FROM t_note where id_secteur_detenteur = 3 group by nom_assujetti,adresse_assujetti order by nom_assujetti";
			$pdo_result = $conx->query($req);
			$rows		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		else if($_GET["action"] == "listRelance")
		{
			$t_noteMontant = $whereFiltre = $whereDate_ordo = "";
			// $jTableResult = array();
			// $jTableResult['Result'] = "ERROR";
			// $jTableResult['Message'] = "choad";
			// print json_encode($jTableResult);
			
			if(isset($_POST["Filtre"])and$_POST["Filtre"]!=""){
				$w = $_POST["Filtre"];
				$whereFiltre = " and ( n.num_note like '%$w%' or 
				n.adresse_assujetti like '%$w%' or 
				s.service like '%$w%' or 
				n.nom_assujetti like '%$w%' )";
			}
			if(isset($_POST["t_noteMontant"])and$_POST["t_noteMontant"]!="" ){
				$w = $_POST["t_noteMontant"];
				$t_noteMontant = " and montant > $w ";
			}
			if(isset($_POST["whereDate_ordo1"])and$_POST["whereDate_ordo2"]!="" ){
				$w = $_POST["t_noteMontant"];
				$whereDate_ordo = " and date_ordo BETWEEN '$_POST[whereDate_ordo1]' and '$_POST[whereDate_ordo2]' ";
			}
			$FiltreTextare = "";
			if(isset($_POST["FiltreTextare"]))
				$FiltreTextare = " AND n.num_note IN ($_POST[FiltreTextare]) ";
			$from = " FROM t_note n inner join 
	            t_note_actes na on  n.num_note = na.num_note inner join 
				t_acte ac on na.id_acte = ac.id inner join 
				t_service_gen s on s.id = ac.acte_id_secteur
				where invalide = 1 and apurer = 0 $FiltreTextare $whereFiltre $t_noteMontant $whereDate_ordo and enroler = 0 and
				(ADDDATE(date_depot,8)) <= NOW() 
				group by n.id
				";
			if(isset($_POST["type_note"])and $_POST["type_note"]=="toutes"){
			$from = " FROM t_note n inner join 
	            t_note_actes na on n.num_note = na.num_note inner join 
				t_acte ac on na.id_acte = ac.id inner join 
				t_service_gen s on s.id = ac.acte_id_secteur
				where invalide = 1 and (ADDDATE(date_depot,8)) <= NOW() $whereFiltre $t_noteMontant $whereDate_ordo
				group by n.id
				";
			}
			$req = "SELECT COUNT(*) AS RecordCount $from";
			
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$recordCount = count($row);
			//Get records from database
			$Select = "SELECT n.id,id_assujetti,id_secteur,na.id_acte,date_relance,banque,
				n.num_note,date_ordo,date_depot,na.frequence,SUM(na.montant_acte) montant,num_bap,
				montant_bap, note_to,
				n.nom_assujetti,n.adresse_assujetti,
				concat(n.nom_assujetti,' -- ',n.adresse_assujetti) assujetti,
				ac.nom, ac.art_bud, ac.penalite ,ac.coefficient ,
				s.service $from ";
			
			$req = $Select." ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			// $req = "SELECT id,nom_assujetti,adresse_assujetti FROM t_note where id_secteur_detenteur = 3 group by nom_assujetti,adresse_assujetti order by nom_assujetti";
			$i = $_GET["jtStartIndex"];
			$rows = array();
			$pdo_result = $conx->query($req);
			$mntTotal = 0;$mntBapTotal = 0;
			while($row = $pdo_result->fetch())
			{
				$row["num"] = ++$i;
				$mntTotal += $row["montant"];
				$mntBapTotal += $row["montant_bap"];
				$row["montant"] = chiffre($row["montant"]);
				$row["montant_bap"] = chiffre($row["montant_bap"]);
				$row["num_note"] = sprintf("%07d", "$row[num_note]");
				$rows[] = $row;
			}
			$rows[] = array("assujetti"=>"Somme totale","montant"=>chiffre($mntTotal),"montant_bap"=>chiffre($mntBapTotal));//$row;
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			// $jTableResult['Message'] 	= "$req";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		else if($_GET["action"] == "ResteRecouv")
		{
			$Filtre = "";
			if(isset($_POST["Filtre"]))
			{
				$w = $_POST["Filtre"];
				$Filtre = " and ( n.num_note ='$w' or ass.nom_assujetti LIKE '%$w%' or  ass.adresse_assujetti LIKE '%$w%' ) ";
			}
			if(isset($_POST["secteur"])and $_POST["secteur"]!="")
			{
				$w = $_POST["secteur"];
				$Filtre .= " and ( s.service LIKE '%$w%' ) ";
			}
			if(isset($_POST["date_ordo"])and $_POST["date_ordo"]!="")
			{
				$w = explode(":",$_POST["date_ordo"]);
				$Filtre .= " and ( date_depot BETWEEN '".$w[0]."' and '".$w[count($w)-1]."' ) ";
			}
			$whereApurer = "";//isset($_GET["apurer"])?"and apurer = $_GET[apurer]":"";
			$whereId_secteur = isset($_GET["id"])?" and n.pr_cpt_de_id_com = '$_GET[id]' ":"";
			$from = "
			FROM t_note n
			inner join t_assujetti ass on ass.id = n.id_assujetti 
			inner join t_note_actes na on n.id = na.id_note 
			inner join t_acte ac on na.id_acte = ac.id 
			inner join t_service s on s.id = ac.acte_id_service 
			left join t_note_actes_payer nacp on nacp.id_noteacte = na.id 
			where n.is_deleted = 0 AND nacp.id is null $Filtre 
				";
			$message = "";$rows = array();
			if(isset($_POST["somme_par_sec"]) and ""!=$_POST["somme_par_sec"]){
				$req = "SELECT n.id AS RecordCount $from group by n.id_secteur ";
				if($pdo_result = $conx->query($req))
				$recordCount= count($pdo_result->fetchAll(PDO::FETCH_ASSOC));
				else $message = $req;
			}
			else {
				$req = "SELECT COUNT(*) AS RecordCount $from";
				if($pdo_result = $conx->query($req)){
					$row		= $pdo_result->fetch();
					$recordCount = $row['RecordCount'];
				}else $message = $req;
			}
			
			//Get records from database
			$req = "
			SELECT (@row_number:=@row_number + 1) AS num, n.id, ass.id id_assujetti,s.id id_secteur,na.id_acte,n.num_note,date_ordo,date_depot, na.freq frequence,SUM(na.montant_acte) montant,note_to,num_bap,montant_bap, ass.nom_assujetti,ass.adresse_assujetti,s.service,ac.acte 
			$from group by n.num_note ";
			
			if(isset($_POST["somme_par_sec"]) and ""!=$_POST["somme_par_sec"]){
			$req = "SELECT (@row_number:=@row_number + 1) AS num,id_secteur, SUM(na.montant_acte) montant, sum(montant_bap)montant_bap
				,s.service $from group by n.id_secteur ";
					$req .= " ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
				/* $pdo_result = $conx->query($req);
				$tot = $tot_ = 0;
				while($r = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$tot += $r["montant"];
					$tot_ += $r["montant_bap"];
					$r["montant"] = "<label style='float:right'>".chiffre($r["montant"])."</label>";
					$r["montant_bap"] = "<label style='float:right'>".chiffre($r["montant_bap"])."</label>";
					$rows[] = $r;
				}
				$rows[] = array("service"=>"<label style='font-weight:bold;text-align:center'>Totaux</label>","montant"=>"<label style='font-weight:bold;float:right'>".chiffre($tot)."</label>","montant_bap"=>"<label style='font-weight:bold;float:right'>".chiffre($tot_)."</label>"); */
			}
			else{
				$req .= " ORDER BY -- num,
				" . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
				$req = $req;
				$pdo_result = $conx->query("SET @row_number = $_GET[jtStartIndex];");
				$pdo_result = $conx->query($req);
				$rows		= array();//$pdo_result->fetchAll(PDO::FETCH_ASSOC);
				$i=1;$mot=$motv=0;
				while($r=$pdo_result->fetch()){
					$r["num"] = $i++;
					$mot+=$r["montant"] ;
					$motv+=$r["montant_bap"] ;
					$r["montant"] = "<label style='float:right;'>".chiffre($r["montant"])."</label>";
					$r["montant_bap"] = "<label style='float:right;'>".chiffre($r["montant_bap"])."</label>";
					$rows[] = $r;
				}
				$rows[] = array("service"=>"TOTAL","montant"=>chiffre($mot),"montant_bap"=>chiffre($motv)); /* */
			}
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			// $jTableResult['Message'] 	= "$req $message";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		else if($_GET["action"] == "list")
		{
			//Get records from database
			$whereApurer = isset($_GET["apurer"])?"and apurer = $_GET[apurer]":"";
			$whereId_secteur = isset($_GET["id"])?" and id_secteur = $_GET[id]":"";
			$where = " where invalide = 1  $whereId_secteur $whereApurer";
			
			$req = "SELECT COUNT(*) AS RecordCount FROM t_note $where";
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetch();
			$recordCount = $row['RecordCount'];
			//Get records from database
			$req = $Select." $where ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			$pdo_result = $conx->query($req);
			$rows		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		
		//Creating a new record (createAction)
		else if($_GET["action"] == "create")
		{
			//Insert record into database
			$req = "INSERT INTO t_note(id_assujetti,id_acte,id_secteur,num_bap,montant_bap,note_to,num_note,date_ordo,date_depot,montant,frequence,apurer,date_recouvrement,banque,relancer,date_relance,enroler,note_date_role,invalide,date_enregistrement,id_secteur_detenteur,type_invalidation,explication_invalidation,date_invalidation,nom_assujetti,adresse_assujetti) VALUES(" .$conx->quote($_POST["id_assujetti"]).",".$conx->quote($_POST["id_acte"]).",".$conx->quote($_POST["id_secteur"]).",".$conx->quote($_POST["num_bap"]).",".$conx->quote($_POST["montant_bap"]).",".$conx->quote($_POST["note_to"]).",".$conx->quote($_POST["num_note"]).",".$conx->quote($_POST["date_ordo"]).",".$conx->quote($_POST["date_depot"]).",".$conx->quote($_POST["montant"]).",".$conx->quote($_POST["frequence"]).",".$conx->quote($_POST["apurer"]).",".$conx->quote($_POST["date_recouvrement"]).",".$conx->quote($_POST["banque"]).",".$conx->quote($_POST["relancer"]).",".$conx->quote($_POST["date_relance"]).",".$conx->quote($_POST["enroler"]).",".$conx->quote($_POST["note_date_role"]).",".$conx->quote($_POST["invalide"]).",".$conx->quote($_POST["date_enregistrement"]).",".$conx->quote($_POST["id_secteur_detenteur"]).",".$conx->quote($_POST["type_invalidation"]).",".$conx->quote($_POST["explication_invalidation"]).",".$conx->quote($_POST["date_invalidation"]).",".$conx->quote($_POST["nom_assujetti"]).",".$conx->quote($_POST["adresse_assujetti"]). ");";
			$conx->exec($req);
			//Get last inserted record (to return to jTable)
			$req = $Select." WHERE id = LAST_INSERT_ID();";
			$pdo_result	= $conx->query($req);
			$row		= $pdo_result->fetch();
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = $row;
			print json_encode($jTableResult);
		}
		
		//Updating a record (updateAction)
		else if($_GET["action"] == "update")
		{
			//Update record in database
			$req = ("UPDATE t_note SET id_assujetti = ".$conx->quote($_POST["id_assujetti"])." ,id_acte = ".$conx->quote($_POST["id_acte"])." ,id_secteur = ".$conx->quote($_POST["id_secteur"])." ,num_bap = ".$conx->quote($_POST["num_bap"])." ,montant_bap = ".$conx->quote($_POST["montant_bap"])." ,note_to = ".$conx->quote($_POST["note_to"])." ,num_note = ".$conx->quote($_POST["num_note"])." ,date_ordo = ".$conx->quote($_POST["date_ordo"])." ,date_depot = ".$conx->quote($_POST["date_depot"])." ,montant = ".$conx->quote($_POST["montant"])." ,frequence = ".$conx->quote($_POST["frequence"])." ,apurer = ".$conx->quote($_POST["apurer"])." ,date_recouvrement = ".$conx->quote($_POST["date_recouvrement"])." ,banque = ".$conx->quote($_POST["banque"])." ,relancer = ".$conx->quote($_POST["relancer"])." ,date_relance = ".$conx->quote($_POST["date_relance"])." ,enroler = ".$conx->quote($_POST["enroler"])." ,note_date_role = ".$conx->quote($_POST["note_date_role"])." ,invalide = ".$conx->quote($_POST["invalide"])." ,date_enregistrement = ".$conx->quote($_POST["date_enregistrement"])." ,id_secteur_detenteur = ".$conx->quote($_POST["id_secteur_detenteur"])." ,type_invalidation = ".$conx->quote($_POST["type_invalidation"])." ,explication_invalidation = ".$conx->quote($_POST["explication_invalidation"])." ,date_invalidation = ".$conx->quote($_POST["date_invalidation"])." ,nom_assujetti = ".$conx->quote($_POST["nom_assujetti"])." ,adresse_assujetti = ".$conx->quote($_POST["adresse_assujetti"])."  WHERE id = $_POST[id];");
			$conx->exec($req);
			
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			print json_encode($jTableResult);
		}
		
		//Deleting a record (deleteAction)
		else if($_GET["action"] == "delete")
		{
			//Delete from database
			$req = "DELETE FROM t_note WHERE id = $_POST[id];";
			$conx->exec($req);
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			print json_encode($jTableResult);
		}

		//Close database connection
		$conx = null;
		
	}
	catch(Exception $ex)
	{
		//Return error message
		$jTableResult = array();
		$jTableResult['Result'] = "ERROR";
		$jTableResult['Message'] = $ex->getMessage();
		print json_encode($jTableResult);
	}
	
