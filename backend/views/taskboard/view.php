<?php

$this->title = 'Review Road';

?>

<?php

$this->title = 'Review Road';

$status = strtolower(trim($road['status'] ?? ''));
$role = strtolower(trim(Yii::$app->user->identity->role));

$isEncoder = in_array($role, [
    'city_encoder',
    'municipal_encoder',
    'provincial_encoder'
], true);

$isSupervisor = in_array($role, [
    'city_supervisor',
    'provincial_supervisor'
], true);

$isRevision =
    $isEncoder &&
    $status === 'forrevision';

$reviewChanges =
    is_array($road['reviewChanges'] ?? null)
        ? $road['reviewChanges']
        : [];
$hasTrackedChanges =
    array_key_exists('roadSection', $reviewChanges) ||
    array_key_exists('segments', $reviewChanges);
$showRoadSection =
    $hasTrackedChanges
        ? !empty($reviewChanges['roadSection'])
        : true;
$showSegments =
    $hasTrackedChanges
        ? !empty($reviewChanges['segments'])
        : true;
$showReviewTabs =
    $showRoadSection && $showSegments;

?>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

    <div class="review-page">



    <?php if ($isRevision): ?>

    <div class="revision-card">

        <h3>
            Revise Road Information
        </h3>

        <div class="revision-grid">

            <!-- ROAD NAME -->
            <div>
                <label>
                    Road Name
                </label>

                <input
                    type="text"
                    id="revisionRoadName"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string)($road['R_NAME'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
            </div>


            <!-- ROAD CLASSIFICATION -->
            <div>
                <label>
                    Road Classification
                </label>

                <select
                id="revisionRoadClass"
                class="form-control"
            >
                
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


            <!-- ROAD IMPORTANCE -->
            <div>
                <label>
                    Road Importance
                </label>

                <select
                    id="revisionRoadImportance"
                    class="form-control"
                >

                    <option value="Core"
                        <?= ($road['R_Importan'] ?? '') === 'Core'
                            ? 'selected'
                            : '' ?>>
                        Core
                    </option>

                    <option value="Non-Core"
                        <?= ($road['R_Importan'] ?? '') === 'Non-Core'
                            ? 'selected'
                            : '' ?>>
                        Non-Core
                    </option>

                </select>
            </div>


            <!-- ENVIRONMENT -->
            <div>
                <label>
                    Environment
                </label>

                <select
                    id="revisionEnvironment"
                    class="form-control"
                >

                    <option value="Urban"
                        <?= ($road['Environmen'] ?? '') === 'Urban'
                            ? 'selected'
                            : '' ?>>
                        Urban
                    </option>

                    <option value="Rural"
                        <?= ($road['Environmen'] ?? '') === 'Rural'
                            ? 'selected'
                            : '' ?>>
                        Rural
                    </option>

                </select>
            </div>


            <!-- DIRECTION OF FLOW -->
            <div>
                <label>
                    Direction of Flow
                </label>

                <select
                    id="revisionDirFlow"
                    class="form-control"
                >

                    <option value="One-way"
                        <?= ($road['DirFlow'] ?? '') === 'One-way'
                            ? 'selected'
                            : '' ?>>
                        One-way
                    </option>

                    <option value="Two-way"
                        <?= ($road['DirFlow'] ?? '') === 'Two-way'
                            ? 'selected'
                            : '' ?>>
                        Two-way
                    </option>

                </select>
            </div>


            <!-- TERRAIN -->
            <div>
                <label>
                    Terrain
                </label>

                <select
                    id="revisionTerrain"
                    class="form-control"
                >

                    <option value="Flat"
                        <?= ($road['Terrain'] ?? '') === 'Flat'
                            ? 'selected'
                            : '' ?>>
                        Flat
                    </option>

                    <option value="Rolling"
                        <?= ($road['Terrain'] ?? '') === 'Rolling'
                            ? 'selected'
                            : '' ?>>
                        Rolling
                    </option>

                    <option value="Mountainous"
                        <?= ($road['Terrain'] ?? '') === 'Mountainous'
                            ? 'selected'
                            : '' ?>>
                        Mountainous
                    </option>

                </select>
            </div>


            <!-- LENGTH -->
            <div>
                <label>
                    Length
                </label>

                <input
                    type="text"
                    id="revisionLength"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        (string)($road['Length'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
            </div>

        </div>


        <button
            type="button"
            id="submitRevisionBtn"
            class="btn btn-warning revision-submit-btn"
        >
            SUBMIT REVISION FOR REVIEW
        </button>

    </div>


    <?php endif; ?>

    <div id="map"></div>

    <?php if ($showReviewTabs): ?>
        <div class="review-tabs" role="tablist" aria-label="Updated road information">
            <button
                type="button"
                class="review-tab active"
                id="roadSectionTab"
                role="tab"
                aria-selected="true"
                aria-controls="roadSectionPanel"
                data-review-tab="roadSectionPanel"
            >
                Road Section Details
            </button>
            <button
                type="button"
                class="review-tab"
                id="segmentsTab"
                role="tab"
                aria-selected="false"
                aria-controls="segmentsPanel"
                data-review-tab="segmentsPanel"
            >
                Road Segments
            </button>
        </div>
    <?php endif; ?>

    <?php if ($showRoadSection): ?>
        <section
            class="review-details-card<?= $showReviewTabs ? ' review-tab-panel' : '' ?>"
            id="roadSectionPanel"
            <?= $showReviewTabs ? 'role="tabpanel" aria-labelledby="roadSectionTab"' : '' ?>
        >
            <h3>Road Section Details</h3>
            <div class="road-details-grid">
                <?php foreach ([
                    'Road Name' => 'R_NAME',
                    'Road Classification' => 'R_CLASS',
                    'Road Importance' => 'R_Importan',
                    'Environment' => 'Environmen',
                    'Direction of Flow' => 'DirFlow',
                    'Terrain' => 'Terrain',
                    'Length' => 'Length',
                    'Right-of-Way Acquisition Date' => 'RROW_acq_date',
                    'Right-of-Way Acquisition Cost' => 'RROW_acq_cost',
                ] as $label => $field): ?>
                    <div class="road-detail">
                        <strong><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></strong>
                        <span><?= htmlspecialchars(
                            (string)($road[$field] ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?: '-' ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showSegments): ?>
        <section
            class="segments-card<?= $showReviewTabs ? ' review-tab-panel' : '' ?>"
            id="segmentsPanel"
            <?= $showReviewTabs ? 'role="tabpanel" aria-labelledby="segmentsTab" hidden' : '' ?>
        >
            <h3>Road Segments</h3>
            <?php $segments = $road['RoadCarriageway'] ?? []; ?>

            <?php if (count($segments) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Segment ID</th>
                                <th>LRP Start Km</th>
                                <th>LRP End Km</th>
                                <th>Segment Length</th>
                                <th>Surface Type</th>
                                <th>No. of Lanes</th>
                                <th>Condition</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($segments as $index => $segment): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars((string)($segment['SegmentID'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($segment['LRPStartKm'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($segment['LRPEndKm'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($segment['SegmentLen'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($segment['SurfaceTyp'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($segment['NumLanes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($segment['SegmentCon'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No road segments recorded.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?= $this->render('@backend/views/shared/_audit-trail', [
        'road' => $road
    ]) ?>

    <!-- ====================================
         SUPERVISOR REVIEW
    ==================================== -->
<?php if ($isSupervisor): ?>
    <div class="remarks-card">

        <h3>
            Supervisor Review
        </h3>

        <label>
            Supervisor
        </label>

        <select
            id="reviewStatus"
            class="form-select"
        >

            <option value="validated">
                Validate
            </option>

            <option value="rejected">
                Reject
            </option>

            <option value="forrevision">
                Return for Revision
            </option>

        </select>

        <label class="remarks-label">
            Supervisor Remarks
        </label>

        <textarea
            id="remarks"
            class="form-control remarks-box"
            placeholder="Enter remarks..."
        ></textarea>

        <button
            class="btn btn-success submit-btn"
            id="submitReviewBtn"
        >

            SUBMIT

        </button>

    </div>
<?php endif; ?>    

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

setTimeout(function() {

    // ====================================
    // MAP
    // ====================================

    const map =
        L.map('map');

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution:
                '&copy; OpenStreetMap'
        }
    ).addTo(map);


    // ====================================
    // MAIN ROAD GEOMETRY
    // ====================================

    const geometry =
        <?= json_encode(
            $road['geometry']
            ?? null
        ) ?>;


    let allLayers = [];


    if (
        geometry &&
        geometry.coordinates
    ) {

        const cleanedCoordinates =
            geometry.coordinates.map(
                coord => [
                    coord[1],
                    coord[0]
                ]
            );


        const polyline =
            L.polyline(
                cleanedCoordinates,
                {
                    color: 'red',
                    weight: 5
                }
            ).addTo(map);


        polyline.bindPopup(
            '<b>Main Road Section</b>'
        );


        allLayers.push(
            polyline
        );

    }


    // ====================================
    // ROAD SEGMENTS
    // ====================================

    const segments =
        <?= json_encode(
            $road['RoadCarriageway']
            ?? []
        ) ?>;


    segments.forEach(
        function(segment, index) {

            const segmentGeometry =
                segment.geometry
                ?? null;


            if (
                !segmentGeometry ||
                !segmentGeometry.coordinates
            ) {

                return;

            }


            let segmentLayer;


            // ====================================
            // LINESTRING
            // ====================================

            if (
                segmentGeometry.type ===
                'LineString'
            ) {

                const coordinates =
                    segmentGeometry.coordinates.map(
                        coord => [
                            coord[1],
                            coord[0]
                        ]
                    );


                segmentLayer =
                    L.polyline(
                        coordinates,
                        {
                            color: 'blue',
                            weight: 7
                        }
                    );

            }


            // ====================================
            // MULTILINESTRING
            // ====================================

            else if (
                segmentGeometry.type ===
                'MultiLineString'
            ) {

                const coordinates =
                    segmentGeometry.coordinates.map(
                        line =>
                            line.map(
                                coord => [
                                    coord[1],
                                    coord[0]
                                ]
                            )
                    );


                segmentLayer =
                    L.polyline(
                        coordinates,
                        {
                            color: 'blue',
                            weight: 7
                        }
                    );

            }


            if (!segmentLayer) {

                return;

            }


            segmentLayer.addTo(map);


            segmentLayer.bindPopup(

                '<b>Road Segment</b>' +
                '<br>' +
                'Segment ID: ' +
                (
                    segment.SegmentID
                    ?? ''
                ) +
                '<br>' +
                'LRP Start: ' +
                (
                    segment.LRPStartKm
                    ?? ''
                ) +
                '<br>' +
                'LRP End: ' +
                (
                    segment.LRPEndKm
                    ?? ''
                ) +
                '<br>' +
                'Length: ' +
                (
                    segment.SegmentLen
                    ?? ''
                )

            );


            allLayers.push(
                segmentLayer
            );

        }
    );


    // ====================================
    // FIT MAP TO ALL DATA
    // ====================================

    if (
        allLayers.length > 0
    ) {

        const group =
            L.featureGroup(
                allLayers
            );

        map.fitBounds(
            group.getBounds()
        );

    }


    // ====================================
    // FIX MAP SIZE
    // ====================================

    setTimeout(
        function() {

            map.invalidateSize();

        },
        300
    );

}, 300);

</script>

<script>

// ====================================
// SUPERVISOR DECISION BUTTON
// ====================================

<?php if ($isSupervisor): ?>
document
    .getElementById(
        'reviewStatus'
    )
    .addEventListener(
        'change',
        function() {

            const button =
                document.getElementById(
                    'submitReviewBtn'
                );

            if (
                this.value ===
                'validated'
            ) {

                button.textContent =
                    'APPROVE & VALIDATE';

                button.className =
                    'btn btn-success submit-btn';

            }

            else if (
                this.value ===
                'rejected'
            ) {

                button.textContent =
                    'REJECT';

                button.className =
                    'btn btn-danger submit-btn';

            }

            else if (
                this.value ===
                'forrevision'
            ) {

                button.textContent =
                    'RETURN FOR REVISION';

                button.className =
                    'btn btn-warning submit-btn';

            }

        }
    );
<?php endif; ?>

const reviewTabs =
    document.querySelectorAll(
        '[data-review-tab]'
    );

reviewTabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
        reviewTabs.forEach(function(item) {
            item.classList.remove('active');
            item.setAttribute('aria-selected', 'false');

            const panel = document.getElementById(
                item.getAttribute('data-review-tab')
            );
            panel.hidden = true;
        });

        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');
        document.getElementById(
            tab.getAttribute('data-review-tab')
        ).hidden = false;
    });
});


// ====================================
// SUBMIT REVIEW
// ====================================

document
    .getElementById(
        'submitReviewBtn'
    )
    .addEventListener(
        'click',
        function() {

            const status =
                document
                    .getElementById(
                        'reviewStatus'
                    )
                    .value;

            const remarks =
                document
                    .getElementById(
                        'remarks'
                    )
                    .value;


            fetch(
                'index.php?r=taskboard/review&id=<?= trim((string)$road["_id"]) ?>',
                {

                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'X-CSRF-Token':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute(
                                    'content'
                                )

                    },

                    body:
                        JSON.stringify({

                            status:
                                status,

                            remarks:
                                remarks

                        })

                }
            )

            .then(
                response =>
                    response.json()
            )

            .then(
                data => {

                    if (
                        data.success
                    ) {

                        alert(
                            'Review submitted successfully.'
                        );

                        location.href =
                            'index.php?r=taskboard/index';

                    }

                    else {

                        alert(
                            data.message ||
                            'Save failed.'
                        );

                    }

                }
            )

            .catch(
                error => {

                    console.log(
                        error
                    );

                    alert(
                        'An error occurred while submitting the review.'
                    );

                }
            );

        }
    );

</script>

<script>

<?php if ($isRevision): ?>

document
    .getElementById('submitRevisionBtn')
    .addEventListener(
        'click',
        function () {

            const button =
                document.getElementById(
                    'submitRevisionBtn'
                );

            button.disabled = true;

            button.textContent =
                'SUBMITTING...';

            fetch(
                'index.php?r=road/update-road-info&id=<?= (string)$road['_id'] ?>',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-Token':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute(
                                    'content'
                                )
                    },

                    body: JSON.stringify({

                        R_NAME:
                            document
                                .getElementById(
                                    'revisionRoadName'
                                )
                                .value,

                        R_CLASS:
                            document
                                .getElementById(
                                    'revisionRoadClass'
                                )
                                .value,

                        R_Importan:
                            document
                                .getElementById(
                                    'revisionRoadImportance'
                                )
                                .value,

                        Environmen:
                            document
                                .getElementById(
                                    'revisionEnvironment'
                                )
                                .value,

                        DirFlow:
                            document
                                .getElementById(
                                    'revisionDirFlow'
                                )
                                .value,

                        Terrain:
                            document
                                .getElementById(
                                    'revisionTerrain'
                                )
                                .value,

                        Length:
                            document
                                .getElementById(
                                    'revisionLength'
                                )
                                .value

                    })
                }
            )

            .then(
                response =>
                    response.json()
            )

            .then(
                data => {

                    if (data.success) {

                        alert(
                            'Revision submitted successfully.'
                        );

                        window.location.href =
                            'index.php?r=taskboard/index';

                    } else {

                        alert(
                            data.message ||
                            'Revision submission failed.'
                        );

                        button.disabled = false;

                        button.textContent =
                            'SUBMIT REVISION FOR REVIEW';

                    }

                }
            )

            .catch(
                error => {

                    console.error(error);

                    alert(
                        'An error occurred while submitting the revision.'
                    );

                    button.disabled = false;

                    button.textContent =
                        'SUBMIT REVISION FOR REVIEW';

                }
            );

        }
    );

<?php endif; ?>

</script>

<style>

.review-page {
    padding: 20px;
}

.review-card,
.remarks-card {

    background: white;

    padding: 25px;

    border-radius: 10px;

    margin-bottom: 20px;
}

.review-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;
}

#map {

    width: 100%;

    height: 500px;

    border-radius: 10px;

    margin-bottom: 20px;
}

.remarks-box {

    margin-top: 20px;

    min-height: 120px;
}

.submit-btn {

    margin-top: 20px;
}

.remarks-label {

    display: block;

    margin-top: 20px;

}

.segments-card {

    background: white;

    padding: 25px;

    border-radius: 10px;

    margin-bottom: 20px;
}

.review-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 16px;
}

.review-tab {
    background: transparent;
    border: 0;
    border-bottom: 3px solid transparent;
    color: #555;
    cursor: pointer;
    font-weight: 600;
    padding: 12px 16px;
}

.review-tab.active {
    border-bottom-color: #2f80ed;
    color: #2f80ed;
}

.review-details-card {
    background: white;
    border-radius: 10px;
    margin-bottom: 20px;
    padding: 25px;
}

.road-details-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0 24px;
}

.road-detail {
    border-bottom: 1px solid #eee;
    display: flex;
    gap: 16px;
    justify-content: space-between;
    padding: 12px 0;
}

.road-detail strong {
    color: #555;
}

.road-detail span {
    text-align: right;
}

.review-tab-panel[hidden] {
    display: none;
}

.segments-card h3 {

    margin-bottom: 20px;
}

.segments-card table {

    width: 100%;
}

.segments-card th {

    background: #f5f5f5;

    white-space: nowrap;
}

.segments-card td {

    vertical-align: middle;
}

.revision-card {

    background: white;

    padding: 25px;

    border-radius: 10px;

    margin-bottom: 20px;

}

.revision-card h3 {

    margin-bottom: 20px;

}

.revision-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;

}

.revision-grid label {

    display: block;

    margin-bottom: 8px;

    color: gray;

}

.revision-submit-btn {

    margin-top: 25px;

}

@media (max-width: 700px) {
    .road-details-grid {
        grid-template-columns: 1fr;
    }

    .road-detail {
        align-items: flex-start;
        flex-direction: column;
        gap: 4px;
    }

    .road-detail span {
        text-align: left;
    }
}

</style>