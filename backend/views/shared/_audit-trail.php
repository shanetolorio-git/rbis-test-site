<?php

$auditEntries = $road['audit_trail'] ?? [];
if ($auditEntries instanceof \Traversable) {
    $auditEntries = iterator_to_array($auditEntries);
}
$auditEntries = is_array($auditEntries)
    ? array_reverse($auditEntries)
    : [];

?>

<div class="card p-4 mb-4 audit-trail-card">
    <h4>Update Audit Trail</h4>

    <?php if (!$auditEntries): ?>
        <p class="text-muted mb-0">No update history recorded yet.</p>
    <?php else: ?>
        <div class="audit-trail-list">
            <?php foreach ($auditEntries as $entry): ?>
                <?php
                $timestamp = $entry['date'] ?? null;
                if ($timestamp instanceof \MongoDB\BSON\UTCDateTime) {
                    $timestamp = $timestamp->toDateTime()->format('Y-m-d H:i:s');
                } else {
                    $timestamp = (string)$timestamp;
                }
                $changes = $entry['changes'] ?? [];
                if ($changes instanceof \Traversable) {
                    $changes = iterator_to_array($changes);
                }
                ?>
                <article class="audit-entry">
                    <div class="audit-entry-heading">
                        <div>
                            <strong><?= htmlspecialchars((string)($entry['action'] ?? 'Updated'), ENT_QUOTES, 'UTF-8') ?></strong>
                            <span class="audit-entry-section">
                                <?= htmlspecialchars((string)($entry['section'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php if (!empty($entry['target'])): ?>
                                <span class="audit-entry-target">
                                    Segment <?= htmlspecialchars((string)$entry['target'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <span class="audit-entry-date"><?= htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="audit-entry-by">
                        Updated by <?= htmlspecialchars((string)($entry['byName'] ?? 'Unknown user'), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <?php if (is_array($changes) && $changes): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered audit-changes-table">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Previous Value</th>
                                        <th>New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($changes as $change): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string)($change['field'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string)($change['old'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                                            <td><?= htmlspecialchars((string)($change['new'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.audit-trail-card h4 {
    margin-bottom: 16px;
}

.audit-entry {
    border: 1px solid #e2e6ea;
    border-left: 4px solid #2f80ed;
    border-radius: 6px;
    margin-bottom: 14px;
    padding: 14px;
}

.audit-entry:last-child {
    margin-bottom: 0;
}

.audit-entry-heading {
    align-items: flex-start;
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    justify-content: space-between;
}

.audit-entry-section,
.audit-entry-target {
    color: #555;
    margin-left: 8px;
}

.audit-entry-date,
.audit-entry-by {
    color: #6c757d;
    font-size: 0.9rem;
}

.audit-entry-by {
    margin: 4px 0 12px;
}

.audit-changes-table {
    margin-bottom: 0;
}
</style>
