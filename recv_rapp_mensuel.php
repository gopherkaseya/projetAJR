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
		{$req = "SELECT n_ap.montant_payer ,ser.service ,ac.acte ,ac.art_bud ,nac.freq ,no.date_ordo ,no.date_depot ,ass.nom_assujetti ,ass.adresse_assujetti ,no.num_note ,no.num_bap ,ifnull('',concat(no.num_bap,' ',no.montant_bap))montant_bap ,no.note_to ,no.date_save date_enrg ,nac.ajouter_le date_enreg ,co.commune,co.id id_commune
		
		FROM t_note_actes nac 
		INNER JOIN t_acte ac ON nac.id_acte = ac.id 
		INNER JOIN t_service ser ON ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON nac.id_Note = no.id 
		INNER JOIN t_assujetti ass ON no.id_assujetti = ass.id 
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				
		INNER JOIN (
			t_note_actes_payer n_ap inner join t_releve rlv on rlv.id=n_ap.id_releve
			) ON n_ap.id_noteacte = nac.id 
		WHERE n_ap.is_deleted=0 and nac.is_deleted=0 and no.is_deleted=0 and rlv.is_deleted=0 
		and ((extract(YEAR FROM rlv.date_paiement)='$year' and extract(MONTH FROM rlv.date_paiement)='$month'))
		and ((extract(YEAR FROM no.date_depot)='$year' and extract(MONTH FROM no.date_depot)='$month'))
		order by ser.service,ac.acte ";
		}
		//echo $req;
		$th = "";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = "";
		if($pdo_result = Chado::$conx->query($req.$whre_order)){//echo $req.$whre_order;
			$tab = array();$tab_col = array();
			while($r = $pdo_result->fetch(PDO::FETCH_ASSOC)){
				$cl_lg = $r["acte"];
				// si nous ne somme pas en face de lubumbashi
				$cl_col = ($r["commune"]);
				if(isset($tab[$cl_lg][$cl_col]))
					$tab[$cl_lg][$cl_col] += $r["montant_payer"];
				else {
					$tab[$cl_lg]["service"] = $r["service"];
					$tab[$cl_lg][$cl_col] = $r["montant_payer"];
					
				}
				if(!isset($tab_col[$cl_col]))$tab_col[$cl_col]=0;
				$tab_col[$cl_col] += $r["montant_payer"];
			}
			//var_dump($tab);
			$lg_total = $tab_col;
			$tab_col = array_keys($tab_col);
			// echo "<pre>";print_r($tab);echo"</pre>";
			//
			$totaux = 0;
			echo "<table style='width:100%;font-weight:bold;font-style:italic;text-align:center'><caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>RAPPORT MENSUEL DES RECOUVREMENTS DES ORDONNANCEMENTS DE: $mois[$month]</caption>";
			$thi = "th style='font-style:italic;' ";
			echo "<trstyle='font-weight:bold;backgroundd:#abc'>
			<$thi>N°</th><$thi>SECTEURS</th><$thi>ACTES GEN</th>";
			for($i=0;$i!=count($tab_col);$i++){
				echo "<$thi>$tab_col[$i]</th>";
			}
			echo "<$thi>TOAL</th></tr>";
			//
			$style = " style='text-align:right' ";
			$j = 1;$ex_serv=$n="";$lg_tt = array();
			foreach($tab as $cle=>$ligne){
				$total=0;
				if($ligne['service']!=$ex_serv){
					if($ex_serv!=""){
						$stotal=0;
						echo "<tr><td></td><td style='font-weight:bold;font-style:italic;font-size:12px' >s/total</td><td></td>";
						for($i=0;$i!=count($tab_col);$i++){
							$mt = is_numeric($lg_tt[$tab_col[$i]])?$lg_tt[$tab_col[$i]]:0;
							$stotal += $mt;
							echo "<td>$mt</td>";
						}
						echo "<td$style>".chiffre($stotal)."</td></tr>";
					}
					$n=$j++;
					$serv = $ligne['service'];
					$lg_tt = array();
				}
				$ex_serv = $ligne['service'];
				echo "<tr><td>$n</td><td style='font-weight:bold;font-style:italic;font-size:12px' >$serv</td><td>$cle</td>";
				for($i=0;$i!=count($tab_col);$i++){
					$mt = "&nbsp;";
					if(isset($ligne[$tab_col[$i]])){
						$mt = $ligne[$tab_col[$i]]?$ligne[$tab_col[$i]]:0;
						$total += $mt;
					}
					// if(isset($lg_tt[$tab_col[$i]]))$lg_tt[$tab_col[$i]] += $mt;
					if( is_numeric($mt) and isset($lg_tt[$tab_col[$i]]) and is_numeric($lg_tt[$tab_col[$i]]))$lg_tt[$tab_col[$i]] += $mt;
					else $lg_tt[$tab_col[$i]] = $mt;
					echo "<td>$mt</td>";
				}
				echo "<td$style>".chiffre($total)."</td></tr>";
				$n = $serv = "";
				$totaux += $total;
			}
			$ttt=0;
			echo "<tr><td></td><td></td><td></td>";
			foreach($lg_total as $cle=>$val){
				echo "<td>".chiffre($val)."</td>";$ttt += $val;
			}
			echo "<td style='font-weight:bold;text-align:right;'>".chiffre($ttt)."</td></tr>";
			echo "</table>";
		}
		
		$std = " style='border:none'";
		echo "
		<table border='0' style='width:100%;font-weight:bold;font-style:italic;text-align:center'>
		<tr><td $std>Total général: ".chiffre($totaux)." Fc</td><td style='width:500px;border:none'></td><td$std>Lubumbashi le ".date("d-M-Y")."</td></tr>
		<tr><td$std><!-- Cumul: ".chiffre(0)." Fc --></td><td$std></td><td$std></td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std><!-- LE C.B. RECOUVREMENTS --></td><td$std></td><td$std>LE C.B. RECOUVREMENTS</td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std><!-- Francine PEMBA MAKUTU --></td><td$std></td><td$std>Olivier MPASI NGOMA</td></tr>
            <tr><td$std><!-- Baudouin MANGA LUKUSU --></td><td$std></td><td$std></td></tr>
		</table>";
		?>
	</div>
	