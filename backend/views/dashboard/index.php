<?php

$this->title = 'Dashboard';

?>

<style>
.dashboard-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 22px 24px;
    background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
    border: 1px solid #e5ecf8;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}

.page-header h2 {
    margin: 0;
    font-size: 1.45rem;
    font-weight: 700;
    color: #12324f;
}

.page-header p {
    margin: 4px 0 0;
    color: #64748b;
}

.header-badge {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2);
}

.dashboard-filter-card {
    background: #ffffff;
    border: 1px solid #e5ecf8;
    border-radius: 20px;
    padding: 20px 22px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
}

.dashboard-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}

.dashboard-tabs > div {
    padding: 9px 14px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #475569;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.06em;
}

.dashboard-tabs .active-tab {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: #fff;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    align-items: end;
}

.filter-grid label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.filter-grid .form-select,
.filter-grid .btn {
    border-radius: 10px;
}

.filter-btn {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
    width: 100%;
    padding: 10px 12px;
    font-weight: 600;
}

.dashboard-top-grid,
.dashboard-bottom-grid {
    display: grid;
    gap: 20px;
}

.dashboard-top-grid {
    grid-template-columns: 1.1fr 0.9fr;
    align-items: stretch;
}

.dashboard-bottom-grid {
    grid-template-columns: 1fr 1fr;
    align-items: stretch;
}

.dashboard-card {
    background: #ffffff;
    border: 1px solid #e5ecf8;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
}

.card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #12324f;
    margin-bottom: 16px;
}

.stats-column {
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-height: 0;
}

.stats-card {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid #e5ecf8;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
    font-size: 1.2rem;
}

.stats-content,
.stats-right {
    flex: 1;
}

.stats-content h2,
.stats-right h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
}

.stats-content p,
.stats-right p {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 0.9rem;
}

.condition-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.condition-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 16px;
    background: #f8fbff;
    border-radius: 14px;
    border: 1px solid #e5ecf8;
}

.condition-item:hover {
    background: #eef4ff;
}

.condition-chart {
    width: 100%;
    height: 140px;
    margin-bottom: 12px;
}

.condition-info {
    flex: 1;
}

.condition-info h4 {
    margin: 8px 0 4px;
    font-size: 0.95rem;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.05em;
}

.condition-info p {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 600;
}

@media (max-width: 992px) {
    .dashboard-top-grid,
    .dashboard-bottom-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-page">

    <div class="page-header">
        <div>
            <h2>Road Network Dashboard</h2>
            <p>Monitor inventory, surface condition, and road statistics at a glance.</p>
        </div>
        <div class="header-badge">Live Overview</div>
    </div>

    <!-- FILTER PANEL -->

    <div class="dashboard-filter-card">

        <div class="dashboard-tabs">

            <div class="active-tab">
                ROAD INVENTORY
            </div>

            <div>
                ROAD VALIDATION STATUS
            </div>

        </div>

        <div class="filter-grid">

            <div>
                <label>Region</label>

                <select class="form-select">
                    <option>All Regions</option>
                </select>
            </div>

            <div>
                <label>Province</label>

                <select class="form-select">
                    <option>All Provinces</option>
                </select>
            </div>

            <div>
                <label>City/Municipality</label>

                <select class="form-select">
                    <option>All Cities/Municipalities</option>
                </select>
            </div>

            <div>
                <label>Road Importance</label>

                <select class="form-select">
                    <option>All</option>
                </select>
            </div>

            <div>
                <label>Road Classification</label>

                <select class="form-select">
                    <option>All</option>
                </select>
            </div>

            <div class="filter-button-wrapper">

                <button class="btn btn-primary filter-btn">
                    APPLY FILTER
                </button>

            </div>

        </div>

    </div>

    <!-- TOP GRID -->

    <div class="dashboard-top-grid">

        <!-- ROAD IMPORTANCE -->

        <div class="dashboard-card">

            <div class="card-title">
                Road Importance
            </div>

            <div id="roadImportanceChart"></div>

        </div>

        <!-- STATS -->

        <div class="dashboard-card stats-column">

            <div class="stats-card">

                <div class="stats-icon">
                    <i class="fas fa-road"></i>
                </div>

                <div class="stats-content">

                    <h2>
                        <?= number_format($totalRoads) ?>
                    </h2>

                    <p>
                        No. of Roads
                    </p>

                </div>

                <div class="stats-right">

                    <h2>
                        <?= $totalLength ?> km
                    </h2>

                    <p>
                        Total Road Length
                    </p>

                </div>

            </div>

            <div class="stats-card">

                <div class="stats-icon">
                    <i class="fas fa-bridge"></i>
                </div>

                <div class="stats-content">

                    <h2>
                        <?= number_format($totalBridges) ?>
                    </h2>

                    <p>
                        No. of Bridges
                    </p>

                </div>

                <div class="stats-right">

                    <h2>
                        <?= $totalBridgeLength ?> m
                    </h2>

                    <p>
                        Total Bridge Length
                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- BOTTOM GRID -->

    <div class="dashboard-bottom-grid">

        <!-- SURFACE TYPE -->

        <div class="dashboard-card">

            <div class="card-title">
                Surface Type
            </div>

            <div class="row">

                <div class="col-md-5">

                    <div id="surfaceChart"></div>

                </div>

                <div class="col-md-7">

                    <h5
                        style="
                            color:#6c757d;
                        "
                    >
                        Total Road Length
                    </h5>

                    <h1
                        style="
                            color:#1565c0;
                            font-weight:bold;
                        "
                    >
                        <?= number_format($totalRoadLength, 3) ?>
                        km
                    </h1>

                    <br>

                    <p>

                        ● Paved Total Length -
                        <strong>
                            <?= $pavedPercentage ?>%
                        </strong>

                    </p>

                    <h2
                        style="
                            color:#4a90e2;
                            font-weight:bold;
                        "
                    >
                        <?= number_format($paved, 3) ?>
                        km
                    </h2>

                    <br>

                    <p>

                        ● Unpaved Total Length -
                        <strong>
                            <?= $unpavedPercentage ?>%
                        </strong>

                    </p>

                    <h2
                        style="
                            color:#e57373;
                            font-weight:bold;
                        "
                    >
                        <?= number_format($unpaved, 3) ?>
                        km
                    </h2>

                </div>

            </div>

            <hr>

            <div class="row text-center">

                <div class="col-md-3">

                    <strong>
                        CONCRETE
                    </strong>

                    <br>

                    <?= number_format($concrete, 3) ?>
                    km

                </div>

                <div class="col-md-3">

                    <strong>
                        ASPHALT
                    </strong>

                    <br>

                    <?= number_format($asphalt, 3) ?>
                    km

                </div>

                <div class="col-md-3">

                    <strong>
                        GRAVEL
                    </strong>

                    <br>

                    <?= number_format($gravel, 3) ?>
                    km

                </div>

                <div class="col-md-3">

                    <strong>
                        EARTH
                    </strong>

                    <br>

                    <?= number_format($earth, 3) ?>
                    km

                </div>

            </div>

        </div>

        <!-- SURFACE CONDITION -->

        <div class="dashboard-card">

            <div class="card-title">
                Surface Condition
            </div>

            <div class="condition-list">

                <!-- GOOD -->

                <div class="condition-item">

                    <div class="condition-chart">
                        <div id="goodChart"></div>
                    </div>

                    <div class="condition-info">

                        <h4>
                            GOOD
                        </h4>

                        <p>
                            <?= number_format($good, 2) ?> km
                        </p>

                    </div>

                </div>

                <!-- FAIR -->

                <div class="condition-item">

                    <div class="condition-chart">
                        <div id="fairChart"></div>
                    </div>

                    <div class="condition-info">

                        <h4>
                            FAIR
                        </h4>

                        <p>
                            <?= number_format($fair, 2) ?> km
                        </p>

                    </div>

                </div>

                <!-- POOR -->

                <div class="condition-item">

                    <div class="condition-chart">
                        <div id="poorChart"></div>
                    </div>

                    <div class="condition-info">

                        <h4>
                            POOR
                        </h4>

                        <p>
                            <?= number_format($poor, 2) ?> km
                        </p>

                    </div>

                </div>

                <!-- BAD -->

                <div class="condition-item">

                    <div class="condition-chart">
                        <div id="badChart"></div>
                    </div>

                    <div class="condition-info">

                        <h4>
                            BAD
                        </h4>

                        <p>
                            <?= number_format($bad, 2) ?> km
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

$this->registerJs("

    // ROAD IMPORTANCE

    new ApexCharts(document.querySelector('#roadImportanceChart'), {

        chart: {
            type: 'pie',
            height: 320
        },

        series: [
            {$corePercentage},
            {$nonCorePercentage}
        ],

        labels: ['Core', 'Non-Core'],

        legend: {
            position: 'bottom'
        }

    }).render();


    // SURFACE TYPE

    new ApexCharts(document.querySelector('#surfaceChart'), {

        chart: {
            type: 'donut',
            height: 320
        },

        series: [
            {$paved},
            {$unpaved}
        ],

        labels: [
            'Concrete',
            'Asphalt',
            'Gravel',
            'Earth'
        ],

        legend: {
            position: 'bottom'
        }

    }).render();


    // RADIAL CHARTS

    function radialChart(el, value, color) {

        new ApexCharts(document.querySelector(el), {

            chart: {
                height: 140,
                type: 'radialBar'
            },

            series: [value],

            colors: [color],

            plotOptions: {

                radialBar: {

                    hollow: {
                        size: '65%'
                    },

                    dataLabels: {

                        name: {
                            show: false
                        },

                        value: {
                            fontSize: '28px',
                            fontWeight: 700,

                            formatter: function(val) {
                                return Number(val).toLocaleString(
                                    'en-US',
                                    {
                                        maximumFractionDigits: 0
                                    }
                                );
                            }
                        }
                    }
                }
            }

        }).render();
    }

    radialChart('#goodChart', {$good}, '#28a745');

    radialChart('#fairChart', {$fair}, '#2f80ed');

    radialChart('#poorChart', {$poor}, '#f2994a');

    radialChart('#badChart', {$bad}, '#eb5757');

");

?>