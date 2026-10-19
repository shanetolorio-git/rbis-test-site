<?php

$this->title = 'Carriageway Information';

$segments = $road['RoadCarriageway'] ?? [];

$editMode = isset($_GET['edit']) && $_GET['edit'] === '1';

?>

<div class="container-fluid mt-4">

    <!-- ROAD HEADER -->

    <div class="card p-4 mb-4">

        <div class="d-flex justify-content-between align-items-center">

            <h2 class="mb-0">
                <?= htmlspecialchars(
                    (string)($road['R_NAME'] ?? '')
                ) ?>
            </h2>

            <span class="badge bg-success fs-6">
                <?= strtoupper(
                    $road['status'] ?? ''
                ) ?>
            </span>

        </div>

    </div>


    <!-- CARRIAGEWAYS -->

    <div class="card p-3">

        <div class="accordion" id="carriagewayAccordion">

            <?php foreach ($segments as $index => $item): ?>

                <?php

                $segmentId =
                    (string)($item['SegmentID'] ?? '');

                $mongoSegmentId =
                isset($item['_id'])
                    ? (string)$item['_id']
                    : '';

                $accordionId =
                    'heading_' . $index;

                $collapseId =
                    'carriageway_' . $index;

                $mapId =
                    'map_' . $index;

                $geometry =
                    $item['geometry'] ?? null;

                ?>

                <div
                    class="accordion-item mb-2"
                    data-segment-id="<?= htmlspecialchars($mongoSegmentId) ?>"
                >

                    <!-- ============================= -->
                    <!-- HEADER -->
                    <!-- ============================= -->

                    <h2
                        class="accordion-header"
                        id="<?= $accordionId ?>"
                    >

                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= $collapseId ?>"
                            aria-expanded="false"
                            aria-controls="<?= $collapseId ?>"
                        >

                            <strong>
                                Carriageway:
                                <?= htmlspecialchars(
                                    $segmentId
                                ) ?>
                            </strong>

                        </button>

                    </h2>


                    <!-- ============================= -->
                    <!-- CONTENT -->
                    <!-- ============================= -->

                    <div
                        id="<?= $collapseId ?>"
                        class="accordion-collapse collapse"
                        aria-labelledby="<?= $accordionId ?>"
                        data-bs-parent="#carriagewayAccordion"
                    >

                        <div class="accordion-body">

                            <div class="row">

                                <!-- ===================== -->
                                <!-- DETAILS -->
                                <!-- ===================== -->

                                <div class="col-md-6">

                                    <div class="row g-3">

                                        <!-- SEGMENT ID -->

                                        <div class="col-md-12">

                                            <label class="form-label">
                                                Segment ID
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?= htmlspecialchars(
                                                    $segmentId
                                                ) ?>"
                                                readonly
                                            >

                                        </div>


                                        <!-- LRP START -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                LRP Start KM
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="LRPStartKm"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['LRPStartKm'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- LRP END -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                LRP End KM
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="LRPEndKm"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['LRPEndKm'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- SEGMENT LENGTH -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Segment Length
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="SegmentLen"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['SegmentLen'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- CARRIAGEWAY WIDTH -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Carriageway Width
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="CarrWidth"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['CarrWidth'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- SURFACE TYPE -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Surface Type
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="SurfaceTyp"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['SurfaceTyp'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- NUMBER OF LANES -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                No. of Lanes
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="NumLanes"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['NumLanes'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- LEFT LANE WIDTH -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Lane Width Left
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="LaneWidthL"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['LaneWidthL'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- RIGHT LANE WIDTH -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Lane Width Right
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="LaneWidthR"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['LaneWidthR'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- PAVEMENT TYPE -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Pavement Type
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="PavementTy"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['PavementTy'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- PAVEMENT THICKNESS -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Pavement Thickness
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="PavementThick"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['PavementThick'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- PAVEMENT STRENGTH -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Pavement Strength
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="PavementStrength"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['PavementStrength'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- CONSTRUCTION DATE -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Construction Date
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="ConstrucDate"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['ConstrucDate'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- CONSTRUCTION VALUE -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Construction Value
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="ConstrucValue"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['ConstrucValue'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- LAST RESURFACE -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Last Resurface Date
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="LastResurfaceDate"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['LastResurfaceDate'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- RECONSTRUCTION YEAR -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Reconstruction Year
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="ReconYear"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['ReconYear'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- CONDITION -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Condition
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="SegmentCon"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['SegmentCon'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>


                                        <!-- USEFUL LIFE -->

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Useful Life
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                data-field="UsefulLife"
                                                value="<?= htmlspecialchars(
                                                    (string)($item['UsefulLife'] ?? '')
                                                ) ?>"
                                                <?= $editMode ? '' : 'readonly' ?>
                                            >

                                        </div>

                                    </div>

                                </div>


                                <!-- ===================== -->
                                <!-- MAP -->
                                <!-- ===================== -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Segment Map
                                    </label>

                                    <div
                                        id="<?= $mapId ?>"
                                        class="segment-map"
                                        data-geometry="<?= htmlspecialchars(
                                            json_encode($geometry),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    ></div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


            <?php endforeach; ?>

        </div>

    </div>

    <?php if ($editMode): ?>

<div class="card p-4 mt-3">

    <div class="d-flex justify-content-between">

        <a
            href="index.php?r=road/view&id=<?= (string)$road['_id'] ?>"
            class="btn btn-secondary"
        >
            CANCEL
        </a>

        <button
            type="button"
            id="saveRoadInfoBtn"
            class="btn btn-success"
        >
            SAVE ROAD INFO
        </button>

    </div>

</div>

<?php endif; ?>

</div>


<!-- ========================================= -->
<!-- LEAFLET -->
<!-- ========================================= -->

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

<script
    src="https://unpkg.com/leaflet/dist/leaflet.js">
</script>


<script>

const segmentMaps = {};


<?php foreach ($segments as $index => $item): ?>

document
    .getElementById(
        '<?= '<?= $collapseId ?>' ?>'
    );


<?php endforeach; ?>


document
    .querySelectorAll(
        '.accordion-collapse'
    )
    .forEach(
        function(collapse) {

            collapse.addEventListener(
                'shown.bs.collapse',
                function() {

                    const mapId =
                        this
                            .querySelector(
                                '.segment-map'
                            )
                            .id;

                    const mapElement =
                        document.getElementById(
                            mapId
                        );

                    if (
                        segmentMaps[mapId]
                    ) {

                        segmentMaps[mapId]
                            .invalidateSize();

                        return;

                    }


                    const map =
                        L.map(mapElement);


                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        {
                            attribution:
                                '&copy; OpenStreetMap'
                        }
                    ).addTo(map);


                    const geometry =
                        JSON.parse(
                            mapElement
                                .dataset
                                .geometry || 'null'
                        );


                    if (
                        !geometry ||
                        !geometry.coordinates
                    ) {

                        map.setView(
                            [12.8797, 121.7740],
                            6
                        );

                        return;

                    }


                    let coordinates =
                        geometry.coordinates;


                    /*
                     * LineString
                     */

                    if (
                        geometry.type ===
                        'LineString'
                    ) {

                        coordinates =
                            coordinates.map(
                                function(coord) {

                                    return [
                                        coord[1],
                                        coord[0]
                                    ];

                                }
                            );

                    }


                    const polyline =
                        L.polyline(
                            coordinates,
                            {
                                color: 'red',
                                weight: 6
                            }
                        ).addTo(map);


                    map.fitBounds(
                        polyline.getBounds()
                    );


                    segmentMaps[mapId] =
                        map;


                    setTimeout(
                        function() {

                            map.invalidateSize();

                        },
                        300
                    );

                }
            );

        }
    );

</script>

<script>

const saveRoadInfoBtn =
    document.getElementById(
        'saveRoadInfoBtn'
    );

if (saveRoadInfoBtn) {

    saveRoadInfoBtn.addEventListener(
        'click',
        async function () {

            const segments = [];

            document
                .querySelectorAll('.accordion-item')
                .forEach(function (item) {

                    const segmentId =
                        item.dataset.segmentId;

                    if (!segmentId) {
                        return;
                    }

                    const fields = {};

                    item
                        .querySelectorAll('[data-field]')
                        .forEach(function (input) {

                            fields[input.dataset.field] =
                                input.value;

                        });

                    segments.push({
                        id: segmentId,
                        fields: fields
                    });

                });

            console.log(
                'Segments to save:',
                segments
            );

            try {

                const response =
                    await fetch(
                        'index.php?r=road/save-carriageway-updates&id=<?= (string)$road['_id'] ?>',
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
                                segments: segments
                            })
                        }
                    );

                const responseText =
                    await response.text();

                console.log(
                    'HTTP status:',
                    response.status
                );

                console.log(
                    'Raw server response:',
                    responseText
                );

                let result;

                try {

                    result =
                        JSON.parse(responseText);

                } catch (e) {

                    console.error(
                        'Response is not valid JSON:',
                        responseText
                    );

                    alert(
                        'Server returned an invalid response. Check the browser Console.'
                    );

                    return;
                }

                console.log(
                    'Save response:',
                    result
                );

                if (result.success) {

                    alert(
                        result.message
                    );

                    window.location.href =
                        'index.php?r=road/view&id=<?= (string)$road['_id'] ?>&edit=1';

                } else {

                    alert(
                        'Save failed: ' +
                        result.message
                    );

                }

            } catch (error) {

                console.error(
                    'Save error:',
                    error
                );

                alert(
                    'An error occurred while saving the carriageway information.'
                );

            }

        }
    );

    if (
    new URLSearchParams(
        window.location.search
    ).get('edit') === '1'
) {

    document
        .querySelectorAll(
            '.accordion-item input[data-field]'
        )
        .forEach(function(input) {

            input.removeAttribute('readonly');

        });

}

}

</script>

<style>

.segment-map {

    width: 100%;

    height: 500px;

    border-radius: 8px;

    border: 1px solid #ddd;

}

</style>