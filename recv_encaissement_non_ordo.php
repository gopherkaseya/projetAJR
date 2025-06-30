	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
	<!--p id="id_lien_rapp"><a onClick="askServeur('liste_objets_ajax.php?page=rapp_recette&rap=ord','div_ordonnancement');" href='#xx'>Les Ordonnancées</a>
	<a onClick="askServeur('liste_objets_ajax.php?page=rapp_recette&rap=rec','div_ordonnancement');" href='#xx'>Les Recouvrées</a></p-->
				
	<div id='div_ordonnancement' style='width:100%' >
		
	<p id="id_lien_rapp"><?php echo $_ville." ".$_com." ".$_service." Les encaissements non ordonnancés sur la période de <b style='color:blue'>$dt_ord1</b> au <b style='color:blue'>$dt_ord2</b> "; ?>
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
		{$req = "SELECT nacp.id ,nacp.id_noteacte 
		
		,rlv.valide ,bq.nom_banque ,rlv.date_paiement ,us.nom ,us.role ,us.titre ,nacp.montant_payer ,nacp.paie_exp_num_note ,ser.service service_2,ac.acte acte_1,ac.art_bud art_bud_1,ass.nom_assujetti nom_assujetti_1 ,nacp.paie_exp_date_ordo  
			
		FROM t_note_actes_payer nacp 
		INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
		INNER JOIN t_banque bq ON  rlv.id_banque = bq.id 
		INNER JOIN t_user us ON rlv.id_user = us.id 
		Left JOIN t_acte ac ON nacp.paie_exp_id_acte = ac.id 
		Left JOIN t_service ser ON ac.acte_id_service = ser.id 
		Left JOIN t_assujetti ass ON nacp.paie_exp_id_assujetti = ass.id 
		WHERE (nacp.id_noteacte < 1 or nacp.id_noteacte='' or nacp.id_noteacte is null) and rlv.is_deleted=0 and nacp.is_deleted=0
		";}
		// echo $req;
		if($pdo_result = Chado::$conx->query($req)){
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
	