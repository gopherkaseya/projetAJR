	<?php 
	$year = explode("-",$dt_ord1)[0];$month=explode("-",$dt_ord1)[1]; 
	
	function afficher_ligne($pdo_result,$th){
			$rows = array();
			$r = $pdo_result->fetch(PDO::FETCH_ASSOC);
			$i=$mntt=0;
			
			while($r){$i=0;
				// echo "<h3>$r[commune]</h3>";text-align:center;
				echo "<table style='width:100%;margin-bottom:30px'>";
				$mnt=0;
				echo "<caption style='font-weight:bold;font-size:16px;font-style:italic;backgroundd:#abc;color:#4e78c2'>Les Ordonnancements de: ".($r["id_ville"]!=1?"$r[ville]":"$r[commune]")." </caption>$th";
				do{
					do{$mnst=0;
						echo "<tr style='font-weight:bold;font-style:italic;backgroundd:#abc'><th></th><th colspan='10' style='text-align:center;'>Les Ordonnancements du secteur de: $r[service]</th></tr>";
						do{	$i++;					
							$mnt += $r["montant_acte"];$mnst += $r["montant_acte"];
							echo "<tr><td>$i</td>
							
							<td>$r[nom_assujetti]</td>"./* "<td>$r[service]</td>". */"<td>$r[acte]</td><td>$r[frequence_acte]</td><td>$r[num_note]</td><td>$r[date_ordo]|$r[date_depot]</td><td>$r[art_bud]</td><td>$r[montant_acte]</td>
							<td>$r[observation]</td><td>$r[montant_bap]</td>					
							</tr>"; 
							// $r["montant"] = chiffre($r["montant"]);
							$ex = $r;
						}while(($r = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["id_ville"]==$r["id_ville"] and $ex["id_commune"]==$r["id_commune"] and $ex["id_service"]==$r["id_service"]);
						echo "<tr style='font-weight:bold;font-style:italic;background:#cdf'><td></td>
							<td>s/total</td>"./* "<td></td>". */"<td></td><td></td><td></td><td></td><td></td><td>$mnst</td><td></td><td></td></tr>";
						$mntt += $mnst;
					}while(($ex["id_ville"]==$r["id_ville"] and $ex["id_commune"]==$r["id_commune"])
						/* or($r["ville"]!="Lubumbashi") */);
					echo "<tr><th colspan='4'></th><th colspan='30'>".chiffre($mnt)." : Total Principal</th></tr>";
				}while($ex["id_ville"]==$r["id_ville"] and (0 or $r["id_ville"]!=1));
				echo "</table>";
				if(!$r)break;
			}
			return array($i,$mntt);
		}
		
	?>
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
		
		.div-mois .div-mois-foot table tr td span {
			display: inline-block;
			text-align: left;
			width:100%;
		}
		.div-mois .div-mois-foot table tr td{
			width:20%;
			border-right: solid 1px;
		}
		.div-mois .div-mois-foot table tr td span strong {
			display: inline-block;
			text-align: right;
			float: right;
		}
		.div-mois table {
			margin: 0;
		}
		.div-mois-foot {
			padding: 0;
		}
		.div-mois .div-mois-foot  table:hover tr td {
			background: #f2b3b3;
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
		
		$MOIS=array('1'=>'JANVIER','2'=>'FEVRIER','3'=>'MARS','4'=>'AVRIL','5'=>'MAI','6'=>'JUIN','7'=>'JUILLET','8'=>'AOUT','9'=>'SEPTEMBRE','10'=>'OCTOBRE','11'=>'NOVEMBRE','12'=>'DECEMBRE');
		$limite_mois = $year==date('Y')?date('m'):12;
		$no_display = "style='display:none'";
		
		$STR_DONUT = "";
		$STR_LINES = "";
		$tab_annee_res = $tab_annee_rec = $tab_annee_ord = $tab_annee_niv = array();
		$tg_ord = $tg_rec = $tg_niv = $tg_res = 0; 
		for($mois_d = 1; $mois_d<= $limite_mois; $mois_d++ ){
			$month = $mois_d;
			$moiss = isset($MOIS[$month])?$MOIS[$month]:$month;
			
			$req_rec_mensuel = "SELECT  service,acte,sum(montant_payer) mnt  FROM `v_rec_mois` 
			where extract(MONTH FROM date_depot) = $month and extract(MONTH FROM date_paie) = $month and extract(YEAR FROM date_depot) = $year and extract(YEAR FROM date_paie) = $year group by id_service, id_acte order by service,acte";
			
			$req_ord_mensuel = "SELECT service,acte,sum(montant_acte) mnt FROM `v_ordonnancements` 
			where extract(MONTH FROM date_depot) = $month and extract(YEAR FROM date_depot) = $year group by id_service, id_acte order by service,acte  ";
			
			$req_rec_nivele = "SELECT service,acte,sum(montant_payer) mnt FROM `v_rec_nivele` 
			where extract(MONTH FROM date_paie) = $month and extract(YEAR FROM date_paie) = $year group by id_service, id_acte order by service,acte ";
			// echo $req_rec_mensuel;
			$pdo_result = Chado::$conx->query($req_rec_mensuel);
			
			$tab_rec_mens = $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			
			$pdo_result = Chado::$conx->query($req_ord_mensuel);
			$tab_ord_mens = $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			
			$pdo_result = Chado::$conx->query($req_rec_nivele);
			$tab_rec_nivele = $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			
			$tab_rapport = array();
			foreach($tab_rec_mens as $lg){
				$tab_rapport[$lg['service']][$lg['acte']] = array('rec'=>$lg['mnt'],'niv'=>0,'ord'=>0);
			}
			
			foreach($tab_rec_nivele as $lg){
				if(!isset($tab_rapport[$lg['service']][$lg['acte']]))
					$tab_rapport[$lg['service']][$lg['acte']] = array('rec'=>0,'niv'=>0,'ord'=>0);
				$tab_rapport[$lg['service']][$lg['acte']]['niv'] = $lg['mnt'];
			}
			
			foreach($tab_ord_mens as $lg){
				if(!isset($tab_rapport[$lg['service']][$lg['acte']]))
					$tab_rapport[$lg['service']][$lg['acte']] = array('rec'=>0,'niv'=>0,'ord'=>0);
				$tab_rapport[$lg['service']][$lg['acte']]['ord'] = $lg['mnt'];
			}
			
			// var_dump($tab_rapport);
			// update t_note_actes_payer, t_releve set date_paie = t_releve.date_paiement where id_releve = t_releve.id
			// update t_note_actes, t_note set act_date_ordo = t_note.date_ordo, act_date_depot=t_note.date_depot WHERE t_note.id = t_note_actes.id_note -- and t_note.date_ordo <> '0000-00-00' and t_note.date_depot <> '0000-00-00';	1
			
			echo "<div  class='div-mois'>";
			$mnt=$mnt_c=0;
			echo "<table $no_display  class='div-mois-head'><caption class='caption-mois'>Rapport du mois de: $moiss  </caption></table>";
			echo "<div  $no_display  class='div-mois-body'>";
			
			$totaux = 0;
			echo "<table style='width:100%;font-weight:bold;font-style:italic;text-align:center'><caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>situation générale des recettes ordonnancées et recouvrées et nivelées provisoire / $moiss-$year</caption>";
			$thi = "th style='font-style:italic;' ";
			echo "<tr style='font-weight:bold;backgroundd:#abc'>
			<$thi>N°</th><$thi>SECTEURS</th><$thi>ACTES GEN</th>";
			echo "<$thi>Ordonnancées</th><$thi>Recouvrées</th><$thi>Nivelées</th><$thi>Rest à rec.</th>";
			echo "</tr>";
			//
			$str = " style='background:#cdf' ";
			$j = 1;$ex_serv="";$style="style='font-weight:bold;font-style:italic;font-size:12px'";
			$t_ord = $t_rec = $t_niv = $t_res = $st_ord = $st_rec = $st_niv = $st_res = 0;
			foreach($tab_rapport as $serv=>$tab_actes){
				$st_ord = $st_rec = $st_niv = $st_res = 0;
				$n=$j++;
				foreach($tab_actes as $act=>$l){
				  $rest = $l['ord'] - $l['rec'];
				  echo "<tr><td>$n</td><td $style >$serv</td><td>$act</td><td>$l[ord]</td><td>$l[rec]</td><td>$l[niv]</td><td>$rest</td></tr>";
				  $st_ord +=$l['ord']; $st_res += $rest; $st_niv +=$l['niv']; $st_rec +=$l['rec'];
				  $n=$serv='';
				}
				echo "<tr $str><td></td><td $style ></td><td>s/total</td><td>".chiffre($st_ord)."</td><td>".chiffre($st_rec)."</td><td>".chiffre($st_niv)."</td><td>".chiffre($st_res)."</td></tr>";
				$t_ord += $st_ord;
				$t_rec += $st_rec;
				$t_niv += $st_niv;
				$t_res += $st_res;
			}
			
			$tg_ord += $t_ord;
			$tg_rec += $t_rec;
			$tg_niv += $t_niv;
			$tg_res += $t_res;
			echo "<tr><td></td><td $style ></td><td>Totaux</td><td>".chiffre($t_ord)."</td><td>".chiffre($t_rec)."</td><td>".chiffre($t_niv)."</td><td>".chiffre($t_res)."</td></tr>";
			echo "</table>";
			
			echo "</div>";// fin class='div-mois-body'
			
			echo "<div class='div-mois-foot'><table style='width:100%'>
			<tr $style>
			<td style='width:200px'>Rapport de <strong>$moiss</strong> </td>
			<td>
				<span>Ordo.: <strong>".chiffre($t_ord)."</strong></span></td>
				<td><span>Rec.: <strong>".chiffre($t_rec)."</strong></span></td>
				<td><span>Niv. <strong>".chiffre($t_niv)."</strong></span></td>
				<td><span>Reste: <strong>".chiffre($t_res)."</strong></span>
			</td>
			</tr></table>
			</div>";
			
			echo "</div>";// fin class='div-mois'
			$tab_annee_ord[] = $t_ord;
			$tab_annee_rec[] = $t_rec;
			$tab_annee_niv[] = $t_niv;
			$tab_annee_res[] = $t_res;
		}
		
		$STR_DONUT = "{value:$tg_ord"."_c_f00_h_f00_l_ORDOS'},";
		$STR_DONUT .= "{value:$tg_rec"."_c_0f0_h_0f0_l_RECVS'},";
		$STR_DONUT .= "{value:$tg_niv"."_c_00f_h_00f_l_NIVS'},";
		$STR_DONUT .= "{value:$tg_res"."_c_000_h_000_l_RESTES'},";
		$STR_LINES = "&amp;ORDO=".implode(',',$tab_annee_ord);
		$STR_LINES .= "&amp;REC=".implode(',',$tab_annee_rec);
		$STR_LINES .= "&amp;NIV=".implode(',',$tab_annee_niv);
		$STR_LINES .= "&amp;RES=".implode(',',$tab_annee_res);
		$std = " style='border:none'";
		echo "
		<table>
			<tr><td>Totaux Annuels</td><td>Ordo.: ".chiffre($tg_ord)."</td><td>Rec.: ".chiffre($tg_rec)."</td><td>Niv. ".chiffre($tg_niv)."</td><td>Reste: ".chiffre($tg_res)."</td></tr></table>
		";
		?>
	</div>
	<?php 
		$src = $LINE_GRAPH.$STR_DONUT.$STR_LINES;
	include("_suite_rapport.php");
	?>