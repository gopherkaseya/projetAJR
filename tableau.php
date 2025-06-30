
	<?php
	$title = "Relance";$note_concer="CONCERNE: MISE EN DEMEURE";
	if(isset($_GET["type"])and $_GET["type"]=="extrait"){	
		$title = "Extrait de rôle";$note_concer="CONCERNE: EXTRAIT DE ROLE";
	}
	if(isset($_GET["list"]))
		$list_num = str_replace(",","','",$_GET["list"]);
	else $list_num = "";
	?>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
		<meta name="fragment" content="!">
		<title><?php echo $title;?></title>
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
		$where = " and num_note in ('$list_num') ";
	}
	
	$req = "select n.id,a.id id_ass,nif,nom_assujetti,adresse_assujetti from t_note n 
	INNER JOIN t_assujetti a on n.id_assujetti=a.id 
	INNER JOIN t_note_actes n_act  ON n_act.id_Note = n.id
	LEFT  JOIN t_note_actes_payer n_ap ON n_ap.id_noteacte = n_act.id  
	WHERE n_ap.id is null 
	and n.is_deleted=0  $where group by a.id,n.id ";
	$tab_ass = array();
	if($pdo_result = $conx->query($req)){
		while($r = $pdo_result->fetch()){
			// $tab_ass["ass_$r[id]"]-- and ADDDATE( date_depot, 8 ) < now()
			$tab_ass["ass_$r[id_ass]"]["id"][]=$r["id"];
			$tab_ass["ass_$r[id_ass]"]["nom"]=$r["nom_assujetti"];
			$tab_ass["ass_$r[id_ass]"]["adr"]=$r["adresse_assujetti"];
			$tab_ass["ass_$r[id_ass]"]["nif"]=$r["nif"];
		}
	}else {
	    //echo $req;
	    var_dump($conx->errorInfo()[2]);
	}
	// var_dump($tab_ass);
	// echo $req;
	
	//echo "<pre>";print_r($tab_ass);echo "</pre><br><br>";
	
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
	<div style="position:absolute;z-index:0;text-align:center;width:1100px;
	margin:20px 0 0 20px;background-repeat: no-repeat;
	background-position:center;border:solid 0px">
	<img src="logo1.jpg" style="height:680px;opacity:0.5;" />
	</div>
	<div style="margin:0;height:50px;font-size:16px;line-height:16px;border:solid 0px;">
		<p style="margin:0;width:auto;float:left;"><i>DGRAD/KATANGA</i></br><u><i>RESSORT DE LUBUMBASHI</i></u></p>
		<p style="font-size:16px;width:auto;margin:0;line-height:23px;float:right;border:solid 0px">
			<span style="">Lubumbashi le: ....................................</span><br/>N° ............../DGRAD/DP/HKHL/RU/23
		</p>
	</div>
	<style>
		.paragraph{font-size:14px;margin:0px;border:solid 0px;text-align:justify;}
		.paragraph.debut{padding-left:45%;overflow:hidden;margin-top:3px;text-align:justify;;}
		table tr td,th{border:solid 1px #000;padding-right:5px}
		table tr th{text-align:center}
		.limit_relance { page-break-after: always}
	</style>
	<div style="margin-bottom:30px;height:35px;border:solid 0px;" >
		<p style="font-size:16px;width:auto;margin:0;margin-top:10;float:left;border:solid 0px">
			<b><?php echo $note_concer;?></b>
		</p>
		<p style="margin:0;font-size:16px;font-bold:weight;line-height:16px;text-align:right;border:solid 0px">
			<p style="margin:0;border:solid 0px;width:auto;float:right;text-align:left">
			<i>ASSUJETTI: </i> <b><?php echo strtoupper($assu);?></b>
			</br><i>N.I.F. : </i>  <b style="color:red" ><?php echo $nif;?></b>
			</br><i>ADRESSE: </i>  <b><?php echo $adrs;?></b></p>
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
		Eu  égard à ce retard  de paiement, je vous demande d’apurer  votre dette sans délai envers
		
	</p>
	<p class="paragraph"> 
		  le Trésor Public, suivant le tableau ci-dessous. Faute de quoi  les mesures de recouvrement forcé seront  mises en application  conformément 
		à la législation en vigueur pour<br/> remettre le Trésor Public dans ses droits.
	</p>
	<p class="paragraph debut">
		Veuillez agréer, Messieurs, l’expression de ma  parfaite  considération.
	</p>
	
	<a id='bt_print_retour' style="display:none" href='#x' >Impression-retour</a>
	<?php tracer_extrait_role($tab); ?>
	
	
	<div style="border:solid 0px;margin-top:0px">
	<p style="width:100%;"><!--span style='float:left;font-weight:bold;'>VISA DU CHEF DE RESSORT URBAIN</span--><span style='float:right;padding-right:100px;font-weight:bold;'>          LE  RECEVEUR  URBAIN</span></p>
	<br>
	<br>
	<p style="width:100%;"><span style='float:left'><!--Joseph MANIEMA MUZAMA--></span><span style='float:right;padding-right:75px;font-weight:bold;'>Olivier  MPASI  NGOMA   </span></p>
	</div>
	<br>
	<hr style=""/>
	<p style="font-size:16px;text-align:center;line-height:12px;" >
	Adresse: 498, Avenue du trente juin, Commune de lubumbashi<br/>
	<a href="#1">dgradlshiressurbaim@yahoo.fr</a></p>
	
	</div>
	<hr class="limit_relance" style="margin:0;padding:0;border:none" />
	<?php
}
	?>
	