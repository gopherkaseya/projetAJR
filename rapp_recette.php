	
	<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:200px;}
		#table_ordonnancer_note input{width:100%}
		#id_lien_rapp a{margin-right:10px}
	</style>
	<p id="id_lien_rapp"><a onClick="askServeur('liste_objets_ajax.php?page=rapp_recette&rap=ord','div_ordonnancement');" href='#xx'>Les Ordonnancées</a>
	<a onClick="askServeur('liste_objets_ajax.php?page=rapp_recette&rap=rec','div_ordonnancement');" href='#xx'>Les Recouvrées</a></p>
				
	<div id='div_ordonnancement' style='width:100%' >
		
		<br/>
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
				case "exp":$req = " where n_ord.num_note is null ";break;
			}
		}
		$th .= '</tr>';
		{$req = "SELECT n_act.id ,n_ord.date_ordo,n_ord.date_depot,n_ord.num_note,n_ord.num_bap,n_ord.montant_bap,n_ord.observation
		,ass.nom_assujetti,ass.adresse_assujetti
		,n_act.montant_acte montant,act.art_bud,ser.service serv_acte
		,n_act.frequence_acte
		,com.commune com_ordo,ser_1.service serv_ordo,vll.ville ville_ordo,ntp.num_note num_note_payee,ntp.montant_paye,rlv.date_paiement,bq.nom_banque,bq.adresse_banque,bq.telephone_banque 
		FROM snp_t_noteactes n_act INNER JOIN snp_t_acte act ON n_act.id_acte = act.id INNER JOIN snp_t_service ser ON act.acte_id_service = ser.id INNER JOIN snp_t_Note n_ord ON n_act.id_Note = n_ord.id INNER JOIN snp_t_assujetti ass ON n_ord.id_assujetti = ass.id INNER JOIN snp_t_assoc_com_serv com_ser ON n_act.id_com_serv_ordo = com_ser.id INNER JOIN snp_t_commune com ON com_ser.id_commune = com.id INNER JOIN snp_t_ville vll ON com.id_ville = vll.id INNER JOIN snp_t_service ser_1 ON com_ser.id_service = ser_1.id LEFT JOIN (snp_t_notepayees ntp INNER JOIN snp_t_releve rlv ON ntp.id_releve = rlv.id INNER JOIN snp_t_banque bq ON rlv.id_banque = bq.id ) ON ntp.num_note = n_ord.id
		$req
		order by com_ordo,serv_ordo	";}
		
		if($pdo_result = Chado::$conx->query($req)){
			$rows = array();
			$row = $pdo_result->fetch(PDO::FETCH_ASSOC);
			
			while(true){$i=0;
				echo "<h3>$row[com_ordo]</h3>
				<table style='width:1200px'>$th";$mnt=0;
				do{$i++;
					echo "<tr><td>$i</td>";
					$mnt += $row["montant"];
					$row["montant"] = chiffre($row["montant"]);
					foreach($row as $c=>$v){
						if(!in_array($c,$t_ch))
						echo "<td>$v</td>";
					}
					echo "</tr>";
					$ex = $row;
				}while(($row = $pdo_result->fetch(PDO::FETCH_ASSOC)) and $ex["com_ordo"]==$row["com_ordo"]);
				echo "<tr><th colspan='4'></th><th colspan='9'>".chiffre($mnt)." : Total Principal</th></tr></table>";
				if(!$row)break;
			}
				
		}
		?>
	</div>
	