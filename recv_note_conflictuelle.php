	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
		.td_conf{background-color: #cb1267;color:#fff;}
	</style>
	<p id="id_lien_rapp">
	Ici, vous avez les notes ayant subi un échec d'apurement automatique pour cause d'incohérence soit sur:<br/><ul><li>l'assujetti</li><li>l'acte ou service générateur</li><li>la date d'ordo</li></ul>
	</p>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<br/>
		<?php
		{$req = "SELECT n_ap.id ,n_ap.id_noteacte id_noteacte,n_act.montant_acte ,ser.service ,act.acte ,act.art_bud ,n_act.freq frequence_acte ,n_ord.date_ordo ,n_ord.date_depot ,ass.nom_assujetti ,ass.adresse_assujetti ,n_ord.num_note ,n_ord.num_bap ,n_ord.montant_bap ,n_ord.note_to observation ,n_ord.date_save date_enrg ,n_act.ajouter_le date_enreg ,co.commune ,n_ap.id_releve id_releve,rlv.valide ,bq.nom_banque ,rlv.date_paiement ,us.nom ,us.role ,us.titre ,n_ap.montant_payer ,n_ap.paie_exp_num_note paie_exp_num_note,n_ap.paie_exp_id_acte ,ser_2.service service_2,act_1.acte acte_1,act_1.art_bud art_bud_1,ass_1.nom_assujetti nom_assujetti_1 ,n_ap.paie_exp_date_ordo  
			
		FROM t_note_actes_payer n_ap 
		INNER JOIN t_note n_ord ON n_ap.paie_exp_num_note = n_ord.num_note 
		INNER JOIN t_assujetti ass ON n_ord.id_assujetti = ass.id 
		INNER JOIN t_commune co ON  n_ord.pr_cpt_de_id_com = co.id 
		
		INNER JOIN t_note_actes n_act ON n_act.id_Note = n_ord.id 
		INNER JOIN t_acte act ON n_act.id_acte = act.id 
		INNER JOIN t_service ser ON act.acte_id_service = ser.id 
			
		INNER JOIN t_releve rlv ON n_ap.id_releve = rlv.id 
		INNER JOIN t_banque bq ON rlv.id_banque = bq.id 
		INNER JOIN t_user us ON rlv.id_user = us.id 
		
		Left JOIN t_acte act_1 ON n_ap.paie_exp_id_acte = act_1.id 
		Left JOIN t_service ser_2 ON act_1.acte_id_service = ser_2.id 
		Left JOIN t_assujetti ass_1 ON n_ap.paie_exp_id_assujetti = ass_1.id 
		WHERE (n_ap.id_noteacte='' or n_ap.id_noteacte=0 or n_ap.id_noteacte is null) and (n_ap.paie_exp_id_acte!=n_act.id_acte 
		or n_ap.paie_exp_id_assujetti!=n_ord.id_assujetti or n_ord.date_ordo!=n_ap.paie_exp_date_ordo) and
		 rlv.is_deleted=0 and n_ap.is_deleted=0 and n_act.is_deleted=0 and 
";}
		
		if($pdo_result = Chado::$conx->query($req)){
			$rows = array();
			$th = "<th>N°</th>
					
					<th>Banque</th><th>Payée Le:</th>
					<th>N°NP.</th><th>Mt.Act</th><th>Acte</th>
					<th>Art.B.</th><th>Serv.</th><th>Date Ord.</th>
					<th>Assujetti</th><th>Fréq.</th><th>N°Bap</th><th>Mt.Bap</th><th>Serv.Ord.</th>
					";
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=0;
			$td_cl = " class='td_conf' ";
			while($r){$i=0;
				echo "<h3>$r[commune]</h3>
				<table style='width:1200px'>$th";
				$mnt=0;
				do{$i++;
					$mnt += $r["montant_payer"];
					echo "<tr><td>$i</td>
					
					<td colspan='2' style='text-align:center'>Note payée à </td>
					<td>$r[num_note]</td><td>$r[montant_acte]</td><td $td_cl>$r[acte]</td>
					<td $td_cl>$r[art_bud]</td><td $td_cl>$r[service]</td><td $td_cl>$r[date_ordo]</td>
					
					<td $td_cl>$r[nom_assujetti]</td><td>$r[frequence_acte]</td><td>$r[num_bap]</td><td>$r[montant_bap]</td><td>$r[service_1]</td>					
					</tr>
					
					<tr><td></td><td>$r[nom_banque]</td><td>$r[date_paiement]</td>
					<td>$r[paie_exp_num_note]</td><td>$r[montant_payer]</td><td $td_cl>$r[acte_1]</td>
					<td $td_cl>$r[art_bud_1]</td><td $td_cl>$r[service_2]</td><td $td_cl>$r[paie_exp_date_ordo]</td>
					<td $td_cl>$r[nom_assujetti_1]</td><td colspan='4'></td>
					</tr>"; 
					// $r["montant"] = chiffre($r["montant"]);
					$ex = $r;
				}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["commune"]==$r["commune"]);
				echo "<tr><th colspan='4'></th><th colspan='30'>".chiffre($mnt)." : Total Principal</th></tr></table>";
				if(!$r)break;
			}
			if($i==0)echo "<table style='width:1200px'>
			<tr>$th</tr>
			<tr><td colspan='30'>Aucune note apurée sur cette période</td></tr>
			</table>";
		}
		?>
	</div>
	