/**
   * Constructeur de la classe
   * @param element l'element DOM sur lequel s'applique l'autocompletion
   */
function autocomplete(element, focus)
{

	this.select 		 	= element;
	this.options 		 	= null;
	this.charSequence 	 	= null;
	this.regex			 	= null;
	this.T_regex			= [];
	this.enablePropagation 	= false;
	
	this.keydownHandler  	= null;
	this.unloadHandler 	 	= null;
		
	this.init(focus);

}

/**
  * Initialisation de la classe, sauvegarde les options initiales, attache les gestionnaires d'évenements, préserve l'action du onchange
  */
autocomplete.prototype.init = function(focus)
{
	this.options = new Array();
	this.charSequence = "";
	window.cancelOnChange = false;	
	for(var v=0; v<this.select.options.length; v++)
	{
		this.options.push(this.select.options[v]);
	}
	
	var ctx = this;
	this.keydownHandler = function(e){ctx.keydown.apply(ctx,[e]);};
	this.unloadHandler 	 = function(){ctx.unload.apply(ctx);}

	if (window.addEventListener)
	{
		this.select.addEventListener("keydown",this.keydownHandler, false);
		window.addEventListener("beforeunload",this.unloadHandler, false);
	}
	else if(window.attachEvent)
	{
		this.select.attachEvent("onkeydown",this.keydownHandler);	
		window.attachEvent("onbeforeunload", this.unloadHandler);
	}
	else
	{
		this.select.onkeydown = this.keydownHandler;	
		window.onbeforeunload = this.unloadHandler;
	}

	if(focus) 
		this.select.focus();
}

/**
  * Retourne tre si l'option est à filtrer, false sinon
  * @param current l'option à tester
  */
autocomplete.prototype.matches = function(current)
{//alert(this.regex);
	
	for(i in this.T_regex){
		if(!current.text.match(this.T_regex[i]))return false;
	}
	return true;
	// return current.text.match(this.regex);
}

autocomplete.prototype.getDisplayedFilterMessage = function()
{
	return ">> Filtre : " + this.charSequence + "   (ECHAP pour annuler, <-- pour effacer le dernier caractère)";
}

/**
  * Filtre les données de la liste avec le masque représenté par la valeur de charSequence
  */
autocomplete.prototype.filter = function()
{
	this.T_regex			= [];
	this.select.options.length 	= 0;
	// on coupe le mot clé de l'utilisateur en fonction des espace
		var t = this.charSequence.split(' ');
		for(i in t){
			this.T_regex.push(new RegExp("" + t[i], "gi"));
		}
	//
	this.regex 					= new RegExp("" + this.charSequence, "gi");
	
	if(this.charSequence != "")
	{
		this.select.options[this.select.options.length] = new Option(this.getDisplayedFilterMessage(),"");
		this.select.options[this.select.options.length-1].style.fontStyle = "italic";
		this.select.options[this.select.options.length-1].style.color = "#6F6F6F";
	}
	
	for(var v=0; v<this.options.length; v++)
	{
		if(this.matches(this.options[v]))
		{
			this.select.options[this.select.options.length] = new Option(this.options[v].text,this.options[v].value);
		}
	}
	
	if(this.select.options.length == 2)
	{
		this.enablePropagation = true;
		this.select.options[1].selected = true;	
	}
	else
	{
		this.select.options[0].selected = true;
	}
}

/**
  * Remet toutes les valeurs dans la liste
  */
autocomplete.prototype.reset = function()
{

	this.select.options.length 	= 0;
	for(var v=0; v<this.options.length; v++)
	{
		this.select.options[this.select.options.length] = new Option(this.options[v].text, this.options[v].value);
	}	

}

/**
  * Bloque la propagation de l'evenement clavier ainsi que le conportement par défaut du sélect si besoin est. 
  * Si l'evenement n'a pas besoin d'être bloqué car il ne reste plus qu'une seule option au select, on déclenche l'evenement onchange sur cette dernière valeur
  * @param e l'evenement clavier qui a été déclanché
  */
autocomplete.prototype.handleEventPropagation = function(e)
{
   if(this.enablePropagation !== true)
   {
	   if (e.cancelBubble != null)
	      e.cancelBubble = true;
	   if (e.stopPropagation)
	      e.stopPropagation();
	   if (e.preventDefault)
	      e.preventDefault();
	   if (window.event)
	      e.returnValue = false;
	   if (e.cancel != null)
	      e.cancel = true;
	}
	else
	{
		this.fireOnChange();
	}

}

/**
  * Déclenche l'evenement onchange sur le select
  */
autocomplete.prototype.fireOnChange = function()
{

    if (document.createEventObject)
	{
        var evt = document.createEventObject();
        return this.select.fireEvent("onchange",evt);
    }
    else
	{
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", true, true ); // event type,bubbling,cancelable
        return !this.select.dispatchEvent(evt);
    }
}

/**
  * Retoune le caractère de la touche tapée.
  * Les touches du pavé numérique ont besoin d'un traitement spécial
  * @param charCode le code ASCII du caractère
  */
autocomplete.prototype.getKey = function(charCode)
{
	var keys = {"96" : "0", "97" : "1", "98" : "2", "99" : "3", "100" : "4", "101" : "5", "102" : "6", "103" : "7", "104" : "8", "105" : "9"} 
	return keys[charCode] || String.fromCharCode(charCode);
}

autocomplete.prototype.keydown = function(e)
{
	var evt = (e) ? e : 
					(window.event) ? window.event : 
												null; 

	var key = evt.keyCode;
    var charCode = (evt.charCode) ? evt.charCode : 
										((evt.keyCode) ? evt.keyCode : 
															((evt.which) ? evt.which : 0));

	if(charCode && !evt.ctrlKey && !evt.altKey && charCode != 16)
	{
			
		// touche <-- , supprime le dernier caractère
		if(charCode == 8)
		{	
			this.charSequence = this.charSequence.slice(0, this.charSequence.length-1);
			this.filter();
			this.handleEventPropagation(evt);
			return false;
		}
		
		// touche ESC, supprime tous les derniers caractères
		else if(charCode == 27)
		{
			this.charSequence = "";
			this.reset();	
			return false;
		}
		// touche alpha numérique, ajoute un caractère
		else if((charCode >= 48 && charCode <= 90) || (charCode >=96 && charCode <=106) || (charCode ==32))
		{
			this.charSequence += this.getKey(charCode);
			this.filter();
			this.handleEventPropagation(evt);
			return false;
		}
		
		// laisse passer les autres touches 
		else
		{	
			return true;
		}
		
	}
	
	else
	{
		window.cancelOnChange = false;	
		return true;
	}	
}

/**
  * Décharge le composant de la mémoire 
  * Cette méthode est appelée automatiquement quand la page est déchargée
  */
autocomplete.prototype.unload = function()
{
	if (window.removeEventListener)
	{	
		this.select.removeEventListener("keydown",this.keydownHandler,false);	
		window.removeEventListener("beforeunload", this.unloadHandler,false);
	}
	else if(window.detachEvent)
	{
		this.select.detachEvent("onkeydown",this.keydownHandler);	
		window.detachEvent("onbeforeunload", this.unloadHandler);
	}
	else
	{
		this.select.onkeydown = null;
		window.onbeforeunload = null;
	}
	
	this.select 		 = null;
	this.options 		 = null;
	this.charSequence 	 = null;
	this.regex			 = null;
	
	this.keydownHandler = null;
	this.unloadHandler 	 = null;	
	
}