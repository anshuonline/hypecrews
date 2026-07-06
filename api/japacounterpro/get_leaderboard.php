<?php
// get_leaderboard.php
header('Content-Type: application/json');
require_once 'config.php';

// Fetch Leaderboard Config
$status = 'ACTIVE';
$challenge_start = date('Y-m-01 00:00:00');
$challenge_end = date('Y-m-t 23:59:59');

try {
    $cfg_query = "SELECT status, challenge_start, challenge_end FROM leaderboard_config LIMIT 1";
    $cfg_res = $conn->query($cfg_query);
    if ($cfg_res && $cfg_res->num_rows > 0) {
        $cfg_row = $cfg_res->fetch_assoc();
        $status = $cfg_row['status'];
        $challenge_start = $cfg_row['challenge_start'];
        $challenge_end = $cfg_row['challenge_end'];
    }

} catch (Throwable $e) {
    // If table doesn't exist or column is missing, attempt to create/alter it but suppress errors if it fails
    try {
        $conn->query("ALTER TABLE leaderboard_config ADD COLUMN challenge_end datetime NOT NULL DEFAULT '2099-12-31 23:59:59'");
    } catch (Throwable $e1) {
        // Suppress alter error
    }
    try {
        $conn->query("CREATE TABLE IF NOT EXISTS `leaderboard_config` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `status` enum('ACTIVE','RESULTS','WAITING') NOT NULL DEFAULT 'ACTIVE',
            `challenge_start` datetime NOT NULL,
            `challenge_end` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $conn->query("INSERT IGNORE INTO leaderboard_config (id, status, challenge_start, challenge_end) VALUES (1, 'ACTIVE', '$challenge_start', '$challenge_end')");
    } catch (Throwable $e2) {
        // Silently fail if they don't have CREATE privileges
    }
}

// Auto-lock based on exact start and end times
$current_time = date('Y-m-d H:i:s');
$computed_status = 'ACTIVE';

if ($current_time < $challenge_start) {
    $computed_status = 'WAITING'; // Hasn't started yet
} else if ($current_time > $challenge_end) {
    $computed_status = 'RESULTS'; // Has ended
}

if ($computed_status !== $status) {
    $status = $computed_status;
    try {
        // Map WAITING to RESULTS in DB to lock the app from sending new scores
        $db_status = ($status === 'WAITING') ? 'RESULTS' : $status;
        $conn->query("UPDATE leaderboard_config SET status = '$db_status' WHERE id = 1");
    } catch (Throwable $e) {
    }
}

// YAHAN NAYA LOGIC HAI (Dynamic Monthly Leaderboard):
// Ab hum 'users' table ka lifetime total_counts check nahi karte!
// Hum sirf 'daily_counts' table ko check karte hain aur sirf unhi dates ke counts ko jodh (SUM) karte hain,
// jo Admin ne Monthly Challenge ki Start Date aur End Date mein set kiye hain.
// Isse har mahine leaderboard automatically 0 se start hota hai bina database wipe kiye!
// Fetch top 100 users ordered by dynamic challenge counts
$sql = "SELECT u.username, SUM(dc.daily_count) as total_counts, MAX(u.level) as level 
        FROM users u 
        JOIN daily_counts dc ON u.id = dc.user_id 
        WHERE dc.date >= DATE('$challenge_start') AND dc.date <= DATE('$challenge_end')
        GROUP BY u.id 
        HAVING total_counts > 0
        ORDER BY total_counts DESC LIMIT 100";
$result = $conn->query($sql);

$leaderboard = [];

if ($result->num_rows > 0) {
    $rank = 1;
    while($row = $result->fetch_assoc()) {
        $leaderboard[] = [
            "rank" => $rank,
            "username" => $row['username'],
            "total_counts" => (int)$row['total_counts'],
            "level" => (int)$row['level']
        ];
        $rank++;
    }
}

// Handle user specific rank
$user_rank = null;
$user_counts = null;
$user_level = null;

if (isset($_GET['username']) && !empty($_GET['username'])) {
    $username = trim($_GET['username']);
        // Yeh bhi NAYA LOGIC hai: Jis specific user ne app kholi hai, uska rank bhi current month ke hisaab se nikala jayega
        $stmt = $conn->prepare("SELECT SUM(dc.daily_count) as total_counts, MAX(u.level) as level 
                                FROM users u 
                                JOIN daily_counts dc ON u.id = dc.user_id 
                                WHERE u.username = ? 
                                AND dc.date >= DATE('$challenge_start') AND dc.date <= DATE('$challenge_end')
                                GROUP BY u.id");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $user_row = $res->fetch_assoc();
            $user_counts = (int)$user_row['total_counts'];
            $user_level = (int)$user_row['level'];
            
            $rank_stmt = $conn->prepare("SELECT COUNT(*) + 1 AS rnk FROM (
                                            SELECT SUM(daily_count) as score 
                                            FROM daily_counts 
                                            WHERE date >= DATE('$challenge_start') AND date <= DATE('$challenge_end') 
                                            GROUP BY user_id 
                                            HAVING score > ?
                                         ) as subquery");
            $rank_stmt->bind_param("i", $user_counts);
            $rank_stmt->execute();
            $rank_res = $rank_stmt->get_result();
            if ($rank_res->num_rows > 0) {
                $user_rank = (int)$rank_res->fetch_assoc()['rnk'];
            }
            $rank_stmt->close();
    }
    $stmt->close();
}

echo json_encode([
    "success" => true, 
    "status" => $status,
    "challenge_start" => $challenge_start,
    "challenge_end" => $challenge_end,
    "data" => $leaderboard,
    "user_rank" => $user_rank,
    "user_counts" => $user_counts,
    "user_level" => $user_level
]);

$conn->close();
?>
