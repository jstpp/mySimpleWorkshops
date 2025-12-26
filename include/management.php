<div class="fast_actions">
    <a class="button" href="?mode=participants">Pobierz listę uczestników</a>
    <a class="button" onClick="document.getElementById('round_add').style.display = 'block';">Dodaj nową turę rejestracji</a>
    <a class="button" id="add_course_button" onClick="document.getElementById('course_add').style.display = 'block';">Dodaj nowe warsztaty</a>

    <br />
    <div class="custom_box_1" id="course_add" style="display: none;">
        <form id="course_add_form" action="process.php?call=add_course" method="POST" style="max-width: 80%; float: left;">
            <p>
                Nazwa warsztatu: <input type="text" placeholder="Nazwa warsztatu" name="course_add_title" required/><br />
                Prowadzący_a: <input type="text" placeholder="Prowadzący_a" name="course_add_leader" required/><br />
                Maks. liczba miejsc: <input type="number" placeholder="10" name="course_add_max_seats" min="0" required/><br />
                Przypisz do tury:
                <select name="course_add_round" required>
                <?php
                    $pdo = db_init();

                    $db = $pdo->prepare("SELECT * FROM rounds");
                    $db->execute();
                    while($round = $db->fetch())
                    {
                        echo('<option value="'.$round['id'].'">'.$round['name'].'</option>');
                    }
                ?>
                </select>
                <br />Opis warsztatu:<br />
                <textarea name="course_add_description" style="width: 40vw;"></textarea>
                <br />
            </p>
        </form>
        <div style="float: right; position: relative; top: 2.5vmax;">
            <a class="button" onClick="document.getElementById('course_add_form').submit();">Dodaj</a>
            <a class="button" onClick="document.getElementById('course_add').style.display = 'none';">Anuluj</a>
        </div>
        <br style="clear: both;"/>
    </div>
    <div class="custom_box_1" id="round_add" style="display: none;">
        <form id="round_add_form" action="process.php?call=add_round" method="POST" style="max-width: 80%; float: left;">
            <p>
                Nazwa tury: <input type="text" placeholder="Nazwa tury" id="round_add_name" name="round_add_name" required/><br />
                Otwarcie rejestracji: <input type="datetime-local" name="round_add_open" required /><br />
                Zamknięcie rejestracji: <input type="datetime-local" name="round_add_close" required /><br />
                Kolorystyka tła tury: <input type="color" value="#2b4fc4" name="round_add_color1"/> <input type="color" value="#2b75c4" name="round_add_color2"/> <input type="color" value="#28cbed" name="round_add_color3"/><br />
                Kolorystyka tekstu nagłówka: <input type="color" value="#ffffff" name="round_add_color4"/>
            </p>
        </form>
        <div style="float: right; position: relative; top: 2.5vmax;">
            <a class="button" onClick="document.getElementById('round_add_form').submit();">Dodaj</a>
            <a class="button" onClick="document.getElementById('round_add').style.display = 'none';">Anuluj</a>
        </div>
        <br style="clear: both;"/>
    </div>
    <br />
    <h2>Utworzone tury rejestracji</h2>
    <?php 
        $pdo = db_init();

        $db = $pdo->prepare("SELECT * FROM rounds");
        $db->execute();
        $count_of_windows=0;
        while($round = $db->fetch())
        {
            $count_of_windows++;
            echo('<div class="custom_box_1">
                <p style="max-width: 80%; float: left;"><b style="color: '.$round['color2'].';">(#'.$round['id'].')&emsp;'.$round['name'].'</b><br />Otwiera się: <b>'.$round['open_time'].'</b><br />Zamyka się: <b>'.$round['close_time'].'</b></p>
                <div style="float: right; position: relative; top: 2.5vmax;">');
            $dbc = $pdo->prepare("SELECT * FROM courses WHERE round_id=:round_id");
            $dbc->execute(['round_id' => $round['id']]);
            if(!isset($dbc->fetch()['id']))
            {
                echo('<a class="button" href="process.php?call=remove_round&id='.$round['id'].'">Usuń</a>');
            }
            echo('    </div>
                <br style="clear: both;"/>
            </div>');
        }
        if($count_of_windows==0)
        {
            echo("Na ten moment nic tu nie ma.");
            echo("<script>document.getElementById('add_course_button').style.display = 'none';</script>");
        }
    ?>
    <h2>Utworzone warsztaty</h2>
    <?php 
        $pdo = db_init();

        $db = $pdo->prepare("SELECT * FROM courses");
        $db->execute();
        $count_of_windows=0;
        while($course = $db->fetch())
        {
            $count_of_windows++;
            echo('<div class="custom_box_1" id="course_info_'.$course['id'].'">
                <p style="max-width: 80%; float: left;"><b style="color: '.$color2.';">'.$course['title'].'</b> (w turze #'.$course['round_id'].')<br />Prowadzący_a: <b>'.$course['leader'].'</b><br />Miejsca: <b>'.$course['available_seats'].'/'.$course['max_seats'].'</b></p>
                <div style="float: right; position: relative; top: 2.5vmax;">
                    <a class="button" class="modify_button" onClick="show_modify_box('.$course['id'].');">Modyfikuj</a>
                    <a class="button" href="process.php?call=remove_course&id='.$course['id'].'">Usuń</a>
                </div>
                <br style="clear: both;"/>
            </div>');
            echo('<div class="custom_box_1" id="course_modify_'.$course['id'].'" style="display: none;">
            <form id="course_modify_form_'.$course['id'].'" action="process.php?call=modify_course&id='.$course['id'].'" method="POST" style="max-width: 80%; float: left;">
                <p>
                    Nazwa warsztatu: <input name="change_title" type="text" value="'.$course['title'].'" required/><br />
                    Prowadzący_a: <input name="change_leader" type="text" value="'.$course['leader'].'" required/><br />
                    Maks. liczba miejsc: <input name="change_seats" type="number" value="'.$course['max_seats'].'" required/><br />
                </p>
            </form>
            <div style="float: right; position: relative; top: 2.5vmax;">
                <a class="button" onClick="document.getElementById(\'course_modify_form_'.$course['id'].'\').submit();">Zatwierdź</a>
                <a class="button" onClick="show_info_box('.$course['id'].');">Anuluj</a>
            </div>
            <br style="clear: both;"/>
            </div>');
        }
        if($count_of_windows==0)
        {
            echo("Na ten moment nic tu nie ma.");
        }
    ?>
    <script>
        function show_modify_box(box_id)
        {
            document.getElementById('course_modify_'+box_id).style.display = 'inherit';
            document.getElementById('course_info_'+box_id).style.display = 'none';
        }
        function show_info_box(box_id)
        {
            document.getElementById('course_modify_'+box_id).style.display = 'none';
            document.getElementById('course_info_'+box_id).style.display = 'inherit';
        }
    </script>
</div>