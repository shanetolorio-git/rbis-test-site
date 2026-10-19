<?php

namespace common\rbac;

use common\components\JurisdictionHelper;
use common\models\User;
use yii\rbac\Rule;

class RoadJurisdictionRule extends Rule
{
    public $name = 'roadJurisdiction';

    public function execute($user, $item, $params)
    {
        $currentUser = User::findOne($user);
        if (!$currentUser) {
            return false;
        }

        if ($currentUser->isSuperAdmin()) {
            return true;
        }

        return JurisdictionHelper::canAccessRoad($currentUser, $params['road'] ?? null);
    }
}
