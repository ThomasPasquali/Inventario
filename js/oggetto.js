/*********************EVENT HANDLERS***********************/
function f5(e = null) {
	if(e == null || (e.which || e.keyCode) == 116) {
		if(e != null) e.preventDefault();
		window.location.replace('oggetto.php?id='+oggetto);
	}
}
document.addEventListener("keydown", f5);

$('.etichetta').hover(
	function(){ $(this).removeClass().addClass('mr-3 bg-danger etichetta') },
	function(){ $(this).removeClass().addClass('mr-3 etichetta') },
);

$('.etichetta').click(function() {
	let elem = $(this);
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {"request" : "removeLabel", "oggetto" : oggetto, "etichetta" : $(this).text()},
		dataType: "text",
		error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
	}).done(function(result) {
		if(result != 'OK') {
			console.log(result);
			alert(result);
		}else
			f5();
	});
});

$('#fieldEtichetta').keyup(function() {
	let search = $(this).val().toUpperCase();
	$('.hint-etichetta').each(function() {
		$(this).css('display', ($(this).html().toUpperCase().indexOf(search) > -1 ? 'block' : 'none'));
	});
});

$('.hint-etichetta').click(function() {
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {"request" : "addEtichettaToOggetto", "oggetto" : $('#formEtichette input[name="id"]').val(), "nomeEtichetta" : $(this).html()},
		error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
	}).done(function() { window.location.reload(); });
});

$('#formElimina input[type=button]').click(function(e) {
	if(confirm('Sei sicuro di voler eliminare l\'oggetto e tutti i suoi collegamenti con etichette ed immagini?'))
		$('#formElimina').submit();
});

var oldVal;
$('#tabellaDati textarea').focusin(function() { oldVal = $(this).val(); });

$('#tabellaDati textarea').focusout(function() {
	let field = $(this);
	let fields = $('#tabellaDati').children().filter(function() { return $(this).attr('name')?true:false; });
	let data = {};
	for (const el of fields)
		data[$(el).attr('name')] = $(el).val()?$(el).val():$(el).text();

	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {"request" : "updateOggetto", "oggetto" : data},
		dataType: "text",
		error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
	}).done(function(res) {
		if(res != 'OK') {
			alert(res);
			field.val(oldVal);
		}
	});
});

/*********************CALL FUNCTIONS***********************/
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

function deleteLabel(id) {
	if(confirm('Sei sicuro di voler eliminare l\'etichetta e tutti i suoi collegamenti con gli oggetti?'))
		$.ajax({
			url: "runtime/handler.php",
			type: "POST",
			data: {"request" : "deleteLabel", "etichetta" :id},
			dataType: "text",
			error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
		}).done(function(result) {
			if(result != 'OK') {
				console.log(result);
				alert(result);
			}else
				window.location.reload();
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

function prossimoOggetto() {
	let params = {};
	let url = window.location.href.split('?');
	url[1] = url[1].split('&');
	for (const param of url[1]) {
		let tmp = param.split('=');
		params[tmp[0]] = tmp[1];
	}
	window.location.href = (url[0]+'?id='+(parseInt(params.id)+1));
}

function precedenteOggetto() {
	let params = {};
	let url = window.location.href.split('?');
	url[1] = url[1].split('&');
	for (const param of url[1]) {
		let tmp = param.split('=');
		params[tmp[0]] = tmp[1];
	}
	window.location.href = (url[0]+'?id='+(parseInt(params.id)-1));
}

/*********************INIT***********************/
for (var i = 0; i < nImmagini; i++)
	$("#bottoniFoto").append(createImageButton(i));

var selectedIndex = 0;
if($(".bottone").get(0))
	$(".bottone").get(0).click();