<?php
	# Vérifie la validité d'une date
	function is_valide_date($date, $sep='-')
	{
		if(count(explode($sep, $date))!=3)return false;
		if(!list($year, $month, $day) = explode($sep, $date))
			return false;
	 
		if($day > 31 OR $day < 1 OR $month > 12 OR $month < 1 OR $year > 32767 OR $year < 1)
			return false;
	 
		return checkdate($month, $day, $year);
	}
	
		function form_enreg_noteacte_payee(){
			echo 
				"<form enctype='multipart/form-data' method='post' action='' style=''>".
					"<table>
						<caption style='".NoteActesPayer::$style_caption."'><b>Paiement d'une note</caption>".
					"<tr>".
						"<td><label for='id_noteacte' >NP-Acte</label></td><td>:</td>".
						"<td><select id='id_noteacte' name='id_noteacte' required class='".NoteActesPayer::$input_class."' style='".NoteActesPayer::$input_style."' ><option value=''>Choisir</option>" .NoteActes::options("",0)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='id_releve' >Relevé</label></td><td>:</td>".
						"<td><select id='id_releve' name='id_releve' required class='".NoteActesPayer::$input_class."' style='".NoteActesPayer::$input_style."' ><option value=''>Choisir</option>" .Releve::options($_SESSION["snp"]["id_releve"])."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='montant_payer' >Montant Payé</label></td><td>:</td>".
						"<td><input type='text' value=\"\" title=\"Montant Payé\" placeholder=\"Montant Payé\" id='montant_payer' name='montant_payer' required  class='".NoteActesPayer::$input_class."' style='".NoteActesPayer::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_num_note' >N°Note Payée</label></td><td>:</td>".
						"<td><input type='text' value=\"$paie_exp_num_note\" title=\"N°Note Payée\" placeholder=\"N°Note Payée\" id='paie_exp_num_note' name='paie_exp_num_note'   class='".NoteActesPayer::$input_class."' style='".NoteActesPayer::$input_style."' /></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_id_acte' >Acte Payé</label></td><td>:</td>".
						"<td><select id='paie_exp_id_acte' name='paie_exp_id_acte'  class='".NoteActesPayer::$input_class."' style='".NoteActesPayer::$input_style."' ><option value=''>Choisir</option>" .Actes::options($paie_exp_id_acte)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_id_assujetti' >Exp-Assujetti</label></td><td>:</td>".
						"<td><select id='paie_exp_id_assujetti' name='paie_exp_id_assujetti'  class='".NoteActesPayer::$input_class."' style='".NoteActesPayer::$input_style."' ><option value=''>Choisir</option>" .Assujetti::options($paie_exp_id_assujetti)."</select></td>".
					"</tr>". 
					"<tr>".
						"<td><label for='paie_exp_date_ordo' >Exp-Date Ordo</label></td><td>:</td>".
						"<td><input type='text' value=\"$paie_exp_date_ordo\" title=\"Exp-Date Ordo\" placeholder=\"Exp-Date Ordo\" id='paie_exp_date_ordo' name='paie_exp_date_ordo' required  class='".NoteActesPayer::$input_class."' style='".Chado::$input_style."' /></td>".
					"</tr>".  
					"<tr><td></td><td></td><td>
					<input type='submit' class='".NoteActesPayer::$bt_class." btn-primary  maia-button' name='bt_attribuer_carnet' value='Attribuer ce Carnet' 	style='' >
					<input type='reset' class='".NoteActesPayer::$bt_class."' name='bt_resset_".NoteActesPayer::$table."' value='Annuler' style='float:right' >
					</td></tr>".
					"</table>".
				"</form>";
		}
	function return_extrai_role($conx,$list_num){
		$list_num = str_replace(",","','",$list_num);
		$req = "select id from t_note where num_note in ('$list_num') ";
		$pdo_result = $conx->query($req);
		$Liste_id = array();
		while($ligne = $pdo_result->fetch())$Liste_id[] = $ligne["id"];
		$Liste_id = implode(",",$Liste_id);
		return tableau_relance($conx,$Liste_id);
	}
	function tableau_relance($conx,$Liste_id){
		$var = "";
		
		$req = "select n_ord.id,n_act.id id_noteacte, n_ord.num_note, date_ordo, date_depot, n_act.freq frequence_acte, ( n_act.montant_acte ) montant,
		'0000-00-00' note_date_role, num_bap, montant_bap, '' note_to, ass.nom_assujetti, ass.adresse_assujetti, 
		act.acte nom, act.art_bud, act.penalite, act.coefficient, ser.service nom_ser_gen, (
		DATEDIFF( NOW() , date_depot ) -8
		) AS nbr_jr_retard
		-- , DATEDIFF( now() , MAKEDATE( EXTRACT(DAY FROM date_save ) , DAYOFYEAR( act.date_role ) ) ) nbr_j_pen_a
		, DATEDIFF( now() , date_depot) nbr_j_pen_a
		, EXTRACT(DAY FROM act.date_role ) db_j
		, EXTRACT(MONTH FROM act.date_role ) db_m,
		EXTRACT( YEAR FROM date_save ) db_y
		FROM t_note n_ord
		inner join t_note_actes n_act on n_act.id_Note = n_ord.id
		inner join t_acte act on act.id = n_act.id_acte
		inner join t_service ser on ser.id = act.acte_id_service
		inner join t_assujetti ass on ass.id = n_ord.id_assujetti
		where -- (ADDDATE(date_depot,8)) <= NOW() and 
		( n_ord.id IN ($Liste_id) ) ";
		// echo $req;
		// $Mnt_Princ = 0;
		$T_mt = 0;$T_pa=0;$T_pr=0;$T_tot_p=0;$T_p_60=0;$T_p_40=0;$T_tot_en=0;
		$lign_td = ''; $i=0; $TOTAL = 0;
		$ligne = "";$i=0;
		$invi = "style='color:transparent'";
		// echo "$req";
		$TabRelance = array();
		$pdo_result = $conx->query($req);
		if(!$pdo_result)
		var_dump($conx->errorInfo()[2]);
		$note="";
		while($ligne = $pdo_result->fetch())
		{
		// echo "<pre>";print_r($ligne);echo "</pre><br><br>";
			$nbrMoi_a = ceil($ligne["nbr_j_pen_a"]/30);
			$n = ($ligne["nbr_jr_retard"]?$ligne["nbr_jr_retard"]:1);
			$n = ($ligne["db_m"]?$ligne["db_m"]:1);
			$nbrMoi_r = ceil(date('m')-$n)+1;
			$nbrMoi_r = $nbrMoi_r?$nbrMoi_r:1;
			{	$mt_pri = $ligne["montant"];
				$mt_bap = $ligne["montant_bap"];
				$bap_existe = ($mt_bap == "" or $mt_bap == "0")?false:true;
				// $ligne["coefficient"] = str_replace(",",".",$_GET["coeff"]);
						
				
				// echo "<br>$p_recou = $nbrMoi_r*(($mt_pri*4)/100);";
				$p_recou_bap = $nbrMoi_r*(($mt_bap*2)/100);
				$p_recou = $nbrMoi_r*(($mt_pri*2)/100);
				
				$coeff = "$ligne[coefficient]";
				if(($ligne["db_y"] <= date("Y"))and ($ligne["db_m"] <= date("m"))/*and ($ligne["db_j"] <= date("d"))*/)
				{
					$p_acciette = $coeff=='R'?$p_recou:( $bap_existe?0:($mt_pri*$coeff));
					$p_acciette_bap = $coeff=='R'?$p_recou_bap:($bap_existe?0:($mt_bap*$coeff));
				}
				else {
					$p_acciette = 0;
					$p_acciette_bap = 0;
				}
				
						
				$tot_p = $p_acciette+$p_recou;
				// $p_60 = ((60*$tot_p)/100) + $mt_pri;
				// $p_40 = (40*($tot_p)/100);
				$p_40 = (50*($tot_p)/100);
				$p_60 = $p_40;
				$tot_en = $p_40+$p_60 + $mt_pri;
				
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
			
			$ds = "<b>";$fs = "</b>";
			{$i++;
			$id = $ligne["id"];
			$date_art = $ligne["note_date_role"]=="0000-00-00"?date("Y-m-d"):$ligne["note_date_role"];
			$apuer = "Ext.";
			$name = "$ligne[id]_$ligne[num_note]";
			$assuj = "$ligne[nom_assujetti]";
			$adrss = "$ligne[adresse_assujetti]";
			}
			if($note!=$ligne["num_note"]){
				$note = $ligne["num_note"];
				$TabRelance[$note]["assujetti"] = "$ligne[nom_assujetti]";
				$TabRelance[$note]["nom_ser_gen"] = "$ligne[nom_ser_gen]";
				$TabRelance[$note]["date_depot"] = "$ligne[date_depot]";
				$t = explode("-","$ligne[date_ordo]");
				$date = count($t)==3?"$t[2]/$t[1]/$t[0]":"00/00/0000";
				$TabRelance[$note]["date_ordo"] = "$ligne[date_ordo]";
				$TabRelance[$note]["num_note"] = sprintf("%09d", $note);
				// on crée la ligne de totalité
				
				$TabRel = array();
				// $TabRel["assujetti"] = $ds."TOTAUX$fs";
				$TabRelance[$note]["data"]["total"]["mt_pri"] = 0;
				$TabRelance[$note]["data"]["total"]["p_acciette"] = 0;
				$TabRelance[$note]["data"]["total"]["p_recou"] = 0;
				$TabRelance[$note]["data"]["total"]["tot_p"] = 0;
				$TabRelance[$note]["data"]["total"]["p_60"] = 0;
				$TabRelance[$note]["data"]["total"]["p_40"] = 0;
				$TabRelance[$note]["data"]["total"]["tot_en"] = 0;
				// $TabRelance[$note]["data"]["total"] = $TabRel;
			}
			
			$fc = "";
			// on ajoute la ligne du montant de l'acte
			{$TabRel = array();
			// $TabRel["assujetti"] = "$ligne[nom_assujetti]";
			// $TabRel["nom_ser_gen"] = "$ligne[nom_ser_gen]";
			// $TabRel["date_depot"] = "$ligne[date_depot]";
			// $TabRel["date_ordo"] = $date;
			// $TabRel["num_note"] = sprintf("%09d", $note);
			$act = "acte_$ligne[id_noteacte]";
			$TabRelance[$note]["data"][$act]["BapOUnon"] = "Princ";
			$TabRelance[$note]["data"][$act]["nom_ser_gen"] = "$ligne[nom_ser_gen]";
			$TabRelance[$note]["data"][$act]["acte"] = "$ligne[nom]";
			$TabRelance[$note]["data"][$act]["mt_pri"] = chiffre($mt_pri)."$fc";
			$TabRelance[$note]["data"][$act]["p_acciette"] = chiffre($p_acciette)."$fc";
			$TabRelance[$note]["data"][$act]["p_recou"] = chiffre($p_recou)."$fc";
			$TabRelance[$note]["data"][$act]["tot_p"] = chiffre($tot_p)."$fc";
			$TabRelance[$note]["data"][$act]["p_60"] = chiffre($p_60)."$fc";
			$TabRelance[$note]["data"][$act]["p_40"] = chiffre($p_40)."$fc";
			$TabRelance[$note]["data"][$act]["nbr_j_pen_a"] = "$ligne[nbr_j_pen_a]";
			$TabRelance[$note]["data"][$act]["tot_en"] = chiffre($tot_en)."$fc";
			
			// insetion de la ligne de l'acte
			// $TabRelance[$note][$act] = $TabRel;
				
			// insertion du bap pour la note une seule fois
			if(!isset($TabRelance[$note]["data"]["bap"]) and !empty($mt_bap)){
				$TabRel = array();
				$TabRelance[$note]["data"]["bap"]["BapOUnon"] = "BAP";
				$TabRelance[$note]["data"]["bap"]["acte"] = "A.T";
				$TabRelance[$note]["data"]["bap"]["mt_pri"] = chiffre($mt_bap)."$fc";
				$TabRelance[$note]["data"]["bap"]["p_acciette"] = chiffre($p_acciette_bap)."$fc";
				$TabRelance[$note]["data"]["bap"]["p_recou"] = chiffre($p_recou_bap)."$fc";
				$TabRelance[$note]["data"]["bap"]["tot_p"] = chiffre($tot_p_bap)."$fc";
				$TabRelance[$note]["data"]["bap"]["p_60"] = chiffre($p_60_bap)."$fc";
				$TabRelance[$note]["data"]["bap"]["p_40"] = chiffre($p_40_bap)."$fc";
				$TabRelance[$note]["data"]["bap"]["tot_en"] = chiffre($tot_en_bap)."$fc";
				// $TabRelance[$note]["bap"] = $TabRel;
			}
			}
			// on ajoute la ligne du bap
			// if(!$bap_existe){
				/* 
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
				$TabRelance[] = $TabRel; */
			// }
			// là on ajoute la ligne de totalité
			
			// echo"<pre>";print_r($TabRelance);echo"</pre>";
			$TabRelance[$note]["data"]["total"]["mt_pri"] 		+= $T_mt;
			$TabRelance[$note]["data"]["total"]["p_acciette"]	+= $T_pa;
			$TabRelance[$note]["data"]["total"]["p_recou"] 	+= $T_pr;
			$TabRelance[$note]["data"]["total"]["tot_p"] 	+= $T_tot_p;
			$TabRelance[$note]["data"]["total"]["p_60"] 	+= $T_p_60;
			$TabRelance[$note]["data"]["total"]["p_40"] 	+= $T_p_40;
			$TabRelance[$note]["data"]["total"]["tot_en"] 	+= $T_tot_en;
		}	
		
		$req = "update t_note set nbr_relance = relancer+1, date_relance = now() where id = '$Liste_id' ;";
		$conx->exec($req);
		return $TabRelance;
	}
	
	//trancage de l'extrait de role
	function tracer_extrait_role($tab){
		$table = $td_bap = $td_total = $td_acte = $td_assu = "";
		$t_p_acciette = $t_mt_pri = $t_p_recou = $t_tot_p = $t_p_60 = $t_p_40 = $t_tot_en = 0;
		foreach($tab as $note=>$infos){
			$td_bap = $td_total = $td_acte = $td_assu = "";
			$ass = "$infos[assujetti]";
			$ser = "$infos[nom_ser_gen]";
			$dpt = "$infos[date_depot]";
			$dod = "$infos[date_ordo]";
			$not = "$infos[num_note]";
			$td_assu .= "<tr><th colspan='10' style='font-weight:bold;text-align:center'>N.P.: $not Ordonnancée le: $dod et Déposée le: $dpt</th></tr>";
			// echo"<pre>";print_r($infos);echo"</pre>";
			$data = $infos["data"];
			foreach($data as $cle=>$val){
				if($cle=="bap"){
					$td_bap .= "<tr><td>BAP</td><td>$val[acte]</td><td>$val[mt_pri]</td><td>$val[p_acciette]</td><td>$val[p_recou]</td><td>$val[tot_p]</td><td>$val[p_60]</td><td>$val[p_40]</td><td>$val[tot_en]</td></tr>";
				}
				else if($cle=="total"){
					$td_total .= "<tr><td>TOTAUX</td><td></td><td>".chiffre($val['mt_pri'])."</td><td>".chiffre($val['p_acciette'])."</td><td>".chiffre($val['p_recou'])."</td><td>".chiffre($val['tot_p'])."</td><td>".chiffre($val['p_60'])."</td><td>".chiffre($val['p_40'])."</td><td>".chiffre($val['tot_en'])."</td></tr>";
					$t_mt_pri += $val['mt_pri'];$t_p_acciette += $val['p_acciette'];$t_p_recou += $val['p_recou'];$t_tot_p += $val['tot_p'];$t_p_60 += $val['p_60'];$t_p_40 += $val['p_40'];$t_tot_en += $val['tot_en'];
				}
				else {
					$td_acte .= "<tr><td>$val[nom_ser_gen]</td><td>$val[acte]</td><td>$val[mt_pri]</td><td>$val[p_acciette]</td><td>$val[p_recou]</td><td>$val[tot_p]</td><td>$val[p_60]</td><td>$val[p_40]</td><td>$val[tot_en]</td></tr>";
				}
			}
			$table .= "$td_assu $td_acte $td_bap $td_total";
		}
		$ss = " style='text-align:right'";
		$t_tr = count($tab)>1?
		"<tr><td style='border:none'></td><th$ss>TOTAUX GEN.</th><th$ss>".chiffre($t_mt_pri)."</th><th$ss>".chiffre($t_p_acciette)."</th><th$ss>".chiffre($t_p_recou)."</th><th$ss>".chiffre($t_tot_p)."</th><th$ss>".chiffre($t_p_60)."</th><th$ss>".chiffre($t_p_40)."</th><th$ss>".chiffre($t_tot_en)."</th></tr>":"";
		
		$cles = array("service"=>"Service","acte"=>"Acte","mt_pri"=>"Montant","p_acciette"=>"P.A.","p_recou"=>"2%Int./M.","tot_p"=>"T.Péna","p_60"=>"P.50%","p_40"=>"P.50%","tot_en"=>"TOTAL");
		$j=0;
		echo "<table style='width:100%;text-align:right'>";
		echo "<tr>";
		$s_no = " style='border:none' ";
		foreach($cles as $cl=>$val)
			echo "<th>$val</th>";
		echo "</tr>
		$table 
		$t_tr 
		</table>";
		// if(!isset($_POST["bt_extraire_roles"]) and !isset($_GET["id_ass"]))echo"<button onClick=\"document.getElementById('div_extrait_de_role').innerHTML = '';\">Terminer</button>
		// ";
		// else 
		{echo
		"<table>
			<!--caption style='text-align:left'> :</caption-->
			<tr><td colspan='3' >Montant à payer</td></tr>
			<tr><td>1.Tésor Public (Principal + 50% pénalité) </td><td$s_no>:</td><td align='right' style='padding-left:50px'><b>".chiffre($t_p_60+$t_mt_pri)."</b></td></tr>
			<tr><>2.BAP (50% pénalités) </td><td>:</td><td align='right' style='padding-left:50px'> <b>".chiffre($t_p_40)."</b></td></tr>
		</table>";
		}
	}
		
	#region /* === ANALYSE N°NOTE POUR ORDONNANCEMENT === */
		
		/* la fonction dit le service ou commune detenant le carnet contenant la note
		 * à ordonnancer
		 ******************************************************************************/
		function analyser_num_note($num_note){
			$where = " where '$num_note' BETWEEN car.num_debut AND (car.num_debut+49) ";
			$Rslt = CarnetAttribuer::liste_objet($where," order by co.commune");
			// var_dump($Rslt);
			// Analyse du résultat
			{
				$pour_compte_de="";
				if($Rslt and count($Rslt)==0){
					echo"<b style='color:red'>Cette note n'appartient à aucun carnet déjà attribué.</b>";
					return false;
				}else if($Rslt[0]["souche"]){
					if($r = Note::liste_objet(" where num_note between ".$Rslt[0]["num_debut"]." and (".$Rslt[0]["num_debut"]."+49) "," ")){
						// $nbr = Note::$count;
						// $n = new Note();$n->detail($r[0]["id"]);
					}
					echo"<p id='id_p'><b style='color:red'>Cette note appartient à carnet déjà souché depuis le ".$Rslt[0]["date_epuisement"]." Et ".Note::$count." notes ont déjà été deposées.</b></p>";
					
					Note::chargerHtml("id_p",Note::$id_div_crud);
					
					return false;
				}
				else if($Rslt and count($Rslt)==1 ){
					
					// 1.
					if($Rslt[0]["commune"]=="Centre"){
						if($Rslt[0]["service"]){
							$endroit = " du Centre ".$Rslt[0]["service"];
							$opt_actes = opt_actes_service($Rslt[0]["id_service"]);
							$pour_compte_de = "<input type='hidden' name='pr_cpt_de_id_com' value='1' />";
							$src_fonc= "opt_actes_service";
							$src_parm= $Rslt[0]["id_service"];
						}
						else{
							echo "<h2 style='text-align:center'><b style='color:red'>Erreur: La note vient d'un carnet mal attribué </b></h2></br>Veuillez, S.V.P. revoir l'attribution de ce carnet (Début série: <b style='font-size:16px'>".(100*($q=((int)($num_note/100)))+(($m=$num_note%100)>50?51:1))."</b>)</br>Le nom du Centre n'a pas été précisé au moment de l'attribution.</br>";
							return false;
						}
					}
					// 2.
					else if($Rslt[0]["commune"]=="CB.ORDO"){
						$endroit = "du CB.Ordo.";
						$pour_compte_de = "Ordonnancement effectué pour compte de:<br/> <select name='pr_cpt_de_id_com' style='width:285px' title='Ordonnancer pour compte de:' >". Commune::options(" ",22)."</select>";
						$src_fonc= "opt_actes_antenne";
						$src_parm= $Rslt[0]["id_commune"];
						$opt_actes = ($Rslt[0]["id_service"])?opt_actes_service($Rslt[0]["id_service"]):opt_actes_antenne($Rslt[0]["id_commune"]);
					}
					// 3.
					else {//print_r($Rslt);
						if($Rslt[0]["commune"]){
							$endroit = "de l'antenne ".$Rslt[0]["commune"];
							$opt_actes = ($Rslt[0]["id_service"])?opt_actes_service($Rslt[0]["id_service"]):opt_actes_antenne($Rslt[0]["id_commune"]);
							$pour_compte_de = "<input type='hidden' name='pr_cpt_de_id_com' value='".$Rslt[0]["id_commune"]."' />";
							$src_fonc= "opt_actes_antenne";
							$src_parm= $Rslt[0]["id_commune"];
						}
						else{
							echo "<h2 style='text-align:center'><b style='color:red'>Erreur: La note vient d'un carnet mal attribué </b></h2></br>Veuillez, S.V.P. revoir l'attribution de ce carnet (Début série: <b style='font-size:16px'>".(100*($q=((int)($num_note/100)))+(($m=$num_note%100)>50?51:1))."</b>)</br>Le nom de l'antenne n'a pas été précisé au moment de l'attribution.</br>";
							return false;
						}
					}//else {}
					echo"<h2 style='text-align:center'><b style='color:blue'>Note $endroit</b></h2>
					$pour_compte_de
					<input type='hidden' id='source_opt_acte_ordo_fonc' value='$src_fonc' />
					<input type='hidden' id='source_opt_acte_ordo_parm' value='$src_parm' />
					".show_ordo_combo_acte($opt_actes);
					return true;
				}
				// 4. on parcourt les service et antenne concernet par le carnet et on les affiche
				else {
					echo "<h2 style='text-align:center'><b style='color:red'>Erreur: La note vient d'un carnet attribué plusieurs fois: </b></h2>Veuillez, S.V.P. revoir l'attribution de ce carnet (Début série: <b style='font-size:16px'>".(100*($q=((int)($num_note/100)))+(($m=$num_note%100)>50?51:1))."</b>)</br></br>";
					if($Rslt ){
						foreach($Rslt as $c){
						echo $c["commune"]=="Centre"?" Centre $c[service]; ":($c["commune"]=="CB.ORDO"?" CB.ORDO; ":(" l'antenne ".$c["commune"]."; "));
						echo "</br>";
					}
					}
					
						
					return false;
				}
			}
		}
		
		/* Analyse le résultat de analyser_num_note($num_note) et si la note est d'un carnet:
		 * 	1. Attribué à un service du Centre; l'id de ce service du centre est renvoyé
		 *	2. Attribué à une antenne; un combobox de Services de cette antenne là, est renvoyé
		 *	3. Attribué au CB.Ordo; un combo d'antanne (et centre),un combo des service (par antenne du centre) sont renvoyés
		 *********************************************************************************************/
		function traiter_ordo_num_note($num){
			// analyse du N° de la note
			if($r = analyser_num_note($num)){
				return true; 
			}else {echo "Le N° de la note n'appartient à aucun carnet déjà attribué!<br/>Début du carnet: <b style='font-size:16px'>".(((int)$num)-($num%50)+1)."</b>";return false;}
		}
		function show_ordo_combo_acte($tab_acte){
			$opt="";$name=" name='id_acte' ";
			$t = $tab_acte;$id_act=$acte="";
			foreach($t as $id_act=>$acte)$opt.="<option value='$id_act' >$acte</option>";
			
			if(count($t)>1){
				$fonc = "getOptions(' ',' ',' ')";
				$html_acte = "<p style='margin:0px;width:300px;' class='acte_p' >
				<select id='opt_actes_ordo' multiplee='' $name style='display:inline;width:285px;' required >$opt</select></p>";
			}
				
			else{
				$html_acte = "<input type='hidden' value='$id_act' $name />
				<br/><p  style='margin:0px;width:300px;' class='acte_p' >
				<label >Acte</label>: $acte</p>";
			}
			return $html_acte 
			. form_acte_ordo();
		}
		// renvoi pour une antenne, tous les id_services (??_??) sous forme d'options d'un combo
		function opt_serv_antenne($id_antenne){
			$opt  ="";$optArray = array(); $and = $id_antenne?" and cs.id_commune='$id_antenne' ":"";
			// On charge les antennes, sauf les Centres
			$req = "SELECT cs.id,service FROM `t_service` s inner join `t_assoc_com_serv` cs on cs.id_service=s.id  $and Order By cs.id_service,service ";
			// echo $req;
			if($pdo_result = Chado::$conx->query($req)){
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					// $opt .= "<option value='$row[id]' >$row[service]</option>";
					$optArray["$row[id]"] = "$row[service]";
				}
			}else echo Chado::$conx->errorInfo()[2];
			// return $opt;
			return $optArray;
		}
		// renvoi pour un service gén.; un tableau assoc d'id_act=>nom_acte
		// les id_acte d'un service dans un combo
		function opt_actes_service($id_service){
			$opt  =""; $optArray = array();
			$req = "SELECT a.id,a.acte,a.art_bud FROM t_acte a WHERE a.acte_id_service = '$id_service' 
			and a.is_deleted!=1 ";
			// echo $req;
			if($pdo_result = Chado::$conx->query($req)){
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					// $opt .= "<option value='$row[id]' >$row[acte] / $row[art_bud]</option>";
					$optArray["$row[id]"] = "$row[acte] / $row[art_bud]";
				}
			}else echo Chado::$conx->errorInfo()[2];
			// return $opt;
			return $optArray;
		}
		// les id_acte d'un service dans un combo
		function opt_actes_antenne($id_antenne){
			$opt  =""; $optArray = array();
			$req = "SELECT s.service,a.acte,art_bud, a.id FROM t_com_serv cs 
			INNER JOIN t_acte a ON a.acte_id_service = cs.id_serv 
			INNER JOIN t_service s on s.id = a.acte_id_service
			WHERE cs.id_com = '$id_antenne' and a.is_deleted!=1 ";
			// echo $req;
			if($pdo_result = Chado::$conx->query($req)){
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					// $opt .= "<option value='$row[id]' >$row[acte] / $row[art_bud]</option>";
					$optArray["$row[id]"] = "($row[service]) $row[acte] / $row[art_bud]";
				}
			}else echo Chado::$conx->errorInfo()[2];
			// return $opt;
			return $optArray;
		}
		// renvoi les id_service d'une antenne (??_??) dans un combo
		function opt_service_commune(){
			$opt  =""; 
			$req = "SELECT id,acte FROM t_acte where acte_id_service='$id_service' Order By acte ";
			// echo $req;
			if($pdo_result = Chado::$conx->query($req)){
				while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
					$opt .= "<option value='$row[id]' >$row[acte]</option>";
				}
			}else echo Chado::$conx->errorInfo()[2];
			return $opt;
		}
		
	#end 
		
	#region CREATION FORMULAIRE ENREGISTREMENT NOTE
		function form_note_ordo(){
			// return "";
			return "
			<table id='table_ordonnancer_note' style='floatt:left' >
				<!--tr><td>
					<input title=\"Assujetti\" placeholder=\"Assujetti\" id='filtre_nom_assujetti' name='nom_assujetti' style='' onKeyUp=\"getOptions('assu','','')\" />
				</td><td>
					<input title=\"Adresse Assujetti\" placeholder=\"Adresse Assujetti\" id='filtre_adresse_assujetti' name='adresse_assujetti' style=''  onKeyUp=\"getOptions('assu','','')\" />
				</td></tr-->
				<tr><td colspan='2' id='list_assujetti' >
					<select id='id_assujetti_ordo' name='id_assujetti' multiplee='' class='' style='width:370px' ><option value=''>Choisir</option>" .Assujetti::options("",0)."</select>
				</td></tr>
				<tr><td>
					<input value=\"".date("Y-m-d")."\" title=\"Date Ordo.\" placeholder=\"Date Ordo.\" id='date_ordo' name='date_ordo' required  class='' style='' />
				</td><td>
					<input value=\"".date("Y-m-d")."\" title=\"Date Dépot\" placeholder=\"Date Dépot\" id='date_depot' name='date_depot' required  class='' style='' />
				</td></tr>
				
				<tr><td><input title=\"N°Bap\" placeholder=\"N°Bap\" id='num_bap' name='num_bap'   class='' style='' /></td><td>
				<input title=\"Mont.BAP\" placeholder=\"0.00Fc\" id='montant_bap' name='montant_bap'   class='' style='' /></td></tr>
				
				<tr><td colspan='2' ><p id='erreur_en_remplacement_de' style='color:red;text-align:center;margin:0;padding:0'></p></td></tr>
				<tr><td style='text-align:center'>Taxation d'office?<br/> <input type='checkbox' title=\"TO ?\" id='note_to' name='note_to' /></td><td>En remplacement de la note:<br/>
				<input title=\"N° note à remplacer\" placeholder=\"N° note à remplacer\" id='en_remplacement_de' name='remplacement_de' class='' style='' onBlur=\"remplacer_note('en_remplacement_de')\" /></td></tr>
				<tr><td colspan='2' >
				<textarea title=\"raison de remplacement\" placeholder=\"raison de remplacement\" id='' name='raison_remplacage' class='' style='width:90%;height:50px' ></textarea>
				</td></tr>
				<tr><td colspan='2' >
				<textarea title=\"Observation\" placeholder=\"Observation\" id='' name='observation' class='' style='width:90%;height:50px' ></textarea>
				</td></tr>
			</table><script>id_num_note='en_remplacement_de';</script>
			";
		}
		function enregistrer_assujetti($nom,$adresse){
			if( ''!= $nom){
				$req = "INSERT INTO `t_assujetti`(nom_assujetti,adresse_assujetti) VALUES (" .Chado::$conx->quote($nom).",".Chado::$conx->quote($adresse). ");";
				if(Chado::$conx->exec($req)){
					return Chado::$conx->lastInsertId();
				}else echo Chado::$notifInsErro.Chado::$conx->errorInfo()[2];
			}else echo Chado::$notifRemplir;
		}
		function enregistrer_note(){
			if( ''!= $_POST["num_note"] and ''!= $_POST["date_ordo"] and ''!= $_POST["date_depot"] and (''!=($_POST["id_assujetti"]=($_POST["id_assujetti"]==''?(isset($_SESSION['t_assujetti']['id'])?$_SESSION['t_assujetti']['id']:''):$_POST["id_assujetti"])))){
				$req = "INSERT INTO t_note (id_assujetti,pr_cpt_de_id_com,num_bap,montant_bap,note_to,num_note,date_ordo,date_depot,remplacement_de,date_save) VALUES(" .Chado::$conx->quote($_POST["id_assujetti"]).",".Chado::$conx->quote($_POST["pr_cpt_de_id_com"]).",".Chado::$conx->quote($_POST["num_bap"]).",".Chado::$conx->quote($_POST["montant_bap"]).","."'".isset($_POST["note_to"])."'" .",".Chado::$conx->quote($_POST["num_note"]).",".Chado::$conx->quote($_POST["date_ordo"]).",".Chado::$conx->quote($_POST["date_depot"]).",".Chado::$conx->quote($_POST["remplacement_de"]).",".Chado::$conx->quote(date('Y-m-d H:i:s')). ");";
				if(Chado::$conx->exec($req)){
					$id =Chado::$conx->lastInsertId();
					$num_note = (int)$_POST["num_note"];
					$d = (100*($q=((int)($num_note/100)))+(($m=$num_note%100)>50?51:1));
					$req = "select count(*) n from t_note where num_note between $d and ".($d+49);
					if($r = Chado::$conx->query($req)){
						if($c = $r->fetch()){
							if($c["n"]>=50){
								$req="update t_carnet set souche =1 where num_debut=$d ";
								Chado::$conx->exec($req);
							}
						}
					}
					return $id;
				}else echo Chado::$notifInsErro.Chado::$conx->errorInfo()[2];
			}else echo Chado::$notifRemplir;
		}
		function enregistrer_note_acte($id_Note){
			if( ''!=$_POST["frequence_acte"] and ''!=$_POST["id_acte"] and ''!=$_POST["montant_acte"]){
				$req = "INSERT INTO t_note_actes (id_acte,id_note,montant_acte,freq,ajouter_le) VALUES
				(".Chado::$conx->quote($_POST["id_acte"]).",'$id_Note',".Chado::$conx->quote($_POST["montant_acte"]).",".Chado::$conx->quote($_POST["frequence_acte"]).",now());";
				if(Chado::$conx->exec($req)){
					echo Chado::$notifInsSucc;
					return Chado::$conx->lastInsertId();
				}else echo Chado::$notifInsErro.Chado::$conx->errorInfo()[2];
			}else echo Chado::$notifRemplir;
		}
		function form_acte_ordo(){
			$std = " style='padding:0;border:none' ";
			return "
			<table><tr><td$std>
				<label for='montant_acte' >Mont. Acte</label></td><td$std>
				<input type='text' title=\"Mont. Acte\" placeholder=\"Mont. Acte\" id='montant_acte' name='montant_acte' required  class='' style='width:220px;' />
			</td></tr>
			<tr><td$std>
				<label for='frequence_acte'>Fréqence.</label></td><td$std>
				<input type='text' title=\"Fréq.\" placeholder=\"Fréq.\" value='1'  id='frequence_acte' name='frequence_acte' required  class='' style='width:220px;' />
			</td></tr>
			<tr><td></td><td>
				<input type='submit' id='bt_valider_ajt_acte_ordo' name='bt_valider_ajt_acte_ordo' value='Valider Ajout Acte' class='maia-button' />
				<p id='id_rapport_save_acte_ordo'></p>
			</td></tr></table>
			";
		}
		
		function traiter_id_service($id_com_serv){
			$opt="";$name=" name='id_acte' ";
			$t = opt_actes_service($id_com_serv);
			foreach($t as $id_act=>$acte)$opt.="<option value='$id_act' >$acte</option>";
			
			if(count($t)>1)
				$html_acte = "<br/><p  style='margin:0px;width:300px;' class='acte_p' >
				<label >Acte</label>:
				<select id='opt_actes_service' $name style='display:inline'>$opt</select></p>";
			else{
				$html_acte = "<input type='hidden' value='$id_act' $name />
				<br/><p  style='margin:0px;width:300px;' class='acte_p' >
				<label >Acte</label>: $acte</p>";
			}
			return $html_acte 
			. "<input type='hidden' value='$id_com_serv' name='id_com_serv_ordo' />"
			. form_acte_ordo();
		}
		
		
	#end
	function enregistrer_note_enserie($debut,$nbr,$date_ordo,$date_depot,$id_compte_de,$montant,$id_acte,$id_ass,$freq=1){
		$message="";$nbr_note_ok=0;
		for($i=$debut;$i!=($debut+$nbr);$i++){
			$req = "select id from t_note where num_note='$i' ";
			if(!($r=Chado::$conx->query($req)) or !($r->fetch())){
				$req = "insert into t_note (id_assujetti,pr_cpt_de_id_com,num_note,date_ordo,date_depot,date_save)values('$id_ass','$id_compte_de','$i','$date_ordo','$date_depot',now()); ";
				if(Chado::$conx->exec($req)){				
					$_POST["id_note"]=$id_note = Chado::$conx->lastInsertId();						
					$req="insert into t_note_actes (id_acte,id_note,montant_acte,freq,ajouter_le)value('$id_acte','$id_note','$montant','$freq',now());";
					if(!Chado::$conx->exec($req))$message .="Acte no enregistré!<br>";
					else $nbr_note_ok++;
				}
				else $message .="Note ($i) non enregistrée!<br>";
			}else $message .= "Note ($i) est déjà enregistrée!<br>";
		}
		$num_note = $debut+$nbr_note_ok;
		$d = (100*($q=((int)($num_note/100)))+(($m=$num_note%100)>50?51:1));
		$req = "select count(*) n from t_note where num_note between $d and ".($d+49);
		if(Chado::$conx->query($req)){
			if($c = $r->fetch()){
				if($c["n"]>=50){
					$req="update t_carnet set souche =1 where num_debut=$d ";
					Chado::$conx->exec($req);
				}
			}
		}
		echo "$nbr_note_ok notes enregistrées!<br>".$message;
	}
		
		