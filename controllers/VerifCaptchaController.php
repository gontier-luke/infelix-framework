<?php

class VerifCaptchaController extends ControllerCore
{
    protected string $name;

    public function run(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //cle secrete de notre captcha
            $secret = '6LfuDd0pAAAAADNCaKGxBszOE7Gq_sbrMRUOfJI9';
            $response = $_POST['g-recaptcha-response'];
            $remoteip = $_SERVER['REMOTE_ADDR'];
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array(
                'secret' => $secret,
                'response' => $response,
                'remoteip' => $remoteip
            );

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $resultJson = json_decode($result);
            if (!$resultJson->success) {
                var_dump("TEST1");
                echo 0;
            } else {
                $_SESSION['VerifCapcha'] = true;
                echo 1;
            }
        } else {
            var_dump($_SERVER['REQUEST_METHOD']);
            var_dump($_SERVER);
            var_dump("TEST2");
            echo 0;
        }
        return true;
    }
}