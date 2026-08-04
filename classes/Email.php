<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->Port = $_ENV['EMAIL_PORT'];

        $mail->setFrom($_ENV['EMAIL_FROM'], $_ENV['EMAIL_FROM_NAME']);
        $mail->addAddress($this->email, $this->nombre);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = 'Confirma tu cuenta en Freelance Manager EDN';

        $url = $_ENV['APP_URL'] . '/confirmar-cuenta?token=' . $this->token;

        $mail->Body = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #111827;'>
                    <h2>Hola {$this->nombre}</h2>

                    <p>Gracias por registrarte en <strong>Freelance Manager EDN</strong>.</p>

                    <p>Para activar tu cuenta, confirma tu correo electrónico haciendo clic en el siguiente enlace:</p>

                    <p>
                        <a 
                            href='{$url}' 
                            style='background: #0057ff; color: #ffffff; padding: 12px 18px; text-decoration: none; border-radius: 8px; display: inline-block;'
                        >
                            Confirmar cuenta
                        </a>
                    </p>

                    <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>

                    <p>{$url}</p>

                    <p>Si tú no creaste esta cuenta, puedes ignorar este mensaje.</p>
                </body>
            </html>
        ";

        $mail->AltBody = "Hola {$this->nombre}. Confirma tu cuenta visitando este enlace: {$url}";

        $mail->send();
    }
}