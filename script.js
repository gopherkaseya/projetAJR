
		if(window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
			// alert("Votre navigateur est compatible pour AJAX");
		}
		else if(window.ActiveXObject){
			xhr=new ActiveXObject("Microsoft.XMLHTTP");
			alert("Votre navigateur est compatible pour AJAX");
		}
		else alert("Votre navigateur n’est pas compatible avec AJAX");
		
		function connectURL(url) {
			if (window.XMLHttpRequest)
				objXHR = new XMLHttpRequest();
			else {
				if (window.ActiveXObject)
					objXHR = new ActiveXObject("Microsoft.XMLHTTP");
				alert(objXHR);
			}
			document.getElementById("loading").style.display="";// innerHTML = 
			/* $(document).ready(function(){
			$()
			.insertBefore('#forgot').ajaxStart(function() {
			$(this).show();//alert("ajax start");
			}).ajaxStop(function() {//alert("ajax stop");
			//$(this).hide();
			});
			}); */
			objXHR.open("GET",url,false);
			objXHR.send(null);
			document.getElementById("loading").style.display="none";// innerHTML = 
			if (objXHR.readyState == 4) return objXHR.responseText;
			else return false;
		}
		/* la fonction envoi par GET un paramettre et sa valeur au serveur [id_service|id_antenne]
		 * et récupère un combo, sois d'acte gén, soit de service gén.
		 * le parametre à envoyer est dans parmToSend
		 * la valeur du parametre à envoyer est dans l'élément html dont l'id est dans valOfParmToSend
		 * le text renvoyé du serveur est écrit dans l'élément html dont l'id est dans 
		 *****************************************************************************/
		function askServeur1(url,id_retour){
			var dt_ord1 = document.getElementById("date_ordo1").value;
			var dt_ord2 = document.getElementById("date_ordo2").value;
			var id_serv = document.getElementById("id_service").value;
			var id_ante = document.getElementById("id_antenne").value;
			var dt_save = document.getElementById("dt_save").checked?"ok":"";
			var check_to = document.getElementById("check_to").checked?"ok":"";
			// var id_vill = document.getElementById("id_ville").value;
			document.getElementById(id_retour).innerHTML = connectURL(url+"&dt_ord1="+dt_ord1+"&dt_ord2="+dt_ord2+"&id_serv="+id_serv+"&id_ante="+id_ante+"&dt_save="+dt_save+"&check_to="+check_to+"&id_vill=v");
		}
		function getFormSupNoteActe(id_note,id_note_acte){
			document.getElementById('htmk_form_sup_note_acte').innerHTML = connectURL('ajax.php?operation=sup_note_acte&id_note_acte='+id_note_acte+'&id_note='+id_note);
		}
		function updateObjet(classe,opt,id){
			var url = "UpdateObjet.php?class="+classe+"&opt="+opt+"&id="+id+"";
			document.getElementById("zone_update_objet").innerHTML = connectURL(url);
		}
		
		function filtre_opt_actes_ordo(){
			var el = document.getElementById("filtre_opt_actes_ordo");
			var param = document.getElementById("source_opt_acte_ordo_parm").value;
			var fonc = document.getElementById("source_opt_acte_ordo_fonc").value;
			document.getElementById("opt_actes_ordo").innerHTML = connectURL('ajax.php?operation=filtrer_acte_ordo&src_parm='+param+'&src_fonc='+fonc+'&filtre='+el.value);
		}
		function filtrer_assuj_ordo(){
			var n = document.getElementById("filtre_nom_assujetti").value;
			var a = document.getElementById("filtre_adresse_assujetti").value;
			document.getElementById("id_assujetti_ordo").innerHTML = connectURL('ajax.php?operation=filtrer_assuj_ordo&filtre_n='+n+'&filtre_a='+a);
		}
		