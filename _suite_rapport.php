
	<style>
	.btn{
		-webkit-tap-highlight-color: rgba(0,0,0,0);
		box-sizing: border-box;
		font: inherit;
		overflow: visible;
		text-transform: none;
		-webkit-appearance: button;
		font-family: inherit;
		display: inline-block;
		padding: 6px 12px;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		touch-action: manipulation;
		cursor: pointer;
		user-select: none;
		background-image: none;
		border: 1px solid transparent;
		margin: 10px;
		color: #fff !important;
		background-color: #d81b60 !important;
		border-radius: 0;
		box-shadow: none;
		border-width: 1px;
	}
	</style>
	<style>
	#iframe-rapport{
		width: 100%;
		height: 700px;
		/* position: absolute;
		top: 0;
		z-index: 100; */		
	}
	</style>
	<iframe id="iframe-rapport" style="display:nonee" src="<?php echo $src;?>"></iframe>
	<script>
	function afficher_rapport(vrai){
		if(vrai){
			document.getElementById("div_ordonnancement").style.display="none";document.getElementById("iframe-rapport").style.display="block";
		}
		else {
			document.getElementById("div_ordonnancement").style.display="block";document.getElementById("iframe-rapport").style.display="none";
		}
	}
	
	</script>
	
