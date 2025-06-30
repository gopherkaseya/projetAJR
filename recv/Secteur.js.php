
<script type='text/javascript'>
	$(document).ready(function () {
		var TDIV = '#t_service_genTableContainer';
		var Page = 'Secteur.php';
		var Page1 = 'Note.php';
		var apurer = <?php echo (isset($_GET["apurer"])and $_GET["apurer"]!="")?$_GET["apurer"]:0;?>;
		var SelectedRowList = '#t_service_genSelectedRowList';
		$(TDIV).jtable({
			title: 'liste de tous les secteurs et antennes',
			selecting: true, //Enable selecting
			multiselect: true, //Allow multiple selecting
			selectingCheckboxes: true, //Show checkboxes on first column
			paging: true,
			pageSize: 15,
			sorting: true,
			defaultSorting: 'nom_ser_gen ASC',
			actions: {
				listAction  : Page+'?action=list'
				// ,createAction: Page+'?action=create',
				// updateAction: Page+'?action=update',
				// deleteAction: Page+'?action=delete'
			},
			fields: {
				id: {
					key: true,
					create: false,
					edit: false,
					list: false
				},
				//CHILD TABLE DEFINITION FOR ''
				Notes: {
					title: '',
					width: '1%',
					sorting: false,
					edit: false,
					create: false,
					display: function (Datat_service_gen) {
						//Create an image that will be used to open child table
						var $img = $('<img src="image/list_metro.png" title="Notes du secteurs" />');
						//Open child table when user clicks the image
						$img.click(function () {
							$(TDIV).jtable('openChildTable',
								$img.closest('tr'),
								{
									title: ' Liste de tout le reste à recouvrer dans '+Datat_service_gen.record.nom_ser_gen,
									selecting: true, //Enable selecting
									multiselect: true, //Allow multiple selecting
									// selectingCheckboxes: true, //Show checkboxes on first column
									paging: true,
									pageSize: 10,
									sorting: true,
									defaultSorting: 'num_note ASC',
									actions: {
										listAction: Page1+'?action=ResteRecouv&id=' + Datat_service_gen.record.id+'&apurer='+apurer
										// listAction: Page1+'?action=list&id=' + Datat_service_gen.record.id+'&apurer=1'
										// createAction: Page1+'?action=create&id=' + Datat_service_gen.record.id,
										// updateAction: Page1+'?action=update&id=' + Datat_service_gen.record.id,
										// deleteAction: Page1+'?action=delete&id=' + Datat_service_gen.record.id
									},
									fields: {
										id_secteur: {
											type: 'hidden',edit: false,
											defaultValue: Datat_service_gen.record.id
										},
										id: {key: true,create: false,edit: false,list: false},
										// id_acte:{title:'Acte',options:'Options.php?action=acte',width: ''},
										// id_secteur:{title:'Secteur',options:'Options.php?action=secteur',width: ''},
										// date_ordo:{title:'Dt.Ord',type:'',width: ''},
										// frequence:{title:'Freq',type:'',width: ''},
										// apurer:{title:'Apurée',type:'checkbox',values:{'1':'Oui','0':'Non'},defaultValue:'0'},
										// date_recouvrement:{title:'Dt.Recv.',type:'',width: ''},
										// banque:{title:'Banque',type:'',width: ''},
										// relancer:{title:'Relancée',type:'checkbox',values:{'1':'Oui','0':'Non'},defaultValue:'0'},
										// date_relance:{title:'Dt.Relance',type:'',width: ''},
										// enroler:{title:'Enrolée',type:'checkbox',values:{'1':'Oui','0':'Non'},defaultValue:'0'},
										// note_date_role:{title:'Dt.Rôle',type:'',width: ''},
										// invalide:{title:'Inval',type:'checkbox',values:{'1':'Oui','0':'Non'},defaultValue:'0'},
										// date_enregistrement:{title:'Dt.Enreg',type:'',width: ''},
										// id_secteur_detenteur:{title:'Sect.Detent',options:'Options.php?action=secteur',width: ''},
										// type_invalidation:{title:'Typ.Invl',type:'',width: ''},
										// explication_invalidation:{title:'Rai.Invl',type:'',width: ''},
										// date_invalidation:{title:'Dt.Invl',type:'',width: ''},
										nom_assujetti:{title:'Assujetti',type:'',width: '30%'},
										adresse_assujetti:{title:'Adresse',type:'',width: '30%'},
										num_note:{title:'N°',type:'',width: '3%'},
										date_depot:{title:'Date.Dep',type:'',width: '3%'},
										montant:{title:'Montant',type:'',width: '3%'},
										note_to:{title:'OBS',type:'',width: '3%'},
										num_bap:{title:'BAP',type:'',width: '3%'},
										montant_bap:{title:'Mont.BAP',type:'',width: '3%'}
									}
								}, 
								function (data) { //opened handler
									data.childTable.jtable('load');
								});
						});
						//Return image to show on the person row
						return $img;
					}
				},
				
				nom_ser_gen:{title:'Service générateur',type:'',width: ''},
				type_serv_gen:{title:'Type service',type:'',width: ''},
				nbr_carenet:{title:'Nombre Carnet',type:'',width: ''}
			},
			//Register to selectionChanged event to hanlde events
			selectionChanged: function () {
				//Get all selected rows
				var $selectedRows = $(TDIV).jtable('selectedRows');
				$(SelectedRowList).empty();
				if ($selectedRows.length > 0) {
					//Show selected rows
					$selectedRows.each(function () {
						var record = $(this).data('record');
						$(SelectedRowList).append( '<b>ID</b>: ' + record.id + '<br />' );
					});
				} else {
					//No rows selected
					$(SelectedRowList).append('No row selected! Select rows to see here...');
				}
			},
			// événement déclenché lors de l'enregistrement
			rowInserted: function (event, data) {
				// if (data.record.Name.indexOf('Andrew') >= 0) {
					// $(TDIV).jtable('selectRows', data.row);
				// }
			}
		});
		
		//Re-load records when user click 'load records' button.
		var reload = '#t_service_genLoadRecordsButton';
		$(reload).click(function (e) {
			e.preventDefault();
			$(TDIV).jtable('load', {
				Filtre: $('#t_service_genFiltre').val()
				,secteur: $('#t_service_genFiltre_sec').val()
			});
		});
		
		//Load all records when page is first shown
		$(reload).click();
		
		//Delete selected students
		$('#t_service_genDeleteAllButton').button().click(function () {
			var $selectedRows = $(TDIV).jtable('selectedRows');
			$(TDIV).jtable('deleteRows', $selectedRows);
		});
		
		//Load person list from server
		// $(TDIV).jtable('load');
	});
</script>
