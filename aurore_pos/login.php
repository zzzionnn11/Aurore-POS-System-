<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AURORE Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

<div class="login-card">
    <div class="brand-logo">
        <img src="assets/images/logoDesign.png" alt="AURORE Logo">
        <h1>AURORE</h1>
        <p>Please log in using the form below.</p>
    </div>

    <?php
    // Start session and handle login inside body
    session_start();
    if(isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $error = '';
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        require_once 'db_connect.php';
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
        if($result && $result->num_rows == 1){
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
    ?>

    <?php if($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="text-center mt-3 small text-muted">
        <i class="fas fa-question-circle"></i> Coordinate with admin for the account details.
    </div>
</div>

</body>
</html>