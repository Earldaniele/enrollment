<!DOCTYPE html>
<html>
<head>
    <title>Test Redirect</title>
</head>
<body>
    <h3>Testing Redirect</h3>
    <button onclick="testRedirect()">Test Redirect to Dashboard</button>
    
    <script>
    function testRedirect() {
        console.log('Testing redirect...');
        console.log('Current URL:', window.location.href);
        console.log('Target URL: ../student/dashboard.php');
        window.location.href = '../student/dashboard.php';
    }
    </script>
</body>
</html>
