<?php

use yii\widgets\LinkPager;
use yii\widgets\Pjax;

$this->title = 'Roads Management';

?>

<style>
.content-card {
    background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    border: 1px solid #e5ecf8;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5ecf8;
}

.page-header h2 {
    font-size: 1.45rem;
    font-weight: 700;
    color: #12324f;
    margin: 0;
}

.create-btn {
    border-radius: 999px;
    padding: 10px 18px;
    font-weight: 600;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
    box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2);
}

.filter-section {
    background: #ffffff;
    border: 1px solid #e5ecf8;
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 20px;
}

.filter-section .form-control,
.filter-section .btn {
    border-radius: 10px;
}

.filter-section .btn-primary {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    border: none;
}

.filter-section h6 {
    color: #475569;
    font-size: 0.8rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.filter-section label {
    color: #334155;
    font-size: 0.95rem;
    margin-right: 16px;
}

.road-table {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    border-collapse: separate;
    border-spacing: 0;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
}

.road-table thead th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 700;
    padding: 14px 12px;
    border-bottom: 1px solid #e2e8f0;
}

.road-table td {
    padding: 14px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.road-table tbody tr:hover {
    background: #f8fbff;
}

.badge {
    border-radius: 999px;
    padding: 6px 10px;
    font-weight: 600;
}

.pagination-wrapper {
    margin-top: 18px;
}

.pagination .page-item .page-link {
    border-radius: 999px;
    margin: 0 3px;
    color: #2563eb;
}

.pagination .page-item.active .page-link {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}
</style>

<div class="content-card">

    <div class="page-header">

        <h2>Roads and Road Feature Management</h2>

        <?php if (Yii::$app->user->identity && Yii::$app->user->identity->hasPermission('road.create')): ?>
            <a href="<?= \yii\helpers\Url::to(['road/create']) ?>"
               class="btn btn-primary create-btn">
                CREATE NEW ROAD
            </a>
        <?php endif; ?>

    </div>

<?php Pjax::begin([
    'id' => 'roads-grid',
    'timeout' => 5000,
    'enablePushState' => true,
]); ?>

<form method="get" data-pjax="1">

    <div class="filter-section">

        <div class="row mb-4 align-items-end">

            <div class="col-md-8">

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by Road Name or Road ID"
                    value="<?= Yii::$app->request->get('search') ?>"
                >

            </div>

            <div class="col-md-4">

                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    FILTER
                </button>

            </div>

        </div>

        <div class="mb-3">

            <h6>Class</h6>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="class[]"
                    value="Barangay"
                >
                Barangay
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="class[]"
                    value="City"
                >
                City
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="class[]"
                    value="Municipal"
                >
                Municipal
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="class[]"
                    value="Provincial"
                >
                Provincial
            </label>

        </div>

        <div class="mb-4">

            <h6>Status</h6>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="status[]"
                    value="draft"
                >
                Draft
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="status[]"
                    value="forreview"
                >
                For Review
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="status[]"
                    value="forrevision"
                >
                For Revision
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="status[]"
                    value="inprogress"
                >
                In Progress
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="status[]"
                    value="rejected"
                >
                Rejected
            </label>

            <label class="me-4">
                <input
                    type="checkbox"
                    name="status[]"
                    value="validated"
                >
                Validated
            </label>

        </div>

    </div>

</form>

    <table class="table road-table">

        <thead>
        <tr>
            <th>Road ID</th>
            <th>Road Name</th>
            <th>Road Classification</th>
            <th>Length(km)</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Segments</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($dataProvider->models as $road): ?>

        <tr>

            <td><?= $road->R_ID ?></td>

            <td><?= $road->R_NAME ?></td>

            <td><?= $road->R_CLASS ?></td>

            <td><?= $road->Length ?></td>

            <td>

            <?php

            $status = strtoupper($road->status ?? '');

            $badgeClass = 'bg-secondary';

            switch ($status) {

                case 'DRAFT':
                case 'FORREVIEW':
                case 'FOR REVIEW':
                    $badgeClass = 'bg-primary';
                    break;

                case 'REVISION':
                case 'IN PROGRESS':
                    $badgeClass = 'bg-warning';
                    break;

                case 'REJECTED':
                    $badgeClass = 'bg-danger';
                    break;

                case 'VALIDATED':
                    $badgeClass = 'bg-success';
                    break;
            }

            ?>

            <span class="badge <?= $badgeClass ?>">

                <?= $status ?>

            </span>

            </td>

            <td>
                <?php
                if (!empty($road->lastupdate_date)) {

                    $date = $road->lastupdate_date->toDateTime();
                    echo $date->format('m/d/Y');

                } else {
                    echo '-';
                }
                ?>
            </td>

            <td>
                <a href="index.php?r=road/view&id=<?= trim((string)$road['_id']) ?>">View</a>
            </td>

        </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <div class="pagination-wrapper">

        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,

            'options' => [
                'class' => 'pagination justify-content-end'
            ],

            'linkContainerOptions' => [
                'class' => 'page-item'
            ],

            'linkOptions' => [
                'class' => 'page-link'
            ],

            'disabledListItemSubTagOptions' => [
                'tag' => 'a',
                'class' => 'page-link'
            ],

            'prevPageLabel' => '<',
            'nextPageLabel' => '>',
            'firstPageLabel' => '<<',
            'lastPageLabel' => '>>',
        ]) ?>

    </div>

    <?php Pjax::end(); ?>

</div>

<?php
$this->registerJs("

    let timer;

    $(document).on('input', '.search-input', function () {

        clearTimeout(timer);

        let value = $(this).val();

        timer = setTimeout(function () {

            $.pjax({
                url: '?r=road/index&search=' + encodeURIComponent(value),
                container: '#roads-grid',
                timeout: 5000
            });

        }, 300);

    });

");
?>