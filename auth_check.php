<?php
require_once 'functions.php';
if (!is_user_active($_SESSION['id'])) {
    logout();
}
