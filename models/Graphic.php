<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Data;

//use yii\helpers\Json;

class Graphic extends Model {
    //
    $query['year_month'] = "
    SELECT *, AVG(value) FROM log 
    
    WHERE 
    model = 'taskdefined' AND
    created_at BETWEEN '2017-01-01' AND '2017-12-31'
            
    GROUP BY name, YEAR(`created_at`), MONTH(`created_at`)  
    ";
    
    $query['year_day'] = "
    SELECT *, AVG(value) FROM log 
    
    WHERE 
    model = 'taskdefined' AND
    created_at BETWEEN '2017-01-01' AND '2017-12-31'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)
    ";
    
    $query['month_day'] = "
    SELECT *, AVG(data1), AVG(data2), AVG(data3) FROM data 
    
    WHERE 
    model = 'taskdefined' AND
    created_at BETWEEN '2017-02-01' AND '2017-02-31'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)
    ";
    
    $query['week_day'] = "
    SELECT *, AVG(value) FROM log 
    
    WHERE 
    model = 'taskdefined' AND
    created_at BETWEEN '2017-03-01' AND '2017-03-31'
            
    GROUP BY model, model_id, name, YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)
    ";
    
    $query['day_hour'] = "
    SELECT *, AVG(value) FROM log 
    
    WHERE 
    model = 'taskdefined' AND
    name != 'err' AND
    created_at LIKE '2017-03-01%'
            
    GROUP BY model_id, name, YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`), HOUR(`created_at`)
    ";
    
    $query['day_all'] = "
    SELECT * FROM data 
    
    WHERE 
    model = 'taskdefined' AND
    created_at LIKE '2017-02-24%'
    ";
}