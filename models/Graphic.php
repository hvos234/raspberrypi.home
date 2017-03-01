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
    SELECT *, AVG(data1), AVG(data2), AVG(data3) FROM data 
    
    WHERE 
    model = 'task' AND
    created_at BETWEEN '2017-01-01' AND '2017-12-31'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`)  
    ";
    
    $query['year_day'] = "
    SELECT *, AVG(data1), AVG(data2), AVG(data3) FROM data 
    
    WHERE 
    model = 'task' AND
    created_at BETWEEN '2017-01-01' AND '2017-12-31'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)
    ";
    
    $query['month_day'] = "
    SELECT *, AVG(data1), AVG(data2), AVG(data3) FROM data 
    
    WHERE 
    model = 'task' AND
    created_at BETWEEN '2017-02-01' AND '2017-02-31'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)
    ";
    
    $query['week_day'] = "
    SELECT *, AVG(data1), AVG(data2), AVG(data3) FROM data 
    
    WHERE 
    model = 'task' AND
    created_at BETWEEN '2017-02-01' AND '2017-02-31'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`)
    ";
    
    $query['day_hour'] = "
    SELECT id, model, model_id, key1, key2, key3, created_at, AVG(data1) as data1, AVG(data2) as data2, AVG(data3) as data3 FROM data 
    
    WHERE 
    model = 'task' AND
    key1 != 'err' AND key2 != 'err' AND key3 != 'err' AND
    created_at LIKE '2017-02-24%'
            
    GROUP BY YEAR(`created_at`), MONTH(`created_at`), DAY(`created_at`), HOUR(`created_at`)
    ";
    
    $query['day_all'] = "
    SELECT * FROM data 
    
    WHERE 
    model = 'task' AND
    created_at LIKE '2017-02-24%'
    ";
}