<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("config.php");
    
    if (isset($_POST['name'], $_POST['email'], $_POST['message'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        
        $stmt = $conn->prepare("INSERT INTO feedbacks (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            $feedback_message = "Your feedback has been submitted successfully.";
            $success = true;
        } else {
            $feedback_message = "An error occurred. Please try again.";
            $success = false;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Feedback - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/add-feedbacks.css">
</head>
<body>
    <div class="feedback-container">
        <a href="index.php" class="back-button">Back</a>
        <h2>Submit Your Feedback</h2>
        <?php if (isset($feedback_message)): ?>
            <p><?php echo htmlspecialchars($feedback_message); ?></p>
        <?php endif; ?>
        <form action="add-feedback.php" method="POST">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" rows="4" required></textarea><br><br>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>