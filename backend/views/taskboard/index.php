<?php

$this->title = 'Taskboard';

?>

<div class="taskboard-page">

    <div class="page-title">

        <h2>
            Taskboard
        </h2>

    </div>

    <?php

    foreach (
        $dataProvider->allModels
        as $road
    ) :

    ?>

        <div class="task-card">

            <div class="task-content">

                <h3>

                    <?= $road['R_NAME'] ?>

                </h3>

                <p>

                    Submitted for review

                </p>

                <div class="task-status">

                    PENDING

                </div>

            </div>

            <div class="task-action">

                <a
                href="index.php?r=taskboard/view&id=<?= trim((string)$road['_id']) ?>"
                    class="review-btn"
                >

                    REVIEW NOW

                </a>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<style>

.taskboard-page {
    padding: 20px;
}

.page-title {
    margin-bottom: 20px;
}

.task-card {

    background: white;

    border-radius: 10px;

    padding: 25px;

    margin-bottom: 20px;

    display: flex;

    justify-content: space-between;

    align-items: center;
}

.task-content h3 {
    margin: 0;
}

.task-status {

    margin-top: 15px;

    background: #f4c542;

    color: #222;

    display: inline-block;

    padding: 8px 15px;

    border-radius: 6px;

    font-weight: bold;
}

.review-btn {

    background: #2f80ed;

    color: white;

    text-decoration: none;

    padding: 12px 20px;

    border-radius: 6px;

    font-weight: bold;
}

.review-btn:hover {

    color: white;

    text-decoration: none;
}

</style>