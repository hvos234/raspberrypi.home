var StartiAmReallyAtHome = i_am_really_at_home;
var StartCurrent = current;
var StartTarget = target;
var StartDefault = _default;

var thermostatTargetMinusTimeout;
var thermostatTargetPlusTimeout;

var thermostatDefaultMinusTimeout;
var thermostatDefaultPlusTimeout;

$(document).ready(function(){
    // first time
    thermostatSetIamReallyAtHome(i_am_really_at_home);
    thermostatSetCurrent(current);
    thermostatSetTarget(target);
    thermostatSetDefault(_default);
            
    // get date and time every minut
    var thermostatGetDateTimeInterval;
    thermostatGetDateTimeInterval = setInterval(function(){
        thermostatSetDateTime();
    }, (1000 * 60));
        
    // get data every 5 minutes
    var thermostatGetDataInterval;
    thermostatGetDataInterval = setInterval(function(){
        thermostatGetData();
    }, (1000 * 60 * 5));
    
    // drag target pointer
    $('#thermostate .target-pointer').bind('vmousedown', function(event) {
        thermostatTargetStartDrag(event);
    });
    
    // click target minus and plus (on thermostate to)
    $('#thermostate .target .minus, .detail-view .target-minus').bind('click', function(event) {
        setTimeout(function(){ // the vmousedown is also fireing, this ensures that the click is the last event also the last to send data trough ajax
            thermostatTargetMinus(event);
            thermostatTargetSetSetting();
        }, 100);  
    });
    
    $('#thermostate .target .plus, .detail-view .target-plus').bind('click', function(event) {
        setTimeout(function(){ // the vmousedown is also fireing, this ensures that the click is the last event also the last to send data trough ajax
            thermostatTargetPlus(event);
            thermostatTargetSetSetting();
        }, 100);
    });
    
    // hold down the minus and plus of the target
    //var thermostatTargetMinusTimeout;
    $('#thermostate .target .minus, .detail-view .target-minus').bind('vmousedown', function(event) {
        thermostatTargetMinusStart();
        thermostatTargetMinusTimeout = setInterval(function(){
            thermostatTargetMinus();
        }, 100);
        
        return false;
    });
    
    //var thermostatTargetPlusTimeout;
    $('#thermostate .target .plus, .detail-view .target-plus').bind('vmousedown', function(event) {
        thermostatTargetPlusStart();
        thermostatTargetPlusTimeout = setInterval(function(){
            thermostatTargetPlus();
        }, 100);
        
        return false;
    });
    
    // click default min and plus
    $('.detail-view .default-minus').bind('click', function(event) {
        setTimeout(function(){
            thermostatDefaultMinus(event);
            thermostatDefaultSetSetting();
        }, 100);
    });
    $('.detail-view .default-plus').bind('click', function(event) {
        setTimeout(function(){
            thermostatDefaultPlus(event);
            thermostatDefaultSetSetting();
        }, 100);
    });
    
    // hold down the minus and plus of the default
    //var thermostatDefaultMinusTimeout;
    $('.detail-view .default-minus').bind('vmousedown', function(event) {
        thermostatDefaultMinusStart();
        thermostatDefaultMinusTimeout = setInterval(function(){
            thermostatDefaultMinus();
        }, 100);
        
        return false;
    });
    
    //var thermostatDefaultPlusTimeout;
    $('.detail-view .default-plus').bind('vmousedown', function(event) {
        thermostatDefaultPlusStart();        
        thermostatDefaultPlusTimeout = setInterval(function(){
            thermostatDefaultPlus();
        }, 100);
        
        return false;
    });
});

// date and time
function thermostatSetDateTime(){
    var dateTime = thermostatGetDateTime();
    $('.detail-view .date_time').html(dateTime);
}

function thermostatGetDateTime() {
    var now     = new Date(); 
    var year    = now.getFullYear();
    var month   = now.getMonth()+1; 
    var day     = now.getDate();
    var hour    = now.getHours();
    var minute  = now.getMinutes();
    var second  = now.getSeconds(); 
    if(month.toString().length == 1) {
        var month = '0'+month;
    }
    if(day.toString().length == 1) {
        var day = '0'+day;
    }   
    if(hour.toString().length == 1) {
        var hour = '0'+hour;
    }
    if(minute.toString().length == 1) {
        var minute = '0'+minute;
    }
    if(second.toString().length == 1) {
        var second = '0'+second;
    }   
    //var dateTime = year+'-'+month+'-'+day+' '+hour+':'+minute+':'+second;   
    var dateTime = year+'-'+month+'-'+day+' '+hour+':'+minute;   
    return dateTime;
}


// data
function thermostatGetData(){
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=thermostat/ajax-get-data',  
        //data: {chart_type: chart_type},
        dataType: 'json', // the return is a json string
        success: function(data) {
            if(data.error) {
                alert(data.error);
            }else {
                i_am_really_at_home = data['i_am_really_at_home'];
                current = data['current'];
                target = data['target'];
                _default = data['default'];

                thermostatSetIamReallyAtHome(data['i_am_really_at_home']);
                thermostatSetCurrent(data['current']);
                thermostatSetTarget(data['target']);
                thermostatSetDefault(data['default']);
            }
        }
    });
}

function thermostatSetIamReallyAtHome(_i_am_really_at_home){
    // detail view
    if(0 == _i_am_really_at_home){
        $('.detail-view .i_am_really_at_home').attr('class', 'yes');
    }else {
        $('.detail-view .i_am_really_at_home').attr('class', 'no');
    }
    
    // update globals
    i_am_really_at_home = _i_am_really_at_home;
}

function thermostatSetCurrent(temp){
    // detail view
    $('.detail-view .current').html(temp);
    
    // thermostat
    $('#thermostate .current .degree').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('#thermostate .current-pointer-ovelay', temp);
    
    // update globals
    current = temp;
}

function thermostatSetTarget(temp){
    if(-10 >= temp){
        temp = -10;
    }
    
    if(55 <= temp){
       temp = 55;
    }
    
    // detail view
    $('.detail-view .target').html(temp);
    
    // thermostat
    $('#thermostate .target .degree').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('#thermostate .target-pointer', temp);
    
    // update globals
    target = temp;
}

function thermostatSetDefault(temp){
    // detail view
    $('.detail-view .default').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('#thermostate .default-pointer', temp);
    
    // update globals
    _default = temp;
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
function thermostatTargetStartDrag(event){
    var startDragPos = { x: -1, y: -1 };
    startDragPos.x = event.pageX;
    startDragPos.y = event.pageY;
    
    startTarget = target;
    
    thermostatTargetDrag(startDragPos);
    
    $(document).bind('vmouseup', function(event) {    
        thermostatTargetStopDrag();
    });
}

function thermostatTargetDrag(startDragPos){
    var currentDragPos = { x: -1, y: -1 };
    
    $(document).bind('vmousemove', function(event) {
        if (event.which == 1 || event.which == 0) { // if left mouse button is still prest, and the right (right or 0 is also for the phone touch)
            currentDragPos.x = event.pageX;
            currentDragPos.y = event.pageY;
                        
            var deg = parseFloat(thermostatPointerTempToDeg(parseFloat(startTarget)));                    
            deg = deg + (currentDragPos.x - startDragPos.x);            
            
            var temp = parseFloat(thermostatPointerDegToTemp(deg));            
            thermostatSetTarget(temp);
        }
    });
    
}

function thermostatTargetStopDrag(){
    $(document).unbind('vmousemove');
    $(document).unbind('vmouseup');
    
    thermostatTargetSetSetting();
}


// target minus and plus
function thermostatTargetMinus(event){
    var temp = parseFloat(target) - 0.5;
    thermostatSetTarget(temp);
}

function thermostatTargetPlus(event){
    var temp = parseFloat(target) + 0.5;
    thermostatSetTarget(temp);
}

function thermostatTargetSetSetting(){
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=thermostat/ajax-set-setting-target',
        type: 'post',
        data: {'target': target},
        dataType: 'json', // the return is a json string
        success: function(data) {
            /*if(data.error) {
                alert(data.error);
            }else {
                console.log('data: ' + data);
            }*/
        }
    });
}

// target minus hold
function thermostatTargetMinusStart(){
  startTarget = target;
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatTargetMinusStop();
  });
}

function thermostatTargetMinusStop(){
  clearInterval(thermostatTargetMinusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatTargetSetSetting();
  
  //setTimeout(function(){ mouseDown = false; }, 100); // is needed, after mousedown fire the click event en we donnot want that
}

// target plus hold
function thermostatTargetPlusStart(){
  startTarget = target;
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatTargetPlusStop();
  });
}

function thermostatTargetPlusStop(){
  clearInterval(thermostatTargetPlusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatTargetSetSetting();
  
  //setTimeout(function(){ mouseDown = false; }, 100); // is needed, after mousedown fire the click event en we donnot want that
}

// default minus and plus
function thermostatDefaultMinus(event){
    var temp = parseFloat(_default) - 0.5;
    thermostatSetDefault(temp);
}

function thermostatDefaultPlus(event){
    var temp = parseFloat(_default) + 0.5;
    thermostatSetDefault(temp);
}

function thermostatDefaultSetSetting(){
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=thermostat/ajax-set-setting-default',
        type: 'post',
        data: {'default': _default},
        dataType: 'json', // the return is a json string
        success: function(data) {            
            /*if(data.error) {
                alert(data.error);
            }else {
                console.log(data);
            }*/
        }
    });
}

// default minus hold
function thermostatDefaultMinusStart(){
  startDefault = _default;
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatDefaultMinusStop();
  });
}

function thermostatDefaultMinusStop(){
  clearInterval(thermostatDefaultMinusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatDefaultSetSetting();
  
  //setTimeout(function(){ mouseDown = false; }, 100);
}

// default plus hold
function thermostatDefaultPlusStart(){
  startDefault = _default;
  
  $(document).bind('vmouseup', function(event) {
      
      thermostatDefaultPlusStop();
  });
}

function thermostatDefaultPlusStop(){
  clearInterval(thermostatDefaultPlusTimeout);
  
  $(document).unbind('vmouseup');
  
  thermostatDefaultSetSetting();
  
  //setTimeout(function(){ mouseDown = false; }, 100);
}