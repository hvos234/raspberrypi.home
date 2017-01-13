$(document).ready(function(){
	
	function getChartDatas(){
		// show all actions jopined to the device
		// get the value of to_device_id
		var chart_type = $('#data-chart_type option:selected').val(); // returns null if nothing has selected
		var chart_date = $('#data-chart_date option:selected').val(); // returns null if nothing has selected
		var chart_interval = $('#data-chart_interval option:selected').val(); // returns null if nothing has selected
		var taskdefinded_id = $('#data-taskdefinded_id option:selected').val(); // returns null if nothing has selected
		
		if(null != taskdefinded_id){
			$.ajax({
				// you can not use AjaxDeviceAction as action name, like in
				// the controller, they must be lowercase and with lines
				url: '?r=data/ajax-get-chart-datas',  
				data: {chart_type: chart_type, chart_date: chart_date, chart_interval: chart_interval, taskdefinded_id: taskdefinded_id},
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
	}
	
	// by default get Chart Data
	getChartDatas();
	
	// one of the selectboxes change get Chart Data
	$('#data-chart_type').on('change', function() {
            getChartDatas();
	});
	$('#data-chart_date').on('change', function() {
            getChartDatas();
	});
        $('#data-chart_interval').on('change', function() {
            getChartDatas();
	});
        $('#data-taskdefinded_id').on('change', function() {
            getChartDatas();
	});
});

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