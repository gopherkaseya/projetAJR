	
	<?php
	
	$s = " style='float:right' ";	
    if(isset($_SESSION["connected"]) and $_SESSION["connected"]=="OK"){
    	$id_liste 	= "id_liste_classes";
    	if(isset($_SESSION['user']['tab_class']['Front_cr'])){
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_encaissement&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Encaisser Note</a></li>";	
    	}
    	if(isset($_SESSION['user']['tab_class']['Front_etud']) or isset($_SESSION['user']['tab_class']['Front_cr'])){
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=ordo_rapp_mensuel&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Rapport Mesuel Des Ordo.</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_rapp_mensuel&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Rapport Mesuel Des Recv.</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_rapp_mensuel_niv&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Rapport Mesuel Des Recv&Niv</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=etud_rapp_mensuel&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Rapport Mesuel</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=etud_rapp_mensuel_ant&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Rapport Mesuel Antenne</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=etud_reste_recv&','$id_liste');\" ";
    		echo "<li><a $onClick href='#xx'>Rest à recouvrer</a></li>";	
    	}
    	// else if(isset($_GET["bureau"]) and ($_GET["bureau"]=="recv" or $_GET["bureau"]=="caisse")){
    	if(isset($_SESSION['user']['tab_class']['Front_recv'])){
    		if(isset($_SESSION['user']["password"])){
    			$pwd = strtoupper($_SESSION['user']["password"]);
    			if(in_array($pwd,array("KEITAJ","MIK-1234"))){
    				// echo "<li><a onClick=\"window.open('joli_snp/upload_releves.php')\" href='#xx'>Up. Relevés.</a></li>";	
    				// echo "<li><a onClick=\"window.open('encaisser.php')\" href='#xx'>Encaisser.</a></li>";	
    				// echo "<li><a id='id_link_encaiss_jr' href='#xx'>Encaissement Jr.</a></li>";				
    				// echo "<li><a id='id_link_img_releve' href='#xx'>IMG-Relevé.</a></li>";							
    			}
    			if(in_array($pwd,array("MIK-1234","KEITAJ","RASSA_AIDE"))){
    				$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_encaissement&','$id_liste');\" ";
    				echo "<li><a $onClick href='#xx'>Encaisser Note</a></li>";	
    				$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_carnet_ordo&','$id_liste');\" ";
    				echo "<li><a $onClick href='#xx'>Carnets Ordo.</a></li>";	
    				$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_role&','$id_liste');\" ";
    				echo "<li><a $onClick href='#xx'>Rôle</a></li>";				
    			}
    			
    			if(in_array($pwd,array("KEITAJ","KEITAJ","MIK-1234"))){
    				$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_note_apurees&','$id_liste');\" ";
    				echo "<li><a $onClick href='#xx'>Notes Encaissées</a></li>";
    				// $onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_note_conflictuelle&','$id_liste');\" ";
    				// echo "<li style='width:auto;float:right'><a $onClick href='#xx'>Notes Conflictuelles</a></li>";
    				
    				// $onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_encaissement_non_ordo&','$id_liste');\" ";
    				// echo "<li style='width:auto;float:right;margin:0 20px'><a $onClick href='#xx'>Encaiss. Non Ord.</a></li>";	
    				// $onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_encaissement_non_ordo_hors_serie&','$id_liste');\" ";
    				// echo "<li style='width:auto;float:right'><a $onClick href='#xx'>Encaiss.Hors Série.</a></li>";
    			}			
    			if($pwd=="KEITAJ"){
    			$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_rapp_mensuel&','$id_liste');\" ";
    			echo "<li><a $onClick href='#xx'>Rapport Mesuel Des Recv.</a></li>";
    			$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=recv_rapp_mensuel_niv&','$id_liste');\" ";
    			echo "<li><a $onClick href='#xx'>Rapport Mesuel Des Recv&Niv</a></li>";	
    			}
    		}	
    	}		
    	// else if(isset($_GET["bureau"]) and $_GET["bureau"]=="ordo"){
    	if(isset($_SESSION['user']['tab_class']['Front_ordo'])){
    		$s = " style='float:right' ";
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=ordo&','$id_liste');\" ";
    		echo "<li id='linck_ordo_ordonnancer'><a $onClick href='#xx'>Ordonnancer</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=ordo_journal&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journall'><a $onClick href='#xx'>Journaux Ressort</a></li>";
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=ordo_jnl_ant_et_centre&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal_tous'><a $onClick href='#xx'>Journal/Secteur</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=ordo_rapp_mensuel&','$id_liste');\" ";
    		echo "<li id='linck_ordo_rapp_mens'><a $onClick href='#xx'>Rapport Périodique</a></li>";	
    		// echo "<li id='linck_ordo_rapp_annuel'><a href='#xx'>Rapport Annuel</a></li>";	
    	}
    	// else if(isset($_GET["bureau"]) and $_GET["bureau"]=="ordo"){
    	if(isset($_SESSION['user']['tab_class']['Front_cr'])){
    		
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_ordo_journal&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-Journaux Ressort</a></li>";
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_ordo_jnl_ant_et_centre&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-Journal/Secteur</a></li>";
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_ordo_rapp_mensuel&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-R.M.-Ordo</a></li>";
    		
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_recv_rapp_mensuel&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-R.M.-Rec.</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_recv_rapp_mensuel_niv&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-R.M.-Rec.Niv.</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_etud_reste_recv&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-R.M.-Rest à Rec.</a></li>";	
    		$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=cr_etud_rapp_mensuel&','$id_liste');\" ";
    		echo "<li id='linck_ordo_journal'><a $onClick href='#xx'>CR-Rapport M.</a></li>";	
    		
    		echo "<style>
    		ul li#linck_ordo_journal {
    			display: block;
    		}
    
    		ul.paginationn li {
    			display: none;
    		}
    
    		ul.paginationn li:first-child, ul.paginationn li:last-child {
    			display: block;
    		}
    		</style>";
    	}		
    	else {		
    		
    		// $onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=snp_apropos&','$id_liste');\" ";
    		// echo "<li><a $onClick href='#xx'>A propos</a></li>";		
    		// $onClick = " onClick=\"askServeur1('liste_objets_ajax.php?page=snp_aide&','$id_liste');\" ";
    		// echo "<li><a $onClick href='#xx'>Besoin d'Aide?</a></li>";		
    		
    	}	
        
    }
	  ?>   