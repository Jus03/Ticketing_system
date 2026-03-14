<?php
session_start();
$conn = require_once 'config/conn.php';

$email_error = '';
$password_error = '';
$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $is_valid = true;

    if (empty($email)) {
        $email_error = 'Email is required';
        $is_valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = 'Enter a valid email';
        $is_valid = false;
    }

    if (empty($password)) {
        $password_error = 'Password is required';
        $is_valid = false;
    } elseif (strlen($password) < 6) {
        $password_error = 'Password must be at least 6 characters';
        $is_valid = false;
    }

    if ($is_valid) {
        // Check if connection exists
        if (!$conn) {
            die("Database connection error");
        }
        
        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
        
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            $login_error = 'An error occurred. Please try again.';
        } else {
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    if (isset($user['password']) && !is_null($user['password'])) {
                        if (password_verify($password, $user['password'])) {
                            // Login successful
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_email'] = $user['email'];
                            $_SESSION['logged_in'] = true;
                            
                            // Redirect to dashboard
                            header("Location: home.html");
                            exit();
                        } else {
                            $login_error = 'Invalid email or password';
                        }
                    } else {
                        $login_error = 'Invalid user data';
                        error_log("User has null password: " . $email);
                    }
                } else {
                    $login_error = 'Invalid email or password';
                }
            } else {
                error_log("Execute failed: " . $stmt->error);
                $login_error = 'An error occurred. Please try again.';
            }
            
            $stmt->close();
        }
    }
}

// Don't close connection here if you need it in HTML below
// We'll close it after HTML if needed
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="login.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
    <style>
        .error-message { color: red; font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body class="min-h-screen bg-white">
    <div class="flex flex-col md:flex-row w-full min-h-screen">
        <!-- Left side - Form -->
        <div class="w-full md:w-1/2 p-6 sm:p-8 lg:p-12 xl:p-16 flex flex-col justify-center bg-white order-2 md:order-1">
            <div class="max-w-md mx-auto w-full">
                <div class="mb-8 flex justify-center md:justify-center">
                    <img src="../Assets/logo.png" alt="logo" class="h-8 sm:h-10 w-auto" />
                </div>

                <div class="mb-6 sm:mb-8">
                    <h1 class="text-3xl sm:text-4xl font-bold mb-2 text-center md:text-center">Welcome Back</h1>
                    <p class="text-gray-500 text-base sm:text-lg text-center md:text-center">Sign in to your account</p>
                </div>
                
                <!-- Display login error if any -->
                <?php if (!empty($login_error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $login_error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="loginForm">
                    <div class="mb-6">
                        <label class="block text-sm sm:text-md font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input
                                type="email"
                                name="email"
                                placeholder="Your email"
                                id="email"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                class="w-full pl-10 p-3 border <?php echo !empty($email_error) ? 'border-red-500' : 'border-gray-200'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                            />
                        </div>
                        <?php if (!empty($email_error)): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $email_error; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm sm:text-md font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="passwordInput"
                                placeholder="Password"
                                class="w-full pl-10 p-3 border <?php echo !empty($password_error) ? 'border-red-500' : 'border-gray-200'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                            />
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg id="eyeIcon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg
                                    id="eyeOffIcon"
                                    class="h-5 w-5 text-gray-400 hidden"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <?php if (!empty($password_error)): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $password_error; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="text-right mb-4">
                        <a href="#" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-3.5 rounded-lg font-medium hover:bg-blue-700 transition mb-8 text-lg"
                    >
                        Sign in
                    </button>
                </form>

                <div class="relative mb-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">OR</span>
                    </div>
                </div>

                <button
                    class="w-full border border-gray-200 text-gray-700 py-3.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center justify-center gap-3 text-lg"
                >
                    <img src="../Assets/google.png" alt="Google" class="w-5 h-5" />
                    Continue with Google
                </button>

                <p class="text-center text-gray-600 mt-3">
                    Don't have an account? <a href="../auth/signUp.html" class="text-blue-600 font-medium hover:underline">Sign up</a>
                </p>
            </div>
        </div>

        <div
            class="w-full md:w-1/2 h-64 md:h-auto bg-cover bg-center order-1 md:order-2"
            style="background-image: url('../Assets/aio.jpg')"
        ></div>
    </div>

    <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }
    </script>
</body>
</html>
<?php
// Close connection after HTML
if (isset($conn)) {
    $conn->close();
}
?>