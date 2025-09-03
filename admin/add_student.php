<?php
$_title = "Add Student";
require_once '../_base.php';
checkSuperadmin();

// Include the AWS SDK for PHP
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// AWS S3 configuration
$bucket = 'assm-image-bucket'; // Your actual bucket name
$region = 'us-east-1'; // Your S3 region

// Connect to S3 (using IAM Role - RECOMMENDED)
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region,
    // If using an IAM role on EC2, remove 'credentials'
    // 'credentials' => [
    //     'key'    => 'YOUR_ACCESS_KEY', // Only if not using IAM role
    //     'secret' => 'YOUR_SECRET_KEY', // Only if not using IAM role
    // ],
]);

include 'admin_header.php';
if (isset($_POST['cancel'])) {
    echo "<script>window.location.href = 'display_staff.php';</script>";
}

$_err = [];


if (is_post()) {
    $sname = post("sname") ?? "";
    $semail = post("semail") ?? "";
    $sphone = post("sphone") ?? "";
    $saddress = post("saddress") ?? "";
    $scity = post("scity") ?? "";
    $sstate = post("sstate") ?? "";

    $_err["sname"] = checkUsername($sname) ?? '';
    $_err["semail"] = checkRegisterEmail($semail) ?? '';
    $_err["sphone"] = checkRegisterContact($sphone) ?? '';
    $_err["saddress"] = checkAddress($saddress) ?? '';
    $_err["scity"] = checkCity($scity) ?? '';
    $_err["sstate"] = checkState($sstate) ?? '';

    $newFileName = null;

    // file upload
    $s3ObjectURL = null; // Variable to store S3 URL

    if (isset($_FILES['spic']) && $_FILES['spic']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['spic'];
        $_err["spic"] = checkUploadPic($file);

        if (empty($_err["spic"])) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $ext;

    $fileStream = @fopen($file['tmp_name'], 'rb');
    if (!$fileStream) {
        $_err["spic"] = "Failed to open uploaded file.";
    } else {
        try {
            $result = $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => 'user-images/' . $newFileName,
                'Body'   => $fileStream,
                'ACL'    => 'public-read',
            ]);
            $s3ObjectURL = $result['ObjectURL'];
        } catch (S3Exception $e) {
            $_err["spic"] = "S3 Upload failed: " . $e->getMessage();
            $newFileName = null;
        }
    }
}

error_log("Upload file size: " . filesize($file['tmp_name']));
error_log("Upload file path: " . $file['tmp_name']);
error_log("Upload error: " . $file['error']);


    } else if (isset($_FILES['spic']) && $_FILES['spic']['error'] !== UPLOAD_ERR_NO_FILE) {
         // Handle other upload errors
        $_err["spic"] = "File upload error: " . $_FILES['spic']['error'];
        $newFileName = null;
    } else {
         $newFileName = null; // No file uploaded
    }


    $_err = array_filter($_err);

    // no error then store new student record
    if (empty($_err)) {
        $stmt = $_db->prepare("INSERT INTO student (studName, studPic, studEmail, studPhone, studAddress, studCity, studState) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Store the S3 object key or URL in the database
        $stmt->execute([$sname, $newFileName, $semail, $sphone, $saddress, $scity, $sstate]);

        if ($stmt->rowCount() > 0) {
            // success
            // Remove the local file move, as it's now on S3
            // if ($newFileName !== null) {
            //     move_uploaded_file($file['tmp_name'], '../profilePic/' . $newFileName);
            // }

            sweet_alert_msg('New student record added successfully!', 'success', 'student_list.php', false);
            exit();
        } else {
            // fail
            $_err[] = "Unable to insert. Please try again.";
        }
    }
}
?>

<head>
    <link href="../css/login.css" rel="stylesheet" type="text/css" />
</head>

<style>
    #save {
        width: 100%;
        height: 35px;
        line-height: 1.5;
        font-size: 14px;
        font-family: inherit;
        background: #373737;
        color: white;
        border-radius: 10px;
        padding: 5px 20px;
    }
</style>

<div class="add-staff-box w-100">
    <div class="add-staff w-60" style="margin: 20px auto;">
        <h1 class="title text-center">Enter Student Information</h1>
        <div class="main-content">
            <form id="reg" method="post" action="" name="reg" enctype="multipart/form-data">


                <div class="input-field">
                    <label for="sname" class="required">Full Name</label>
                    <?= html_text('sname', "placeholder='Enter Full Name' required") ?>
                    <?= err("sname") ?>
                </div>
                <div class="input-field">
                    <label for="semail" class="required">Email Address</label>
                    <?= html_text('semail', "placeholder='Enter Email (e.g. xxxx@xxx.xxx)' required") ?>
                    <?= err("semail") ?>
                </div>
                <div class="input-field">
                    <label for="sphone" class="required">Mobile</label>
                    <?= html_text('sphone', "placeholder='Enter Mobile Number (e.g. 0123456789)' required") ?>
                    <?= err("sphone") ?>
                </div>
                <div class="input-field">
                    <label for="saddress" class="required">Address</label>
                    <?= html_text('saddress', "placeholder='Enter Address' required") ?>
                    <?= err("saddress") ?>
                </div>
                <div class="input-field">
                    <label for="scity" class="required">City</label>
                    <?= html_text('scity', "placeholder='Enter City' required") ?>
                    <?= err("scity") ?>
                </div>
                <div class="input-field">
                    <label for="sstate" class="required">State</label>
                    <?= html_text('sstate', "placeholder='Enter State' required") ?>
                    <?= err("sstate") ?>
                </div>

                <!-- upload profile pic -->
                <div class="input-field">
                    <label for="spic">Profile Picture</label>
                    <div class="custom-file-button">
                        <?= html_file('spic', 'image/*') ?>
                        <label for="spic">Upload Image ... </label>
                    </div>
                    <?= err('spic') ?>
                    <!-- photo preview -->
                    <label id="upload-preview" tabindex="0">
                        <img src="../profilePic/profile.png">
                    </label>
                </div>
                <!-- ----------------------------- -->
                <!-- submit button -->
                <div class="submit-button">
                    <input type="submit" value="Save" name="save" id="save" class="form-button" />
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "admin_footer.php" ?>