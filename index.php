<?php	



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	if(isset($_POST["bt_fixer_regle_rapport"])){
		$deux_mois = time() + 3600*24 *60 ;
		setcookie($_POST["table"],$_POST["champs"],$deux_mois);
		$_COOKIE[$_POST["table"]] = $_POST["champs"];
	}
	?>
<!DOCTYPE HTML><html>
	<head>
		<meta charset="utf-8">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		
    header("Location: security/index.php");
    exit();
	}

	// $_SESSION=[];
	require_once('model/classes.php');
	Chado::$conx = Connexion::GetConnexion();
	$duree_expiration="00:10:00";
	if(isset($_POST["bt_resend_code"]) and isset($_SESSION["id_utilisateur"])){
        $_SESSION["connected"] = "ATTENTE";
        $t=date('His');
        $code = "$t[5]$t[3]$t[1]$t[0]$t[2]$t[4]";
        $conx = Connexion::GetConnexion();
        $id =  $_SESSION["id_utilisateur"];
        $req = "UPDATE `sys_utilisateur` SET `code_sec` = '$code',heure_conx=now() WHERE `sys_utilisateur`.`id` = '$id'; ";						
		$conx->exec($req);
        header("location://bulletin.delardc.com/send-mail?sujet=CODE SECRET&message=Le code SECRET EST:<h2>$code</h2>&dest=$lg&red=dgradru.delardc.com");
    }
	elseif(isset($_POST["bt_second_conx"]) and isset($_SESSION["id_utilisateur"])){
        $code = $_POST['code'];
	    $conx = Connexion::GetConnexion();
	    $id = $_SESSION["id_utilisateur"];
        $req = "select (TIMEDIFF(CURRENT_TIME(), heure_conx) > '$duree_expiration') as a_expire from sys_utilisateur where code_sec='$code' and `sys_utilisateur`.`id` = '$id'; ";	
    	$pdo_result = Chado::$conx->query($req);
        
	    if($pdo_result and $t = $pdo_result->fetch(PDO::FETCH_ASSOC)){
			if($t['a_expire']==0) 
			    $_SESSION["connected"] = "OK";
			else {
			    $_SESSION["message"] = "CODE SECRET DEJA EXPIRE, RENVOYEZ LE CODE";
			    $_SESSION["connected"] = "EXPIRE";
			}
		}
		else{
			$_SESSION["message"] = "CODE SECRET INCORRECT, RESSAYEZ SVP!";
			$_SESSION["connected"] = "INCORRECT";
		}
	}
	elseif(isset($_POST["bt_login"])){
		$_SESSION["db"] = $_POST["annee"];
		$lg = $_POST["login"];
		$pwd = $_POST["password"];
		$l = Utilisateur::liste_objet("where login='$lg' and password='$pwd' -- and sys_ut.actif=1  and sys_ut.is_deleted=0 ","");
		if(Utilisateur::$count ){
			$_SESSION['user']["password"] = $pwd;
			$_SESSION["connected"] = "OK";
			$_POST["id_utilisateur"]=$_SESSION["id_utilisateur"] = $l[0]["id"];
			$_POST["date_heure_deconx"]=time();
			$_SESSION["snp"]["user"] = $l[0];
			$_SESSION["message"] = "Authentification Effectuée";
			$u = new Visites();
			$u->creer();
			$_SESSION['user']['tab_class'] = array();
			// classe permises
			if($c = UserClasse::liste_objet(" where id_utilisateur ='$_POST[id_utilisateur]' ","")){
				foreach($c as $cla)
					$_SESSION['user']['tab_class'][$cla['class_nom']] = true;
			}
			if(count(explode('@',$lg))==2){
		        $_SESSION["connected"] = "ATTENTE";
		        $t=date('His');
                $code = "$t[5]$t[3]$t[1]$t[0]$t[2]$t[4]";
                $req = "UPDATE `sys_utilisateur` SET `code_sec` = '$code',heure_conx=now() WHERE `sys_utilisateur`.`id` = '$_POST[id_utilisateur]'; ";						
				Chado::$conx->exec($req);
                header("location://bulletin.delardc.com/send-mail?sujet=CODE SECRET&message=Le code SECRET EST:<h2>$code</h2>&dest=$lg&red=dgradru.delardc.com?check=ok");
		    }
		}
		else{
			$_SESSION["connected"] = "NO";
			$_SESSION["message"] = "Echec Authentification";
		}
		// var_dump($_SESSION);
		// if(!isset($_SESSION["user"]["password"])or $_SESSION["user"]["password"]!="lumbu")header("location: /index.php");
	}
	else if(isset($_POST["bt_deconx"])){
		$_SESSION["connected"] = "NO";
		$_GET = $_SESSION = array();
		
		// require_once("generer_ficher.php");
		try{
			$m = ceil(date("i")/6);
			$file = "backUp_dbb/dbb_du_".date("j_m_Y").".sql";
			if (!file_exists($file)) {
				exec("C:\wamp\bin\mysql\mysql5.6.12\bin\mysqldump --user=root --password=dgrad2015 --host=localhost dgrad_15_06_2016 > $file");
				// exec("C:/wamp/bin/mysqldump --user=root --password=dgrad2015 --host=localhost dgrad > $file1");
			}	
		}
		catch(Exception $e){echo "Echec: " . $e->getMessage();}
		// Redirection après déconnexion
		header("Location: security/index.php");
		exit();
	}
	// if(isset($_POST["bt_attribuer_carnet"]))attribuer_carnet();
?>
		<title>SNP DGRAD</title>
		<link href='maia.css' rel='stylesheet'>
		<script src="asset/js/jquery.js"></script>
		<link href='asset/js/select2.min.css' rel='stylesheet'>
		<link hreff='asset/js/bootstrap.min.css' rel='stylesheet'>
		<link href='pagination_style.css' rel='stylesheet'>
		<?php Chado::script(); ?>
		<script type='text/javascript' src='autocomplete.js'></script>
		<script srcc="asset/js/jquery.min.js"></script>
		
	
	<?php
	// STYLE RESERVE AU BUREAU REC
	$pwd = isset($_SESSION['user'])?strtoupper($_SESSION['user']["password"]):"-";
    	if(in_array($pwd,array("KEITAJ","MIK-1234"))){ ?>
	<style>
	#bt_lancer_ajt_t_note_actes {
  		display: none;
	}
	#id_div_liste_NoteActe input[name="bt_lancer_mod_t_note_actes"],
	#id_div_liste_NoteActe input[name="bt_detail_t_note_actes"],
	#id_div_liste_NoteActe input[name="bt_lancer_sup_t_note_actes"]  {
  		display: none;
	}
	</style>
	<?php }
 
 ?> 
	
	<style>
	.border_red td {
		border-top: solid 2px red;
	}
	
	
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
		.text-primary { color: #007bff !important; }
		.bg-white { background-color: #ffffff !important; }
/* Tu peux personnaliser ici pour tes besoins précis */

	
	</style>
	</head>
	<?php if(true OR (isset($_GET["c"])and $_GET["c"]=="ok")){ ?>
	
	<body>
	<!--p style="background:#ff0000aa;color:#f00;text-align:center">your hosting plan has been renewed for 2 years</p-->
	<span id="r_entete_snp" style="width:10px;float:left;display:none"><a href='#xx'>&raquo;</a></span>
		<div class="maia-util" style='height:40px' id='entete_snp'>
         <span style="font-size:20px;font-weight:bold">DGRAD-SNP / <span style='color:blue' ><?php echo isset($_GET["class"])?"$_GET[class]":"Accueil"?></span></span> <span id="linck_to_print"><a href='#xx'>Print</a></span>
		 <?php echo isset($_SESSION["db_annee"])?$_SESSION["db_annee"]:""?>
		<span id="loading" style="color:red;display:none" >Chargement en cours... <img src="loader.gif" style="width:15px"/></span>
		 <p style="width:auto;float:right;margin:0;padding:0" >O.O.
			<input id="check_to" title="Cochez et Considérer uniquement les notes avec O.O" type="checkbox" style="width:">
			<select style="width:90px" id="id_antenne" ><option value="">Ressort</option><?php echo Commune::options("  ",0); ?></select>
			<select style="width:120px" id="id_service" ><option value="">Service</option><?php echo Service::options("",0); ?></select>
			<input id="date_ordo1" placeholder="Date Ordo1" type="date" value="<?php echo date("Y-m") ;?>-01" style="widthh:95px">
			<input id="date_ordo2" placeholder="Date Ordo2" type="date" value="<?php echo date("Y-m-d") /* "2016-12-31"*/;?>" style="widthh:95px">
			<input id="dt_save" title="Cochez et Considérer la date d'enregistrement plutôt que celle de dépot" type="checkbox" style="width:">
            
        </p>
        </div>
		<div class='maia-nav maia-complex' id='maia-nav-x' role='navigation' style='margin-bottom:10px'>
		
		<div class='maia-aux' style='max-width: 96%;'>
		    
		    
		
		
			<ul class='paginationn'>
		<?php $class = isset($_GET["class"])?"$_GET[class]":""; ?> 
		<?php if(!isset($_GET['chado_back_office'])or $_GET['chado_back_office']!=1){ ?><li><a href='?' class="<?php echo $class==""?"active":""?>">Accueil</a></li>
		<li style='float:right'><a href='?chado_back_office=1' >Back Office</a></li>
		<?php require_once("menu.php"); ?>
		<!-- controle d'accès au back offiche -->
	<?php } else if(isset($_GET['chado_back_office'])and $_GET['chado_back_office']==1){ ?>
		<li><a href='?chado_back_office=1' class="<?php echo $class==""?"active":""?>">Accueil</a></li>
			
			<?php require_once("menu_back.php"); ?>
			<li style='float:right'><a href='?' >Front Office</a></li>
		<?php } ?>
		</ul>
		
		</div>
		</div>
		<div class="page" id='id_liste_classes' style="width:100%;margin:auto;min-height:550px">
				<div id='id_zone_rslt_msgScript' ></div>
		<?php
			
			if(isset($_SESSION["connected"])and $_SESSION["connected"]=="OK" and (isset($_GET["class"])and $_GET["class"]!="")){
			if(isset($_GET["class"])and $_GET["class"]=="Actes"){
				$o = new Actes(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Assujetti"){
				$o = new Assujetti(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Banque"){
				$o = new Banque(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Carnet"){
				$o = new Carnet(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="CarnetAttribuer"){
				$o = new CarnetAttribuer(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="CarnetLot"){
				$o = new CarnetLot(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Commune"){
				$o = new Commune(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="ComServ"){
				$o = new ComServ(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Note"){
				$o = new Note(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="NoteActe"){
				$o = new NoteActe(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="NoteActesPayer"){
				$o = new NoteActesPayer(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Releve"){
				$o = new Releve(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Service"){
				$o = new Service(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="User"){
				$o = new User(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Utilisateur"){
				$o = new Utilisateur(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Visites"){
				$o = new Visites(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="UserClasse"){
				$o = new UserClasse(); 
				$o->liste(true);
			}
			else if(isset($_GET["class"])and $_GET["class"]=="Classe_in_Code"){
				$o = new Classe_in_Code(); 
				$o->liste(true);
			}
			}
			else {
			?>
			<div style='text-align:center'>
        			<?php
        			if(isset($_SESSION["connected"]) and $_SESSION["connected"] == "OK")
        				echo "<form method='POST'><input type='submit' name='bt_deconx' value='DéConneter'/></form>";
        			else if(isset($_SESSION["connected"]) and $_SESSION["connected"] == "ATTENTE"){
        			    echo "<h3 style='color:blue'>UN CODE A ETE ENVOYE DANS VOTRE BOITE MAIL, TAPEZ-LE ICI POUR VALIDER VOTRE IDENTITE</h3>";
        			    echo "<form method='POST'><input type='password' name='code' placeholder='code secret'/><input type='submit' name='bt_second_conx' value='VALIDER CODE SECRET'/></form>";
        			}
        			else if(isset($_SESSION["connected"]) and $_SESSION["connected"] == "INCORRECT"){
        			    echo "<h3 style='color:red'>$_SESSION[message]</h3>";
        			    echo "<form method='POST'><input type='password' name='code' placeholder='code secret'/>
        			    <input type='submit' name='bt_second_conx' value='VALIDER CODE SECRET'/></form>";
        			}
        			else if(isset($_SESSION["connected"]) and $_SESSION["connected"] == "EXPIRE"){
        			    echo "<h3 style='width: 274px;margin: auto;background: red;color: white;font-size: 18px;font-weight: bolder;'>$_SESSION[message]</h3>";
        			    echo "<form method='POST'><input style='width: 274px;color: blue;font-weight: bold;' type='submit' name='bt_resend_code' value='CLIQUEZ ICI POUR RENVOYER LE CODE'/></form>";
        			}
        			else {
        				if(isset($_POST["bt_login"]) and isset($_SESSION["message"]) and ""!=$_SESSION["message"])
        					echo "<h3 style='color:red'>$_SESSION[message]</h3>";
        			    ?>
            			<form method="POST" style='width:auto;margin:auto'>
            			<table style="width:150px;margin: auto;">
            			<caption><h3>CONNECTEZ VOUS ICI.</h3></caption><tr><th>Login:</th><td><input name="login" value="-" style="width:150px"/></td></tr>
            			<tr><th>Password:</th><td><input type="password" name="password" value="" style="width:150px"/></td></tr>
            			<tr><th>Annee:</th><td>
            				<select name="annee" value="" style="width:150px">
            					<option value="3" selected >2022</option>
            					<option value="2"  >2018 A 2021</option>
            					<option value="1">2015 A 2017</option>
            				</select>
            			</td></tr>
            			<tr><td></td><td><input type="submit" name="bt_login" value="Connexion"/></td></tr>
            			</table>
            			</form>
        			
        			        <?php 
        			    } ?>
    			</div>
    			<p>L’analyste en informatique est celui qui, d’une part, évalue les besoins informatiques et techniques des utilisateurs et qui, d’autre part, veille à l’implantation et à l’évaluation des systèmes informatiques. Selon les besoins informatiques, que ce soit à propos de la performance du système ou de la correction d’une défaillance, l’analyste en informatique est appelé à chercher des solutions et à assurer la qualité de ses applications. Il peut endosser différents rôles : analyste fonctionnel, analyste technique et analyste de systèmes.</p>
    			
    			<h3> Tâches et responsabilités</h3>
    			<ul>
    				<li>Analyser les besoins informatiques et techniques de l’entreprise.</li>
    				<li>Participer au développement, à la mise en place et à la réalisation des stratégies en matière informatique.</li>
    				<li>Conseiller les personnes responsables de l’informatique dans l’entreprise.</li>
    				<li>Veiller à la qualité des produits et des services informatiques selon les standards, les normes et les procédures en vigueur.</li>
    				<li>Proposer et appliquer des solutions aux défaillances informatiques.</li>
    				<li>Analyser les coûts des systèmes informatiques, l’utilisation de ces systèmes et les solutions proposées pour l’optimisation.</li>
    			</ul>
    			<h4>Contact: 089 89 200 46, 097 2 44 44 66</h4>
    		
		        <?php 
		    } ?>	
		<hr/>
		<p>ChelDap vous remercie d'avoir accepté d'utiliser son produit; Merci</p>
		</div>
		<iframe name='iframe' style='width:100%;display:none' ></iframe>
		<div id='retourHtmlAjaxLIST' style='width:1%;display:none' ></div>
	
<script type="text/javascript" src="script.js"></script>
<script type="text/javascript" src="jquery.min.js"></script>
		<script src="asset/js/select2.full.min.js"></script>
<script type='text/javascript'>
    
		function select_2_assujetti(){
		    $('#id_assujetti_ordo').select2();
		    $('#opt_actes_ordo').select2();
		    console.log("ok");
		}
	$(function(){
	    //$('.select2').select2()
	    
		$('#id_liste_classes').on('click','.div-mois table tfoot',function(){
			// alert('chad')
			if($(this).parent().find('tbody').css('display')=='none' ){
				$(this).parent().find('tbody').slideDown(500);
				$(this).parent().find('thead').show();
			}
			else{
				$(this).parent().find('tbody').slideUp(500);
				$(this).parent().find('thead').hide();
			}
			
		})
	
		$('#id_liste_classes').on('click','.div-mois .div-mois-head, .div-mois .div-mois-foot',function(){
			// console.log($(this).parent().find('table'))
			if($(this).parent().find('.div-mois-head').css('display')=='none' ){
				$(this).parent().find('.div-mois-head').slideDown(500);
				$(this).parent().find('.div-mois-body').slideDown(500);
			}
			else{
				$(this).parent().find('.div-mois-head').slideUp(500);
				$(this).parent().find('.div-mois-body').slideUp(500);
			}
			
		})
		$('#id_liste_classes').on('submit','#ajouter_assujetti',function(event){
			event.preventDefault();
			ajouter_assujetti();
			return false;
		})
		function ajouter_assujetti(){
			var nom = $('#nom_assujetti').val();
			var nif = $('#nif_assujetti').val();
			var adresse = $('#adresse_assujetti').val();
			
			var url = "traitement_sup_2020.php?action=new-assu&nom="+nom+"&nif="+nif+"&adr="+adresse+"&";
			$.get(url, function(data) {
				var t = data.split('|')
				console.log(data.msg,data.result)
				if(t[1] != 'success' && t[1] != 'existe')
					alert(t[0])
				else {
					if(t[1] == 'existe')
						alert("Cet assujetti existe deja ")
					$('#ajouter_assujetti_cap').html((t[1] == 'existe'?"Cet assujetti existe deja ":"")+"<span style='font-size:20px'>"+nom+" "+nif+" "+adresse+" "+"</span>")
				}
			})
			// return confirm('Voulez-vous supprimer ?')
			return false;
		}
	})
		// encaissement journalier au recouvrement
		$("#id_link_encaiss_jr").click(function(){
			var date1 = $("#date_ordo1").val();
			var date2 = $("#date_ordo2").val();
			var url = "ajax_json.php?list=encaisse_jr&drt1="+date1+"&drt2="+date2+"&id_service="+$("#id_service").val()+"&id_antenne="+$("#id_antenne").val()+"";
			$.getJSON(url, function(data) {
				if(data.result=="success"){
					var li = "";var date="";var total=0, total_g=0;
					// <!--thead><tr><th>ASSUJETTI</th><th>SECTEUR</th><th>N.P.</th><th>MONTANT</th><th>ACTE GEN.</th><th>BANQUE</th></tr></thead-->
					var table = "<tbody>";
					$.each(data.json,function(key,val){
						if(val.dp!=date){
							if(date!="")
								table+="<tr><th colspan='7' style='text-align:center' >Paiement total du: "+date+": "+total+"</th></tr>";
							table+="<tr><th colspan='7' style='text-align:center' >Paiement du: "+val.dp+"</th></tr><tr><th>ASSUJETTI</th><th>SECTEUR</th><th>N.P.</th><th>MONTANT</th><th>ACTE GEN.</th><th>BANQUE</th></tr>";
							total_g+=total;
							total=0;
							date = val.dp;
						}
						total+=parseInt(val.mt_p);
						table+="<tr><td title=\"Adresse: "+val.ad+"\">"+val.asj+"</td><td>"+val.s+"</td><td>"+val.n+"</td><td title=\"montant taxé: "+val.mt_o+"\">"+val.mt_p+"</td><td>"+val.a+"</td><td>"+val.bq+"</td></tr>";
					});
					table+="</tbody><tfoot><tr><th colspan='7' style='text-align:center' >Total général d'encaissement du "+date1+" AU "+date2+": "+total_g+" Fc</th></tr></tfoot>";
					$("#id_liste_classes").html(table);
				}else alert(data.message)
			});
		});
		// image des relevé au recouvrement
		var MOIS = ["","DE JANVIER","DE FEVRIER","DE MARS","D'AVRIL","DE MAI","DE JUIN","DE JUILLET","D'AOUT","DE SEPTEMBRE","D'OCTOBRE","DE NOVEMBRE","DE DESCEMBRE"];
		$("#id_link_img_releve").click(function(){
			var date1 = $("#date_ordo1").val();
			var date2 = $("#date_ordo2").val();
			var dt_save = document.getElementById("dt_save").checked;// $("#dt_save").is(":checked");
			var url = "ajax_json.php?list=img_releve&drt1="+date1+"&drt2="+date2+"&id_service="+$("#id_service").val()+"&id_antenne="+$("#id_antenne").val()+"&dt_save="+dt_save;
			$.getJSON(url, function(data) {
				if(data.result=="success"){
					var li = "";var date="",m="";var total=0, total_g=0;
					var table = "<tbody>";
					$.each(data.json,function(key,val){
						classe="";
						if(date!=val.dp){
							date = val.dp;
							classe="border_red";
						}
							
						if(val.m!=m){
							if(m!="")//mt_o mt_p bq dp m i nbr_n
								table+="<tr><th colspan='7' style='text-align:center' >Paiement total du mois "+MOIS[val.m]+": "+total+"</th></tr>";
							table+="<tr><th colspan='7' style='text-align:center' >Paiement du mois  "+MOIS[val.m]+"</th></tr><tr><th>BANQUE</th><th>DATE</th><th>NBR.NP</th><th>MONTANT ORDO</th><th>MONTANT REC.</th><th>RELEVE</th></tr>";
							total_g+=total;
							total=0;
							m = val.m;
						}
						total+=parseInt(val.mt_p);
						table+="<tr class='"+classe+"'><td>"+val.bq+"</td><td>"+val.dp+"</td><td>"+val.nbr_n+"</td><td title=\"montant taxé (ordonnancé): "+val.mt_o+"\" style='text-align:right;color:red' >"+chiffre(val.mt_o)+"</td><td title=\"montant payé (recouvré): "+val.mt_p+"\" style='text-align:right;color:green' >"+chiffre(val.mt_p)+
						"</td><td>"+((val.i!="")?("<a href='#"+val.i+"' onclick=\"window.open('test/releves_traiter/"+val.i+"')\" title='voir le relevé' >"+val.ns+"</a>"):"")+"</td></tr>";
					});
					table+="<tr><th colspan='7' style='text-align:center' >Paiement total du mois de: "+MOIS[m]+": "+chiffre(total)+"Fc</th></tr>";
					total_g+=total;
					table+="</tbody><tfoot><tr><th colspan='7' style='text-align:center' >Total général d'encaissement du "+date1+" AU "+date2+": "+total_g+" Fc</th></tr></tfoot>";
					$("#id_liste_classes").html("<caption><h3>Paiement total du mois "+MOIS[m]+": "+chiffre(total)+"Fc</h3></caption>"+table);
				}else alert(data.message);
			});
		});
		function acceder_releve(img){
			
		}
		function getFormServer(url,id_element){
			alert(url);
			document.getElementById(id_element).innerHTML = connectURL(url);
		}
		function filtre_opt_actes_ordo(){
			var el = document.getElementById("filtre_opt_actes_ordo");
			var parm = document.getElementById("source_opt_acte_ordo_parm").value;
			var fonc = document.getElementById("source_opt_acte_ordo_fonc").value;
			document.getElementById("opt_actes_ordo").innerHTML = connectURL('ajax.php?operation=filtrer_acte_ordo&src_parm='+parm+'&src_fonc='+fonc+'&filtre='+el.value);
		} /* */
		/* la fonction envoi par GET un paramettre et sa valeur au serveur [id_service|id_antenne]
		 * et récupère un combo, sois d'acte gén, soit de service gén.
		 * le parametre à envoyer est dans parmToSend
		 * la valeur du parametre à envoyer est dans l'élément html dont l'id est dans valOfParmToSend
		 * le text renvoyé du serveur est écrit dans l'élément html dont l'id est dans 
		 *****************************************************************************/
		function getOptions(parmToSend,valOfParmToSend,writeRsltHere){
			if(parmToSend==" "){
				filtre_opt_actes_ordo();
				return;
			}
			else if(parmToSend=="assu"){
				filtrer_assuj_ordo();
				return;
			}
			if(valOfParmToSend==""){alert("xx");
				var el = document.getElementById(valOfParmToSend);
				document.getElementById(writeRsltHere).innerHTML = connectURL('ajax.php?'+parmToSend+'='+el.value);
			}
		}
		function getFormSupNoteActe(id_note,id_note_acte){
			document.getElementById('htmk_form_sup_note_acte').innerHTML = connectURL('ajax.php?operation=sup_note_acte&id_note_acte='+id_note_acte+'&id_note='+id_note);
		}
		// var serv = document.getElementById('opt_serv_antenne');
		// serv.addEventListener("change",getAvtesOptions, false);
		// envoi au serveur des paramètre d'enregistrement de l'acte d'une note d'un releve
		function enreg_note_payee(id_noteacte,id_releve,montant_a_payer,num_note,date_releve) {
			var montant = document.getElementById('montant_acte_payer_'+id_noteacte).value;
			document.getElementById('link_bt_payer_acte_'+id_noteacte).innerHTML = connectURL('ajax.php?operation=enreg_note_payee&id_noteacte='+id_noteacte+'&id_releve='+id_releve+'&montant='+montant+'&montant_a_payer='+montant_a_payer+'&num_note='+num_note+'&date_releve='+date_releve);
		}
		//
		
		function extrait_role(Liste_id){
			document.getElementById('div_extrait_de_role').innerHTML = connectURL('ajax.php?operation=extrait_role&Liste_id='+Liste_id);
		}
		function extrait_role_assu(id_ass){
			window.open("tableau.php?id_ass="+id_ass);
		}
		function remplacer_note(en_remplacement_de){
			//en_remplacement_de
			// alert("chado");
			var num_note = document.getElementById(en_remplacement_de).value;
			if(num_note!=""){
				var r = connectURL('ajax.php?operation=remplacer_note&num_note='+num_note);
				if(r!=''){
					document.getElementById(en_remplacement_de).value='';
				} /* */
				document.getElementById('erreur_en_remplacement_de').innerHTML = r;
			}
			
		}
				
		function return_extrai_role(){
			getFormServer('ajax.php?operation=return_extrai_role&list_num_note='+document.getElementById('list_num_note').value,'div_extrait_de_role');
		}
		
		
		// click sur le lien de journal
		/* */ 
		document.getElementById("linck_to_print").addEventListener("click", function (){
			document.getElementById("maia-nav-x").style.display = 'none';
			document.getElementById("entete_snp").style.display = 'none';
			document.getElementById("r_entete_snp").style.display = 'inline';
		}, false);
		document.getElementById("r_entete_snp").addEventListener("click", function (){
			document.getElementById("maia-nav-x").style.display = '';
			document.getElementById("entete_snp").style.display = '';
			document.getElementById("r_entete_snp").style.display = 'none';
		}, false);
		// alert(node.innerHTML);
		/* function assu_autocomplet(){			
			new autocomplete(document.getElementById("id_assujetti_ordo"));
		} */
		$(".link_to_releve").click(function(){
			alert("")
			window.open()
		});
	function chiffre($br)
	{
		$s = "";
		$bb = (""+$br).split(".");//explode(".",$br);
		$br = parseInt($bb[0]);
		if($br<0){$br = -1*$br;$s = "-";}
		$v = ""+$br;
		$r = "";
		for($i=($v.length-1),$j=0; $i!=-1; $i--,$j++)
		{
			$r = ($j%3 == 0)? ($v[$i]+""+(  ($j == 0)?  "":".")+""+$r):($v[$i]+""+$r);
		}	
		return (($bb[1]!=undefined)?($s+""+$r+","+$bb[1]):($s+""+$r+","+"00"));
	}
	// alert();
		
	</script>
	<!--select id="id_assujetti_ordod" ><option>kas</option><option>chado</option></select-->
	
	
</body>
	<?php }else{ ?>
	
        			    <p style="text-align:center">
        			        <img src="namecheap.png" style="width:auto;margin:auto;">
        			    </p>
        			    <?php } ?>
</html>