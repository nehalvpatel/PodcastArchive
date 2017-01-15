<!DOCTYPE html>
<html>
    <head>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <style>
            .addTimestamp {
                background: #3498db;
            }
            .addTimeline {
                background: #2ecc71;
            }
            .updateTimestamp {
                background: #f1c40f;
            }
            .deleteTimestamp {
                background: #c0392b;
            }
            table {
                margin-top: 10px;
            }
            td, th {
                padding: 5px;
            }
            html {
                font-family: 'Open Sans', sans-serif;
            }
        </style>
    </head>
    <body>
<?php

require_once("../../config.php");

if (!isset($_SESSION["username"])) {
    echo "You are not logged in.";
} else {
    $log_count_query = $con->prepare("SELECT COUNT(*) FROM `log`");
    $log_count_query->execute();
    $log_count = $log_count_query->fetchColumn();

    $logs_query = $con->prepare("SELECT * FROM `log` ORDER BY `id` DESC LIMIT :limit OFFSET :offset");

    // How many items to list per page
    $limit = 100;

    // How many pages will there be
    $page_count = ceil($log_count / $limit);

    // What page are we currently on?
    $current_page = min($page_count, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));

    // Calculate the offset for the query
    $offset = ($current_page - 1)  * $limit;

    // Some information to display to the user
    $start = $offset + 1;
    $end = min(($offset + $limit), $log_count);

    $last_page = $page_count;

    $logs_query->bindParam(":limit", $limit, PDO::PARAM_INT);
    $logs_query->bindParam(":offset", $offset, PDO::PARAM_INT);
    $logs_query->execute();

    $logs = array();
    $users = array();
    if ($logs_query->rowCount() > 0) {
        $logs = $logs_query->fetchAll();

        $users_query = $con->prepare("SELECT * FROM `admins`");
        $users_query->execute();
        foreach ($users_query->fetchAll() as $user) {
            $users[$user["ID"]] = $user["Username"];
        }
    }

    if ($current_page > 1) {
        echo "<a href='/api/log.php?page=" . ($current_page - 1) . "'>Previous</a>";
    } else {
        echo "Previous";
    }

    echo " ";

    if ($current_page < $page_count) {
        echo "<a href='/api/log.php?page=" . ($current_page + 1) . "'>Next</a>";
    } else {
        echo "Next";
    }

?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Reference ID</th>
                    <th>Previous Value</th>
                    <th>New Value</th>
                </tr>
            </thead>
            <tbody>
<?php
    foreach ($logs as $log) {
?>
                <tr>
                    <td><?php echo $log["id"]; ?></td>
                    <td><?php echo $log["time"]; ?></td>
                    <td><?php echo $users[$log["user"]]; ?></td>
                    <td class="<?php echo $log['action']; ?>"><?php echo $log["action"]; ?></td>
                    <td><?php echo $log["reference_id"]; ?></td>
                    <td><textarea><?php echo $log["previous_value"]; ?></textarea></td>
                    <td><textarea><?php echo $log["new_value"]; ?></textarea></td>
                </tr>
<?php
    }
?>
            </tbody>
        </table>
<?php
}
?>
    </body>
</html>