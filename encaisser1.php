<?php
	session_start();
	require_once('model/classes.php');
	Chado::$conx = Connexion::GetConnexion();
	 
	if(isset($_POST["bt_login"])){
		$lg = $_POST["login"];
		$pwd = $_POST["password"];
		$l = Utilisateur::liste_objet(" where login='$lg' and password='$pwd' -- and sys_ut.actif=1  and sys_ut.is_deleted=0 ","");
		if(Utilisateur::$count ){
			$_SESSION['user']["password"] = $pwd;
			$_SESSION["connected"] = "OK";
			$_POST["id_utilisateur"]=$_SESSION["id_utilisateur"] = $l[0]["id"];
			$_POST["date_heure_deconx"]='';
			$_SESSION["snp"]["user"] = $l[0];
			$_SESSION["message"] = "Authentification Effectuée";
			$u = new Visites();
			$u->creer();
			$_SESSION['user']['id_visite'] = Visites::$id;
			$_SESSION['user']['tab_class'] = array();
			// classe permises
			if($c = UserClasse::liste_objet(" where id_utilisateur ='$_POST[id_utilisateur]' ","")){
				foreach($c as $cla)
					$_SESSION['user']['tab_class'][$cla['class_nom']] = true;
			}
		}
		else{
			$_SESSION["connected"] = "NO";
			$_SESSION["message"] = "Echec Authentification";
		} 
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>        
        <!-- META SECTION -->
        <title>Encaisser</title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <!-- END META SECTION -->
        
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="joli_snp/css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                                      
		<style>
		#tooltip {
			position: absolute;
			z-index: 99999999;
			background: #daedeb;
			padding: 3px;color:#000;
		}
		</style>
    </head>
    
    <?php 
	if(isset($_SESSION["connected"])and $_SESSION["connected"]=="OK"){ 
	?>
    <body style="height:1000px">
                <!-- START CONTENT FRAME -->
                <div class="content-frame">                       
                        <div class="pull-left push-up-10">
                            <button class="btn btn-primary" id="gallery-toggle-items">Toggle All</button>
							<a href="joli_snp/upload_releves.php" href='#xx'>Uploader d'autres Relevés.</a>
                        </div>
                        <div class="pull-right push-up-10">
                            <div class="btn-group">
                                <button class="btn btn-primary"><span class="fa fa-pencil"></span> Edit</button>
                                <button class="btn btn-primary"><span class="fa fa-trash-o"></span> Delete</button>
                            </div>                            
                        </div>
                        
                        <div class="gallery" id="links">
                             
                            
                            <?php
							$dir = "joli_snp/uploads/releves/";
							chdir($dir);
							array_multisort(array_map('filemtime', ($files = glob("*.{jpg,png,gif}", GLOB_BRACE))), SORT_DESC, $files);
							foreach($files as $filename)
							{
								// echo "<li>".substr($filename, 0, -4)."</li>"; 
								echo '
								<a class="gallery-item" href="'.$dir.$filename.'" title="Relevé<br>chado<br> '.$filename.'" data-gallery>
									<div class="image">                              
										<img src="'.$dir.$filename.'" alt="Relevé '.$filename.'"/>                                        
										<ul class="gallery-item-controls">
											<li><label class="check"><input type="checkbox" class="icheckbox"/></label></li>
											<li><span class="gallery-item-remove"><i class="fa fa-times"></i></span></li>
										</ul>                                                                    
									</div>
									<div class="meta">
										<strong>Relevé<br>chado<br> '.$filename.'</strong>
										<span>Description</span>
									</div>                                
								</a>';
							} 
							?>

                            <a class="gallery-item" href="joli_snp/assets/images/gallery/music-1.jpg" title="Music picture 1" data-gallery>
                                <div class="image">
                                    <img src="joli_snp/assets/images/gallery/music-1.jpg" alt="Music picture 1"/>    
                                    <ul class="gallery-item-controls">
                                        <li><label class="check"><input type="checkbox" class="icheckbox"/></label></li>
                                        <li><span class="gallery-item-remove"><i class="fa fa-times"></i></span></li>
                                    </ul>                                                                    
                                </div>
                                <div class="meta">
                                    <strong>Music picture 1</strong>
                                    <span>Other description</span>
                                </div>                                
                            </a>                            

                            <a class="gallery-item" href="joli_snp/assets/images/gallery/girls-11.jpg" title="Girls1" data-gallery>
                                <div class="image">
                                    <img src="joli_snp/assets/images/gallery/girls-11.jpg" alt="Girls Image 1"/>                                        
                                    <ul class="gallery-item-controls">
                                        <li><label class="check"><input type="checkbox" class="icheckbox"/></label></li>
                                        <li><span class="gallery-item-remove"><i class="fa fa-times"></i></span></li>
                                    </ul>                                                                    
                                </div>
                                <div class="meta">
                                    <strong>Girls image 1</strong>
                                    <span>Description</span>
                                </div>                                
                            </a>

                        </div>
                             
                        <ul class="pagination pagination-sm pull-right push-down-20 push-up-20">
                            <li class="disabled"><a href="#">«</a></li>
                            <li class="active"><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>                                    
                            <li><a href="#">»</a></li>
                        </ul>
                </div>               
                <!-- END CONTENT FRAME -->
        
        <!-- BLUEIMP GALLERY -->
        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>      
        <!-- END BLUEIMP GALLERY -->
        
        
    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="joli_snp/js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="joli_snp/js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="joli_snp/js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="joli_snp/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        
        <script type="text/javascript" src="joli_snp/js/plugins/blueimp/jquery.blueimp-gallery.min.js"></script>
        <script type="text/javascript" src="joli_snp/js/plugins/dropzone/dropzone.min.js"></script>
        <script type="text/javascript" src="joli_snp/js/plugins/icheck/icheck.min.js"></script>
        <!-- END THIS PAGE PLUGINS-->        

        <!-- START TEMPLATE -->
        <script type="text/javascript" src="joli_snp/js/settings.js"></script>
        
        <script type="text/javascript" src="joli_snp/js/plugins.js"></script>        
        <script type="text/javascript" src="joli_snp/js/actions.js"></script>        
        <!-- END TEMPLATE -->

        <script>            
            document.getElementById('links').onclick = function (event) {
                event = event || window.event;
                var target = event.target || event.srcElement;
                var link = target.src ? target.parentNode : target;
                var options = {index: link, event: event,onclosed: function(){
						fermer_form();
                        setTimeout(function(){
                            $("body").css("overflow","");
                        },200);
                    }};
                var links = this.getElementsByTagName('a');
                blueimp.Gallery(links, options);
            };
			// === == === === CREATION DU PROFIL === === ======
			$('body').on('submit','#tooltip',function(e) {
				e.preventDefault();		
				var $form = $(this);
				var formdata = (window.FormData) ? new FormData($form[0]) : null;
				var data = (formdata !== null) ? formdata : $form.serialize();
		 
				$.ajax({
					url: $form.attr('action'),
					type: $form.attr('method'),
					contentType: false, // obligatoire pour de l'upload
					processData: false, // obligatoire pour de l'upload
					dataType: 'json', // selon le retour attendu
					data: data,
					success: function (response) {
						$("#msg_retour").html(response.message);
					}
				});
				return false;		
			});
			$(".gallery-item").click(function(){
				$("#img_releve").val($(this).attr("href"));
			});
			var $tooltip = $('<form method="POST" action="save_note_payee.php" id="tooltip" style="text-align: center;display:none;opacity: 50%;">'+
			'<div style="margin-top:10px;padding:10px;border:solid 1px red">Banque:<br><input name="banque" placeholder="Banque"/><br>Date:<br><input placeholder="Payer le: " name="date" /></div>'+
			'<div style="margin-top:10px;padding:10px;border:solid 1px red">'
			+'<input placeholder="N°N.P" name="n1"/><br><input placeholder="N°N.P" name="n2"/><br>'
			+'<input placeholder="N°N.P" name="n3"/><br><input placeholder="N°N.P" name="n4"/><br>'
			+'<input placeholder="N°N.P" name="n5"/><br><input placeholder="N°N.P" name="n6"/><br>'
			+'<input placeholder="N°N.P" name="n7"/><br><input placeholder="N°N.P" name="n8"/><br>'
			+'<input placeholder="N°N.P" name="n9"/><br><input placeholder="N°N.P" name="n10"/><br>'
			+'<input type="hidden" id="img_releve" name="img" value="" />'
			+'</div><input type="submit" name="btn_save_releve" value="Sauvegarder" style="margin:auto" style="width:150px"/><p id="msg_retour"></p></form>').appendTo('body');
			var positionTooltip = function(event) {
				var tPosX = event.pageX;
				var tPosY = event.pageY;
				//alert(tPosX+" "+tPosY);
				$tooltip.css({top: tPosY, left: tPosX});
			};
			// ouverture formulaire
			$(".slides").on('click','.slide img',function(event){
				positionTooltip(event);
				ouvrir_form();
			});
			// fermeture du formulaire
			$(".prev, .next").click(function(){
				fermer_form();
			});
			$(".indicator").on("click","li",function(){
				if(!$(this).is(".active"))fermer_form();
				//alert("chado");
			});
			function ouvrir_form(){
				$tooltip.css({"opacity":0.9,"display":""});
			}
			function fermer_form(){
				$tooltip.find("input[type='text']").val("");
				$tooltip.css("display","none");
			}
        </script>        
        
    <!-- END SCRIPTS -->         
    </body>
	<?php
	}else {
	?>
	<form method="POST" style="width:auto;margin:auto">
		<table style="width:150px;margin: auto;">
		<caption><h3>CONNECTEZ VOUS ICI.</h3></caption><tbody><tr><th>Login:</th><td><input name="login" value="-" style="width:150px"></td></tr>
		<tr><th>Password:</th><td><input name="password" value="" style="width:150px" type="password"></td></tr>
		<tr><td></td><td><input name="bt_login" value="Connexion" type="submit"></td></tr>
		</tbody></table>
	</form>
	<?php 
	}
	?>
</html>






