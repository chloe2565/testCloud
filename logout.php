<?php
require_once "_base.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();
sweet_alert_msg("Log out successful. ", 'success', 'admin/admin_login.php', true);