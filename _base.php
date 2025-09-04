<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200)
{
    $photo = uniqid() . '.jpg';

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value)
{
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email?
function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// Is date?
function is_date($value, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $value);
    return $d && $d->format($format) == $value;
}

// Is time?
function is_time($value, $format = 'H:i')
{
    $d = DateTime::createFromFormat($format, $value);
    return $d && $d->format($format) == $value;
}

// Return year list items
function get_years($min, $max, $reverse = false)
{
    $arr = range($min, $max);

    if ($reverse) {
        $arr = array_reverse($arr);
    }

    return array_combine($arr, $arr);
}

// Return month list items
function get_months()
{
    return [
        1  => 'January',
        2  => 'February',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];
}

// Return local root path
function root($path = '')
{
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}

// Return base url (host + port)
function base($path = '')
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

// Return TRUE if ALL array elements meet the condition given
function array_all($arr, $fn)
{
    foreach ($arr as $k => $v) {
        if (!$fn($v, $k)) {
            return false;
        }
    }
    return true;
}

// ============================================================================
// HTML Helpers
// ============================================================================
// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}

// Generate <input type='hidden'>
function html_hidden($key, $value = null, $attr = '')
{
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='text'>
function html_text($key, $attr = '', $value = '')
{
    $value = encode($value ?: ($GLOBALS[$key] ?? ''));
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}


// Generate <input type='password'>
function html_password($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='date'>
function html_date($key, $min = '', $max = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='date' id='$key' name='$key' value='$value'
                 min='$min' max='$max' $attr>";
}

// Generate <input type='time'>
function html_time($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='time' id='$key' name='$key' value='$value' $attr>";
}

// Generate <textarea>
function html_textarea($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate SINGLE <input type='checkbox'>
function html_checkbox($key, $label = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 0 ? 'checked' : '';
    $name = $name ?? $key;
    echo "<label style='user-select:none'><input type='checkbox' id='$key' name='$key' value='$value' $status $attr>$label</label>";
}

// Generate SINGLE <input type='checkbox'> for list
function html_checkbox_group($key, $label = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 0 ? 'checked' : '';
    $name = $name ?? $key;
    echo "<label><input type='checkbox' id='$key' name='{$key}[]' value='$value' $status $attr>$label</label>";
}

// Generate <input type='checkbox'> list
function html_checkboxes($key, $items, $br = false)
{
    $values = $GLOBALS[$key] ?? [];
    if (!is_array($values)) $values = [];

    echo '<div>';
    foreach ($items as $id => $text) {
        $state = in_array($id, $values) ? 'checked' : '';
        echo "<label><input type='checkbox' id='{$key}_$id' name='{$key}[]' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <select>
function html_select_group($key, $name, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='{$name}[]' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class

        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<p class='inputError'>$_err[$key]</p>";
    }
}

// ============================================================================
// Security
// ============================================================================

// Global user object
$_userID = $_SESSION['uid'] ?? null;
$_adminID = $_SESSION['admin_id'] ?? null;

// Login user
function login($id, $url = '/')
{
    $_SESSION['uid'] = $id;
    // alert_msg("Log in successful", "/");
    sweet_alert_msg("Log in successful", "success",  "/", true);
}

// Logout user
function logout($url = '/')
{
    unset($_SESSION['uid']);
    redirect($url);
}

// Authorization
function auth(...$roles)
{
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return; // OK
            }
        } else {
            return; // OK
        }
    }

    redirect('/login.php');
}

function checkSuperadmin()
{
    global $_adminID;
    global $_db;
    if ($_adminID) {
        $admin = $_db->query("SELECT * FROM user WHERE uid = $_adminID")->fetch();
        if ($admin->superadmin == 1) {
            return; // OK
        }
    }

    sweet_alert_msg("Missing authentication to this page. ", 'error', 'admin/logout.php', true);
}

function checklogin()
{
    global $_adminID;
    global $_userID;
    global $_db;
    if (empty($_adminID) && empty($_userID)) {
        sweet_alert_msg("Login required",'error' ,'login.php', true);
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// $host = 'assm-db.czi26mueg446.us-east-1.rds.amazonaws.com'; //RDS endpoint
// $dbname = 'studentrecord'; //RDS DB name
// $username = 'admin'; //RDS username
// $password = 'abcd1234'; //RDS password

require 'get_secrets.php';
$creds = getDbCredentials('MyAssmDB'); // name of the secret in AWS Secrets Manager

$host = $creds['host'];
$username = $creds['username'];
$password = $creds['password'];
$dbname = $creds['dbname'];
$port = $creds['port'];

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Global PDO object
try {
    $_db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

//$_db = new PDO('mysql:dbname=studentrecord', 'root', '', [
//    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
//]);

// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Global Constants and Variables
// ============================================================================

function alert_msg($msg, $url = null)
{
    echo "<script>alert('$msg');" . ($url != null ? "window.location.href='$url';" : "") . "</script>";
}

// Sweet Alert Message just for some type of message except confirm message
function sweet_alert_msg($msg, $type = 'success', $url = null, $replace = false) {
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            text: '$msg',
            icon: '$type',
            showConfirmButton: true,
        }).then(() => {
            " . ($url 
                ? ($replace 
                    ? "window.location.replace('$url');" 
                    : "window.location.href='$url';") 
                : "") . "
        });
    });
    </script>";
}

function alert_msg_refresh($msg, $url = null)
{
    $url ??= $_SERVER['PHP_SELF'];
    echo "<script>alert('$msg');" . ($url != null ? "window.location.href='$url';" : "") . "</script>";
}

function createOrder($uid, $cartList, $orderType, $total)
{
    global $_db;

    // Start a transaction
    $_db->beginTransaction();

    try {
        // Insert the order into the database
        $stmt = $_db->prepare("INSERT INTO `order` (uid, type, orderAmount, orderStatus) VALUES (?, ?, ?, 'Processing')");
        $stmt->execute([$uid, $orderType, $total]);

        $orderID = $_db->lastInsertId();

        // Insert order items
        $stmt = $_db->prepare("INSERT INTO order_record (orderID, productID, itemID, qty) VALUES (?, ?, ?, ?)");
        foreach ($cartList as $productID => $details) {
            $itemID = $details['itemID'] ?? null;  // Assuming you have itemID in your cart details
            $stmt->execute([$orderID, $productID, $itemID, $details['qty']]);
        }

        // If everything is successful, commit the transaction
        $_db->commit();

        return $orderID;
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $_db->rollBack();
        throw $e;  // Re-throw the exception for handling at a higher level
    }
}

function getGender()
{
    return array(
        "M" => "Mr.",
        "F" => "Ms."
    );
}

function getSortOrder()
{
    return array(
        "ASC" => "Ascending",
        "DESC" => "Descending"
    );
}

function getSortOptions()
{
    return array(
        "orderID" => "Order ID",
        "orderDate" => "Order Date",
        "orderAmount" => "Price"
    );
}

function checkState($state)
{
    if (empty($state)) {
        return "Please enter the <b>STATE</b>";
    } else if (!preg_match("/^[a-zA-Z\s]+$/", $state)) {
        return "Invalid <b>STATE</b>. Only letters and spaces are allowed.";
    }
    return ""; // Return empty string if validation passes
}

function checkAddress($address)
{
    $address = trim($address);
    if (empty($address)) {
        return "Address is required.";
    }
    if (strlen($address) < 5 || strlen($address) > 100) {
        return "Address must be between 5 and 100 characters long.";
    }
    return "";
}

function checkCity($city)
{
    $city = trim($city);
    if (empty($city)) {
        return "City is required.";
    }
    if (!preg_match("/^[a-zA-Z\s-]+$/", $city)) {
        return "City name can only contain letters, spaces, and hyphens.";
    }
    return "";
}

function checkPickUpDate($date)
{
    $date = trim($date);
    if (empty($date)) {
        return "Pick-up date is required.";
    }
    $inputDate = strtotime($date);
    $currentDate = strtotime(date('Y-m-d'));
    $maxDate = strtotime('+30 days', $currentDate);

    if ($inputDate < $currentDate) {
        return "Pick-up date cannot be in the past.";
    }
    if ($inputDate > $maxDate) {
        return "Pick-up date cannot be more than 30 days in the future.";
    }
    return "";
}

function strongPass($password)
{
    if (!preg_match('/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{8,12}$/', $password)) {
        return "Password must have contains : 
        <ul class='inputError'>
        <li>a minimum of 8 characters</li>
        <li>at least one uppercase letter</li>
        <li>at least one number (digit)</li>
        <li>at least one of the following special characters !@#$%^&*-</li>
        </ul>";
    }
}

function checkUsername($name)
{
    if ($name == null) {
        return "Please enter a name.";
    } else if (strlen($name) > 50) {
        return "The name should not exceeds 50 characters.";
    } else if (!preg_match("/^[A-Za-z @,\.\'\/]+$/", $name)) {
        return "Name with invalid character(s).";
    }
}

function checkEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    } else if ($email == null) {
        return "Please fill in your email";
    }
}

function checkRegisterEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    } else if ($email == null) {
        return "Please fill in your email";
    } else {
        // check duplication
        global $_db;
        $duplicateEmail = $_db->query("SELECT * FROM user WHERE level = '0' AND email = '$email'");
        if ($duplicateEmail->rowCount() > 0) {
            return 'Email has been registered.';
        }
    }
}

function checkRegisterContact($contnum)
{
    // erase characters other than number 
    $cleaned_contact_number = preg_replace('/[^0-9]/', '', $contnum);

    if (strlen($cleaned_contact_number) < 10 || strlen($cleaned_contact_number) > 11) {
        return "Mobile number should have 10 - 11 numbers.";
    } else if (substr($contnum, 0, 2) != "01") {
        return "Incorrect format (Correct format e.g 0123456789)";
    } else {
        if ((substr($contnum, 0, 3) == "011" && strlen($contnum) != 11) && (substr($contnum, 0, 3) != "011" && strlen($contnum != 10))) {
            return "Invalid length of contact number entered. ";
        }

        // check duplicate
        global $_db;
        $result = $_db->query("SELECT * FROM user WHERE level = '1' AND contact = '$contnum'");
        if ($result->rowCount() > 0) {
            return "This phone number has been registered. ";
        }
    }
}

function checkContact($contnum)
{
    // erase characters other than number 
    $cleaned_contact_number = preg_replace('/[^0-9]/', '', $contnum);

    if (strlen($cleaned_contact_number) < 10 || strlen($cleaned_contact_number) > 11) {
        return "Mobile number should have 10 - 11 numbers.";
    } else if (substr($contnum, 0, 2) != "01") {
        return "Incorrect format (Correct format e.g 0123456789)";
    } else if ((substr($contnum, 0, 3) == "011" && strlen($contnum) != 11) && (substr($contnum, 0, 3) != "011" && strlen($contnum != 10))) {
        return "Invalid length of contact number entered. ";
    }
}

function checkUploadPic($file)
{
    if ($file['error'] > 0) {
        //WITH ERROR, handle to display error msg
        switch ($file['error']) {
            case UPLOAD_ERR_NO_FILE:
                break;
            case UPLOAD_ERR_FORM_SIZE:
                return "File uploaded is too large. Maximum 1MB allowed!";
                break;
            default: //other error
                return "There was an error when uploading the file!";
                break;
        }
    } else if ($file['size'] > 1048576) {
        //Validate specifically, file size
        //1MB = 1024 x 1024
        return "File uploaded is too large. Max 1MB allowed!";
    } else {
        //extract file extension, eg: png, jpg, gif
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        //check file extension
        if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png') {
            return "Only accept PJG, JPEG, GIF and PNG format.";
        }
    }
}

function generate_password($length = 8)
{
    $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
        '0123456789!@#$%^&*-';

    $special = "!@#$%^&*-";

    $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    $number = "0123456789";

    $str = '';

    for ($i = 0; $i < $length; $i++)
        $str .= $chars[random_int(0, strlen($chars) - 1)];

    $str .= $special[random_int(0, strlen($special) - 1)];
    $str .= $upper[random_int(0, strlen($upper) - 1)];
    $str .= $number[random_int(0, strlen($number) - 1)];

    return $str;
}

