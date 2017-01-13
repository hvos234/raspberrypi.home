<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Action]].
 *
 * @see Action
 */
class ActionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Action[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Action|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}