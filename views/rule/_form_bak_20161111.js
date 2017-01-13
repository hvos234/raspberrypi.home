/**
 * use .prop
 * 
 * use .prop('selected', true) or .prop('selected', false) instate of
 * .attr('selected', 'selected') or .removeAttr('selected') and also
 * for readonly, if you do use .attr / .removeAttr the browser won`t 
 * update the select box proper and the javascript won`t get the right value
 */

$(document).ready(function(){
    // on click Add Condition button (RuleCondition_add)
    // loop trough all the table rows and set by the
    // first one the display on table-row and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleCondition_add').on('click', function() {
        $('.RuleCondition-row').each(function( index ) {
            if('none' == $(this).css('display')){
                $(this).find('.RuleCondition-value').val('');
                $(this).css('display', 'table-row');
                RuleConditionShowHideButton();
                return false;
            }
        });
        RuleConditionShowHideButton();
        return false;
    });
    
    // on click Remove Condition button (RuleCondition_remove)
    // loop trough all the table rows and set by the
    // last one the display on none and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleCondition_remove').on('click', function() {
        // first in reverse order
        $($('.RuleCondition-row').get().reverse()).each(function(index) { 
            if('table-row' == $(this).css('display')){
                $(this).find('.RuleCondition-value').val(tNone);
                $(this).css('display', 'none');
                RuleConditionShowHideButton();
                return false;
            }
        });
        RuleConditionShowHideButton();
        return false;
    });
    
    // loop trough all the RuleCondition-row, and
    // count how many are visible (display table-row)
    // and show or hide the Add Condition button (RuleCondition_add) 
    // or the Remove Condition button (RuleCondition_remove)
    function RuleConditionShowHideButton(){
        var count = 0;
        var visible = 0;
        $('.RuleCondition-row').each(function( index ) {
            count++;
            if('table-row' == $(this).css('display')){
                visible++;
            }
        });
        if(1 >= visible){
            $('#RuleCondition_add').css('display', 'inline-block');
            $('#RuleCondition_remove').css('display', 'none');
        }
        if(1 < visible){
            $('#RuleCondition_add').css('display', 'inline-block');
            $('#RuleCondition_remove').css('display', 'inline-block');
        }
        if(count <= visible){
            $('#RuleCondition_add').css('display', 'none');
            $('#RuleCondition_remove').css('display', 'inline-block');
        }
    }
    
    RuleConditionShowHideButton();
    
    // onchange RuleCondtion Condition, hide or show
    // the RuleCondition Condition Values
    // add a onchange function to all the RuleCondition-condition
    $('.RuleCondition-condition').on('change', function() {
        RuleConditionShowHideConditionValue($(this).attr('index'));
    });
    
    // show or hide on loading page
    $('.RuleCondition-condition').each(function() {
        RuleConditionShowHideConditionValue($(this).attr('index'));
    });
    
    // hide or show the RuleConditionValue optiongroups.
    // use the index to now witch row it is, and show or 
    // hide the optgroup, and select the first option
    // from the optgroup that is visible
    
    // use .prop
    function RuleConditionShowHideConditionValue(index){
        var condition_val = $("select[name='RuleCondition["+index+"][condition]']").val();
        var condition_value_val = $("select[name='RuleCondition["+index+"][condition_value]']").val();
        var condition_value_optgroup_label = $("select[name='RuleCondition["+index+"][condition_value]'] option:selected").parent().attr('label');
        
        $("select[name='RuleCondition["+index+"][condition_value]'] option").prop('selected', false)  // unselect all 
        $("select[name='RuleCondition["+index+"][condition_value]'] optgroup").each(function() {
            if(condition_val == $(this).attr('label')){
                $(this).show();
                //$(this).children().first().prop('selected', true);
                
                // check if this optgroup is not the same as the last condition_value_optgroup
                // if not select first option
                var element = $(this);
                
                if(condition_value_optgroup_label != $(element).attr('label')){
                    $(element).children().first().prop('selected', true);
                    
                }else {
                    // loop trough the options, and check if the condition_value exists
                    // in the one of the options
                    // only the options under the correct optgroup
                    var condition_value_val_exists = false;
                    $(element).children("option").each(function() {
                        if(condition_value_val == $(this).val()){
                            condition_value_val_exists = true;
                            $(this).prop('selected', true);
                        }
                    });
                    if(!condition_value_val_exists){
                        $(element).children().first().prop('selected', true);
                    }
                }
            }else {
                $(this).hide();
            }
        });
    }
    
    // on click Add Action button (RuleAction_add)
    // loop trough all the table rows and set by the
    // first one the display on table-row and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleAction_add').on('click', function() {
        $('.RuleAction-row').each(function( index ) {
            if('none' == $(this).css('display')){
                $(this).find('.RuleAction-value_value').val('');
                $(this).css('display', 'table-row');
                RuleActionShowHideButton();
                return false;
            }
        });
        RuleActionShowHideButton();
        return false;
    });
    
    // on click Remove Action button (RuleAction_remove)
    // loop trough all the table rows and set by the
    // last one the display on none and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleAction_remove').on('click', function() {
        // first in reverse order
        $($('.RuleAction-row').get().reverse()).each(function(index) { 
            if('table-row' == $(this).css('display')){
                $(this).find('.RuleAction-value_value').val(tNone);
                $(this).css('display', 'none');
                RuleActionShowHideButton();
                return false;
            }
        });
        RuleActionShowHideButton();
        return false;
    });
    
    // loop trough all the RuleAction-row, and
    // count how many are visible (display table-row)
    // and show or hide the Add Action button (RuleAction_add) 
    // or the Remove Action button (RuleAction_remove)
    function RuleActionShowHideButton(){
        var count = 0;
        var visible = 0;
        $('.RuleAction-row').each(function( index ) {
            count++;
            if('table-row' == $(this).css('display')){
                visible++;
            }
        });
        if(1 >= visible){
            $('#RuleAction_add').css('display', 'inline-block');
            $('#RuleAction_remove').css('display', 'none');
        }
        if(1 < visible){
            $('#RuleAction_add').css('display', 'inline-block');
            $('#RuleAction_remove').css('display', 'inline-block');
        }
        if(count <= visible){
            $('#RuleAction_add').css('display', 'none');
            $('#RuleAction_remove').css('display', 'inline-block');
        }
    }
    
    RuleActionShowHideButton();
    
    // onchange RuleAction Action, hide or show
    // the RuleAction Action Values
    // add a onchange function to all the RuleAction-action
    $('.RuleAction-action').on('change', function() {
        RuleActionShowHideActionValue($(this).attr('index'));
    });
    
    // show or hide on loading page
    $('.RuleAction-action').each(function() {
        RuleActionShowHideActionValue($(this).attr('index'));
    });
    
    // hide or show the RuleAction Value optiongroups.
    // use the index to now witch row it is, and show or 
    // hide the optgroup, and select the first option
    // from the optgroup that is visible
    
    // use .prop
    function RuleActionShowHideActionValue(index){       
        var action_val = $("select[name='RuleAction["+index+"][action]']").val();
        var action_value_val = $("select[name='RuleAction["+index+"][action_value]']").val();
        var action_value_optgroup_label = $("select[name='RuleAction["+index+"][action_value]'] option:selected").parent().attr('label');
        
        $("select[name='RuleAction["+index+"][action_value]'] option").prop('selected', false)  // unselect all 
        $("select[name='RuleAction["+index+"][action_value]'] optgroup").each(function() {
            if(action_val == $(this).attr('label')){
                $(this).show();
                //$(this).children().first().prop('selected', true);
                
                // check if this optgroup is not the same as the last condition_value_optgroup
                // if not select first option
                var element = $(this);
                
                if(action_value_optgroup_label != $(element).attr('label')){
                    $(element).children().first().prop('selected', true);
                    
                }else {
                    // loop trough the options, and check if the condition_value exists
                    // in the one of the options
                    // only the options under the correct optgroup
                    var action_value_val_exists = false;
                    $(element).children("option").each(function() {
                        if(action_value_val == $(this).val()){
                            action_value_val_exists = true;
                            $(this).prop('selected', true);
                        }
                    });
                    if(!action_value_val_exists){
                        $(element).children().first().prop('selected', true);
                    }
                }
            }else {
                $(this).hide();
            }
        });
    }
    
    // RuleAction Value
    // 
    // onchange RuleAction[0][value] execute 1 to 6
    // onchange RuleAction[0][values_values] execute 5 and 6
    
    // 1. show or hide the RuleAction[0][values_values]
    // 2. show or hide the RuleAction[0][values_values] optgroups
    // 3. unselect all the RuleAction[0][values_values] option
    // 4. select the right RuleAction[0][values_values] option
    // 5. set the RuleAction[0][value_value] readonly or not
    // 6. fill the right value from RuleAction[0][values_values] to 
    //    RuleAction[0][value_value]
    
    // onchange 
    $('.RuleAction-value').on('change', function() {
        RuleActionValuesValuesShowHide($(this).attr('index'));
    });
    
    // on page load
    // show or hide on loading page
    $('.RuleAction-value').each(function() {
        RuleActionValuesValuesShowHide($(this).attr('index'));
    });
    
    // onchange 
    $('.RuleAction-values_values').on('change', function() {
        RuleActionValueValue($(this).attr('index'));
    });
        
    // use .prop
    function RuleActionValuesValuesShowHide(index){        
        var value_val = $("select[name='RuleAction["+index+"][value]']").val();
        var values_values_val = $("select[name='RuleAction["+index+"][values_values]']").val();
        var values_values_optgroup_label = $("select[name='RuleAction["+index+"][values_values]'] option:selected").parent().attr('label');
        
        if('value' == value_val || 'on' == value_val || 'off' == value_val){
            $("select[name='RuleAction["+index+"][values_values]']").hide();
        }else {
            $("select[name='RuleAction["+index+"][values_values]']").show();
            
            $("select[name='RuleAction["+index+"][values_values]'] optgroup").each(function() {
                if(value_val == $(this).attr('label')){
                    $(this).show();
                    //$(this).children().first().prop('selected', true);
                    
                    // check if this optgroup is not the same as the last condition_value_optgroup
                    // if not select first option
                    var element = $(this);

                    if(values_values_optgroup_label != $(element).attr('label')){
                        $(element).children().first().prop('selected', true);

                    }else {
                        // loop trough the options, and check if the condition_value exists
                        // in the one of the options
                        // only the options under the correct optgroup
                        var values_values_val_exists = false;
                        $(element).children("option").each(function() {
                            if(values_values_val == $(this).val()){
                                values_values_val_exists = true;
                                $(this).prop('selected', true);
                            }
                        });
                        if(!values_values_val_exists){
                            $(element).children().first().prop('selected', true);
                        }
                    }
                }else {
                    $(this).hide();
                }
            });
        }
        RuleActionValueValue(index);
    }
    
    function RuleActionValueValue(index){
        
        if(tNone != $("input[name='RuleAction["+index+"][value_value]']").val()){
            
            var value = $("select[name='RuleAction["+index+"][value]']").val();

            if('value' == value){
                $("input[name='RuleAction["+index+"][value_value]']").prop('readonly', false);
                $("input[name='RuleAction["+index+"][value_value]']").val('');

            }else if('on' == value){
                $("input[name='RuleAction["+index+"][value_value]']").prop('readonly', true);
                $("input[name='RuleAction["+index+"][value_value]']").val('1');

            }else if('off' == value){
                $("input[name='RuleAction["+index+"][value_value]']").prop('readonly', true);
                $("input[name='RuleAction["+index+"][value_value]']").val('0');

            }else {
                $("input[name='RuleAction["+index+"][value_value]']").prop('readonly', true);
                $("input[name='RuleAction["+index+"][value_value]']").val($("select[name='RuleAction["+index+"][values_values]']").val());
            }
        }
    }
});