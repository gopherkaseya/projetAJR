<style>
		.acte_p{margin:0px;padding:0px;}
		.acte_p label{width:75px;float:left;font-weight:bold}
		.acte_p select,.acte_p input{width:175px;}
		#table_ordonnancer_note input{width:100%}
	</style>
	<div id='div_ordonnancement' style='width:100%' >
		<form method='POST' action='traitement_sup.php' target='iframe' style='border:solid 0px;text-align:center;margin:0'>
			<?php
				$where = " where rlv.is_deleted = 0 and en_cours_dedition = 1 ";
				$r = Releve::liste_objet($where," order by rlv.id DESC ");
				$_SESSION["snp"]["nbr_note_max"] = 0;$_SESSION["snp"]["id_releve"] = 0;
				if(isset($r[0])){
					$r = $r[0];
					$_SESSION["snp"]["nbr_note_max"] = $r["nbr_note"];
					$_SESSION["snp"]["id_releve"] = $r["id"];
					echo "<h2>Relevé en cours: <strong style='color:red'>".(isset($r["nom_banque"])?$r["nom_banque"]:"")."</strong> payé le :<strong style='color:red'>".(isset($r["date_paiement"])?$r["date_paiement"]:"")."</strong></h2>
					
					<input type='text' name='num_note' class='form-control' title='N° Note'  placeholder='N° Note' required style='padding: 8px;' />
					<input type='submit' name='bt_num_note_releve' class='btn maia-button' style='height:35px' value='Payer La Note' />";
						
				}
				else {$_SESSION["snp"]["id_releve"]=0;
					echo "<p style='color:red'><br/><br/><br/>Aucun relevé n'est en cours d'édition, vous ne pouvez donc pas faire d'encaissement; et si vous voulez en faire, il faudra mettre en cours d'édition le relevé de la note à encaisser</p>";
				}
			?>
			
		</form>
		<div style="width:100%;display: block;min-height:2px;float:left">
			<form method='POST' action='traitement_sup.php' target='iframe' style=''>
				<div id='id_div_crud_Note' style='' ></div>
				<div id='div_operation_releve' style='' ></div>
			</form>
		</div>
	</div>
	
	