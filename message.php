<?php
require 'vendor/autoload.php'; // charge les librairies

// Charger le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupération des champs
    $name = htmlspecialchars(strip_tags($_POST["name"] ?? ""));
    $email = htmlspecialchars(strip_tags($_POST["email"] ?? ""));
    $phone = htmlspecialchars(strip_tags($_POST["phone"] ?? ""));
    $subjectField = htmlspecialchars(strip_tags($_POST["subject"] ?? "No Subject"));
    $messageContent = htmlspecialchars(strip_tags($_POST["message"] ?? ""));
    $newsletter = isset($_POST["newsletter"]) ? "Oui" : "Non";

    if(empty($name) || empty($email) || empty($messageContent)) {
        echo "error: missing fields";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL_USER']; 
        $mail->Password   = $_ENV['EMAIL_PASS']; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Activer le debug pour voir les erreurs (mettre 2 si besoin de tester)
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';

        // Expéditeur et destinataire
        $mail->setFrom($_ENV['EMAIL_USER'], 'ImmoKeys'); 
        $mail->addAddress($_ENV['EMAIL_USER']); 

        // Contenu du mail
        $mail->Subject = "Nouveau message depuis le site ImmoKeys: $subjectField";
        $mail->Body    = "Nom: $name\nEmail: $email\nTéléphone: $phone\nNewsletter: $newsletter\nMessage:\n$messageContent";

        // Envoi
        $mail->send();
        echo "success";

    } catch (Exception $e) {
        echo "error: {$mail->ErrorInfo}";
    }
}
?>
