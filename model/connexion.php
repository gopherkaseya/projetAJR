<?php
	class Connexion {
		//Déclaration des attributs
		private static $ressource;
		public static $conx;
		public static function GetConnexion(){
			$_SESSION["db_annee"] = "2022";
			$db = isset($_SESSION["db"])?$_SESSION["db"]:"dgrad_15_06_2016";
			switch($db){
				case '1':
					$db = "delaqvyy_dgradru_2015";
					$_SESSION["db_annee"] = "2015";
				break;
				case '2':
					$db = "delaqvyy_dgradru_2018";
					$_SESSION["db_annee"] = "2018";
					break;
				case '3':
					$db = "delaqvyy_dgradru_2022";
					$_SESSION["db_annee"] = "2022";
					break;
			}
			$server = "localhost";// "CHADO-PC\SQLEXPRESS";// 
			$username = "root";//"sa"//
			$password = "";//"password"//
			$dbname = $db;//"base de donnee"//3306
			if(self::$conx == null) {
				try{
					self::$ressource = self::$conx = new PDO("mysql:host=$server;dbname=$dbname;port=3306",$username,$password);
				    //var_dump(self::$conx);
					return self::$conx;	
				}
				catch(PDOException $e){
				    //var_dump($e->getMessage()." ".$e->getCode());
					return $e->getMessage()." ".$e->getCode();
				}
			}else return self::$conx;
		}
	}
	
	// 25/03/2016 création de la classe image
	class Image{
		public static $Ext_OK	= array('JPEG', 'JPG', 'PNG', 'GIF');
		public static $Tail_OK 	= 1048576;//1024 * 1024 octet = 1Mo
		public $Ficher			= array();
		public $extension	 	= "";
		public $full_path 		= "";
		public $message 		= "";
		public $erreur	 		= "";
		
		public function __construct($input_name){
			if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === 0){
				$this->Ficher 		= $_FILES[$input_name];
				$this->extension 	= pathinfo($this->Ficher['name'], PATHINFO_EXTENSION);
				/*============ ================= ================= =============== =======*/
				if(in_array(strtoupper($this->extension), self::$Ext_OK)){	// si l'extention du fichier est acceptable
					if ($this->Ficher['size'] < self::$Tail_OK) {			// si taille fichier est < à celle prévue au max
						//
					}else $this->erreur = "La taille de l'image est tros grande, Il faut ".(self::$Tail_OK/1048576)."Mo = ".(self::$Tail_OK/1024)."Ko = ".(self::$Tail_OK)."octets au maximum!";
				}else $this->erreur = 'Extension invalide';
			}else $this->erreur = 'Pas de Fichier Envoy&eacute; pour '.$input_name;
		}
		// permet de créer le repertoir où sera stocké l'image, s'il n'existe pas
		public function creer_repertoire($repertoire){
			$r = explode("/",$repertoire);$chemin = "";
			foreach($r as $rep){
				if(!is_dir($chemin.$rep)){
					if(!mkdir($chemin.$rep, 0755)){
						$this->error = 'Echec cr&eacute;ation repertoire! ('.$chemin.$rep.')';
						return false;
					}
				}$chemin .= "$rep/";
			}
			$this->message .= "Le r&eacute;pertoire existe d&eacute;j&agrav;<br/>";
			return true;
		}
		// enregistrement effectif du fichier
		public function sauver($repertoire,$nom_ficher_a_enregistrer){
			if($this->erreur == ''){						// s'il n y a aucune eurreur n'est enregistré après création de l'objet
				if ($this->creer_repertoire($repertoire)) { // si la création du répertoire réussi
					$fichier = $nom_ficher_a_enregistrer.".".$this->extension;
					if (!(@move_uploaded_file($this->Ficher['tmp_name'], ($this->full_path = $repertoire."/".$fichier)))) // déplace le fichier uploadé du répertoire temporaire vers le répertoire indiqué
						$this->erreur = "Echec sauvegarde du fichier";
					else {
						return true;
						$this->message .= "Fichier enregistr&eacute; correctement!<br/>";
					}
				}
			}
			return false;
		}
	}
	function chiffre($br)
	{
		$s = "";
		$bb = explode(".",$br);
		$br = $bb[0];
		if($br<0){$br = -1*$br;$s = "-";}
		$v = "$br";
		$r = "";
		for($i=strlen($v)-1,$j=0; $i!=-1; $i--,$j++)
		{
			$r = ($j%3 == 0)? ($v[$i].(  ($j == 0)?  "":".")."".$r):($v[$i].$r);
		}	
		return (isset($bb[1])?"$s$r,$bb[1]":"$s$r,00");
	}
	function paginner($total,$p_encours,$taille,$classe,$id_retour,$appel_ajax=true){
		$taille = $taille?$taille:1;
		$rest = $total % $taille;
		$div = ($total-$rest) / $taille;
		$bre_p = $div + ($rest?1:0);
		$nb_p_v = 3;
		$back = (isset($_GET["chado_back_office"])and 1==$_GET["chado_back_office"])?"chado_back_office=1&amp;":"";
		if($total>$taille){
			echo "<ul class='pagination'>";
			for($i=1;$i!=$bre_p+1;$i++){
				$t = ($i-1)*$taille;
				$onClick = $class = "";
				if($i==$bre_p)$n = "";
				else if($i!=1)$n = "$i";
				$url = "action=liste&amp;class=$classe&amp;active_pg=$i&amp;d_pg=$t&amp;t_pg=$taille";
				if($appel_ajax){$onClick = " onClick=\"askServeur1('liste_objets_ajax.php?$url','$id_retour');\" ";
				$url = "#ddd";
				}else $url = "?$back$url";
				if($i==1){
					echo "<li class='first-page'><a $onClick href='$url'>&laquo;</a></li>";
				}
				if($i==$p_encours)$class = "active";
				
				if($i >= $p_encours - $nb_p_v and ($i-$p_encours) <= $nb_p_v )
					echo "<li><a class='$class' $onClick href='$url'>$i</a></li>";
				
				if($i==$bre_p){
					echo "<li class='first-page'><a $onClick href='$url'>&raquo;</a></li>";
				}
			}
			$l = $p_encours*$taille>$total?$total:$p_encours*$taille;
			echo "</ul><ul class='pagination' style='margin-left:10px'><li><a href='#$total' title='il y a $total lignes.' >De ".(($taille*($p_encours-1))+1)." à $l sur $total</a></li></ul> ";
		}
	}
?>
