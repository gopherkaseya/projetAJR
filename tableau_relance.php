
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
		<meta name="fragment" content="!">
		<title>Extrait de rôle</title>
		<link href="maia.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">
	</head>
	<?php  
	session_start();
	include("model/connexion.php");
	require_once("model/fonctions_sup.php");
	$conx = Connexion::GetConnexion();
	if(isset($_GET["id_ass"]))
		$where = " and a.id = '$_GET[id_ass]' ";
	else{
		$list_num = str_replace(",","','",$_POST["liste_num_note"]);
		$where = " and num_note in ('$list_num') ";
	}
	
	$req = "select n.id,a.id id_ass,nif,nom_assujetti,adresse_assujetti from t_Note n 
	INNER JOIN t_assujetti a on n.id_assujetti=a.id 
	INNER JOIN t_note_actes n_act  ON n_act.id_Note = n.id
	LEFT  JOIN t_note_actes_payer n_ap ON n_ap.id_noteacte = n_act.id  
	WHERE n_ap.id is null and ADDDATE( date_depot, 8 ) < now()
	and n.is_deleted=0  $where group by a.id,n.id ";
	$pdo_result = $conx->query($req);
	echo $req;
	$tab_ass = array();
	while($r = $pdo_result->fetch()){
		// $tab_ass["ass_$r[id]"]
		$tab_ass["ass_$r[id_ass]"]["id"][]=$r["id"];
		$tab_ass["ass_$r[id_ass]"]["nom"]=$r["nom_assujetti"];
		$tab_ass["ass_$r[id_ass]"]["adr"]=$r["adresse_assujetti"];
		$tab_ass["ass_$r[id_ass]"]["nif"]=$r["nif"];
	}
	
	// echo "<pre>";print_r($tab_ass);echo "</pre><br><br>";
	
foreach($tab_ass as $id=>$tt)
{
	$assu = $tt["nom"];
	$adrs = $tt["adr"];
	$nif = $tt["nif"];
	$list_id = implode(",",$tt["id"]);
	$tab = tableau_relance($conx,$list_id);
	// $tab = return_extrai_role(Chado::$conx,$_POST["list_num_note"]);
	// echo "$assu $adrs";
?>	
	
	<div style="margin:0;height:50px;font-size:16px;line-height:16px;border:solid 0px;">
		<p style="margin:0;width:auto;float:left;"><i>DGRAD/KATANGA</i></br><u><i>RESSORT DE LUBUMBASHI</i></u></p>
		<p style="font-size:16px;width:auto;margin:0;line-height:23px;float:right;border:solid 0px">
			<span style="">Lubumbashi le: ....................................</span><br/>N° ............../DGRAD/DP/KTG/RU/16
		</p>
	</div>
	<style>
		.paragraph{font-size:16px;margin:0px;border:solid 0px;text-align:justify;}
		.paragraph.debut{padding-left:50%;overflow:hidden;margin-top:3px;text-align:justify;;}
		table tr td,th{border:solid 1px #000;padding-right:5px}
		table tr th{text-align:center}
	</style>
	<div style="margin-bottom:30px;height:35px;border:solid 0px;" >
		<p style="font-size:16px;width:auto;margin:0;margin-top:10;float:left;border:solid 0px">
			<b>CONCERNE: RELANCE</b>
		</p>
		<p style="margin:0;font-size:16px;font-bold:weight;line-height:16px;text-align:right;border:solid 0px">
			<p style="margin:0;border:solid 0px;width:auto;float:right;text-align:left">
			<i>ASSUJETTI: </i> <b><?php echo strtoupper($assu);?></b>
			</br><i>N.I.F. : </i>  <b><?php echo $nif;?></b>
			</br><i>ADRESSE: </i>  <b><?php echo $adrs;?></b>
		  </p>
		</p>
	</div>
	<!-- début de la lettre --->
	<p class="paragraph debut" style="border:solid 0px;" >
		<b>Monsieur</b>,<br/>
		J`ai l`honneur de vous rappeler que le(s) Titre(s) de perception établi(s)
	</p>
	<p class="paragraph">
		 à votre charge dans  le  secteur ci-après demeurent impayés;
	</p>
	<p class="paragraph debut">
		Eu  égard à ce retard  de paiement, je vous demande d’apurer  votre dette
		
	</p>
	<p class="paragraph"> 
		 envers le Trésor Public, suivant le tableau ci-dessous. Faute de quoi  les mesures de recouvrement forcé seront  mises en application  conformément 
		à la législation en vigueur pour remettre le Trésor Public dans ses droits.
	</p>
	<p class="paragraph debut">
		Veuillez agréer, Messieurs, l’expression de ma  parfaite  considération.
	</p>
	
	<a id='bt_print_retour' style="display:none" href='#x' >Impression-retour</a>
	<?php tracer_extrait_role($tab); ?>
	
	
	<div style="height:125px;border:solid 0px;margin-top:30px">
	<p style="width:100%;height:30px;border:solid 0px;margin:0"><span style='float:right;padding-right:90px;'><!--i>Fait à lubumbashi le <?php echo /* date("d/m/Y"). */"<br>";?></i--></span></p>
	<p style="width:100%;"><!-- style='float:left;font-weight:bold;'>VISA DU CHEF DE RESSORT URBAIN</span--><span style='float:right;padding-right:100px;font-weight:bold;'>LE RECEVEUR URBAIN</span></p>
	<br>
	<br>
	<p style="width:100%;"><span style='float:left'><!--Joseph MANIEMA MUZAMA--></span><span style='float:right;padding-right:75px;font-weight:bold;'>Olivier MPASI NGOMA </span></p>
	</div>
	<br>
	<hr style=""/>
	<p style="font-size:16px;text-align:center;line-height:12px;" >
	Adresse: 498, Avenue du trente juin, Commune de lubumbashi<br/>
	<a href="#1">dgradlshiressurbaim@yahoo.fr</a></p>
	
	</div>
	<?php
}
	?>
	