		
	<p style="
		position: absolute;
		top: -70px;
		right: 0;
		z-index: 400;
	">
		<button type="button" class="btn bg-purple btn-flat margin" onclick='document.getElementById("div_ordonnancement").style.display="block";document.getElementById("iframe-rapport").style.display="none";'>Details</button>
		
		<button type="button" class="btn bg-maroon btn-flat margin" style="background-color: #3d9970 !important;" onclick='document.getElementById("div_ordonnancement").style.display="none";document.getElementById("iframe-rapport").style.display="block";'>Rapport</button>
	</p>
	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
	<p id="id_lien_rapp"><?php echo $_ville." ".$_com." ".$_service." Rôle des ordonnancements du <b style='color:blue'>$dt_ord1</b> au <b style='color:blue'>$dt_ord2</b> "; ?>
	Ici, vous avez la liste de notes non payées et ayant déjà excéder 8 jours dépuis leurs dépôt.<a href="#x" onclick="window.open('recv/reste_rec.php')">Rest à recouvrer</a>
	<!--form method="POST" action="tableau_relance.php" target="chdo">
	<input title="saisissez les numéro de note à relancer" placeholder="0000000,0000000,000000" name="liste_num_note"/>
	<button type="submit" name="bt_relancer_parnote" />Relancer Par note</button></form--></p>
	<div id="div_extrait_de_role" style='width:100%'  ></div>
	<div id='div_ordonnancement' style='width:100%;display:none' >
		
		<?php
		$year = explode("-",$dt_ord1)[0];$month=explode("-",$dt_ord1)[1]; 
		$limite_mois = $year==date('Y')?date('m'):12;
		
		$STR_DONUT = "";
		$STR_LINES = "";
		$MOIS=array('1'=>'JANVIER','2'=>'FEVRIER','3'=>'MARS','4'=>'AVRIL','5'=>'MAI','6'=>'JUIN','7'=>'JUILLET','8'=>'AOUT','9'=>'SEPTEMBRE','10'=>'OCTOBRE','11'=>'NOVEMBRE','12'=>'DECEMBRE');
		$tab_mnt_mois = array();
		
		for($mois_d = 1; $mois_d<= $limite_mois; $mois_d++ ){
		
		{$req = "SELECT no.id id_ordo ,n_ap.id_noteacte ,nac.montant_acte ,ser.service ,ac.acte ,ac.art_bud ,nac.freq ,no.date_ordo ,no.date_depot ,ass.nom_assujetti ,ass.id id_ass,ass.adresse_assujetti ,no.num_note ,no.num_bap ,no.montant_bap ,no.note_to ,no.date_save date_enrg ,nac.ajouter_le date_enreg ,co.commune 
		,extract(YEAR FROM no.$dt_save) annee, extract(MONTH FROM no.$dt_save) mois
		
		FROM t_note_actes nac 
		INNER JOIN t_acte ac ON nac.id_acte = ac.id 
		INNER JOIN t_service ser ON ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON nac.id_Note = no.id 
		INNER JOIN t_assujetti ass ON no.id_assujetti = ass.id 
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
		
		LEFT JOIN t_note_actes_payer n_ap ON n_ap.id_noteacte = nac.id  
		WHERE  no.is_deleted=0 and 
		n_ap.id is null and 
		-- ( no.$dt_save BETWEEN '$dt_ord1' AND '$dt_ord2') 
		extract(MONTH FROM no.$dt_save) = $mois_d and extract(YEAR FROM no.$dt_save) = $year
		$_WHERE
		order by  annee, mois,
		co.commune,ser.service, ass.id
";}
		// echo $req;-- co.ordre ASC,and ADDDATE( date_depot, 8 ) < now()
		
		if($pdo_result = Chado::$conx->query($req)){
			$rows = array();
			$th = "<th>N°</th>
					
					<th>Ass.</th><th>Date Ord.</th><th>Date Dept.</th><th>N°NP.</th><th>Mt.ac</th><th>Acte</th><th>Serv.</th><th>N°Bap</th><th>Mt.Bap</th>
					<th>Obs.</th>
					";//<th>Serv.Ord.</th><th>Fréq.</th><th>Art.</th>
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=$mttt=0;
			$no_display = "style='display:none'";
			while($r){
				$moiss = isset($MOIS[$r['mois']])?$MOIS[$r['mois']]:$r['mois'];
				echo "<div  class='div-mois'>";
				$mnt=$mnt_c=$tt_mois = 0;
				echo "<table $no_display  class='div-mois-head'><caption class='caption-mois'>Les Ordonnancements du mois de: $moiss  </caption></table>";
				echo "<div  $no_display  class='div-mois-body'>";
				do{
					$i=0;
					echo "
					<table style='width:100%'>
						<thead $no_display ><tr><th colspan='30' ><h3 class='caption-com' >﻿Rôle de: $r[commune]</h3> </th></tr></thead>
						<tbody $no_display ><tr>$th</tr>";
						$mnt=0;
						do{$i++;
							$mnt += $r["montant_acte"];
							$as = "";
							$a = "";
							$_a = "";
							echo "<tr><td>$i</td>
							
							<td>$as$r[nom_assujetti]$_a</td><td>$r[date_ordo]</td><td>$r[date_depot]</td><td>$a$r[num_note]$_a</td><td>$r[montant_acte]</td><td>$r[acte]</td>
							<td>$r[service]</td>
							<td>$r[num_bap]</td><td>$r[montant_bap]</td>
							<td>".($r["note_to"]?"OO":"")." $r[adresse_assujetti]</td>					
							</tr>"; 
							//<td>$r[art_bud]</td><td>$r[frequence_acte]</td> $r["montant"] = chiffre($r["montant"]);//<td>$r[service_1]</td>
							$ex = $r;
						}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["commune"]==$r["commune"]);
						echo 
						"</tbody>
							<tfoot>
							<tr class='lg_total'><td colspan='11' style='background: #5f9e9e;' >TOTAL ﻿Rôle de: $ex[commune] : <u style='25px'>".chiffre($mnt)."</u></td></tr>
						</tfoot>
					</table>";
					$tt_mois += $mnt;
				}while( $ex["mois"]==$r["mois"]);
				$mttt += $tt_mois;
				$tab_mnt_mois[] = $tt_mois;
				echo "</div>";// fin class='div-mois-body'
				
				echo "<div class='div-mois-foot'>Total Ordonnancements <strong>$moiss</strong> <b>".chiffre($tt_mois)."</b></div>";
				
				echo "</div>";// fin class='div-mois'
				if(!$r)break;
				
			}
			
		}
		}
		if($mttt)echo"<h3>Total Général: ".chiffre($mttt)."</h3>";
		if($i==0)echo "<table style='width:1200px'>
		<tr>$th</tr>
		<tr><td colspan='30'>Aucune note apurée sur cette période</td></tr>
		</table>";
		
		$STR_DONUT = "";
		$STR_LINES = "";
		$STR_LINES = "&amp;CENTRES=".implode(',',$tab_mnt_mois);
		$src = $LINE_GRAPH.$STR_DONUT.$STR_LINES;
		
		?>
	</div>
	<?php 
	include("_suite_rapport.php");
	?>
	
	