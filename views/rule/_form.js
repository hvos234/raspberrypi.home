$(document).ready(function(){
    // Rule Condition 
    // Condition
    $('.RuleCondition-condition').bind('change', function(event) {
        RuleConditionSetConditionValue($(this).attr('index'));
    });
    
    $('.RuleCondition-condition_value').bind('change', function(event) {
        RuleConditionSetConditionSubValue($(this).attr('index'));
    }); 
    
    // Value
    $('.RuleCondition-value').bind('change', function(event) {
        RuleConditionSetValuesValues($(this).attr('index'));
    });
    
    $('.RuleCondition-values_values').bind('change', function(event) {
        RuleConditionSetValueSubValue($(this).attr('index'));
    });
    
    // Rule Action 
    // Action
    $('.RuleAction-action').bind('change', function(event) {
        RuleActionSetActionValue($(this).attr('index'));
    });
    
    $('.RuleAction-action_value').bind('change', function(event) {
        RuleActionSetActionSubValue($(this).attr('index'));
    }); 
    
    // Value
    $('.RuleAction-value').bind('change', function(event) {
        RuleActionSetValuesValues($(this).attr('index'));
    });
    
    $('.RuleAction-values_values').bind('change', function(event) {
        RuleActionSetValueSubValue($(this).attr('index'));
    }); 
});

function RuleConditionActionGetIds(model_selector, model_id_selector){
    var model = $(model_selector).val();
    $(model_id_selector).empty(); // empty the select box
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=rule-condition/ajax-get-model-ids',  
        data: {model: model},
        dataType: 'json', // the return is a json string
        async: false,
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
                
                $(selector_model_id).find('option[value="none"]').attr('selected','selected'); // select the none option
            }
        }
    });
}

function RuleConditionActionGetFields(model_selector, model_id_selector, field_selector){
    var model = $(model_selector).val();
    var model_id = $(model_id_selector).val();
    $(field_selector).empty(); // empty the select box
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=rule-condition/ajax-get-model-fields',  
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
                        $(field_selector).prepend($("<option></option>").attr("value", field).text(name)); // prepend put the none key to top
                    }else {
                        $(field_selector).append($("<option></option>").attr("value", field).text(name));
                    }
                });
                
                $(field_selector).find('option[value="none"]').attr('selected','selected'); // select the none option
            }
        }
    });
}

// Rule Condition
function RuleConditionSetConditionValue(index){
    var condition_selector = $('select[name="RuleCondition[' + index + '][condition]"]');
    var condition_value_selector = $('select[name="RuleCondition[' + index + '][condition_value]"]');
    
    RuleConditionActionGetIds(condition_selector, condition_value_selector);
}

function RuleConditionSetConditionSubValue(index){
    var condition_selector = $('select[name="RuleCondition[' + index + '][condition]"]');
    var condition_value_selector = $('select[name="RuleCondition[' + index + '][condition_value]"]');
    var condition_sub_value_selector = $('select[name="RuleCondition[' + index + '][condition_sub_value]"]');
    
    RuleConditionActionGetFields(condition_selector, condition_value_selector, condition_sub_value_selector);
}