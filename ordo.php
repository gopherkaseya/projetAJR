<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:175px;}
		#table_ordonnancer_note input{width:90%}
	</style>
	
	
	<div id='div_ordonnancement' style='width:100%' >
		<?php echo "";?>
		<form method='POST' action='traitement_sup.php' target='iframe' id="" style='border:solid 0px;margin:0;display:inline'>
			<input type="text" name="num_note" class="form-control" title="N° Note"  placeholder="N° Note" required style="padding: 8px;" />
			<input type="submit" name="bt_num_note" class="btn maia-button" style="height:35px" value="Analyser" />
		</form>
		<form method='POST' action='traitement_sup.php' target='iframe' style='border:solid 0px;margin:0;display:inline'>
			<input type="text" name="num_note" class="form-control" title="N° Note"  placeholder="N° Note" required style="padding: 8px;width:65px" />
			<input type="submit" name="bt_form_annuler_note" class="btn maia-button" style="height:35px" value="Annuler Note" />
		</form>
		<?php echo Assujetti::bt_ajout(Assujetti::$bt_class,'A. Assujetti',false)."<div id='".Assujetti::$id_div_crud."' ></div>";?>
		
		<div id="id_form_annuler_note" style="" ></div>
		<div style="width:100%;display: block;min-height:2px;float:left">
			<form method='POST' action='traitement_sup.php' target='iframe' style='widtth:100%;float:left'>
				<div id='div_form_ordo_notee' style='float:left;margin-right:20px;border-right:solid 1px;padding-right:10px' ></div>
				<div id='div_form_ordo' style='float:left;margin-right:20px;' ></div>
			</form>
			
		</div>
		<?php
			$c = new Note();
			echo "<div class='alert tip'>";Note::div_notification();echo "</div>";
			// $c->liste(true);
			Note::div_html_crud();
		?>
		</div>
		<br/>
		
		<div id="id_div_crud_Assujetti" style="display: inline-block;">
			<form id="ajouter_assujetti" enctype="" style="">
				<input type="hidden" name="class" value="Assujetti">
				<table>
					<caption id="ajouter_assujetti_cap" style="background-color: #829AA8;color: white;"><b>Enregistrement Assujetti</b></caption>
					<tbody>
						<tr><td><label for="nom_assujetti">Assujetti</label></td><td>:</td><td><input type="text" value="" title="Assujetti" placeholder="Assujetti" id="nom_assujetti" name="nom_assujetti" required="" class="form-control " style="width:250px"></td></tr>
						<tr><td><label for="nif_assujetti"> NIF Assujetti</label></td><td>:</td><td><input type="text" value="" title="NIF Assujetti" placeholder="NIF Assujetti" id="nif_assujetti" name="nif_assujetti" class="form-control " style="width:250px"></td></tr>
						<tr><td><label for="adresse_assujetti">Adresse Assujetti</label></td><td>:</td><td><input type="text" value="" title="Adresse Assujetti" placeholder="Adresse Assujetti" id="adresse_assujetti" name="adresse_assujetti" class="form-control " style="width:250px"></td></tr>
						<tr><td></td><td></td><td>
						<input type="submit" class="btn  btn-primary" name="bt_ajt_t_assujetti" value="Ajouter" style=""><input type="reset" class="btn " name="bt_resset_t_assujetti" value="Annuler" style="float:right">
						</td></tr>
					</tbody>
				</table></form>
				<a href="assujetti_doublons.php" onclick="" >Fusionner assujetti</a>
			<script>
				window.top.window.cache1_et_affiche2('retourHtmlAjaxLIST','id_div_crud_Assujetti');
			</script>
		</div>
		
	</div>
	<div id="div_rap_ordonnancement" style="display:none">
	<?php //(new NoteActes())->rapport_complet(); //NoteActes::div_html_rapport(); ?></div>

	
	
	<script>
		document.getElementById("bt_form_annuler_note").addEventListener("click", function (){alert("");
			new askServeur1('ajax.php?operation=form_annuler_note&','id_form_annuler_note');
		}, false);
		
		$('#ajouter_assujetti').submit(function(event){
			event.preventDefault();
			ajouter_assujetti();
		})
		
		
		//$("#test").select2();
		
	</script>
	