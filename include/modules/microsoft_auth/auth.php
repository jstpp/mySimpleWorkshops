<?php
    require_once("../include/config.php");
    if(isset($_POST['access_token']) and !isset($_SESSION['ms_mail']))
    {
        $_SESSION['ms_access_token'] = $_POST['access_token'];
        $_SESSION['ms_id_token'] = $_POST['id_token'];

        $ch = curl_init();
        $url = "https://graph.microsoft.com/v1.0/me";

        $headers = [
            'Authorization: '.$_POST['token_type'].' '.$_SESSION['ms_access_token']
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Wystąpił błąd! To smutne. ' . curl_error($ch);
            die;
        } else {
            $data = json_decode($response, true);
            $_SESSION['user']['mail'] = $data['mail'];
            $_SESSION['user']['nameandsurname'] = $data['displayName'];
            $_SESSION['user']['id'] = $data['id'];
            if(strtolower($data['jobTitle'])==strtolower($microsoft_admin_role)) 
            {
                $_SESSION['user']['is_admin'] = 1;
            } else {
                $_SESSION['user']['is_admin'] = 0;
            }
        }
        curl_close($ch);
        redirect("index.php");

    } else if (is_logged_in()){
        echo("<script>console.log('Zalogowano jako ".$_SESSION['user']['mail']."')</script>");
        redirect("index.php");
    } else {
        redirect("https://login.microsoftonline.com/".$microsoft_organization_domain."/oauth2/v2.0/authorize?client_id=".$microsoft_client_id."&response_type=token+id_token&redirect_uri=https%3A%2F%2F".$site_domain."%2Findex.php&scope=user.read+openid+profile+email&response_mode=form_post&state=12345&nonce=678910");
        die();
    }
?>