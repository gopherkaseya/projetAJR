
	<?php  include("scriptsEnfant.php");?>
	<title>Notes à relancer</title>
	<div style="width:98%;margin:auto;">
	<a id='bt_print' href='#x' >Preparer Impression</a>
	<a id='bt_print_retour' style="display:none" href='#x' >Impression-retour</a>
	<div class='filtering'>
		<form>
			Filtrage: <input type='text' name='t_noteFiltre' id='t_noteFiltre' />
			<input type='text' placeholder='montant > à' id='t_noteMontant' />
			<input type='text' placeholder='Date Ord. 1' id='whereDate_ordo1' />
			<input type='text' placeholder='Date Ord. 2' id='whereDate_ordo2' />
			<select id="type_note">
				<option value="toutes">Toutes les notes</option>
				<option value="nomRelancee">Notes non relancées</option>
			</select>
			<button type='submit' id='t_noteLoadRecordsButton'>Charger</button>
		</form>
		<form>
			Num(s) Note(s): <br/><textarea style="width:85%" placeholder="fournir la liste des numéro des notes, séparées par une virgule" id="textarea_num_note"></textarea>
			<button type='submit' id='bt_textarea_num_note'>Charger</button>
		</form>
	</div>
	<div id='t_noteTableContainer' style=''></div>
	<div id='t_noteSelectedRowList' style=''></div>
	<p id="p_bt_relance">
	<a id='bt_relancer_note' class="button" href='#x' >Relancer</a>
	<input type="text" value="1" id="txt_coeff" style="width:50px;height:30px;font-size:20px;" />
	</p>
	
	<?php require('Note.js.php'); ?>
	
	</div>
	<script>
		var coeff = 0;var id_note = 0;var num_note = 0;var secteur = "";var nom_assujetti = ""; var adresse_assujetti = "";
		$(document).ready(function () {
			$('#bt_relancer_note').click(function(){
				// window.open("tableaurelance.php?id="+id_note+"&coeff="+$("#txt_coeff").val()+"&nom="+nom_assujetti+"&adres="+adresse_assujetti);
				// window.open("tableau.php?id="+id_note+"&coeff="+$("#txt_coeff").val()+"&nom="+nom_assujetti+"&adres="+adresse_assujetti+"&sect="+secteur);
				var TDIV = '#t_noteTableContainer';
				var SelectedRowList = '#t_noteSelectedRowList';
				var $selectedRows = $(TDIV).jtable('selectedRows');
				$(SelectedRowList).empty();
				if ($selectedRows.length > 0) {
					//Show selected rows
					$selectedRows.each(function () {
						var record = $(this).data('record');
						$(SelectedRowList).append( '<b>Assujetti: </b>: ' + record.nom_assujetti );
						coeff = record.coeff;
						id_note = /* ","+ */record.id;num_note = record.num_note; nom_assujetti = record.nom_assujetti;
						adresse_assujetti = record.adresse_assujetti;
						secteur = record.nom_ser_gen;
						if(secteur!=undefined)
						window.open("tableau.php?id="+id_note+"&coeff="+$("#txt_coeff").val()+"&nom="+nom_assujetti+"&adres="+adresse_assujetti+"&sect="+secteur);
					});
					// $("#bt_relancer_note").show();
				}
			
			});
			$('.button').button();
			$('#bt_print').button();
			$("#bt_print").click(function(){
				// $(".jtable-column-header-container").hide('5000');
				$(".filtering").hide('5000');
				$("#p_bt_relance").hide('5000');
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
				$("#p_bt_relance").show('5000');
			});
		});
	</script>