<?php 
	session_start();
	header("Content-type: text/javascript");
	require_once('model/classes.php');
	

	function select_assujetti_ajax( $q ){
		$conx = Connexion::GetConnexion();
		$cle = '%'.str_replace(' ','%', trim($q) ).'%';
		if($cle=='' or strlen($cle)<3)
			return "";
			
		$req = "SELECT concat(nom_assujetti,' ',adresse_assujetti) text , id, 'chad' as attrs
		FROM t_assujetti ass 
		WHERE nom_assujetti like '$cle' or adresse_assujetti  like '$cle' 
		order by nom_assujetti	";
		//echo $req ;
		if($pdo_result = $conx->query($req)){
			$items = array();
			while($row = $pdo_result->fetch(PDO::FETCH_ASSOC)){
				
				$items[] = array('id'=>$row['id'],'text'=>$row['text'],'attrs'=>array('c'=>'c'));
			}
			echo json_encode( array('items'=> $items));
				
		}
		else echo json_encode( $conx->errorInfo());
		
	}

	function fusionner_assujettis( $s1,$s2 ){
		$conx = Connexion::GetConnexion();
		$ids = implode(',',$s1);
		$req = "UPDATE `t_note` SET `id_assujetti` = '$s2'  WHERE id in ($ids) and id <> $s2";			
		//echo $req ;
		if($pdo1 = $conx->exec($req)){
			$req = "DELETE FROM t_assujetti WHERE id in ($ids) and id <> $s2";
			$pdo2 = $conx->exec($req);
			//var_dump($pdo1,$pdo2);
			header("location: assujetti_doublons.php");
		}
		else echo json_encode( $conx->errorInfo());
		
	}

	if(isset($_GET["term"],$_GET["_type"],$_GET["q"]))
	select_assujetti_ajax( $_GET["q"] );

	else if(isset($_POST["s1"],$_POST["s2"]) and !empty($_POST["s1"]) and !empty($_POST["s2"])){
		//echo json_encode($_POST);
		fusionner_assujettis($_POST["s1"],$_POST["s2"]);
	}
	else echo 'Aucune Information fourniee faite retour svp!'; 


?>