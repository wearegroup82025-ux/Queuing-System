<?php
require_once 'connect1.php';

if (isset($_POST['id'])) {
  $id = intval($_POST['id']);

  $stmt = $conn->prepare("UPDATE register SET status = 'Approved', updated_at = NOW() WHERE ID = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "<script>alert('Student approved successfully!'); window.location='registrar.php';</script>";
  } else {
    echo "<script>alert('Failed to approve student.'); window.location='registrar.php';</script>";
  }

  $stmt->close();
  $conn->close();
} else {
  echo "<script>alert('Invalid request.'); window.location='registrar.php';</script>";
}
?>
