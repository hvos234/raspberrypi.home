$(document).ready(function(){
    // Action
    $('.action_model').bind('change', function(event) {
        ActionModelSetModelIds();
    });
    
    $('.action_model_id').bind('change', function(event) {
        ActionModelSetFields();
    });
});

function ActionModelSetModelIds(){
    var model = $('.action_model').val();
    $('.action_model_id').empty(); // empty the select box
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=voice/ajax-get-model-ids',  
        data: {model: model},
        dataType: 'json', // the return is a json string
        async: false,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(model_id, name) {
                    if('none' == model_id){
                        $('.action_model_id').prepend($("<option></option>").attr("value", model_id).text(name)); // prepend put the none key to top
                    }else {
                        $('.action_model_id').append($("<option></option>").attr("value", model_id).text(name));
                    }
                });
                
                $('.action_model_id').find('option[value="none"]').attr('selected','selected'); // select the none option
            }
        }
    });
}

function ActionModelSetFields(){
    var model = $('.action_model').val();
    var model_id = $('.action_model_id').val();
    $('.action_model_field').empty(); // empty the select box
        
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=voice/ajax-get-model-fields',  
        data: {
            model: model,
            model_id: model_id
        },
        dataType: 'json', // the return is a json string
        //async: false,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(field, name) {
                    if('none' == field){
                        $('.action_model_field').prepend($("<option></option>").attr("value", field).text(name)); // prepend put the none key to top
                    }else {
                        $('.action_model_field').append($("<option></option>").attr("value", field).text(name));
                    }
                });
                
                $('.action_model_field').find('option[value="none"]').attr('selected','selected'); // select the none option
                
                // if there is only none hide field else show field
                if(1 == $('.action_model_field').find('option').length){
                    $('.action_model_field').hide();
                }else {
                    $('.action_model_field').show();
                }
            }
        }
    });
}