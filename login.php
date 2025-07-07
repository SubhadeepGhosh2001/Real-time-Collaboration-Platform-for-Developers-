<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php'; // Ensure this file establishes $conn with mysqli_connect

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index");
    ob_end_flush(); // Flush output buffer and end
    exit();
}

$error = '';
$success = '';

// Handle POST login logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic input validation
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check database connection
        if (!$conn) {
            $error = "Database connection failed.";
        } else {
            // Prepare and execute query
            $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            if ($stmt === false) {
                $error = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("s", $email);
                if (!$stmt->execute()) {
                    $error = "Execution failed: " . $stmt->error;
                } else {
                    $result = $stmt->get_result();
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        if (password_verify($password, $user['password'])) {
                            $_SESSION['logged_in'] = true;
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['name'] = $user['name'];
                            $_SESSION['email'] = $user['email'];
                            // Redirect after setting session
                            header("Location: index");
                            ob_end_flush(); // Flush output buffer and end
                            exit();
                        } else {
                            $error = "Incorrect password.";
                        }
                    } else {
                        $error = "No account found with this email.";
                    }
                }
                $stmt->close();
            }
        }
    }
    $conn->close(); // Close connection after use
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Sync - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #0f172a;
            color: white;
            overflow-x: hidden;
        }

        .container {
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .background {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .particles {
            position: absolute;
            inset: 0;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background-color: rgba(34, 197, 94, 0.2);
            border-radius: 50%;
            animation: pulse 3s infinite;
        }

        .particle:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; top: 80%; animation-delay: 0.5s; }
        .particle:nth-child(3) { left: 30%; top: 40%; animation-delay: 1s; }
        .particle:nth-child(4) { left: 40%; top: 70%; animation-delay: 1.5s; }
        .particle:nth-child(5) { left: 50%; top: 10%; animation-delay: 2s; }
        .particle:nth-child(6) { left: 60%; top: 90%; animation-delay: 2.5s; }
        .particle:nth-child(7) { left: 70%; top: 30%; animation-delay: 0.3s; }
        .particle:nth-child(8) { left: 80%; top: 60%; animation-delay: 0.8s; }
        .particle:nth-child(9) { left: 90%; top: 15%; animation-delay: 1.3s; }
        .particle:nth-child(10) { left: 15%; top: 50%; animation-delay: 1.8s; }
        .particle:nth-child(11) { left: 25%; top: 25%; animation-delay: 0.2s; }
        .particle:nth-child(12) { left: 35%; top: 85%; animation-delay: 0.7s; }
        .particle:nth-child(13) { left: 45%; top: 45%; animation-delay: 1.2s; }
        .particle:nth-child(14) { left: 55%; top: 75%; animation-delay: 1.7s; }
        .particle:nth-child(15) { left: 65%; top: 5%; animation-delay: 2.2s; }
        .particle:nth-child(16) { left: 75%; top: 95%; animation-delay: 0.4s; }
        .particle:nth-child(17) { left: 85%; top: 35%; animation-delay: 0.9s; }
        .particle:nth-child(18) { left: 95%; top: 65%; animation-delay: 1.4s; }
        .particle:nth-child(19) { left: 5%; top: 55%; animation-delay: 1.9s; }
        .particle:nth-child(20) { left: 12%; top: 12%; animation-delay: 2.4s; }

        .gradient-orb {
            position: absolute;
            width: 384px;
            height: 384px;
            border-radius: 50%;
            filter: blur(96px);
            animation: pulse 4s infinite;
        }

        .orb-1 {
            top: 25%;
            left: 25%;
            background-color: rgba(34, 197, 94, 0.1);
        }

        .orb-2 {
            bottom: 25%;
            right: 25%;
            background-color: rgba(16, 185, 129, 0.1);
            animation-delay: 1s;
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(34, 197, 94, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 197, 94, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: pulse 6s infinite;
        }

        .content {
            position: relative;
            z-index: 10;
            padding: 2rem;
            min-height: 100vh;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            min-height: 100vh;
            max-width: 1200px;
            margin: 0 auto;
        }

        .illustration-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .illustration {
            width: 384px;
            height: 384px;
            position: relative;
        }

        .desk-shadow {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 320px;
            height: 16px;
            background-color: #334155;
            border-radius: 50%;
            filter: blur(8px);
            opacity: 0.5;
        }

        .desk {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%) perspective(1000px) rotateX(12deg);
            width: 288px;
            height: 128px;
            background: linear-gradient(135deg, #475569, #334155);
            border-radius: 8px;
        }

        .monitor {
            position: absolute;
            background-color: #1e293b;
            border: 2px solid #475569;
            border-radius: 4px;
        }

        .monitor-1 {
            bottom: 80px;
            left: 32px;
            width: 96px;
            height: 64px;
            transform: rotate(-12deg);
        }

        .monitor-2 {
            bottom: 96px;
            left: 128px;
            width: 112px;
            height: 72px;
            transform: rotate(6deg);
        }

        .screen {
            width: 100%;
            height: 100%;
            background-color: rgba(34, 197, 94, 0.2);
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .screen .icon {
            width: 24px;
            height: 24px;
            color: #22c55e;
            stroke-width: 2;
        }

        .laptop {
            position: absolute;
            bottom: 64px;
            right: 32px;
            width: 128px;
            height: 80px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 8px;
            transform: rotate(12deg);
        }

        .laptop-screen {
            width: calc(100% - 8px);
            height: 75%;
            background-color: #0f172a;
            border-radius: 6px 6px 0 0;
            margin: 4px 4px 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .laptop-screen .icon {
            width: 24px;
            height: 24px;
            color: #22c55e;
            stroke-width: 2;
        }

        .character {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
        }

        .character .head {
            width: 48px;
            height: 64px;
            background: linear-gradient(to bottom, #fda4af, #fb7185);
            border-radius: 24px 24px 0 0;
        }

        .character .body {
            width: 64px;
            height: 48px;
            background-color: white;
            border-radius: 8px;
            margin-top: -8px;
            margin-left: -8px;
        }

        .character .legs {
            width: 80px;
            height: 64px;
            background-color: #475569;
            border-radius: 8px;
            margin-top: -4px;
            margin-left: -16px;
        }

        .floating-element {
            position: absolute;
            background-color: #22c55e;
            border-radius: 8px;
        }

        .element-1 {
            top: 32px;
            right: 48px;
            width: 32px;
            height: 32px;
            animation: bounce 2s infinite;
            animation-delay: 0.5s;
        }

        .element-2 {
            top: 64px;
            left: 64px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #10b981;
            animation: bounce 2s infinite;
            animation-delay: 1s;
        }

        .content-section {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .network-icon {
            position: relative;
            width: 48px;
            height: 48px;
            color: #22c55e;
        }

        .network-icon svg {
            width: 100%;
            height: 100%;
            stroke-width: 2;
        }

        .ping-effect {
            position: absolute;
            inset: 0;
            color: #22c55e;
            opacity: 0.2;
            animation: ping 2s infinite;
        }

        .ping-effect svg {
            width: 100%;
            height: 100%;
            stroke-width: 2;
        }

        .logo h1 {
            font-size: 3rem;
            font-weight: bold;
            color: white;
        }

        .highlight {
            color: #22c55e;
        }

        .tagline {
            color: #cbd5e1;
            font-size: 1.125rem;
        }

        .form-card {
            background-color: rgba(30, 41, 59, 0.5);
            border: 1px solid #475569;
            border-radius: 8px;
            backdrop-filter: blur(8px);
        }

        .form-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-group label {
            color: #cbd5e1;
            font-size: 0.875rem;
        }

        .input-group input {
            background-color: rgba(51, 65, 85, 0.5);
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 0.75rem;
            color: white;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .input-group input::placeholder {
            color: #94a3b8;
        }

        .input-group input:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .error-message, .success-message {
            font-size: 0.875rem;
            text-align: center;
        }

        .error-message {
            color: #f87171;
        }

        .success-message {
            color: #22c55e;
        }

        .login-btn {
            width: 100%;
            background-color: #22c55e;
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .login-btn:hover:not(:disabled) {
            background-color: #16a34a;
            transform: scale(1.05);
        }

        .login-btn:disabled {
            background-color: #374151;
            cursor: not-allowed;
            transform: none;
        }

        .register-link {
            text-align: center;
        }

        .register-link a {
            color: #22c55e;
            text-decoration: underline;
            font-size: 0.875rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .register-link a:hover {
            color: #16a34a;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding-top: 1rem;
        }

        .feature {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            color: #22c55e;
            stroke-width: 2;
        }

        .feature p {
            color: #cbd5e1;
            font-size: 0.875rem;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                transform: translate3d(0, -30px, 0);
            }
            70% {
                transform: translate3d(0, -15px, 0);
            }
            90% {
                transform: translate3d(0, -4px, 0);
            }
        }

        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .illustration {
                width: 300px;
                height: 300px;
            }
            
            .logo h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }
            
            .illustration {
                width: 250px;
                height: 250px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
            
            .features {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        .button-wrapper {
            margin-top: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="background">
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="grid-pattern"></div>
        </div>

        <div class="content">
            <div class="main-grid">
                <div class="illustration-container">
                    <div class="illustration">
                        <div class="desk-shadow"></div>
                        <div class="desk"></div>
                        <div class="monitor monitor-1">
                            <div class="screen">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="16,18 22,12 16,6"></polyline>
                                    <polyline points="8,6 2,12 8,18"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="monitor monitor-2">
                            <div class="screen">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="laptop">
                            <div class="laptop-screen">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="m22 21-3-3"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="character">
                            <div class="head"></div>
                            <div class="body"></div>
                            <div class="legs"></div>
                        </div>
                        <div class="floating-element element-1"></div>
                        <div class="floating-element element-2"></div>
                    </div>
                </div>

                <div class="content-section">
                    <div class="header">
                        <div class="logo">
                            <div class="network-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"></path>
                                    <path d="M12 1v6m0 6v6"></path>
                                    <path d="m21 9-6 6-6-6"></path>
                                    <path d="m21 15-6-6-6 6"></path>
                                    <path d="M12 1a8 8 0 0 1 8 8"></path>
                                    <path d="M12 1a8 8 0 0 0-8 8"></path>
                                </svg>
                                <div class="ping-effect">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"></path>
                                        <path d="M12 1v6m0 6v6"></path>
                                        <path d="m21 9-6 6-6-6"></path>
                                        <path d="m21 15-6-6-6 6"></path>
                                        <path d="M12 1a8 8 0 0 1 8 8"></path>
                                        <path d="M12 1a8 8 0 0 0-8 8"></path>
                                    </svg>
                                </div>
                            </div>
                            <h1>Code <span class="highlight">Sync</span></h1>
                        </div>
                        <p class="tagline">Code, Chat and Collaborate. It's All in Sync.</p>
                    </div>

                    <div class="form-card">
                        <div class="form-content">
                            <?php if ($error): ?>
                                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <?php if ($success): ?>
                                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <div class="input-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" placeholder="Enter your email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                </div>
                                <div class="input-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                </div>
                                <div class="button-wrapper">
                                    <button type="submit" class="login-btn">Login</button>
                                </div>
                            </form>
                            
                            <div class="register-link">
                                <a href="register.php">Don't have an account? Register</a>
                            </div>
                        </div>
                    </div>

                    <div class="features">
                        <div class="feature">
                            <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <polyline points="16,18 22,12 16,6"></polyline>
                                <polyline points="8,6 2,12 8,18"></polyline>
                            </svg>
                            <p>Real-time Coding</p>
                        </div>
                        <div class="feature">
                            <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"></path>
                            </svg>
                            <p>Live Chat</p>
                        </div>
                        <div class="feature">
                            <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="m22 21-3-3"></path>
                            </svg>
                            <p>Team Collaboration</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>