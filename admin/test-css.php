<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Test - Perfume Store</title>
    
    <!-- Test CSS Loading -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .test-section h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .status {
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: 600;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 CSS Loading Test</h1>
        <p>This page tests if CSS files are loading correctly</p>
        
        <div class="test-section">
            <h2>1. File Existence Check</h2>
            <?php
            $css_files = [
                'assets/css/style.css',
                'assets/css/responsive.css'
            ];
            
            foreach ($css_files as $file) {
                if (file_exists($file)) {
                    $size = filesize($file);
                    echo '<div class="status success">✅ ' . $file . ' exists (' . number_format($size) . ' bytes)</div>';
                } else {
                    echo '<div class="status error">❌ ' . $file . ' NOT FOUND</div>';
                }
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>2. Category Card Test</h2>
            <p>These should show colored gradient backgrounds:</p>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
                <a href="#" class="category-card">
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: absolute; top: 0; left: 0;"></div>
                    <div class="category-overlay">
                        <h3 class="category-name">Men Perfumes</h3>
                    </div>
                </a>
                
                <a href="#" class="category-card">
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); position: absolute; top: 0; left: 0;"></div>
                    <div class="category-overlay">
                        <h3 class="category-name">Women Perfumes</h3>
                    </div>
                </a>
                
                <a href="#" class="category-card">
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); position: absolute; top: 0; left: 0;"></div>
                    <div class="category-overlay">
                        <h3 class="category-name">Unisex</h3>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="test-section">
            <h2>3. CSS Variables Test</h2>
            <div style="padding: 20px; background: var(--primary-color, #d4af37); color: white; border-radius: 8px; margin-bottom: 10px;">
                Primary Color (should be gold/yellow)
            </div>
            <div style="padding: 20px; background: var(--secondary-color, #1a1a1a); color: white; border-radius: 8px;">
                Secondary Color (should be dark/black)
            </div>
        </div>
        
        <div class="test-section">
            <h2>4. Browser Console Check</h2>
            <div class="status info">
                ℹ️ Press F12 to open browser console and check for CSS loading errors
            </div>
            <p>Look for errors like:</p>
            <ul>
                <li>"Failed to load resource: assets/css/style.css"</li>
                <li>"404 Not Found"</li>
                <li>"MIME type mismatch"</li>
            </ul>
        </div>
        
        <div class="test-section">
            <h2>5. Path Information</h2>
            <?php
            echo '<div class="status info">';
            echo '<strong>Document Root:</strong> ' . $_SERVER['DOCUMENT_ROOT'] . '<br>';
            echo '<strong>Current File:</strong> ' . __FILE__ . '<br>';
            echo '<strong>CSS Path:</strong> ' . realpath('assets/css/style.css') . '<br>';
            echo '</div>';
            ?>
        </div>
        
        <div class="test-section">
            <h2>6. Quick Fixes</h2>
            <p>If categories are showing as gray boxes:</p>
            <ol>
                <li><strong>Hard Refresh:</strong> Press Ctrl+Shift+R (or Cmd+Shift+R on Mac)</li>
                <li><strong>Clear Cache:</strong> Press Ctrl+Shift+Delete and clear cached images/files</li>
                <li><strong>Check Console:</strong> Press F12 and look for red errors</li>
                <li><strong>Try Incognito:</strong> Open in private/incognito window</li>
            </ol>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <a href="index.php" style="display: inline-block; padding: 12px 30px; background: #667eea; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
            Back to Homepage
        </a>
    </div>
    
    <script>
        // Check if CSS is loaded
        window.addEventListener('load', function() {
            const testElement = document.querySelector('.category-card');
            if (testElement) {
                const styles = window.getComputedStyle(testElement);
                const position = styles.getPropertyValue('position');
                const height = styles.getPropertyValue('height');
                
                console.log('Category Card Styles:');
                console.log('Position:', position);
                console.log('Height:', height);
                
                if (position === 'relative' && height === '300px') {
                    console.log('✅ CSS is loaded correctly!');
                } else {
                    console.log('❌ CSS might not be loaded. Expected position: relative, height: 300px');
                }
            }
        });
    </script>
</body>
</html>
