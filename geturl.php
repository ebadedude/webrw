<?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        $url = '';
        $cbk = '';

        if(isset($_GET['url'])) {
                $url = trim($_GET['url']);
        } elseif(isset($_POST['url'])) {
                $url = trim($_POST['url']);
        }

        if(isset($_GET['callback'])) {
                $cbk = trim($_GET['callback']);
        } elseif(isset($_POST['callback'])) {
                $cbk = trim($_POST['callback']);
        }

        if(strlen($cbk) < 1) { $cbk = "callback"; }

        header('Content-type: text/javascript');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        if(strlen($url) > 0) {
                $crl = curl_init();
                curl_setopt($crl, CURLOPT_URL, $url);
                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, 5);
                $rtn = curl_exec($crl);
                curl_close($crl);

                $final_rtn = '';
                $final_rtn_shell = "$cbk({\"content\":||CONTENT||,\"url\":\"$url\",\"date\":\"".date('M-d-Y H:i:s')."\"})";
                $search = array("\r\n", "\n\r", "\n", "\r");
                //$replace = array("\",\"", "\",\"", "\",\"", "\",\"");
                $rtn = str_replace($search, '||DIV||', $rtn);

                $arr_rtn = explode('||DIV||', $rtn);
                $counter = 0;
                foreach($arr_rtn as $val) {
                                $final_rtn .= (($counter > 0)?",":"").json_encode($val);
                                $counter++;
                }
                $final_rtn_shell = str_replace('||CONTENT||', "[".$final_rtn."]", $final_rtn_shell);
                echo $final_rtn_shell;
        }
?>
