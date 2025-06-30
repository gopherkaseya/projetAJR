	<?php $year = explode("-",$dt_ord1)[0];$month=explode("-",$dt_ord1)[1]; ?>
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php
		function afficher_ligne($pdo_result,$th){
			$rows = array();
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=$mntt=0;
			
			while($r){$i=0;
				// echo "<h3>$r[commune]</h3>";text-align:center;
				echo "<table style='width:100%;margin-bottom:30px'>";
				$mnt=0;
				echo "<caption style='font-weight:bold;font-size:16px;font-style:italic;backgroundd:#abc;color:#4e78c2'>Les Ordonnancements de: ".($r["id_ville"]!=1?"$r[ville]":"$r[commune]")." </caption>$th";
				do{
					do{$mnst=0;
						echo "<tr style='font-weight:bold;font-style:italic;backgroundd:#abc'><th></th><th colspan='10' style='text-align:center;'>Les Ordonnancements du secteur de: $r[service]</th></tr>";
						do{	$i++;					
							$mnt += $r["montant_acte"];$mnst += $r["montant_acte"];
							echo "<tr><td>$i</td>
							
							<td>$r[nom_assujetti]</td>"./* "<td>$r[service]</td>". */"<td>$r[acte]</td><td>$r[frequence_acte]</td><td>$r[num_note]</td><td>$r[date_ordo]|$r[date_depot]</td><td>$r[art_bud]</td><td>$r[montant_acte]</td>
							<td>$r[observation]</td><td>$r[montant_bap]</td>					
							</tr>"; 
							// $r["montant"] = chiffre($r["montant"]);
							$ex = $r;
						}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["id_ville"]==$r["id_ville"] and $ex["id_commune"]==$r["id_commune"] and $ex["id_service"]==$r["id_service"]);
						echo "<tr style='font-weight:bold;font-style:italic;background:#cdf'><td></td>
							<td>s/total</td>"./* "<td></td>". */"<td></td><td></td><td></td><td></td><td></td><td>$mnst</td><td></td><td></td></tr>";
						$mntt += $mnst;
					}while(($ex["id_ville"]==$r["id_ville"] and $ex["id_commune"]==$r["id_commune"])
						/* or($r["ville"]!="Lubumbashi") */);
					echo "<tr><th colspan='4'></th><th colspan='30'>".chiffre($mnt)." : Total Principal</th></tr>";
				}while($ex["id_ville"]==$r["id_ville"] and (0 or $r["id_ville"]!=1));
				echo "</table>";
				if(!$r)break;
			}
			return array($i,$mntt);
		}
		{$req = "SELECT n_act.montant_acte ,ser.service ,act.acte ,act.art_bud ,n_act.freq  frequence_acte ,n_ord.date_ordo ,n_ord.date_depot ,ass.nom_assujetti ,ass.adresse_assujetti ,n_ord.num_note ,n_ord.num_bap ,ifnull('',concat(n_ord.num_bap,' ',n_ord.montant_bap))montant_bap ,n_ord.note_to observation ,n_ord.date_save date_enrg ,n_act.ajouter_le date_enreg ,co.commune ,co.id id_commune,ser.id id_service,n_ap.montant_payer,extract(YEAR FROM n_ord.date_depot)annee, extract(MONTH FROM n_ord.date_depot)mois
		
		FROM t_note_actes  n_act
		INNER JOIN t_acte act ON n_act.id_acte = act.id 
		INNER JOIN t_service ser ON act.acte_id_service = ser.id 
		INNER JOIN t_note n_ord ON n_act.id_note = n_ord.id 
		INNER JOIN t_assujetti ass ON n_ord.id_assujetti = ass.id 
		INNER JOIN t_commune co ON  n_ord.pr_cpt_de_id_com = co.id 
		
		LEFT JOIN (
			t_note_actes_payer n_ap INNER JOIN t_releve rlv on rlv.id=n_ap.id_releve
			) ON n_ap.id_noteacte = n_act.id 
			and (extract(YEAR FROM rlv.date_paiement)='$year' and extract(MONTH FROM rlv.date_paiement)='$month')
			
		WHERE n_ord.is_deleted=0 and ((extract(YEAR FROM n_ord.date_depot)='$year' and extract(MONTH FROM n_ord.date_depot)='$month') or (extract(YEAR FROM rlv.date_paiement)='$year' and extract(MONTH FROM rlv.date_paiement)='$month'))
		order by ser.service,act.acte ";
		}
		//echo $req ;
		//exit();
		$th = "";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = "";$ord = "ordo";$rec = "rec";$niv = "niv";$res = "res";
		if($pdo_result = Chado::$conx->query($req.$whre_order)){//
			$tab = array();$tab_col = array();
			while($r = $pdo_result->fetch(PDO::FETCH_ASSOC)){
				$cl_lg = $r["acte"];
				// si nous ne somme pas en face de lubumbashi
				
				if($r['annee']==$year and $r['mois']==$month){
					if(isset($tab[$cl_lg][$ord]))$tab[$cl_lg][$ord] += $r["montant_acte"];
					else {
						$tab[$cl_lg]["service"] = $r["service"];
						$tab[$cl_lg][$ord] = $r["montant_acte"];
					}
					
					if(isset($tab[$cl_lg][$rec]))$tab[$cl_lg][$rec] += $r["montant_payer"];
					else $tab[$cl_lg][$rec] = $r["montant_payer"];
				}
				if(isset($tab[$cl_lg][$niv]))$tab[$cl_lg][$niv] += $r["montant_payer"];
				else {
					$tab[$cl_lg]["service"] = $r["service"];
					$tab[$cl_lg][$niv] = $r["montant_payer"];
					if(!isset($tab[$cl_lg][$rec]))$tab[$cl_lg][$rec] = 0;
					if(!isset($tab[$cl_lg][$ord]))$tab[$cl_lg][$ord] = 0;
				}
			}
			$lg_total = $tab_col;
			$tab_col = array_keys($tab_col);
			// echo "<pre>";print_r($tab);echo"</pre>";
			//
			$totaux = 0;
			echo "<table style='width:100%;font-weight:bold;font-style:italic;text-align:center'><caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>situation générale des recettes ordonnancées et recouvrées et nivelées provisoire / $month-$year</caption>";
			$thi = "th style='font-style:italic;' ";
			echo "<trstyle='font-weight:bold;backgroundd:#abc'>
			<$thi>N°</th><$thi>SECTEURS</th><$thi>ACTES GEN</th>";
			echo "<$thi>Ordonnancées</th><$thi>Recouvrées</th><$thi>Nivelées</th><$thi>Rest à rec.</th>";
			echo "</tr>";
			//
			$str = " style='background:#cdf' ";
			$j = 1;$ex_serv="";$style="style='font-weight:bold;font-style:italic;font-size:12px'";
			$t_ord = $t_rec = $t_niv = $t_res = $st_ord = $st_rec = $st_niv = $st_res = 0;
			foreach($tab as $cle=>$l){
				if($l['service']!=$ex_serv){
					$n=$j++;$serv = $l['service'];
					if(""!=$ex_serv){						
						echo "<tr$str><td></td><td $style ></td><td>s/total</td><td>".chiffre($st_ord)."</td><td>".chiffre($st_rec)."</td><td>".chiffre($st_niv)."</td><td>".chiffre($st_res)."</td></tr>";
					}
					$st_ord = $st_rec = $st_niv = $st_res = 0;
				}
				$rest = $l[$ord]-$l[$rec];
				echo "<tr><td>$n</td><td $style >$serv</td><td>$cle</td><td>$l[$ord]</td><td>$l[$rec]</td><td>$l[$niv]</td><td>$rest</td></tr>";
				$n = $serv = "";
				$t_ord += $l[$ord];$t_rec += $l[$rec];$t_niv += $l[$niv];$t_res += $rest;
				$st_ord += $l[$ord];$st_rec += $l[$rec];$st_niv += $l[$niv];$st_res += $rest;
				$ex_serv = $l['service'];
			}
			echo "<tr$str><td></td><td $style ></td><td>s/total</td><td>".chiffre($st_ord)."</td><td>".chiffre($st_rec)."</td><td>".chiffre($st_niv)."</td><td>".chiffre($st_res)."</td></tr>";
			echo "<tr><td></td><td $style ></td><td>Totaux</td><td>".chiffre($t_ord)."</td><td>".chiffre($t_rec)."</td><td>".chiffre($t_niv)."</td><td>".chiffre($t_res)."</td></tr>";
			echo "</table>";
		}
		
		$std = " style='border:none'";
		echo "
		<table border='0' style='width:100%;font-weight:bold;font-style:italic;text-align:center'>
		<tr><td $std>Total général: ".chiffre($totaux)." Fc</td><td style='width:500px;border:none'></td><td$std>Lubumbashi le ".date("d-M-Y")."</td></tr>
		<tr><td$std>Cumul: ".chiffre(0)." Fc</td><td$std></td><td$std></td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std>LE C.B. ORDONNANCEMENT</td><td$std></td><td$std>LE C.B. CONTRÔLE </td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std>Francine PEMBA MAKUTU</td><td$std></td><td$std>SIX VUNINGA A KIRIZA</td></tr>
		</table>";
		?>
	</div>
	