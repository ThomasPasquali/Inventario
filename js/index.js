var columnDefs = [
    //{field:"Colori"},
    {field:"ID", editable: false},
    {field:"Codice"},
    {field:"Descrizione"},
    {field:"Larghezza"},
    {field:"Altezza"},
    {field:"Profondita"},
    {field:"Anno"},
    {field:"Materiale"},
    {field:"Proprieta"},
    {field:"Donatore"},
    {field:"Data_donazione"},
    {field:"Stato"},
    {field:"Ubicazione"},
    {field:"Quantita"},
    {field:"Note"},
    {field:"Valore"},
    //{field:"Link"}
  ];

var grid, data;
var gridOptions = {
    defaultColDef: {
        sortable: true,
        resizable: true,
        editable: true,
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
    },
    /*
    sideBar: {
    toolPanels: [
      {
        id: 'columns',
        labelDefault: 'Columns',
        labelKey: 'columns',
        iconKey: 'columns',
        toolPanel: 'agColumnsToolPanel',
        toolPanelParams: {
          suppressRowGroups: true,
          suppressValues: true,
          suppressPivots: true,
          suppressPivotMode: true,
          suppressSideButtons: true,
          suppressColumnFilter: true,
          suppressColumnSelectAll: true,
          suppressColumnExpandAll: true,
        },
      },
    ],
    defaultToolPanel: 'columns',
  },*/
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

$(document).ready(async function() {
    grid = new agGrid.Grid($('#table')[0], gridOptions);
    refreshTableData();
    gridOptions.columnApi.autoSizeColumns(['ID', 'Codice', 'Larghezza', 'Altezza', 'Profondita', 'Anno', 'Data_donazione', 'Stato', 'Ubicazione', 'Quantita', 'Valore']);
});

/*************FUNCIONS**************/
async function refreshTableData() {
    data = await getData();
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