<?php

use yii\db\Migration;

class m260727_000003_add_user_jurisdiction_columns extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'region_id', $this->string()->null());
        $this->addColumn('{{%user}}', 'province_id', $this->string()->null());
        $this->addColumn('{{%user}}', 'city_municipality_id', $this->string()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'city_municipality_id');
        $this->dropColumn('{{%user}}', 'province_id');
        $this->dropColumn('{{%user}}', 'region_id');
        $this->dropColumn('{{%user}}', 'role');
    }
}
