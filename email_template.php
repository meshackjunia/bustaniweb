<?php
function getEmailTemplate($name, $email, $phone, $subject, $message) {
    return "
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
}
?>