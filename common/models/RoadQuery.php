<?php

namespace common\models;

use common\components\JurisdictionHelper;
use yii\mongodb\ActiveQuery;

class RoadQuery extends ActiveQuery
{
    public function accessibleBy(User $user = null): self
    {
        if ($user === null) {
            return $this->andWhere(['_id' => null]);
        }

        return JurisdictionHelper::filterRoadQueryByJurisdiction($this, $user);
    }
}
