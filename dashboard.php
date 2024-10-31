<?php
session_start();

// Database connection
$host = 'localhost'; // Change if your database is on a different host
$db = 'facebook'; // Database name
$user = 'root'; // Replace with your database username
$pass = ''; // Replace with your database password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Replace with your own authentication logic
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard with Sidebar</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar styles */
        .sidebar {
            width: 250px;
            background-color: #1877f2;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s;
        }
        .sidebar a i {
            margin-right: 10px; /* Spacing for the icons */
        }
        .sidebar a:hover {
            background-color: #165eab;
        }

        /* Dashboard content styles */
        .dashboard-container {
            flex: 1;
            padding: 20px;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            color: #1877f2;
            font-size: 28px;
        }
        .card-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .card, .table-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            min-width: 200px;
            flex: 1 1 30%;
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover, .table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .card h2, .table-card h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .card-content {
            font-size: 24px;
            font-weight: bold;
            color: #1877f2;
        }
        .table-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-card th, .table-card td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .crud-buttons button {
            margin: 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        .add-btn { background-color: #28a745; }
        .edit-btn { background-color: #ffc107; }
        .delete-btn { background-color: #dc3545; }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="#account-overview"><i class="fas fa-user-circle"></i>Account Overview</a>
        <a href="#messages"><i class="fas fa-envelope"></i>Messages</a>
        <a href="#notifications"><i class="fas fa-bell"></i>Notifications</a>
        <a href="#settings"><i class="fas fa-cog"></i>Account Settings</a>
        <a href="#recent-activity"><i class="fas fa-history"></i>Recent Activity</a>
        <a href="#reports"><i class="fas fa-file-alt"></i>Reports</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome to Facebook</h1>
            <p>Life made easy with Technology</p>
        </div>
        
        <!-- Cards Row -->
        <div class="card-grid">
            <div class="card">
                <h2>Account Overview</h2>
                <p>Profile completion:</p>
                <div class="card-content">80%</div>
            </div>
            <div class="card">
                <h2>Messages</h2>
                <p>New messages:</p>
                <div class="card-content">5</div>
            </div>
            <div class="card">
                <h2>Notifications</h2>
                <p>New notifications:</p>
                <div class="card-content">3</div>
            </div>
        </div>

        <!-- Pie Chart and User Table Row -->
        <div class="card-grid">
            <!-- Pie Chart -->
            <div class="card" style="max-width: 300px;">
                <h2>Analysis Chart</h2>
                <canvas id="pieChart" style="max-height: 200px;"></canvas>
            </div>

            <!-- User Table with CRUD functionality -->
            <div class="table-card">
                <h2>User Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        <tr>
                            <td>John Doe</td>
                            <td>Admin</td>
                            <td class="crud-buttons">
                                <button class="edit-btn" onclick="editUser(this)">Edit</button>
                                <button class="delete-btn" onclick="deleteUser(this)">Delete</button>
                            </td>
                        </tr>
                        <!-- Additional users will be added here -->
                    </tbody>
                </table>
                <button class="add-btn" onclick="addUser()">Add User</button>
            </div>
        </div>
    </div>

    <!-- JavaScript for Pie Chart and CRUD Operations -->
    <script>
        // Pie Chart Data
        const ctx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Profile Completion', 'Messages', 'Notifications'],
                datasets: [{
                    data: [80, 5, 3],
                    backgroundColor: ['#1877f2', '#28a745', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // CRUD operations for User Management Table
        function addUser() {
            const table = document.getElementById('userTable');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>New User</td>
                <td>User</td>
                <td class="crud-buttons">
                    <button class="edit-btn" onclick="editUser(this)">Edit</button>
                    <button class="delete-btn" onclick="deleteUser(this)">Delete</button>
                </td>
            `;
            table.appendChild(row);
        }

        function editUser(button) {
            const row = button.parentElement.parentElement;
            const name = prompt('Enter new name:', row.cells[0].textContent);
            const role = prompt('Enter new role:', row.cells[1].textContent);
            if (name) row.cells[0].textContent = name;
            if (role) row.cells[1].textContent = role;
        }

        function deleteUser(button) {
            const row = button.parentElement.parentElement;
            row.remove();
        }
    </script>
</body>
</html>
