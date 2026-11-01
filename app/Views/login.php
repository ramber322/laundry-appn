<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SS Laundry</title>
    <style>
        :root {
            --bg: #f4f7f9;
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --success: #10b981;
            --danger: #ef4444;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --card-bg: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }

        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(59,130,246,0.06), transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(139,92,246,0.06), transparent 50%);
            z-index: 0;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 10px 25px -5px rgba(59,130,246,0.4);
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .brand-tagline {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
        }

        .login-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.35rem;
        }

        .login-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .input-wrap {
            position: relative;
        }

        /* Icon wrapper on the LEFT side of the input */
        .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 2.75rem 0.85rem 2.5rem;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.15s;
            font-family: inherit;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        /* --- Global icon sizing (applies to mail, lock, and eye) --- */
        .eye-icon {
            width: 18px;
            height: 18px;
            display: block;
        }

        /* --- Password toggle button (right side) --- */
        .password-toggle {
            position: absolute;
            right: 0.6rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s, background 0.15s;
            border-radius: 6px;
        }

        .password-toggle:hover {
            color: var(--text);
            background: #f1f5f9;
        }

        /* Eye toggle is slightly larger than the left-side input icons */
        .password-toggle .eye-icon {
            width: 20px;
            height: 20px;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.8rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .checkbox-label input {
            accent-color: var(--primary);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px -5px rgba(59,130,246,0.4);
        }

        .btn-login:active:not(:disabled) {
            transform: translateY(0);
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            margin-top: 1rem;
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem;
            }
            .brand-icon {
                width: 64px;
                height: 64px;
                font-size: 2rem;
            }
            .brand-name {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">

        <!-- Brand -->
        <div class="brand">
            <div class="brand-icon">🧺</div>
            <div class="brand-name">SS Laundry</div>
            <div class="brand-tagline">Admin Dashboard</div>
        </div>

        <!-- Login card -->
        <div class="login-card">

            <div class="login-title">Welcome back</div>
            <div class="login-subtitle">Sign in to access the staff dashboard</div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="error-message">
                    <span>⚠️</span>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <img
                                src="<?= base_url('images/mail-outline.svg') ?>"
                                alt=""
                                class="eye-icon"
                            >
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="you@sslaundry.com"
                            autocomplete="username"
                            required
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <img
                                src="<?= base_url('images/lock-closed-outline.svg') ?>"
                                alt=""
                                class="eye-icon"
                            >
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            id="passwordToggle"
                            title="Show password"
                            aria-label="Show password"
                        >
                            <img
                                src="<?= base_url('images/eye-outline.svg') ?>"
                                alt=""
                                class="eye-icon"
                                id="eye-icon"
                            >
                        </button>
                    </div>
                </div>

                <!-- Remember + Forgot -->
                <div class="login-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember">
                        Remember me
                    </label>
                    <a href="#" class="forgot-link" onclick="showForgotHelp(); return false;">Forgot password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="loginBtnText">Sign In</span>
                </button>
            </form>

        </div>

        <!-- Footer -->
        <div class="login-footer">
            © 2025 SS Laundry · <a href="#">Privacy Policy</a> · <a href="#">Contact Support</a>
            <div class="security-note">
                 Secure admin access only
            </div>
        </div>

    </div>

<script>
    /* Absolute URLs to each icon, rendered once by PHP */
    const EYE_OPEN_SVG = "<?= base_url('images/eye-outline.svg') ?>";
    const EYE_OFF_SVG  = "<?= base_url('images/eye-off-outline.svg') ?>";

    function togglePassword() {
        const input = document.getElementById("password");
        const icon  = document.getElementById("eye-icon");
        const btn   = document.getElementById("passwordToggle");

        const isHidden = input.type === "password";

        if (isHidden) {
            input.type = "text";
            icon.src   = EYE_OFF_SVG;
            btn.title  = "Hide password";
            btn.setAttribute("aria-label", "Hide password");
        } else {
            input.type = "password";
            icon.src   = EYE_OPEN_SVG;
            btn.title  = "Show password";
            btn.setAttribute("aria-label", "Show password");
        }
    }

    function showForgotHelp() {
        alert(
            "🔒 Password Reset\n\n" +
            "For security reasons, password resets are handled by the shop owner.\n\n" +
            "Please contact:\n" +
            "📞 0917-123-4567\n" +
            "📧 admin@sslaundry.com"
        );
    }

    window.addEventListener("DOMContentLoaded", () => {
        document.getElementById("email").focus();
    });
</script>

</body>
</html>