<link href="asset/css/select2.min.css" rel="stylesheet">
<script src="asset/js/jquery.min.js"></script>
<script src="asset/js/select2.full.js"></script>

<style>
	form {
    width: 900px;
    margin: auto;
        margin-top: auto;
    margin-top: 50px;
    border: solid 1px;
    padding: 30px;
}
</style>

<form action="traitement_sup_doublon_assujetti.php" method="post"  onsubmit="return confirm('Voulez-vous fusonner ?')">
	<h3 style="text-align: center;background: blue;padding: 20px;color: white;border-bottom: solid red 7px;">
		DGRAD / BUREAU ORDO. <br>
		Suppression des doublons sur les noms des Assujettis
	</h3>
	<p></p>
	<blockquote class="blockquote-success">
	<p style="font-size: 17px;border-left-color: #CCC;line-height: 20px;">Ne proceder a cette operation que lorsque vous etes certain de vouloir fusionner deux assujettis ou plus <br>
		dans la premiere liste de selection vous indiquez les assujettis a fusionner <br>
		dans la secode liste vous indiquz l'assujetti a conserver <br>
		et vous cliquer sur le bouton " <b>Fusionner</b> " pour valider l'operation
	</p>
	<footer style="color: #777;font-size: 18px;font-weight: bold;">NB. <cite title="Source Title">cette operation est irreverssible</cite></footer>
</blockquote>
<select name="s1[]" require id="liste-assujetti-select" multiple style="width:400px" class="select2" ></select>
=>
<select name="s2" require id="liste-assujetti-selected" style="width:400px" class="select2" ></select>
<button type="submit">Fusioner</button>
</form>
	
	<script type="text/javascript">
		   function repoFormatResult(repo) {
					console.log(repo);
			  var markup = '<div class="row-fluid">' +
				 '<div class="span2"><img src="' + repo.owner.avatar_url + '" /></div>' +
				 '<div class="span10">' +
					'<div class="row-fluid">' +
					   '<div class="span6">' + repo.full_name + '</div>' +
					   '<div class="span3"><i class="fa fa-code-fork"></i> ' + repo.forks_count + '</div>' +
					   '<div class="span3"><i class="fa fa-star"></i> ' + repo.stargazers_count + '</div>' +
					'</div>';

			  if (repo.description) {
				 markup += '<div>' + repo.description + '</div>';
			  }

			  markup += '</div></div>';

			  return markup;
		   }

		   function repoFormatSelection(repo) {
			  return repo.full_name;
		   }
		   
		//
		
		$(function () {	
			$('#liste-assujetti-select').select2({
			  placeholder: "Indiquer les assujettis a fusioner",
			  allowClear: true,
			  minimumInputLength: 2,
			  ajax: {
				url: "traitement_sup_doublon_assujetti.php",
				processResults: function (data) {
          //console.log(JSON.parse(data).items);
				  // Transforms the top-level key of the response object from 'items' to 'results'
				  return {
					results: JSON.parse(data).items
				  };
				},
				formatResult: repoFormatResult, // omitted for brevity, see the source of this page
				formatSelection: repoFormatSelection
			  }
			});
			$('#liste-assujetti-selected').select2({
			  placeholder: "Indiquer l'assujetti a conserver",
			  allowClear: true,
			  minimumInputLength: 2,
			  ajax: {
				url: "traitement_sup_doublon_assujetti.php",
				processResults: function (data) {
          //console.log(JSON.parse(data).items);
				  // Transforms the top-level key of the response object from 'items' to 'results'
				  return {
					results: JSON.parse(data).items
				  };
				},
				formatResult: repoFormatResult, // omitted for brevity, see the source of this page
				formatSelection: repoFormatSelection
			  }
			});
		
				
			});
		</script>