<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../dbconfig.php');
require_once(__DIR__ . '/../PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/../PHPMailer/src/SMTP.php');
require_once(__DIR__ . '/../PHPMailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// 檢查必要參數
if (
    empty($_POST['to_email']) ||
    empty($_POST['to_name']) ||
    empty($_POST['subject']) ||
    empty($_POST['body'])
) {
    echo json_encode(['success' => false, 'message' => '缺少必要欄位']);
    exit;
}

$toEmail = $_POST['to_email'];
$toName = $_POST['to_name'];
$subject = $_POST['subject'];
$bodyHtml = $_POST['body'];

$mail = new PHPMailer(true);

try {
    // SMTP 設定
    $mail->isSMTP();
    $mail->Host = 'student.nsysu.edu.tw';
    $mail->SMTPAuth = true;
    $mail->Username = 'b112040003';        // 你的學號
    $mail->Password = 'Edward0519';        // 中山信箱密碼
    $mail->Port = 25;
    $mail->SMTPSecure = false;
    $mail->SMTPAutoTLS = false;

    // 寄件者
    $mail->setFrom('b112040003@student.nsysu.edu.tw', 'reserved system');

    // 收件者
    $mail->addAddress($toEmail, $toName);

    // 郵件內容
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $bodyHtml;

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Send Email Error: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => $mail->ErrorInfo]);
}
?>
