<?php

namespace common\components;

use common\models\Road;
use common\models\User;
use yii\mongodb\ActiveQuery;

class JurisdictionHelper
{
    public static function getJurisdictionScope(User $user): array
    {
        return [
            'role' => $user->getRoleId(),
            'region_id' => $user->region_id,
            'province_id' => $user->province_id,
            'city_municipality_id' => $user->city_municipality_id,
        ];
    }

    public static function canAccessRoad(User $user, $road): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $roadRegion = self::getRoadValue($road, 'RegionCode');
        $roadProvince = self::getRoadValue($road, 'ProvinceCo');
        $roadCity = self::getRoadValue($road, 'CityMunCod');
        $role = strtolower($user->getRoleId() ?? '');

        if (in_array($role, [
            User::ROLE_CITY_ENCODER,
            User::ROLE_CITY_SUPERVISOR,
            User::ROLE_CITY_VIEWER,
        ], true)) {
            $userCity = (string)($user->city_municipality_id ?? '');
            return $userCity !== ''
                && (string)$roadCity === $userCity;
        }

        if (in_array($role, [
            User::ROLE_PROVINCIAL_ENCODER,
            User::ROLE_PROVINCIAL_SUPERVISOR,
            User::ROLE_PROVINCIAL_VIEWER,
        ], true)) {
            $userProvince = (string)($user->province_id ?? '');
            return $userProvince !== ''
                && (string)$roadProvince === $userProvince;
        }

        if ($role === User::ROLE_REGIONAL_VIEWER) {
            $userRegion = (string)($user->region_id ?? '');
            return $userRegion !== ''
                && (string)$roadRegion === $userRegion;
        }

        return false;
    }

    public static function filterRoadQueryByJurisdiction(ActiveQuery $query, User $user): ActiveQuery
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $role = strtolower($user->getRoleId() ?? '');

        if (in_array($role, [
            User::ROLE_CITY_ENCODER,
            User::ROLE_CITY_SUPERVISOR,
            User::ROLE_CITY_VIEWER,
        ], true)) {
            $userCity = (string)($user->city_municipality_id ?? '');
            if ($userCity === '') {
                return $query;
            }
            return $query->andWhere([
                'CityMunCod' => $userCity,
            ]);
        }

        if (in_array($role, [
            User::ROLE_PROVINCIAL_ENCODER,
            User::ROLE_PROVINCIAL_SUPERVISOR,
            User::ROLE_PROVINCIAL_VIEWER,
        ], true)) {
            $userProvince = (string)($user->province_id ?? '');
            if ($userProvince === '') {
                return $query;
            }
            return $query->andWhere([
                'ProvinceCo' => $userProvince,
            ]);
        }

        if ($role === User::ROLE_REGIONAL_VIEWER) {
            $userRegion = (string)($user->region_id ?? '');
            if ($userRegion === '') {
                return $query;
            }
            return $query->andWhere([
                'RegionCode' => $userRegion,
            ]);
        }

        return $query;
    }

    private static function getRoadValue($road, string $field)
    {
        if (is_array($road)) {
            return $road[$field] ?? null;
        }

        if ($road instanceof Road) {
            return $road->{$field};
        }

        if (is_object($road)) {
            return $road->{$field} ?? null;
        }

        return null;
    }
}
