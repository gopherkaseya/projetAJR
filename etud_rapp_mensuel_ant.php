	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php
		{
			
		$req = "select commune,service,acte,
		sum(case when date_ordo between '$dt_ord1'and'$dt_ord2' then  montant_acte else 0 end) ordo,
		sum(case when ((date_ordo between '$dt_ord1'and'$dt_ord2') and (date_paiement between '$dt_ord1'and'$dt_ord2')) then  montant_payer else 0 end) recv,
		sum(montant_payer) nivel
		from t_acte ac 
		inner join t_service ser on ser.id = ac.acte_id_service 
		
		inner join t_note_actes nac on nac.id_acte = ac.id
		inner join t_note no on no.id = nac.id_note
		inner join t_commune co on co.id = no.pr_cpt_de_id_com
		
		left join t_note_actes_payer nacp on nacp.id_noteacte = nac.id
		left join t_releve rlv on rlv.id = nacp.id_releve
		where (no.date_depot BETWEEN '$dt_ord1' and '$dt_ord2') or (date_paiement between '$dt_ord1' and '$dt_ord2')
		group by co.id,ser.id -- ,ac.id 
		order by commune,service
		";
		}
		
		$th = "<tr><th>N°</th><th>Service</th><th>Ordonnancée</th><th>Recouvrée</th><th>Nivelée</th><th>Rest à Rec.</th></tr>";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = "";
		$tord = $trec = $tniv = $trst = 0;
		// echo $req.$whre_order;
		if($pdo_result = Chado::$conx->query($req.$whre_order)){
			$tab = array();$tab_col = array();
			$i=$mntt=0;
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			echo "<table style='width:800px' >";
			while($r){$i=0;
				$ord = $rec = $niv = $rst = 0;
				echo "
				<tr><td colspan='6'><h3>Rapport Mensuel de l'antenne $r[commune] - (Mois de $n_mois)</h3></td></tr>$th";
				do{$i++;
					$ord += $r["ordo"];
					$rec += $r["recv"];
					$niv += $r["nivel"];
					$rst += $rest = $r["ordo"]-$r["recv"];
					echo "<tr><td>$i</td>
						<td>$r[service]</td><td>$r[ordo]</td><td>$r[recv]</td><td>$r[nivel]</td><td>$rest</td>					
					</tr>";
					$ex = $r;
				}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["commune"]==$r["commune"]);
				echo "<tr><th></th>
						<th>Total</th><th>".chiffre($ord)."</th><th>".chiffre($rec)."</th><th>".chiffre($niv)."</th><th>".chiffre($rst)."</th>					
					</tr><tr><td colspan='6'></td></tr>";
				$tord += $ord;
				$trec += $rec;
				$tniv += $niv;
				$trst += $rst;
				if(!$r)break;
			}			
		}
		echo "</table><table>$th<tr><td></td>
				<td>Total</td><td>".chiffre($tord)."</td><td>".chiffre($trec)."</td><td>".chiffre($tniv)."</td><td>".chiffre($trst)."</td>					
			</tr></table>";
		$std = " style='border:none'";
		echo "<br/><br/><br/>";
		?>
	</div>
	