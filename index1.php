<?php		
	if(isset($_POST["bt_fixer_regle_rapport"])){
		$deux_mois = time() + 3600*24 *60 ;
		setcookie($_POST["table"],$_POST["champs"],$deux_mois);
		$_COOKIE[$_POST["table"]] = $_POST["champs"];
	}
	?>
<!DOCTYPE HTML><html>
	<head>
		<meta charset="utf-8">
<?php
	session_start();
	require_once('model/classes.php');
	Chado::$conx = Connexion::GetConnexion();
	 
	if(isset($_POST["bt_login"])){
		$lg = $_POST["login"];
		$pwd = $_POST["password"];
		$l = Utilisateur::liste_objet("where login='$lg' and password='$pwd' -- and sys_ut.actif=1  and sys_ut.is_deleted=0 ","");
		if(Utilisateur::$count){
			$_SESSION['user']["password"] = $pwd;
			$_SESSION["connected"] = "OK";
			$_POST["id_utilisateur"]=$_SESSION["id_utilisateur"] = $l[0]["id"];
			$_POST["date_heure_deconx"]='';
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
		}
		else{
			$_SESSION["connected"] = "NO";
			$_SESSION["message"] = "Echec Authentification";
		} 
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
	}
	
	// if(isset($_POST["bt_attribuer_carnet"]))attribuer_carnet();
?>
		<title>Admin</title>
		<link href='maia.css' rel='stylesheet'>
		<link href='pagination_style.css' rel='stylesheet'>
		<?php Chado::script();?>
		<script type='text/javascript' src='autocomplete.js'></script>
	<style>
	.border_red td {
		border-top: solid 2px red;
	}
	</style>
	</head>
	<body><span id="r_entete_snp" style="width:10px;float:left;display:none"><a href='#xx'>&raquo;</a></span>
		<div class="maia-util" style='height:40px' id='entete_snp'>
         <span style="font-size:20px;font-weight:bold">DGRAD-SNP / <span style='color:blue' ><?php echo isset($_GET["class"])?"$_GET[class]":"Accueil"?></span></span> <span id="linck_to_print"><a href='#xx'>Print</a></span>
		 
		<span id="loading" style="color:red;display:none" >Chargement en cours... <img src="loader.gif" style="width:15px"/></span>
		 <p style="width:auto;float:right;margin:0;padding:0" >O.O.
			<input id="check_to" title="Cochez et Considérer uniquement les notes avec O.O" type="checkbox" style="width:">
			<select style="width:90px" id="id_antenne" ><option value="">Ressort</option><?php echo Commune::options("  ",0); ?></select>
			<select style="width:120px" id="id_service" ><option value="">Service</option><?php echo Service::options("",0); ?></select>
			<input id="date_ordo1" placeholder="Date Ordo1" type="text" value="<?php echo date("Y-m-")."01";?>" style="width:95px">
			<input id="date_ordo2" placeholder="Date Ordo2" type="text" value="<?php echo date("Y-m-d");?>" style="width:95px">
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
			else {
				if(isset($_POST["bt_login"]) and isset($_SESSION["message"]) and ""!=$_SESSION["message"])
					echo "<h3 style='color:red'>$_SESSION[message]</h3>";
			?>
			
			<form method="POST" style='width:auto;margin:auto'>
			<table style="width:150px;margin: auto;">
			<caption><h3>CONNECTEZ VOUS ICI.</h3></caption><tr><th>Login:</th><td><input name="login" value="-" style="width:150px"/></td></tr>
			<tr><th>Password:</th><td><input type="password" name="password" value="" style="width:150px"/></td></tr>
			<tr><td></td><td><input type="submit" name="bt_login" value="Connexion"/></td></tr>
			</table>
			</form>
			
			<?php } ?>
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
			<h4>Contact: 086 14 91 971</h4>
		
			
		<?php } ?>	
		<hr/>
		<p>ChelDap vous remercie d'avoir accepté d'utiliser son produit; Merci</p>
		</div>
		<iframe name='iframe' style='width:100%;display:none' ></iframe>
		<div id='retourHtmlAjaxLIST' style='width:1%;display:none' ></div>
	
<script type="text/javascript" src="script.js"></script>
<script type="text/javascript" src="jquery.min.js"></script>
<script type='text/javascript'>
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
		$("#id_link_img_releve").click(function(){
			var date1 = $("#date_ordo1").val();
			var date2 = $("#date_ordo2").val();
			var url = "ajax_json.php?list=img_releve&drt1="+date1+"&drt2="+date2+"&id_service="+$("#id_service").val()+"&id_antenne="+$("#id_antenne").val()+"";
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
								table+="<tr><th colspan='7' style='text-align:center' >Paiement total du mois N° "+m+": "+total+"</th></tr>";
							table+="<tr><th colspan='7' style='text-align:center' >Paiement du mois N° "+val.m+"</th></tr><tr><th>BANQUE</th><th>DATE</th><th>NBR.NP</th><th>MONTANT ORDO</th><th>MONTANT REC.</th><th>RELEVE</th></tr>";
							total_g+=total;
							total=0;
							m = val.m;
						}
						total+=parseInt(val.mt_p);
						table+="<tr class='"+classe+"'><td>"+val.bq+"</td><td>"+val.dp+"</td><td>"+val.nbr_n+"</td><td title=\"montant taxé (ordonnancé): "+val.mt_o+"\">"+val.mt_o+"</td><td title=\"montant payé (recouvré): "+val.mt_p+"\">"+val.mt_p+"</td><td><a href='#x' title='voir le relevé' img='"+val.i+"' >voir</a></td></tr>";
					});
					table+="<tr><th colspan='7' style='text-align:center' >Paiement total du mois N° "+m+": "+total+"</th></tr>";
					total_g+=total;
					table+="</tbody><tfoot><tr><th colspan='7' style='text-align:center' >Total général d'encaissement du "+date1+" AU "+date2+": "+total_g+" Fc</th></tr></tfoot>";
					$("#id_liste_classes").html(table);
				}else alert(data.message)
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
		function enreg_note_payee(id_noteacte,id_releve,montant_a_payer,num_note) {
			var montant = document.getElementById('montant_acte_payer_'+id_noteacte).value;
			document.getElementById('link_bt_payer_acte_'+id_noteacte).innerHTML = connectURL('ajax.php?operation=enreg_note_payee&id_noteacte='+id_noteacte+'&id_releve='+id_releve+'&montant='+montant+'&montant_a_payer='+montant_a_payer+'&num_note='+num_note);
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
	</script>
	<!--select id="id_assujetti_ordod" ><option>kas</option><option>chado</option></select-->
</body>
	
</html>>