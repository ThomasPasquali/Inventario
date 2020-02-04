/*********************CUSTOM FILTERS***********************/
var minMaxFilterEditor = function(cell, onRendered, success, cancel, editorParams){
    var end;
    var container = document.createElement("span");
    //create and style inputs
    var start = document.createElement("input");
    start.setAttribute("type", "number");
    start.setAttribute("placeholder", "Min");
    start.setAttribute("min", 0);
    start.style.padding = "4px";
    start.style.width = "50%";
    start.style.boxSizing = "border-box";
    start.value = cell.getValue();
    function buildValues(){
        success({
            start:start.value,
            end:end.value,
        });
    }
    function keypress(e){
        if(e.keyCode == 13) buildValues();
        if(e.keyCode == 27) cancel();
    }
    end = start.cloneNode();
    end.setAttribute("placeholder", "Max");
    start.addEventListener("change", buildValues);
    start.addEventListener("blur", buildValues);
    start.addEventListener("keydown", keypress);
    end.addEventListener("change", buildValues);
    end.addEventListener("blur", buildValues);
    end.addEventListener("keydown", keypress);
    container.appendChild(start);
    container.appendChild(end);
    return container;
}

//custom max min filter function
function minMaxFilterFunction(headerValue, rowValue, rowData, filterParams){
	let res = (!headerValue.start && !headerValue.end);
	rowValue = parseInt(rowValue);
    if(rowValue > 0) {
        if(headerValue.start != "")
            if(headerValue.end != "")
                res = (rowValue >= headerValue.start && rowValue <= headerValue.end);
            else
                res = (rowValue >= headerValue.start);
        else if(headerValue.end != "")
                res = (rowValue <= headerValue.end);
        //console.log("min: "+headerValue.start+" max: "+headerValue.end+" val: "+rowValue+" res: "+res);
    }
    return res; //must return a boolean, true if it passes the filter.
}

function annoFilterFunction(headerValue, rowValue, rowData, filterParams){
	if((headerValue.start && !headerValue.start.match(/^\d{4}$/)) || (headerValue.end && !headerValue.end.match(/^\d{4}$/)))
		return true;
    return minMaxFilterFunction(headerValue, rowValue && rowValue.length > 4 ? rowValue.split('-')[0] : rowValue, rowData, filterParams);
}

/*********************TABLE DECLARATION***********************/
const digitWidth = 3;
table = new Tabulator("#tabella", {
	layout:"fitColumns",
    /*pagination:"local",
    paginationSize:20,
    paginationSizeSelector:[10, 20, 30],*/
	movableColumns:true,
	printAsHtml:true,
	printVisibleRows:true,
    cellEdited:function(cell){
    	//console.log(cell);
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
				cell.setValue('');
				showAlert(result, 'danger');
    		}
    	});
     },
     columns:[
     	{title:"ID",field:"ID",sorter:"number",headerFilter:"number",headerFilterPlaceholder:"ID = ?",headerFilterFunc:"="},
     	{title:"Descrizione",field:"Descrizione",headerFilterPlaceholder:"Descrizione...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Larghezza",field:"Larghezza",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:minMaxFilterFunction,editor:"number",validator:["min:1", "max:99999", "integer"]},
     	{title:"Altezza",field:"Altezza",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:minMaxFilterFunction,editor:"number",validator:["min:1", "max:99999", "integer"]},
     	{title:"Profondita",field:"Profondita",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:minMaxFilterFunction,editor:"number",validator:["min:1", "max:99999", "integer"]},
     	{title:"Anno",field:"Anno",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:annoFilterFunction,editor:"input",validator:["regex:\\d{4}(-\\d{4})?"]},
     	{title:"Materiale",field:"Materiale",headerFilterPlaceholder:"Materiale...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Proprietà",field:"Proprieta",headerFilterPlaceholder:"Proprietario...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Donatore",field:"Donatore",headerFilterPlaceholder:"Donatore...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Data donazione",field:"Data_donazione",headerFilterPlaceholder:"Data donazione...",align:"center",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",sorterParams:{alignEmptyValues:"bottom"},editor:"input",validator:["regex:\\d{4}-\\d{2}-\\d{2}"]},
     	{title:"Stato",field:"Stato",headerFilterPlaceholder:"Stato...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Note",field:"Note",headerFilterPlaceholder:"Note...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Valore",field:"Valore",sorter:"number",headerFilter:"number",headerFilterPlaceholder:"Valore = ?",headerFilterFunc:"=",editor:"number",validator:["min:0", "max:999999", "numeric"]},
     	{title:"",field:"Link",cellClick:function(e, cell) { apriPaginaOggetto(cell) }}
     ]
});

/*********************CALL FUNCTIONS***********************/
function printTable() {
	table.getColumn('Link').hide();
	table.print();
	table.getColumn('Link').show();
}

function apriPaginaOggetto(cell) {
	window.open('oggetto.php?id='+cell.getRow().getCells()[0].getValue(), '_blank');
}

function aggiornaTabella() {
	//console.log(getFormAsJSON($("#form-aggiorna")));
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {request : "query"},
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus);
			console.log(errorThrown);
		}
	}).done(function(result) {
		 //console.log(result);
		for (var i = 0; i < result.length; i++)
			 result[i].Link = "Img/Etic";
		 table.setData(result);
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

/*********************EVENT HANDLERS***********************/
$('#btnAggiornaTabella').click(function(){
	$('#btnCollapseMenu').click();
	aggiornaTabella();
});

$(`.colonna`).click(function() {
	if(table.getColumn($(this).attr('name')).getVisibility())
		table.getColumn($(this).attr('name')).hide();
	else
		table.getColumn($(this).attr('name')).show();
	setColumnsPreferences();
});

/*********************INIT***********************/
$('#btnAggiornaTabella').click();
$('.colonna').each(function() {
	if(!$(this).is(':checked'))
		table.getColumn($(this).attr('name')).hide();
});
