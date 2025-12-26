<center style="margin-bottom: 3vw; user-select: none;">
    <?php
        if(isset($_GET['mode']) and $_GET['mode']=="management")
        {
            echo('<a class="header_button" href="?mode=registration">Panel rejestracji</a>');
        } else {
            if(is_admin()) echo('<a class="header_button" href="?mode=management">Panel zarządzania</a>');
        }
    ?>
    <br style="clear: both;"/>
    <h1 id="registration_header" style="font-size: 5vw; margin-bottom: 1vw;">Rejestracja na &nbsp;<span style="background: -webkit-linear-gradient(315deg, rgba(43,79,196,1) 0%, rgba(43,79,196,1) 30%, rgba(40,203,237,1) 100%); -webkit-text-fill-color: white;">&nbsp;warsztaty&nbsp;</span></h1>
    <small>Zalogowano jako <b><?php echo($_SESSION['user']['nameandsurname']); ?></b> (<?php echo($_SESSION['user']['mail']); ?>)</small>
    <br />
</center>