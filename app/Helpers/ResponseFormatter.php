<?php

namespace App\Helpers;


use PHPMailer\PHPMailer\PHPMailer;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null,
        ],
        'data' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($data = null, $message = null)
    {
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    /**
     * Give error response.
     */
    public static function error($data = null, $message = null, $code = 400)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function email(){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        $mail->SMTPDebug = 0;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        // $mail->Host       = 'mail.ptadaro.com';                    // Set the SMTP server to send through
        // $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        // $mail->Username   = 'ptadaro\fwivindi';                     // SMTP username
        // $mail->Password   = 'Suzuran888';                               // SMTP password
        // $mail->SMTPSecure = 'null';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        // $mail->Port       = 25;

        // $mail->Host       = 'smtp.mailtrap.io';                    // Set the SMTP server to send through
        // $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        // $mail->Username   = '74b83bdf7d3200';                     // SMTP username
        // $mail->Password   = 'c9f3082d160156';                               // SMTP password
        // $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        // $mail->Port       = 2525;

        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'laravel.mailer88@gmail.com';                     // SMTP username
        $mail->Password   = 'ljgctenbuuukhxoj';                               // SMTP password
        // $mail->Password   = 'c9f3082d160156';                               // SMTP password
        $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 465;
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        $mail->From = 'laravel.mailer88@gmail.com';
        $mail->FromName = 'Laravel';

        // // Content
        $mail->isHTML(true);

        return $mail;
    }

    public static function strReplace(
        $body, $email, $otp
    )
    {
        $body = str_replace('$email',$email, $body);
        $body = str_replace('$otp',$otp, $body);

        return $body;
    }
}
