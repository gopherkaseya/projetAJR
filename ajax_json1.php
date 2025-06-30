<?php
	session_start();
	header("Content-type: text/javascript");
	require_once("model/connexion.php");
	function rapport_ordo_annuel($dt1="",$dt2="",$id_ser="",$id_ant="",$dt_save="date_depot"){
		$conx = Connexion::GetConnexion();
		$message ="";
		$result=array("result"=>"fail","message"=>"");
		$req = "SELECT ac.acte a,ser.service s,ac.art_bud ar,ass.nom_assujetti asj,ass.adresse_assujetti ad,co.commune cm,no.num_bap nb,no.montant_bap mt_b,no.note_to to,no.num_note nu,no.date_ordo dto,no.date_depot dtd,nac.montant_acte mt_a,nac.freq f,nac.ajouter_le dts,ac.acte_id_service id_service,no.pr_cpt_de_id_com id_commune
		,extract(YEAR FROM no.$dt_save) an, extract(MONTH FROM no.$dt_save) moi
		FROM t_note_actes nac
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id 
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id
		WHERE nac.is_deleted=0 and  no.is_deleted=0 and no.$dt_save BETWEEN '$dt1' AND '$dt2'
		order by ser.service,ac.acte  
		( rlv.date_paiement BETWEEN '$dt1' AND '$dt2') 
		".($id_ser?" and ser.id=$id_ser":"")."
		".($id_ant?" and co.id=$id_ant":"")."
		group by date_paiement,no.id
		order by dp
		";
		if($pdo_result = $conx->query($req)){
			if($row = $pdo_result->fetchAll(PDO::FETCH_ASSOC)){
				$result["json"] = ($row);
				$result["result"]="success";
				}
			else {$message = $conx->errorInfo()[2];}
		}else $message = $conx->errorInfo()[2];
		$result["message"]=$message;
		echo json_encode($result);
	}
	function encaissement_journalier($dt1="",$dt2="",$id_ser="",$id_ant=""){
		$conx = Connexion::GetConnexion();
		$message ="";
		$result=array("result"=>"fail","message"=>"");
		$req = "SELECT nacp.id, ac.acte a,ser.service s,ass.nom_assujetti asj,ass.adresse_assujetti ad,co.commune c,no.num_note n,sum(nac.montant_acte) mt_o  ,bq.nom_banque bq,rlv.date_paiement dp ,sum(nacp.montant_payer) mt_p
		-- ,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte
		FROM t_note_actes_payer nacp 
		INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 
		INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
		INNER JOIN t_banque bq ON  rlv.id_banque = bq.id 
		WHERE rlv.is_deleted=0 and nacp.is_deleted=0 and nac.is_deleted=0 and no.is_deleted=0 and  
		( rlv.date_paiement BETWEEN '$dt1' AND '$dt2') 
		".($id_ser?" and ser.id=$id_ser":"")."
		".($id_ant?" and co.id=$id_ant":"")."
		group by date_paiement,no.id
		order by dp
		";
		if($pdo_result = $conx->query($req)){
			if($row = $pdo_result->fetchAll(PDO::FETCH_ASSOC)){
				$result["json"] = ($row);
				$result["result"]="success";
				}
			else {$message = $conx->errorInfo()[2];}
		}else $message = $conx->errorInfo()[2];
		$result["message"]=$message;
		echo json_encode($result);
	}
	function relever_imge($dt1="",$dt2="",$id_ser="",$id_ant=""){
		$conx = Connexion::GetConnexion();
		$message ="";
		$result=array("result"=>"fail","message"=>"");
		$req = "SELECT sum(nac.montant_acte) mt_o  ,bq.nom_banque bq,rlv.date_paiement dp ,sum(nacp.montant_payer) mt_p
		,count(distinct no.id) nbr_n, img_releve i, extract(MONTH FROM rlv.date_paiement) m
		-- ,nacp.date_enreg,nacp.paie_exp_num_note,nacp.paie_exp_id_acte
		FROM t_note_actes_payer nacp 
		INNER JOIN t_note_actes nac ON  nacp.id_noteacte = nac.id  
		INNER JOIN t_acte ac ON  nac.id_acte = ac.id  
		INNER JOIN t_service ser ON  ac.acte_id_service = ser.id  
		INNER JOIN t_note no ON  nac.id_note = no.id  
		INNER JOIN t_assujetti ass ON  no.id_assujetti = ass.id  
		INNER JOIN t_commune co ON  no.pr_cpt_de_id_com = co.id 		
		INNER JOIN t_releve rlv ON  nacp.id_releve = rlv.id  
		INNER JOIN t_banque bq ON  rlv.id_banque = bq.id 
		WHERE rlv.is_deleted=0 and nacp.is_deleted=0 and nac.is_deleted=0 and no.is_deleted=0 and  
		( rlv.date_paiement BETWEEN '$dt1' AND '$dt2') 
		".($id_ser?" and ser.id=$id_ser":"")."
		".($id_ant?" and co.id=$id_ant":"")."
		group by rlv.id,date_paiement
		order by dp,bq
		";
		if($pdo_result = $conx->query($req)){
			if($row = $pdo_result->fetchAll(PDO::FETCH_ASSOC)){
				$result["json"] = ($row);
				$result["result"]="success";
				}
			else {$message = $conx->errorInfo()[2];}
		}else $message = $conx->errorInfo()[2];
		$result["message"]=$message;
		echo json_encode($result);
	}
	// print_r($_SESSION);
	if(isset($_SESSION["user"])){
		if(isset($_GET["list"])){
			if($_GET["list"]=="journal_ordo_annuel")
				rapport_ordo_annuel($_GET["drt1"],$_GET["drt2"],$_GET["id_service"],$_GET["id_antenne"],((isset($_GET["dt_ord"])and $_GET["dt_ord"]=="ok")?"date_save":"date_depot"));
			else if($_GET["list"]=="encaisse_jr")
				encaissement_journalier($_GET["drt1"],$_GET["drt2"],$_GET["id_service"],$_GET["id_antenne"]);
			else if($_GET["list"]=="img_releve")
				relever_imge($_GET["drt1"],$_GET["drt2"],$_GET["id_service"],$_GET["id_antenne"]);
		}
	}