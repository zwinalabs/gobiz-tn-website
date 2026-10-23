<?php
// Enforce JSON responses back to JavaScript
header('Content-Type: application/json');

// Ensure request type is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// 1. Retrieve and sanitize input fields
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// 2. Basic Validation
if (!$name || !$email || !$subject || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill out all fields accurately.']);
    exit;
}

// 3. Email Configurations
$to = "contact@gobiz.tn"; // <-- Change this to your real inbox destination
$subject = "New Contact Form Submission from " . $name;

// Construct message template
$body = "You have received a new message from Gobiz.tn contact form.\n\n";
$body .= "Name: " . $name . "\n";
$body .= "Email: " . $email . "\n\n";
$body .= "Subject: " . $subject . "\n";
$body .= "Message:\n" . $message . "\n";

// Secure and valid MIME headers
$headers = [
    'From' => 'contact@gobiz.tn', // Best practice: Use an email mapped to your domain hosting
    'Reply-To' => $email,
    'X-Mailer' => 'PHP/' . phpversion()
];

// 4. Send Email and return JSON status reports
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Your message was sent successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Mail delivery failed on server side.']);
}
