<?php


namespace backend\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Road;

class RoadController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'view',
                            'carriageway-list',
                            'carriageway',
                            'save-carriageway'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create', 'save'],
                        'allow' => true,
                        'matchCallback' => function () {
                            return \Yii::$app->user->identity->hasPermission('road.create');
                        },
                    ],
                    [
                        'actions' => ['update-road-info'],
                        'allow' => true,
                        'matchCallback' => function () {
                            return \Yii::$app->user->identity->hasPermission('road.update');
                        }
                    ],

                    [
        'actions' => ['submit-for-review'],
        'allow' => true,
        'matchCallback' => function () {
            return \Yii::$app->user->identity
                ->hasPermission('road.update');
        }
    ],

                    [
                        
                        'actions' => [
                            'save-carriageway',
                            'update-carriageway',
                            'save-carriageway-updates',
                            'save-carriageway-batch',
                            'save-new-carriageways'
                        ],

                        'allow' => true,
                        'matchCallback' => function () {

                            $role = strtolower(
                                trim(
                                    \Yii::$app->user->identity->role
                                )
                            );

                            return in_array($role, [
                                'city_encoder',
                                'municipal_encoder',
                                'provincial_encoder'
                            ]);
                        }
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'matchCallback' => function () {
                            return \Yii::$app->user->identity->hasPermission('road.delete');
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'save' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    // ====================================
    // ROAD LIST
    // ====================================

    public function actionIndex()
    {
        $search =
            \Yii::$app->request
                ->get('search');

        $selectedClass =
            \Yii::$app->request
                ->get('class', []);

        $selectedStatus =
            \Yii::$app->request
                ->get('status', []);

        $query =
            Road::find()
                ->accessibleBy(
                    \Yii::$app->user->identity
                );

            // CLASS FILTER

        if (!empty($selectedClass)) {

            $query->andWhere([
                'R_CLASS' => $selectedClass
            ]);

        }

        // STATUS FILTER

        if (!empty($selectedStatus)) {

            $query->andWhere([
                'status' => $selectedStatus
            ]);

        }

        if (!empty($search)) {
            $searchTerm = trim((string) $search);

            if ($searchTerm !== '') {
                $query->andWhere([
                    'R_NAME' =>
                        new \MongoDB\BSON\Regex(
                            '^' . preg_quote($searchTerm, '/') . '$',
                            'i'
                        )
                ]);
            }
        }

        $dataProvider =
            new ActiveDataProvider([

                'query' =>
                    $query,

                'sort' => [
                    'defaultOrder' => [
                        'created_date' => SORT_DESC,
                    ],
                ],

                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);

        return $this->render(
            'index',
            [
                'dataProvider' =>
                    $dataProvider,

                'search' =>
                    $search,
            ]
        );
    }

    // ====================================
    // VIEW PAGE
    // ====================================

    public function actionView($id)
    {
        $collection =
            \Yii::$app->mongodb
                ->getCollection('roads');

        $road =
            $collection->findOne([
                '_id' =>
                    new \MongoDB\BSON\ObjectId(
                        trim($id)
                    )
            ]);

        if (!$road) {

            throw new NotFoundHttpException(
                'Road record not found or access denied.'
            );
        }

        $user =
            \Yii::$app->user->identity;

        if (!$user->canAccessRoad($road)) {

            throw new \yii\web\ForbiddenHttpException(
                'You are not authorized to access this road.'
            );
        }

        return $this->render('view', [
            'road' => $road
        ]);
    }

    public function actionCarriagewayList($id)
    {
        $collection =
            \Yii::$app->mongodb
                ->getCollection('roads');

        $road =
            $collection->findOne([
                '_id' =>
                    new \MongoDB\BSON\ObjectId(
                        trim($id)
                    )
            ]);

        if (!$road) {
            throw new NotFoundHttpException(
                'Road record not found or access denied.'
            );
        }

        return $this->render(
            'carriageway-list',
            [
                'road' => $road
            ]
        );
    }

    public function actionUpdateRoadInfo($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {

            $road = $this->findRoadModel($id);

            $data = json_decode(file_get_contents('php://input'), true);

            $collection = \Yii::$app->mongodb->getCollection('roads');
            $roadId = new \MongoDB\BSON\ObjectId($id);
            $currentRoad = $collection->findOne(['_id' => $roadId]);
            $roadFields = [
                'R_NAME' => $data['R_NAME'] ?? '',
                'R_CLASS' => $data['R_CLASS'] ?? '',
                'R_Importan' => $data['R_Importan'] ?? '',
                'Environmen' => $data['Environmen'] ?? '',
                'DirFlow' => $data['DirFlow'] ?? '',
                'Terrain' => $data['Terrain'] ?? '',
                'Length' => $data['Length'] ?? '',
                'RROW_acq_date' => $data['RROW_acq_date'] ?? '',
                'RROW_acq_cost' => $data['RROW_acq_cost'] ?? '',
            ];
            $roadFieldLabels = [
                'R_NAME' => 'Road Name',
                'R_CLASS' => 'Road Classification',
                'R_Importan' => 'Road Importance',
                'Environmen' => 'Environment',
                'DirFlow' => 'Direction of Flow',
                'Terrain' => 'Terrain',
                'Length' => 'Length',
                'RROW_acq_date' => 'Right-of-Way Acquisition Date',
                'RROW_acq_cost' => 'Right-of-Way Acquisition Cost',
            ];
            $changes = $this->buildAuditChanges(
                (array)$currentRoad,
                $roadFields,
                $roadFieldLabels
            );

            $collection->update(
                [
                    '_id' => $roadId
                ],
                [
                    '$set' => [
                        'R_NAME'      => $roadFields['R_NAME'],
                        'R_CLASS'     => $roadFields['R_CLASS'],
                        'R_Importan'  => $roadFields['R_Importan'],
                        'Environmen'  => $roadFields['Environmen'],
                        'DirFlow'     => $roadFields['DirFlow'],
                        'Terrain'     => $roadFields['Terrain'],
                        'Length'      => $roadFields['Length'],
                        'RROW_acq_date' => $roadFields['RROW_acq_date'],
                        'RROW_acq_cost' => $roadFields['RROW_acq_cost'],
                        'status' => 'inprogress',
                        'reviewChanges.roadSection' => true,
                        'lastupdate_date' => new \MongoDB\BSON\UTCDateTime(),
                    ]
                ]
            );
            $this->appendAuditTrail(
                $collection,
                $roadId,
                'Road section',
                'Updated road section',
                $changes
            );

            return [
                'success' => true,
                'message' => 'Updated'
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ];

        }
    }

    public function actionSubmitForReview($id)
    {
        \Yii::$app->response->format =
            \yii\web\Response::FORMAT_JSON;

        try {

            $collection =
                \Yii::$app->mongodb
                    ->getCollection('roads');

            $roadId =
                new \MongoDB\BSON\ObjectId(
                    trim($id)
                );

            $road =
                $collection->findOne([
                    '_id' => $roadId
                ]);

            if (!$road) {

                return [
                    'success' => false,
                    'message' => 'Road not found.'
                ];

            }

            $currentStatus =
                strtolower(
                    trim(
                        (string)(
                            $road['status'] ?? ''
                        )
                    )
                );

            if ($currentStatus !== 'validated') {

                return [
                    'success' => false,
                    'message' =>
                        'Only validated roads can be submitted for review.'
                ];

            }

            $collection->update(
                [
                    '_id' => $roadId
                ],
                [
                    '$set' => [
                        'status' => 'forreview'
                    ]
                ]
            );
            foreach ($newSegments as $newSegment) {
                $this->appendAuditTrail(
                    $collection,
                    $roadId,
                    'Road segments',
                    'Added road segment',
                    $this->buildAuditChanges(
                        [],
                        array_diff_key(
                            $newSegment,
                            ['_id' => true, 'geometry' => true]
                        ),
                        $this->segmentAuditLabels()
                    ),
                    (string)$newSegment['SegmentID']
                );
            }

            return [
                'success' => true,
                'message' =>
                    'Road submitted for supervisor review.'
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

        }
    }

    protected function findRoadModel($id)
    {
        $road = Road::find()
            ->accessibleBy(
                \Yii::$app->user->identity
            )
            ->where([
                '_id' =>
                    new \MongoDB\BSON\ObjectId(
                        trim($id)
                    )
            ])
            ->one();

        if (!$road) {
            throw new NotFoundHttpException('Road record not found or access denied.');
        }

        return $road;
    }

    // ====================================
    // CREATE PAGE
    // ====================================

    protected function getNextRoadSequence($collection, string $prefix): int
    {
        if ($prefix === '') {
            return 1;
        }

        $latestRoad = $collection->find(
            [
                'R_ID' => new \MongoDB\BSON\Regex(
                    '^' . preg_quote($prefix, '/') . '[0-9]{4}$'
                )
            ],
            [
                'sort' => ['R_ID' => -1],
                'limit' => 1
            ]
        )->toArray();

        if (empty($latestRoad[0]['R_ID'])) {
            return 1;
        }

        return (int)substr((string)$latestRoad[0]['R_ID'], -4) + 1;
    }

    public function actionCreate()
    {
        $user = \Yii::$app->user->identity;
        $collection = \Yii::$app->mongodb->getCollection('roads');
        $provincePrefix = trim((string)($user->province_id ?? ''));
        $cityPrefix = trim((string)($user->city_municipality_id ?? ''));

        $roadIdOptions = [
            'Provincial' => [
                'prefix' => $provincePrefix,
                'nextSequence' => $this->getNextRoadSequence($collection, $provincePrefix),
            ],
            'City' => [
                'prefix' => $cityPrefix,
                'nextSequence' => $this->getNextRoadSequence($collection, $cityPrefix),
            ],
            'Municipal' => [
                'prefix' => $cityPrefix,
                'nextSequence' => $this->getNextRoadSequence($collection, $cityPrefix),
            ],
            'Barangay' => [
                'prefix' => $cityPrefix,
                'nextSequence' => $this->getNextRoadSequence($collection, $cityPrefix),
            ],
        ];

        return $this->render(
            'create',
            [
                'roadIdOptions' => $roadIdOptions,
            ]
        );
    }

    // ====================================
    // SAVE ROAD
    // ====================================

    public function actionSave()
    {
        \Yii::$app->response->format =
            \yii\web\Response::FORMAT_JSON;

        $data =
            json_decode(
                file_get_contents('php://input'),
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
            \Yii::$app->mongodb
                ->getCollection(
                    'roads'
                );

        $currentUser = \Yii::$app->user->identity;

        $regionCode = trim((string)($currentUser->region_id ?? ''));
        $provinceCode = trim((string)($currentUser->province_id ?? ''));
        $cityMunCode = trim((string)($currentUser->city_municipality_id ?? ''));

        if ($regionCode === '') {
            $regionCode = trim((string)($data['RegionCode'] ?? ''));
        }
        if ($provinceCode === '') {
            $provinceCode = trim((string)($data['ProvinceCo'] ?? ''));
        }
        if ($cityMunCode === '') {
            $cityMunCode = trim((string)($data['CityMunCod'] ?? ''));
        }

        // ====================================
        // DETERMINE PREFIX
        // ====================================

        if (

            ($data['R_CLASS'] ?? '')
                == 'Provincial'

        ) {

            $prefix = $provinceCode;

        } else {

            $prefix = $cityMunCode;
        }

        if ($prefix === '') {
            return [
                'success' => false,
                'message' => 'A province or city/municipality code is required to create this road.'
            ];
        }

        $geometry = $data['geometry'] ?? null;
        if (($geometry['type'] ?? null) === 'FeatureCollection') {
            $geometry = $geometry['features'][0]['geometry'] ?? null;
        } elseif (($geometry['type'] ?? null) === 'Feature') {
            $geometry = $geometry['geometry'] ?? null;
        }

        $requestedSuffix = trim((string)($data['R_ID_SUFFIX'] ?? ''));

        if ($requestedSuffix === '') {
            $requestedSuffix = str_pad(
                (string)$this->getNextRoadSequence($collection, $prefix),
                4,
                '0',
                STR_PAD_LEFT
            );
        }

        if (!preg_match('/^[0-9]{4}$/', $requestedSuffix) || (int)$requestedSuffix < 1) {
            return [
                'success' => false,
                'message' => 'The last four digits of the Road ID must be a number from 0001 to 9999.'
            ];
        }

        $generatedRoadId = $prefix . $requestedSuffix;

        if ($collection->findOne(['R_ID' => $generatedRoadId])) {
            \Yii::$app->response->statusCode = 409;
            return [
                'success' => false,
                'message' => 'Road ID ' . $generatedRoadId . ' is already taken. Choose a different last-four-digit sequence.'
            ];
        }

        // ====================================
        // SAVE DOCUMENT
        // ====================================

        $document = [

            'R_ID' =>
                $generatedRoadId,

            'RegionCode' =>
                $regionCode,

            'ProvinceCo' =>
                $provinceCode,

            'CityMunCod' =>
                $cityMunCode,

            'Station' =>
                $data['Station'] ?? '',

            'R_NAME' =>
                $data['R_NAME'] ?? '',

            'R_CLASS' =>
                $data['R_CLASS'] ?? '',

            'R_Importan' =>
                $data['R_Importan'] ?? '',

            'Environmen' =>
                $data['Environmen'] ?? '',

            'DirFlow' =>
                $data['DirFlow'] ?? '',

            'Terrain' =>
                $data['Terrain'] ?? '',

            'Length' =>
                $data['Length'] ?? '',

            'RROW_acq_date' =>
                $data['RROW_acq_date'] ?? '',

            'RROW_acq_cost' =>
                $data['RROW_acq_cost'] ?? '',

            'geometry' =>
                $geometry,

            'status' =>
                $data['status'] ?? 'draft',

            'reviewChanges' => [
                'roadSection' => true,
                'segments' => false
            ],

            'remarks_trail' => [],

            'RoadCarriageway' => [],

            'created_date' =>
                new \MongoDB\BSON\UTCDateTime(),

            'created_by' =>
                \Yii::$app->user->id,
        ];

        $collection->insert(
            $document
        );

        return [

            'success' => true,

            'message' =>
                'Road saved successfully.',

            'R_ID' =>
                $generatedRoadId
        ];
    }

public function actionCarriageway($id, $segment = null)
{
    $collection =
        \Yii::$app->mongodb
            ->getCollection(
                'roads'
            );

    $road =
        $collection->findOne([
            '_id' =>
                new \MongoDB\BSON\ObjectId($id)
        ]);

    if (!$road) {
        throw new NotFoundHttpException(
            'Road record not found.'
        );
    }

    $segments =
        $road['RoadCarriageway']
        ?? [];

    $selectedSegment = null;

    // EDIT EXISTING SEGMENT
    if ($segment !== null) {

        foreach ($segments as $item) {

            if (
                isset($item['_id']) &&
                (string)$item['_id'] ===
                (string)$segment
            ) {

                $selectedSegment = $item;

                break;
            }
        }

        if (!$selectedSegment) {
            throw new NotFoundHttpException(
                'Carriageway segment not found.'
            );
        }
    }

    return $this->render(
        'carriageway',
        [
            'road' => $road,
            'segments' => $segments,
            'selectedSegment' => $selectedSegment
        ]
    );
}

public function actionSaveCarriageway($id)
{
    \Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    $data =
        json_decode(
            file_get_contents('php://input'),
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
        \Yii::$app->mongodb
            ->getCollection(
                'roads'
            );

    $roadId =
        new \MongoDB\BSON\ObjectId($id);

    $segmentId =
        time();

    $newSegment = [

        '_id' =>
            new \MongoDB\BSON\ObjectId(),

        'SegmentID' =>
            (string)$segmentId,

        'LRPStartKm' =>
            $data['lrpStartKm'] ?? '',

        'LRPEndKm' =>
            $data['lrpEndKm'] ?? '',

        'SegmentLen' =>
            $data['segmentLength'] ?? '',

        'CarrWidth' =>
            $data['carriagewayWidth'] ?? '',

        'SurfaceTyp' =>
            $data['surfaceType'] ?? '',

        'NumLanes' =>
            $data['numLanes'] ?? '',

        'LaneWidthL' =>
            $data['laneWidthLeft'] ?? '',

        'LaneWidthR' =>
            $data['laneWidthRight'] ?? '',

        'PavementTy' =>
            $data['pavementType'] ?? '',

        'PavementThick' =>
            $data['pavementThickness'] ?? '',

        'PavementStrength' =>
            $data['pavementStrength'] ?? '',

        'ConstrucDate' =>
            $data['constructionDate'] ?? '',

        'ConstrucValue' =>
            $data['constructionValue'] ?? '',

        'LastResurfaceDate' =>
            $data['lastResurfaceDate'] ?? '',

        'ReconYear' =>
            $data['reconstructionYear'] ?? '',

        'SegmentCon' =>
            $data['condition'] ?? '',

        'UsefulLife' =>
            $data['usefulLife'] ?? '',

        'geometry' =>
            $data['geometry'] ?? null
    ];

    $result =
        $collection->update(
            [
                '_id' =>
                    $roadId
            ],
            [

                '$push' => [

                    'RoadCarriageway' =>
                        $newSegment
                ],
                '$set' => [
                    'reviewChanges.segments' => true
                ],
            ]
        );
    $this->appendAuditTrail(
        $collection,
        $roadId,
        'Road segments',
        'Added road segment',
        $this->buildAuditChanges(
            [],
            array_diff_key(
                $newSegment,
                ['_id' => true, 'geometry' => true]
            ),
            $this->segmentAuditLabels()
        ),
        (string)$newSegment['SegmentID']
    );

    return [

        'success' => true,

        'message' =>
            'New carriageway segment added successfully.'
    ];
}


public function actionSaveCarriagewayBatch($id)
{
    \Yii::$app->response->format = Response::FORMAT_JSON;

    try {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            empty($data['segments']) ||
            !is_array($data['segments'])
        ) {
            return [
                'success' => false,
                'message' => 'No segments received.'
            ];
        }

        $collection = \Yii::$app->mongodb->getCollection('roads');
        $roadId = new \MongoDB\BSON\ObjectId(trim($id));

        $road = $collection->findOne(['_id' => $roadId]);

        if (!$road) {
            return [
                'success' => false,
                'message' => 'Road not found.'
            ];
        }

        $newSegments = [];

        foreach ($data['segments'] as $index => $segment) {
            if (
                !isset($segment['geometry']) ||
                ($segment['geometry']['type'] ?? '') !== 'LineString' ||
                empty($segment['geometry']['coordinates']) ||
                count($segment['geometry']['coordinates']) < 2
            ) {
                return [
                    'success' => false,
                    'message' => 'Segment ' . ($index + 1) .
                        ' has invalid LineString geometry.'
                ];
            }

            $coordinates = $segment['geometry']['coordinates'];

            foreach ($coordinates as $coordinate) {
                if (
                    !is_array($coordinate) ||
                    count($coordinate) < 2 ||
                    !is_numeric($coordinate[0]) ||
                    !is_numeric($coordinate[1]) ||
                    $coordinate[0] < -180 ||
                    $coordinate[0] > 180 ||
                    $coordinate[1] < -90 ||
                    $coordinate[1] > 90
                ) {
                    return [
                        'success' => false,
                        'message' => 'Segment ' . ($index + 1) .
                            ' contains invalid coordinates.'
                    ];
                }
            }

            $fields = $segment['fields'] ?? [];

            $newSegments[] = [
                '_id' => new \MongoDB\BSON\ObjectId(),
                'SegmentID' => (string) (time() . sprintf('%03d', $index)),
                'LRPStartKm' => $fields['LRPStartKm'] ?? '',
                'LRPEndKm' => $fields['LRPEndKm'] ?? '',
                'SegmentLen' => $fields['SegmentLen'] ?? '',
                'CarrWidth' => $fields['CarrWidth'] ?? '',
                'SurfaceTyp' => $fields['SurfaceTyp'] ?? '',
                'NumLanes' => $fields['NumLanes'] ?? '',
                'LaneWidthL' => $fields['LaneWidthL'] ?? '',
                'LaneWidthR' => $fields['LaneWidthR'] ?? '',
                'PavementTy' => $fields['PavementTy'] ?? '',
                'PavementThick' => $fields['PavementThick'] ?? '',
                'PavementStrength' => $fields['PavementStrength'] ?? '',
                'ConstrucDate' => $fields['ConstrucDate'] ?? '',
                'ConstrucValue' => $fields['ConstrucValue'] ?? '',
                'LastResurfaceDate' => $fields['LastResurfaceDate'] ?? '',
                'ReconYear' => $fields['ReconYear'] ?? '',
                'SegmentCon' => $fields['SegmentCon'] ?? '',
                'UsefulLife' => $fields['UsefulLife'] ?? '',
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $coordinates
                ]
            ];
        }

        $collection->update(
            ['_id' => $roadId],
            ['$push' => [
                'RoadCarriageway' => ['$each' => $newSegments]
            ],
            '$set' => [
                'reviewChanges.segments' => true
            ]]
        );
        foreach ($newSegments as $newSegment) {
            $this->appendAuditTrail(
                $collection,
                $roadId,
                'Road segments',
                'Added road segment',
                $this->buildAuditChanges(
                    [],
                    array_diff_key(
                        $newSegment,
                        ['_id' => true, 'geometry' => true]
                    ),
                    $this->segmentAuditLabels()
                ),
                (string)$newSegment['SegmentID']
            );
        }

        return [
            'success' => true,
            'message' => count($newSegments) .
                ' carriageway segment(s) saved successfully.',
            'count' => count($newSegments)
        ];
    } catch (\Throwable $e) {
        \Yii::error(
            'Batch carriageway save failed: ' . $e->getMessage(),
            __METHOD__
        );

        return [
            'success' => false,
            'message' => 'Unable to save carriageway segments.'
        ];
    }
}


public function actionUpdateCarriageway($id, $segment)
{
    \Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    try {

        $data =
            json_decode(
                file_get_contents('php://input'),
                true
            );

        if (!$data) {
            return [
                'success' => false,
                'message' => 'No data received.'
            ];
        }

        $collection =
            \Yii::$app->mongodb
                ->getCollection(
                    'roads'
                );

        $roadId =
            new \MongoDB\BSON\ObjectId(
                trim($id)
            );

        $segmentId =
            new \MongoDB\BSON\ObjectId(
                trim($segment)
            );

        $currentRoad = $collection->findOne(['_id' => $roadId]);
        if (!$currentRoad) {
            return [
                'success' => false,
                'message' => 'Road not found.'
            ];
        }

        $currentSegment = null;
        foreach (($currentRoad['RoadCarriageway'] ?? []) as $item) {
            if (
                isset($item['_id']) &&
                (string)$item['_id'] === (string)$segmentId
            ) {
                $currentSegment = (array)$item;
                break;
            }
        }

        if ($currentSegment === null) {
            return [
                'success' => false,
                'message' => 'Road segment not found.'
            ];
        }

        $segmentFields = [
            'LRPStartKm' => $data['lrpStartKm'] ?? '',
            'LRPEndKm' => $data['lrpEndKm'] ?? '',
            'SegmentLen' => $data['segmentLength'] ?? '',
            'CarrWidth' => $data['carriagewayWidth'] ?? '',
            'SurfaceTyp' => $data['surfaceType'] ?? '',
            'NumLanes' => $data['numLanes'] ?? '',
            'LaneWidthL' => $data['laneWidthLeft'] ?? '',
            'LaneWidthR' => $data['laneWidthRight'] ?? '',
            'PavementTy' => $data['pavementType'] ?? '',
            'PavementThick' => $data['pavementThickness'] ?? '',
            'PavementStrength' => $data['pavementStrength'] ?? '',
            'ConstrucDate' => $data['constructionDate'] ?? '',
            'ConstrucValue' => $data['constructionValue'] ?? '',
            'LastResurfaceDate' => $data['lastResurfaceDate'] ?? '',
            'ReconYear' => $data['reconstructionYear'] ?? '',
            'SegmentCon' => $data['condition'] ?? '',
            'UsefulLife' => $data['usefulLife'] ?? '',
            'geometry' => $data['geometry'] ?? null,
        ];
        $segmentChanges = $this->buildAuditChanges(
            $currentSegment,
            $segmentFields,
            $this->segmentAuditLabels()
        );

        $collection->update(
            [
                '_id' => $roadId,
                'RoadCarriageway._id' => $segmentId
            ],
            [
                '$set' => [

                    'RoadCarriageway.$.LRPStartKm' =>
                        $data['lrpStartKm'] ?? '',

                    'RoadCarriageway.$.LRPEndKm' =>
                        $data['lrpEndKm'] ?? '',

                    'RoadCarriageway.$.SegmentLen' =>
                        $data['segmentLength'] ?? '',

                    'RoadCarriageway.$.CarrWidth' =>
                        $data['carriagewayWidth'] ?? '',

                    'RoadCarriageway.$.SurfaceTyp' =>
                        $data['surfaceType'] ?? '',

                    'RoadCarriageway.$.NumLanes' =>
                        $data['numLanes'] ?? '',

                    'RoadCarriageway.$.LaneWidthL' =>
                        $data['laneWidthLeft'] ?? '',

                    'RoadCarriageway.$.LaneWidthR' =>
                        $data['laneWidthRight'] ?? '',

                    'RoadCarriageway.$.PavementTy' =>
                        $data['pavementType'] ?? '',

                    'RoadCarriageway.$.PavementThick' =>
                        $data['pavementThickness'] ?? '',

                    'RoadCarriageway.$.PavementStrength' =>
                        $data['pavementStrength'] ?? '',

                    'RoadCarriageway.$.ConstrucDate' =>
                        $data['constructionDate'] ?? '',

                    'RoadCarriageway.$.ConstrucValue' =>
                        $data['constructionValue'] ?? '',

                    'RoadCarriageway.$.LastResurfaceDate' =>
                        $data['lastResurfaceDate'] ?? '',

                    'RoadCarriageway.$.ReconYear' =>
                        $data['reconstructionYear'] ?? '',

                    'RoadCarriageway.$.SegmentCon' =>
                        $data['condition'] ?? '',

                    'RoadCarriageway.$.UsefulLife' =>
                        $data['usefulLife'] ?? '',

                    'RoadCarriageway.$.geometry' =>
                        $data['geometry'] ?? null,

                    'reviewChanges.segments' =>
                        true
                ]
            ]
        );
        $this->appendAuditTrail(
            $collection,
            $roadId,
            'Road segments',
            'Updated road segment',
            $segmentChanges,
            (string)($currentSegment['SegmentID'] ?? $segmentId)
        );

        return [
            'success' => true,
            'message' =>
                'Road segment updated successfully.'
        ];

    } catch (\Throwable $e) {

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
}

public function actionSaveNewCarriageways($id)
{
    \Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    try {

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !$data ||
            empty($data['segments']) ||
            !is_array($data['segments'])
        ) {
            return [
                'success' => false,
                'message' => 'No new segments were received.'
            ];
        }

        $collection =
            \Yii::$app->mongodb
                ->getCollection('roads');

        $roadId =
            new \MongoDB\BSON\ObjectId(
                trim($id)
            );

        $road =
            $collection->findOne([
                '_id' => $roadId
            ]);

        if (!$road) {
            return [
                'success' => false,
                'message' => 'Road record not found.'
            ];
        }

        $existingSegments =
            $road['RoadCarriageway'] ?? [];

        /*
         * Build new carriageway segments from the split queue.
         */
        $newSegments = [];

        foreach ($data['segments'] as $index => $item) {

            $fields =
                $item['fields'] ?? [];

            $newSegments[] = [

                '_id' =>
                    new \MongoDB\BSON\ObjectId(),

                'SegmentID' =>
                    date('YmdHis') .
                    str_pad(
                        (string)($index + 1),
                        2,
                        '0',
                        STR_PAD_LEFT
                    ),

                'LRPStartKm' =>
                    $fields['LRPStartKm'] ?? '',

                'LRPEndKm' =>
                    $fields['LRPEndKm'] ?? '',

                'SegmentLen' =>
                    $fields['SegmentLen'] ?? '',

                'CarrWidth' =>
                    $fields['CarrWidth'] ?? '',

                'SurfaceTyp' =>
                    $fields['SurfaceTyp'] ?? '',

                'NumLanes' =>
                    $fields['NumLanes'] ?? '',

                'LaneWidthL' =>
                    $fields['LaneWidthL'] ?? '',

                'LaneWidthR' =>
                    $fields['LaneWidthR'] ?? '',

                'PavementTy' =>
                    $fields['PavementTy'] ?? '',

                'PavementThick' =>
                    $fields['PavementThick'] ?? '',

                'PavementStrength' =>
                    $fields['PavementStrength'] ?? '',

                'ConstrucDate' =>
                    $fields['ConstrucDate'] ?? '',

                'ConstrucValue' =>
                    $fields['ConstrucValue'] ?? '',

                'LastResurfaceDate' =>
                    $fields['LastResurfaceDate'] ?? '',

                'ReconYear' =>
                    $fields['ReconYear'] ?? '',

                'SegmentCon' =>
                    $fields['SegmentCon'] ?? '',

                'UsefulLife' =>
                    $fields['UsefulLife'] ?? '',

                'geometry' =>
                    $item['geometry'] ?? null
            ];
        }

        $existingSegments = array_merge(
            $existingSegments,
            $newSegments
        );

        /*
         * Save the complete revised RoadCarriageway
         * array back to the road document.
         */
        $collection->update(
            [
                '_id' => $roadId
            ],
            [
                '$set' => [
                    'RoadCarriageway' =>
                        $existingSegments,
                    'reviewChanges.segments' =>
                        true
                ]
            ]
        );
        foreach ($newSegments as $newSegment) {
            $this->appendAuditTrail(
                $collection,
                $roadId,
                'Road segments',
                'Added road segment',
                $this->buildAuditChanges(
                    [],
                    array_diff_key(
                        $newSegment,
                        ['_id' => true, 'geometry' => true]
                    ),
                    $this->segmentAuditLabels()
                ),
                (string)$newSegment['SegmentID']
            );
        }

        return [
            'success' => true,
            'message' =>
                count($newSegments) .
                ' new carriageway segment(s) added successfully.',
            'count' =>
                count($newSegments)
        ];

    } catch (\Throwable $e) {

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
}

public function actionSaveCarriagewayUpdates($id)
{
    \Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    try {

        $data =
            json_decode(
                file_get_contents('php://input'),
                true
            );

        if (
            !$data ||
            !isset($data['segments']) ||
            !is_array($data['segments'])
        ) {

            return [
                'success' => false,
                'message' => 'No carriageway data received.'
            ];

        }

        $collection =
            \Yii::$app->mongodb
                ->getCollection('roads');

        $roadId =
            new \MongoDB\BSON\ObjectId(
                trim($id)
            );

        $road =
            $collection->findOne([
                '_id' => $roadId
            ]);

        if (!$road) {

            return [
                'success' => false,
                'message' => 'Road not found.'
            ];

        }

        foreach ($data['segments'] as $segment) {

            if (
                !isset($segment['id']) ||
                !isset($segment['fields'])
            ) {
                continue;
            }

            $segmentObjectId =
                new \MongoDB\BSON\ObjectId(
                    trim($segment['id'])
                );

            $fields =
                $segment['fields'];
            $currentSegment = null;
            foreach (($road['RoadCarriageway'] ?? []) as $item) {
                if (
                    isset($item['_id']) &&
                    (string)$item['_id'] === (string)$segmentObjectId
                ) {
                    $currentSegment = (array)$item;
                    break;
                }
            }

            if ($currentSegment === null) {
                continue;
            }

            $updatedFields = [];
            foreach ($this->segmentAuditLabels() as $field => $label) {
                if ($field !== 'geometry' && $field !== 'SegmentID') {
                    $updatedFields[$field] = $fields[$field] ?? '';
                }
            }
            $changes = $this->buildAuditChanges(
                $currentSegment,
                $updatedFields,
                $this->segmentAuditLabels()
            );

            $collection->update(
                [
                    '_id' =>
                        $roadId,

                    'RoadCarriageway._id' =>
                        $segmentObjectId
                ],
                [
                    '$set' => [

                        'RoadCarriageway.$.LRPStartKm' =>
                            $fields['LRPStartKm'] ?? '',

                        'RoadCarriageway.$.LRPEndKm' =>
                            $fields['LRPEndKm'] ?? '',

                        'RoadCarriageway.$.SegmentLen' =>
                            $fields['SegmentLen'] ?? '',

                        'RoadCarriageway.$.CarrWidth' =>
                            $fields['CarrWidth'] ?? '',

                        'RoadCarriageway.$.SurfaceTyp' =>
                            $fields['SurfaceTyp'] ?? '',

                        'RoadCarriageway.$.NumLanes' =>
                            $fields['NumLanes'] ?? '',

                        'RoadCarriageway.$.LaneWidthL' =>
                            $fields['LaneWidthL'] ?? '',

                        'RoadCarriageway.$.LaneWidthR' =>
                            $fields['LaneWidthR'] ?? '',

                        'RoadCarriageway.$.PavementTy' =>
                            $fields['PavementTy'] ?? '',

                        'RoadCarriageway.$.PavementThick' =>
                            $fields['PavementThick'] ?? '',

                        'RoadCarriageway.$.PavementStrength' =>
                            $fields['PavementStrength'] ?? '',

                        'RoadCarriageway.$.ConstrucDate' =>
                            $fields['ConstrucDate'] ?? '',

                        'RoadCarriageway.$.ConstrucValue' =>
                            $fields['ConstrucValue'] ?? '',

                        'RoadCarriageway.$.LastResurfaceDate' =>
                            $fields['LastResurfaceDate'] ?? '',

                        'RoadCarriageway.$.ReconYear' =>
                            $fields['ReconYear'] ?? '',

                        'RoadCarriageway.$.SegmentCon' =>
                            $fields['SegmentCon'] ?? '',

                        'RoadCarriageway.$.UsefulLife' =>
                            $fields['UsefulLife'] ?? '',

                        'reviewChanges.segments' =>
                            true
                    ]
                ]
            );
            $this->appendAuditTrail(
                $collection,
                $roadId,
                'Road segments',
                'Updated road segment',
                $changes,
                (string)($currentSegment['SegmentID'] ?? $segmentObjectId)
            );

        }

        return [
            'success' => true,
            'message' =>
                'All carriageway information saved successfully.'
        ];

    } catch (\Throwable $e) {

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

    }
}

private function segmentAuditLabels(): array
{
    return [
        'SegmentID' => 'Segment ID',
        'LRPStartKm' => 'LRP Start Km',
        'LRPEndKm' => 'LRP End Km',
        'SegmentLen' => 'Segment Length',
        'CarrWidth' => 'Carriageway Width',
        'SurfaceTyp' => 'Surface Type',
        'NumLanes' => 'Number of Lanes',
        'LaneWidthL' => 'Left Lane Width',
        'LaneWidthR' => 'Right Lane Width',
        'PavementTy' => 'Pavement Type',
        'PavementThick' => 'Pavement Thickness',
        'PavementStrength' => 'Pavement Strength',
        'ConstrucDate' => 'Construction Date',
        'ConstrucValue' => 'Construction Value',
        'LastResurfaceDate' => 'Last Resurface Date',
        'ReconYear' => 'Reconstruction Year',
        'SegmentCon' => 'Condition',
        'UsefulLife' => 'Useful Life',
        'geometry' => 'Segment Geometry',
    ];
}

private function buildAuditChanges(
    array $before,
    array $after,
    array $fieldLabels
): array {
    $changes = [];

    foreach ($fieldLabels as $field => $label) {
        $oldValue = $before[$field] ?? null;
        $newValue = $after[$field] ?? null;

        if ($field === 'geometry') {
            $oldGeometry = json_encode($oldValue);
            $newGeometry = json_encode($newValue);
            if ($oldGeometry === $newGeometry) {
                continue;
            }

            $oldDisplay = empty($oldValue) ? 'No geometry' : 'Existing geometry';
            $newDisplay = empty($newValue) ? 'No geometry' : 'Geometry updated';
        } else {
            $oldDisplay = $this->formatAuditValue($oldValue);
            $newDisplay = $this->formatAuditValue($newValue);
            if ($oldDisplay === $newDisplay) {
                continue;
            }
        }

        $changes[] = [
            'field' => $label,
            'old' => $oldDisplay,
            'new' => $newDisplay,
        ];
    }

    return $changes;
}

private function formatAuditValue($value): string
{
    if ($value === null) {
        return '';
    }
    if ($value instanceof \MongoDB\BSON\ObjectId) {
        return (string)$value;
    }
    if ($value instanceof \MongoDB\BSON\UTCDateTime) {
        return $value->toDateTime()->format('Y-m-d H:i:s');
    }
    if (is_bool($value)) {
        return $value ? 'Yes' : 'No';
    }
    if (is_array($value) || is_object($value)) {
        return (string)json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    return (string)$value;
}

private function appendAuditTrail(
    $collection,
    \MongoDB\BSON\ObjectId $roadId,
    string $section,
    string $action,
    array $changes,
    string $target = ''
): void {
    if (!$changes) {
        return;
    }

    $user = \Yii::$app->user->identity;
    $entry = [
        'section' => $section,
        'action' => $action,
        'target' => $target,
        'changes' => $changes,
        'by' => \Yii::$app->user->id,
        'byName' => (string)($user->username ?? ''),
        'date' => new \MongoDB\BSON\UTCDateTime(),
    ];

    $collection->update(
        ['_id' => $roadId],
        ['$push' => ['audit_trail' => $entry]]
    );
}

}