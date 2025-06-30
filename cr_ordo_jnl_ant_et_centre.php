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
			
			background-color: #bec2d0;
			/* font-weight:bold; */
			font-style:italic;text-align:center;
		}
		.montant{text-align:right}
		
		.div-mois{}
		
		.div-mois {
			width:1300px;
			background: #f0f8ffc9;
			margin: auto;
			margin-bottom: 4px;
			border:solid 1px;
		}
		.caption-mois{font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2}
		.caption-com {
			font-weight: bold;
			font-size: 16px;
			font-style: italic;
			color: #fff;
			display: block;
			background: black;
			padding: 5px 23px;
		}
		.div-mois table {
			margin: auto;
			margin-bottom: 4px;
			min-width: 90%;
		}
		.lg_total {
			color: #fff;
			font-size: 16px;
			text-align: left;
			cursor: pointer;
		}
		.lg_total td u {
			display: inline-block;
			float: right;
			text-align: right;
		}
		
		.div-mois-head {
			cursor: pointer;
		}
		.div-mois-foot {
			background: chartreuse;
			padding: 5px;
			font-size: 20px;
			cursor: pointer;
			background: #f0fffef2;
		}

		.div-mois-foot b {
			display: inline-block;
			float: right;
		}
		.partie-body {
			padding: 10px;
			background: #3b66b038;
		}

		.partie-foot h1 b{
			display: inline-block;
			float: right;
			text-align: right;
			color: #fff;
		}

		.partie-foot h1 {
			background: #5ba6e8b8;
			margin: 0;
			padding: 9px 12px;
			color: #fff;
		}

		.partie-head h1 {
			text-align: center;
		}

		.partie {
			margin-bottom: 30px;
		}
	</style>
	
	<p style="
		position: absolute;
		top: -70px;
		right: 0;
		z-index: 400;
	">
		<button type="button" class="btn bg-purple btn-flat margin" onclick='document.getElementById("div_ordonnancement").style.display="block";document.getElementById("iframe-rapport").style.display="none";'>Details</button>
		
		<button type="button" class="btn bg-maroon btn-flat margin" style="background-color: #3d9970 !important;" onclick='document.getElementById("div_ordonnancement").style.display="none";document.getElementById("iframe-rapport").style.display="block";'>Rapport</button>
	</p>
				
	<div id='div_ordonnancement' style='width:100%;display:none' >
		
		<?php 	
		
		echo "JOURNAL DES JOURNAUX DES ORDONNANCEMENTS".($check_to=="TO"?" D'OFFICES":"")."<b>".($dt_save=="date_save"?" ENREGISTRES":" DEPOSES")."</b>"." DU <b style='color:blue'>$dt_ord1</b> AU <b style='color:blue'>$dt_ord2</b>  par Secteur pour tout le RESSORT/LUBUMBASHI.";
		
		function afficher_ligne($pdo_result,$th){
			$MOIS=array('1'=>'JANVIER','2'=>'FEVRIER','3'=>'MARS','4'=>'AVRIL','5'=>'MAI','6'=>'JUIN','7'=>'JUILLET','8'=>'AOUT','9'=>'SEPTEMBRE','10'=>'OCTOBRE','11'=>'NOVEMBRE','12'=>'DECEMBRE');
			$rows = $tab_ann = array();
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=$mntt=0;
			$no_display = "style='display:none'";
			
			while($r){$i=0;
				$moiss = isset($MOIS[$r["mois"]])?$MOIS[$r["mois"]]:$r["mois"];
				// echo "<h3>$r[commune]</h3>";text-align:center;
				echo "<div  class='div-mois'>";
				$mnt=$mnt_c=0;
				echo "<table $no_display  class='div-mois-head'><caption class='caption-mois'>Les Ordonnancements du mois de: $moiss  </caption></table>";
				echo "<div  $no_display  class='div-mois-body'>";
				$mnt_mois=0;
				do{
					// echo "<table><tr><th colspan='11' ><caption class='caption-com'>Les Ordonnancements Commune: ".("$r[commune]")." </caption></th></tr></table>";
					// $mnt_c=0;
					// do{
						$mnst=0;
						echo "
						<table>
							<thead $no_display ><tr><th colspan='11' >Ordonnancements Service: $r[service] </th></tr></thead>
							<tbody $no_display ><tr>$th</tr>";
						do{	$i++;					
							// $mnt += $r["montant_acte"];
							$mnst += $r["montant_acte"];
							echo "<tr><td>$i</td>
							<td>$r[nom_assujetti] </td><td>$r[nif]</td><td>$r[acte]</td><td>$r[freq]</td><td>$r[num_note]</td><td>$r[date_ordo]|$r[date_depot]</td><td>$r[art_bud]</td><td class='montant'>".chiffre($r["montant_acte"])."</td>
							<td>".($r["note_to"]?"OO":"")." $r[observation]</td><td class='montant'>".chiffre($r["montant_bap"])."</td>					
							</tr>"; 
							
							$ex = $r;
							$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
							if($r){
								// $same_com = $ex["id_commune"]==$r["id_commune"];
								$same_ser = $ex["id_service"]==$r["id_service"];
								$same_mon = /* $ex["annee"]==$r["annee"] and  */$ex["mois"]==$r["mois"];
							}
							else {$same_mon = false;}
						}while($r and $same_ser and $same_mon);
						echo "
							</tbody>
							<tfoot>
								<tr class='lg_total'><td colspan='11' >TOTAL Ordonnancements Service: $ex[service] : <u style='25px'>".chiffre($mnst)."</u></td></tr>
							</tfoot>
						</table>";
						$mnt_mois += $mnst;
					// }while(($same_com and $same_mon));
					// echo "<table><tr><th colspan='30'>Ordonnancements Commune: ".("$ex[commune]")." Total:".chiffre($mnt_c)." : Total Principal</th></tr></table>";
					// $mnt_mois += $mnt_c;
				}while(( $same_mon));
				
				echo "</div>";// fin class='div-mois-body'
				
				echo "<div class='div-mois-foot'>Total Ordonnancements <strong>$moiss</strong> <b>".chiffre($mnt_mois)."</b></div>";
				
				echo "</div>";// fin class='div-mois'
				$mntt += $mnt_mois;
				$tab_ann[] = $mnt_mois;
				echo "";
				if(!$r)break;
			}
			return array($i,$mntt,$tab_ann);
		}
		{$req = "SELECT ac.acte ,ser.service ,ac.art_bud ,ass.nif ,ass.nom_assujetti ,ass.adresse_assujetti ,co.commune ,no.num_bap ,no.montant_bap ,no.note_to ,no.num_note ,no.date_ordo ,no.date_depot ,no.observation ,nac.montant_acte,nac.freq,nac.ajouter_le,ac.acte_id_service id_service,no.pr_cpt_de_id_com id_commune
		,extract(YEAR FROM no.$dt_save) annee, extract(MONTH FROM no.$dt_save) mois
		FROM t_note_actes nac
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id
		where nac.is_deleted=0 and  no.is_deleted=0 and ( no.$dt_save BETWEEN '$dt_ord1' AND '$dt_ord2') 
		$_WHERE ";
		}
		$th = "<th>N°</th><th>Ass.</th><th>NIF</th><th>Acte</th><th>Fréq.</th><th>N°NP.</th><th>Date Ord/Date Dp.</th><th>Art.Bud</th><th>Mnt.(Fc)</th><th>Obs.</th><th>Mt.Bap(Fc)</th>";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = " order by annee, mois, ser.service, ser.id ";
		// echo $req.$whre_order;
		if($pdo_result = Chado::$conx->query($req.$whre_order)){
			echo"<div class='partie'>";
				echo"<div class='partie-head'><h1>TOUS LES ORDONNANCEMENTS DES CENTRES, DES ANTENNES ET DES BUREAUX / SECTEUR</h1></div>";
				echo "<div class='partie-body'>";
					$rep = afficher_ligne($pdo_result,$th);
					
					if($rep[0]==0)echo "
					<table style='width:1300px'>
						<tr>$th</tr>
						<tr><td colspan='30'>Aucun ordonnancement </td></tr>
					</table>";
				echo "</div>";
				echo"<div class='partie-foot'><h1>TOTAL ORDONNANCEMENTS DES CENTRES, DES ANTENNES ET DES BUREAUX <b>".chiffre($rep[1])."</b></h1></div>";
			echo "</div>";
			$totaux += $rep[1];
		}
		$STR_DONUT = "";
		$STR_LINES = "";
		$STR_LINES = "&amp;CENTRES=".implode(',',$rep[2]);
		$src = $LINE_GRAPH.$STR_DONUT.$STR_LINES;		
			
		$std = " style='border:none'";
		echo "
		<table border='0' style='width:1300px;font-weight:bold;font-style:italic;text-align:center;margin: auto;font-size: 20px;'>
		<tr><td $std>Total général: ".chiffre($totaux)." Fc</td><td style='width:500px;border:none'></td><td$std>Lubumbashi le ".date("d-M-Y")."</td></tr>
		<tr><td$std>Cumul: ".chiffre(0)." Fc</td><td$std></td><td$std></td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std>LE C.B. ORDONNANCEMENT</td><td$std></td><td$std>LE C.B. CONTRÔLE </td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std>Francine PEMBA MAKUTU</td><td$std></td><td$std>SIX VUNINGA A KIRIZA</td></tr>
		</table>";
		?>
	</div>
	<?php 
	include("_suite_rapport.php");
	?>
	