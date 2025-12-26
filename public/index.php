<?php 
    session_start();
    include("../include/functions.php");
    # test_admin(); #Use this function to grant yourself admin permissions without adjusting config file
    if(!is_logged_in())
    {
        redirect("process.php");
        die;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
            include("../include/base.php");
        ?>
        <link href="./static/css/index.css" rel="stylesheet" />
    </head>
    <body>
        <?php
            include("../include/header.php");
        ?>
        <?php
            if(isset($_GET['mode']) and $_GET['mode']=="management" and is_admin())
            {
                new_management_window();
            } else if (isset($_GET['mode']) and $_GET['mode']=="participants" and is_admin()) {
                $pdo = db_init();
                $db = $pdo->prepare("SELECT *, courses.id AS c_id FROM courses INNER JOIN rounds ON courses.round_id=rounds.id;");
                $db->execute();
                $count_of_windows = 0;
                while($course = $db->fetch())
                {
                    new_signins_window($course['c_id'], $course['title'], $course['color1'], $course['color2'], $course['color3'], $course['color_title']);
                    $count_of_windows++;
                }
                if($count_of_windows==0)
                {
                    echo("<center>Na ten moment brak warsztatów! Dodasz je w panelu zarządzania.</center>");
                }
            } else {
                $pdo = db_init();

                $db = $pdo->prepare("SELECT * FROM rounds");
                $db->execute();
                $count_of_windows = 0;
                while($round = $db->fetch())
                {
                    new_round($round['id'], $round['name'], $round['color1'], $round['color2'], $round['color3'], $round['color_title'], $round);
                    $count_of_windows++;
                }
                if($count_of_windows==0)
                {
                    echo("<center>Na ten moment brak warsztatów! Dodasz je w panelu zarządzania.</center>");
                }
            }
        ?>
        <?php
            include("../include/footer.php");
        ?>
    </body>
</html>