$(document).ready(function(){
    // first time
    thermostatSetIamReallyAtHome(i_am_really_at_home);
    thermostatSetCurrent(current);
    thermostatSetTarget(target);
    thermostatSetMin(min);
    thermostatSetMax(max);
            
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
    $('#thermostate .target-pointer').bind('mousedown vmousedown', function(event) {
        thermostatTargetStartDrag(event);
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
            //console.log(data);
            
            i_am_really_at_home = data['i_am_really_at_home'];
            current = data['current'];
            target = data['target'];
            min = data['min'];
            max = data['max'];
            
            thermostatSetIamReallyAtHome(data['i_am_really_at_home']);
            thermostatSetCurrent(data['current']);
            thermostatSetTarget(data['target']);
            thermostatSetMin(data['min']);
            thermostatSetMax(data['max']);
        }
    });
}

function thermostatSetIamReallyAtHome(i_am_really_at_home){
    // detail view
    if(0 == i_am_really_at_home){
        $('.detail-view .i_am_really_at_home').attr('class', 'yes');
    }else {
        $('.detail-view .i_am_really_at_home').attr('class', 'no');
    }
}

function thermostatSetCurrent(temp){
    // detail view
    $('.detail-view .current').html(temp);
    
    // thermostat
    $('#thermostate .current .degree').html(temp);
    
    // thermostat rotate
    thermostatPointerOverlayRotateDeg('#thermostate .current-pointer-ovelay', temp);
}

function thermostatSetTarget(temp){
    // detail view
    $('.detail-view .target').html(temp);
    
    // thermostat
    $('#thermostate .target .degree').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('#thermostate .target-pointer', temp); 
}

function thermostatSetMin(temp){
    // detail view
    $('.detail-view .min').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('#thermostate .min-pointer', temp);
}

function thermostatSetMax(temp){
    // detail view
    $('.detail-view .max').html(temp);
    
    // thermostat rotate
    thermostatPointerRotateTemp('#thermostate .max-pointer', temp);
}

// thermostate rotate target, min, max pointer
function thermostatPointerTempToDeg(temp){
    return (temp * 2) - 65;
}

function thermostatPointerDegToTemp(deg){
    return (65 + (deg / 2));
}

/*
target: 20.00
deg: -25
deg2: -92
temp: 19
rot deg: -27

target: 20:00
deg: -25
deg2: -24
temp: 53

rot deg: 41
 */

function thermostatPointerRotateTemp(element, temp){
    var deg = thermostatPointerTempToDeg(temp);
    thermostatPointerRotateDeg(element, deg);
}

function thermostatPointerRotateDeg(element, deg){
    console.log('rot deg: ' + deg);
    if(-65 >= deg){
        deg = -65;
    }
    
    if(65 <= deg){
       deg = 65; 
    }
    
    //text = deg / 2;
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

// thermostate rotate current pointer overlay
function thermostatPointerOverlayTempToDeg(temp){
    return temp * 2;
}

function thermostatPointerOverlayRotateDeg(element, temp){
    var deg = thermostatPointerOverlayTempToDeg(temp);
    if(0 >= deg){
        deg = 0;
    }
    
    if(130 <= deg){
       deg = 130; 
    }
    
    //text = deg / 2;
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
    //console.log(startDragPos);
    thermostatTargetDrag(startDragPos);
    
    $(document).bind('mouseup vmouseup', function(event) {
        thermostatTargetStopDrag();
    });
}

function thermostatTargetDrag(startDragPos){
    var currentDragPos = { x: -1, y: -1 };
    $(document).bind('mousemove vmousemove', function(event) {
        if (event.which == 1 || event.which == 0) { // if left mouse button is still prest, and the right (right or 0 is also for the phone touch)
            currentDragPos.x = event.pageX;
            currentDragPos.y = event.pageY;
            
            console.log('target: ' + target);
            
            var deg = thermostatPointerTempToDeg(parseFloat(target));
            console.log('deg: ' + deg);
            
            deg = (deg*4) + (currentDragPos.x - startDragPos.x);
            console.log('deg2: ' + deg);
            
            var temp = thermostatPointerDegToTemp(deg);
            console.log('temp: ' + temp);
            
            thermostatSetTarget(temp);
        }
    });
    
}

/*
target: 20.00
deg: -25
deg2: -92
temp: 19
rot deg: -27

target: 20:00
deg: -25
deg2: -24
temp: 53

rot deg: 41
 */

function thermostatTargetStopDrag(){
    $(document).unbind('mousemove vmousemove');
    $(document).unbind('mouseup vmouseup');
}


// target plus and minus
/*function thermostatTargetMinus(){
    var currentDeg = parseFloat($('#thermostate .temperature .degree').html());
    thermostatRotate((currentDeg * 2) - 1);
}

function thermostatTargetPlus(){
    var currentDeg = parseFloat($('#thermostate .temperature .degree').html());
    thermostatRotate((currentDeg * 2) + 1);
}*/

//$(document).ready(function(){    
    /*// thermostate
    thermostatRotate(1);
    
    var thermostatTemperaturStartMousePos = { x: -1, y: -1 };
    $('#thermostate .overlay').bind('mousedown vmousedown', function(event) {
        thermostatTemperaturStartMousePos.x = event.pageX;
        thermostatTemperaturStartMousePos.y = event.pageY;
        
        var thermostatTemperaturCurrentMousePos = { x: -1, y: -1 };
        var thermostatTemperaturCurrentDeg = parseFloat($('#thermostate .temperature .degree').html());
        $(document).bind('mousemove vmousemove', function(event) {

            if (event.which == 1 || event.which == 0) { // if left mouse button is still prest, and the right (right or 0 is also for the phone touch)
                thermostatTemperaturCurrentMousePos.x = event.pageX;
                thermostatTemperaturCurrentMousePos.y = event.pageY;

                thermostatRotate((thermostatTemperaturCurrentDeg *2) + (thermostatTemperaturCurrentMousePos.x - thermostatTemperaturStartMousePos.x));
            }
        });
    });
    
    $('#thermostate .overlay').bind('mouseup vmouseup', function(event) {
        $(document).unbind('mousemove vmousemove');
    });*/
    
    // plus and minus function
    /*$('#thermostate .temperature .minus').click(function() {
        thermostatTemperaturMinus()
    });
    
    $('#thermostate .temperature .plus').click(function() {
        thermostatTemperaturPlus();
    });
    
    var thermostatTemperaturMinusTimeout;
    $('#thermostate .temperature .minus').bind('mousedown vmousedown', function(event) {
        thermostatTemperaturMinusTimeout = setInterval(function(){
            thermostatTemperaturMinus()
        }, 100);
        
        return false;
    });
    
    $(document).bind('mouseup vmouseup', function(event) {
        clearInterval(thermostatTemperaturMinusTimeout);
        return false;
    });
    
    var thermostatTemperaturPlusTimeout;
    $('#thermostate .temperature .plus').bind('mousedown vmousedown', function(event) {
        thermostatTemperaturPlusTimeout = setInterval(function(){
            thermostatTemperaturPlus()
        }, 100);
        
        return false;
    });
    
    $(document).bind('mouseup vmouseup', function(event) {
        clearInterval(thermostatTemperaturPlusTimeout);
        return false;
    });*/
//});

// thermostate
/*function thermostatRotate(deg){
    if(0 >= deg){
        deg = 0;
    }
    
    if(130 <= deg){
       deg = 130; 
    }
    
    text = deg / 2;
    deg = deg.toString();
    $('#thermostate .overlay').css({
       /* 'transform': 'rotate('+ deg + 'deg)', /* For modern browsers(CSS3)  */
        /*'-ms-transform': 'rotate('+ deg + 'deg)', /* IE 9 */
        /*'-moz-transform': 'rotate('+ deg + 'deg)', /* Firefox */
        /*'-webkit-transform': 'rotate('+ deg + 'deg)', /* Safari and Chrome */
        /*'-o-transform': 'rotate('+ deg + 'deg)' /* Opera */
    /*});
    $('#thermostate .temperature .degree').html(text);
}

// plus and minus function
function thermostatTemperaturMinus(){
    var currentDeg = parseFloat($('#thermostate .temperature .degree').html());
    thermostatRotate((currentDeg * 2) - 1);
}

function thermostatTemperaturPlus(){
    var currentDeg = parseFloat($('#thermostate .temperature .degree').html());
    thermostatRotate((currentDeg * 2) + 1);
}*/