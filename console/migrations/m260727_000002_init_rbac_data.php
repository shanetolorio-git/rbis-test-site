<?php

use common\rbac\RoadJurisdictionRule;
use yii\db\Migration;

class m260727_000002_init_rbac_data extends Migration
{
    public function safeUp()
    {
        $auth = \Yii::$app->authManager;

        $rule = new RoadJurisdictionRule();
        $auth->add($rule);

        $roadJurisdiction = $auth->createPermission('road.jurisdiction');
        $roadJurisdiction->description = 'Access road records within assigned jurisdiction.';
        $roadJurisdiction->ruleName = $rule->name;
        $auth->add($roadJurisdiction);

        $permissions = [
            'user.manage' => 'Manage all user accounts.',
            'user.create' => 'Create user accounts.',
            'user.update' => 'Update any user account.',
            'user.delete' => 'Delete user accounts.',
            'user.assignRole' => 'Assign and revoke user roles.',
            'user.assignJurisdiction' => 'Assign jurisdiction to users.',
            'road.view' => 'View road records.',
            'road.create' => 'Create road records.',
            'road.update' => 'Update road records.',
            'road.delete' => 'Delete road records.',
            'road.approve' => 'Approve road records.',
            'road.submit' => 'Submit road records for validation.',
            'road.review' => 'Review submitted road records.',
            'road.reject' => 'Reject submitted road records.',
            'road.return' => 'Return submitted road records to encoder.',
            'report.generate' => 'Generate reports.',
            'master.manage' => 'Manage master data and system configuration.',
            'system.manage' => 'Access all system modules.',
        ];

        foreach ($permissions as $name => $description) {
            $permission = $auth->createPermission($name);
            $permission->description = $description;
            $auth->add($permission);
        }

        $auth->addChild($roadJurisdiction, $auth->getPermission('road.view'));
        $auth->addChild($roadJurisdiction, $auth->getPermission('road.create'));
        $auth->addChild($roadJurisdiction, $auth->getPermission('road.update'));
        $auth->addChild($roadJurisdiction, $auth->getPermission('road.submit'));
        $auth->addChild($roadJurisdiction, $auth->getPermission('road.review'));
        $auth->addChild($roadJurisdiction, $auth->getPermission('road.reject'));
        $auth->addChild($roadJurisdiction, $auth->getPermission('road.return'));

        $superAdmin = $auth->createRole('super_admin');
        $auth->add($superAdmin);
        $auth->addChild($superAdmin, $auth->getPermission('user.manage'));
        $auth->addChild($superAdmin, $auth->getPermission('user.create'));
        $auth->addChild($superAdmin, $auth->getPermission('user.update'));
        $auth->addChild($superAdmin, $auth->getPermission('user.delete'));
        $auth->addChild($superAdmin, $auth->getPermission('user.assignRole'));
        $auth->addChild($superAdmin, $auth->getPermission('user.assignJurisdiction'));
        $auth->addChild($superAdmin, $auth->getPermission('master.manage'));
        $auth->addChild($superAdmin, $auth->getPermission('system.manage'));
        $auth->addChild($superAdmin, $auth->getPermission('road.delete'));
        $auth->addChild($superAdmin, $auth->getPermission('road.approve'));
        $auth->addChild($superAdmin, $auth->getPermission('report.generate'));
        $auth->addChild($superAdmin, $roadJurisdiction);

        $encoder = $auth->createRole('encoder');
        $auth->add($encoder);
        $auth->addChild($encoder, $auth->getPermission('road.create'));
        $auth->addChild($encoder, $auth->getPermission('road.update'));
        $auth->addChild($encoder, $auth->getPermission('road.submit'));
        $auth->addChild($encoder, $auth->getPermission('report.generate'));
        $auth->addChild($encoder, $roadJurisdiction);

        $provincialEncoder = $auth->createRole('provincial_encoder');
        $auth->add($provincialEncoder);
        $auth->addChild($provincialEncoder, $encoder);

        $cityEncoder = $auth->createRole('city_encoder');
        $auth->add($cityEncoder);
        $auth->addChild($cityEncoder, $encoder);

        $supervisor = $auth->createRole('supervisor');
        $auth->add($supervisor);
        $auth->addChild($supervisor, $auth->getPermission('road.view'));
        $auth->addChild($supervisor, $auth->getPermission('road.review'));
        $auth->addChild($supervisor, $auth->getPermission('road.approve'));
        $auth->addChild($supervisor, $auth->getPermission('road.reject'));
        $auth->addChild($supervisor, $auth->getPermission('road.return'));
        $auth->addChild($supervisor, $auth->getPermission('report.generate'));
        $auth->addChild($supervisor, $roadJurisdiction);

        $provincialSupervisor = $auth->createRole('provincial_supervisor');
        $auth->add($provincialSupervisor);
        $auth->addChild($provincialSupervisor, $supervisor);

        $citySupervisor = $auth->createRole('city_supervisor');
        $auth->add($citySupervisor);
        $auth->addChild($citySupervisor, $supervisor);

        $viewer = $auth->createRole('viewer');
        $auth->add($viewer);
        $auth->addChild($viewer, $auth->getPermission('road.view'));
        $auth->addChild($viewer, $auth->getPermission('report.generate'));
        $auth->addChild($viewer, $roadJurisdiction);

        $regionalViewer = $auth->createRole('regional_viewer');
        $auth->add($regionalViewer);
        $auth->addChild($regionalViewer, $viewer);

        $provincialViewer = $auth->createRole('provincial_viewer');
        $auth->add($provincialViewer);
        $auth->addChild($provincialViewer, $viewer);

        $cityViewer = $auth->createRole('city_viewer');
        $auth->add($cityViewer);
        $auth->addChild($cityViewer, $viewer);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
