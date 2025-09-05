<?php
require_once '../_base.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $admin = $_db->query("SELECT * FROM user WHERE uid = $admin_id AND status = '1' AND level = '1'")->fetch();
}

// check admin login
if (empty($admin)) {
    sweet_alert_msg('Login Required!', 'error','admin_login.php', true);
    session_destroy();
}
?>

<head>
    <meta charset="UTF-8">
    <title><?= $_title ?? "Untitled" ?></title>
    <link href="../css/utility.css" rel="stylesheet" type="text/css" />
    <link href="../css/admin_header.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/app.js"></script>
</head>

<script>
    $(document).ready(function(e) {
        $(".accordion-parent").click(function() {
            if ($(this).hasClass("active")) {
                $(this).toggleClass("active");
            } else {
                $(".accordion-parent").removeClass("active");
                $(this).toggleClass("active");
            }
        });
    })
</script>

<body class="admin">
    <div class="admin-content">
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="logo" data-get="admin_homepage.php">
                    <p style="font-family: serif; font-size: 25px; font-weight: bold; font-style: italic; text-transform: uppercase; letter-spacing: 2px; line-height: 1.5;">Example University</p>
                </div>
                <div class="menu-list">
                    <div class="admin-profile">
                        <div class="profile d-flex-center" data-get="admin_profile.php">
                            <div class="admin-pic pointer-event-none">
                            <img src="../profilePic/<?= $admin?->proPic ?>" alt="" class="admin-pic">
                        </div>
                        <div class="admin-name pointer-event-none"><?= $admin?->uname ?></div>
                        </div>
                        
                        <div class="logout" onclick="window.location.href = '../logout.php'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fdfdfd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 3H6a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h4M16 17l5-5-5-5M19.8 12H9" />
                            </svg>
                        </div>
                    </div>
                    <div class="menu-item">
                        <div class="menu-title" data-get="admin_homepage.php">Home</div>
                        <!-- only for superadmin -->
                        <!-- <?php if ($admin->superadmin == 1): ?> 
                            <div class="menu-title" data-get="admin_list.php">Admin</div>
                        <?php endif ?> -->
                        <div class="menu-title" data-get="student_list.php">Student List</div>
                        <!-- <div class="menu-title" data-get="categoryList.php">Category</div>
                        <div class="menu-title" data-get="productList.php">Product</div>                      
                        <div class="menu-title" data-get="optionList.php">Options</div>                      
                        <div class="menu-title" data-get="order_record.php">Order</div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="top-menu-body-content">
            <div class="top-menu">
                <div class="top-menu-bar">
                    <div class="page-title">
                        <?= $_title ?? "Untitled" ?>
                    </div>
                </div>
            </div>
            <div class="body-content">
                <!-- to write content -->