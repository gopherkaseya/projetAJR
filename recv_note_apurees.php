	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>			
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php
		
		echo $_ville." ".$_com." ".$_service." Encaissements des Ordos. du <b style='color:blue'>$dt_ord1</b> au <b style='color:blue'>$dt_ord2</b> ";
		{$req = "SELECT nacp.id, ac.acte ,ser.service ,ac.art_bud ,ass.nif,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,nac.montant_acte ,nac.freq ,bq.nom_banque ,rlv.date_paiement ,nacp.montant_payer,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte,nacp.paie_exp_date_ordo
		FROM t_note_actes_payer nacp 
		INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
		INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
		INNER JOIN t_banque bq ON  rlv.id_banque = bq.id 
		WHERE rlv.is_deleted=0 and nacp.is_deleted=0 and nac.is_deleted=0 and no.is_deleted=0 and  
		( rlv.date_paiement BETWEEN '$dt_ord1' AND '$dt_ord2') 
		$_WHERE
		order by -- co.ordre ASC,
		co.commune
		";}
		// echo $req;
		if($pdo_result = Chado::$conx->query($req)){
			$rows = array();
			$th = "<th>N°</th>
					
					<th>Banque</th><th>Payée Le:</th>
					<th>Mt.Act</th><th>Acte</th><th>Serv.</th><th>Date Ord.</th><th>Ass.</th><th>NIF.</th><th>N°NP.</th><th>N°Bap</th><th>Mt.Bap</th>
					
					";
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=0;
			while($r){$i=0;
				echo "<h3>$r[commune]</h3>
				<table style='width:100%'>$th";
				$mnt=0;
				do{$i++;
					$mnt += $r["montant_payer"];
					echo "<tr><td>$i</td>
					<td>$r[nom_banque]</td><td>$r[date_paiement]</td>
					<td style='text-align:right'>$r[montant_acte]</td><td>$r[acte]</td>
					<td>$r[service]</td>
					<td title='déposée le $r[date_depot]' >$r[date_ordo]</td><td>$r[nom_assujetti]</td><td>$r[nif]</td><td>$r[num_note]</td><td>$r[num_bap]</td><td style='text-align:right'>$r[montant_bap]</td>					
					</tr>"; 
					// $r["montant"] = chiffre($r["montant"]);
					$ex = $r;
				}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["commune"]==$r["commune"]);
				echo "<tr><th colspan='4'></th><th colspan='30'>Encaissements $ex[commune] Total: ".chiffre($mnt)."  </th></tr></table>";
				if(!$r)break;
			}
			if($i==0)echo "<table style='width:100%'>
			<tr>$th</tr>
			<tr><td colspan='30'>Aucune note apurée sur cette période</td></tr>
			</table>";
		}
		?>
	</div>
	