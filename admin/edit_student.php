<?php
$_title = "Edit Student";
require_once "../_base.php";
include "admin_header.php";
// check if the logged in user is superadmin
checkSuperadmin();

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


// get method -- check if the url have id
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if (is_exists($id, 'student', 'studid')) {
        $err = [];
        if (is_post()) {
            $student = $_db->query("SELECT * FROM student WHERE studid = '$id'")->fetch();

            $studName = post("sname") ?? "";
            $studEmail = post("semail") ?? "";
            $studPhone = post("sphone") ?? "";
            $studAddress = post("saddress") ?? "";
            $studCity = post("scity") ?? "";
            $studState = post("sstate") ?? "";

            if ($studName != $student->studName) $_err["sname"] = checkUsername($studName) ?? '';
            if ($studEmail != $student->studEmail) $_err["semail"] = checkRegisterEmail($studEmail) ?? '';
            if ($studPhone != $student->studPhone)  $_err["sphone"] = checkContact($studPhone) ?? '';
            if ($studAddress != $student->studAddress)  $_err["saddress"] = checkAddress($studAddress) ?? '';
            if ($studCity != $student->studCity)  $_err["scity"] = checkCity($studCity) ?? '';
            if ($studState != $student->studState)  $_err["sstate"] = checkState($studState) ?? '';


            // If new profile picture is uploaded
            if (isset($_FILES['spic'])) {
                $file = $_FILES['spic'];
                $_err["spic"] = checkUploadPic($file);

                if (empty($_err["spic"])) {
                    // create a unique id and use it as the new file name
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $newFileName = uniqid() . '.' . $ext;

                    // Upload the new file to S3
                    try {
                        $result = $s3Client->upload(
                            $bucketName,  // S3 bucket name
                            'user-images/' . $newFileName,  // S3 folder path
                            fopen($file['tmp_name'], 'rb') // File resource
                        );

                        // Get the URL of the uploaded file
                        $fileUrl = $result['ObjectURL'];  // Public URL
                    } catch (AwsException $e) {
                        $_err['spic'] = 'Error uploading file to S3: ' . $e->getMessage();
                        $newFileName = $student->studPic; // Fallback to existing file
                    }
                } else {
                    $newFileName = $student->studPic; // Fallback to existing file if no valid upload
                }
            } else {
                $newFileName = $student->studPic; // Keep existing image if no new upload
            }


            $_err = array_filter($_err);

            // store new student record
            if (empty($_err)) {
                $stmt = $_db->prepare("UPDATE student SET studName = ?, studPic = ?, studEmail = ?, studPhone = ?, studAddress = ?, studCity = ?, studState = ? WHERE studid = ?");
                $stmt->execute([$studName, $newFileName, $studEmail, $studPhone, $studAddress, $studCity, $studState, $id]);
                if ($stmt->rowCount() < 1) {
                    sweet_alert_msg("Unable to update details. Please try again.", 'error', null, false);
                } else {
                    //save the file
                    if ($newFileName != $student->studPic) {
                        move_uploaded_file($file['tmp_name'], '../profilePic/' . $newFileName);
                        // delete old pic from file
                        if($student->studPic != null){
                            unlink("../profilePic/$student->studPic");
                        }
                    }
                    sweet_alert_msg('Record update successful', 'success', null, false);
                }
            }
        }
    } else {
        sweet_alert_msg('Student not exist.', 'error', $_SERVER['HTTP_REFERER'], false);
    }
} else {
    sweet_alert_msg("No student selected.", 'error', $_SERVER['HTTP_REFERER'], false);
}

$result = $_db->query("SELECT * FROM student WHERE studid = '$id'");
$s = $result->fetch();

// Generate the S3 URL for the profile picture to fetch the image
$profilePicUrl = $s->studPic ? "https://assm-student-web-bucket.s3.amazonaws.com/user-images/{$s->studPic}" : "../profilePic/profile.png"; 

$sname = $s->studName;
$semail = $s->studEmail;
$sphone = $s->studPhone;
$saddress = $s->studAddress;
$scity = $s->studCity;
$sstate = $s->studState;
?>

<head>
    <link href="../css/login.css" rel="stylesheet" type="text/css" />
    <link href="../css/edit_staff.css" rel="stylesheet" type="text/css" />
</head>

<form method="post" enctype="multipart/form-data">
    <div class="form-box">
        <div class="photo-area">
            <div class="photo">
                <!-- photo preview -->
                <label id="upload-preview" tabindex="0">
                    <?= html_file('spic', 'image/*', 'hidden') ?>
                    <img src="<?= $profilePicUrl ?>" alt="Profile Picture">
                    <span>Upload Profile Picture</span>
                </label>
                <?= err('spic') ?>
            </div>
        </div>
        <div class="data-area">
            <div class="data">
                <div class="input-field">
                    <div>Student ID : <?= $s->studid ?></div>
                </div>
                <div class="input-field">
                    <label for="sname" class="required">Full Name</label>
                    <?= html_text('sname', "placeholder='Enter Full Name'") ?>
                    <?= err("sname") ?>
                </div>
                <div class="input-field">
                    <label for="semail" class="required">Email Address</label>
                    <?= html_text('semail', "placeholder='Enter Email (e.g. xxxx@xxx.xxx)'") ?>
                    <?= err("semail") ?>
                </div>
                <div class="input-field">
                    <label for="sphone" class="required">Mobile</label>
                    <?= html_text('sphone', "placeholder='Enter Mobile Number (e.g. 0123456789)'") ?>
                    <?= err("sphone") ?>
                </div>
                <div class="input-field">
                    <label for="saddress" class="required">Address</label>
                    <?= html_text('saddress', "placeholder='Enter Address'") ?>
                    <?= err("saddress") ?>
                </div>
                <div class="input-field">
                    <label for="scity" class="required">City</label>
                    <?= html_text('scity', "placeholder='Enter City'") ?>
                    <?= err("scity") ?>
                </div>
                <div class="input-field">
                    <label for="sstate" class="required">State</label>
                    <?= html_text('sstate', "placeholder='Enter State'") ?>
                    <?= err("sstate") ?>
                </div>
                <!-- submit button -->
                <div class="submit-button">
                    <input type="submit" class="form-button" value="Save" name="save" id="save" />
                </div>
            </div>
        </div>
    </div>
</form>
<?php include "admin_footer.php" ?>
