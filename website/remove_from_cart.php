<?php
include 'config.php';
header('Content-Type: application/json');

if (isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];
    $session_id = session_id();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id) {
        $sql = "DELETE FROM cart WHERE item_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $item_id, $user_id);
    } else {
        $sql = "DELETE FROM cart WHERE item_id = ? AND session_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $item_id, $session_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid item ID']);
}
?>