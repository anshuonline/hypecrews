<?php
// patch_old_api.php
header('Content-Type: text/plain');

$files_to_patch = [
    __DIR__ . '/../sync_score.php',
    __DIR__ . '/../save_score.php',
    __DIR__ . '/../sync_meditation.php'
];

$patch_code = "<?php\ndate_default_timezone_set('Asia/Kolkata');\n";

foreach ($files_to_patch as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, "date_default_timezone_set('Asia/Kolkata')") === false) {
            // Replace <?php with our patch
            $content = preg_replace('/<\?php/', $patch_code, $content, 1);
            file_put_contents($file, $content);
            echo "SUCCESS: Patched $file to India Timezone.\n";
        } else {
            echo "SKIPPED: $file is already patched.\n";
        }
    } else {
        echo "NOT FOUND: $file (this is fine if it doesn't exist)\n";
    }
}
echo "\nAll done! Old apps will now sync to 14 June properly.";
?>
