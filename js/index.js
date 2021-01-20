var columnDefs = [
    //{field:"Colori"},
    {field:"ID", editable: false},
    {field:"Codice"},
    {field:"Descrizione", filter: 'agTextColumnFilter'},
    {field:"Larghezza"},
    {field:"Altezza"},
    {field:"Profondita"},
    {field:"Anno", filter: 'agTextColumnFilter'},
    {field:"Materiale", filter: 'agTextColumnFilter'},
    {field:"Proprieta", filter: 'agTextColumnFilter'},
    {field:"Donatore", filter: 'agTextColumnFilter'},
    {field:"Data_donazione", filter: 'agDateColumnFilter'},
    {field:"Stato", filter: 'agTextColumnFilter'},
    {field:"Ubicazione", filter: 'agTextColumnFilter'},
    {field:"Quantita"},
    {field:"Note", filter: 'agTextColumnFilter'},
    {field:"Valore"},
    //{field:"Link"}
  ];

var grid, data;
var gridOptions = {
    defaultColDef: {
        sortable: true,
        resizable: true,
        editable: true,
        filter: 'agNumberColumnFilter',
        comparator: function(valueA, valueB, nodeA, nodeB, isInverted) {
            if(parseInt(valueA)&&parseInt(valueB))
                return parseInt(valueA) - parseInt(valueB);
            return valueA - valueB;
        }
    },
    columnDefs: columnDefs,
    animateRows: true,
    debounceVerticalScrollbar: true,
    undoRedoCellEditing: true,
    onCellDoubleClicked: function (e) {
        if(e.column.colId == 'ID') {
            let win = window.open('oggetto.php?id='+e.value, '_blank');
            win.focus();
        }
    },
    onCellValueChanged: function (e) {
        $.ajax({
    		url: "runtime/handler.php",
    		type: "POST",
    		data: {
    			'request': 'updateValue',
    			'field': e.colDef.field,
    			'newVal': e.newValue,
    			'oggetto': e.data.ID
    		},
    		dataType: "text",
    		error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(textStatus);
                console.log(errorThrown);
                alert('Errore durante la modifica');
			}
    	}).done(function(result) {
    		if(result != 'OK') {
                console.log(result);
                gridOptions.api.undoCellEditing();
				alert('Errore durante la modifica');
    		}
    	});
    }
}

/*************HANDLERS**************/

//Eventi menù
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

//Refresh on focus
$(window).focus(function() { refreshTableData(); });

//Columns handled
$('.colonna').on('change', function() {
    //Non il massimo ma limita lo spreco
    if(gridOptions.columnApi.getColumn($(this).attr('name')).visible != $(this).is(':checked'))
        setColumnsPreferences();
    gridOptions.columnApi.applyColumnState({
        state: [{
            colId: $(this).attr('name'),
            hide: (!$(this).is(':checked'))
        }]
    });
});

$(document).ready(async function() {
    grid = new agGrid.Grid($('#table')[0], gridOptions);
    refreshTableData();
    gridOptions.columnApi.autoSizeColumns(['ID', 'Codice', 'Larghezza', 'Altezza', 'Profondita', 'Anno', 'Data_donazione', 'Stato', 'Ubicazione', 'Quantita', 'Valore']);
    
    //Columns init
    $('.colonna').each(function() {
        $(this).trigger('change');
    });
});

/*************FUNCIONS**************/
async function refreshTableData(params) {
    data = await getData(params);
    gridOptions.api.setRowData(data);
}

function getData(options = null) {
	return $.ajax({
		url: "runtime/handler.php",
		type: "POST",
		data: {request: "query", options: options},
        dataType: "json"
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
        if(res.status != 'OK')
            alert(res.error);
		else {
            data.push(res.record);
            gridOptions.api.setRowData(data);
            gridOptions.columnApi.applyColumnState({
                state: [{ colId: 'ID', sort: 'desc' }],
                defaultState: { sort: null },
            });
        }
	});
}

function setForPrint() {
    $('#navbar').hide();
    $('#navbar-targets').hide();
    gridOptions.api.setDomLayout('print');
    window.print();
    gridOptions.api.setDomLayout(null);
    $('#navbar-targets').show();
    $('#navbar').show();
}

function exportCSV() {
    gridOptions.api.exportDataAsCsv();
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