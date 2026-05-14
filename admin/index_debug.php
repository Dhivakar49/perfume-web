<?php
/**
 * Debug Dashboard - Quick Access to All Debug Tools
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Dashboard - Perfume Palace</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .card-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .card-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .card-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-primary {
            background: #d4af37;
            color: white;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        .main-actions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .main-actions h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 16px;
        }
        .btn-primary {
            background: #d4af37;
            color: white;
        }
        .btn-primary:hover {
            background: #b8941f;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        .btn-info:hover {
            background: #138496;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .alert {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            color: #856404;
        }
        .alert h3 {
            margin-bottom: 10px;
            color: #856404;
        }
        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Debug Dashboard</h1>
            <p>Perfume Palace - Login Issue Diagnostic Tools</p>
        </div>

        <div class="alert">
            <h3>⚠️ Login Not Working?</h3>
            <p><strong>Try the SIMPLE versions first (no AJAX):</strong></p>
            <p>1. <a href="register_simple.php" style="color: #856404; font-weight: bold;">Simple Register</a> - Create account without AJAX</p>
            <p>2. <a href="login_simple.php" style="color: #856404; font-weight: bold;">Simple Login</a> - Login without AJAX</p>
            <p>3. Or use the "Complete Diagnostic" tool below to debug the AJAX version</p>
        </div>

        <div class="grid">
            <a href="debug_login.php" class="card">
                <div class="card-icon">🔍</div>
                <div class="card-title">Complete Diagnostic</div>
                <div class="card-description">
                    Checks database, user, password, session, and login function. Shows exactly what's wrong and how to fix it.
                </div>
                <span class="card-badge badge-primary">Start Here</span>
            </a>

            <a href="test_ajax_login.html" class="card">
                <div class="card-icon">🧪</div>
                <div class="card-title">Test AJAX Login</div>
                <div class="card-description">
                    Test the login API directly with detailed console logs. Pre-filled credentials for quick testing.
                </div>
                <span class="card-badge badge-success">Quick Test</span>
            </a>

            <a href="check_login_status.php" class="card">
                <div class="card-icon">✓</div>
                <div class="card-title">Check Login Status</div>
                <div class="card-description">
                    Quick check to see if you're currently logged in and view your session data.
                </div>
                <span class="card-badge badge-info">Status Check</span>
            </a>

            <a href="test_session.php" class="card">
                <div class="card-icon">📊</div>
                <div class="card-title">Session Details</div>
                <div class="card-description">
                    View detailed session information, variables, and configuration.
                </div>
                <span class="card-badge badge-info">Advanced</span>
            </a>

            <a href="START_DEBUGGING_HERE.md" class="card" target="_blank">
                <div class="card-icon">📖</div>
                <div class="card-title">Step-by-Step Guide</div>
                <div class="card-description">
                    Complete guide with instructions, common issues, and solutions.
                </div>
                <span class="card-badge badge-warning">Documentation</span>
            </a>

            <a href="TESTING_GUIDE.md" class="card" target="_blank">
                <div class="card-icon">📋</div>
                <div class="card-title">Testing Guide</div>
                <div class="card-description">
                    Detailed testing procedures and troubleshooting steps.
                </div>
                <span class="card-badge badge-warning">Documentation</span>
            </a>

            <a href="register_simple.php" class="card">
                <div class="card-icon">📝</div>
                <div class="card-title">Simple Register</div>
                <div class="card-description">
                    Register without AJAX - guaranteed to work. Creates account and logs you in automatically.
                </div>
                <span class="card-badge badge-success">No AJAX</span>
            </a>

            <a href="login_simple.php" class="card">
                <div class="card-icon">🔑</div>
                <div class="card-title">Simple Login</div>
                <div class="card-description">
                    Login without AJAX - guaranteed to work. Traditional form submission.
                </div>
                <span class="card-badge badge-success">No AJAX</span>
            </a>

            <a href="simple_register_test.php" class="card">
                <div class="card-icon">🧪</div>
                <div class="card-title">Auto Register & Login Test</div>
                <div class="card-description">
                    Automatically creates a test user and logs in. Shows you working credentials.
                </div>
                <span class="card-badge badge-primary">Auto Test</span>
            </a>
        </div>

        <div class="main-actions">
            <h2>🎯 Quick Start - Choose Your Path</h2>
            <p style="margin-bottom: 20px; color: #666;">Pick the easiest option for you</p>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px;">Option 1: Simple (Recommended)</h3>
                <a href="register_simple.php" class="btn btn-success">Register (No AJAX)</a>
                <a href="login_simple.php" class="btn btn-success">Login (No AJAX)</a>
                <p style="margin-top: 10px; color: #666;"><small>Traditional form submission - guaranteed to work</small></p>
            </div>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px;">Option 2: Auto Test</h3>
                <a href="simple_register_test.php" class="btn btn-primary">Auto Create & Login</a>
                <p style="margin-top: 10px; color: #666;"><small>Automatically creates a test user and logs in</small></p>
            </div>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h3 style="margin-bottom: 15px;">Option 3: Debug AJAX Version</h3>
                <a href="debug_login.php" class="btn btn-info">Complete Diagnostic</a>
                <p style="margin-top: 10px; color: #666;"><small>If you want to fix the AJAX login/register pages</small></p>
            </div>
            
            <hr style="margin: 30px 0;">
            
            <h3>Main Application Pages</h3>
            <a href="index.php" class="btn btn-primary">Homepage</a>
            <a href="products.php" class="btn btn-secondary">Products</a>
            <a href="check_login_status.php" class="btn btn-info">Check Login Status</a>
        </div>

        <div class="footer">
            <p>Perfume Palace Debug Dashboard</p>
            <p><small>Use these tools to diagnose and fix login issues</small></p>
        </div>
    </div>
</body>
</html>
