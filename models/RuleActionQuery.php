<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RuleAction]].
 *
 * @see RuleAction
 */
class RuleActionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return RuleAction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RuleAction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}