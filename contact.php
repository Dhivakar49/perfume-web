<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Contact Us - Perfume Palace';

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // In a real application, you would save to database or send email
        // For now, we'll just show a success message
        $success_message = 'Thank you for contacting us! We will get back to you soon.';
        
        // Clear form
        $name = $email = $subject = $message = '';
    }
}

include 'includes/header.php';
?>

<div class="container" style="padding: 60px 15px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 15px;">Contact Us</h1>
        <p style="text-align: center; color: #666; font-size: 16px; margin-bottom: 50px;">
            Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
        
        <?php if ($success_message): ?>
            <div style="padding: 15px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                ✓ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div style="padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                ✗ <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px;">
            <!-- Contact Form -->
            <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Send us a Message</h2>
                
                <form method="POST" action="">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Your Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Subject</label>
                        <input type="text" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Message</label>
                        <textarea name="message" rows="6" required
                                  style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; resize: vertical;"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="submit_contact"
                            style="width: 100%; padding: 15px; background: #d4af37; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 16px;">
                        Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div>
                <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Contact Information</h2>
                    
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; align-items: start; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #d4af37; font-size: 20px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">Address</h3>
                                <p style="color: #666; margin: 0;">123 Perfume Street<br>Luxury District, City 12345</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; align-items: start; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #d4af37; font-size: 20px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">Phone</h3>
                                <p style="color: #666; margin: 0;">+91 9876543210</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; align-items: start; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #d4af37; font-size: 20px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">Email</h3>
                                <p style="color: #666; margin: 0;">info@perfumepalace.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div style="display: flex; align-items: start; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #d4af37; font-size: 20px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px;">Business Hours</h3>
                                <p style="color: #666; margin: 0;">
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px;">Follow Us</h2>
                    <div style="display: flex; gap: 15px;">
                        <a href="#" style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px; text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px; text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px; text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; font-size: 20px; text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
