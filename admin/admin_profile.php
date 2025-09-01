<?php
$_title = "My Profile";
require_once '../_base.php';
global $_adminID;

$err = [];
if (is_post()) {
    $upic = post("upic");
    $uname = post("uname");
    $uemail = post('uemail');
    $umobile = post("umobile");

    $me = $_db->query("SELECT * FROM user WHERE uid = $_adminID")->fetch();

    if ($uname != $me->uname) $_err["uname"] = checkUsername($uname) ?? '';
    if ($uemail != $me->email) $_err["uemail"] = checkRegisterEmail($uemail) ?? '';
    if ($umobile != $me->contact)  $_err["umobile"] = checkContact($umobile) ?? '';

    if (isset($_FILES['upic'])) {
        $file = $_FILES['upic'];
        $_err["upic"] = checkUploadPic($file);

        // no error
        if (empty($_err["upic"])) {
            //everything okey, save the file
            //create a unique id and use it as file name
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFileName = isset($_FILES["upic"]) && !empty($_FILES["upic"] && !empty($ext)) ? uniqid() . '.' . $ext : $me->proPic;
        } else {
            $newFileName = $me->proPic;
        }
    }

    $_err = array_filter($_err);

    if (empty($_err)) {
        $stmt = $_db->prepare("UPDATE user SET uname = ?, email = ?, contact = ?, proPic = ? WHERE uid = ?");
        $stmt->execute([$uname, $uemail, $umobile, $newFileName, $_adminID]);
        if ($stmt->rowCount() < 1) {
            sweet_alert_msg("No changes made.", 'info', null, false);
        } else {
            //save the file
            if ($newFileName != $me->proPic) {
                move_uploaded_file($file['tmp_name'], '../profilePic/' . $newFileName);
                // delete old pic from file
                if ($me->proPic != null)
                    unlink("../profilePic/$me->proPic");
            }
            sweet_alert_msg('Record update successful', 'success', null, false);
        }
    }
}

include './admin_header.php';

$uname = $admin->uname;
$umobile = $admin->contact;
$uemail = $admin->email;
$upic = $admin->proPic;
$superadmin = $admin->superadmin;
?>

<head>
    <link href="../css/profile.css" rel="stylesheet" type="text/css" />
    <style>
        input[type="submit"] {
            background: #373737;
        }

        .input-field {
            flex-direction: row;
        }

        /* .edit-area.active #upload-preview:hover>span {
            opacity: 1;
        }

        .edit-area.disabled-edit #upload-preview {
            pointer-events: none;
        } */
    </style>
</head>


<div class="profile-box">
    <!-- <div class="profile-nav">
        <div class="open" id="accound-details">
            <div class="nav-title">
                <svg width="20px" height="20px" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Rounded" transform="translate(-238.000000, -156.000000)">
                            <g id="Action" transform="translate(100.000000, 100.000000)">
                                <g id="-Round-/-Action-/-account_circle" transform="translate(136.000000, 54.000000)">
                                    <g>
                                        <polygon id="Path" points="0 0 24 0 24 24 0 24"></polygon>
                                        <path d="M12,2 C6.48,2 2,6.48 2,12 C2,17.52 6.48,22 12,22 C17.52,22 22,17.52 22,12 C22,6.48 17.52,2 12,2 Z M12,5 C13.66,5 15,6.34 15,8 C15,9.66 13.66,11 12,11 C10.34,11 9,9.66 9,8 C9,6.34 10.34,5 12,5 Z M12,19.2 C9.5,19.2 7.29,17.92 6,15.98 C6.03,13.99 10,12.9 12,12.9 C13.99,12.9 17.97,13.99 18,15.98 C16.71,17.92 14.5,19.2 12,19.2 Z" id="🔹Icon-Color" fill="#1D1D1D"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
                <span>Account Details</span>
            </div>
        </div>
        <div id="change-password" onclick="window.location.href='change_pass.php'">
            <div class="nav-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Change Password</span>
            </div>
        </div>
    </div> -->
    <div id="page-content">
        <div id="account-details">
            <form method="post" enctype="multipart/form-data">
                <div class="edit-area disabled-edit">
                    <div class="photo-area">
                        <div class="d-flex-center">
                            <!-- photo preview -->
                            <label class="photo" for="upic" id="upload-preview" tabindex="0">
                                <?= html_file('upic', 'image/*', 'hidden') ?>
                                <img src="../profilePic/<?= $upic ?? 'profile.png' ?>">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 8l-5-5-5 5M12 4.2v10.3" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                        <?= err('upic') ?>

                        <?php if ($admin->superadmin == 1): ?>
                            <div class="label">
                                <label for="superadmin">Superadmin</label>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="input-data">
                        <div class="input-field">
                            <label for="uname" class="required">Username</label>
                            <?= html_text('uname', "placeholder='Enter Username'") ?>
                            <?= err("uname") ?>
                        </div>
                        <div class="input-field">
                            <label for="uemail" class="required">Email address</label>
                            <?= html_text('uemail', "placeholder='Enter Email (e.g. xxxx@xxx.xxx)'") ?>
                            <?= err("uemail") ?>
                        </div>
                        <div class="input-field">
                            <label for="mobile" class="required">Mobile</label>
                            <?= html_text('umobile', "placeholder='Enter Mobile Number (e.g. 0123456789)'") ?>
                            <?= err("umobile") ?>
                        </div>
                    </div>
                    <input type="submit" class="show-edit form-button" value="Save">
                </div>
                <input type="button" class="form-button hide-edit" value="Edit Info" id="enable-edit">
            </form>
        </div>
    </div>
</div>

<!-- <script>
    $('#enable-edit').click(function() {
        e = $('.disabled-edit:has(+ #enable-edit)')[0];
        $('.disabled-edit input').prop("disabled", false);
        $('.disabled-edit .show-edit').prop("hidden", false);
        $(".hide-edit").prop("hidden", true);
        $(e).removeClass("disabled-edit");
        $(e).addClass("active");
    });
</script> -->

<?php include "admin_footer.php" ?>