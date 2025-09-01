<?php
require_once '../_base.php';
$_title = "Homepage";
$_pageTitle = "Admin Dashboard";
include 'admin_header.php';

$hotSales = $_db->query("SELECT p.*, IFNULL((SELECT SUM(qty) FROM `order` o JOIN order_record r ON o.orderID = r.orderID WHERE productID = p.productID AND DATE(o.orderDate) = CURRENT_DATE()), 0) totalSales FROM product p WHERE status = '1' HAVING totalSales > 0 ORDER BY totalSales DESC LIMIT 5");

$monlySales = $_db->query("SELECT p.*, IFNULL((SELECT SUM(qty) FROM `order` o JOIN order_record r ON o.orderID = r.orderID WHERE productID = p.productID AND Month(o.orderDate) = Month(CURRENT_DATE())), 0) totalSales FROM product p WHERE status = '1' HAVING totalSales > 0 ORDER BY totalSales DESC LIMIT 5");

$recentOrder = $_db->query("SELECT * FROM `order` WHERE DATE(orderDate) = CURRENT_DATE;");
?>

<head>
    <link href="../css/admin_homepage.css" rel="stylesheet" type="text/css" />
</head>

<div class="items-data">
    <h3 class="section-title">Today Orders</h3>
    <?php if ($recentOrder->rowCount() > 0): ?>
        <div class="analysis-data-list">
            <table class="data-table" id="table-record">
                <tr class="table-header-row">
                    <td class="w-10">Order ID</td>
                    <td>Member Name</td>
                    <td>Order Date</td>
                    <td>Status</td>
                </tr>
                <?php foreach ($recentOrder as $record) :
                    $student = $_db->query("SELECT * FROM user WHERE uid = '$record->uid'")->fetch();
                ?>
                    <tr class="hp-order-record" onclick="window.location.href='edit_order.php?id=<?= $record->orderID?>'">
                        <td><?= $record->orderID ?></td>
                        <td><?= $student->uname ?></td>
                        <td><?= $record->orderDate ?></td>
                        <td><?= $record->orderStatus ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    <?php else: ?>
        <div class="d-flex-center">No order received</div>
    <?php endif ?>
</div>

<div class="trending-item">

    <div class="items-data">
        <h3 class="section-title">Daily Trending Items</h3>
        <?php if ($hotSales->rowCount() > 0): ?>
            <div class="analysis-data-list">
                <table class="data-table" id="table-record">
                    <tr class="table-header-row">
                        <td colspan="2">Item</td>
                        <td>Today Sales</td>
                        <td>Total Income</td>
                    </tr>
                    <?php foreach ($hotSales as $r) :
                        $pic = $_db->query("SELECT pic FROM product_image WHERE productID = '$r->productID' LIMIT 1")->fetch();
                    ?>
                        <tr>
                            <td><img src="../productImg/<?= $pic->pic ?>"></td>
                            <td><?= $r->name ?></td>
                            <td><?= $r->totalSales ?></td>
                            <td>RM <?= sprintf("%.2f", $r->price * $r->totalSales) ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        <?php else: ?>
            <div class="d-flex-center">No record found</div>
        <?php endif ?>
    </div>

    <div class="items-data">
        <h3 class="section-title"><?= date('F') ?> Trending Items</h3>
        <?php if ($monlySales->rowCount() > 0): ?>
            <div class="analysis-data-list">
                <table class="data-table" id="table-record">
                    <tr class="table-header-row">
                        <td colspan="2">Item</td>
                        <td>Total Sales</td>
                        <td>Total Income</td>
                    </tr>
                    <?php foreach ($monlySales as $record) :
                        $pic = $_db->query("SELECT pic FROM product_image WHERE productID = '$record->productID' LIMIT 1")->fetch();
                    ?>
                        <tr>
                            <td><img src="../productImg/<?= $pic->pic ?>"></td>
                            <td><?= $record->name ?></td>
                            <td><?= $record->totalSales ?></td>
                            <td>RM <?= sprintf("%.2f", $record->price * $record->totalSales) ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        <?php else: ?>
            <div class="d-flex-center">No record found.</div>
        <?php endif ?>
    </div>
</div>

<?php include "admin_footer.php"?>