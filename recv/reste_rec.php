
	<?php  include("scriptsEnfant.php");?>
	<title><?php echo (isset($_GET["service"]))?"$_GET[service]":"Reste à recouvrer";?></title>
	<div style="width:98%;margin:auto;">
	<a id='bt_print' href='#x' >Preparer Impression</a>
	<a id='bt_print_retour' style="display:none" href='#x' >Impression-retour</a>
	<div class='filtering'>
		<h3>Liste du reste à recouvrer pas service générateur y compris les antennes</h3>
		<div>
			Filtrage: <input type='text' name='t_service_genFiltre' id='t_service_genFiltre' />
			Secteur: 
			<?php
				include("../model/connexion.php");
				$conx = Connexion::GetConnexion();$url="";
				$date_depot=isset($_GET["date_depot"])?$_GET["date_depot"]:'';
				if(isset($_POST["date_depot"])){
					$date_depot = $_POST["date_depot"];
				}
				if(isset($_GET["service"]))echo "<input type='text' id='t_service_genFiltre_sec' value=\"$_GET[service]\"  />";
				else {
					echo "<select id='t_service_genFiltre_sec' ><option value=''>Choisir</option>";
					$Select = "SELECT id Value ,commune DisplayText FROM t_commune where is_deleted = 0 order by commune ";
					//Get records from database
					$req = $Select;
					$pdo_result = $conx->query($req);
					while($rows	= $pdo_result->fetch()){
						echo "<option value=\"$rows[DisplayText]\">$rows[DisplayText]</option>";
						$url .= "window.open(\"?apurer=0&rest_rec__secteur=0&service=$rows[DisplayText]&date_depot=$date_depot\");";
					}
					echo"</select>";
				}
				
			?>
			<form style='display:inline-block;' method="POST" >
				Date Ordo.: <input type='text' placeholder='aaaa-mm-jj:aaaa-mm-jj' id='t_service_genFiltre_date_ordo' name='date_depot' <?php echo "value=\"".($date_depot==""?(date("Y-m-01").":".date("Y-m-d")):$date_depot)."\" ";?> />
			<input type="submit" name="bt_tous_les_sect" value='All Sectors'/>	
			</form>	
			<select id="somme_par_sec" title="renvoyer les sommes totales par secteur"><option value="">Non</option><option value="oui">Oui</option></select>
			<button type='submit' id='t_service_genLoadRecordsButton'>Charger</button>
		</div>
	</div>
	<div id='t_service_genTableContainer' style=''></div>
	<!--div id='t_service_genSelectedRowList' style=''></div-->
	<!--a id='t_service_genDeleteAllButton' href='#x' >Tout supprimer</a-->
	
	<?php 
		$_GET["rest_rec__secteur"]=0;
		require('ResteRecouvrer.js.php')
	?>
	</div>
	<script>
		$(document).ready(function () {
			<?php			
				if(isset($_POST["bt_tous_les_sect"])){
					echo  $url;
				}
				// if(isset($_GET['chad'])and $_GET['chad']=='all_sec')
				// echo "window.open(\"?apurer=0&rest_rec__secteur=0\");"; 
			?>
			
			$('#bt_print').button();
			$("#bt_print").click(function(){
				$(".jtable-column-header-container").hide('5000');
				$(".filtering").hide('5000');
				$(".jtable-bottom-panel").hide('5000');
				$("#bt_print_retour").show('5000');
				$(this).hide('5000');
			});
			$("#bt_print_retour").click(function(){
				$(".jtable-column-header-container").show('5000');
				$(".filtering").show('5000');
				$(".jtable-bottom-panel").show('5000');
				$(this).hide('5000');
				$("#bt_print").show('5000');
			});
		});
	</script>