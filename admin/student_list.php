<?php
$_title = "All Students";
require_once '../_base.php';
include 'admin_header.php';

?>

<script>
    // ajax to get user records
    $(document).ready(function() {
        if (performance.navigation.type == 2) {
            location.reload(true);
        }
        getRecord();

        $('#search').keyup(function(e) {
            // Clear the timeout if it has already been set.
            // This will prevent the previous task from executing
            // if it has been less than <MILLISECONDS>
            clearTimeout(timeout);

            timeout = setTimeout(function() {
                getRecord()
            }, 100);
        });

        var Timeout; //For reference to timeout
        var DelayInMs = 800;
        // When input is received, (re)set the timer
        $("#search").keydown(function() {
            if (Timeout) {
                clearTimeout(Timeout);
            } // Clear existing timeout, if any
            Timeout = setTimeout(function() {
                getRecord();
            }, DelayInMs);
        });
    });

    function getRecord() {
        $.ajax({
            type: "POST",
            url: "load-student-record.php",
            data: {
                keyword: $('#search').val(),
                sortBy: $("#sortBy").find(":selected").val(),
                sortOrder: $("#sortOrder").find(":selected").val()
            },
            success: function(data) {
                document.getElementById('table-record').innerHTML = data;
            }
        });
        reloadJS();
    }

    function reloadJS() {
        $('script[src="../js/app.js"]').remove();
        $("head").append("<script type='text/javascript' src='../js/app.js'>");
    }
</script>

<!-- display records in table list -->
<div class="category-content">


    <form class="w-100" action="user_update_status.php" method="post" id="f">
        <div class="record-content">
            <div class="action-btn-wrapper" style="justify-content: space-between;">
                <div class="action-btn-box">
                    <div class="selection-block">
                        <!-- Sort option -->
                        <div class="d-flex-column">
                            <label for="sortBy" class="filter-title">Sort By:</label>
                            <select name="sortBy" id="sortBy" class="selection" onchange="getRecord()">
                                <option value="">Default</option>
                                <option value="studName">Name</option>
                                <option value="studEmail">Email</option>
                                <option value="studState">State</option>
                                <option value="studPhone">Contact</option>
                            </select>

                        </div>

                        <!-- Sort order -->
                        <div class="d-flex-column">
                            <label for="sortOrder" class="filter-title">Sort Order:</label>
                            <?php
                            $sortOrders = getSortOrder();
                            html_select('sortOrder', $sortOrders, "Default", 'class="selection" onchange="getRecord()"');
                            ?>
                        </div>

                    </div>
                    <div class="search-bar">
                        <?= html_text('search', 'placeholder="Search by name / email / address / city / state/ contact ... "') ?>
                        <button type='button'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="btns">
                    <?= html_checkbox('checkAll', 'Select All') ?>

                    <button type="button" class="action-btn green-btn" id="btn-add-new" data-get="add_student.php">Add new</button>

                    <button class="action-btn" id="btn-delete" name="btn-delete" onclick="return confirm('Confirm delete?')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2.5" stroke-linecap="square" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <table class="data-table" id="table-record">
    </table>

</div>

<?php include 'admin_footer.php' ?>