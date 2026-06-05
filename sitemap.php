<?php
require_once 'config/db.php';

header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Static pages
$static_pages = [
    '' => ['priority' => '1.0', 'changefreq' => 'weekly'],
    'about.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'services.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'softwares.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'contact.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'careers.php' => ['priority' => '0.7', 'changefreq' => 'weekly'],
    'privacy-policy.php' => ['priority' => '0.5', 'changefreq' => 'yearly'],
    'terms-conditions.php' => ['priority' => '0.5', 'changefreq' => 'yearly'],
    'refund-policy.php' => ['priority' => '0.5', 'changefreq' => 'yearly']
];

$base_url = 'https://hypecrews.com/';
$today = date('Y-m-d');

foreach ($static_pages as $page => $meta) {
    echo '<url>';
    echo '<loc>' . $base_url . $page . '</loc>';
    echo '<lastmod>' . $today . '</lastmod>';
    echo '<changefreq>' . $meta['changefreq'] . '</changefreq>';
    echo '<priority>' . $meta['priority'] . '</priority>';
    echo '</url>';
}

// Dynamic software pages
try {
    $stmt = $pdo->query("SELECT id, updated_at, created_at FROM softwares");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lastmod = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d', strtotime($row['created_at']));
        echo '<url>';
        echo '<loc>' . $base_url . 'software_details.php?id=' . $row['id'] . '</loc>';
        echo '<lastmod>' . $lastmod . '</lastmod>';
        echo '<changefreq>monthly</changefreq>';
        echo '<priority>0.7</priority>';
        echo '</url>';
    }
} catch (PDOException $e) {
    // Ignore database errors in sitemap to prevent XML corruption
}

echo '</urlset>';
?>
