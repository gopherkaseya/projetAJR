	<?php $year = 2016;$mois=array("01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin","07"=>"Jouillet","08"=>"Août","09"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Décembre"); ?>
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
		tr td,th{border:solid 1px #000;}
	</style>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php
		
		{
			$s=" 00:00:00";
			$s="";
			$req = "
		
		SELECT ac.acte ,ser.service ,ac.art_bud ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,nac.montant_acte,nac.freq,nac.ajouter_le,ac.acte_id_service id_service,no.pr_cpt_de_id_com id_commune
		
		FROM t_note_actes nac
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id
		WHERE nac.is_deleted=0 and  no.is_deleted=0	and ".($dt_save=="date_save"?"date(no.date_save)":" no.date_depot")." BETWEEN '$dt_ord1' and '$dt_ord2'
		".($check_to=="TO"?" and no.note_to=1 ":"")."
		order by ser.service,ac.acte ";
		}
		// -- and (extract(YEAR FROM no.$dt_save)='".(explode("-",$dt_ord1)[0])."' and extract(MONTH FROM no.$dt_save)='".($moi = explode("-",$dt_ord1)[1])."') 
		// echo $req;
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
					$tab[$cl_lg][$cl_col] += $r["montant_acte"];
				else {
					$tab[$cl_lg]["service"] = $r["service"];
					$tab[$cl_lg][$cl_col] = $r["montant_acte"];
					
				}
				if(!isset($tab_col[$cl_col]))$tab_col[$cl_col]=0;
				$tab_col[$cl_col] += $r["montant_acte"];
			}
			$lg_total = $tab_col;
			$tab_col = array_keys($tab_col);
			// echo "<pre>";print_r($tab);echo"</pre>";
			//
			$totaux = 0;
			echo "<table style='width:100%;font-weight:bold;font-style:italic;text-align:center'><caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>RAPPORT DES ORDONNANCEMENTS".($check_to=="TO"?" D'OFFICES":"")."<b>".($dt_save=="date_save"?" ENREGISTRES":" DEPOSES")."</b>"." DU $dt_ord1 AU $dt_ord2</caption>";
			$thi = "th style='font-style:italic;' ";
			echo "<tr style='font-weight:bold;backgroundd:#abc'>
			<$thi>N°</th><$thi>SECTEURS</th><$thi>ACTES GEN</th>";
			for($i=0;$i!=count($tab_col);$i++){
				echo "<$thi>$tab_col[$i]</th>";
			}
			echo "<$thi>TOAL</th></tr>";
			//
			$j = 1;$ex_serv=$n="";$lg_tt = array();
			foreach($tab as $cle=>$ligne){
				$total=0;
				if($ligne['service']!=$ex_serv){
					/* if($ex_serv!=""){
						$stotal=0;
						echo "<tr><td></td><td style='font-weight:bold;font-style:italic;font-size:12px' >s/total</td><td></td>";
						for($i=0;$i!=count($tab_col);$i++){
							$mt = $lg_tt[$tab_col[$i]];
							$stotal += $mt;
							echo "<td>$mt</td>";
						}
						echo "<td>$stotal</td></tr>";
					} */
					$n=$j++;
					$serv = $ligne['service'];
					// $lg_tt = array();
				}
				$ex_serv = $ligne['service'];
				echo "<tr><td>$n</td><td style='font-weight:bold;font-style:italic;font-size:12px' >$serv</td><td>$cle</td>";
				for($i=0;$i!=count($tab_col);$i++){
					$mt = "&nbsp;";
					if(isset($ligne[$tab_col[$i]])){
						$mt = $ligne[$tab_col[$i]];
						$total += $mt;
					}
					// if(isset($lg_tt[$tab_col[$i]]))$lg_tt[$tab_col[$i]] += $mt;
					// else $lg_tt[$tab_col[$i]] = $mt;
					echo "<td>$mt</td>";
				}
				echo "<td>$total</td></tr>";
				$n = $serv = "";
				$totaux += $total;
			}
			$ttt=0;
			echo "<tr><td></td><td></td><td></td>";
			foreach($lg_total as $cle=>$val){
				echo "<td>".chiffre($val)."</td>";$ttt += $val;
			}
			echo "<td style='font-weight:bold;'>".chiffre($ttt)."</td></tr>";
			echo "</table>";
		}
		
		$std = " style='border:none'";
		echo "
		<table border='0' style='width:100%;font-weight:bold;font-style:italic;text-align:center'>
		<tr><td $std>Total général: ".chiffre($totaux)." Fc</td><td style='width:500px;border:none'></td><td$std>Lubumbashi le ".date("d-M-Y")."</td></tr>
		<tr><td$std>Cumul: ".chiffre(0)." Fc</td><td$std></td><td$std></td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std>LE C.B. ORDO DOM</td><td$std></td><td$std>LE C.B. CONTRÔLE </td>
</tr>
		<tr><td$std>Francine PEMBA MAKUTU</td><td$std></td><td$std>SIX VUNINGA A KIRIZA</td></tr>
           <tr><td$std></td><td$std></td><td$std></td></tr>
           <tr><td$std>LE C.B. ORDO ADM</td><td$std></td><td$std>
           <tr><td$std></td><td$std></td><td$std>
           <tr><td$std>Baudouin  MANGA LUKUSU</td><td$std>
		</table>";
		?>
	</div>
	