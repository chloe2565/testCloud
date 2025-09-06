<?php
$_title = "Add Student";
require_once '../_base.php';
checkSuperadmin();

include 'admin_header.php';
if (isset($_POST['cancel'])) {
    echo "<script>window.location.href = 'display_staff.php';</script>";
}

$_err = [];

// AWS SDK Setup
require '../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

// Initialize S3 client
$s3Client = new S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1', 
]);

// Bucket name in S3
$bucketName = 'student-web-bucket';  

if (is_post()) {
    // get data filled in the form
    $sname = post("sname") ?? "";
    $semail = post("semail") ?? "";
    $sphone = post("sphone") ?? "";
    $saddress = post("saddress") ?? "";
    $scity = post("scity") ?? "";
    $sstate = post("sstate") ?? "";

    // validate data
    $_err["sname"] = checkUsername($sname) ?? '';
    $_err["semail"] = checkRegisterEmail($semail) ?? '';
    $_err["sphone"] = checkRegisterContact($sphone) ?? '';
    $_err["saddress"] = checkAddress($saddress) ?? '';
    $_err["scity"] = checkCity($scity) ?? '';
    $_err["sstate"] = checkState($sstate) ?? '';

    // file upload
    if (isset($_FILES['spic'])) {
        $file = $_FILES['spic'];
        $_err["spic"] = checkUploadPic($file);

        // no error
        if (empty($_err["spic"])) {
            // everything okay, save the file
            // create a unique id and use it as file name
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFileName = isset($_FILES["spic"]) && !empty($_FILES["spic"] && !empty($ext)) ? uniqid() . '.' . $ext : null;

            // Upload the image to S3
            try {
                // Upload the image file to S3 in 'user-images' folder
                $result = $s3Client->upload(
                    $bucketName,  // S3 bucket name
                    'user-images/' . $newFileName,  // Folder path in the bucket
                    fopen($file['tmp_name'], 'rb') // File resource
                );

                // Get the URL of the uploaded file
                $fileUrl = $result['ObjectURL'];  // This is the public URL

            } catch (AwsException $e) {
                $_err['spic'] = 'Error uploading file to S3: ' . $e->getMessage();
                $newFileName = null;
            }

		
        } else {
            $newFileName = null;
        }
    }

    $_err = array_filter($_err);

    // no error then store new student record
    if (empty($_err)) {
        $stmt = $_db->prepare("INSERT INTO student (studName, studPic, studEmail, studPhone, studAddress, studCity, studState) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$sname, $newFileName, $semail, $sphone, $saddress, $scity, $sstate]);

        if ($stmt->rowCount() > 0) {
            // success
            
            alert_msg('New student record added successfully!', 'student_list.php');
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
