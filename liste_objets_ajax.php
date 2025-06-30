 <meta http-equiv="content-type" content="text/html; charset=UTF-8">
 <style>thh, tdd {
    border: 1px solid #000;
    padding: 4px 12px;
    vertical-align: top;
}</style>
 <?php 
	session_start();
	require_once('model/classes.php');
	Chado::$conx = Connexion::GetConnexion();
	// $v = array("","CB.Ordo. & Etud.");
	$b="<b style='color:blue'>";
	$_com = (isset($_GET["id_ante"]) and $_GET["id_ante"]!="")?$b.(Commune::objet($_GET["id_ante"])["commune"])."</b>":"";
	$_ville = "";//(isset($_GET["id_vill"]) and $_GET["id_vill"]!="")?$b.(Ville::objet($_GET["id_vill"])["ville"])."</b>":"";
	$_service = (isset($_GET["id_serv"]) and $_GET["id_serv"]!="")?$b.(Service::objet($_GET["id_serv"])["service"])."</b>":"";
	// $_ville = (isset($_GET["id_vill"]) and $_GET["id_vill"]!="")?$r[$_GET["id_ante"]]:"";
	$cle = "dt_ord1";$dt_ord1 = (isset($_GET[$cle]) and $_GET[$cle]!="")?($_GET[$cle]):false;
	$cle = "dt_ord2";$dt_ord2 = (isset($_GET[$cle]) and $_GET[$cle]!="")?($_GET[$cle]):false;
	$cle = "dt_save";$dt_save = (isset($_GET[$cle]) and $_GET[$cle]!="")?("date_save"):"date_depot";
	$cle = "check_to";$check_to = (isset($_GET[$cle]) and $_GET[$cle]!="")?("TO"):"";
	// $dt_save="date_depot";
	$_WHERE = ((isset($_GET["id_ante"]) and $_GET["id_ante"]!="")?" and co.id = '$_GET[id_ante]'":"")."
	".((isset($_GET["id_serv"]) and $_GET["id_serv"]!="")?" and ser.id = '$_GET[id_serv]'":"").((isset($_GET["check_to"]) and $_GET["check_to"]!="")?" and no.note_to = 1 ":"");
	
	$mois=array("01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin","07"=>"Juillet","08"=>"Août","09"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Décembre");
	if(!is_valide_date($dt_ord1) or !is_valide_date($dt_ord2))
		echo"<h1 style='color:red' >".(("$dt_ord1$dt_ord2"=="")?"Indiquer la période SVP!":"Période Invalide!!!")."</h1>";
	else {
		$n_mois = $mois[explode("-",$dt_ord1)[1]];
		// require_once('num_pages.php');
		// $defautl_taille = 20;
			
		require_once("liste_objets.php");
	}
	