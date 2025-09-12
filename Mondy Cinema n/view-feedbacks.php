<?php
include_once('config.php');

$feedbacks = [];
$sql = "SELECT message, created_at FROM feedbacks ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedbacks - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/view-feedbacks.css">
</head>
<body>
    <div class="feedback-container">
        <a href="index.php" class="back-button">Back</a>
        <h2>Customer Feedbacks</h2>
        <div class="feedback-list">
            <?php if (!empty($feedbacks)): ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback-item">
                        <p class="feedback-message"><?php echo htmlspecialchars($feedback['message']); ?></p>
                        <p class="feedback-date">Submitted on: <?php echo htmlspecialchars($feedback['created_at']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-feedback">No feedbacks submitted yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>