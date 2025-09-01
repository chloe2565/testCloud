<tr class="table-header-row">
    <td class="w-5"></td>
    <td class="w-10">No</td>
    <td class="sortable">Name</td>
    <td class="sortable">Email</td>
    <td>Address</td>
    <td>City</td>
    <td class="sortable">State</td>
    <td class="sortable">Contact</td>
    <td>Action</td>
</tr>

<?php
require_once '../_base.php';
// get parameter

if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
    $keyword = $_POST['keyword'];
}

if (isset($_POST['sortBy']) && $_POST['sortBy'] != '') {
    $sortBy = $_POST['sortBy'];
}

if (isset($_POST['sortOrder']) && $_POST['sortOrder'] != '') {
    $sortOrder = $_POST['sortOrder'];
}

// get record list
$sql = "SELECT * FROM student " . (isset($keyword) ? " WHERE (studName LIKE '%$keyword%' OR studEmail LIKE '%$keyword%' OR studAddress LIKE '%$keyword%' OR studCity LIKE '%$keyword%' OR studState LIKE '%$keyword%' OR studPhone LIKE '%$keyword%') " : '') . " ORDER BY ".(isset($sortBy) ? " $sortBy ". (isset($sortOrder) ? "$sortOrder" : 'ASC') : 'studid');

$studList = $_db->query($sql);
$i = 1;

if ($studList->rowCount() > 0):
    foreach ($studList as $s):
        $checkboxItem = $s->studid;
?>
        <tr>
            <td class="select-item w-5 text-center">
                <?= html_checkbox_group('checkboxItem', '', "form='f' class='checkboxes'") ?>
            </td>
            <td class="w-10"><?= $i++ ?></td>
            <td><?= $s->studName ?></td>
            <td><?= $s->studEmail ?></td>
            <td><?= $s->studAddress ?></td>
            <td><?= $s->studCity ?></td>
            <td><?= $s->studState ?></td>
            <td><?= $s->studPhone ?></td>
            <td>
                <div class="ind-action-btn w-100">
                    <button class="blue-btn" data-get="edit_student.php?id=<?= $s->studid ?>" style='width:100%'>Edit</button>
                </div>
            </td>
        </tr>
    <?php endforeach;
else: ?>
    <!-- no student record found -->
    <tr>
        <td colspan="8" class="text-center"> No Record Found </td>
    </tr>
<?php endif ?>