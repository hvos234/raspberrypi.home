$(document).ready(function(){    
    // thermostate
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
    });
    
    // plus and minus function
    $('#thermostate .temperature .minus').click(function() {
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
    });
});

// thermostate
function thermostatRotate(deg){
    if(0 >= deg){
        deg = 0;
    }
    
    if(130 <= deg){
       deg = 130; 
    }
    
    text = deg / 2;
    deg = deg.toString();
    $('#thermostate .overlay').css({
        'transform': 'rotate('+ deg + 'deg)', /* For modern browsers(CSS3)  */
        '-ms-transform': 'rotate('+ deg + 'deg)', /* IE 9 */
        '-moz-transform': 'rotate('+ deg + 'deg)', /* Firefox */
        '-webkit-transform': 'rotate('+ deg + 'deg)', /* Safari and Chrome */
        '-o-transform': 'rotate('+ deg + 'deg)' /* Opera */
    });
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
}

