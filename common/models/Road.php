<?php

namespace common\models;

use yii\mongodb\ActiveRecord;

class Road extends ActiveRecord
{
    public static function collectionName()
    {
        return ['local', 'roads'];
    }

    public static function find()
    {
        return new RoadQuery(get_called_class());
    }

    public function attributes()
    {
        return [
            '_id',
            'R_ID',
            'R_NAME',
            'R_CLASS',
            'R_Importan',
            'Environment',
            'DirFlow',
            'Terrain',
            'Length',
            'Station',
            'RegionCode',
            'ProvinceCo',
            'CityMunCod',
            'status',
            'approval_status',
            'shapefile',
            'geometry',
            'created_date',
            'lastupdate_date',
        ];
    }
}