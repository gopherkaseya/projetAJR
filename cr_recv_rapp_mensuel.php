
	<p style="
		position: absolute;
		top: -70px;
		right: 0;
		z-index: 400;
	">
		<button type="button" class="btn bg-purple btn-flat margin" onclick='document.getElementById("div_ordonnancement").style.display="block";document.getElementById("iframe-rapport").style.display="none";'>Details</button>
		
		<button type="button" class="btn bg-maroon btn-flat margin" style="background-color: #3d9970 !important;" onclick='document.getElementById("div_ordonnancement").style.display="none";document.getElementById("iframe-rapport").style.display="block";'>Rapport</button>
	</p>
		
	<?php $year = 2016;
	$mois=array("01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin","07"=>"Jouillet","08"=>"Août","09"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Décembre");
	$MOIS=array('1'=>'JANVIER','2'=>'FEVRIER','3'=>'MARS','4'=>'AVRIL','5'=>'MAI','6'=>'JUIN','7'=>'JUILLET','8'=>'AOUT','9'=>'SEPTEMBRE','10'=>'OCTOBRE','11'=>'NOVEMBRE','12'=>'DECEMBRE');
	?>
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
		tr td,th{border:solid 1px #000;}
		
		
		.div-mois{}
		
		.div-mois {
			width: 1330px;
			background: #effffe;
			margin: auto;
			margin-bottom: 4px;
			border: solid 1px;
			padding: 0px 15px 0px 15px;
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
			margin-top: 10px;
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
	</style>
				
	<div id='div_ordonnancement' style='width:100%;display:none' >
		
		<?php
		
		{
			$s=" 00:00:00";
			$s="";
			$req = "SELECT n_ap.montant_payer ,ser.service ,ac.acte ,ac.art_bud ,nac.freq ,no.date_ordo ,no.date_depot ,ass.nom_assujetti ,ass.adresse_assujetti ,no.num_note ,no.num_bap ,ifnull('',concat(no.num_bap,' ',no.montant_bap))montant_bap ,no.note_to ,no.date_save date_enrg ,nac.ajouter_le date_enreg ,co.commune,co.id id_commune
		,extract(YEAR FROM no.$dt_save) annee, extract(MONTH FROM no.$dt_save) mois
		
		FROM t_note_actes nac 
		INNER JOIN t_acte ac ON nac.id_acte = ac.id 
		INNER JOIN t_service ser ON ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON nac.id_Note = no.id 
		INNER JOIN t_assujetti ass ON no.id_assujetti = ass.id 
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
				
		INNER JOIN (
			t_note_actes_payer n_ap inner join t_releve rlv on rlv.id=n_ap.id_releve
			) ON n_ap.id_noteacte = nac.id 
		WHERE n_ap.is_deleted=0 and nac.is_deleted=0 and no.is_deleted=0 and rlv.is_deleted=0
		and rlv.date_paiement BETWEEN '$dt_ord1' and '$dt_ord2' 
		and no.date_depot BETWEEN '$dt_ord1' and '$dt_ord2' 
		order by  annee, mois, ser.service, ser.id ,ac.acte";
		}
		// -- and (extract(YEAR FROM no.$dt_save)='".(explode("-",$dt_ord1)[0])."' and extract(MONTH FROM no.$dt_save)='".($moi = explode("-",$dt_ord1)[1])."') 
		// echo $req;
			$th = "";//<th>Serv.</th>
		$totaux = 0;
		// ordonnancement de la ville de lubumbashi et uniquement dans les centres
		$whre_order = "";
			$tab_tot_an = array();
		if($pdo_result = Chado::$conx->query($req.$whre_order)){//echo $req.$whre_order;
			$tab = array();$tab_col = array();
			while($r = $pdo_result->fetch(PDO::FETCH_ASSOC)){
				$cl_lg = $r["acte"];
				$month = $r["mois"];
				// si nous ne somme pas en face de lubumbashi
				$cl_col = ($r["commune"]);
				if(isset($tab[$month][$cl_lg][$cl_col]))
					$tab[$month][$cl_lg][$cl_col] += $r["montant_payer"];
				else {
					$tab[$month][$cl_lg]["service"] = $r["service"];
					$tab[$month][$cl_lg][$cl_col] = $r["montant_payer"];
					
				}
				if(!isset($tab_col[$month][$cl_col]))$tab_col[$month][$cl_col]=0;
				$tab_col[$month][$cl_col] += $r["montant_payer"];
			}
			// $lg_total = $tab_col;
			// $tab_col = array_keys($tab_col);
			// echo "<pre>";print_r($tab);echo"</pre>";
			//
			$totaux = 0;
			
			$no_display = "style='display:none'";
			$totaux = 0;
			foreach($tab as $month=>$tab_month){
				$moiss = isset($MOIS[$month])?$MOIS[$month]:$month;
				// echo "<h3>$r[commune]</h3>";text-align:center;
				echo "<div  class='div-mois'>";
				$mnt=$mnt_c=0;
				echo "<table $no_display  class='div-mois-head'><caption class='caption-mois'>Les Ordonnancements du mois de: $moiss  </caption></table>";
				echo "<div  $no_display  class='div-mois-body'>";
				
				$lg_total = $tab_col[$month];
				$tab_col_month = array_keys($tab_col[$month]);
				echo "<table style='width:100%;font-weight:bold;font-style:italic;text-align:center'><caption style='font-weight:bold;font-size:16px;font-style:italic;color:#4e78c2'>RAPPORT MENSUEL DES RECOUVREMENTS NIVELES DE: $moiss</caption>";
				$thi = "th style='font-style:italic;' ";
				echo "<trstyle='font-weight:bold;backgroundd:#abc'>
				<$thi>N°</th><$thi>SECTEURS</th><$thi>ACTES GEN</th>";
				for($i=0;$i!=count($tab_col_month);$i++){
					echo "<$thi>$tab_col_month[$i]</th>";
				}
				echo "<$thi>TOTAL</th></tr>";
				//
				$style = " style='text-align:right' ";
				$j = 1;$ex_serv=$n="";$lg_tt = array();
				foreach($tab_month as $cle=>$ligne){
					$total=0;
					if($ligne['service']!=$ex_serv){
						if($ex_serv!=""){
							$stotal=0;
							echo "<tr><td></td><td style='font-weight:bold;font-style:italic;font-size:12px' >s/total</td><td></td>";
							for($i=0;$i!=count($tab_col_month);$i++){
								$mt = is_numeric($lg_tt[$tab_col_month[$i]])?$lg_tt[$tab_col_month[$i]]:0;
								$stotal += $mt;
								echo "<td>$mt</td>";
							}
							echo "<td$style>".chiffre($stotal)."</td></tr>";
						}
						$n=$j++;
						$serv = $ligne['service'];
						$lg_tt = array();
					}
					$ex_serv = $ligne['service'];
					echo "<tr><td>$n</td><td style='font-weight:bold;font-style:italic;font-size:12px' >$serv</td><td>$cle</td>";
					for($i=0;$i!=count($tab_col_month);$i++){
						$mt = "&nbsp;";
						if(isset($ligne[$tab_col_month[$i]])){
							$mt = $ligne[$tab_col_month[$i]]?$ligne[$tab_col_month[$i]]:0;
							$total += $mt;
						}
						if( is_numeric($mt) and isset($lg_tt[$tab_col_month[$i]]) and is_numeric($lg_tt[$tab_col_month[$i]]))$lg_tt[$tab_col_month[$i]] += $mt;
						else $lg_tt[$tab_col_month[$i]] = $mt;
						echo "<td>$mt</td>";
					}
					echo "<td$style>".chiffre($total)."</td></tr>";
					$n = $serv = "";
					$totaux += $total;
				}
				$ttt=0;
				echo "<tr><td></td><td></td><td></td>";
				foreach($lg_total as $cle=>$val){
					echo "<td>".chiffre($val)."</td>";$ttt += $val;
				}
				$tab_tot_an[] =$ttt;
				echo "<td style='font-weight:bold;text-align:right;'>".($ttt=chiffre($ttt))."</td></tr>";
				echo "</table>";
			
				echo "</div>";// fin class='div-mois-body'
				
				echo "<div class='div-mois-foot'>Total Ordonnancements <strong>$moiss</strong> <b>".($ttt)."</b></div>";
				
				echo "</div>";// fin class='div-mois'
			}
		}
		$STR_DONUT = "";
		$STR_LINES = "";
		$STR_LINES = "&amp;CENTRES=".implode(',',$tab_tot_an);
		$src = $LINE_GRAPH.$STR_DONUT.$STR_LINES;
		$std = " style='border:none'";
		echo "
		<table border='0' style='width:100%;font-weight:bold;font-style:italic;text-align:center'>
		<tr><td $std>Total général: ".chiffre($totaux)." Fc</td><td style='width:500px;border:none'></td><td$std>Lubumbashi le ".date("d-M-Y")."</td></tr>
		<tr><td$std>Cumul: ".chiffre(0)." Fc</td><td$std></td><td$std></td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std><!-- LE C.B. RECOUVREMENTS --></td><td$std></td><td$std>LE C.B. RECOUVREMENTS</td></tr>
		<tr><td$std></td><td$std></td><td$std>&nbsp;</td></tr>
		<tr><td$std><!-- Francine MPEMBA MAKUTU --></td><td$std></td><td$std>Six VUNINGA A KIRIZA</td></tr>
		</table>";
		?>
	</div>
	<?php 
	include("_suite_rapport.php");
	?>
	
	