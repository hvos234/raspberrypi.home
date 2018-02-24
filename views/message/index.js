$(document).ready(function(){
    //console.log = console.__proto__.log
    /*var date = new Date(1519505360029);
    console.log(date);
    //var date = new Date(microtime);
    // Hours part from the timestamp
    var hours = date.getHours();
    // Minutes part from the timestamp
    var minutes = "0" + date.getMinutes();
    // Seconds part from the timestamp
    var seconds = "0" + date.getSeconds();
    console.log(hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2));*/
    
    setInterval(function(){        
        $.ajax({
            // you can not use AjaxDeviceAction as action name, like in
            // the controller, they must be lowercase and with lines  
            url: '?r=message/ajax-get-messages',
            dataType: 'json', // the return is a json string
            //async: false,
            success: function(datas) {
                if(datas.error) {
                    alert(datas.error);

                }else {
                    var messages = '';
                    $.each(datas, function(microtime, message) {
                        //console.log(microtime);
                        //console.log(data);
                        
                        var date = new Date(parseInt(microtime));
                        var hours = date.getHours();
                        var minutes = "0" + date.getMinutes();
                        var seconds = "0" + date.getSeconds();
                        
                        messages += hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2) + ' ' + message;
                        $('.message-index .messages').html(messages);
                    });
                }
            }
        });
    }, (1000));
});