//var StartiAmReallyAtHome = i_am_really_at_home;
var StartCurrent = 0;
var StartTarget = 0;
var StartDefault = 0;

var thermostatTargetMinusTimeout;
var thermostatTargetPlusTimeout;
var thermostatTargetCountDownTimeout = [];

var thermostatDefaultMinusTimeout;
var thermostatDefaultPlusTimeout;
var thermostatDefaultCountDownTimeout = [];


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
            //thermostatSetWeights();
        }    
    });
    
    $('.name').bind('keypress', function(event) {
        thermostatSetName($(this).attr('index'));
    });
    
    $('.on_model').bind('change', function(event) {
        thermostatSetModelIds($(this).attr('index'), 'on');
    });
    
    $('.off_model').bind('change', function(event) {
        thermostatSetModelIds($(this).attr('index'), 'off');
    });
    
    $('.temperature_model').bind('change', function(event) {
        thermostatSetModelIds($(this).attr('index'), 'temperature');
    });
    
    $('.weight').bind('change', function(event) {
        thermostatChangeWeight($(this).attr('index'));
    });
    
    // https://yii2-cookbook.readthedocs.io/forms-activeform-js/
    $('.thermostat-activeform').on('beforeSubmit', function (event) {
        var activeform = $(this);
        var index = $(this).attr('index');
        
        var id = $('input[name="Thermostat[' + index + '][id]"]').val();
        var name = $('input[name="Thermostat[' + index + '][name]"]').val();
        
        var on_model = $('select[name="Thermostat[' + index + '][on_model]"]').val();
        var on_model_id = $('select[name="Thermostat[' + index + '][on_model_id]"]').val();
        var off_model = $('select[name="Thermostat[' + index + '][off_model]"]').val();
        var off_model_id = $('select[name="Thermostat[' + index + '][off_model_id]"]').val();
        var temperature_model = $('select[name="Thermostat[' + index + '][temperature_model]"]').val();
        var temperature_model_id = $('select[name="Thermostat[' + index + '][temperature_model_id]"]').val();
        
        var temperature_default = $('input[name="Thermostat[' + index + '][temperature_default]"]').val();
        var temperature_default_max = $('input[name="Thermostat[' + index + '][temperature_default_max]"]').val();
        var temperature_target = $('input[name="Thermostat[' + index + '][temperature_target]"]').val();
        var temperature_target_max = $('input[name="Thermostat[' + index + '][temperature_target_max]"]').val();
        var temperature_current = $('input[name="Thermostat[' + index + '][temperature_current]"]').val();

        var weight = $('select[name="Thermostat[' + index + '][weight]"]').val();
        
        $.ajax({
            type: 'POST',
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines
            url: '?r=thermostat/ajax-create-update&id=' + id,  
            data: {
                Thermostat: {
                    id: id,
                    name: name,
                    on_model: on_model, 
                    on_model_id: on_model_id, 
                    off_model: off_model, 
                    off_model_id: off_model_id, 
                    temperature_model: temperature_model, 
                    temperature_model_id: temperature_model_id, 
                    temperature_default: temperature_default, 
                    temperature_default_max: temperature_default_max, 
                    temperature_target: temperature_target,
                    temperature_target_max: temperature_target_max,
                    weight: weight
                }
            },
            dataType: 'json', // the return is a json string
            success: function(data) {                
                if(data.errors) {                    
                    $.each(data.errors, function( field, error ) {
                        $(activeform).yiiActiveForm('updateAttribute', 'thermostat-' + index + '-' + field, [error]); //https://yii2-cookbook.readthedocs.io/forms-activeform-js/
                    });
                    
                    return false;
                }else {
                    $('input[name="Thermostat[' + index + '][id]"]').val(data.id); // set id
                    $('button.thermostat_create[index="' + index + '"]').hide();
                    $('button.thermostat_update[index="' + index + '"]').show();
                    $('button.thermostat_update[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    $('button.thermostat_delete[index="' + index + '"]').show();
                    $('button.thermostat_delete[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    
                    return true;
                }
            }
        });
        
        return false;
    });
    
    // delete
    $('.thermostat_delete').bind('click', function(event) {
        var index = $(this).attr('index');
        var activeform = $('thermostat-activeform[index="' + index + '"]');
        
        var id = $('input[name="Thermostat[' + index + '][id]"]').val();
        
        $.ajax({
            type: 'POST',
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines
            url: '?r=thermostat/ajax-delete&id=' + id,  
            data: {
                Thermostat: {
                    id: id
                }
            },
            dataType: 'json', // the return is a json string
            success: function(data) {                
                if(data.errors) {                    
                    alert(data.errors)
                }else {
                    $('input[name="Thermostat[' + index + '][id]"]').val(''); // set id
                    $('input[name="Thermostat[' + index + '][name]"]').val(''); // set id
                    
                    $('button.thermostat_create[index="' + index + '"]').hide();
                    $('button.thermostat_update[index="' + index + '"]').show();
                    $('button.thermostat_update[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    $('button.thermostat_delete[index="' + index + '"]').show();
                    $('button.thermostat_delete[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    
                    // reset form
                    $('select[name="Thermostat[' + index + '][on_model]"]').val('none');
                    
                    $('select[name="Thermostat[' + index + '][on_model_id]"]').empty();
                    $('select[name="Thermostat[' + index + '][on_model_id]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Thermostat[' + index + '][on_model_id]"]').val('none');
                    
                    $('select[name="Thermostat[' + index + '][off_model]"]').val('none');
                    
                    $('select[name="Thermostat[' + index + '][off_model_id]"]').empty();
                    $('select[name="Thermostat[' + index + '][off_model_id]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Thermostat[' + index + '][off_model_id]"]').val('none');
                    
                    $('select[name="Thermostat[' + index + '][temperature_model]"]').val('none');
                    
                    $('select[name="Thermostat[' + index + '][temperature_model_id]"]').empty();
                    $('select[name="Thermostat[' + index + '][temperature_model_id]"]').append($("<option></option>").attr("value", 'none').text(tNone));
                    $('select[name="Thermostat[' + index + '][temperature_model_id]"]').val('none');
                       
                    $('input[name="Thermostat[' + index + '][temperature_default]"]').val(0);
                    $('input[name="Thermostat[' + index + '][temperature_default_max]"]').val(0);
                    $('input[name="Thermostat[' + index + '][temperature_target]"]').val(0);
                    $('input[name="Thermostat[' + index + '][temperature_target_max]"]').val(0);
                    $('input[name="Thermostat[' + index + '][temperature_current]"]').val(0);

                    //var weight = $('select[name="Thermostat[' + index + '][weight]"]').val();
                    
                    $('button.thermostat_create[index="' + index + '"]').show();
                    $('button.thermostat_create[index="' + index + '"]').css('display', 'inline-block'); // buttons next to each other
                    $('button.thermostat_update[index="' + index + '"]').hide();
                    $('button.thermostat_delete[index="' + index + '"]').hide();
                }
            }
        });
    });
    
    // add remove
    thermostatShowHideButtonAddRemove();
        
    // add
    $('.thermostat_add').bind('click', function(event) {
        thermostatAdd();
    });
    
    // remove
    $('.thermostat_remove').bind('click', function(event) {
        thermostatRemove();
    });
    
    // thermostate
    $('.thermostat').each(function(_index, object) {
        var index = $(object).attr('index');
        
        //thermostatSetIamReallyAtHome(i_am_really_at_home);
        var current = parseFloat($('.temperature_current[index="' + index + '"]').val());
        thermostatSetCurrent(index, current);
        
        var target = parseFloat($('.temperature_target[index="' + index + '"]').val());
        thermostatSetTarget(index, target);
        
        var _default = parseFloat($('.temperature_default[index="' + index + '"]').val());
        thermostatSetDefault(index, _default);
                
        // set keys count down
        thermostatTargetCountDownTimeout[index];
        thermostatDefaultCountDownTimeout[index];
                
        // drag target pointer
        $('.target-pointer[index="' + index + '"]').bind('vmousedown', function(event) {
            thermostatTargetStartDrag(index, event);
        });
    
        // click target minus and plus (on thermostate to)
        $('.thermostat .target .minus[index="' + index + '"], .detail-view .target-minus[index="' + index + '"]').bind('click', function(event) {
            setTimeout(function(){ // the vmousedown is also fireing, this ensures that the click is the last event also the last to send data trough ajax
                thermostatTargetMinus(index, event);
                thermostatTargetSetSetting(index);
            }, 100);  
        });

        $('.thermostat .target .plus[index="' + index + '"], .detail-view .target-plus[index="' + index + '"]').bind('click', function(event) {
            setTimeout(function(){ // the vmousedown is also fireing, this ensures that the click is the last event also the last to send data trough ajax
                thermostatTargetPlus(index, event);
                thermostatTargetSetSetting(index);
            }, 100);
        });
        
        // hold down the minus and plus of the target
        //var thermostatTargetMinusTimeout;
        $('.thermostat .target .minus[index="' + index + '"], .detail-view .target-minus[index="' + index + '"]').bind('vmousedown', function(event) {
            thermostatTargetMinusStart(index);
            thermostatTargetMinusTimeout = setInterval(function(){
                thermostatTargetMinus(index);
            }, 100);

            return false;
        });

        //var thermostatTargetPlusTimeout;
        $('.thermostat .target .plus[index="' + index + '"], .detail-view .target-plus[index="' + index + '"]').bind('vmousedown', function(event) {
            thermostatTargetPlusStart(index);
            thermostatTargetPlusTimeout = setInterval(function(){
                thermostatTargetPlus(index);
            }, 100);

            return false;
        });
        
        // click default min and plus
        $('.detail-view .default-minus[index="' + index + '"]').bind('click', function(event) {
            setTimeout(function(){
                thermostatDefaultMinus(index, event);
                thermostatDefaultSetSetting(index);
            }, 100);
        });
        $('.detail-view .default-plus[index="' + index + '"]').bind('click', function(event) {
            setTimeout(function(){
                thermostatDefaultPlus(index, event);
                thermostatDefaultSetSetting(index);
            }, 100);
        });
        
        // hold down the minus and plus of the default
        //var thermostatDefaultMinusTimeout;
        $('.detail-view .default-minus[index="' + index + '"]').bind('vmousedown', function(event) {
            thermostatDefaultMinusStart(index);
            thermostatDefaultMinusTimeout = setInterval(function(){
                thermostatDefaultMinus(index);
            }, 100);

            return false;
        });

        //var thermostatDefaultPlusTimeout;
        $('.detail-view .default-plus[index="' + index + '"]').bind('vmousedown', function(event) {
            thermostatDefaultPlusStart(index);        
            thermostatDefaultPlusTimeout = setInterval(function(){
                thermostatDefaultPlus(index);
            }, 100);

            return false;
        });
        
        // set temperature first time      
        thermostatSetTemperature(index);
        
        // set temperature every five minutes
        setInterval(function(){
            thermostatSetTemperature(index);
        }, (1000 * 60 * 5));
    });
});

function thermostatSetName(index){
    var name = $('input[name="Thermostat[' + index + '][name]"]').val();
    $('.thermostat-header[index="' + index + '"] .text').html(name);
}

function thermostatSetModelIds(index, on_off_temperature){
    var model = '';
    var model_id_selector = {};
    if('on' == on_off_temperature){
        model = $('select[name="Thermostat[' + index + '][on_model]"]').val();
        model_id_selector = $('select[name="Thermostat[' + index + '][on_model_id]"]');
    }
    
    if('off' == on_off_temperature){
        model = $('select[name="Thermostat[' + index + '][off_model]"]').val();
        model_id_selector = $('select[name="Thermostat[' + index + '][off_model_id]"]');
    }
    
    if('temperature' == on_off_temperature){
        model = $('select[name="Thermostat[' + index + '][temperature_model]"]').val();
        model_id_selector = $('select[name="Thermostat[' + index + '][temperature_model_id]"]');
    }
    
    $(model_id_selector).empty(); // empty the select box
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=thermostat/ajax-get-model-ids',  
        data: {model: model},
        dataType: 'json', // the return is a json string
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
        }
    });
}

function thermostatChangeWeight(index){
    var weight = $('select[name="Thermostat[' + index + '][weight]"]').val(); 
    
    var listItem = $('.thermostat-index ul li[index="' + index + '"]');
    var weight_current = $( '.thermostat-index ul li' ).index( listItem );
    
    // if current weight is smaller than the new weight use insertAfter
    // if current weight is bigger than the new weight use insertBefore
    if(weight_current <= weight){
        $('.thermostat-index ul li[index="' + index + '"]').insertAfter('.thermostat-index ul li:eq(' + weight + ')').hide().show('slow');
    }
    if(weight_current > weight){
        $('.thermostat-index ul li[index="' + index + '"]').insertBefore('.thermostat-index ul li:eq(' + weight + ')').hide().show('slow');
    }
    
    thermostatSetWeights();    
}

// add remove thermostat
function thermostatShowHideButtonAddRemove(){
    var count_thermostats = $('.thermostat-index ul li').length;
    var count_thermostats_hidden = $('.thermostat-index ul li:hidden').length;
    var count_thermostats_visible = $('.thermostat-index ul li:visible').length;
    
    if(1 == count_thermostats_visible){
        $('.thermostat_add').show();
        $('.thermostat_add').css('display', 'inline-block'); // buttons next to each other
        $('.thermostat_remove').hide();
    }
    
    if(2 <= count_thermostats_visible){
        $('.thermostat_add').show();
        $('.thermostat_add').css('display', 'inline-block'); // buttons next to each other
        $('.thermostat_remove').show();
        $('.thermostat_remove').css('display', 'inline-block'); // buttons next to each other
    }
    
    if(count_thermostats <= count_thermostats_visible){
        $('.thermostat_add').hide();
        $('.thermostat_remove').show();
        $('.thermostat_remove').css('display', 'inline-block'); // buttons next to each other
    }
}

function thermostatAdd(){
    $('.thermostat-index ul li:hidden:first').show(function(event) {

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

        thermostatShowHideButtonAddRemove();
    });
}

function thermostatRemove(){
    $('.thermostat-index ul li:visible:last').hide(function(event) {

        // sortable
        $(this).addClass('ui-state-disabled'); // disable sortable

        // accordion
        $(this).find('.accordion').addClass('disabled');
        $(this).find('.accordion').removeClass('enabled');
        $(this).find('.accordion.disabled').accordion('destroy');

        thermostatShowHideButtonAddRemove();
    });
}

function thermostatSetWeights(){
    var weights = [];
    $('.thermostat-index ul li').each(function(weight){
        var index = $(this).attr('index');        
        var id = $('input[name="Thermostat[' + index + '][id]"]').val();
        
        $('select[name="Thermostat[' + index + '][weight]"]').val(weight);
        
        weights[weight] = id; 
    });
    
    $.ajax({
        type: 'POST',
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=thermostat/ajax-weights',  
        data: {
            Thermostat: {
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

// thermostat
function thermostatSetTemperature(index){    
    var temperature_model = $('select[name="Thermostat[' + index + '][temperature_model]"]').val();
    var temperature_model_id = $('select[name="Thermostat[' + index + '][temperature_model_id]"]').val();
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=thermostat/ajax-execute-model',  
        data: {model: temperature_model, model_id: temperature_model_id},
        dataType: 'json', // the return is a json string
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(field, value) {
                    thermostatSetCurrent(index, value);
                });
            }
        }
    });    
}

function thermostatSetCurrent(index, temp){
    // detail view
    $('.detail-view .current[index="' + index + '"]').html(temp);
    
    // thermostat
    $('.thermostat .current .degree[index="' + index + '"]').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('.thermostat .current-pointer-ovelay[index="' + index + '"]', temp);
    
    // update globals
    //current = temp;
    // no update input
    $('.temperature_current[index="' + index + '"]').val(temp);
}

function thermostatSetTarget(index, temp){
    if(-10 >= temp){
        temp = -10;
    }
    
    if(55 <= temp){
       temp = 55;
    }
    
    // detail view
    $('.detail-view .target[index="' + index + '"]').html(temp);
    
    // thermostat
    $('.thermostat .target .degree[index="' + index + '"]').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('.thermostat .target-pointer[index="' + index + '"]', temp);
    
    // update globals
    //target = temp;
    // no update input
    $('.temperature_target[index="' + index + '"]').val(temp);
    $('.temperature_target_max[index="' + index + '"]').val((temp + 1));
}

function thermostatSetDefault(index, temp){
    // detail view
    $('.detail-view .default[index="' + index + '"]').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('.thermostat .default-pointer[index="' + index + '"]', temp);
    
    // update globals
    //_default = temp;
    // no update input
    $('.temperature_default[index="' + index + '"]').val(temp);
    $('.temperature_default_max[index="' + index + '"]').val((temp + 1));
}

// thermostate rotate current pointer overlay
function thermostatPointerTempToDeg(temp){
    return (temp * 2) + 20;
}

function thermostatPointerDegToTemp(deg){
    return (deg - 20) / 2;
}

function thermostatPointerRotateTemp(element, temp){
    var deg = thermostatPointerTempToDeg(temp);
    thermostatPointerRotateDeg(element, deg);
}

function thermostatPointerRotateDeg(element, deg){
    if(0 >= deg){
        deg = 0;
    }
    
    if(130 <= deg){
       deg = 130; 
    }
    
    deg = deg.toString();
    
    $(element).css({
        'transform': 'rotate('+ deg + 'deg)', /* For modern browsers(CSS3)  */
        '-ms-transform': 'rotate('+ deg + 'deg)', /* IE 9 */
        '-moz-transform': 'rotate('+ deg + 'deg)', /* Firefox */
        '-webkit-transform': 'rotate('+ deg + 'deg)', /* Safari and Chrome */
        '-o-transform': 'rotate('+ deg + 'deg)' /* Opera */
    });
    //$('#thermostate .temperature .degree').html(text);
}

// drag target pointer
function thermostatTargetStartDrag(index, event){
    var startDragPos = { x: -1, y: -1 };
    startDragPos.x = event.pageX;
    startDragPos.y = event.pageY;
    
    //startTarget = target;
    startTarget = parseFloat($('.temperature_target[index="' + index + '"]').val());
    
    thermostatTargetDrag(index, startDragPos);
    
    $(document).bind('vmouseup', function(event) {    
        thermostatTargetStopDrag(index);
    });
}

function thermostatTargetDrag(index, startDragPos){
    var currentDragPos = { x: -1, y: -1 };
    
    $(document).bind('vmousemove', function(event) {
        if (event.which == 1 || event.which == 0) { // if left mouse button is still prest, and the right (right or 0 is also for the phone touch)
            currentDragPos.x = event.pageX;
            currentDragPos.y = event.pageY;
                        
            var deg = parseFloat(thermostatPointerTempToDeg(parseFloat(startTarget)));                    
            deg = deg + (currentDragPos.x - startDragPos.x);            
            
            var temp = parseFloat(thermostatPointerDegToTemp(deg));            
            thermostatSetTarget(index, temp);
        }
    });
}

function thermostatTargetStopDrag(index){
    $(document).unbind('vmousemove');
    $(document).unbind('vmouseup');
    
    thermostatTargetSetSetting(index);
}

// target minus and plus
function thermostatTargetMinus(index, event){
    var target = parseFloat($('.temperature_target[index="' + index + '"]').val());
    
    var temp = parseFloat(target) - 0.5;
    thermostatSetTarget(index, temp);
}

function thermostatTargetPlus(index, event){
    var target = parseFloat($('.temperature_target[index="' + index + '"]').val());
    
    var temp = parseFloat(target) + 0.5;
    thermostatSetTarget(index, temp);
}

function thermostatTargetSetSetting(index){
    // clear / stop last timer
    clearTimeout(thermostatTargetCountDownTimeout[index]);
    
    // wait 5 seconds
    thermostatTargetCountDownTimeout[index] = setTimeout(function(){        
        var model = '';
        var model_id = '';
        
        var on_model = $('select[name="Thermostat[' + index + '][on_model]"]').val();
        var on_model_id = $('select[name="Thermostat[' + index + '][on_model_id]"]').val();
        var off_model = $('select[name="Thermostat[' + index + '][off_model]"]').val();
        var off_model_id = $('select[name="Thermostat[' + index + '][off_model_id]"]').val();
        
        var temperature_target = $('input[name="Thermostat[' + index + '][temperature_target]"]').val();
        var temperature_target_max = $('input[name="Thermostat[' + index + '][temperature_target_max]"]').val();
        var temperature_current = $('input[name="Thermostat[' + index + '][temperature_current]"]').val();

        // if temperature_current is lower than temperature_target switch on
        if(temperature_current < temperature_target){
            model = on_model; 
            model_id = on_model_id; 
        }
        
        // if temperature_current is higher than temperature_target_max switch off
        if(temperature_current < temperature_target_max){
            model = off_model; 
            model_id = off_model_id; 
        }
        
        $.ajax({
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines
            url: '?r=thermostat/ajax-execute-model',  
            data: {model: model, model_id: model_id},
            dataType: 'json', // the return is a json string
            success: function(data) {
                if(data.error) {
                    alert(data.error);

                }else {
                    return true;
                }
            }
        });
    }, (1000 * 5));
}

// target minus hold
function thermostatTargetMinusStart(index){
  //startTarget = target;
  startTarget = parseFloat($('.temperature_target[index="' + index + '"]').val());
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatTargetMinusStop(index);
  });
}

function thermostatTargetMinusStop(index){
  clearInterval(thermostatTargetMinusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatTargetSetSetting(index);
  
  //setTimeout(function(){ mouseDown = false; }, 100); // is needed, after mousedown fire the click event en we donnot want that
}

// target plus hold
function thermostatTargetPlusStart(index){
  //startTarget = target;
  startTarget = parseFloat($('.temperature_target[index="' + index + '"]').val());
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatTargetPlusStop(index);
  });
}

function thermostatTargetPlusStop(index){
  clearInterval(thermostatTargetPlusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatTargetSetSetting(index);
  
  //setTimeout(function(){ mouseDown = false; }, 100); // is needed, after mousedown fire the click event en we donnot want that
}

// default minus and plus
function thermostatDefaultMinus(index, event){
    var _default = parseFloat($('.temperature_default[index="' + index + '"]').val());
    
    var temp = parseFloat(_default) - 0.5;
    thermostatSetDefault(index, temp);
}

function thermostatDefaultPlus(index, event){
    var _default = parseFloat($('.temperature_default[index="' + index + '"]').val());
        
    var temp = parseFloat(_default) + 0.5;
    thermostatSetDefault(index, temp);
}

function thermostatDefaultSetSetting(index){
    // clear / stop last timer
    clearTimeout(thermostatDefaultCountDownTimeout[index]);
    
    // wait 5 seconds
    thermostatDefaultCountDownTimeout[index] = setTimeout(function(){        
        var model = '';
        var model_id = '';
        
        var on_model = $('select[name="Thermostat[' + index + '][on_model]"]').val();
        var on_model_id = $('select[name="Thermostat[' + index + '][on_model_id]"]').val();
        var off_model = $('select[name="Thermostat[' + index + '][off_model]"]').val();
        var off_model_id = $('select[name="Thermostat[' + index + '][off_model_id]"]').val();
        
        var temperature_default = $('input[name="Thermostat[' + index + '][temperature_default]"]').val();
        var temperature_default_max = $('input[name="Thermostat[' + index + '][temperature_default_max]"]').val();
        var temperature_current = $('input[name="Thermostat[' + index + '][temperature_current]"]').val();

        // if temperature_current is lower than temperature_default switch on
        if(temperature_current < temperature_default){
            model = on_model; 
            model_id = on_model_id; 
        }
        
        // if temperature_current is higher than temperature_default_max switch off
        if(temperature_current < temperature_default_max){
            model = off_model; 
            model_id = off_model_id; 
        }
        
        $.ajax({
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines
            url: '?r=thermostat/ajax-execute-model',  
            data: {model: model, model_id: model_id},
            dataType: 'json', // the return is a json string
            success: function(data) {
                if(data.error) {
                    alert(data.error);

                }else {
                    return true;
                }
            }
        });
    }, (1000 * 5));
}

// default minus hold
function thermostatDefaultMinusStart(index){
  //startDefault = _default;
  startDefault = parseFloat($('.temperature_default[index="' + index + '"]').val());
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatDefaultMinusStop(index);
  });
}

function thermostatDefaultMinusStop(index){
  clearInterval(thermostatDefaultMinusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatDefaultSetSetting(index);
  
  //setTimeout(function(){ mouseDown = false; }, 100);
}

// default plus hold
function thermostatDefaultPlusStart(index){
  //startDefault = _default;
  startDefault = parseFloat($('.temperature_default[index="' + index + '"]').val());
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatDefaultPlusStop(index);
  });
}

function thermostatDefaultPlusStop(index){
  clearInterval(thermostatDefaultPlusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatDefaultSetSetting(index);
  
  //setTimeout(function(){ mouseDown = false; }, 100);
}