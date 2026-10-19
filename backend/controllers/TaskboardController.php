<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\data\ArrayDataProvider;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class TaskboardController extends Controller
{
    // ====================================
    // TASKBOARD LIST
    // ====================================

    public function actionIndex()
    {
        $collection =
            Yii::$app->mongodb
                ->getCollection(
                    'roads'
                );

        $user =
            Yii::$app->user->identity;

        $role =
            strtolower(
                trim(
                    (string) $user->role
                )
            );

        $filter = [];

        // ====================================
        // TASKBOARD STATUS BY ROLE
        // ====================================

        if (
            in_array(
                $role,
                [
                    'city_encoder',
                    'provincial_encoder'
                ],
                true
            )
        ) {

            // ENCODERS ONLY SEE
            // ROADS RETURNED FOR REVISION

            $filter['status'] = [
                '$in' => [
                    'forrevision'
                ]
            ];

        } elseif (
            in_array(
                $role,
                [
                    'city_supervisor',
                    'provincial_supervisor'
                ],
                true
            )
        ) {

            // SUPERVISORS SEE
            // ROADS REQUIRING REVIEW

            $filter['status'] = [
                '$in' => [
                    'forreview',
                    'inprogress'
                ]
            ];

        } elseif (
            $role === 'super_admin'
        ) {

            // SUPER ADMIN CAN SEE
            // ALL TASKBOARD STATUSES

            $filter['status'] = [
                '$in' => [
                    'forreview',
                    'inprogress',
                    'forrevision'
                ]
            ];

        } else {

            // OTHER ROLES SEE NOTHING

            $filter['_id'] = [
                '$exists' => false
            ];

        }

        // ====================================
        // PSGC JURISDICTION FILTER
        // ====================================

        // SUPER ADMIN = ALL ROADS

        if (
            $role === 'super_admin'
        ) {

            // No geographic restriction

        }

        // CITY LEVEL

        elseif (
            in_array(
                $role,
                [
                    'city_encoder',
                    'city_supervisor',
                    'city_viewer'
                ],
                true
            )
        ) {

            $filter['CityMunCod'] =
                (string) $user->city_municipality_id;

        }

        // PROVINCIAL LEVEL

        elseif (
            in_array(
                $role,
                [
                    'provincial_encoder',
                    'provincial_supervisor',
                    'provincial_viewer'
                ],
                true
            )
        ) {

            $filter['ProvinceCo'] =
                (string) $user->province_id;

        }

        // REGIONAL LEVEL

        elseif (
            $role === 'regional_viewer'
        ) {

            $filter['RegionCode'] =
                (string) $user->region_id;

        }

        // NO VALID JURISDICTION

        else {

            $filter['_id'] = [
                '$exists' => false
            ];

        }

        $roads =
            $collection->find(
                $filter
            );

        $dataProvider =
            new ArrayDataProvider([

                'allModels' =>
                    $roads,

                'pagination' => [
                    'pageSize' => 10,
                ],

            ]);

        return $this->render(
            'index',
            [
                'dataProvider' =>
                    $dataProvider
            ]
        );
    }

    // ====================================
    // VIEW ROAD FOR REVIEW
    // ====================================

    public function actionView($id)
    {
        $collection =
            Yii::$app->mongodb
                ->getCollection(
                    'roads'
                );

        $road =
            $collection->findOne([
                '_id' =>
                    new ObjectId($id)
            ]);

        if (!$road) {
            throw new \yii\web\NotFoundHttpException(
                'Road not found.'
            );
        }

        // ====================================
        // PSGC JURISDICTION CHECK
        // ====================================

        $user =
            Yii::$app->user->identity;

        $role =
            strtolower(
                trim(
                    (string) $user->role
                )
            );

        $hasAccess = false;

        // SUPER ADMIN = ALL ROADS
        if (
            $role === 'super_admin'
        ) {

            $hasAccess = true;

        }

        // CITY LEVEL
        elseif (
            in_array(
                $role,
                [
                    'city_encoder',
                    'city_supervisor',
                    'city_viewer'
                ],
                true
            )
        ) {

            $hasAccess =
                (string) ($road['CityMunCod'] ?? '') ===
                (string) ($user->city_municipality_id ?? '');

        }

        // PROVINCIAL LEVEL
        elseif (
            in_array(
                $role,
                [
                    'provincial_encoder',
                    'provincial_supervisor',
                    'provincial_viewer'
                ],
                true
            )
        ) {

            $hasAccess =
                (string) ($road['ProvinceCo'] ?? '') ===
                (string) ($user->province_id ?? '');

        }

        // REGIONAL LEVEL
        elseif (
            $role === 'regional_viewer'
        ) {

            $hasAccess =
                (string) ($road['RegionCode'] ?? '') ===
                (string) ($user->region_id ?? '');

        }

        // ====================================
        // DENY ACCESS
        // ====================================

        if (!$hasAccess) {

            throw new \yii\web\ForbiddenHttpException(
                'You are not authorized to access this road.'
            );
        }

        return $this->render(
            'view',
            [
                'road' => $road
            ]
        );
    }

    // ====================================
    // SUBMIT REVIEW
    // ====================================

// ====================================
// SUBMIT REVIEW
// ====================================

    public function actionReview($id)
    {
        Yii::$app->response->format =
            Response::FORMAT_JSON;

        $data =
            json_decode(
                file_get_contents(
                    'php://input'
                ),
                true
            );

        if (!$data) {

            return [

                'success' => false,

                'message' =>
                    'No data received.'
            ];
        }

        $collection =
            Yii::$app->mongodb
                ->getCollection(
                    'roads'
                );

        $roadId =
            new ObjectId($id);

        $road =
            $collection->findOne([
                '_id' =>
                    $roadId
            ]);

        if (!$road) {

            return [

                'success' => false,

                'message' =>
                    'Road not found.'
            ];
        }

        // ====================================
        // PSGC JURISDICTION CHECK
        // ====================================

        $user =
            Yii::$app->user->identity;

        $role =
            strtolower(
                trim(
                    (string) $user->role
                )
            );

        $hasAccess = false;

        // SUPER ADMIN = ALL ROADS
        if (
            $role === 'super_admin'
        ) {

            $hasAccess = true;

        }

        // CITY LEVEL
        elseif (
            in_array(
                $role,
                [
                    'city_supervisor',
                    'city_encoder',
                    'city_viewer'
                ],
                true
            )
        ) {

            $hasAccess =
                (string) ($road['CityMunCod'] ?? '') ===
                (string) ($user->city_municipality_id ?? '');

        }

        // PROVINCIAL LEVEL
        elseif (
            in_array(
                $role,
                [
                    'provincial_supervisor',
                    'provincial_encoder',
                    'provincial_viewer'
                ],
                true
            )
        ) {

            $hasAccess =
                (string) ($road['ProvinceCo'] ?? '') ===
                (string) ($user->province_id ?? '');

        }

        // REGIONAL LEVEL
        elseif (
            $role === 'regional_viewer'
        ) {

            $hasAccess =
                (string) ($road['RegionCode'] ?? '') ===
                (string) ($user->region_id ?? '');

        }

        // ====================================
        // DENY REVIEW
        // ====================================

        if (!$hasAccess) {

            return [

                'success' => false,

                'message' =>
                    'You are not authorized to review this road.'
            ];
        }

        // ====================================
        // EXISTING REVIEW PROCESS
        // ====================================

        $status =
            $data['status'] ?? '';

        $remarks =
            $data['remarks'] ?? '';
        $identity = Yii::$app->user->identity;
        $auditChanges = [
            [
                'field' => 'Review Status',
                'old' => (string)($road['status'] ?? ''),
                'new' => (string)$status
            ]
        ];
        if (trim((string)$remarks) !== '') {
            $auditChanges[] = [
                'field' => 'Supervisor Remarks',
                'old' => '',
                'new' => (string)$remarks
            ];
        }

        $collection->update(
            [
                '_id' =>
                    $roadId
            ],
            [

                '$set' => [

                    'status' =>
                        $status
                ],

                '$push' => [

                    'remarks_trail' => [

                        'status' =>
                            $status,

                        'remarks' =>
                            $remarks,

                        'date' =>
                            new UTCDateTime(),

                        'by' =>
                            Yii::$app->user->id
                    ],

                    'audit_trail' => [
                        'section' => 'Supervisor review',
                        'action' => 'Submitted supervisor decision',
                        'target' => '',
                        'changes' => $auditChanges,
                        'by' => Yii::$app->user->id,
                        'byName' => (string)($identity->username ?? ''),
                        'date' => new UTCDateTime()
                    ]
                ],

                '$unset' => [
                    'reviewChanges' => ''
                ]
            ]
        );

        return [

            'success' => true,

            'message' =>
                'Review submitted successfully.'
        ];
    }
   
}