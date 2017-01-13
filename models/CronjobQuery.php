<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Cronjob]].
 *
 * @see Cronjob
 */
class CronjobQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Cronjob[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Cronjob|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}