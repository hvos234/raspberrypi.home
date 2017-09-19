var startModel = model;
var startModelId = model_id;
var startName = name;
var startType = type;
var startDate = date;
var startCreatedAtStart = created_at_start;
var startCreatedAtEnd = created_at_end;
var startInterval = interval;
var startSelection = selection;


$(document).ready(function(){
    chartStart();
    //chartSetModelId();
    //chartSetName();
    
    $('#chart-model').bind('change', function(event) {
        chartSetModelId();
    });
    
    $('#chart-model_id').bind('change', function(event) {
        chartSetName();
    });
    
    $('#chart-name').bind('change', function(event) {
        chartSetChart();
    });
    
    $('#chart-name').bind('change', function(event) {
        chartSetChart();
    });
    
    $('input[name="Chart[type]"]').bind('change', function(event) {
        chartSetChart();
    });
    
    $('#chart-date').bind('change', function(event) {       
        chartSetChart();
    });
    
    $('#chart-created_at_start').bind('change', function(event) {
        chartSetChart();
    });
    
    $('#chart-created_at_end').bind('change', function(event) {
        chartSetChart();
    });
    
    $('#chart-interval').bind('change', function(event) {
        chartSetChart();
    });
    
    $('input[name="Chart[selection]"]').bind('change', function(event) {
        chartSetChart();
    });
});

function chartStart(){
    chartSetModel(startModel);
    chartSetModelId(startModelId);
    chartSetName(startName);
    chartSetType(startType);
    chartSetDate(startDate);
    chartSetCreatedAtStart(startCreatedAtStart);
    chartSetCreatedAtEnd(startCreatedAtEnd);
    chartSetInterval(startInterval);
    chartSetSelection(startSelection);
}

function chartChangeModel(){
    chartSetModel();
    chartSetModelId();
}

function chartSetModel(model){
    $('#chart-model').empty();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-models',  
        //data: {chart_type: chart_type},
        dataType: 'json', // the return is a json string
        async: false,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(index, model) {
                    $('#chart-model').append($("<option></option>").attr("value", model['model']).text(model['name']));
                });
                if(if (typeof model != 'undefined')){
                    $('#chart-model').val(model).change();
                }
            }
        }
    });
}

function chartSetModelId(){
    var model = $('#chart-model').val();
    
    $('#chart-model_id').empty();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-model-ids',  
        data: {model: model},
        dataType: 'json', // the return is a json string
        async: false,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(index, model_id) {
                    $('#chart-model_id').append($("<option></option>").attr("value", model_id['model_id']).text(model_id['name']));
                }); 
            }
        }
    });
    
    chartSetName();
}

function chartSetName(){
    var model_id = $('#chart-model_id').val();
    
    $('#chart-name').empty();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-names',  
        data: {model_id: model_id},
        dataType: 'json', // the return is a json string
        async: false,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(index, name) {
                    $('#chart-name').append($("<option></option>").attr("value", name['name']).text(name['name']));
                }); 
            }
        }
    });
    
    chartSetChart();
}

function chartSetChart(){
    var model = $('#chart-model').val();
    var model_id = $('#chart-model_id').val();
    var name = $('#chart-name').val();
    
    var type = $('input[name="Chart[type]"]:checked').val();
    var date = $('#chart-date').val();
    var created_at_start = $('#chart-created_at_start').val();
    var created_at_end = $('#chart-created_at_end').val();
    var interval = $('#chart-interval').val();
    var selection = $('input[name="Chart[selection]"]:checked').val();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-chart',  
        data: {model: model, model_id: model_id, name: name, type: type, date: date, created_at_start: created_at_start, created_at_end: created_at_end, interval: interval, selection: selection},
        dataType: 'json', // the return is a json string
        success: function(data) {
            // the php HighchartsWidget demants that the data in php has a array with four array in it, so the title in the array is in the char key
            data['title'] = data['char']['title'];
            // the data of the series comes as a string it must be a int or float or it will not display the data
            data['series'] = highchartsSeriesDataToInt(data['series']);
            //console.log(data);

            $('#highcharts').highcharts(data);
        }
    });
}

// the data of the series comes as a string it must be a int or float or it will not display the data
function highchartsSeriesDataToInt(series){
    var _return = [];
    if('data' in series){ // if data exists it is one serie
        _return = highchartsSeriesDataToIntData(series);
    }else { // multiple series
        $.each(series, function(index, element) {
            _return[index] = highchartsSeriesDataToIntData(element); 
        });
    }
    return _return;
}

function highchartsSeriesDataToIntData(series){
    var _return = [];
    $.each(series, function(index, element) {
        if('data' == index){
            _return[index] = [];
            $.each(element, function(index2, data) {
               _return[index][index2] = parseFloat(data);
            });
        }else {
            _return[index] = element;
        }
    });
    return _return;
}