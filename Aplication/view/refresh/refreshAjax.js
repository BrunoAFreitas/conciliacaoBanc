/**
 * Ajax que tira refresh das paginas view LstRom.php. 
 *
 */
jQuery(document).ready(function(){
	jQuery('#form').submit(function(){
		var dados = jQuery( this ).serialize();
		dados.innerHTML = '<img src="img/loading_icon.gif"/>';	
		jQuery.ajax({
			type: "POST",
			url:  "../model/teste.php",
			data: dados,
			success: function( data )
			{
				alert( data );
			}
		});

		return false;
	});
});
