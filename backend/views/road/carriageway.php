<?php

$this->title =
    'Road Carriageway';

$isEditingSegment = !empty($selectedSegment);
$segments = $segments ?? ($road['RoadCarriageway'] ?? []);
$selectedSegmentId = isset($selectedSegment['_id'])
    ? (string)$selectedSegment['_id']
    : (string)Yii::$app->request->get('segment', '');

?>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

<style>
/* Modern road-splitting console */
.split-console {
    margin-bottom: 14px;
}

.split-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
}

.split-toolbar .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 15px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.split-toolbar .btn:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.10);
}

.split-toolbar .btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.split-instructions {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 12px;
    margin-bottom: 14px;
    padding: 14px 16px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-left: 4px solid #2563eb;
    border-radius: 10px;
    color: #1e40af;
    font-size: 13px;
    line-height: 1.6;
}

.split-instructions::before {
    content: "ⓘ";
    font-size: 17px;
    font-weight: 700;
    flex-shrink: 0;
}

#map {
    overflow: hidden;
    border: 1px solid #dbe3ed;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(15, 23, 42, 0.08);
}

@media (max-width: 576px) {
    .split-toolbar .btn {
        flex: 1 1 calc(50% - 10px);
        padding: 9px;
    }
}


.map-editor-container {
    position: relative;
    width: 100%;
    overflow: hidden;
    border-radius: 14px;
    isolation: isolate;
}

.map-editor-container #map {
    position: relative;
    z-index: 1;
    border: 1px solid #dbe3ed;
    border-radius: 14px;
}

.map-editor-console {
    position: absolute;
    top: 14px;
    left: 58px;
    z-index: 1000;
    width: min(560px, calc(100% - 76px));
    padding: 15px;
    background: rgba(255, 255, 255, 0.97);
    border: 1px solid #dce3ed;
    border-radius: 14px;
    box-shadow: 0 8px 28px rgba(15, 23, 42, 0.18);
    backdrop-filter: blur(10px);
}

.map-editor-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 13px;
}

.map-editor-title {
    font-size: 15px;
    font-weight: 700;
    color: #172033;
}

.map-editor-subtitle {
    margin-top: 3px;
    font-size: 12px;
    color: #64748b;
}

.map-editor-badge {
    padding: 5px 8px;
    border-radius: 6px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.map-editor-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
}

.map-editor-toolbar .btn {
    min-height: 38px;
    padding: 8px 11px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.map-editor-toolbar .btn:disabled {
    opacity: 0.45;
}

.map-editor-status {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-top: 12px;
    padding: 10px 11px;
    border: 1px solid #dbeafe;
    border-radius: 8px;
    background: #eff6ff;
    color: #1e40af;
    font-size: 12px;
    line-height: 1.5;
}

.map-status-dot {
    flex: 0 0 8px;
    width: 8px;
    height: 8px;
    margin-top: 4px;
    border-radius: 50%;
    background: #3b82f6;
}

@media (max-width: 576px) {
    .map-editor-console {
        top: 10px;
        left: 48px;
        width: calc(100% - 58px);
        padding: 10px;
    }

    .map-editor-toolbar .btn {
        flex: 1 1 calc(50% - 7px);
    }

    .map-editor-badge {
        display: none;
    }
}

</style>


<div class="container-fluid mt-4">

    <div class="card p-4">

        <div class="d-flex justify-content-between">

            <h3>

                <?= $road['R_NAME'] ?>

            </h3>

        </div>

        <hr>

        <div class="row">

            
            <div class="col-md-12">
            
            <div class="map-editor-container">

                <div id="map"
                    style="width:100%; height:700px;">
                </div>

                <div class="map-editor-console">

                        <div class="map-editor-status">
                            <span class="map-status-dot"></span>

                            <div id="splitInstructions">
                                Select "Split Road" to begin placing split points
                                on the existing road line.
                            </div>
                        </div>

                    <div class="map-editor-heading">
                        <div>
                            <div class="map-editor-title">Road Segment Editor</div>
                            <div class="map-editor-subtitle">
                                Split and preview road geometry
                            </div>
                        </div>
                        <span class="map-editor-badge">GIS TOOLS</span>
                    </div>

                    <div class="map-editor-toolbar">
                        <button type="button"
                                class="btn btn-primary"
                                id="startSplitBtn">
                            <span aria-hidden="true">✂</span>
                            Split Road
                        </button>

                        <button type="button"
                                class="btn btn-outline-secondary"
                                id="undoSplitBtn"
                                disabled>
                            ↶ Undo
                        </button>

                        <button type="button"
                                class="btn btn-success"
                                id="previewSplitBtn"
                                disabled>
                            ◎ Preview
                        </button>

                        <button type="button"
                                class="btn btn-outline-danger"
                                id="cancelSplitBtn"
                                disabled>
                            × Cancel
                        </button>
                    </div>

                </div>
            </div>

            </div>

        </div>

    </div>

    

    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">New Carriageway Segments</h4>
                <small class="text-muted">
                    Draw, review, and add the new segments to this road.
                </small>
            </div>
            <span class="badge bg-primary" id="revisionQueueCount">
                0 segments
            </span>
        </div>

        <div class="alert alert-warning" id="revisionStatus">
            No segments have been queued.
        </div>

        <div
            id="revisionLengthSummary"
            class="alert alert-secondary mb-3"
        >
            <div class="d-flex flex-wrap gap-4">
                <div>
                    <small class="text-muted">
                        Road Section Length
                    </small>
                    <div class="fw-bold">
                        <span id="roadSectionLengthDisplay">0.000</span> km
                    </div>
                </div>

                <div>
                    <small class="text-muted">
                        Total Segment Length
                    </small>
                    <div class="fw-bold">
                        <span id="totalSegmentLengthDisplay">0.000</span> km
                    </div>
                </div>

                <div>
                    <small class="text-muted">
                        Difference
                    </small>
                    <div class="fw-bold">
                        <span id="segmentLengthDifferenceDisplay">0.000</span> km
                    </div>
                </div>
            </div>

            <div
                id="segmentLengthValidationMessage"
                class="mt-2"
            ></div>
        </div>

        <div class="accordion mb-3" id="revisionQueueAccordion">
            <div class="text-center text-muted p-4 border rounded">
                No segments added yet.
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button
                type="button"
                class="btn btn-outline-primary"
                id="addSegmentToQueueBtn"
                disabled>
                Add Segment to Queue
            </button>

            <button
                type="button"
                class="btn btn-success"
                id="saveAllNewSegmentsBtn"
                disabled>
                Save All New Segments
            </button>
        

            <button
                type="button"
                class="btn btn-outline-secondary"
                id="clearRevisionQueueBtn"
                disabled>
                Clear Queue
            </button>
        </div>
    </div>


</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

const carriagewayData = <?= json_encode($selectedSegment ?? null) ?>;

const roadSectionLength =
    Number(
        <?= json_encode($road['Length'] ?? 0) ?>
    );

let geometryData = carriagewayData ? carriagewayData.geometry : null;

if (carriagewayData) {
    const fieldValues = {
        lrpStartKm: carriagewayData.LRPStartKm,
        lrpEndKm: carriagewayData.LRPEndKm,
        segmentLength: carriagewayData.SegmentLen,
        surfaceType: carriagewayData.SurfaceTyp,
        pavementType: carriagewayData.PavementTy,
        numLanes: carriagewayData.NumLanes,
        laneWidthLeft: carriagewayData.LaneWidthL,
        laneWidthRight: carriagewayData.LaneWidthR,
        condition: carriagewayData.SegmentCon,
        carriagewayWidth: carriagewayData.CarrWidth,
        pavementThickness: carriagewayData.PavementThick,
        pavementStrength: carriagewayData.PavementStrength,
        constructionDate: carriagewayData.ConstrucDate,
        constructionValue: carriagewayData.ConstrucValue,
        lastResurfaceDate: carriagewayData.LastResurfaceDate,
        reconstructionYear: carriagewayData.ReconYear,
        usefulLife: carriagewayData.UsefulLife
    };

    Object.keys(fieldValues).forEach(function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field && fieldValues[fieldId] !== undefined && fieldValues[fieldId] !== null) {
            field.value = fieldValues[fieldId];
        }
    });
}

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
// MAIN ROAD
// ====================================

const uploadedRoadGeometry =
<?= json_encode($road['geometry']) ?>;

const mainRoad = uploadedRoadGeometry.type === 'FeatureCollection'
    ? uploadedRoadGeometry.features[0].geometry
    : uploadedRoadGeometry.type === 'Feature'
        ? uploadedRoadGeometry.geometry
        : uploadedRoadGeometry;

const mainCoordinates =
    mainRoad.coordinates.map(
        coord => [

            coord[1],
            coord[0]
        ]
    );

    
let splitModeActive = false;
let splitMarkers = [];
let splitPreviewLayers = [];

const splitInstructions = document.getElementById('splitInstructions');

const previewSplitBtn = document.getElementById('previewSplitBtn');

let generatedSplitSegments = [];


const revisionQueue = [];
let selectedSplitIndex = null;

const revisionQueueBody =
    document.getElementById('revisionQueueBody');

const revisionStatus =
    document.getElementById('revisionStatus');

const addSegmentToQueueBtn =
    document.getElementById('addSegmentToQueueBtn');

const saveAllNewSegmentsBtn =
    document.getElementById('saveAllNewSegmentsBtn');

const clearRevisionQueueBtn =
    document.getElementById('clearRevisionQueueBtn');

function getCurrentSegmentFields() {
    return {
        LRPStartKm: '',
        LRPEndKm: '',
        SegmentLen: '',
        CarrWidth: '',
        SurfaceTyp: '',
        NumLanes: '',
        LaneWidthL: '',
        LaneWidthR: '',
        PavementTy: '',
        PavementThick: '',
        PavementStrength: '',
        ConstrucDate: '',
        ConstrucValue: '',
        LastResurfaceDate: '',
        ReconYear: '',
        SegmentCon: '',
        UsefulLife: ''
    };
}

function validateRevisionLengths() {

    const totalSegmentLength =
        revisionQueue.reduce(function(total, item) {

            const length =
                Number(item.fields.SegmentLen);

            return total +
                (Number.isFinite(length) ? length : 0);

        }, 0);

    const difference =
        totalSegmentLength - roadSectionLength;

    const tolerance = 0.001;

    const isValid =
        roadSectionLength > 0 &&
        Math.abs(difference) <= tolerance;

    document.getElementById(
        'roadSectionLengthDisplay'
    ).textContent =
        roadSectionLength.toFixed(3);

    document.getElementById(
        'totalSegmentLengthDisplay'
    ).textContent =
        totalSegmentLength.toFixed(3);

    document.getElementById(
        'segmentLengthDifferenceDisplay'
    ).textContent =
        difference.toFixed(3);

    const message =
        document.getElementById(
            'segmentLengthValidationMessage'
        );

    if (!roadSectionLength) {

        message.className =
            'mt-2 text-danger fw-semibold';

        message.textContent =
            'Road section length is not available.';

        saveAllNewSegmentsBtn.disabled = true;

        return false;
    }

    if (revisionQueue.length === 0) {

        message.className =
            'mt-2 text-muted';

        message.textContent =
            'No new segments have been queued.';

        saveAllNewSegmentsBtn.disabled = true;

        return false;
    }

    if (isValid) {

        message.className =
            'mt-2 text-success fw-semibold';

        message.textContent =
            '✓ Total segment length matches the road section length.';

        saveAllNewSegmentsBtn.disabled = false;

        return true;
    }

    message.className =
        'mt-2 text-danger fw-semibold';

    if (difference < 0) {

        message.textContent =
            '✕ Total segment length is ' +
            Math.abs(difference).toFixed(3) +
            ' km shorter than the road section length.';

    } else {

        message.textContent =
            '✕ Total segment length is ' +
            difference.toFixed(3) +
            ' km longer than the road section length.';
    }

    saveAllNewSegmentsBtn.disabled = true;

    return false;
}


let revisionQueueMaps = [];

function renderRevisionQueue() {
    // Remove maps created by the previous render.
    revisionQueueMaps.forEach(function (miniMap) {
        miniMap.remove();
    });
    revisionQueueMaps = [];

    const accordion = document.getElementById('revisionQueueAccordion');
    accordion.replaceChildren();

    document.getElementById('revisionQueueCount').textContent =
        revisionQueue.length + ' segment(s)';

    if (revisionQueue.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'text-center text-muted p-4 border rounded';
        empty.textContent = 'No segments added yet.';
        accordion.appendChild(empty);
    }

    const fieldLabels = {
        LRPStartKm: 'LRP Start KM',
        LRPEndKm: 'LRP End KM',
        SegmentLen: 'Segment Length (km)',
        CarrWidth: 'Carriageway Width (m)',
        SurfaceTyp: 'Surface Type',
        NumLanes: 'Number of Lanes',
        LaneWidthL: 'Lane Width Left (m)',
        LaneWidthR: 'Lane Width Right (m)',
        PavementTy: 'Pavement Type',
        PavementThick: 'Pavement Thickness',
        PavementStrength: 'Pavement Strength',
        ConstrucDate: 'Construction Date',
        ConstrucValue: 'Construction Value',
        LastResurfaceDate: 'Last Resurface Date',
        ReconYear: 'Reconstruction Year',
        SegmentCon: 'Condition',
        UsefulLife: 'Useful Life'
    };

    revisionQueue.forEach(function (item, index) {
        const itemId = 'revision-segment-' + index;
        const headingId = itemId + '-heading';
        const collapseId = itemId + '-details';

        const card = document.createElement('div');
        card.className = 'accordion-item mb-2 border rounded';

        const header = document.createElement('h2');
        header.className = 'accordion-header';
        header.id = headingId;

        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'accordion-button collapsed fw-bold';
        toggle.textContent =
            'Proposed Segment ' + item.segmentNumber +
            ' — ' + Number(item.fields.SegmentLen || 0).toFixed(3) +
            ' km';

        toggle.setAttribute('data-bs-toggle', 'collapse');
        toggle.setAttribute('data-bs-target', '#' + collapseId);
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-controls', collapseId);

        header.appendChild(toggle);

        const collapse = document.createElement('div');
        collapse.id = collapseId;
        collapse.className = 'accordion-collapse collapse';
        collapse.setAttribute('aria-labelledby', headingId);
        collapse.setAttribute('data-bs-parent', '#revisionQueueAccordion');

        const body = document.createElement('div');
        body.className = 'accordion-body';

        const row = document.createElement('div');
        row.className = 'row g-4';

        // All segment attributes.
        const detailsColumn = document.createElement('div');
        detailsColumn.className = 'col-lg-6';

        const detailsGrid = document.createElement('div');
        detailsGrid.className = 'row g-3';

        Object.entries(fieldLabels).forEach(function ([key, label]) {
            const fieldColumn = document.createElement('div');
            fieldColumn.className = 'col-sm-6';

            const fieldLabel = document.createElement('div');
            fieldLabel.className = 'small text-muted mb-1';
            fieldLabel.textContent = label;

            const fieldValue = document.createElement('div');
            fieldValue.className = 'fw-semibold text-break';
            
            const displayLabels = {
                SurfaceTyp: {
                    C: 'Concrete',
                    A: 'Asphalt',
                    G: 'Gravel',
                    E: 'Earth'
                },
                SegmentCon: {
                    G: 'Good',
                    F: 'Fair',
                    P: 'Poor',
                    B: 'Bad'
                }
            };

            const rawValue = item.fields[key];

            fieldValue.textContent =
                rawValue === undefined || rawValue === null || rawValue === ''
                    ? '—'
                    : displayLabels[key]?.[String(rawValue)] ?? String(rawValue);


            fieldColumn.appendChild(fieldLabel);
            fieldColumn.appendChild(fieldValue);
            detailsGrid.appendChild(fieldColumn);
        });

        detailsColumn.appendChild(detailsGrid);

        // Geometry preview.
        const mapColumn = document.createElement('div');
        mapColumn.className = 'col-lg-6';

        const mapLabel = document.createElement('div');
        mapLabel.className = 'fw-semibold mb-2';
        mapLabel.textContent = 'Segment Geometry';

        const mapElement = document.createElement('div');
        mapElement.id = itemId + '-map';
        mapElement.style.width = '100%';
        mapElement.style.height = '260px';
        mapElement.style.background = '#eeeeee';
        mapElement.style.borderRadius = '6px';

        mapColumn.appendChild(mapLabel);
        mapColumn.appendChild(mapElement);

        row.appendChild(detailsColumn);
        row.appendChild(mapColumn);

        // Edit and remove actions.
        const actions = document.createElement('div');
        actions.className = 'd-flex flex-wrap gap-2 mt-4';

        const editButton = document.createElement('button');
editButton.type = 'button';
editButton.className = 'btn btn-outline-primary btn-sm';
editButton.textContent = 'Edit Details';

const editPanel = document.createElement('div');
editPanel.className = 'border rounded p-3 mt-3 bg-light';
editPanel.style.display = 'none';

const editTitle = document.createElement('div');
editTitle.className = 'fw-semibold mb-3';
editTitle.textContent =
    'Edit Segment ' + item.segmentNumber + ' Details';

editPanel.appendChild(editTitle);

const editGrid = document.createElement('div');
editGrid.className = 'row g-3';

const editDefinitions = [
    ['LRPStartKm', 'LRP Start KM', 'number'],
    ['LRPEndKm', 'LRP End KM', 'number'],
    ['SegmentLen', 'Segment Length (km)', 'number'],
    ['CarrWidth', 'Carriageway Width (m)', 'number'],

    [
        'SurfaceTyp',
        'Surface Type',
        'select',
        [
            ['C', 'Concrete'],
            ['A', 'Asphalt'],
            ['G', 'Gravel'],
            ['E', 'Earth']
        ]
    ],

    ['NumLanes', 'Number of Lanes', 'number'],
    ['LaneWidthL', 'Lane Width Left (m)', 'number'],
    ['LaneWidthR', 'Lane Width Right (m)', 'number'],
    ['PavementTy', 'Pavement Type', 'text'],
    ['PavementThick', 'Pavement Thickness', 'number'],
    ['PavementStrength', 'Pavement Strength', 'number'],
    ['ConstrucDate', 'Construction Date', 'date'],
    ['ConstrucValue', 'Construction Value', 'number'],
    ['LastResurfaceDate', 'Last Resurface Date', 'date'],
    ['ReconYear', 'Reconstruction Year', 'number'],

    [
        'SegmentCon',
        'Condition',
        'select',
        [
            ['G', 'Good'],
            ['F', 'Fair'],
            ['P', 'Poor'],
            ['B', 'Bad']
        ]
    ],

    ['UsefulLife', 'Useful Life', 'number']
];

const editInputs = {};

editDefinitions.forEach(function (definition) {

    const key = definition[0];
    const label = definition[1];
    const type = definition[2];
    const options = definition[3] || [];

    const col = document.createElement('div');
    col.className = 'col-md-6';

    const wrapper = document.createElement('div');
    wrapper.className = 'mb-1';

    const labelEl = document.createElement('label');
    labelEl.className = 'form-label';
    labelEl.textContent = label;

    let input;

    if (type === 'select') {

        input = document.createElement('select');
        input.className = 'form-control';

        options.forEach(function (option) {

            const optionEl = document.createElement('option');

            optionEl.value = option[0];
            optionEl.textContent = option[1];

            input.appendChild(optionEl);
        });

    } else {

        input = document.createElement('input');

        input.type = type;
        input.className = 'form-control';

        if (type === 'number') {
            input.step = 'any';
        }
    }

    input.value = item.fields[key] ?? '';

    editInputs[key] = input;

    wrapper.appendChild(labelEl);
    wrapper.appendChild(input);

    col.appendChild(wrapper);
    editGrid.appendChild(col);
});

editPanel.appendChild(editGrid);

        const editActions = document.createElement('div');
        editActions.className = 'd-flex gap-2 mt-3';

        const saveEditButton = document.createElement('button');
        saveEditButton.type = 'button';
        saveEditButton.className = 'btn btn-primary btn-sm';
        saveEditButton.textContent = 'Update Segment';

        const cancelEditButton = document.createElement('button');
        cancelEditButton.type = 'button';
        cancelEditButton.className =
            'btn btn-outline-secondary btn-sm';
        cancelEditButton.textContent = 'Cancel';

        editActions.appendChild(saveEditButton);
        editActions.appendChild(cancelEditButton);

        editPanel.appendChild(editActions);

        editButton.addEventListener('click', function () {

            editPanel.style.display = '';

            editButton.style.display = 'none';
            removeButton.style.display = 'none';

            revisionStatus.textContent =
                'Editing Segment ' + item.segmentNumber +
                ' inside the revised-segment queue. ' +
                'The segment geometry will remain unchanged.';

            revisionStatus.className = 'alert alert-info';
        });

        cancelEditButton.addEventListener('click', function () {

            editPanel.style.display = 'none';

            editButton.style.display = '';
            removeButton.style.display = '';

            revisionStatus.textContent =
                'Editing cancelled. No changes were made to Segment ' +
                item.segmentNumber + '.';
        });

        saveEditButton.addEventListener('click', function () {

            const updatedFields = {};

            editDefinitions.forEach(function (definition) {

                const key = definition[0];

                updatedFields[key] =
                    editInputs[key].value;
            });

            const segmentLength =
                parseFloat(updatedFields.SegmentLen);

            if (
                !Number.isFinite(segmentLength) ||
                segmentLength <= 0
            ) {

                alert(
                    'Enter a valid Segment Length greater than 0 km.'
                );

                editInputs.SegmentLen.focus();

                return;
            }

            /*
            * IMPORTANT:
            * Only update the attributes.
            * DO NOT modify item.geometry.
            */
            item.fields = updatedFields;

            renderRevisionQueue();

            revisionStatus.textContent =
                'Segment ' + item.segmentNumber +
                ' updated in the queue. Nothing has been saved yet.';

            revisionStatus.className = 'alert alert-info';
        });

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-outline-danger btn-sm';
        removeButton.textContent = 'Remove Segment';

        removeButton.addEventListener('click', function () {
            if (!confirm(
                'Remove proposed Segment ' + item.segmentNumber +
                ' from the queue?'
            )) {
                return;
            }

            revisionQueue.splice(index, 1);
            renderRevisionQueue();
        });

        actions.appendChild(editButton);
        actions.appendChild(removeButton);

        body.appendChild(row);
        body.appendChild(editPanel);
        body.appendChild(actions);
        collapse.appendChild(body);

        card.appendChild(header);
        card.appendChild(collapse);
        accordion.appendChild(card);

        // Initialize the segment map after its accordion opens.
        collapse.addEventListener('shown.bs.collapse', function () {
            if (mapElement.dataset.initialized === '1') {
                const existingMap = revisionQueueMaps.find(
                    miniMap => miniMap._revisionMapId === mapElement.id
                );

                if (existingMap) {
                    existingMap.invalidateSize();
                }
                return;
            }

            if (
                !item.geometry ||
                item.geometry.type !== 'LineString' ||
                !Array.isArray(item.geometry.coordinates) ||
                item.geometry.coordinates.length < 2
            ) {
                mapElement.textContent = 'No valid segment geometry available.';
                return;
            }

            const miniMap = L.map(mapElement, {
                scrollWheelZoom: false
            });

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(miniMap);

            
            const fullRoadLayer = L.geoJSON(mainRoad, {
                style: {
                    color: '#9e9e9e',
                    weight: 5,
                    opacity: 0.8
                }
            }).addTo(miniMap);

            L.geoJSON(item.geometry, {
                style: {
                    color: '#ff0d0d',
                    weight: 7,
                    opacity: 1
                }
            }).addTo(miniMap);

            miniMap.fitBounds(fullRoadLayer.getBounds(), {
                padding: [15, 15]
            });

            miniMap._revisionMapId = mapElement.id;
            revisionQueueMaps.push(miniMap);
            mapElement.dataset.initialized = '1';
        });
    });

    revisionStatus.textContent = revisionQueue.length
        ? revisionQueue.length + ' proposed segment(s) queued. Nothing has been saved.'
        : 'No segments have been queued.';

    revisionStatus.className = revisionQueue.length
        ? 'alert alert-info'
        : 'alert alert-warning';

    validateRevisionLengths();
    clearRevisionQueueBtn.disabled = revisionQueue.length === 0;
}

addSegmentToQueueBtn.addEventListener('click', function () {
    if (
        selectedSplitIndex === null ||
        !generatedSplitSegments[selectedSplitIndex]
    ) {
        alert('Select a preview segment on the map first.');
        return;
    }

    const feature = generatedSplitSegments[selectedSplitIndex];

    if (revisionQueue.some(item =>
        item.segmentNumber === feature.properties.segmentNumber
    )) {
        alert('This segment is already in the queue.');
        return;
    }

    const fields = getCurrentSegmentFields();
    fields.SegmentLen = feature.properties.lengthKm.toFixed(3);

    revisionQueue.push({
        segmentNumber: feature.properties.segmentNumber,
        geometry: JSON.parse(JSON.stringify(feature.geometry)),
        fields: fields
    });

    saveAllNewSegmentsBtn.disabled =
        revisionQueue.length === 0;

    clearRevisionQueueBtn.disabled =
        revisionQueue.length === 0;

    renderRevisionQueue();

    revisionStatus.textContent =
        'Segment ' + feature.properties.segmentNumber +
        ' added. You can select another segment and encode its details.';
});

clearRevisionQueueBtn.addEventListener('click', function () {
    if (!confirm('Clear all queued segments?')) return;

    revisionQueue.length = 0;
    renderRevisionQueue();
});

saveAllNewSegmentsBtn.addEventListener(
    'click',
    async function () {

        if (revisionQueue.length === 0) {
            alert('No new segments to save.');
            return;
        }

        const confirmed = confirm(
            'Save all ' +
            revisionQueue.length +
            ' new segment(s) to this road?'
        );

        if (!confirmed) {
            return;
        }

        saveAllNewSegmentsBtn.disabled = true;

        saveAllNewSegmentsBtn.textContent =
            'Saving...';

        try {

            const response = await fetch(
                'index.php?r=road/save-new-carriageways'
                + '&id=<?= trim((string)$road["_id"]) ?>',
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
                        segments: revisionQueue
                    })
                }
            );

            const responseText = await response.text();
            let result;

            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('Unexpected server response:', responseText);
                throw new Error(
                    'Server returned a non-JSON response (HTTP ' +
                    response.status +
                    '). Check the browser console and Yii logs.'
                );
            }

            if (!response.ok) {
                throw new Error(
                    result.message ||
                    'Request failed with HTTP ' + response.status + '.'
                );
            }

            if (!result.success) {
                throw new Error(
                    result.message ||
                    'Failed to save new segments.'
                );
            }

            alert(
                result.message ||
                'New segments added successfully.'
            );

            window.location.href =
                'index.php?r=road/view'
                + '&id=<?= trim((string)$road["_id"]) ?>'
                + '&edit=1';

        } catch (error) {

            console.error(error);

            alert(
                error.message ||
                'An error occurred while adding the new segments.'
            );

            saveAllNewSegmentsBtn.disabled = false;

            saveAllNewSegmentsBtn.textContent =
                'Save All New Segments';
        }
    }
);


const startSplitBtn = document.getElementById('startSplitBtn');
const undoSplitBtn = document.getElementById('undoSplitBtn');
const cancelSplitBtn = document.getElementById('cancelSplitBtn');

// Convert Leaflet map coordinates [lat, lng] to GeoJSON [lng, lat].
function toGeoJSONCoordinate(latlng) {
    return [latlng.lng, latlng.lat];
}

// Clear temporary split markers and preview lines.
function clearSplitPreview() {
    splitMarkers.forEach(marker => map.removeLayer(marker));
    splitPreviewLayers.forEach(layer => map.removeLayer(layer));

    splitMarkers = [];
    splitPreviewLayers = [];
    generatedSplitSegments = [];

    if (previewSplitBtn) {
        previewSplitBtn.disabled = true;
    }
}


// Update the split-tool buttons.
function updateSplitButtons() {
    undoSplitBtn.disabled = !splitModeActive || splitMarkers.length === 0;
    cancelSplitBtn.disabled = !splitModeActive;
}


function getNearestPointOnRoad(clickLatLng) {
    const clickPoint = map.latLngToLayerPoint(clickLatLng);
    let nearest = null;
    let minDistance = Infinity;

    for (let i = 0; i < mainCoordinates.length - 1; i++) {
        const a = map.latLngToLayerPoint(
            L.latLng(mainCoordinates[i][0], mainCoordinates[i][1])
        );
        const b = map.latLngToLayerPoint(
            L.latLng(mainCoordinates[i + 1][0], mainCoordinates[i + 1][1])
        );

        const dx = b.x - a.x;
        const dy = b.y - a.y;
        const lengthSquared = dx * dx + dy * dy;

        if (lengthSquared === 0) continue;

        let t = (
            (clickPoint.x - a.x) * dx +
            (clickPoint.y - a.y) * dy
        ) / lengthSquared;

        t = Math.max(0, Math.min(1, t));

        const x = a.x + t * dx;
        const y = a.y + t * dy;
        const distance = Math.hypot(clickPoint.x - x, clickPoint.y - y);

        if (distance < minDistance) {
            minDistance = distance;

            const nearestLatLng = map.layerPointToLatLng(L.point(x, y));

            nearest = {
                latlng: nearestLatLng,
                segmentIndex: i,
                t: t,
                distance: distance
            };
        }
    }

    return nearest;
}


function buildSplitSegments() {
    if (splitMarkers.length === 0) {
        alert('Place at least one split point first.');
        return [];
    }

    const points = mainCoordinates.map(coord => ({
        lat: coord[0],
        lng: coord[1]
    }));

    // Calculate cumulative distance along the original road.
    const cumulative = [0];

    for (let i = 1; i < points.length; i++) {
        cumulative.push(
            cumulative[i - 1] +
            map.distance(
                [points[i - 1].lat, points[i - 1].lng],
                [points[i].lat, points[i].lng]
            )
        );
    }

    // Project every selected marker onto the closest road segment.
    const splitPositions = splitMarkers.map(marker => {
        const nearest = getNearestPointOnRoad(marker.getLatLng());
        if (!nearest) return null;

        const a = points[nearest.segmentIndex];
        const b = points[nearest.segmentIndex + 1];

        const segmentDistance =
            cumulative[nearest.segmentIndex + 1] -
            cumulative[nearest.segmentIndex];

        return {
            distance:
                cumulative[nearest.segmentIndex] +
                nearest.t * segmentDistance,
            coordinate: [
                nearest.latlng.lng,
                nearest.latlng.lat
            ]
        };
    }).filter(Boolean);

    // Sort split points in the order they occur along the road.
    splitPositions.sort((a, b) => a.distance - b.distance);

    // Remove duplicate split locations.
    const uniqueSplits = splitPositions.filter((point, index, array) =>
        index === 0 ||
        Math.abs(point.distance - array[index - 1].distance) > 1
    );

    const boundaries = [
        { distance: 0, coordinate: [points[0].lng, points[0].lat] },
        ...uniqueSplits,
        {
            distance: cumulative[cumulative.length - 1],
            coordinate: [
                points[points.length - 1].lng,
                points[points.length - 1].lat
            ]
        }
    ];

    const segments = [];

    for (let s = 0; s < boundaries.length - 1; s++) {
        const start = boundaries[s];
        const end = boundaries[s + 1];

        const coordinates = [start.coordinate];

        for (let i = 1; i < points.length - 1; i++) {
            if (
                cumulative[i] > start.distance &&
                cumulative[i] < end.distance
            ) {
                coordinates.push([points[i].lng, points[i].lat]);
            }
        }

        coordinates.push(end.coordinate);

        if (coordinates.length >= 2 && end.distance > start.distance) {
            segments.push({
                type: 'Feature',
                properties: {
                    segmentNumber: s + 1,
                    lengthKm: (end.distance - start.distance) / 1000
                },
                geometry: {
                    type: 'LineString',
                    coordinates: coordinates
                }
            });
        }
    }

    return segments;
}

previewSplitBtn.addEventListener('click', function () {
    splitPreviewLayers.forEach(layer => map.removeLayer(layer));
    splitPreviewLayers = [];

    generatedSplitSegments = buildSplitSegments();

    splitModeActive = false;
    updateSplitButtons();

    if (generatedSplitSegments.length === 0) {
        splitInstructions.textContent =
            'No segments generated. Place split points along the road.';
        return;
    }

    const colors = [
        '#e53935', '#8e24aa', '#00897b',
        '#fb8c00', '#3949ab', '#6d4c41'
    ];

    
    generatedSplitSegments.forEach((feature, index) => {
        const layer = L.geoJSON(feature, {
            style: {
                color: colors[index % colors.length],
                weight: 7
            }
        }).addTo(map);

        layer.bindTooltip(
            'Segment ' + (index + 1) +
            ' — ' + feature.properties.lengthKm.toFixed(3) + ' km'
        );
        
        
        layer.on('click', function (event) {
            L.DomEvent.stopPropagation(event);

            selectedSplitIndex = index;

            const selectedFeature = generatedSplitSegments[selectedSplitIndex];

            geometryData = JSON.parse(
                JSON.stringify(selectedFeature.geometry)
            );

            addSegmentToQueueBtn.disabled = false;

            splitPreviewLayers.forEach(function (previewLayer) {
                previewLayer.setStyle({
                    color: previewLayer === layer ? '#00a651' : '#888888',
                    weight: previewLayer === layer ? 9 : 6
                });
            });

            splitInstructions.textContent =
                'Segment ' + selectedFeature.properties.segmentNumber +
                ' selected (' +
                selectedFeature.properties.lengthKm.toFixed(3) +
                ' km). Enter its details, then click Add Segment to Queue.';
        });

        splitPreviewLayers.push(layer);
    });


    splitInstructions.textContent =
        generatedSplitSegments.length +
        ' segment(s) generated. Preview only; nothing has been saved.';
});

startSplitBtn.addEventListener('click', function () {
    splitModeActive = true;
    previewSplitBtn.disabled = false;

    splitInstructions.textContent =
        'Click the blue road line to place split points. Use Undo to remove the last point, or Cancel to stop.';

    updateSplitButtons();
});

undoSplitBtn.addEventListener('click', function () {
    if (!splitModeActive || splitMarkers.length === 0) return;

    const marker = splitMarkers.pop();
    map.removeLayer(marker);

    updateSplitButtons();
});

cancelSplitBtn.addEventListener('click', function () {
    splitModeActive = false;
    clearSplitPreview();

    splitInstructions.textContent =
        'Split cancelled. Select "Split Road" to start again.';

    updateSplitButtons();
});

map.on('click', function (event) {
    if (!splitModeActive) return;

    const nearest = getNearestPointOnRoad(event.latlng);

    if (!nearest || nearest.distance > 15) {
        splitInstructions.textContent =
            'Click closer to the blue road line to place a split point.';
        return;
    }

    const marker = L.circleMarker(nearest.latlng, {
        radius: 7,
        color: '#ffffff',
        weight: 2,
        fillColor: '#ff6600',
        fillOpacity: 1
    }).addTo(map);

    marker.bindTooltip('Split point ' + (splitMarkers.length + 1));

    splitMarkers.push(marker);

    splitInstructions.textContent =
        splitMarkers.length + ' split point(s) placed.';

    updateSplitButtons();
});


const mainPolyline =
    L.polyline(
        mainCoordinates,
        {
            color: 'blue',
            weight: 5
        }
    ).addTo(map);

// ====================================
// EXISTING CARRIAGEWAY SEGMENT
// ====================================

const existingSegment =
    <?= json_encode(
        $selectedSegment['geometry'] ?? null
    ) ?>;

if (
    existingSegment &&
    existingSegment.coordinates
) {

    const segmentCoordinates =
        existingSegment.coordinates.map(
            coord => [
                coord[1],
                coord[0]
            ]
        );

    const existingSegmentPolyline =
        L.polyline(
            segmentCoordinates,
            {
                color: 'red',
                weight: 6
            }
        ).addTo(map);

}

map.fitBounds(
    mainPolyline.getBounds()
);

setTimeout(function(){

    map.invalidateSize();

},300);

</script>