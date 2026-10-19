<?php

$this->title =
    'Road Details';

?>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

<div class="container-fluid mt-4">

    <!-- ===================================== -->
    <!-- ROAD HEADER -->
    <!-- ===================================== -->

    <div class="card p-4 mb-4">

        <div
            class="
                d-flex
                justify-content-between
                align-items-center
            "
        >

            <div>

                <h2>

                    <?= $road['R_NAME'] ?>

                </h2>

                <span
                    class="
                        badge
                        bg-success
                    "
                    style="
                        font-size:14px;
                    "
                >

                    <?= strtoupper(
                        $road['status']
                    ) ?>

                </span>

            </div>

            <?php

           $status = strtolower(trim($road['status'] ?? ''));
            $role   = strtolower(trim(Yii::$app->user->identity->role));

            $isEncoder = in_array($role, [
                'city_encoder',
                'provincial_encoder'
            ]);

            $isSupervisor = in_array($role, [
                'city_supervisor',
                'provincial_supervisor'
            ]);

            $canUpdateRoad = $isEncoder && $status === 'validated';

            ?>

            <div>

            <?php if ($canUpdateRoad): ?>

                <button
                    class="btn btn-primary"
                    id="updateRoadBtn">

                    UPDATE ROAD INFO

                </button>

                <button
                    class="btn btn-success"
                    id="saveRoadBtn"
                    style="display:none;">

                    SAVE CHANGES

                </button>

            <?php elseif ($isEncoder && $status == 'forreview'): ?>

                <div class="alert alert-info mb-0">
                    Waiting for Supervisor approval.
                </div>

            <?php elseif ($isEncoder && $status == 'inprogress'): ?>

                <div class="alert alert-warning mb-0">
                    This road is currently under Supervisor review.
                </div>

            <?php elseif ($isEncoder && $status == 'forrevision'): ?>

                <div class="alert alert-danger mb-0">
                    Continue updating this road through the Taskboard.
                </div>

            <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- ===================================== -->
    <!-- ROAD DETAILS -->
    <!-- ===================================== -->

    <div class="card p-4 mb-4">

        <div class="row">

            <!-- LEFT -->

            <div class="col-md-6">

                <div class="mb-3">

                    <label>
                        Road ID
                    </label>

                    <h5>

                        <?= isset($road['Station']) ? htmlspecialchars($road['Station'], ENT_QUOTES, 'UTF-8') : (isset($road['R_ID']) ? htmlspecialchars($road['R_ID'], ENT_QUOTES, 'UTF-8') : 'N/A') ?>

                    </h5>

                </div>

                <div class="mb-3">

                    <label>
                        Road Name
                    </label>

                <input
                    type="text"
                    id="roadName"
                    class="
                        form-control
                        road-edit
                    "
                    value="<?= $road['R_NAME'] ?>"
                    readonly
                >

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>
                            Road Classification
                        </label>

                        <select
                            id="roadClass"
                            class="form-control road-edit"
                            disabled
                        >

                            <option value="">
                            </option>

                            <?php
                            $allowedRoadClasses =
                                Yii::$app->user->identity
                                    ->getAllowedRoadClassifications();
                            ?>

                            <?php foreach ($allowedRoadClasses as $roadClass): ?>

                                <option
                                    value="<?= htmlspecialchars($roadClass, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= ($road['R_CLASS'] ?? '') === $roadClass ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($roadClass, ENT_QUOTES, 'UTF-8') ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>
                            Road Importance
                        </label>

                        <select
                            id="roadImp"
                            class="form-control road-edit"
                            disabled
                        >

                            <option value=""
                                <?= empty($road['R_Importan']) ? 'selected' : '' ?>>
                                
                            </option>

                            <option value="Core"
                                <?= ($road['R_Importan'] ?? '') == 'Core' ? 'selected' : '' ?>>
                                Core
                            </option>

                            <option value="Non-Core"
                                <?= ($road['R_Importan'] ?? '') == 'Non-Core' ? 'selected' : '' ?>>
                                Non-Core
                            </option>

                        </select>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>
                            Environment
                        </label>

                        <select
                            id="Environmen"
                            class="form-control road-edit"
                            disabled
                        >

                            <option value=""
                                <?= empty($road['Environmen']) ? 'selected' : '' ?>>
                                
                            </option>

                            <option value="Urban"
                                <?= ($road['Environmen'] ?? '') == 'Urban' ? 'selected' : '' ?>>
                                Urban
                            </option>

                            <option value="Rural"
                                <?= ($road['Environmen'] ?? '') == 'Rural' ? 'selected' : '' ?>>
                                Rural
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>
                            Direction Flow
                        </label>

                        <select
                            id="DirFlow"
                            class="form-control road-edit"
                            disabled
                        >

                            <option value=""
                                <?= empty($road['DirFlow']) ? 'selected' : '' ?>>
                                
                            </option>

                            <option value="One-way"
                                <?= ($road['DirFlow'] ?? '') == 'One-way' ? 'selected' : '' ?>>
                                One-way
                            </option>

                            <option value="Two-way"
                                <?= ($road['DirFlow'] ?? '') == 'Two-way' ? 'selected' : '' ?>>
                                Two-way
                            </option>

                        </select>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>
                            Terrain
                        </label>

                        <select
                            id="Terrain"
                            class="form-control road-edit"
                            disabled
                        >

                            <option value=""
                                <?= empty($road['Terrain']) ? 'selected' : '' ?>>
                                
                            </option>

                            <option value="Flat"
                                <?= ($road['Terrain'] ?? '') == 'Flat' ? 'selected' : '' ?>>
                                Flat
                            </option>

                            <option value="Rolling"
                                <?= ($road['Terrain'] ?? '') == 'Rolling' ? 'selected' : '' ?>>
                                Rolling
                            </option>

                            <option value="Mountainous"
                                <?= ($road['Terrain'] ?? '') == 'Mountainous' ? 'selected' : '' ?>>
                                Mountainous
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>
                            Length
                        </label>

                        <input
                            type="text"
                            id="roadLength"
                            class="form-control road-edit"
                            value="<?= $road['Length'] ?? '' ?>"
                            readonly
                        >

                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <!-- MAP -->

            <div class="col-md-6">

                <div
                    id="map"
                ></div>

            </div>

        </div>

    </div>

    <?= $this->render('@backend/views/shared/_audit-trail', [
        'road' => $road
    ]) ?>

    <!-- ===================================== -->
    <!-- REMARKS -->
    <!-- ===================================== -->

    <div class="card p-4 mb-4">

        <h4>
            Remarks History
        </h4>

        <table
        class="
            table
            table-bordered
        "
    >

        <thead>

            <tr>

                <th>
                    Status
                </th>

                <th>
                    Submitted By
                </th>

                <th>
                    Validated By
                </th>

                <th>
                    Remarks
                </th>

            </tr>

        </thead>

        <tbody>

            <?php

            if (
                isset($road['remarks_trail'])
            ):

                foreach (
                    $road['remarks_trail']
                    as $remark
                ):

                    $status =
                        strtolower(
                            trim(
                                (string)(
                                    $remark['status']
                                    ?? ''
                                )
                            )
                        );

                    $statusLabel = [
                        'inprogress' =>
                            'In Progress',

                        'validated' =>
                            'Validated / Approved',

                        'forreview' =>
                            'For Review',

                        'forrevision' =>
                            'For Revision',

                        'rejected' =>
                            'Rejected',

                        'draft' =>
                            'Draft'
                    ];

                    $displayStatus =
                        $statusLabel[$status]
                        ?? ucfirst($status);

                    $userName = '—';

                    /*
                    * Get the user who created
                    * this history entry.
                    *
                    * We are only reading the
                    * existing "by" value.
                    */
                    if (
                        isset($remark['by'])
                    ) {

                        $user =
                            \common\models\User::findOne(
                                (int)$remark['by']
                            );

                        if ($user) {

                            $userName =
                                $user->username;

                        }

                    }

            ?>

                <tr>

                    <td>

                        <?= htmlspecialchars(
                            $displayStatus,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                    <td>

                        <?php if (
                            $status === 'inprogress'
                        ): ?>

                            <?= htmlspecialchars(
                                $userName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        <?php else: ?>

                            —

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php if (
                            $status === 'validated'
                        ): ?>

                            <?= htmlspecialchars(
                                $userName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        <?php else: ?>

                            —

                        <?php endif; ?>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $remark['remarks']
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>

            <?php

                endforeach;

            endif;

            ?>

        </tbody>

    </table>

    </div>

    <!-- ===================================== -->
    <!-- OTHER ROAD INFORMATION -->
    <!-- ===================================== -->

    <div class="card p-4">

        <div
            class="
                d-flex
                justify-content-between
                align-items-center
                mb-3
            "
        >

            <h4>
                Other Road Information
            </h4>

        </div>

        <table
            class="
                table
                table-bordered
            "
        >

            <thead>

                <tr>

                    <th>
                        Type
                    </th>

                    <th>
                        Count
                    </th>

                    <th>
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                <!-- CARRIAGEWAY -->

                <tr>

                    <td>
                        Carriageway
                    </td>

                    <td>

                        <?= isset(
                            $road['RoadCarriageway']
                        )
                        ?
                        count(
                            $road['RoadCarriageway']
                        )
                        :
                        0 ?>

                    </td>

            <td id="carriagewayAction">

                <a
                    href="
                    index.php?r=road/carriageway-list&id=<?= (string)$road['_id'] ?>
                    "
                >
                    View All
                </a>

            </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

<!-- ===================================== -->
<!-- LEAFLET -->
<!-- ===================================== -->

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

setTimeout(function() {

    // =====================================
    // MAP
    // =====================================

    const map =
        L.map('map');

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap'
        }
    ).addTo(map);

    // =====================================
    // GEOMETRY
    // =====================================

    const geometry =
    <?= json_encode($road['geometry']) ?>;

    const cleanedCoordinates =
        geometry.coordinates.map(
            coord => [

                coord[1],
                coord[0]
            ]
        );

    // FULL ROAD

    const fullRoad =
        L.polyline(
            cleanedCoordinates,
            {
                color: 'blue',
                weight: 5
            }
        ).addTo(map);

    map.fitBounds(
        fullRoad.getBounds()
    );

    setTimeout(function() {

        map.invalidateSize();

    }, 300);

}, 300);

const updateRoadBtn =
    document.getElementById(
        'updateRoadBtn'
    );

function enterEditMode() {

    document
        .querySelectorAll(
            '.road-edit'
        )
        .forEach(
            function(field){

                if (
                    field.tagName === 'SELECT'
                ) {

                    field.disabled = false;

                } else {

                    field.removeAttribute(
                        'readonly'
                    );

                }

            }
        );

    const saveRoadBtn =
        document.getElementById(
            'saveRoadBtn'
        );

    if (saveRoadBtn) {

        saveRoadBtn.style.display =
            'inline-block';

    }

    const segmentCount =
        <?= isset($road['RoadCarriageway'])
            ? count($road['RoadCarriageway'])
            : 0 ?>;

    if (segmentCount == 0) {

        document
            .getElementById(
                'carriagewayAction'
            )
            .innerHTML = `

                <a href="index.php?r=road/carriageway-list&id=<?= (string)$road['_id'] ?>">
                    View All
                </a>

                |

                <a href="index.php?r=road/carriageway&id=<?= (string)$road['_id'] ?>">
                    Add
                </a>
                

            `;

    } else {

        document
            .getElementById(
                'carriagewayAction'
            )
            .innerHTML = `

                <a href="index.php?r=road/carriageway-list&id=<?= (string)$road['_id'] ?>">
                    View All
                </a>

                |

                <a href="index.php?r=road/carriageway&id=<?= (string)$road['_id'] ?>">
                    Add
                </a>

                |

                <a href="index.php?r=road/carriageway-list&id=<?= (string)$road['_id'] ?>&edit=1">
                    Edit
                </a>

            `;

    }

}

if (updateRoadBtn) {

    updateRoadBtn.addEventListener(
        'click',
        enterEditMode
    );

}

const urlParams =
    new URLSearchParams(
        window.location.search
    );

const roadEditDraftKey =
    'road-edit-draft-<?= (string)$road["_id"] ?>';
const canEditRoadSection =
    <?= $canUpdateRoad ? 'true' : 'false' ?>;

if (
    urlParams.get('edit') === '1' && canEditRoadSection
) {

    enterEditMode();

    const savedRoadDraft =
        sessionStorage.getItem(roadEditDraftKey);

    if (savedRoadDraft) {
        try {
            const draft = JSON.parse(savedRoadDraft);
            Object.keys(draft).forEach(function(fieldId) {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.value = draft[fieldId];
                }
            });
        } catch (error) {
            console.error('Could not restore unsaved road edits:', error);
        }
    }

} else if (!canEditRoadSection) {
    sessionStorage.removeItem(roadEditDraftKey);
}

const saveRoadBtn =
    document.getElementById(
        'saveRoadBtn'
    );

function saveRoadInfoNow() {
    return fetch(
        'index.php?r=road/update-road-info&id=<?= (string)$road["_id"] ?>',
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token':
                    document
                        .querySelector(
                            'meta[name="csrf-token"]'
                        )
                        .content
            },
            body: JSON.stringify({
                R_NAME: document.getElementById('roadName').value,
                R_CLASS: document.getElementById('roadClass').value,
                R_Importan: document.getElementById('roadImp').value,
                Environmen: document.getElementById('Environmen').value,
                DirFlow: document.getElementById('DirFlow').value,
                Terrain: document.getElementById('Terrain').value,
                Length: document.getElementById('roadLength').value
            })
        }
    )
    .then(async response => {
        const text = await response.text();
        console.log(text);

        try {
            const result = JSON.parse(text);
            if (result.success) {
                return result;
            }
            throw new Error(result.message || 'Failed to update road information.');
        } catch (error) {
            console.error('Invalid JSON response:', text);
            throw new Error('Update failed. Please try again.');
        }
    });
}

if (saveRoadBtn) {

    saveRoadBtn.addEventListener(

        'click',

        function () {

            saveRoadInfoNow()
                .then(function (result) {
                    sessionStorage.removeItem(roadEditDraftKey);
                    alert(result.message || 'Road information updated successfully.');
                    window.location.reload();
                })
                .catch(function (error) {
                    console.error(error);
                    alert(error.message || 'Failed to update road information.');
                });

        }

    );

}

document.addEventListener('click', function(event) {
    const link = event.target.closest('a[href*="carriageway"]');
    if (!link) {
        return;
    }

    const draft = {};
    [
        'roadName',
        'roadClass',
        'roadImp',
        'Environmen',
        'DirFlow',
        'Terrain',
        'roadLength'
    ].forEach(function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            draft[fieldId] = field.value;
        }
    });

    sessionStorage.setItem(roadEditDraftKey, JSON.stringify(draft));
});

</script>

<style>

#map {

    width: 100%;

    height: 500px;

    border-radius: 10px;
}

label {

    color: gray;
}

.table th {

    background: #f5f5f5;
}

</style>