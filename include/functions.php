<?php
    function test_admin()
    {
        $_SESSION['user']['is_admin'] = 1; 
        $_SESSION['user']['id'] = "1d239"; 
        $_SESSION['user']['nameandsurname'] = "John Doe Admin"; 
        $_SESSION['user']['mail'] = "john@doe.example";
    }

    
    function db_init()
    {
        include("../include/config.php");
        $dsn = "mysql:host=$db_host;dbname=$db_dbname;charset=utf8;";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, $db_username, $db_password, $options);
    }


    function log_error($content, $error_code)
    {
        $pdo = db_init();
        $db = $pdo->prepare("INSERT INTO errors (error_code, description) VALUES (:error_code, :desc)");
        $db->execute(['error_code' => htmlentities($error_code), 'desc' => htmlentities($content)]);
    }


    function redirect($destination)
    {
        header("Location: $destination");
        echo("<meta http-equiv=\"refresh\" content=\"0; url=$destination\" />");
        die;
    }

    function is_logged_in()
    {
        if(isset($_SESSION['user']))
        {
            return True;
        } else {
            return False;
        }
    }


    function is_admin()
    {
        if(is_logged_in() and $_SESSION['user']['is_admin']==1)
        {
            return True;
        } else {
            return False;
        }
    }


    function new_management_window($color1 = "rgba(43,79,196,1)", $color2 = "rgba(43,79,196,1)", $color3 = "rgba(40,203,237,1)", $header_text_color = "white")
    {
        $background = "-webkit-linear-gradient(315deg, $color1 0%, $color2 50%, $color3 100%)";

        echo('<div class="round">
                <div class="round_header" style="background: '.$background.'; color: '.$header_text_color.';">
                    <h1>Panel zarządzania</h1>
                </div>');
            include("../include/management.php");
        echo('</div>');
    }


    function new_signins_window($id, $title, $color1 = "rgba(43,79,196,1)", $color2 = "rgba(43,79,196,1)", $color3 = "rgba(40,203,237,1)", $header_text_color = "white")
    {
        $background = "-webkit-linear-gradient(315deg, $color1 0%, $color2 50%, $color3 100%)";

        echo('<div class="round">
                <div class="round_header" style="background: '.$background.'; color: '.$header_text_color.';">
                    <h1>'.$title.'</h1>
                </div><ul>');
        $pdo = db_init();
        $db = $pdo->prepare("SELECT * FROM signins WHERE course_id=:course_id");
        $db->execute(['course_id' => $id]);
        
        while($signin = $db->fetch())
        {
            echo('<li>'.$signin['nameandsurname'].'</li>');
        }
        echo('</ul></div>');
    }


    function new_countdown_timer($metadata, $key)
    {
        echo('<center style="margin: 3vw;"><small>Tura otworzy się za:</small><h1 id="countdown_timer_'.$metadata['id'].'">0<small>d</small> 0<small>h</small> 0<small>m</small> 0<small>s</small></h1></center>');
        echo('<script>
        var x'.$metadata['id'].'_countDownDate = new Date("'.$metadata[$key].'").getTime();

        var x'.$metadata['id'].' = setInterval(function() {

        var now = new Date().getTime();
            
        var x'.$metadata['id'].'_distance = x'.$metadata['id'].'_countDownDate - now;
            
        var x'.$metadata['id'].'_days = Math.floor(x'.$metadata['id'].'_distance / (1000 * 60 * 60 * 24));
        var x'.$metadata['id'].'_hours = Math.floor((x'.$metadata['id'].'_distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var x'.$metadata['id'].'_minutes = Math.floor((x'.$metadata['id'].'_distance % (1000 * 60 * 60)) / (1000 * 60));
        var x'.$metadata['id'].'_seconds = Math.floor((x'.$metadata['id'].'_distance % (1000 * 60)) / 1000);
            
        document.getElementById("countdown_timer_'.$metadata['id'].'").innerHTML = x'.$metadata['id'].'_days + "<small>d</small> " + x'.$metadata['id'].'_hours + "<small>h</small> "
        + x'.$metadata['id'].'_minutes + "<small>m</small> " + x'.$metadata['id'].'_seconds + "<small>s</small> ";
            
        if (x'.$metadata['id'].'_distance < 0) {
            clearInterval(x'.$metadata['id'].');
            document.getElementById("countdown_timer_'.$metadata['id'].'").innerHTML = "Odśwież stronę";
            location.reload();
        }
        }, 1000);
        </script>');
    }


    function new_round($id, $title, $color1 = "rgba(43,79,196,1)", $color2 = "rgba(43,79,196,1)", $color3 = "rgba(40,203,237,1)", $header_text_color = "white", $metadata = array())
    {
        $background = "-webkit-linear-gradient(315deg, $color1 0%, $color2 50%, $color3 100%)";

        if(strtotime($metadata['open_time'])>time())
        {
            echo('<div class="round">
            <div class="round_header" style="background: '.$background.'; color: '.$header_text_color.';">
                <h1>'.$title.'</h1>
            </div>
            <br />');
            new_countdown_timer($metadata, 'open_time');
            echo("</div>");
        }
        else if(strtotime($metadata['open_time']) <= time() and time() <= strtotime($metadata['close_time']))
        {
            echo('<div class="round">
            <div class="round_header" style="background: '.$background.'; color: '.$header_text_color.';">
                <h1>'.$title.'</h1>
            </div>
            <br />');
            echo('<div class="round_courses">');

            $pdo = db_init();
            $db = $pdo->prepare("SELECT * FROM signins WHERE user_id=:user_id AND round_id=:round_id");
            $db->execute(['user_id' => $_SESSION['user']['id'], 'round_id' => $id]);

            $users_choice = $db->fetch();

            $db = $pdo->prepare("SELECT * FROM courses WHERE round_id=:round_id");
            $db->execute(['round_id' => $id]);

            while($course = $db->fetch())
            {
                if(!isset($users_choice['course_id']) and $course['available_seats']>0)
                {
                    echo('<div>
                        <h2 style="color: '.$color2.'">'.$course['title'].'</h2>
                        <b>Prowadzący_a: <span style="background: '.$background.'; -webkit-text-fill-color: '.$header_text_color.';">&nbsp;'.$course['leader'].'&nbsp;</span>&emsp;Pozostało: <span style="background: '.$background.'; -webkit-text-fill-color: '.$header_text_color.';">&nbsp;'.$course['available_seats'].' miejsc&nbsp;</span></b>
                        <p>'.$course['description'].'</p>
                        <a href="process.php?call=sign_in&id='.$course['id'].'" class="button">Zapisz się</a>
                    </div>');
                } else if(isset($users_choice['course_id']) and $users_choice['course_id']==$course['id'])
                {
                    echo('<div>
                        <h2 style="color: '.$color2.'"><i class="fa fa-check-circle-o" style="color: green;"></i>&nbsp;&nbsp;'.$course['title'].'</h2>
                        <b>Prowadzący_a: <span style="background: '.$background.'; -webkit-text-fill-color: '.$header_text_color.';">&nbsp;'.$course['leader'].'&nbsp;</span>&emsp;Pozostało: <span style="background: '.$background.'; -webkit-text-fill-color: '.$header_text_color.';">&nbsp;'.$course['available_seats'].' miejsc&nbsp;</span></b>
                        <p>'.$course['description'].'</p>
                        <a href="process.php?call=sign_out&id='.$course['id'].'" class="button">Zrezygnuj</a>
                    </div>');
                } else {
                    echo('<div style="color: gray;">
                        <h2 style="color: gray">'.$course['title'].'</h2>
                        <b>Prowadzący_a: <span style="background: gray; color: white;">&nbsp;'.$course['leader'].'&nbsp;</span>&emsp;Pozostało: <span style="background: gray; color: white;">&nbsp;'.$course['available_seats'].' miejsc&nbsp;</span></b>
                        <p>'.$course['description'].'</p>
                        <center><small><i>Nie możesz zapisać się na ten warsztat.</i></small></center>
                    </div>');
                }
            }
            echo('</div></div>');
        } else {
            echo('<div class="round">
            <div class="round_header" style="background: '.$background.'; color: '.$header_text_color.';">
                <h1>'.$title.'</h1>
            </div>
            <center style="margin: 3vw;">Ta tura została zakończona.<br />
            <small style="color: gray;">Czas trwania tury: od '.$metadata['open_time'].' do '.$metadata['close_time'].'</small>
            </center>
            </div>');
        }
    }
?>