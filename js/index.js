//custom max min header filter
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

table = new Tabulator("#tabella", {
	layout:"fitData",
	height:"80%",
    layout:"fitColumns",
    pagination:"local",
    paginationSize:20,
    paginationSizeSelector:[10, 20, 30],
    movableColumns:true,
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
    		error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
    	}).done(function(result) {
    		if(result != 'OK') {
    			console.log(result);
    			alert(result);
    		}
    	});
     },
     columns:[
     	{title:"ID",field:"ID",sorter:"number",headerFilter:"number",headerFilterPlaceholder:"ID = ?",headerFilterFunc:"="},
     	{title:"Descrizione",field:"Descrizione",headerFilterPlaceholder:"Descrizione...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Larghezza",field:"Larghezza",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:minMaxFilterFunction,editor:"number",validator:["min:1", "max:99999", "integer"]},
     	{title:"Altezza",field:"Altezza",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:minMaxFilterFunction,editor:"number",validator:["min:1", "max:99999", "integer"]},
     	{title:"Profondita",field:"Profondita",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:minMaxFilterEditor,headerFilterFunc:minMaxFilterFunction,editor:"number",validator:["min:1", "max:99999", "integer"]},
     	{title:"Anno",field:"Anno",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"number",headerFilterPlaceholder:"Anno = ?",headerFilterFunc:"=",editor:"number",validator:["min:0", "max:9999", "integer"]},
     	{title:"Anno valido",field:"Anno_valido",sorter:"number",sorterParams:{alignEmptyValues:"bottom"},headerFilter:true,headerFilterParams:{values:{Y:"Si", N:"No", "<non_specificato>":""}},editor:"select", editorParams:{values:{"":"<non_specificato>","Y":"Si","N":"No"}}},
     	{title:"Materiale",field:"Materiale",headerFilterPlaceholder:"Materiale...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Proprietà",field:"Proprieta",headerFilterPlaceholder:"Proprietario...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Donatore",field:"Donatore",headerFilterPlaceholder:"Donatore...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Data donazione",field:"Data_donazione",headerFilterPlaceholder:"Data donazione...",align:"center",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",sorterParams:{alignEmptyValues:"bottom"},editor:"input",validator:["regex:\\d{4}-\\d{2}-\\d{2}"]},
     	{title:"Stato",field:"Stato",headerFilterPlaceholder:"Stato...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Note",field:"Note",headerFilterPlaceholder:"Note...",sorter:"string",sorterParams:{alignEmptyValues:"bottom"},headerFilter:"input",editor:"input"},
     	{title:"Valore",field:"Valore",sorter:"number",headerFilter:"number",headerFilterPlaceholder:"Valore = ?",headerFilterFunc:"=",editor:"number",validator:["min:0", "max:999999", "numeric"]},
     	{title:"",field:"immagini",cellClick:function(e, cell) { modifica(cell) }}
     ]
});

function modifica(cell) {
	window.open('oggetto.php?id='+cell.getRow().getCells()[0].getValue(), '_blank');
}

function aggiornaTabella() {
	//console.log(getFormAsJSON($("#form-aggiorna")));
	$.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: getFormAsJSON($("#form-aggiorna")),
		dataType: "json",
		error: function(XMLHttpRequest, textStatus, errorThrown) { alert(textStatus); }
	}).done(function(result) {
		 //console.log(result);
		
		 /*for (var i = 0; i < result.Colonne.length; i++)
			 result.Colonne[i] = JSON.parse(result.Colonne[i]);
		 result.Colonne.push({title:"",field:"modifica",cellClick:function(e, cell){ modifica(cell) }});
		 
		 */
		 
		 //table.setColumns(result.Colonne);
		for (var i = 0; i < result.length; i++)
			 result[i].immagini = "Immagini";
		 table.setData(result);
	});
	
	//$('#table').css('display', 'block');
}

$("#form-aggiorna").submit(function(e){
	e.preventDefault();
	$('#menuSelezioneBtn').click();
	aggiornaTabella();
});
$('#btnAggiorna').click(function(){
	$('#menuSelezioneBtn').click();
	aggiornaTabella();
});
