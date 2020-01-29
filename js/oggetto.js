for (var i = 0; i < nImmagini; i++)
	$("#bottoniFoto").append(createImageButton(i));

var selectedIndex = 0;
if($(".bottone").get(0))
	$(".bottone").get(0).click();

function createImageButton(n) {
	let btn = $("<button></button>");
	btn.text(n+1);
	btn.attr("id", "btn_"+n);
	btn.click(function() {
		let index = parseInt($(this).attr("id").substring(4));
		$("#img_"+selectedIndex).css("display", "none");
		$("#img_"+index).css("display", "block");
		$("#btn_"+selectedIndex).removeClass().addClass("btn bottone btn-primary");
		$("#btn_"+index).removeClass().addClass("btn bottone btn-success");
		selectedIndex = index;
	});
	btn.addClass("btn btn-primary bottone");
	return btn;
}

function removeImage() {
	if(nImmagini <= 0) return;
	let matches = $("#img_"+selectedIndex).attr("src").match(new RegExp('^imgs/(.*)$'));
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {"request" : "removeImage", "oggetto" : oggetto, "immagine" : matches[1]},
		dataType: "text",
		error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
	}).done(function(result) {
		if(result != 'OK') {
			console.log(result);
			alert(result);
		}else
			removeSelectedIndex();
	});
}

function removeSelectedIndex() {
	$("#img_"+selectedIndex).remove();
	$("#btn_"+selectedIndex).remove();
	nImmagini--;
	
	let n = 0;
	$(".immagine").each(function() {
		$(this).attr("id", "img_"+(n++));
	});
	n = 0;
	$(".bottone").each(function() {
		$(this).attr("id", "btn_"+(n++));
		$(this).text(n);
	});
	
	selectedIndex = 0;
	if($("#btn_0").length) $("#btn_0").click();
	
	if(nImmagini == 0)
		$('#btnRemoveImg').hide();
}

/*$('img').each(function() {
	EXIF.getData(this, function() {
	    var orientation = EXIF.getTag(this, "Orientation");
	    console.log(orientation);
	    if(orientation == 6)
	        $(this).css('transform', 'rotate(90deg)')
	});  
});*/