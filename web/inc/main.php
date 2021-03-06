<?php
// Check user session
if (!isset($_SESSION['user'])) {
    $_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
    header("Location: /login/");
    exit;
}

if (isset($_SESSION['look']) && ( $_SESSION['look'] != 'admin' )) {
    $user = $_SESSION['look'];
} else {
    $user = $_SESSION['user'];
}

define('VESTA_CMD', '/usr/bin/sudo /usr/local/vesta/bin/');

$i = 0;

// Define functions
function check_error($return_var){
    if ( $return_var > 0 ) {
        header("Location: /error/");
        exit;
    }
}

function top_panel($user, $TAB) {
    global $panel;
    $command = VESTA_CMD."v-list-user '".$user."' 'json'";
    exec ($command, $output, $return_var);
    if ( $return_var > 0 ) {
        header("Location: /error/");
        exit;
    }
    $panel = json_decode(implode('', $output), true);
    unset($output);
    if ( $user == 'admin' ) {
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/panel.html');
    } else {
        include($_SERVER['DOCUMENT_ROOT'].'/templates/user/panel.html');
    }
}

function humanize_time($usage) {
    if ( $usage > 60 ) {
        $usage = $usage / 60;
        $usage = number_format($usage, 2);
        $usage = $usage." Hour.";
    } else {
        $usage = $usage." Min.";
    }
    return $usage;
}

function humanize_usage($usage) {
    if ( $usage > 1000 ) {
        $usage = $usage / 1000;
        if ( $usage > 1000 ) {
                $usage = $usage / 1000 ;
                if ( $usage > 1000 ) {
                    $usage = $usage / 1000 ;
                    $usage = number_format($usage, 2);
                    $usage = $usage." pb";
                } else {
                    $usage = number_format($usage, 2);
                    $usage = $usage." tb";
                }
        } else {
            $usage = number_format($usage, 2);
            $usage = $usage." gb";
        }
    } else {
        $usage = $usage." mb";
    }
    return $usage;
}

function get_percentage($used,$total) {
    if (!isset($total)) $total =  0;
    if (!isset($used)) $used =  0;
    if ( $total == 0 ) {
        $percent = 0;
    } else {
        $percent = $used / $total;
        $percent = $percent * 100;
        $percent = number_format($percent, 0, '', '');
        if ( $percent > 100 ) {
            $percent = 100;
        }
        if ( $percent < 0 ) {
            $percent = 0;
        }

    }
    return $percent;
}

function send_email($to,$subject,$mailtext,$from) {
    $charset = "utf-8";
    $to = '<'.$to.'>';
    $boundary = '--' . md5( uniqid("myboundary") );
    $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
    $priority = $priorities[2];
    $ctencoding = "8bit";
    $sep = chr(13) . chr(10);
    $disposition = "inline";
    $subject = "=?$charset?B?".base64_encode($subject)."?=";
    $header = "From: $from \nX-Priority: $priority\nCC:\n";
    $header .= "Mime-Version: 1.0\nContent-Type: text/plain; charset=$charset \n";
    $header .= "Content-Transfer-Encoding: $ctencoding\nX-Mailer: Php/libMailv1.3\n";
    $message = $mailtext;
    mail($to, $subject, $message, $header);
}

function display_error_block() {
    if (!empty($_SESSION['error_msg'])) {
        echo '
                        <script type="text/javascript">
                            $(function() {
                                $( "#dialog:ui-dialog" ).dialog( "destroy" );
                                $( "#dialog-message" ).dialog({
                                    modal: true,
                                    buttons: {
                                        Ok: function() {
                                            $( this ).dialog( "close" );
                                        }
                                    }
                                });
                            });
                    </script>
                    <div id="dialog-message" title="Error">
                        <p>';
        echo $_SESSION['error_msg'];
        echo "</p>\n                        </div>\n";
        unset($_SESSION['error_msg']);
    }
}
?>
