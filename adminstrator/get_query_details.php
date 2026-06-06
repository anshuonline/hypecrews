<?php
require_once 'auth.php';
require_once '../config/db.php';

$query_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$query_id) {
    echo '<p class="text-red-500 font-bold">Invalid query ID</p>';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM service_queries WHERE id = ?");
    $stmt->execute([$query_id]);
    $query = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$query) {
        echo '<p class="text-red-500 font-bold">Query not found</p>';
        exit;
    }
    
    // Format the response as HTML for the modal
    $statusLabels = [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved'
    ];
    
    $statusClasses = [
        'new' => 'bg-yellow-500/10 text-yellow-700 border border-yellow-500/20',
        'in_progress' => 'bg-blue-500/10 text-blue-700 border border-blue-500/20',
        'resolved' => 'bg-green-500/10 text-green-700 border border-green-500/20'
    ];
    
    $html = '
    <div class="space-y-6">
        <div class="bg-white/50 rounded-2xl p-4 border border-black/5 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-apple_muted mb-1">Service</p>
            <p class="font-bold text-lg text-apple_text">' . htmlspecialchars($query['service_name']) . '</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/50 rounded-2xl p-4 border border-black/5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-apple_muted mb-1">Customer</p>
                <p class="font-semibold text-apple_text">' . htmlspecialchars($query['name']) . '</p>
                ' . ($query['company'] ? '<p class="text-sm font-medium text-apple_muted mt-0.5">' . htmlspecialchars($query['company']) . '</p>' : '') . '
            </div>
            
            <div class="bg-white/50 rounded-2xl p-4 border border-black/5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-apple_muted mb-1">Contact</p>
                <p class="font-medium text-primary/80"><a href="mailto:' . htmlspecialchars($query['email']) . '" class="hover:underline">' . htmlspecialchars($query['email']) . '</a></p>
                ' . ($query['phone'] ? '<p class="text-sm font-medium text-apple_muted mt-0.5"><a href="tel:' . htmlspecialchars($query['phone']) . '" class="hover:text-apple_text transition-colors">' . htmlspecialchars($query['phone']) . '</a></p>' : '') . '
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/50 rounded-2xl p-4 border border-black/5 shadow-sm flex flex-col justify-center">
                <p class="text-[11px] font-bold uppercase tracking-wider text-apple_muted mb-2">Status</p>
                <div>
                    <span class="inline-flex items-center px-3 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full ' . $statusClasses[$query['status']] . ' shadow-sm">' . $statusLabels[$query['status']] . '</span>
                </div>
            </div>
            
            <div class="bg-white/50 rounded-2xl p-4 border border-black/5 shadow-sm flex flex-col justify-center">
                <p class="text-[11px] font-bold uppercase tracking-wider text-apple_muted mb-1">Submitted</p>
                <p class="font-medium text-apple_text">' . date('M j, Y g:i A', strtotime($query['created_at'])) . '</p>
            </div>
        </div>
        
        <div class="bg-white/50 rounded-2xl p-4 border border-black/5 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-apple_muted mb-2">Message</p>
            <p class="whitespace-pre-wrap text-apple_text font-medium leading-relaxed">' . nl2br(htmlspecialchars($query['message'])) . '</p>
        </div>
    </div>';
    
    echo $html;
    
} catch (PDOException $e) {
    echo '<p class="text-red-500 font-bold bg-red-50 p-4 rounded-xl">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>