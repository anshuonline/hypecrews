<?php
require_once 'auth.php';
require_once '../config/db.php';

$query_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$query_id) {
    echo '<p>Invalid query ID</p>';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM service_queries WHERE id = ?");
    $stmt->execute([$query_id]);
    $query = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$query) {
        echo '<p>Query not found</p>';
        exit;
    }
    
    // Format the response as HTML for the modal
    $statusLabels = [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved'
    ];
    
    $statusClasses = [
        'new' => 'bg-yellow-500/10 text-yellow-500',
        'in_progress' => 'bg-blue-500/10 text-blue-500',
        'resolved' => 'bg-green-500/10 text-green-500'
    ];
    
    $html = '
    <div class="space-y-4">
        <div>
            <p class="text-gray-400 text-sm">Service</p>
            <p class="font-medium">' . htmlspecialchars($query['service_name']) . '</p>
        </div>
        
        <div>
            <p class="text-gray-400 text-sm">Customer</p>
            <p>' . htmlspecialchars($query['name']) . '</p>
            ' . ($query['company'] ? '<p class="text-sm text-gray-400">' . htmlspecialchars($query['company']) . '</p>' : '') . '
        </div>
        
        <div>
            <p class="text-gray-400 text-sm">Contact Information</p>
            <p>' . htmlspecialchars($query['email']) . '</p>
            ' . ($query['phone'] ? '<p class="text-sm text-gray-400">' . htmlspecialchars($query['phone']) . '</p>' : '') . '
        </div>
        
        <div>
            <p class="text-gray-400 text-sm">Status</p>
            <span class="status-badge ' . $statusClasses[$query['status']] . '">' . $statusLabels[$query['status']] . '</span>
        </div>
        
        <div>
            <p class="text-gray-400 text-sm">Message</p>
            <p class="whitespace-pre-wrap">' . nl2br(htmlspecialchars($query['message'])) . '</p>
        </div>
        
        <div>
            <p class="text-gray-400 text-sm">Submitted</p>
            <p>' . date('M j, Y g:i A', strtotime($query['created_at'])) . '</p>
        </div>
    </div>';
    
    echo $html;
    
} catch (PDOException $e) {
    echo '<p>Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>