<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class DashboardController extends Controller
{

    public function behaviors()
    {
        return [

            'access' => [

                'class' => AccessControl::class,

                'rules' => [

                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],

            ],

        ];
    }

    public function actionIndex()
    {
        $collection =
            \Yii::$app->mongodb
                ->getCollection('roads');

        // ====================================
        // ALL ROADS
        // ====================================

        $roads = $collection->find();

        // ====================================
        // TOTAL ROADS
        // ====================================

        $totalRoads = $collection->count();

        // ====================================
        // INITIALIZE VARIABLES
        // ====================================

        $totalLength = 0;

        // ROAD IMPORTANCE

        $coreCount = 0;
        $nonCoreCount = 0;

        // SURFACE TYPES

        $concrete = 0;
        $asphalt = 0;
        $gravel = 0;
        $earth = 0;

        // ROAD CONDITIONS

        $good = 0;
        $fair = 0;
        $poor = 0;
        $bad = 0;

        // ====================================
        // ROAD VALIDATION STATUS
        // ====================================

        $draft = 0;
        $forReview = 0;
        $forRevision = 0;
        $inProgress = 0;
        $validated = 0;
        $rejected = 0;

        // BRIDGES
        

        $totalBridges = 0;
        $totalBridgeLength = 0;

        // PAVED / UNPAVED

        $paved = 0;
        $unpaved = 0;

        // ====================================
        // LOOP ROADS
        // ====================================

        foreach ($roads as $road) {

        // ====================================
        // VALIDATION STATUS
        // ====================================

        $status =
            strtolower(
                $road['status'] ?? ''
            );

        switch ($status) {

            case 'draft':
                $draft++;
                break;

            case 'forreview':
                $forReview++;
                break;

            case 'revision':
                $forRevision++;
                break;

            case 'inprogress':
                $inProgress++;
                break;

            case 'validated':
                $validated++;
                break;

            case 'rejected':
                $rejected++;
                break;
        }

            // ====================================
            // ROAD IMPORTANCE
            // ====================================

            $importance =
                strtoupper(
                    $road['R_Importan'] ?? ''
                );

            if ($importance == 'CORE') {

                $coreCount++;

            } else {

                $nonCoreCount++;
            }

            // ====================================
            // ROAD CARRIAGEWAY
            // ====================================

            if (
                isset($road['RoadCarriageway']) &&
                !empty($road['RoadCarriageway'])
            ) {

                foreach (
                    $road['RoadCarriageway']
                    as $segment
                ) {

                    // ====================================
                    // SEGMENT LENGTH
                    // ====================================

                    $length = 0;

                    if (
                        isset($segment['SegmentLen'])
                    ) {

                        $length =
                            (float) (string)
                            $segment['SegmentLen'];
                    }

                    // TOTAL ROAD LENGTH

                    $totalLength +=
                        $length;

                    // ====================================
                    // SURFACE TYPE
                    // ====================================

                    $surface =
                        strtoupper(
                            $segment['SurfaceTyp'] ?? ''
                        );

                    if ($surface == 'C') {

                        $concrete +=
                            $length;

                    } elseif ($surface == 'A') {

                        $asphalt +=
                            $length;

                    } elseif ($surface == 'G') {

                        $gravel +=
                            $length;

                    } elseif ($surface == 'E') {

                        $earth +=
                            $length;
                    }

                    // ====================================
                    // ROAD CONDITION
                    // ====================================

                    // PAVED / UNPAVED

                        if (
                            $surface == 'C' ||
                            $surface == 'A'
                        ) {

                            $paved += $length;

                        } elseif (

                            $surface == 'G' ||
                            $surface == 'E'

                        ) {

                            $unpaved += $length;

                        }

                    $condition =
                        strtoupper(
                            $segment['SegmentCon'] ?? ''
                        );

                    if ($condition == 'G') {

                        $good +=
                            $length;

                    } elseif ($condition == 'F') {

                        $fair +=
                            $length;

                    } elseif ($condition == 'P') {

                        $poor +=
                            $length;

                    } elseif ($condition == 'B') {

                        $bad +=
                            $length;
                    }
                }
            }

            // ====================================
            // ROAD BRIDGES
            // ====================================

            if (
                isset($road['RoadBridges']) &&
                !empty($road['RoadBridges'])
            ) {

                foreach (
                    $road['RoadBridges']
                    as $bridge
                ) {

                    // TOTAL BRIDGES

                    $totalBridges++;

                    // BRIDGE LENGTH

                    $bridgeLength = 0;

                    if (
                        isset($bridge['Length'])
                    ) {

                        $bridgeLength =
                            (float) (string)
                            $bridge['Length'];
                    }

                    // TOTAL BRIDGE LENGTH

                    $totalBridgeLength +=
                        $bridgeLength;
                }
            }
        }

        // ====================================
        // VALIDATION PERCENTAGES
        // ====================================

        $validatedPercent =
            $totalRoads
            ? round(($validated / $totalRoads) * 100, 2)
            : 0;

        $forReviewPercent =
            $totalRoads
            ? round(($forReview / $totalRoads) * 100, 2)
            : 0;

        $forRevisionPercent =
            $totalRoads
            ? round(($forRevision / $totalRoads) * 100, 2)
            : 0;

        $inProgressPercent =
            $totalRoads
            ? round(($inProgress / $totalRoads) * 100, 2)
            : 0;

        $draftPercent =
            $totalRoads
            ? round(($draft / $totalRoads) * 100, 2)
            : 0;

        $rejectedPercent =
            $totalRoads
            ? round(($rejected / $totalRoads) * 100, 2)
            : 0;

        $totalRoadLength =
            $totalLength;

        $pavedPercentage =
            $totalRoadLength > 0
            ? round(
                ($paved / $totalRoadLength) * 100,
                2
            )
            : 0;

        $unpavedPercentage =
            $totalRoadLength > 0
            ? round(
                ($unpaved / $totalRoadLength) * 100,
                2
            )
            : 0;

        // ====================================
        // ROAD IMPORTANCE PERCENTAGE
        // ====================================

        $corePercentage =
            $totalRoads > 0
            ? round(
                ($coreCount / $totalRoads) * 100,
                2
            )
            : 0;

        $nonCorePercentage =
            $totalRoads > 0
            ? round(
                ($nonCoreCount / $totalRoads) * 100,
                2
            )
            : 0;

        // ====================================
        // RENDER VIEW
        // ====================================

        return $this->render('index', [

            // TOTALS

            'totalRoads' =>
                $totalRoads,

            'totalLength' =>
                number_format(
                    $totalLength,
                    3
                ),

            // ROAD IMPORTANCE

            'corePercentage' =>
                $corePercentage,

            'nonCorePercentage' =>
                $nonCorePercentage,

            // SURFACE TYPES


            'concrete' =>
                round($concrete, 2),

            'asphalt' =>
                round($asphalt, 2),

            'gravel' =>
                round($gravel, 2),

            'earth' =>
                round($earth, 2),

            'paved' =>
                round($paved, 2),

            'unpaved' =>
                round($unpaved, 2),

            'totalRoadLength' =>
                round($totalRoadLength, 2),

            'pavedPercentage' =>
                $pavedPercentage,

            'unpavedPercentage' =>
                $unpavedPercentage,
                

            // ROAD CONDITIONS

            'good' =>
                round($good, 2),

            'fair' =>
                round($fair, 2),

            'poor' =>
                round($poor, 2),

            'bad' =>
                round($bad, 2),

            // BRIDGES

            'totalBridges' =>
                $totalBridges,

            'totalBridgeLength' =>
                number_format(
                    $totalBridgeLength,
                    3
                ),
                // ROAD VALIDATION STATUS

            'draft' => $draft,

            'forReview' => $forReview,

            'forRevision' => $forRevision,

            'inProgress' => $inProgress,

            'validated' => $validated,

            'rejected' => $rejected,

            'draftPercent' => $draftPercent,

            'forReviewPercent' => $forReviewPercent,

            'forRevisionPercent' => $forRevisionPercent,

            'inProgressPercent' => $inProgressPercent,

            'validatedPercent' => $validatedPercent,

            'rejectedPercent' => $rejectedPercent,
        ]);
    }
}