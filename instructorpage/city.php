<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'database-config.php';
//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY
error_log("Request ... ");
$query = (string) filter_input(INPUT_GET, 'query');
if (strlen($query) > 2) {
    $sql = "SELECT distinct s.name state, ci.name city FROM countries co INNER JOIN states s ON co.id=s.country_id INNER JOIN cities ci ON s.id = ci.state_id  WHERE co.sortname='US' AND ci.name LIKE '%{$query}%' ORDER BY ci.name LIMIT 15";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $array = array();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $array[] = ['label' => $row['city'] . ', ' . $row['state'],
            'value' => ['city' => $row['city'], 'state' => $row['state']]
        ];
    }
//RETURN JSON ARRAY
    echo json_encode($array);
} else {
    echo '[]';
}
?>