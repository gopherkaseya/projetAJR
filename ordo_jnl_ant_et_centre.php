	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
		td{border: 1px solid #000;}
		p, pre, table, form {
			margin: 10px 0px;
		}
		table tr th {
			text-align: center;
		}
		th {
			background-color: #F5F5F5;
		}
		th, td {
			border: 1px solid #EEE;
			padding: 4px 12px;
			vertical-align: top;
		}
		th {
			text-align: left;
		}
		tr:nth-of-type(2n) {
			background-color: #FFF;
		}
		table {
			border-collapse: collapse;
			border-spacing: 0px;
		}
		h5, h6, pre, table, input, textarea, code {
			font-size: 1em;
		}
		html {
			color: #444;
		}
		html, h4, h5, h6 {
			font-size: 13px;
		}
		html {
			line-height: 1.54;
		}
		html, input, textarea {
			font-family: arial,sans-serif;
		}
	</style>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php 
		echo "JOURNAL DES JOURNAUX DES ORDONNANCEMENTS".($check_to=="TO"?" D'OFFICES":"")."<b>".($dt_save=="date_save"?" ENREGISTRES":" DEPOSES")."</b>"." DU <b style='color:blue'>$dt_ord1</b> AU <b style='color:blue'>$dt_ord2</b>  par Secteur pour tout le RESSORT/LUBUMBASHI.";
		function afficher_ligne($pdo_result,$th){
			$rows = array();
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=$mntt=0;
			
			while($r){$i=0;
				// echo "<h3>$r[commune]</h3>";text-align:center;
				echo "<table style='width:100%;margin-bottom:30px'>";
				$mnt=0;
				// echo "<caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>Les Ordonnancements de: ".($r["id_ville"]!=1?"$r[ville]":"$r[commune]")." </caption>";
				// do{
					// do{
						$mnst=0;
						echo "<tr style='font-weight:bold;font-style:italic;backgroundd:#abc'><th></th><th colspan='11' style='text-align:center;'>Ressort Ordo. - $r[service]</th></tr>$th";
						do{	$i++;					
							$mnt += $r["montant_acte"];$mnst += $r["montant_acte"];
							echo "<tr><td>$i</td>
							
							<td>$r[nom_assujetti]</td><td>$r[nif]</td>"./* "<td>$r[service]</td>". */"<td>$r[acte]</td><td>$r[freq]</td><td>$r[num_note]</td><td>$r[date_ordo]|$r[date_depot]</td><td>$r[art_bud]</td><td>$r[montant_acte]</td>
							<td>".($r["note_to"]?"OO":"")." $r[observation]</td><td>$r[montant_bap]</td>					
							</tr>"; 
							// $r["montant"] = chiffre($r["montant"]);
							$ex = $r;
						}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and  /*$ex["id_ville"]==$r["id_ville"] and $ex["id_commune"]==$r["id_commune"] and */ $ex["id_service"]==$r["id_service"]);
					
						$mntt += $mnst;
					// }while(($ex["id_ville"]==$r["id_ville"] and $ex["id_commune"]==$r["id_commune"]));
					echo "<tr><th></th><th colspan='30' style='text-align:center'>Ressort Ordo. - $ex[service] Total: ".chiffre($mnt)." </th></tr>";
				// }while($ex["id_ville"]==$r["id_ville"] and (0 or $r["id_ville"]!=1));
				echo "</table>";
				if(!$r)break;
			}
			return array($i,$mntt);
		}
		/* SELECT n_act.montant_acte ,ser.service ,act.acte ,act.art_bud ,n_act.frequence_acte ,n_ord.date_ordo ,n_ord.date_depot ,ass.nom_assujetti ,ass.adresse_assujetti ,n_ord.num_note ,n_ord.num_bap ,ifnull('',concat(n_ord.num_bap,' ',n_ord.montant_bap))montant_bap ,n_ord.observation ,n_ord.date_enrg ,n_act.date_enreg ,com.commune ,vll.ville ,ser_1.service */
		{$req = " 
		SELECT ac.acte ,ser.service ,ac.art_bud ,ass.nif ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte,nac.freq,nac.ajouter_le,ac.acte_id_service id_service,no.pr_cpt_de_id_com id_commune
		
		FROM t_note_actes nac
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id
		where nac.is_deleted=0 and  no.is_deleted=0 and ( no.$dt_save BETWEEN '$dt_ord1' AND '$dt_ord2') 
		$_WHERE 
		";
		}
		
		$th = "<th>N°</th><th>Ass.</th><th>NIF</th><th>Acte</th><th>Fréq.</th><th>N°NP.</th><th>Date Ord/Date Dp.</th><th>Art.Bud</th><th>Mnt.(Fc)</th><th>Obs.</th><th>Mt.Bap(Fc)</th>";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = " order by ser.service, ser.id,no.num_note ";//echo $req.$whre_order;
		if($pdo_result = Chado::$conx->query($req.$whre_order)){//
			$rep = afficher_ligne($pdo_result,$th);
			if($rep[0]==0)echo "
			<table style='width:100%'>
				<tr>$th</tr>
				<tr><td colspan='30'>Aucun ordonnancement des centres</td></tr>
			</table>";
			$totaux += $rep[1];
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
	