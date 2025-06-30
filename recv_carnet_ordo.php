<?php 
	if(!isset($_SESSION))
	session_start();
	
	require_once('model/connexion.php');
	require_once('model/chado.php');
	require_once('model/fonctions_sup.php');
	Chado::$conx = Connexion::GetConnexion();
	
	$select_style = "style='display:inline'";
	
	$req = "SELECT c.num_debut,co.commune,s.service FROM `t_carnet_attribuer` ca inner join t_carnet c on c.id = ca.id_carnet inner join t_commune co on co.id = ca.id_commune inner join t_service s on s.id = ca.id_service  order by  ca.id desc";
	$count=0;$max = $_SESSION["snp"]["nbr_note_max"]; 
	if($pdo_result = Chado::$conx->query($req) ){
	    $i=1;
	    echo "<table>";
			echo "<tr style='background: #456fb9;color: whitesmoke;font-weight: bold;'>
			<td colspan=3># </td><td > SERIES CARNETS ORDONNANCES</td>
			<td colspan=3># </td><td > SERIES CARNETS ORDONNANCES</td><td ># </td>
			<td colspan=3> SERIES CARNETS ORDONNANCES</td></tr>";
	    while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
			echo "<tr><td > $i </td><td > $row[num_debut] </td><td > $row[commune] </td><td > $row[service] </td>";
			$i++;
			if($row = $pdo_result->fetch(PDO::FETCH_ASSOC))
			    echo "<td > $i </td><td > $row[num_debut] </td><td > $row[commune] </td><td > $row[service] </td>";
			else echo "<td></td>";
			$i++;
			if($row = $pdo_result->fetch(PDO::FETCH_ASSOC))
			    echo "<td > $i </td><td > $row[num_debut] </td><td > $row[commune] </td><td > $row[service] </td>";
			else echo "<td></td>";
			$i++;
			echo "</tr>";
		}
	    echo "</table>";
	    
	}
				
			
	