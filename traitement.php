<?php 
		session_start();
		require_once('model/classes.php');
		new Chado(Connexion::GetConnexion());
		
		$o = new Utilisateur(); $o->traitement();
		$o = new Visites(); $o->traitement();
		$o = new UserClasse(); $o->traitement();
		$o = new Classe_in_Code(); $o->traitement();
		
		
		$o = new Actes(); $o->traitement();
		$o = new Assujetti(); $o->traitement();
		$o = new Banque(); $o->traitement();
		$o = new Carnet(); $o->traitement();
		$o = new CarnetAttribuer(); $o->traitement();
		$o = new CarnetLot(); $o->traitement();
		$o = new Commune(); $o->traitement();
		$o = new ComServ(); $o->traitement();
		$o = new Note(); $o->traitement();
		$o = new NoteActe(); $o->traitement();
		$o = new NoteActesPayer(); $o->traitement();
		$o = new Releve(); $o->traitement();
		$o = new Service(); $o->traitement();
		$o = new User(); $o->traitement();