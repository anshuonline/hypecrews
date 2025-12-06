<?php
// Simple test script for newsletter subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate the newsletter subscription process
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }
    
    // Just return success for testing
    echo json_encode(['status' => 'success', 'message' => 'Test successful!']);
    exit;
}

// Display test form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Newsletter Test</title>
</head>
<body>
    <h2>Newsletter Test Form</h2>
    <form id="testForm">
        <input type="email" id="testEmail" placeholder="Enter email" value="helloanshu.dev@gmail.com" required>
        <button type="submit">Test Subscribe</button>
    </form>
    <div id="result"></div>
    
    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('testEmail').value;
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'newsletter_subscribe.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    document.getElementById('result').innerHTML = 
                        '<p>Status: ' + xhr.status + '</p>' +
                        '<p>Response: ' + xhr.responseText + '</p>';
                }
            };
            
            xhr.send('email=' + encodeURIComponent(email));
        });
    </script>
</body>
</html>