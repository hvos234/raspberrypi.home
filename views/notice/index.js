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
            url: '?r=notice/ajax-get-notices-last',
            dataType: 'json', // the return is a json string
            //async: false,
            success: function(datas) {
                if(datas.error) {
                    alert(datas.error);

                }else {
                    var notices = '';
                    $.each(datas, function(microtime, notice) {
                        //console.log(microtime);
                        //console.log(data);
                        
                        var date = new Date(parseInt(microtime));
                        var hours = date.getHours();
                        var minutes = "0" + date.getMinutes();
                        var seconds = "0" + date.getSeconds();
                        
                        notices += hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2) + ' ' + notice + '<br/>';
                        $('.notice-index .notices').html(notices);
                    });
                }
            }
        });
    }, (1000));
});