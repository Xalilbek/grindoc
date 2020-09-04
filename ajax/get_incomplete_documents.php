<?php
    require_once '../../class/class.functions.php';
    require_once DIRNAME_INDEX . 'prodoc/model/Dashboard/DashboardFilter.php';
    session_start();
    $userId = $_SESSION['erpuserid'];


    $dashboardFilter = new Model\Dashboard\DashboardFilter($userId);
    $filters = $dashboardFilter->getFiltersByName('natamam_qeydiyyat');

    var_dump($filters['document']);exit();
