<?php
header('Content-Type: application/json');

// Security checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Rate limiting
session_start();
if (!isset($_SESSION['last_submit'])) {
    $_SESSION['last_submit'] = 0;
}

if (time() - $_SESSION['last_submit'] < 30) {
    die(json_encode(['success' => false, 'message' => 'Please wait before submitting another message']));
}

$_SESSION['last_submit'] = time();

// Honeypot check
if (!empty($_POST['honeypot'])) {
    die(json_encode(['success' => false, 'message' => 'Bot detected']));
}

// Validate and sanitize input
$errors = [];
$data = [];

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validation
if (empty($name)) {
    $errors['name'] = 'Name is required.';
}

if (empty($email)) {
    $errors['email'] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email is invalid.';
}

if (empty($message)) {
    $errors['message'] = 'Message is required.';
}

if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Email configuration
    $to = 'meshackochumbo@gmail.com'; // REPLACE WITH YOUR EMAIL
    $email_subject = "New Contact Form Submission: $subject";
    
    // Plain text version
    $email_body = "You have received a new message from your website contact form.\n\n".
                 "Name: $name\n".
                 "Email: $email\n".
                 "Phone: " . ($phone ? $phone : 'Not provided') . "\n\n".
                 "Subject: $subject\n\n".
                 "Message:\n$message";
    
    // HTML version
    $email_body_html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #5d3a9b; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { padding: 10px; text-align: center; font-size: 0.8em; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>From:</strong> $name &lt;$email&gt;</p>
                <p><strong>Phone:</strong> " . ($phone ? $phone : 'Not provided') . "</p>
                <h3>Message:</h3>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            <div class='footer'>
                <p>This message was sent from the contact form on Serene Stay BNB website.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Headers
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Send email
    $mailSent = mail($to, $email_subject, $email_body_html, $headers);
    
    // Database storage (optional - uncomment if needed)
    
    if ($mailSent) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=bustanibnb', 'username', 'password');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $db->prepare("INSERT INTO messages (name, email, phone, subject, message) 
                                 VALUES (:name, :email, :phone, :subject, :message)");
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
    
    
    if ($mailSent) {
        $data['success'] = true;
        $data['message'] = "Thank you, $name! Your message has been sent. We'll get back to you soon.";
    } else {
        $data['success'] = false;
        $data['message'] = 'Oops! Something went wrong. Please try again later.';
        error_log("Failed to send email to: $to");
    }
}

echo json_encode($data);
?>