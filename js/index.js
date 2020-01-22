table = new Tabulator("#tabella", {
	layout:"fitColumns"
});

$('#btnAggiorna').click(function() {
	//table.setData("runtime/handler.php", getFormAsJSON($("#form-aggiorna")), "POST");
	
	//TODO
	var request = $.ajax({
	  url: "script.php",
	  method: "POST",
	  data: { id : menuId },
	  dataType: "html"
	});
	 
	request.done(function( msg ) {
	  console.log(msg);
	});
});

