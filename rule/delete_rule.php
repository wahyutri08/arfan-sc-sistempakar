<?php
session_start();
include_once("../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-403.html");
    exit;
}

$id_rule = $_GET["id_rule"];

if (deleteRule($id_rule) > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
exit;
