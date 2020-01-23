table = new Tabulator("#tabella", {
	layout:"fitColumns",
	height:"80%",
    layout:"fitColumns",
    pagination:"local",
    paginationSize:20,
    paginationSizeSelector:[10, 20, 30],
    movableColumns:true,
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
		
		 for (var i = 0; i < result.Colonne.length; i++)
			 result.Colonne[i] = JSON.parse(result.Colonne[i]);
		 result.Colonne.push({title:"",field:"modifica",cellClick:function(e, cell){ modifica(cell) }});
		 
		 for (var i = 0; i < result.Dati.length; i++)
			 result.Dati[i].modifica = "Modifica"; 
		 
		 table.setColumns(result.Colonne);
		 table.setData(result.Dati);
	});
	
	//$('#table').css('display', 'block');
}

$("#form-aggiorna").submit(function(e){ e.preventDefault(); aggiornaTabella();});
$('#btnAggiorna').click(function(){ aggiornaTabella(); });

//custom max min header filter
var minMaxFilterEditor = function(cell, onRendered, success, cancel, editorParams){
    var end;
    var container = document.createElement("span");
    //create and style inputs
    var start = document.createElement("input");
    start.setAttribute("type", "number");
    start.setAttribute("placeholder", "Min");
    start.setAttribute("min", 0);
    start.setAttribute("max", 100);
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
    //headerValue - the value of the header filter element
    //rowValue - the value of the column in this row
    //rowData - the data for the row being filtered
    //filterParams - params object passed to the headerFilterFuncParams property
    if(rowValue)
        if(headerValue.start != "")
            if(headerValue.end != "")
                return rowValue >= headerValue.start && rowValue <= headerValue.end;
            else
                return rowValue >= headerValue.start;
        else
            if(headerValue.end != "")
                return rowValue <= headerValue.end;
    return false; //must return a boolean, true if it passes the filter.
}