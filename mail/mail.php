<?php

require_once "vendor/autoload.php";
require_once "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set("Asia/Bangkok");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../quote.html");
    exit;
}

$name = trim($_POST["name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$email = trim($_POST["email"] ?? "");
$suburb = trim($_POST["suburb"] ?? "");
$service = trim($_POST["service"] ?? "");
$additional_service = trim($_POST["additional_service"] ?? "");
$panel_count = trim($_POST["panel_count"] ?? "");
$property_type = trim($_POST["property_type"] ?? "");
$message = trim($_POST["message"] ?? "");
$terms = $_POST["terms"] ?? "";

if (
    $name === "" ||
    $phone === "" ||
    $email === "" ||
    $suburb === "" ||
    $service === "" ||
    $property_type === "" ||
    $terms !== "1"
) {
    header("Location: ../fail.html");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../fail.html");
    exit;
}

$submitted_at = date("Y-m-d H:i:s");

try {
    $sql = "INSERT INTO quote_requests
        (name, phone, email, suburb, service, additional_service, panel_count, property_type, message, terms_agreed, created_at)
        VALUES
        (:name, :phone, :email, :suburb, :service, :additional_service, :panel_count, :property_type, :message, :terms_agreed, :created_at)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":phone" => $phone,
        ":email" => $email,
        ":suburb" => $suburb,
        ":service" => $service,
        ":additional_service" => $additional_service,
        ":panel_count" => $panel_count,
        ":property_type" => $property_type,
        ":message" => $message,
        ":terms_agreed" => 1,
        ":created_at" => $submitted_at
    ]);

} catch (PDOException $e) {
    error_log("DATABASE ERROR: " . $e->getMessage());
    header("Location: ../fail.html");
    exit;
}
$business_email = "";
    $smtp_username = "";
    $smtp_password = "";

$emailErrors = [];

function configureMailer($mail, $username, $password)
{
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = "UTF-8";
    $mail->Timeout = 30;
}

try {
    $mail = new PHPMailer(true);

    configureMailer($mail, $smtp_username, $smtp_password);

    $mail->setFrom($smtp_username, "LT Solar & Gutter Clean");
    $mail->addAddress($business_email);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);

$mail->Subject = "New Quote Request - " . $name;

$mail->Body = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Quote Request</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f5f7;padding:40px 15px;">
<tr>
<td align="center">

<table width="620" cellpadding="0" cellspacing="0" border="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;">

<tr>
<td style="background:#031838;padding:28px 35px;text-align:center;">
    <div style="font-size:23px;font-weight:700;color:#ffffff;">
        LT Solar &amp; Gutter Clean
    </div>
    <div style="margin-top:7px;font-size:12px;color:#f29a2e;letter-spacing:1.5px;">
        NEW QUOTE REQUEST
    </div>
</td>
</tr>

<tr>
<td style="padding:35px 40px 25px;text-align:left;">

    <div style="display:inline-block;background:#fff5df;color:#c47700;padding:7px 13px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">
        New Enquiry
    </div>

    <h1 style="margin:17px 0 10px;font-size:25px;line-height:1.3;color:#031838;">
        New quote request received
    </h1>

    <p style="margin:0;font-size:14px;line-height:1.7;color:#6b7280;">
        A new customer has submitted a quote request through the website.
        The details are below.
    </p>

</td>
</tr>

<tr>
<td style="padding:0 40px 30px;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8f9fb;border:1px solid #e5e7eb;border-radius:10px;">

<tr>
<td colspan="2" style="padding:17px 20px;border-bottom:1px solid #e5e7eb;">
    <div style="font-size:14px;font-weight:700;color:#031838;">
        Customer Details
    </div>
</td>
</tr>

<tr>
<td style="padding:13px 20px 6px;font-size:12px;color:#6b7280;width:40%;">
    Name
</td>
<td style="padding:13px 20px 6px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($name) . '
</td>
</tr>

<tr>
<td style="padding:6px 20px;font-size:12px;color:#6b7280;">
    Phone
</td>
<td style="padding:6px 20px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($phone) . '
</td>
</tr>

<tr>
<td style="padding:6px 20px 6px;font-size:12px;color:#6b7280;">
    Email
</td>
<td style="padding:6px 20px 6px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($email) . '
</td>
</tr>

<tr>
<td style="padding:6px 20px 17px;font-size:12px;color:#6b7280;">
    Suburb
</td>
<td style="padding:6px 20px 17px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($suburb) . '
</td>
</tr>

</table>

</td>
</tr>

<tr>
<td style="padding:0 40px 30px;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8f9fb;border:1px solid #e5e7eb;border-radius:10px;">

<tr>
<td colspan="2" style="padding:17px 20px;border-bottom:1px solid #e5e7eb;">
    <div style="font-size:14px;font-weight:700;color:#031838;">
        Service Details
    </div>
</td>
</tr>

<tr>
<td style="padding:13px 20px 6px;font-size:12px;color:#6b7280;width:40%;">
    Service
</td>
<td style="padding:13px 20px 6px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($service) . '
</td>
</tr>

<tr>
<td style="padding:6px 20px;font-size:12px;color:#6b7280;">
    Additional Service
</td>
<td style="padding:6px 20px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($additional_service) . '
</td>
</tr>

<tr>
<td style="padding:6px 20px;font-size:12px;color:#6b7280;">
    Panel Count
</td>
<td style="padding:6px 20px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($panel_count) . '
</td>
</tr>

<tr>
<td style="padding:6px 20px 17px;font-size:12px;color:#6b7280;">
    Property Type
</td>
<td style="padding:6px 20px 17px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($property_type) . '
</td>
</tr>

</table>

</td>
</tr>

<tr>
<td style="padding:0 40px 35px;">

<div style="border-left:3px solid #f29a2e;background:#fffaf0;padding:17px 18px;">
    <div style="font-size:13px;font-weight:700;color:#031838;margin-bottom:8px;">
        Customer Message
    </div>

    <div style="font-size:14px;line-height:1.7;color:#4b5563;">
        ' . nl2br(htmlspecialchars($message)) . '
    </div>
</div>

</td>
</tr>

<tr>
<td style="background:#031838;padding:24px 35px;text-align:center;">

    <div style="font-size:14px;font-weight:700;color:#ffffff;">
        LT Solar &amp; Gutter Clean
    </div>

    <div style="margin-top:6px;font-size:12px;color:#cbd5e1;">
        Honest work. Visible proof. Zero mess.
    </div>

    <div style="margin-top:10px;font-size:11px;color:#94a3b8;">
        South East Melbourne
    </div>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
';

$mail->AltBody =
    "LT SOLAR & GUTTER CLEAN\n" .
    "NEW QUOTE REQUEST\n\n" .
    "CUSTOMER DETAILS\n" .
    "────────────────────────\n" .
    "Name: " . $name . "\n" .
    "Phone: " . $phone . "\n" .
    "Email: " . $email . "\n" .
    "Suburb: " . $suburb . "\n\n" .
    "SERVICE DETAILS\n" .
    "────────────────────────\n" .
    "Service: " . $service . "\n" .
    "Additional Service: " . $additional_service . "\n" .
    "Panel Count: " . $panel_count . "\n" .
    "Property Type: " . $property_type . "\n\n" .
    "CUSTOMER MESSAGE\n" .
    "────────────────────────\n" .
    $message . "\n\n" .
    "LT Solar & Gutter Clean\n" .
    "South East Melbourne";

    $mail->send();
} catch (Exception $e) {
    $emailErrors[] = "Business email failed: " . $mail->ErrorInfo;
}

try {
    $customerMail = new PHPMailer(true);

    configureMailer($customerMail, $smtp_username, $smtp_password);

    $customerMail->setFrom($smtp_username, "LT Solar & Gutter Clean");
    $customerMail->addAddress($email, $name);
    $customerMail->addReplyTo($business_email, "LT Solar & Gutter Clean");

    $customerMail->isHTML(true);

    $customerMail->Subject = "We've Received Your Quote Request";

    $customerMail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:650px;margin:auto;line-height:1.6;color:#222;'>
            <h2>Thanks, " . htmlspecialchars($name) . "!</h2>

            <p>We've received your quote request for <strong>LT Solar & Gutter Clean</strong>.</p>

            <p>We'll review your details and get back to you shortly.</p>

            <h3>Your Request</h3>

            <p><strong>Service:</strong> " . htmlspecialchars($service) . "</p>
            <p><strong>Additional Service:</strong> " . htmlspecialchars($additional_service) . "</p>
            <p><strong>Suburb:</strong> " . htmlspecialchars($suburb) . "</p>
            <p><strong>Panel Count:</strong> " . htmlspecialchars($panel_count) . "</p>
            <p><strong>Property Type:</strong> " . htmlspecialchars($property_type) . "</p>

            <p>If you need to contact us, simply reply to this email.</p>

            <p>
                Thanks,<br>
                <strong>LT Solar & Gutter Clean</strong>
            </p>
        </div>
    ";

    $customerMail->isHTML(true);

$customerMail->Body = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quote Request Received</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f5f7;padding:40px 15px;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;">

<tr>
<td style="background:#031838;padding:28px 35px;text-align:center;">
    <div style="font-size:24px;font-weight:700;color:#ffffff;">
        LT Solar &amp; Gutter Clean
    </div>
    <div style="margin-top:7px;font-size:13px;color:#f29a2e;letter-spacing:1px;">
        SOUTH EAST MELBOURNE
    </div>
</td>
</tr>

<tr>
<td style="padding:40px 40px 25px;text-align:left;">

    <div style="display:inline-block;background:#fff5df;color:#c47700;padding:7px 13px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">
        Request Received
    </div>

    <h1 style="margin:18px 0 12px;font-size:27px;line-height:1.3;color:#031838;">
        Thanks, ' . htmlspecialchars($name) . '!
    </h1>

    <p style="margin:0;font-size:15px;line-height:1.7;color:#4b5563;">
        Thanks for requesting a quote from LT Solar &amp; Gutter Clean.
        We\'ve received your request and will review the details shortly.
    </p>

</td>
</tr>

<tr>
<td style="padding:0 40px 30px;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8f9fb;border:1px solid #e5e7eb;border-radius:10px;">

<tr>
<td colspan="2" style="padding:18px 20px;border-bottom:1px solid #e5e7eb;">
    <div style="font-size:14px;font-weight:700;color:#031838;">
        Your Request
    </div>
</td>
</tr>

<tr>
<td style="padding:14px 20px 7px;font-size:12px;color:#6b7280;width:45%;">
    Service
</td>
<td style="padding:14px 20px 7px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($service) . '
</td>
</tr>

<tr>
<td style="padding:7px 20px;font-size:12px;color:#6b7280;">
    Additional Service
</td>
<td style="padding:7px 20px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($additional_service) . '
</td>
</tr>

<tr>
<td style="padding:7px 20px;font-size:12px;color:#6b7280;">
    Suburb
</td>
<td style="padding:7px 20px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($suburb) . '
</td>
</tr>

<tr>
<td style="padding:7px 20px;font-size:12px;color:#6b7280;">
    Panel Count
</td>
<td style="padding:7px 20px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($panel_count) . '
</td>
</tr>

<tr>
<td style="padding:7px 20px 18px;font-size:12px;color:#6b7280;">
    Property Type
</td>
<td style="padding:7px 20px 18px;font-size:14px;font-weight:600;color:#111827;">
    ' . htmlspecialchars($property_type) . '
</td>
</tr>

</table>

</td>
</tr>

<tr>
<td style="padding:0 40px 35px;text-align:left;">

    <div style="border-left:3px solid #f29a2e;padding-left:15px;">
        <p style="margin:0;font-size:14px;line-height:1.7;color:#4b5563;">
            <strong style="color:#031838;">What happens next?</strong><br>
            We\'ll review your request and get back to you with the next steps.
            If we need any additional information, we\'ll contact you directly.
        </p>
    </div>

</td>
</tr>

<tr>
<td style="background:#031838;padding:25px 35px;text-align:center;">

    <div style="font-size:14px;font-weight:700;color:#ffffff;">
        LT Solar &amp; Gutter Clean
    </div>

    <div style="margin-top:6px;font-size:12px;color:#cbd5e1;">
        Honest work. Visible proof. Zero mess.
    </div>

    <div style="margin-top:12px;font-size:11px;color:#94a3b8;">
        Proudly servicing South East Melbourne
    </div>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
';

$customerMail->AltBody =
    "Hi " . $name . ",\n\n" .
    "Thank you for choosing LT Solar & Gutter Clean.\n\n" .
    "We've received your quote request and will review the details shortly.\n\n" .
    "YOUR REQUEST\n\n" .
    "Service: " . $service . "\n" .
    "Additional Service: " . $additional_service . "\n" .
    "Suburb: " . $suburb . "\n" .
    "Panel Count: " . $panel_count . "\n" .
    "Property Type: " . $property_type . "\n\n" .
    "We'll review your request and get back to you with the next steps.\n\n" .
    "Thanks,\n" .
    "LT Solar & Gutter Clean";

    $customerMail->send();
} catch (Exception $e) {
    $emailErrors[] = "Customer email failed: " . $customerMail->ErrorInfo;
}

if (!empty($emailErrors)) {
    error_log("QUOTE EMAIL ERRORS: " . implode(" | ", $emailErrors));
}

header("Location: ../success.html");
exit;