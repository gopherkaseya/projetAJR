<?php 
if(isset($_SESSION["connected"]) and $_SESSION["connected"]=="OK"){
    $back = 'chado_back_office=1&amp;'; 
          if(isset($_SESSION['user']['tab_class']['Actes'])){ ?><li><a href='?chado_back_office=1&amp;class=Actes' class="<?php echo $class=="Actes"?"active":""?>">Actes</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Assujetti'])){ ?><li><a href='?chado_back_office=1&amp;class=Assujetti' class="<?php echo $class=="Assujetti"?"active":""?>">Assujetti</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Banque'])){ ?><li><a href='?chado_back_office=1&amp;class=Banque' class="<?php echo $class=="Banque"?"active":""?>">Banque</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Carnet'])){ ?><li><a href='?chado_back_office=1&amp;class=Carnet' class="<?php echo $class=="Carnet"?"active":""?>">Carnet</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['CarnetAttribuer'])){ ?><li><a href='?chado_back_office=1&amp;class=CarnetAttribuer' class="<?php echo $class=="CarnetAttribuer"?"active":""?>">CarnetAttribuer</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['CarnetLot'])){ ?><li><a href='?chado_back_office=1&amp;class=CarnetLot' class="<?php echo $class=="CarnetLot"?"active":""?>">CarnetLot</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Commune'])){ ?><li><a href='?chado_back_office=1&amp;class=Commune' class="<?php echo $class=="Commune"?"active":""?>">Commune</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['ComServ'])){ ?><li><a href='?chado_back_office=1&amp;class=ComServ' class="<?php echo $class=="ComServ"?"active":""?>">ComServ</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Note'])){ ?><li><a href='?chado_back_office=1&amp;class=Note' class="<?php echo $class=="Note"?"active":""?>">Note</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['NoteActe'])){ ?><li><a href='?chado_back_office=1&amp;class=NoteActe' class="<?php echo $class=="NoteActe"?"active":""?>">NoteActe</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['NoteActesPayer'])){ ?><li><a href='?chado_back_office=1&amp;class=NoteActesPayer' class="<?php echo $class=="NoteActesPayer"?"active":""?>">NoteActesPayer</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Releve'])){ ?><li><a href='?chado_back_office=1&amp;class=Releve' class="<?php echo $class=="Releve"?"active":""?>">Releve</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Service'])){ ?><li><a href='?chado_back_office=1&amp;class=Service' class="<?php echo $class=="Service"?"active":""?>">Service</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['User'])){ ?><li><a href='?chado_back_office=1&amp;class=User' class="<?php echo $class=="User"?"active":""?>">User</a></li><?php } ?> 	
    
    <?php if(isset($_SESSION['user']['tab_class']['Utilisateur'])){ ?><li><a href='?class=Utilisateur&amp;chado_back_office=1' class="<?php echo $class=="Utilisateur"?"active":""?>">Utilisateur</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Visites'])){ ?><li><a href='?class=Visites&amp;chado_back_office=1' class="<?php echo $class=="Visites"?"active":""?>">Visites</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['UserClasse'])){ ?><li><a href='?class=UserClasse&amp;chado_back_office=1' class="<?php echo $class=="UserClasse"?"active":""?>">UserClasse</a></li><?php } ?>
    <?php if(isset($_SESSION['user']['tab_class']['Classe_in_Code'])){ ?><li><a href='?class=Classe_in_Code&amp;chado_back_office=1' class="<?php echo $class=="Classe_in_Code"?"active":""?>">Classe_in_Code</a></li><?php } ?>
    
    <?php
	$pwd = strtoupper($_SESSION['user']["password"]);
    	if(in_array($pwd,array("KEITAJ","MIK-1234"))){ ?>
    		<li><a href='?chado_back_office=1&amp;class=NoteActe' class="<?php echo $class=="NoteActe"?"active":""?>">HISTORIQUE</a></li>
	<?php }
 
 } ?>    
  