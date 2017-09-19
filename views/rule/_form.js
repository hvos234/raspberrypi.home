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

function RuleConditionSetConditionValue(index){
    var condition = $('select[name="RuleCondition[' + index + '][condition]"]').val();
    
    $('select[name="RuleCondition[' + index + '][condition_value]"]').empty(); // empty the select box
    
    $.ajax({
        // you can not use AjaxDeviceAction as action name, like in
        // the controller, they must be lowercase and with lines
        url: '?r=rule-condition/ajax-get-ids',  
        data: {model: condition},
        dataType: 'json', // the return is a json string
        async: true,
        success: function(data) {
            if(data.error) {
                alert(data.error);
                
            }else {
                $.each(data, function(condition_value, name) {
                    if('none' == condition_value){
                        $('select[name="RuleCondition[' + index + '][condition_value]"]').prepend($("<option></option>").attr("value", condition_value).text(name)); // prepend put the none key to top
                    }else {
                        $('select[name="RuleCondition[' + index + '][condition_value]"]').append($("<option></option>").attr("value", condition_value).text(name));
                    }
                });
                
                $('select[name="RuleCondition[' + index + '][condition_value]"]').find('option[value="none"]').attr('selected','selected'); // select the none option
            }
        }
    });
}