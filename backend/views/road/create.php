<?php

use yii\helpers\Html;

$role = strtolower(
    trim(
        Yii::$app->user->identity->role
    )
);

$this->title = 'Create Road';

?>

<!-- LEAFLET CSS -->

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

<div class="create-road-page">

    <!-- PAGE HEADER -->

    <div class="page-header">

        <h2>
            Create New Road
        </h2>

    </div>

    <!-- UPLOAD CARD -->

    <div class="upload-card">

        <div class="upload-grid">

            <div>

                <label class="form-label">
                    Upload Shapefile ZIP
                </label>

                <input
                    type="file"
                    id="zipUpload"
                    accept=".zip"
                    class="form-control"
                >

            </div>

            <div class="upload-button-wrapper">

                <button
                    type="button"
                    class="btn btn-primary"
                    id="uploadBtn"
                >
                    Upload Shapefile
                </button>

            </div>

        </div>

    </div>

    <!-- MAP -->

    <div id="map"></div>

    <!-- ROAD FORM -->

    <div class="road-form-card">

        <h4 class="section-title">
            Road Information
        </h4>

        <div class="form-grid">

            <!-- ROAD ID -->

            <div>

                <label class="form-label" for="roadIdSuffix">
                    Road ID *
                </label>

                <div class="input-group">
                    <span class="input-group-text" id="roadIdPrefix">Select classification</span>
                    <input
                        type="text"
                        id="roadIdSuffix"
                        class="form-control"
                        inputmode="numeric"
                        maxlength="4"
                        pattern="[0-9]{4}"
                        title="Enter exactly four digits."
                        required
                    >
                </div>

                <small class="form-text text-muted">
                    The final four digits are editable; the prefix is assigned by jurisdiction.
                </small>

            </div>

            <!-- STATION -->

            <div>

                <label class="form-label">
                    Station *
                </label>

                <input
                    type="text"
                    id="station"
                    class="form-control"
                    required
                >

            </div>

            <!-- ROAD NAME -->

            <div>

                <label class="form-label">
                    Road Name *
                </label>

                <input
                    type="text"
                    id="roadName"
                    class="form-control"
                    required
                >

            </div>

            <!-- ROAD CLASSIFICATION -->

            <div>

                <label class="form-label">
                    Road Classification *
                </label>

                <select
                    id="roadClass"
                    class="form-select"
                    required
                >

                    <option value="" selected disabled hidden></option>
           
                    <?php
                    $allowedRoadClasses =
                        Yii::$app->user->identity
                            ->getAllowedRoadClassifications();
                    ?>

                    <?php foreach ($allowedRoadClasses as $roadClass): ?>
                        <option value="<?= htmlspecialchars($roadClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($roadClass, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>

                </select>

            </div>

            <!-- ROAD IMPORTANCE -->

            <div>

                <label class="form-label">
                    Road Importance *
                </label>

                <select
                    id="roadImportance"
                    class="form-select"
                    required
                >

                    <option
                        value=""
                        selected
                        disabled
                        hidden
                    ></option>

                    <option value="Core">
                        Core
                    </option>

                    <option value="Non-Core">
                        Non-Core
                    </option>

                </select>

            </div>

            <!-- ENVIRONMENT -->

            <div>

                <label class="form-label">
                    Environment *
                </label>

                <select
                    id="environment"
                    class="form-select"
                    required
                >

                    <option
                        value=""
                        selected
                        disabled
                        hidden
                    ></option>

                    <option value="Urban">
                        Urban
                    </option>

                    <option value="Rural">
                        Rural
                    </option>

                </select>

            </div>

            <!-- DIRECTION FLOW -->

            <div>

                <label class="form-label">
                    Direction Flow *
                </label>

                <select
                    id="directionFlow"
                    class="form-select"
                    required
                >

                    <option
                        value=""
                        selected
                        disabled
                        hidden
                    ></option>

                    <option value="One-way">
                        One-way
                    </option>

                    <option value="Two-way">
                        Two-way
                    </option>

                </select>

            </div>

            <!-- TERRAIN -->

            <div>

                <label class="form-label">
                    Terrain *
                </label>

                <select
                    id="terrain"
                    class="form-select"
                    required
                >

                    <option
                        value=""
                        selected
                        disabled
                        hidden
                    ></option>

                    <option value="Flat">
                        Flat
                    </option>

                    <option value="Rolling">
                        Rolling
                    </option>

                    <option value="Mountainous">
                        Mountainous
                    </option>

                </select>

            </div>

            <!-- LENGTH -->

            <div>

                <label class="form-label">
                    Length *
                </label>

                <input
                    type="number"
                    step="0.001"
                    id="roadLength"
                    class="form-control"
                    required
                >

            </div>

            <!-- RROW DATE -->

            <div>

                <label class="form-label">
                    RROW Acquisition Date
                </label>

                <input
                    type="date"
                    id="rrowDate"
                    class="form-control"
                >

            </div>

            <!-- RROW COST -->

            <div>

                <label class="form-label">
                    RROW Acquisition Cost
                </label>

                <input
                    type="number"
                    step="0.01"
                    id="rrowCost"
                    class="form-control"
                >

            </div>

        </div>

    </div>

    <!-- REMARKS -->

    <div class="remarks-card">
        <h4 class="section-title">Remarks</h4>

        <div class="remarks-content">
            <textarea
                id="roadRemarks"
                class="form-control"
                rows="3"
                placeholder="Enter remarks here..."
            ></textarea>
        </div>
    </div>

    <!-- ACTION BUTTONS -->

    <div class="action-buttons">

        <button
            type="button"
            class="btn btn-secondary"
            id="saveDraftBtn"
        >
            Save as Draft
        </button>

        <button
            type="button"
            class="btn btn-primary"
            id="submitReviewBtn"
        >
            Submit for Review
        </button>

    </div>

</div>

<!-- LEAFLET -->

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- SHAPEFILE -->

<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>

<?= Html::csrfMetaTags() ?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const roadIdOptions = <?= json_encode($roadIdOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const roadIdPrefix = document.getElementById('roadIdPrefix');
    const roadIdSuffix = document.getElementById('roadIdSuffix');
    const roadClassField = document.getElementById('roadClass');

    function updateRoadIdForClassification() {
        const option = roadIdOptions[roadClassField.value];

        if (!option) {
            roadIdPrefix.textContent = 'Select classification';
            roadIdSuffix.value = '';
            return;
        }

        roadIdPrefix.textContent = option.prefix;
        roadIdSuffix.value = option.nextSequence <= 9999
            ? String(option.nextSequence).padStart(4, '0')
            : '';
    }

    roadClassField.addEventListener('change', updateRoadIdForClassification);

    // ====================================
    // INITIALIZE MAP
    // ====================================

    const map = L.map('map').setView([12.8797, 121.7740], 6);

    // ====================================
    // BASEMAP
    // ====================================

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // ====================================
    // UPLOAD SHAPEFILE
    // ====================================

    document.getElementById('uploadBtn').addEventListener('click', function() {
        const file = document.getElementById('zipUpload').files[0];

        if (!file) {
            alert('Please select ZIP file.');
            return;
        }

        const reader = new FileReader();

        reader.onload = function(event) {
            const arrayBuffer = event.target.result;

            shp(arrayBuffer)
                .then(function(geojson) {
                    window.uploadedGeoJSON = geojson;

                    if (window.shapefileLayer) {
                        map.removeLayer(window.shapefileLayer);
                    }

                    window.shapefileLayer = L.geoJSON(geojson, {
                        style: {
                            color: '#ff0000',
                            weight: 4
                        }
                    }).addTo(map);

                    map.fitBounds(window.shapefileLayer.getBounds());

                    const properties = geojson.features && geojson.features[0]
                        ? geojson.features[0].properties
                        : {};

                    document.getElementById('station').value = properties.Station || '';
                    document.getElementById('roadName').value = properties.R_NAME || '';
                    document.getElementById('roadClass').value = properties.R_CLASS || '';
                    roadClassField.dispatchEvent(new Event('change'));
                    document.getElementById('roadImportance').value = properties.R_Importan || '';
                    document.getElementById('environment').value = properties.Environmen || '';
                    document.getElementById('directionFlow').value = properties.DirFlow || '';
                    document.getElementById('terrain').value = properties.Terrain || '';
                    document.getElementById('roadLength').value = properties.Length || '';
                    document.getElementById('rrowDate').value = properties.RROW_acq_date || '';
                    document.getElementById('rrowCost').value = properties.RROW_acq_cost || '';

                    alert('Shapefile uploaded successfully!');
                })
                .catch(function(error) {
                    console.error(error);
                    alert('ERROR: ' + error);
                });
        };

        reader.readAsArrayBuffer(file);
    });

    // ====================================
    // SAVE FUNCTION
    // ====================================
    function saveRoad(status) {
        if (!window.uploadedGeoJSON) {
            const zipInput = document.getElementById('zipUpload');
            if (zipInput) {
                zipInput.focus();
            }
            alert('Please upload a shapefile ZIP first before submitting a new road.');
            return;
        }

        const requiredFields = [
            'station',
            'roadName',
            'roadClass',
            'roadImportance',
            'environment',
            'directionFlow',
            'terrain',
            'roadLength'
        ];

        for (const fieldId of requiredFields) {
            const field = document.getElementById(fieldId);

            if (!field || !field.value.trim()) {
                if (field) {
                    field.focus();
                    field.reportValidity();
                }
                alert('Please complete all required fields before submitting.');
                return;
            }
        }

        if (!roadIdSuffix.reportValidity()) {
            roadIdSuffix.focus();
            return;
        }

        const properties = window.uploadedGeoJSON && window.uploadedGeoJSON.features && window.uploadedGeoJSON.features[0]
            ? window.uploadedGeoJSON.features[0].properties
            : {};

        const payload = {
            status: status,
            R_ID_SUFFIX: roadIdSuffix.value.trim(),
            Station: document.getElementById('station').value.trim(),
            R_NAME: document.getElementById('roadName').value.trim(),
            R_CLASS: document.getElementById('roadClass').value,
            R_Importan: document.getElementById('roadImportance').value,
            Environmen: document.getElementById('environment').value,
            DirFlow: document.getElementById('directionFlow').value,
            Terrain: document.getElementById('terrain').value,
            Length: document.getElementById('roadLength').value,
            RROW_acq_date: document.getElementById('rrowDate').value,
            RROW_acq_cost: document.getElementById('rrowCost').value,
            RegionCode: properties.RegionCode || '',
            ProvinceCo: properties.ProvinceCo || '',
            CityMunCod: properties.CityMunCod || '',
            geometry: window.uploadedGeoJSON || null
        };

        console.log('Saving road:', payload);

        fetch('index.php?r=road/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        })
        .then(async function (response) {
            const text = await response.text();

            let result;
            try {
                result = JSON.parse(text);
            } catch (error) {
                console.error('Invalid JSON response:', text);
                alert('Server returned an invalid response. Check browser console.');
                return;
            }

            if (result.success) {
                alert((result.message || 'Road saved successfully.') + (result.R_ID ? ' (R_ID: ' + result.R_ID + ')' : ''));
                window.location.href = 'index.php?r=road/index';
                return;
            }

            alert(result.message || 'Road save failed.');
        })
        .catch(function (error) {
            console.error(error);
            alert('Failed to save road. Please try again.');
        });
    }

    // ====================================
    // SAVE DRAFT
    // ====================================
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        saveRoad('draft');
    });

    // ====================================
    // SUBMIT REVIEW
    // ====================================
    document.getElementById('submitReviewBtn').addEventListener('click', function() {
        saveRoad('forreview');
    });
});
</script>

<style>

.create-road-page {
    padding: 20px;
}

.page-header {
    margin-bottom: 20px;
}

.upload-card,
.road-form-card,
.remarks-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.upload-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 20px;
    align-items: end;
}

.upload-button-wrapper {
    display: flex;
    align-items: end;
}

#map {
    width: 100%;
    height: 600px;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.section-title {
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.remarks-content {
    color: #555;
}

.action-buttons {
    display: flex;
    justify-content: end;
    gap: 20px;
    margin-top: 30px;
}

</style>