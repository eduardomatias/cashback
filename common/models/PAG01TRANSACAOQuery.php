<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[PAG01TRANSACAO]].
 *
 * @see PAG01TRANSACAO
 */
class PAG01TRANSACAOQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return PAG01TRANSACAO[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PAG01TRANSACAO|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}