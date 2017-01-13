/**
 * use .prop
 * 
 * use .prop('selected', true) or .prop('selected', false) instate of
 * .attr('selected', 'selected') or .removeAttr('selected') and also
 * for readonly, if you do use .attr / .removeAttr the browser won`t 
 * update the select box proper and the javascript won`t get the right value
 */

$(document).ready(function(){
    // onchange Cronjob-job, show or hide the optgroup
    $('.Cronjob-job').on('change', function() {
        CronjobJobShowHideJobId();
    });
    
    // show or hide on loading page
    CronjobJobShowHideJobId();
    
    // hide or show the Cronjob-job_id optiongroups.
    // use the index to now witch row it is, and show or 
    // hide the optgroup, and select the first option
    // from the optgroup that is visible
    
    // use .prop
    function CronjobJobShowHideJobId(){
        var job_val = $("select[name='Cronjob[job]']").val();
        var job_id_val = $("select[name='Cronjob[job_id]']").val();
        var job_id_optgroup_label = $("select[name='Cronjob[job_id]'] option:selected").parent().attr('label');
        
        $("select[name='Cronjob[job_id]'] option").prop('selected', false);  // unselect all 
        $("select[name='Cronjob[job_id]'] optgroup").each(function() {
            
            if(job_val == $(this).attr('label')){
                
                $(this).show();
                //$(this).children().first().prop('selected', true);
                
                // check if this optgroup is not the same as the last condition_value_optgroup
                // if not select first option
                var element = $(this);
                
                if(job_id_optgroup_label != $(element).attr('label')){
                    $(element).children().first().prop('selected', true);
                    
                }else {
                    // loop trough the options, and check if the condition_value exists
                    // in the one of the options
                    // only the options under the correct optgroup
                    var job_id_val_exists = false;
                    $(element).children("option").each(function() {
                        if(job_id_val == $(this).val()){
                            job_id_val_exists = true;
                            $(this).prop('selected', true);
                        }
                    });
                    
                    if(!job_id_val_exists){
                        $(element).children().first().prop('selected', true);
                    }
                }
            }else {
                $(this).hide();
            }
        });
    }
});