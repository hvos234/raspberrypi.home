$(document).ready(function(){
    // accordion
    $( '.accordion.enabled' ).accordion({
      collapsible: true,
      active: true,
      animate: 200
    });
    
    // sortable
    $( '.sortable' ).sortable({
        connectWith: ".column",
        handle: ".portlet-header",
        placeholder: "portlet-placeholder ui-corner-all",
        items: "li:not(.ui-state-disabled)",
        cursor: "move",
        update: function(event, ui) {
            chartSetWeights();
        }    
    });
    
    //$( '.sortable' ).disableSelection(); // It's useful if you want to make text unselectable. If, for instance, you want to make drag-and-drop elements with text on, it'd be annoying to the user if the text on the box accidentally would get selected when trying to drag the box.
    
    $('.name').bind('keypress', function(event) {
        chartSetName($(this).attr('index'));
    });
    
    // primary
    $('.primary_model').bind('change', function(event) {
        chartSetModelIds($(this).attr('index'), 'primary');
    });
    
    $('.primary_model_id').bind('change', function(event) {
        chartSetNames($(this).attr('index'), 'primary');
    });
    
    $('.primary_name').bind('change', function(event) {
        chartSetChart($(this).attr('index'));
    });
    
    $('.primary_selection input').bind('change', function(event) {
        chartSetChart($(this).closest('div').attr('index'));
    });
    
    // secondary
    $('.secondary_model').bind('change', function(event) {
        chartSetModelIds($(this).attr('index'), 'secondary');
    });
    
    $('.secondary_model_id').bind('change', function(event) {
        chartSetNames($(this).attr('index'), 'secondary');
    });
    
    $('.secondary_name').bind('change', function(event) {
        chartSetChart($(this).attr('index'));
    });
    
    $('.secondary_selection input').bind('change', function(event) {
        chartSetChart($(this).closest('div').attr('index'));
    });
    
    // rest
    $('.date, .created_at_start, .created_at_end, .interval').bind('change', function(event) {
        chartSetChart($(this).attr('index'));
    });
    
    $('.type input').bind('change', function(event) {
        chartSetChart($(this).closest('div').attr('index'));
    });
    
    $('.weight').bind('change', function(event) {
        chartChangeWeight($(this).attr('index'));
    });
    
    // set chart id, id is not empty or zero
    $('.id').each(function(index, object) {
        if ('' != $(object).val() || 0 != $(object).val()){
            chartSetChart($(object).attr('index'));
        }
    });
        
    // https://yii2-cookbook.readthedocs.io/forms-activeform-js/
    $('.chart-activeform').on('beforeSubmit', function (event) {
        var activeform = $(this);
        var index = $(this).attr('index');
        
        var id = $('input[name="Chart[' + index + '][id]"]').val();
        var name = $('input[name="Chart[' + index + '][name]"]').val();
        
        var primary_model = $('select[name="Chart[' + index + '][primary_model]"]').val();
        var primary_model_id = $('select[name="Chart[' + index + '][primary_model_id]"]').val();
        var primary_name = $('select[name="Chart[' + index + '][primary_name]"]').val();
        var primary_selection = $('input[name="Chart[' + index + '][primary_selection]"]:checked').val();

        var secondary_model = $('select[name="Chart[' + index + '][secondary_model]"]').val();
        var secondary_model_id = $('select[name="Chart[' + index + '][secondary_model_id]"]').val();
        var secondary_name = $('select[name="Chart[' + index + '][secondary_name]"]').val();
        var secondary_selection = $('input[name="Chart[' + index + '][secondary_selection]"]:checked').val();

        var date = $('select[name="Chart[' + index + '][date]"]').val();
        var created_at_start = $('input[name="Chart[' + index + '][created_at_start]"]').val();
        var created_at_end = $('input[name="Chart[' + index + '][created_at_end]"]').val();
        var type = $('input[name="Chart[' + index + '][type]"]:checked').val();
        var interval = $('select[name="Chart[' + index + '][interval]"]').val();

        var weight = $('select[name="Chart[' + index + '][weight]"]').val();

        $.ajax({
            type: 'POST',
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines
            url: '?r=chart/ajax-create-update&id=' + id,  
            data: {
                Chart: {
                    id: id,
                    name: name,
                    primary_model: primary_model, 
                    primary_model_id: primary_model_id, 
                    primary_name: primary_name, 
                    primary_selection: primary_selection, 
                    secondary_model: secondary_model, 
                    secondary_model_id: secondary_model_id, 
                    secondary_name: secondary_name, 
                    secondary_selection: secondary_selection, 
                    date: date,
                    created_at_start: created_at_start,
                    created_at_end: created_at_end,
                    type: type,
                    interval: interval,
                    weight: weight
                }
            },
            dataType: 'json', // the return is a json string
            success: function(data) {                
                if(data.errors) {                    
                    $.each(data.errors, function( field, error ) {
                        $(activeform).yiiActiveForm('updateAttribute', 'chart-' + index + '-' + field, [error]); //https://yii2-cookbook.readthedocs.io/forms-activeform-js/
                    });
                    
                    return false;
                }else {
                    /*// the php HighchartsWidget demants that the data in php has a array with four array in it, so the title in the array is in the char key
                    data['title'] = data['char']['title'];
                    // the data of the series comes as a string it must be a int or float or it will not display the data
                    data['series'] = highchartsSeriesDataToInt(data['series']);
                    //console.log(data);

                    //$('highcharts-' + index).highcharts(data);
                    var highcharts = Highcharts.chart('highcharts-' + index, data);
                    return true;*/
                    $('input[name="Chart[' + index + '][id]"]').val(data.id); // set id
                    $('button.chart_create[index="' + index + '"]').hide();
                    $('button.chart_update[index="' + index + '"]').show();
                    $('button.chart_update[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    $('button.chart_delete[index="' + index + '"]').show();
                    $('button.chart_delete[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    
                    return true;
                }
            }
        });
        
        return false;
    });
    
    // delete
    $('.chart_delete').bind('click', function(event) {
        var index = $(this).attr('index');
        var activeform = $('chart-activeform[index="' + index + '"]');
        
        var id = $('input[name="Chart[' + index + '][id]"]').val();
        
        $.ajax({
            type: 'POST',
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines
            url: '?r=chart/ajax-delete&id=' + id,  
            data: {
                Chart: {
                    id: id
                }
            },
            dataType: 'json', // the return is a json string
            success: function(data) {                
                if(data.errors) {                    
                    alert(data.errors)
                }else {
                    $('input[name="Chart[' + index + '][id]"]').val(''); // set id
                    $('input[name="Chart[' + index + '][name]"]').val(''); // set id
                    
                    $('button.chart_create[index="' + index + '"]').hide();
                    $('button.chart_update[index="' + index + '"]').show();
                    $('button.chart_update[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    $('button.chart_delete[index="' + index + '"]').show();
                    $('button.chart_delete[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    
                    // reset form
                    // primary
                    $('select[name="Chart[' + index + '][primary_model]"]').val('none');
                    
                    $('select[name="Chart[' + index + '][primary_model_id]"]').empty();
                    $('select[name="Chart[' + index + '][primary_model_id]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Chart[' + index + '][primary_model_id]"]').val('none');
                    
                    $('select[name="Chart[' + index + '][primary_name]"]').empty();
                    $('select[name="Chart[' + index + '][primary_name]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Chart[' + index + '][primary_name]"]').val('none');
                    
                    $('input[name="Chart[' + index + '][primary_selection]"]:checked').prop('checked', false);
                    $('input[name="Chart[' + index + '][primary_selection]"][value="normal"]').prop('checked', true);
                    
                    // secondary
                    $('select[name="Chart[' + index + '][secondary_model]"]').val('none');
                    
                    $('select[name="Chart[' + index + '][secondary_model_id]"]').empty();
                    $('select[name="Chart[' + index + '][secondary_model_id]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Chart[' + index + '][secondary_model_id]"]').val('none');
                    
                    $('select[name="Chart[' + index + '][secondary_name]"]').empty();
                    $('select[name="Chart[' + index + '][secondary_name]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Chart[' + index + '][secondary_name]"]').val('none');
                    
                    $('input[name="Chart[' + index + '][secondary_selection]"]:checked').prop('checked', false);
                    $('input[name="Chart[' + index + '][secondary_selection]"][value="normal"]').prop('checked', true);
                    
                    // rest
                    $('select[name="Chart[' + index + '][date]"]').val('today');
                    $('input[name="Chart[' + index + '][created_at_start]"]').val('');
                    $('input[name="Chart[' + index + '][created_at_end]"]').val('');
                    
                    $('input[name="Chart[' + index + '][type]"]').filter('[value="line"]').attr('checked', true);
                    $('select[name="Chart[' + index + '][interval]"]').val('every_hour');

                    //var weight = $('select[name="Chart[' + index + '][weight]"]').val();
                    
                    $('button.chart_create[index="' + index + '"]').show();
                    $('button.chart_create[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    $('button.chart_update[index="' + index + '"]').hide();
                    $('button.chart_delete[index="' + index + '"]').hide();
                }
            }
        });
    });
    
    // add remove
    chartShowHideButtonAddRemoveChart();
        
    // add
    $('.chart_add').bind('click', function(event) {
        chartAdd();
    });
    
    // remove
    $('.chart_remove').bind('click', function(event) {
        chartRemove();
    });
    
});

function chartSetName(index){
    var name = $('input[name="Chart[' + index + '][name]"]').val();
    $('.chart-header[index="' + index + '"] .text').html(name);
}

function chartSetModelIds(index, primary_secondary){
    var model = '';
    var model_id_selector = {};
    if('primary' == primary_secondary){
        model = $('select[name="Chart[' + index + '][primary_model]"]').val();
        model_id_selector = $('select[name="Chart[' + index + '][primary_model_id]"]');
    }
    
    if('secondary' == primary_secondary){
        model = $('select[name="Chart[' + index + '][secondary_model]"]').val();
        model_id_selector = $('select[name="Chart[' + index + '][secondary_model_id]"]');
    }
    
    $(model_id_selector).empty(); // empty the select box
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-model-ids',  
        data: {model: model},
        dataType: 'json', // the return is a json string
        async: true,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(model_id, name) {
                    if('none' == model_id){
                        $(model_id_selector).prepend($("<option></option>").attr("value", model_id).text(name)); // prepend put the none key to top
                    }else {
                        $(model_id_selector).append($("<option></option>").attr("value", model_id).text(name));
                    }
                });
                
                $(model_id_selector).find('option[value="none"]').attr('selected','selected'); // select the none option
            }
            
            chartSetNames(index, primary_secondary);
        }
    });
}

function chartSetNames(index, primary_secondary){
    var model = '';
    var model_id = '';
    var name_selector = {};
    
    if('primary' == primary_secondary){
        model = $('select[name="Chart[' + index + '][primary_model]"]').val();
        model_id = $('select[name="Chart[' + index + '][primary_model_id]"]').val();
        name_selector = $('select[name="Chart[' + index + '][primary_name]"]');
    }
    
    if('secondary' == primary_secondary){
        model = $('select[name="Chart[' + index + '][secondary_model]"]').val();
        model_id = $('select[name="Chart[' + index + '][secondary_model_id]"]').val();
        name_selector = $('select[name="Chart[' + index + '][secondary_name]"]');
    }
    
    $(name_selector).empty();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-names',  
        data: {
            model: model, 
            model_id: model_id
        },
        dataType: 'json', // the return is a json string
        async: true,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(name, value) {
                    $(name_selector).append($("<option></option>").attr("value", name).text(value));
                });
                $(name_selector).find('option:first').attr('selected','selected'); // select the first option
                
                chartSetChart(index);
            }
        }
    });
}

function chartSetChart(index){
    var name = $('input[name="Chart[' + index + '][name]"]').val();
    
    var primary_model = $('select[name="Chart[' + index + '][primary_model]"]').val();
    var primary_model_id = $('select[name="Chart[' + index + '][primary_model_id]"]').val();
    var primary_name = $('select[name="Chart[' + index + '][primary_name]"]').val();
    var primary_selection = $('input[name="Chart[' + index + '][primary_selection]"]:checked').val();
    
    var secondary_model = $('select[name="Chart[' + index + '][secondary_model]"]').val();
    var secondary_model_id = $('select[name="Chart[' + index + '][secondary_model_id]"]').val();
    var secondary_name = $('select[name="Chart[' + index + '][secondary_name]"]').val();
    var secondary_selection = $('input[name="Chart[' + index + '][secondary_selection]"]:checked').val();
       
    var date = $('select[name="Chart[' + index + '][date]"]').val();
    var created_at_start = $('input[name="Chart[' + index + '][created_at_start]"]').val();
    var created_at_end = $('input[name="Chart[' + index + '][created_at_end]"]').val();
    var type = $('input[name="Chart[' + index + '][type]"]:checked').val();
    var interval = $('select[name="Chart[' + index + '][interval]"]').val();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-get-chart',  
        data: {
            name: name, 
            primary_model: primary_model, 
            primary_model_id: primary_model_id, 
            primary_name: primary_name, 
            primary_selection: primary_selection, 
            secondary_model: secondary_model, 
            secondary_model_id: secondary_model_id, 
            secondary_name: secondary_name, 
            secondary_selection: secondary_selection, 
            date: date,
            created_at_start: created_at_start,
            created_at_end: created_at_end,
            type: type,
            interval: interval
        },
        dataType: 'json', // the return is a json string
        success: function(data) {
            // the php HighchartsWidget demants that the data in php has a array with four array in it, so the title in the array is in the char key
            data['title'] = data['char']['title'];
            // the data of the series comes as a string it must be a int or float or it will not display the data
            data['series'] = highchartsSeriesDataToInt(data['series']);

            //$('highcharts-' + index).highcharts(data);
            var highcharts = Highcharts.chart('highcharts-' + index, data);
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

function chartChangeWeight(index){
    var weight = $('select[name="Chart[' + index + '][weight]"]').val(); 
    
    var listItem = $('.chart-index ul li[index="' + index + '"]');
    var weight_current = $( '.chart-index ul li' ).index( listItem );
    
    // if current weight is smaller than the new weight use insertAfter
    // if current weight is bigger than the new weight use insertBefore
    if(weight_current <= weight){
        $('.chart-index ul li[index="' + index + '"]').insertAfter('.chart-index ul li:eq(' + weight + ')').hide().show('slow');
    }
    if(weight_current > weight){
        $('.chart-index ul li[index="' + index + '"]').insertBefore('.chart-index ul li:eq(' + weight + ')').hide().show('slow');
    }
    
    chartSetWeights();    
}

// add remove chart
function chartShowHideButtonAddRemoveChart(){
    var count_charts = $('.chart-index ul li').length;
    var count_charts_hidden = $('.chart-index ul li:hidden').length;
    var count_charts_visible = $('.chart-index ul li:visible').length;
    
    if(1 == count_charts_visible){
        $('.chart_add').show();
        $('.chart_add').css('display', 'inline-block'); // buttons next to each other
        $('.chart_remove').hide();
    }
    
    if(2 <= count_charts_visible){
        $('.chart_add').show();
        $('.chart_add').css('display', 'inline-block'); // buttons next to each other
        $('.chart_remove').show();
        $('.chart_remove').css('display', 'inline-block'); // buttons next to each other
    }
    
    if(count_charts <= count_charts_visible){
        $('.chart_add').hide();
        $('.chart_remove').show();
        $('.chart_remove').css('display', 'inline-block'); // buttons next to each other
    }
}

function chartAdd(){
    $('.chart-index ul li:hidden:first').show(function(event) {

        // sortable
        $(this).removeClass('ui-state-disabled'); // enable sortable

        // accordion
        $(this).find('.accordion').removeClass('disabled');
        $(this).find('.accordion').addClass('enabled');
        $(this).find('.accordion.enabled').accordion({
            collapsible: true,
            active: 0,
            animate: 200
        });

        chartShowHideButtonAddRemoveChart();
    });
}

function chartRemove(){
    $('.chart-index ul li:visible:last').hide(function(event) {

        // sortable
        $(this).addClass('ui-state-disabled'); // disable sortable

        // accordion
        $(this).find('.accordion').addClass('disabled');
        $(this).find('.accordion').removeClass('enabled');
        $(this).find('.accordion.disabled').accordion('destroy');

        chartShowHideButtonAddRemoveChart();
    });
}

function chartSetWeights(){
    var weights = [];
    $('.chart-index ul li').each(function(weight){
        var index = $(this).attr('index');        
        var id = $('input[name="Chart[' + index + '][id]"]').val();
        
        $('select[name="Chart[' + index + '][weight]"]').val(weight);
        
        weights[weight] = id; 
    });
    
    $.ajax({
        type: 'POST',
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-weights',  
        data: {
            Chart: {
                weights: weights
            }
        },
        dataType: 'json', // the return is a json string
        success: function(data) {                
            if(data.errors) {                    
                alert(data.errors)
            }else {
                console.log(data);
            }
        }
    });
}

/*
function createChart(index){
    var primary_model = $('select[name="Chart[' + index + '][primary_model]"]').val();
    var primary_model_id = $('select[name="Chart[' + index + '][primary_model_id]"]').val();
    var primary_name = $('select[name="Chart[' + index + '][primary_name]"]').val();
    var primary_selection = $('input[name="Chart[' + index + '][primary_selection]"]:checked').val();
    
    var secondary_model = $('select[name="Chart[' + index + '][secondary_model]"]').val();
    var secondary_model_id = $('select[name="Chart[' + index + '][secondary_model_id]"]').val();
    var secondary_name = $('select[name="Chart[' + index + '][secondary_name]"]').val();
    var secondary_selection = $('input[name="Chart[' + index + '][secondary_selection]"]:checked').val();
       
    var date = $('select[name="Chart[' + index + '][date]"]').val();
    var created_at_start = $('input[name="Chart[' + index + '][created_at_start]"]').val();
    var created_at_end = $('input[name="Chart[' + index + '][created_at_end]"]').val();
    var type = $('input[name="Chart[' + index + '][type]"]:checked').val();
    var interval = $('select[name="Chart[' + index + '][interval]"]').val();
    
    var weight = $('select[name="Chart[' + index + '][weight]"]').val();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=chart/ajax-create-chart',  
        data: {
            primary_model: primary_model, 
            primary_model_id: primary_model_id, 
            primary_name: primary_name, 
            primary_selection: primary_selection, 
            secondary_model: secondary_model, 
            secondary_model_id: secondary_model_id, 
            secondary_name: secondary_name, 
            secondary_selection: secondary_selection, 
            date: date,
            created_at_start: created_at_start,
            created_at_end: created_at_end,
            type: type,
            interval: interval,
            weight: weight
        },
        dataType: 'json', // the return is a json string
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                // the php HighchartsWidget demants that the data in php has a array with four array in it, so the title in the array is in the char key
                data['title'] = data['char']['title'];
                // the data of the series comes as a string it must be a int or float or it will not display the data
                data['series'] = highchartsSeriesDataToInt(data['series']);
                console.log(data);

                //$('highcharts-' + index).highcharts(data);
                var highcharts = Highcharts.chart('highcharts-' + index, data);
            }
        }
    });
}

function updateChart(index){
    
}

function deleteChart(index){
    
}*/