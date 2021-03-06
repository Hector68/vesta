<?php
// Init
error_reporting(NULL);
session_start();
$TAB = 'DNS';
$_SESSION['back'] = $_SERVER['REQUEST_URI'];
include($_SERVER['DOCUMENT_ROOT']."/inc/main.php");

// Header
include($_SERVER['DOCUMENT_ROOT'].'/templates/header.html');

// Panel
top_panel($user,$TAB);

// Data
if ($_SESSION['user'] == 'admin') {
    if (empty($_GET['domain'])){
        exec (VESTA_CMD."v-list-dns-domains $user json", $output, $return_var);
        $data = json_decode(implode('', $output), true);
        $data = array_reverse($data);
        unset($output);
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/list_dns.html');
    } else {
        exec (VESTA_CMD."v-list-dns-domain-records '".$user."' '".$_GET['domain']."' 'json'", $output, $return_var);
        $data = json_decode(implode('', $output), true);
        $data = array_reverse($data);
        unset($output);
        include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/list_dns_rec.html');
    }
} else {
    if (empty($_GET['domain'])){
        exec (VESTA_CMD."v-list-dns-domains $user json", $output, $return_var);
        $data = json_decode(implode('', $output), true);
        $data = array_reverse($data);
        unset($output);
        include($_SERVER['DOCUMENT_ROOT'].'/templates/user/list_dns.html');
    } else {
        exec (VESTA_CMD."v-list-dns-domain-records '".$user."' '".$_GET['domain']."' 'json'", $output, $return_var);
        $data = json_decode(implode('', $output), true);
        $data = array_reverse($data);
        unset($output);
        include($_SERVER['DOCUMENT_ROOT'].'/templates/user/list_dns_rec.html');
    }

}

// Footer
include($_SERVER['DOCUMENT_ROOT'].'/templates/footer.html');
