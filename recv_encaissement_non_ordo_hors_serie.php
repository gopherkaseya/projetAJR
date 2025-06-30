	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
				
	<div id='div_ordonnancement' style='width:100%' >
		<p id="id_lien_rapp"> Les encaissements ne faisant parti d'aucun carnet du RESSORT </p> 
		
		<?php
		$t_ch = array("serv_ordo","num_note_payee","adresse_assujetti","id","ville_ordo","com_ordo","observation","frequence_acte","adresse_banque","telephone_banque");
		$ar_rec = array("montant_paye","date_paiement","nom_banque");
		$th_rec = "<th>Mont.Payé</th><th>Date Paie</th><th>Banque</th>";
		$th = "<tr><th>N°</th><th>Date Ordo.</th><th>Date Dpt.</th><th>N.P.</th><th>N°Bap.</th><th>M.Bap.</th><th>Assujetti</th><th>Montant</th><th>Art.B.</th><th>Serv.Gén.</th>";//<th>Serv.Ord.</th>
		
		$req = "";
		if(isset($_GET["rap"])){
			switch($_GET["rap"]){
				case "encaissement":
					$req 	= " where montant_paye is not null ";
					$th    .= $th.$th_rec; 
					$t_ch 	= array_merge($th_ch,$ar_rec);
				break;
				case "notePayees":
					$req 	= " where montant_paye is not null ";
					$th    .= $th.$th_rec; 
					$t_ch 	= array_merge($th_ch,$ar_rec);
				break;
				case "rec":$req = " where num_note_payee is not null ";break;
				case "exp":$req = " where no.num_note is null ";break;
			}
		}
		$th .= '</tr>';
		{$req = "SELECT n_ap.id ,n_ap.id_noteacte 
		
		,rlv.valide ,bq.nom_banque ,rlv.date_paiement ,us.nom ,us.role ,us.titre ,n_ap.montant_payer ,n_ap.paie_exp_num_note ,ser_2.service service_2,act_1.acte acte_1,act_1.art_bud art_bud_1,ass_1.nom_assujetti nom_assujetti_1 ,n_ap.paie_exp_date_ordo  
			
		FROM t_note_actes_payer n_ap 
		INNER JOIN t_releve rlv ON n_ap.id_releve = rlv.id 
		INNER JOIN t_banque bq ON rlv.id_banque = bq.id 
		INNER JOIN t_user us ON rlv.id_user = us.id 
		INNER JOIN t_acte act_1 ON n_ap.paie_exp_id_acte = act_1.id 
		INNER JOIN t_service ser_2 ON act_1.acte_id_service = ser_2.id 
		INNER JOIN t_assujetti ass_1 ON n_ap.paie_exp_id_assujetti = ass_1.id 
		LEFT join t_carnet c on n_ap.paie_exp_num_note between c.num_debut and c.num_debut+50
		WHERE  rlv.is_deleted=0 and n_ap.is_deleted=0 and (n_ap.id_noteacte < 1 or n_ap.id_noteacte='') and c.num_debut is null 
		
";}
		
		if($pdo_result = Chado::$conx->query($req)){//echo $req;
			$rows = array();
			$th = "<th>N°</th>
					
					<th>Banque</th><th>Payée Le:</th><th>Mt.P.</th><th>Act.P.</th><th>Art.B.P.</th><th>Serv.P.</th><th>N°NP.P.</th><th>Ass.P.</th><th>Date.Ord.P.</th>";
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=0;
			while($r){$i=0;
				echo "<h3>$r[service_2]</h3>
				<table style='width:1200px'>$th";
				$mnt=0;
				do{$i++;
					$mnt += $r["montant_payer"];
					echo "<tr><td>$i</td>
					
					<td>$r[nom_banque]</td><td>$r[date_paiement]</td><td>$r[montant_payer]</td><td>$r[acte_1]</td><td>$r[art_bud_1]</td><td>$r[service_2]</td><td>$r[paie_exp_num_note]</td><td>$r[nom_assujetti_1]</td>
					<td>$r[paie_exp_date_ordo]</td>
					
					</tr>"; 
					// $r["montant"] = chiffre($r["montant"]);
					$ex = $r;
				}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["service_2"]==$r["service_2"]);
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
	