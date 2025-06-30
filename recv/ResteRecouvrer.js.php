
<script type='text/javascript'>
	$(document).ready(function () {
		var TDIV = '#t_service_genTableContainer';
		var Page1 = 'Note.php';
		var apurer = <?php echo (isset($_GET["apurer"])and $_GET["apurer"]!="")?$_GET["apurer"]:0;?>;
		var SelectedRowList = '#t_service_genSelectedRowList';
		$(TDIV).jtable({
			title: ' Liste de tout le reste à recouvrer ',
			selecting: true, //Enable selecting
			multiselect: true, //Allow multiple selecting
			// selectingCheckboxes: true, //Show checkboxes on first column
			paging: true,
			pageSize: 50,
			sorting: true,
			defaultSorting: 'nom_assujetti ASC',
			actions: {
				listAction: Page1+'?action=ResteRecouv&apurer='+apurer
			},
			fields: {
				id: {key: true,create: false,edit: false,list: false},
				num:{title:'N°',type:'',width: '1%'},
				nom_ser_gen:{title:'Secteur',type:'',width: '3%'},
				nom_assujetti:{title:'Assujetti',type:'',width: '20%'},
				adresse_assujetti:{title:'Adresse',type:'',width: '20%'},
				num_note:{title:'N°',type:'',width: '3%'},
				date_depot:{title:'Date.Dep',type:'',width: '3%'},
				acte:{title:'Acte',type:'',width: '15%'},
				montant:{title:'Montant',type:'',width: '3%'},
				note_to:{title:'OBS',type:'',width: '3%'},
				num_bap:{title:'BAP',type:'',width: '3%'},
				montant_bap:{title:'Mont.BAP',type:'',width: '3%'}
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
				,date_ordo: $('#t_service_genFiltre_date_ordo').val()
				,somme_par_sec: $('#somme_par_sec').val()
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
