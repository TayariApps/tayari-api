<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController extends Controller
{
    
    public function ownerRegisterMail(Request $request = null, $user){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

        // Email server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';             //  smtp host
        $mail->SMTPAuth = true;
        $mail->Username = 'register@tayari.co.tz';   //  sender username
        $mail->Password = 'puagjnklvoisxcux';       // sender password
        $mail->SMTPSecure = 'ssl';                  // encryption - ssl/tls
        $mail->Port = 465;                          // port - 587/465

        $mail->setFrom('register@tayari.co.tz', 'New Owner Registration');
        $mail->addAddress('adrian.nzamba@tayari.co.tz');

        $mail->isHTML(true);                // Set email content format to HTML

        $mail->Subject = 'A new restaurant owner has registered';
        $mail->Body    = "<html>
        $user->name has registered themselves on the Restaurant dashboard.
        Find their details in the at <a target='_blank' href='control.tayari.co.tz'>control.tayari.co.tz</a>
        <br><br>
        
            Thanks,<br>
            Tayari
            </html>";

            if( !$mail->send() ) {
                return response()->json('Mail failed to be sent', 400);
            }
            
            else {
                return response()->json('Mail sent!', 200);
            }
    
        } catch (Exception $e) {
            return response()->json('Mail failed to be sent', 400);
        }
    }

    public function orderRecievedMail(Request $request = null, $place){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = 'register@tayari.co.tz';   //  sender username
            $mail->Password = 'puagjnklvoisxcux';       // sender password
            $mail->SMTPSecure = 'ssl';                  // encryption - ssl/tls
            $mail->Port = 465;                          // port - 587/465
    
            $mail->setFrom('register@tayari.co.tz', 'New Order');
            $mail->addAddress('info@tayari.co.tz');
            $mail->addAddress('kunbata93@gmail.com');
    
            $mail->isHTML(true);                // Set email content format to HTML
    
            $mail->Subject = 'New order created';
            $mail->Body    = "<html>
           A customer has created a new order at $place->name <br><br>
            
                Thanks,<br>
                Tayari
                </html>";
    
                if( !$mail->send() ) {
                    return response()->json('Mail failed to be sent', 400);
                }
                
                else {
                    return response()->json('Mail sent!', 200);
                }
        
            } catch (Exception $e) {
                return response()->json('Mail failed to be sent', 400);
            }
    }
    
    public function passwordResetMail(Request $request = null, $email, $token){

        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

        // Email server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';             //  smtp host
        $mail->SMTPAuth = true;
        $mail->Username = 'register@tayari.co.tz';   //  sender username
        $mail->Password = 'puagjnklvoisxcux';       // sender password
        $mail->SMTPSecure = 'ssl';                  // encryption - ssl/tls
        $mail->Port = 465;                          // port - 587/465

        $mail->setFrom('register@tayari.co.tz', 'Reset password');
        $mail->addAddress($email);

        $mail->isHTML(true);                // Set email content format to HTML

        $mail->Subject = 'Reset password link';
        $mail->Body    = "<html>
        You have requested to reset your password from the Tayari restaurant
        dashboard. <br><br>

        Please click the following link to head to reset your password:
        <a href='https://restaurants.tayari.co.tz/password/reset/$token' target='_blank'>https://restaurants.tayari.co.tz/password/reset/$token</a>

        <br><br>
        
            Thanks,<br>
            Tayari
            </html>";

            if( !$mail->send() ) {
                return response()->json('Mail failed to be sent', 400);
            }
            
            else {
                return response()->json('Mail sent!', 200);
            }
    
        } catch (Exception $e) {
            return response()->json('Mail failed to be sent', 400);
        }
    }
}
