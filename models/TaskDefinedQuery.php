<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TaskDefined]].
 *
 * @see TaskDefined
 */
class TaskDefinedQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TaskDefined[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TaskDefined|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}