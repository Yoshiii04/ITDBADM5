<?php
     header('Content-Type: application/json');

     
    include 'config.php';
    
     $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

     if ($item_id > 0) {
         $sql = "DELETE FROM cart WHERE item_id = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("i", $item_id);
         
         if ($stmt->execute()) {
             echo json_encode(['success' => true]);
         } else {
             echo json_encode(['success' => false, 'error' => 'Database error']);
         }
         $stmt->close();
     } else {
         echo json_encode(['success' => false, 'error' => 'Invalid item ID']);
     }

     $conn->close();
     ?>