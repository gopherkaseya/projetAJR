<?php        
	function liste_User($liste,$classe,$i=0){
		/* echo "<table><tr> <th>N°</th><th>Utilisateur</th><th>Genre</th><th>Rôle</th><th>Titre</th><th>Password</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[nom]</td>
				<td>$o[titre]</td>
				<td>$o[genre_user]</td>
				<td>$o[role]</td>
				<td>$o[password]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>"; */
	}
	      
	function liste_NoteActesPayer($liste,$classe,$i=0){
		echo "<table><tr> <th>N°</th><th>N°Note</th><th>Montant</th><th>Act.&Art.</th><th>Service</th><th>Date Ord</th><th>Assujetti</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[paie_exp_num_note]</td>
				<td>$o[montant_payer]</td>
				<td>$o[acte_1] $o[art_bud_1]</td>
				<td>$o[service_2]</td>
				<td>$o[paie_exp_date_ordo]</td>
				<td>$o[nom_assujetti_1]</td>
				<td>".($o['en_cours_dedition']?"<strong style='color:red'>Oui</strong>":"Non")."</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>";
	}
	
	function liste_Releve($liste,$classe,$i=0){
		/* echo "<table><tr> <th>N°</th><th>Banque</th><th>Date paiement</th><th>Nbre.Notes</th><th>En édition?</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[nom_banque]</td>
				<td>$o[date_paiement]</td>
				<td>$o[nbr_note]</td>
				<td>".($o['en_cours_dedition']?"<strong style='color:red'>Oui</strong>":"Non")."</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>"; */
	}
	
	function liste_Banque($liste,$classe,$i=0){
		/* echo "<table><tr> <th>N°</th><th>Nom</th><th>Adresse</th><th>Téléphone</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[nom_banque]</td>
				<td>$o[adresse_banque]</td>
				<td>$o[telephone_banque]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>"; */
	}
	function liste_Commune($liste,$classe,$i=0){
		echo "<table><tr> <th>N°</th><th>Ville</th><th>Commune</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[ville]</td>
				<td>$o[commune]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>";
	}
	function liste_Service($liste,$classe,$i=0){
		echo "<table><tr> <th>N°</th><th>Service Générateur</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[service]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>";
	}
	function liste_CarnetAttribuerResum($liste,$classe,$i=0){
		echo "<table><tr> <th>N°</th><th>N°Début</th><th>Attribué le:</th><th>Bénéficiaire</th><th>Etat</th></tr>";$ex="";
		foreach($liste as $o){
			$recepteur = $o["ville"]=="Lubumbashi"?($o["commune"]=="Centre"?($o["service"]):$o["commune"]):$o["ville"];
			{echo $recepteur!=$ex?"<tr><td>".(++$i)."</td>
				<td>$o[num_debut]</td>
				<td>$o[date_attribution]</td>
				<td>$recepteur</td>
				<td>".($o["souche"]=="1"?'Souche':'En Cours')."</td>
			</tr>":"";}
			$ex = $recepteur;
		}
		echo "</table>";
	}
	function liste_Note($liste,$classe,$i=0){
		
		/* echo "<table><tr><th>Date Ordo.</th><th>Date Dépot</th><th>Assujetti</th><th>Adresse</th><th>N° Note</th><th>N°Bap</th><th>Mont.BAP</th><th>Observation</th><th>Enregistrée le:</th>
		<th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$o[date_ordo]</td>
					<td>$o[date_depot]</td>
					<td>$o[nom_assujetti]</td>
					<td>$o[adresse_assujetti]</td>
					<td>$o[num_note]</td>
					<td>$o[num_bap]</td>
					<td>$o[montant_bap]</td>
					<td>$o[observation]</td>
					<td>$o[date_enrg]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>"; */
	}
	function liste_CarnetAttribuer($liste,$classe,$i=0){
		/* echo "<table><tr> <th>N°</th><th>N°Début</th><th>Attribué le:</th><th>Service</th><th>Antenne</th><th>Ville</th><th>Etat</th>
		<th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[num_debut]</td>
				<td>$o[date_attribution]</td>
				<td>$o[service]</td>
				<td>$o[commune]</td>
				<td>$o[ville]</td>
				<td>".($o["souche"]=="1"?'Souche':'En Cours')."</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>"; */
	}
	function liste_Carnet($liste,$classe,$i=0){
		echo "<table><tr> <th>N°</th><th>Souche</th><th>N°Début</th><th>N°Fin</th><th>Date Epuisement</th><th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>".($o["souche"]=="1"?'Souche':'En Cours')."</td>
				<td>$o[num_debut]</td>
				<td>$o[num_fin]</td>
				<td>$o[date_epuisement]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>";
	}
	function liste_Actes($liste,$classe,$i=0){
		echo "<table><tr> <th>N°</th>
		<th>Serv.Gén.</th><th>Acte</th><th>Art.B.</th><th>Date Arrêté</th>
		<th>Date Rôle</th><th>Pénalité</th>
		<th><a href='#x' onClick=\"updateObjet('$classe','ajt',0)\" ><img alt='ajouter' src='images.jpg'></a></th></tr>";
		foreach($liste as $o){ $i++;
			{echo "<tr><td>$i</td>
				<td>$o[service]</td>
				<td>$o[acte]</td>
				<td>$o[art_bud]</td>
				<td>$o[date_arrete]</td>
				<td>$o[date_role]</td>
				<td>$o[penalite]</td>
				<td>$o[coefficient]</td>
				<td><a href='#x' onClick=\"updateObjet('$classe','voir',$o[id])\" ><img alt='detail' src='detail.png'></a></td>
			</tr>";}
		}
		echo "</table>"; 
	}