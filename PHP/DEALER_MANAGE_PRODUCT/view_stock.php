<?php
require_once '../../PHP/db_connection.php';

if (isset($_GET['stock_id'])) {
    $stockId = intval($_GET['stock_id']);
    $query = "SELECT * FROM stock_table WHERE Stock_ID = $stockId";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            'stock' => $row['Quantity_Available'],
            'min_stock' => $row['Minimum_Stock_Level'],
            'watch_id' => $row['Watch_ID'],
            'last_updated' => $row['Last_Updated']
        ]);
    } else {
        echo json_encode(['error' => 'Stock not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid stock ID']);
}
?>
