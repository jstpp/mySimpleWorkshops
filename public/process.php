<?php
    session_start();
    include("../include/functions.php");

    if(isset($_GET['call']) and is_logged_in())
    {
        try {
            if ($_GET['call']=="sign_in" and isset($_GET['id']))
            {
                $pdo = db_init();
                $db = $pdo->prepare("SELECT * FROM courses WHERE id=:course_id");
                $db->execute(['course_id' => $_GET['id']]);
                $course = $db->fetch();
                if(!isset($course['id'])) die;

                $db = $pdo->prepare("UPDATE courses SET available_seats=(available_seats-1) WHERE id=:course_id AND available_seats>0");
                $db->execute(['course_id' => $_GET['id']]);
                if($db->rowCount()>0)
                {
                    $db = $pdo->prepare("INSERT INTO signins (course_id, user_id, round_id, nameandsurname) VALUES (:course_id, :user_id, :round_id, :nameandsurname);");
                    $db->execute(['course_id' => $course['id'], 'user_id' => $_SESSION['user']['id'], 'round_id' => $course['round_id'], 'nameandsurname' => $_SESSION['user']['nameandsurname']]);
                }
                redirect("index.php");

            } else if ($_GET['call']=="sign_out") {
                $pdo = db_init();
                $db = $pdo->prepare("DELETE FROM signins WHERE user_id=:user_id AND course_id=:course_id");
                $db->execute(['course_id' => $_GET['id'], 'user_id' => $_SESSION['user']['id']]);
                $db = $pdo->prepare("UPDATE courses SET available_seats=(available_seats+1) WHERE id=:course_id");
                $db->execute(['course_id' => $_GET['id']]);
                redirect("index.php");
            } else if ($_GET['call']=="add_round" and is_admin()) {
                $pdo = db_init();
                $db = $pdo->prepare("INSERT INTO rounds (name, open_time, close_time, color1, color2, color3, color_title) VALUES (:name, :open_time, :close_time, :color1, :color2, :color3, :color_title)");
                $db->execute(['name' => htmlentities($_POST['round_add_name']), 'open_time' => $_POST['round_add_open'], 'close_time' => $_POST['round_add_close'], 'color1' => $_POST['round_add_color1'], 'color2' => $_POST['round_add_color2'], 'color3' => $_POST['round_add_color3'], 'color_title' => $_POST['round_add_color4']]);
                redirect("index.php?mode=management");
            } else if ($_GET['call']=="remove_round" and is_admin()) {
                $pdo = db_init();
                $db = $pdo->prepare("DELETE FROM rounds WHERE id=:round_id");
                $db->execute(['round_id' => $_GET['id']]);
                redirect("index.php?mode=management");
            } else if ($_GET['call']=="add_course" and is_admin()) {
                $pdo = db_init();
                $db = $pdo->prepare("INSERT INTO courses (round_id, title, leader, max_seats, available_seats, description) VALUES (:round_id, :title, :leader, :max_seats, :available_seats, :description)");
                $db->execute(['round_id' => $_POST['course_add_round'], 'title' => htmlentities($_POST['course_add_title']), 'leader' => htmlentities($_POST['course_add_leader']), 'max_seats' => filter_var($_POST['course_add_max_seats'], FILTER_VALIDATE_INT), 'available_seats' => filter_var($_POST['course_add_max_seats'], FILTER_VALIDATE_INT), 'description' => htmlentities($_POST['course_add_description'])]);
                redirect("index.php?mode=management");
            } else if ($_GET['call']=="remove_course" and is_admin()) {
                $pdo = db_init();
                $db = $pdo->prepare("DELETE FROM signins WHERE course_id=:course_id");
                $db->execute(['course_id' => $_GET['id']]);
                $db = $pdo->prepare("DELETE FROM courses WHERE id=:course_id");
                $db->execute(['course_id' => $_GET['id']]);
                redirect("index.php?mode=management");
            } else if ($_GET['call']=="modify_course" and is_admin()) {
                $pdo = db_init();
                $db = $pdo->prepare("SELECT * FROM courses WHERE id=:course_id");
                $db->execute(['course_id' => $_GET['id']]);
                $course = $db->fetch();
                if(!isset($course['id'])) die;

                $db = $pdo->prepare("UPDATE courses SET title=:title,leader=:leader,max_seats=:max_seats_1,available_seats=((SELECT available_seats FROM courses WHERE id=:course_id_1) + (:max_seats_2 - (SELECT max_seats FROM courses WHERE id=:course_id_2))) WHERE id=:course_id_3");
                $db->execute(['title' => htmlentities($_POST['change_title']), 'leader' => htmlentities($_POST['change_leader']), 'max_seats_1' => filter_var($_POST['change_seats'], FILTER_VALIDATE_INT), 'max_seats_2' => filter_var($_POST['change_seats'], FILTER_VALIDATE_INT), 'course_id_1' => $_GET['id'], 'course_id_2' => $_GET['id'], 'course_id_3' => $_GET['id']]);
                redirect("index.php?mode=management");
            } else {
                http_response_code(404);
                die;
            }
        } catch(Exception $e) {
            $error_code = hash('sha256', time().$_SESSION['user']['id']);
            log_error($e->getMessage(), $error_code);
            echo("Żądanie nie powiodło się. Spróbuj jeszcze raz. Przekierowuję na stronę główną...");
            echo("<br /><small>Kod błędu: ".$error_code."</small>");
            echo("<meta http-equiv=\"refresh\" content=\"5; url=index.php\" />");
        }
    } else {
        include_once("../include/modules/microsoft_auth/auth.php");
        die;
    }
?>