<?php
	session_start();
	include("../model/connexion.php");
	$conx = Connexion::GetConnexion();
	try
	{
		$Select = "SELECT id ,service nom_ser_gen FROM t_service ";
		if($_GET["action"] == "options")
		{
			$Select = "SELECT id Value ,nom_ser_gen DisplayText FROM t_service_gen ";
			//Get records from database
			$req = $Select;
			$pdo_result = $conx->query($req);
			$rows		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			$jTableResult['Options'] 	= $rows;
			print json_encode($jTableResult);
			
		}
		else if($_GET["action"] == "list")
		{
			$Filtre = "";
			if(isset($_POST["Filtre"]))
			{
				$w = $_POST["Filtre"];
				$Filtre = " where service LIKE '%$w%'  ";
			}
			//Get records from database
			$req = "SELECT COUNT(*) AS RecordCount FROM t_service $Filtre";
			$pdo_result = $conx->query($req);
			$row		= $pdo_result->fetch();
			$recordCount = $row['RecordCount'];
			//Get records from database
			$req = $Select."$Filtre ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
			$pdo_result = $conx->query($req);
			$rows		= $pdo_result->fetchAll(PDO::FETCH_ASSOC);
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult 				= array();
			$jTableResult['Result'] 	= "OK";
			$jTableResult['TotalRecordCount'] = $recordCount;
			$jTableResult['Records'] 	= $rows;
			print json_encode($jTableResult);
		}
		
		//Creating a new record (createAction)
		else if($_GET["action"] == "create")
		{
			//Insert record into database
			$req = "INSERT INTO t_service_gen(nom_ser_gen,type_serv_gen,nbr_carenet) VALUES(" .$conx->quote($_POST["nom_ser_gen"]).",".$conx->quote($_POST["type_serv_gen"]).",".$conx->quote($_POST["nbr_carenet"]). ");";
			$conx->exec($req);
			//Get last inserted record (to return to jTable)
			$req = $Select." WHERE id = LAST_INSERT_ID();";
			$pdo_result	= $conx->query($req);
			$row		= $pdo_result->fetch();
			$pdo_result->closeCursor();
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = $row;
			print json_encode($jTableResult);
		}
		
		//Updating a record (updateAction)
		else if($_GET["action"] == "update")
		{
			//Update record in database
			$req = ("UPDATE t_service_gen SET nom_ser_gen = ".$conx->quote($_POST["nom_ser_gen"])." ,type_serv_gen = ".$conx->quote($_POST["type_serv_gen"])." ,nbr_carenet = ".$conx->quote($_POST["nbr_carenet"])."  WHERE id = $_POST[id];");
			$conx->exec($req);
			
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			print json_encode($jTableResult);
		}
		
		//Deleting a record (deleteAction)
		else if($_GET["action"] == "delete")
		{
			//Delete from database
			$req = "DELETE FROM t_service_gen WHERE id = $_POST[id];";
			$conx->exec($req);
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			print json_encode($jTableResult);
		}

		//Close database connection
		$conx = null;
		
	}
	catch(Exception $ex)
	{
		//Return error message
		$jTableResult = array();
		$jTableResult['Result'] = "ERROR";
		$jTableResult['Message'] = $ex->getMessage();
		print json_encode($jTableResult);
	}
	