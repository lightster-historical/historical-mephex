<?php


require_once PATH_LIB . 'com/mephex/input/EmailInput.php';


class Email
{   
    // sends an e-mail with various headers set
    public static function send ($to, $from, $subject, $message, $cc = null, $bcc = null, $replyto = null, $html = false)
    {
        $headers = 'MIME-Version: 1.0' . "\n";

        // if HTML should be parsed, set the content type
        if($html)
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

        $headers .= 'From: ' . $from . "\n";

        // set the carbon-copy header
        if(is_array($cc))
            $headers .= 'Cc: ' . implode(', ', $cc) . "\n";
        else if(!is_null($cc) and $cc != "")
            $headers .= 'Cc: ' . $cc . "\n";

        // set the blind-carbon-copy header
        if(is_array($bcc))
            $headers .= 'Bcc: ' . implode(', ', $bcc) . "\n";
        else if(!is_null($bcc) and $bcc != "")
            $headers .= 'Bcc: ' . $bcc . "\n";

        // set the reply-to header
        if(!is_null($replyto) and $replyto != "")
            $headers .= 'Reply-To: ' . $replyto . "\n";

        // identify PHP as the mailer of the e-mail
        $headers .= 'X-Mailer: PHP/' . phpversion();

        // send the e-mail message
        mail($to, $subject, $message, $headers);
    }
    
    
    public static function isValid($email)
    {
        return EmailInput::getInstance()->isValid($email);
    }
}


?>
