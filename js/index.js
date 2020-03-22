function annoFilterFunction(headerValue, rowValue, rowData, filterParams){
	if((headerValue.start && !headerValue.start.match(/^\d{4}$/)) || (headerValue.end && !headerValue.end.match(/^\d{4}$/)))
		return true;
    return minMaxFilterFunction(headerValue, rowValue && rowValue.length > 4 ? rowValue.split('-')[0] : rowValue, rowData, filterParams);
}

/*********************TABLE DECLARATION***********************/
const digitWidth = 3;
table = new Tabulator("#tabella", {
	layout:"fitColumns",
	headerSort:false,
	movableColumns:true,
	printAsHtml:true,
	printVisibleRows:true,
    cellEdited:function(cell){
    	$.ajax({
    		url: "runtime/handler.php",
    		type: "POST",
    		data: {
    			'request':'updateValue',
    			'field':cell.getColumn().getField(),
    			'newVal':cell.getValue(),
    			'oggetto':cell.getRow().getCell('ID').getValue()
    		},
    		dataType: "text",
    		error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(textStatus);
				console.log(errorThrown);
			}
    	}).done(function(result) {
    		if(result != 'OK') {
				console.log(result);
				cell.restoreOldValue();
				showAlert(result, 'danger');
    		}
    	});
     },
     columns:[
		{field:"Colori", visible:false},
		{title:"ID",field:"ID",sorter:"number",sorterParams:{alignEmptyValues:"bottom"}},
		{title:"Codice",field:"Codice",editor:"input",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},formatter:function(cell) {
			let val = cell.getValue();
			if(val) {
				let cod_col = val.split(' ');
				val = cod_col[0];
				let colori = cod_col[1];
				if(colori) {
					colori = colori.split(',');
					if(colori.length == 1)	cell.getElement().style.backgroundColor = colori[0];
					else					cell.getElement().style.backgroundImage = `linear-gradient(to right, ${colori.join(', ')})`;
				}
			}
			return val;
		},cellEditing:function(cell){
			let val = cell.getValue();
			if(val) val = val.split(' ')[0];
			cell.setValue(val);
		}},
     	{title:"Descrizione",field:"Descrizione",editor:"input",sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Larghezza",field:"Larghezza",editor:"number",validator:["min:1", "max:99999", "integer"],sorter:"number",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Altezza",field:"Altezza",editor:"number",validator:["min:1", "max:99999", "integer"],sorter:"number",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Profondita",field:"Profondita",editor:"number",validator:["min:1", "max:99999", "integer"],sorter:"number",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Anno",field:"Anno",editor:"input",validator:["regex:\\d{4}(-\\d{4})?"],sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Materiale",field:"Materiale",editor:"input",sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Proprietà",field:"Proprieta",editor:"input",sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Donatore",field:"Donatore",editor:"input",sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Data donazione",field:"Data_donazione",editor:"input",validator:["regex:\\d{4}-\\d{2}-\\d{2}"],sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Stato",field:"Stato",editor:"select",editorParams:{values:["Buono","Usurato","Pessimo"]},sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
		{title:"Ubicazione",field:"Ubicazione",editor:"input",sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
		{title:"Quantit&agrave;",field:"Quantita",editor:"number",validator:["min:1", "max:99999", "integer"],sorter:"number",sorterParams:{alignEmptyValues:"bottom"}},
		{title:"Note",field:"Note",editor:"input",sorter:"string",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"Valore",field:"Valore",editor:"number",validator:["min:0", "max:999999", "numeric"],sorter:"number",sorterParams:{alignEmptyValues:"bottom"}},
     	{title:"",field:"Link",formatter:"link",formatterParams:{
			label:"Altro",
			url:function(cell) { return 'oggetto.php?id='+cell.getValue(); },
			target:"_blank",
		}}
     ]
});

/*********************CALL FUNCTIONS***********************/
function tableLoading() {
	$('#tabella').css('visibility', 'hidden');
	$('#loading').show();
	console.log('loading')
}

function tableLoaded() {
	$('#tabella').css('visibility', 'visible');
	$('#loading').hide();
	console.log('loaded')
}

function printTable() {
	table.getColumn('Link').hide();
	table.print();
	table.getColumn('Link').show();
}

function aggiornaTabella(options = null) {
	//console.log(options);
	tableLoading();
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {request: "query", options: options},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus);
			console.log(errorThrown);
		}
	}).done(function(result) {
		//console.log(result);
		table.setData(result);
		tableLoaded();
	});
}

function setColumnsPreferences() {
	let cols = [];
	$('.colonna:checked').each(function() {
		cols.push($(this).attr('name'));
	});
	if(cols.length > 0)
		$.ajax({
			url: "runtime/handler.php",
			type: "POST",
			data: {request : "setPreference", name : 'columns', value : cols},
			dataType: "text",
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(textStatus);
				console.log(errorThrown);
			}
		});
}

function newOggetto() {
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {request : "newOggetto"},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus);
			console.log(errorThrown);
		}
	}).done(function(res) {
		if(res.status != 'OK')	showAlert(res.error, 'danger');
		else					table.addRow(res.record, true);
	});
}

/*********FILTRI********/
var operatori = ['like','=','!=','<','<=','>','>='];
var exclCols = ['Colori', 'Link'];
function addFiltro() {
	let colonna = $('<select name="colonna"></select>');
	for (const col of table.getColumns())
		if(!exclCols.includes(col.getField())) colonna.append($(`<option value="${col.getField()}">${col.getField()}</option>`));

	let operatore = $('<select name="operatore"></select>');
	for (const op of operatori)
		operatore.append($(`<option value="${op}">${op}</option>`));

	let valore = $('<input type="text" name="value" placeholder="Valore...">');

	let elimina = $('<button style="margin-left: 30px;">Elimina</button>').click(function() { $(this).parent().remove(); });
	
	$('#navbar-targets > div[data-value="filtri"] ol').append($('<li></li>').append(colonna, operatore, valore, elimina));
}

var filtri = [];
function applyFiltri() {
	tableLoading();
	filtri = [];
	for(const fil of $('#navbar-targets > div[data-value="filtri"] > ol > li')) {
		let tmp = $(fil).children('input[name="value"]').val();
		let val = parseInt(tmp);
		if(isNaN(val)) val = tmp;

		filtri.push({
			field: $(fil).children('select[name="colonna"]').val(),
			type: $(fil).children('select[name="operatore"]').val(),
			value: val
		});
	}
		
	table.setFilter(filtri);
	tableLoaded();
}

function resetFiltri() {
	tableLoading();
	table.setFilter((filtri = []));
	tableLoaded();
}

/*********ORDINAMENTI********/
function addOrdinamento() {
	let colonna = $('<select name="colonna"></select>');
	for (const col of table.getColumns())
		if(!exclCols.includes(col.getField())) colonna.append($(`<option value="${col.getField()}">${col.getField()}</option>`));
	
	let dir = $('<select name="dir"><option value="asc">Crescente</option><option value="desc">Decrescente</option></select>');

	let elimina = $('<button>Elimina</button>').click(function() { $(this).parent().remove(); });

	$('#navbar-targets > div[data-value="ordinamenti"] ol').append($('<li></li>').append(colonna, dir, elimina));
}

var ordinamenti = [];
function applyOrdinamenti() {
	tableLoading();
	ordinamenti = [];
	for(const ord of $('#navbar-targets > div[data-value="ordinamenti"] > ol > li'))
		ordinamenti.unshift({
			column: $(ord).children('select[name="colonna"]').val(),
			dir: $(ord).children('select[name="dir"]').val(),
		});
	table.setSort(ordinamenti);
	tableLoaded();
}

function resetOrdinamenti() {
	tableLoading();
	table.setSort((ordinamenti = []));
	tableLoaded();
}

/*********************EVENT HANDLERS***********************/
$(`.colonna`).click(function() {
	if(table.getColumn($(this).attr('name')).getVisibility())
		table.getColumn($(this).attr('name')).hide();
	else
		table.getColumn($(this).attr('name')).show();
	setColumnsPreferences();
});

$('#navbar > div').click(function() {
	let target = $(this).data('target');
	if(target)
		$('#navbar-targets > div').each(function() {
			if($(this).data('value') == target && $(this).css('display') != 'grid')
				$(this).css('display','grid');
			else
				$(this).hide();
		});
});

$(window).focus(function() { aggiornaTabella(); });

/*********************INIT***********************/
aggiornaTabella();
$('.colonna').each(function() {
	if(!$(this).is(':checked'))
		table.getColumn($(this).attr('name')).hide();
});
