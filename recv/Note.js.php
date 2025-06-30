
<script type='text/javascript'>
	var titre = <?php echo isset($_GET["titre"])?"'$_GET[titre]'":"'Notes à la relance'"; ?>;
	$(document).ready(function () {
		var TDIV = '#t_noteTableContainer';
		var Page = 'Note.php';
		//var Page1 = '.php';
		var SelectedRowList = '#t_noteSelectedRowList';
		$(TDIV).jtable({
			title: titre,
			selecting: true, //Enable selecting
			multiselect: true, //Allow multiple selecting
			selectingCheckboxes: true, //Show checkboxes on first column
			paging: true,
			pageSize: 15,
			sorting: true,
			defaultSorting: 'num_note ASC',
			actions: {
				listAction  : Page+'?action=listRelance'
				// ,createAction: Page+'?action=create'
				// ,updateAction: Page+'?action=update'
				// deleteAction: Page+'?action=delete'
			},
			fields: {
				id: {
					key: true,
					create: false,
					edit: false,
					list: false
				},
				num:{title:'N°',sorting:false,type:'',width: '1%'},
				assujetti:{title:'Assujetti',type:'',width: '20%'},
				nom_assujetti:{list:false,title:'Assujetti',type:'',width: '25%'},
				adresse_assujetti:{list:false,title:'Adresse',type:'',width: '10%'},
				nom_ser_gen:{title:'Service-Gén',type:'',width: '10%'},
				nom:{title:'Acte',type:'',width: '8%'},
				num_note:{title:'N°Note',type:'',width: '1%'},
				montant:{title:'Montant',type:'',width: '1%'},
				date_ordo:{title:'Date Ordo.',type:'',width: ''},
				coeff:{list:false,title:'Coeff',type:'',width: '1%'},
				note_to:{title:'note_to',type:'',width: '1%'},
				num_bap:{title:'N°Bap',type:'',width: '1%'},
				montant_bap:{title:'Bap',type:'',width: '1%'}
				// num_note:{title:'num_note',type:'',width: ''},
				// date_depot:{title:'date_depot',type:'',width: ''},
				// frequence:{title:'frequence',type:'',width: ''},
				// apurer:{title:'apurer',type:'',width: ''},
				// date_recouvrement:{title:'date_recouvrement',type:'',width: ''},
				// banque:{title:'banque',type:'',width: ''},
				// relancer:{title:'relancer',type:'',width: ''},
				// date_relance:{title:'date_relance',type:'',width: ''},
				// enroler:{title:'enroler',type:'',width: ''},
				// note_date_role:{title:'note_date_role',type:'',width: ''},
				// invalide:{title:'invalide',type:'',width: ''},
				// date_enregistrement:{title:'date_enregistrement',type:'',width: ''},
				// id_secteur_detenteur:{title:'id_secteur_detenteur',type:'',width: ''},
				// type_invalidation:{title:'type_invalidation',type:'',width: ''},
				// explication_invalidation:{title:'explication_invalidation',type:'',width: ''},
				// date_invalidation:{title:'date_invalidation',type:'',width: ''}
				//CHILD TABLE DEFINITION FOR ''
				/*,Champ: {
					title: '',
					width: '1%',
					sorting: false,
					edit: false,
					create: false,
					display: function (Datat_note) {
						//Create an image that will be used to open child table
						var $img = $('<img src="image/list_metro.png" title=" " />');
						//Open child table when user clicks the image
						$img.click(function () {
							$(TDIV).jtable('openChildTable',
								\img.closest('tr'),
								{
									title: '  '+Datat_note.record.nom,
									actions: {
										listAction: Page1+'?action=list&id=' + Datat_note.record.id,
										createAction: Page1+'?action=create&id=' + Datat_note.record.id,
										updateAction: Page1+'?action=update&id=' + Datat_note.record.id,
										deleteAction: Page1+'?action=delete&id=' + Datat_note.record.id
									},
									fields: {
										id_t_note: {
											type: 'hidden',edit: false,
											defaultValue: Datat_note.record.id
										},
										id: {key: true,create: false,edit: false,list: false},
										champ:{title:'',type:'checkbox',values:{'1':'','0':''},defaultValue:'1'},
										champ:{title:'',options:'Enfant.php?action=options',width: ''}
									}
								}, 
								function (data) { //opened handler
									data.childTable.jtable('load');
								});
						});
						//Return image to show on the person row
						return $\img;
					}
				}*/
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
						$(SelectedRowList).append( '<b>Assujetti: </b>: ' + record.nom_assujetti );
						coeff = record.coeff;
						id_note = /* ","+ */record.id;num_note = record.num_note; nom_assujetti = record.nom_assujetti;
						adresse_assujetti = record.adresse_assujetti;
						secteur = record.nom_ser_gen;
					});
					$("#bt_relancer_note").show();
				} else {
					//No rows selected
					$(SelectedRowList).append('Aucune note selectionnée!');
					$("#bt_relancer_note").hide();
					id_note = "0";
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
		var reload = '#t_noteLoadRecordsButton';
		$(reload).click(function (e) {
			e.preventDefault();
			$(TDIV).jtable('load', {
				Filtre: $('#t_noteFiltre').val()
				,type_note: $('#type_note').val()
				,t_noteMontant: $('#t_noteMontant').val()
				,whereDate_ordo1: $('#whereDate_ordo1').val()
				,whereDate_ordo2: $('#whereDate_ordo2').val()
			});
		});
		$("#bt_textarea_num_note").click(function (e) {
			e.preventDefault();
			$(TDIV).jtable('load', {
				FiltreTextare: $('#textarea_num_note').val()
			});
		});
		
		//Load all records when page is first shown
		// $(reload).click();
		
		//Delete selected students
		$('#t_noteDeleteAllButton').button().click(function () {
			var $selectedRows = $(TDIV).jtable('selectedRows');
			$(TDIV).jtable('deleteRows', $selectedRows);
		});
		
		//Load person list from server
		$(TDIV).jtable('load');
	});
</script>
