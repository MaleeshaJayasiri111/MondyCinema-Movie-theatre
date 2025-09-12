<?php
global $conn;
$feedbacks = [];
$sql = "SELECT id, name, email, message, created_at FROM feedbacks ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>
<section class="section">
    <h3 class="section-title">Customer Feedbacks</h3>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($feedbacks)): ?>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['name']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['email']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['message']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="no-data">No feedbacks found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>