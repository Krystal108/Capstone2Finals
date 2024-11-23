<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $basic_pay = $_POST['basic_pay'];
    $overtime_pay = $_POST['overtime_pay'];
    $late_deduct = $_POST['late_deduct'];
    $sss_deduct = $_POST['sss_deduct'];
    $pagibig_deduct = $_POST['pagibig_deduct'];
    $philhealth_deduct = $_POST['philhealth_deduct'];

    $total_deduct
