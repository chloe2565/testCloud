<?php
$_title = "Add Student";
require_once '../_base.php';
checkSuperadmin();

// Include the AWS SDK for PHP
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// AWS S3 configuration
$bucket = 'assm-image-bucket';
$region = 'us-east-1';

// Connect to S3 (using IAM Role)
try {
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $region,
    ]);
    
    // Test S3 connection
    $result = $s3->listObjectsV2([
        'Bucket' => $bucket,
        'MaxKeys' => 1
    ]);
    error_log("S3 Client connected successfully. Found " . count($result['Contents'] ?? []) . " objects.");
    
} catch (S3Exception $e) {
    error_log("S3 Connection Test Failed: " . $e->getMessage());
    $_err["s3_connection"] = "Unable to connect to S3 service.";
}

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

    // Validate form fields
    $_err["sname"] = checkUsername($sname) ?? '';
    $_err["semail"] = checkRegisterEmail($semail) ?? '';
    $_err["sphone"] = checkRegisterContact($sphone) ?? '';
    $_err["saddress"] = checkAddress($saddress) ?? '';
    $_err["scity"] = checkCity($scity) ?? '';
    $_err["sstate"] = checkState($sstate) ?? '';

    $newFileName = null;
    $s3ObjectURL = null;

    // Handle file upload
    if (isset($_FILES['spic'])) {
        error_log("File upload details:");
        error_log("File name: " . $_FILES['spic']['name']);
        error_log("File type: " . $_FILES['spic']['type']);
        error_log("File size: " . $_FILES['spic']['size']);
        error_log("Temp name: " . $_FILES['spic']['tmp_name']);
        error_log("Upload error code: " . $_FILES['spic']['error']);

        if ($_FILES['spic']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['spic'];
            
            // Validate the uploaded file
            $_err["spic"] = checkUploadPic($file);

            if (empty($_err["spic"]) && !isset($_err["s3_connection"])) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $newFileName = uniqid() . '.' . $ext;

                try {
                    // Read file content
                    $fileContent = file_get_contents($file['tmp_name']);
                    
                    if ($fileContent === false) {
                        throw new Exception("Failed to read uploaded file");
                    }

                    // Upload to S3
                    $result = $s3->putObject([
                        'Bucket' => $bucket,
                        'Key'    => 'user-images/' . $newFileName,
                        'Body'   => $fileContent,
                        'ContentType' => $file['type'],
                        'ACL'    => 'public-read',
                    ]);

                    // Get the S3 URL - Use the correct method to get URL
                    $s3ObjectURL = $s3->getObjectUrl($bucket, 'user-images/' . $newFileName);
                    
                    error_log("File uploaded successfully to S3: " . $s3ObjectURL);

                } catch (S3Exception $e) {
                    error_log("S3 Upload Error: " . $e->getMessage());
                    $_err["spic"] = "S3 Upload failed: " . $e->getMessage();
                    $newFileName = null;
                } catch (Exception $e) {
                    error_log("General Upload Error: " . $e->getMessage());
                    $_err["spic"] = "Upload failed: " . $e->getMessage();
                    $newFileName = null;
                }
            }
        } else {
            // Handle different upload error codes
            switch ($_FILES['spic']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $_err["spic"] = "Uploaded file exceeds the maximum size allowed in php.ini (" . ini_get('upload_max_filesize') . ").";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_err["spic"] = "Uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $_err["spic"] = "The uploaded file was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $_err["spic"] = "Missing a temporary folder for file upload.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $_err["spic"] = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $_err["spic"] = "File upload stopped by extension.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    // No file uploaded - this is okay, profile picture is optional
                    break;
                default:
                    $_err["spic"] = "Unknown file upload error: " . $_FILES['spic']['error'];
                    break;
            }
            
            if ($_FILES['spic']['error'] !== UPLOAD_ERR_NO_FILE) {
                error_log("File upload error: " . $_err["spic"]);
            }
        }
    }

    // Filter out empty errors
    $_err = array_filter($_err);

    // Insert into database if no errors
    if (empty($_err)) {
        try {
            $stmt = $_db->prepare("INSERT INTO student (studName, studPic, studEmail, studPhone, studAddress, studCity, studState) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sname, $newFileName, $semail, $sphone, $saddress, $scity, $sstate]);

            if ($stmt->rowCount() > 0) {
                error_log("Student record inserted successfully. File: " . ($newFileName ?? 'none'));
                sweet_alert_msg('New student record added successfully!', 'success', 'student_list.php', false);
                exit();
            } else {
                $_err[] = "Unable to insert record. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $_err[] = "Database error occurred. Please try again.";
        }
    } else {
        error_log("Form validation errors: " . print_r($_err, true));
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
    
    .error-message {
        color: #d32f2f;
        font-size: 14px;
        margin-top: 5px;
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

                <!-- Upload profile pic -->
                <div class="input-field">
                    <label for="spic">Profile Picture (Optional)</label>
                    <div class="custom-file-button">
                        <?= html_file('spic', 'image/*') ?>
                        <label for="spic">Upload Image ...</label>
                    </div>
                    <?= err('spic') ?>
                    
                    <!-- S3 connection error -->
                    <?php if (isset($_err["s3_connection"])): ?>
                        <div class="error-message"><?= $_err["s3_connection"] ?></div>
                    <?php endif; ?>
                    
                    <!-- Photo preview -->
                    <label id="upload-preview" tabindex="0">
                        <img src="../profilePic/profile.png" alt="Profile Preview">
                    </label>
                </div>
                
                <!-- Submit button -->
                <div class="submit-button">
                    <input type="submit" value="Save" name="save" id="save" class="form-button" />
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "admin_footer.php" ?>