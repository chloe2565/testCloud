<?php
require '../_base.php';

if (is_post()) {
    // process login
    $email = post('email');
    $password = post('password');
    $result = $_db->query("SELECT * FROM user WHERE email = '$email' AND pass = SHA1('$password') AND level = '1'");
    $admin = $result->fetch();

    if (!empty($admin)) {
        // check status
        if ($admin->status != 1) {
            sweet_alert_msg('This account is blocked. ', 'error', 'admin_login.php', false);
        } else {
            // successfully login -> set session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_id'] = $admin->uid;
            sweet_alert_msg('Login Successful', 'success', 'admin_profile.php', true);
        }
    } else {
        sweet_alert_msg('Invalid email or password. ', 'error', 'admin_login.php', false);
    }
}
?>

<head>
    <link rel="stylesheet" href="../css/admin_login.css?v=2">
    <link rel="stylesheet" href="../css/login.css?v=2">
    <link rel="stylesheet" href="../css/utility.css?v=2">
    <link rel="stylesheet" href="../css/change_pass.css?v=2">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/app.js"></script>
</head>

<div class="container">
    <div class="content-wrapper">
        <h1 class="title">Staff Login</h1>
        <form method="post">
            <div class="login-infor">
                <div class="admin-input">
                    <label for="email" class="u_input required">Email</label>
                    <?= html_text('email', 'placeholder="Enter your email"') ?>
                </div>


                <div class="admin-input">
                    <label for="password" class="u_input required">Password</label>
                    <div class="password-field">
                        <button type="button" class="show-pw" onclick="showpwButton('password', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 64 64">
                                <g id="Layer_85" data-name="Layer 85">
                                    <path d="M61.59,30.79C61.06,30.08,48.26,13.5,32,13.5a24.84,24.84,0,0,0-4.6.44,2,2,0,1,0,.74,3.93A20.89,20.89,0,0,1,32,17.5c11.9,0,22.23,10.82,25.41,14.5a55.56,55.56,0,0,1-6.71,6.55,2,2,0,1,0,2.54,3.09,56.15,56.15,0,0,0,8.35-8.43A2,2,0,0,0,61.59,30.79Z"></path>
                                    <path d="M48.4,42.29l-8.8-8.8L30.51,24.4l-7.82-7.82L12.25,6.14A2,2,0,0,0,9.43,9l8.49,8.49A55.12,55.12,0,0,0,2.41,30.79a2,2,0,0,0,0,2.42C2.94,33.92,15.74,50.5,32,50.5a29.57,29.57,0,0,0,14.67-4.29L55,54.57a2,2,0,0,0,2.83-2.83ZM28.82,28.36l6.83,6.83a4.84,4.84,0,1,1-6.83-6.83ZM32,46.5C20.1,46.5,9.77,35.68,6.59,32A50.36,50.36,0,0,1,20.87,20.41L26,25.53A8.83,8.83,0,1,0,38.47,38l5.27,5.27A25,25,0,0,1,32,46.5Z"></path>
                                </g>
                            </svg>
                        </button>
                        <?= html_password('password', "class='pass-field' placeholder='Enter your password'") ?>
                    </div>
                    <?= err("rpass") ?>
                </div>
            </div>
            <input type="submit" class="login" value="Login">
        </form>
    </div>
</div>