	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
	<div id="id_lien_rapp">
		<?php echo $_ville." ".$_com." ".$_service." Rôle des ordonnancements du <b style='color:blue'>$dt_ord1</b> au <b style='color:blue'>$dt_ord2</b> "; ?>
		Ici, vous avez la liste de notes non payées et ayant déjà excéder 8 jours dépuis leurs dépôt.<a href="#x" onclick="window.open('recv/reste_rec.php')">Rest à recouvrer</a></br>
		<input title="saisissez les numéro de note à extraire" placeholder="0000000,0000000,000000" name="liste_num_note" id="liste_num_note" />
		<button type="submit" onClick="window.open('tableau.php?list='+document.getElementById('liste_num_note').value+'&type=relance');" />RELANCE</button>
		<button type="button" id="btn_extrait_role" onClick="window.open('tableau.php?list='+document.getElementById('liste_num_note').value+'&type=extrait');" />EXTRAIT</button>
	

		<form method='POST' action='traitement_sup.php' target='iframe' style='border:solid 0px;margin:0;display:inline'>
			<input type="text" name="num_note" class="form-control" title="N° Note"  placeholder="N° Note" required style="padding: 8px;width:65px" />
			<input name="recouvrement" value="ok" type="hidden"/>
			<input type="submit" name="bt_form_annuler_note" class="btn maia-button" style="height:35px" value="Annuler Note" />
		</form>
		<div id="id_form_annuler_note" style="display:inline-bloc;margin-left:20px;padding:10px;background:#ff00001c;width:350px;float:right" ></div>
		<div style="width:100%;display: block;min-height:2px;float:left">
			<form method='POST' action='traitement_sup.php' target='iframe' style='widtth:100%;float:left'>
				<div id='div_form_ordo_notee' style='float:left;margin-right:20px;border-right:solid 1px;padding-right:10px' ></div>
				<div id='div_form_ordo' style='float:left;margin-right:20px;' ></div>
			</form>
			
		</div>

	</div>

	<div id="div_extrait_de_role" style='width:100%'  ></div>
	<div id='div_ordonnancement' style='width:100%' >
		
		<?php
		
		
		{$req = "SELECT no.id id_ordo ,n_ap.id_noteacte ,nac.montant_acte ,ser.service ,ac.acte ,ac.art_bud ,nac.freq ,no.date_ordo ,no.date_depot ,ass.nom_assujetti ,ass.id id_ass,ass.adresse_assujetti ,no.num_note ,no.num_bap ,no.montant_bap ,no.note_to ,no.date_save date_enrg ,nac.ajouter_le date_enreg ,co.commune 
		
		FROM t_note_actes nac 
		INNER JOIN t_acte ac ON nac.id_acte = ac.id 
		INNER JOIN t_service ser ON ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON nac.id_Note = no.id 
		INNER JOIN t_assujetti ass ON no.id_assujetti = ass.id 
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
		
		LEFT JOIN t_note_actes_payer n_ap ON n_ap.id_noteacte = nac.id  
		WHERE nac.is_deleted=0 and no.is_deleted=0 and n_ap.id is null and 
		( no.date_depot BETWEEN '$dt_ord1' AND '$dt_ord2') -- and ADDDATE( no.date_depot,  ) < now()
		$_WHERE
		order by 
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
			while($r){$i=0;
				echo "<h3>$r[commune]</h3>
				<table style='width:100%'>$th";
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
				echo "<tr><th colspan='2'></th><th colspan='30'>".chiffre($mnt)." : Total Principal</th></tr></table>";
				$mttt += $mnt;
				if(!$r)break;
			}
			if($mttt)echo"<h3>Total Général: ".chiffre($mttt)."</h3>";
			if($i==0)echo "<table style='width:1200px'>
			<tr>$th</tr>
			<tr><td colspan='30'>Aucune note apurée sur cette période</td></tr>
			</table>";
		}
		?>
	</div>
	