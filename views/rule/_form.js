$(document).ready(function(){
    // Rule Condition 
    // Condition
    $('.rule-condition-condition').bind('change', function(event) {
        RuleConditionSetConditionValues($(this).attr('index'));
    });
    
    $('.rule-condition-condition_value').bind('change', function(event) {
        RuleConditionSetConditionSubValues($(this).attr('index'));
    });
    
    /*$('.rule-condition-condition_sub_value').bind('change', function(event) {
        
    });*/
        
    // Value
    $('.rule-condition-value').bind('change', function(event) {
        RuleConditionSetValueValues($(this).attr('index'));
    });
    
    $('.rule-condition-value_value').bind('change', function(event) {
        RuleConditionSetValueSubValues($(this).attr('index'));
    });
    
    /*$('.rule-condition-value_sub_value').bind('change', function(event) {
        
    });*/
    
    /*$('.rule-condition-value_sub_value2').bind('change', function(event) {
        
    });*/
    
    // Rule Action 
    // Action
    $('.rule-action-action').bind('change', function(event) {
        RuleActionSetActionValues($(this).attr('index'));
    });
    
    $('.rule-action-action_value').bind('change', function(event) {
        RuleActionSetActionSubValues($(this).attr('index'));
    });
    
    /*$('.rule-condition-condition_sub_value').bind('change', function(event) {
        
    });*/
    
    // Value
    $('.rule-action-value').bind('change', function(event) {
        RuleActionSetValueValues($(this).attr('index'));
    });
    
    $('.rule-action-value_value').bind('change', function(event) {
        RuleActionSetValueSubValues($(this).attr('index'));
    });
    
    /*$('.rule-action-value_sub_value').bind('change', function(event) {
    
    });*/
    
    /*$('.rule-action-value_sub_value2').bind('change', function(event) {
    
    });*/
    
    
    // add remove condition
    RuleConditionShowHideButtonAddRemove();
        
    // add
    $('.rule-condition_add').bind('click', function(event) {
        RuleConditionAdd();
    });
    
    // remove
    $('.rule-condition_remove').bind('click', function(event) {
        RuleConditionRemove();
    });
    
    // add remove action
    RuleActionShowHideButtonAddRemove();
        
    // add
    $('.rule-action_add').bind('click', function(event) {
        RuleActionAdd();
    });
    
    // remove
    $('.rule-action_remove').bind('click', function(event) {
        RuleActionRemove();
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
                
                $(model_id_selector).find('option[value="none"]').attr('selected','selected'); // select the none option
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
                
                // if there is only none hide field else show field
                if(1 == $(field_selector).find('option').length){
                    $(field_selector).hide();
                }else {
                    $(field_selector).show();
                }
            }
        }
    });
}

// Rule Condition
function RuleConditionSetConditionValues(index){
    var condition_selector = $('.rule-condition-condition[index="' + index + '"]');
    var condition_value_selector = $('.rule-condition-condition_value[index="' + index + '"]');
    
    RuleConditionActionGetIds(condition_selector, condition_value_selector);
    
    RuleConditionSetConditionSubValues(index);
}

function RuleConditionSetConditionSubValues(index){
    var condition_selector = $('.rule-condition-condition[index="' + index + '"]');
    var condition_value_selector = $('.rule-condition-condition_value[index="' + index + '"]');
    var condition_sub_value_selector = $('.rule-condition-condition_sub_value[index="' + index + '"]');
    
    RuleConditionActionGetFields(condition_selector, condition_value_selector, condition_sub_value_selector);
}

function RuleConditionSetValueValues(index){
    var value_selector = $('.rule-condition-value[index="' + index + '"]');
    var value_value_selector = $('.rule-condition-value_value[index="' + index + '"]');
    
    RuleConditionActionGetIds(value_selector, value_value_selector);
    
    RuleConditionSetValueSubValues(index);
}

function RuleConditionSetValueSubValues(index){
    var value_selector = $('.rule-condition-value[index="' + index + '"]');
    var value_value_selector = $('.rule-condition-value_value[index="' + index + '"]');
    var value_sub_value_selector = $('.rule-condition-value_sub_value[index="' + index + '"]');
    
    RuleConditionActionGetFields(value_selector, value_value_selector, value_sub_value_selector);
    
    var value = $('select[name="RuleCondition[' + index + '][value]"]').val();
    var value_value = $('select[name="RuleCondition[' + index + '][value_value]"]').val();
    
    if('RuleValue' == value && 'value' == value_value){
        //$('.rule-condition-value_sub_value[index="' + index + '"]').hide();
        $('.rule-condition-value_sub_value2[index="' + index + '"]').show();
        
    }else {
        //$('.rule-condition-value_sub_value[index="' + index + '"]').show();
        $('.rule-condition-value_sub_value2[index="' + index + '"]').hide();        
    }
    
    $('select[name="RuleCondition[' + index + '][value_sub_value]"]').val('none');
    $('select[name="RuleCondition[' + index + '][value_sub_value2]"]').val('');
}

// Rule Action
function RuleActionSetActionValues(index){
    var action_selector = $('.rule-action-action[index="' + index + '"]');
    var action_value_selector = $('.rule-action-action_value[index="' + index + '"]');
    
    RuleConditionActionGetIds(action_selector, action_value_selector);
    
    RuleActionSetActionSubValues(index);
}

function RuleActionSetActionSubValues(index){
    var action_selector = $('.rule-action-action[index="' + index + '"]');
    var action_value_selector = $('.rule-action-action_value[index="' + index + '"]');
    var action_sub_value_selector = $('.rule-action-action_sub_value[index="' + index + '"]');
    
    RuleConditionActionGetFields(action_selector, action_value_selector, action_sub_value_selector);
}

function RuleActionSetValueValues(index){
    var value_selector = $('.rule-action-value[index="' + index + '"]');
    var value_value_selector = $('.rule-action-value_value[index="' + index + '"]');
    
    RuleConditionActionGetIds(value_selector, value_value_selector);
    
    RuleActionSetValueSubValues(index);
}

function RuleActionSetValueSubValues(index){
    var value_selector = $('.rule-action-value[index="' + index + '"]');
    var value_value_selector = $('.rule-action-value_value[index="' + index + '"]');
    var value_sub_value_selector = $('.rule-action-value_sub_value[index="' + index + '"]');
    
    RuleConditionActionGetFields(value_selector, value_value_selector, value_sub_value_selector);
    
    var value = $('select[name="RuleAction[' + index + '][value]"]').val();
    var value_value = $('select[name="RuleAction[' + index + '][value_value]"]').val();
    
    if('RuleValue' == value && 'value' == value_value){
        //$('.rule-action-value_sub_value[index="' + index + '"]').hide();
        $('.rule-action-value_sub_value2[index="' + index + '"]').show();
        
    }else {
        //$('.rule-action-value_sub_value[index="' + index + '"]').show();
        $('.rule-action-value_sub_value2[index="' + index + '"]').hide();        
    }
    
    $('select[name="RuleAction[' + index + '][value_sub_value]"]').val('none');
    $('select[name="RuleAction[' + index + '][value_sub_value2]"]').val('');
}

// add remove
function RuleConditionActionShowHideButtonAddRemove(form_selector, button_add_selector, button_remove_selector){
    var count_conditaion_action = $(form_selector).find('ul li').length;
    var count_conditaion_action_hidden = $(form_selector).find('ul li:hidden').length;
    var count_conditaion_action_visible = $(form_selector).find('ul li:visible').length;
    
    if(1 == count_conditaion_action_visible){
        $(button_add_selector).show();
        $(button_add_selector).css('display', 'inline-block'); // buttons next to each other
        $(button_remove_selector).hide();
    }
    
    if(2 <= count_conditaion_action_visible){
        $(button_add_selector).show();
        $(button_add_selector).css('display', 'inline-block'); // buttons next to each other
        $(button_remove_selector).show();
        $(button_remove_selector).css('display', 'inline-block'); // buttons next to each other
    }
    
    if(count_conditaion_action <= count_conditaion_action_visible){
        $(button_add_selector).hide();
        $(button_remove_selector).show();
        $(button_remove_selector).css('display', 'inline-block'); // buttons next to each other
    }
}

function RuleConditionActionAdd(form_selector){
    $(form_selector).find('ul li:hidden:first').show(function(event) {
        /*
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
        */
       
       RuleConditionShowHideButtonAddRemove();
       RuleActionShowHideButtonAddRemove();
    });
}

function RuleConditionActionRemove(form_selector){
    $(form_selector).find('ul li:visible:last').hide(function(event) {
        /*
        // sortable
        $(this).addClass('ui-state-disabled'); // disable sortable

        // accordion
        $(this).find('.accordion').addClass('disabled');
        $(this).find('.accordion').removeClass('enabled');
        $(this).find('.accordion.disabled').accordion('destroy');
        */
       RuleConditionShowHideButtonAddRemove();
       RuleActionShowHideButtonAddRemove();
    });
}

// Rule Condition
function RuleConditionShowHideButtonAddRemove(){
    var form_selector = $('.rule-condition-form');
    var button_add_selector = $('.rule-condition_add');
    var button_remove_selector = $('.rule-condition_remove');
    
    RuleConditionActionShowHideButtonAddRemove(form_selector, button_add_selector, button_remove_selector);
}

function RuleConditionAdd(){
    var form_selector = $('.rule-condition-form');
    RuleConditionActionAdd(form_selector);
}

function RuleConditionRemove(){
    var form_selector = $('.rule-condition-form');
    RuleConditionActionRemove(form_selector);
}

// Rule Action
function RuleActionShowHideButtonAddRemove(){
    var form_selector = $('.rule-action-form');
    var button_add_selector = $('.rule-action_add');
    var button_remove_selector = $('.rule-action_remove');
    
    RuleConditionActionShowHideButtonAddRemove(form_selector, button_add_selector, button_remove_selector);
}

function RuleActionAdd(){
    var form_selector = $('.rule-action-form');
    RuleConditionActionAdd(form_selector);
}

function RuleActionRemove(){
    var form_selector = $('.rule-action-form');
    RuleConditionActionRemove(form_selector);
}