	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
		td{border: 1px solid #000;}
		th, td {
			border: 1px solid #000;
			padding: 4px 12px;
			vertical-align: top;
		}
		tr {
			background-color: #EDEEF2;
		}
		tr:nth-of-type(2n) {
			background-color: #FFF;
		}
		trj h:nth-of-type(2n) {
			background-color: #FFF;
		}
		table {
			border-collapse: collapse;
			border-spacing: 0px;
		}
		h5, h6, pre, table, input, textarea, code {
			font-size: 1em;
		}
		.lg_total,tr.lg_total:nth-of-type(2n) {
			
			background-color: #D142F5;
			font-weight:bold;font-style:italic;text-align:center;
		}
		.montant{text-align:right}
	</style>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php 
		echo $_ville." ".$_com." ".$_service."JOURNAL DES JOURNAUX DES ORDONNANCEMENTS".($check_to=="TO"?" D'OFFICES":"").
		"<b>".($dt_save=="date_save"?" ENREGISTRES":" DEPOSES")."</b>"." DU <b style='color:blue'>$dt_ord1</b> AU <b style='color:blue'>$dt_ord2</b> ";
		function afficher_ligne($pdo_result,$th){
			$rows = array();
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=$mntt=0;
			
			while($r){$i=0;
				// echo "<h3>$r[commune]</h3>";text-align:center;
				echo "<table style='width:1300px;margin-bottom:30px'>";
				$mnt=0;
				echo "<caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>Les Ordonnancements de: ".("$r[commune]")." </caption>
				</table>";
				// do{
					do{$mnst=0;
						echo "<table><tr style='font-weight:bold;font-style:italic;backgroundd:#abc'><th colspan='11' style='text-align:center;'>Ordonnancements de: $r[service]</th></tr><tr>$th</tr>";
						do{	$i++;					
							$mnt += $r["montant_acte"];$mnst += $r["montant_acte"];
							echo "<tr><td>$i</td>
							<td>$r[nom_assujetti] </td><td>$r[adresse_assujetti] </td><td>$r[nif]</td><td>$r[acte]</td><td>$r[freq]</td><td>$r[num_note]</td><td>$r[date_ordo]|$r[date_depot]</td><td>$r[art_bud]</td><td class='montant'>".chiffre($r["montant_acte"])."</td>
							<td>".($r["note_to"]?"OO":"")." $r[observation]</td><td class='montant'>".chiffre($r["montant_bap"])."</td>					
							</tr>";  /* */
							// $r["montant"] = chiffre($r["montant"]);
							$ex = $r;
						}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["id_commune"]==$r["id_commune"] and $ex["id_service"]==$r["id_service"]);
						echo "<tr class='lg_total'>
							<td colspan='11' >TOTAL Ordonnancements $ex[service] : <u style='25px'>".chiffre($mnst)."</u></td></tr></table>";
						$mntt += $mnst;
					}while( isset($r["id_commune"]) and ($ex["id_commune"]==$r["id_commune"]));
						/* or($r["ville"]!="Lubumbashi") */
					echo "<table><tr><th colspan='5'></th><th colspan='30'>Ordonnancements ".("$ex[commune]")." Total:".chiffre($mnt)." : Total Principal</th></tr>";
				// }while($ex["id_ville"]==$r["id_ville"] and (0 or $r["id_ville"]!=1));
				echo "</table>";
				if(!$r)break;
			}
			return array($i,$mntt);
		}
		{$req = "SELECT ac.acte ,ser.service ,ac.art_bud ,ass.nif ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte,nac.freq,nac.ajouter_le,ac.acte_id_service id_service,no.pr_cpt_de_id_com id_commune
		,extract(YEAR FROM no.$dt_save) annee, extract(MONTH FROM no.$dt_save) mois
		FROM t_note_actes nac
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id
		where nac.is_deleted=0 and  no.is_deleted=0 
		-- and ( no.date_save BETWEEN '$dt_ord1' AND '$dt_ord2') 
		and ( no.$dt_save BETWEEN '$dt_ord1' AND '$dt_ord2') 
		$_WHERE ";
		}
		$th = "<th>N°</th><th>Ass.</th><th>Adresse</th><th>NIF</th><th>Acte</th><th>Fréq.</th><th>N°NP.</th><th style='width:150px'>Date Ord/Date Dp.</th><th>Art.Bud</th><th>Mnt.(Fc)</th><th>Obs.</th><th>Mt.Bap(Fc)</th>";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = " and co.commune='Centre' order by ser.service, ser.id ,no.num_note ASC";
		// echo $req.$whre_order;
		if($pdo_result = Chado::$conx->query($req.$whre_order)){
			$rep = afficher_ligne($pdo_result,$th);
			if($rep[0]==0)echo "
			<table style='width:1300px'>
				<tr>$th</tr>
				<tr><td colspan='30'>Aucun ordonnancement des centres</td></tr>
			</table>";
			$totaux += $rep[1];
		}
		// ordonnancement de la ville de lubumbashi et uniquement dans les antenne
		$whre_order = " and co.commune not in ('Centre','CB.ORDO','CB.ET - CONT') order by co.id,ser.service,ser.id,no.num_note ASC";
		// echo $req.$whre_order;
		if($pdo_result = Chado::$conx->query($req.$whre_order)){
			$rep = afficher_ligne($pdo_result,$th);
			if($rep[0]==0)echo "
			<table style='width:1300px'>
				<tr>$th</tr>
				<tr><td colspan='30'>Aucun ordonnancement pour des antennes</td></tr>
			</table>";
			$totaux += $rep[1];
		}		
		// tous les ordonnancement confondus hors mis ceux de la ville de Lubumbashi 
		$whre_order = " and co.commune in ('CB.ORDO','CB.ET - CONT')order by ser.service,ser.id,no.num_note ASC ";
		if($pdo_result = Chado::$conx->query($req.$whre_order)){ //echo $req.$whre_order;
			$rep = afficher_ligne($pdo_result,$th);
			if($rep[0]==0)echo "
			<table style='width:1300px'>
				<tr>$th</tr>
				<tr><td colspan='30'>Aucun ordonnancement pour des Bureaux</td></tr>
			</table>";
			$totaux += $rep[1];
		}
			
		$std = " style='border:none'";
		echo "
		<table border='0' style='width:1300px;font-weight:bold;font-style:italic;text-align:center'>
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
	