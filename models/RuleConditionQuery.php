<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RuleCondition]].
 *
 * @see RuleCondition
 */
class RuleConditionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RuleCondition[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RuleCondition|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}