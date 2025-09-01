<?php
require_once '../_base.php';

if (is_post()) {
    // multiple remove
    $ids = req('checkboxItem', []);
    if (!is_array($ids)) $ids = [$ids];

    if (sizeof($ids) < 1) {
        sweet_alert_msg("No item selected. ", 'error', 'student_list.php', false);
    }

    $num = 0;

    if (isset($_POST['btn-delete'])) {
        $updateValue = 0;
        $action = 'deleted';

        foreach ($ids as $id) {
            $stm = $_db->prepare("DELETE FROM student WHERE studid = ?");
            $stm->execute([$id]);
            if ($stm->rowCount() > 0) {
                $num++;
            }
        }
    } else {
        sweet_alert_msg('Invalid action.', 'error', $_SERVER['HTTP_REFERER'], false);
    }
    
    if ($num > 0) {
        sweet_alert_msg("$num record(s) has been $action. ", 'success', $_SERVER['HTTP_REFERER'], false);
    } else {
        sweet_alert_msg("No changes made. ", 'info', $_SERVER['HTTP_REFERER'], false);
    }
}
