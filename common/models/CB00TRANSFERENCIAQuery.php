<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CB00TRANSFERENCIA]].
 *
 * @see CB00TRANSFERENCIA
 */
class CB00TRANSFERENCIAQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return CB00TRANSFERENCIA[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CB00TRANSFERENCIA|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}