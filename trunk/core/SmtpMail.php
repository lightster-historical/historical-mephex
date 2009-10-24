<?php


require_once 'Mail.php'; // PEAR/Mail


class MxSmtpMail
{
    protected $factory;

    protected $lastErrorHandler;


    public function __construct($host, $port = 25, $auth = false, $debug = false, $timeout = 5, $attempts = 1)
    {
        $params = array
        (
            'host' => $host,
            'port' => $port,
            'timeout' => $timeout,
            'debug' => $debug,
            'persist' => true
        );

        if(is_array($auth))
        {
            if(array_key_exists('username', $auth)
                && array_key_exists('password', $auth))
            {
                if(array_key_exists('host', $auth))
                {
                    $stream = imap_open($auth['host']
                        , $auth['username'], $auth['password']);
                    $streamClosed = imap_close($stream);
                }
                else
                {
                    if(array_key_exists('type', $auth))
                        $params['auth'] = $auth['type'];
                    else
                        $params['auth'] = true;

                    $params['username'] = $auth['username'];
                    $params['password'] = $auth['password'];
                }
            }
        }

        $this->enableErrorHandling();

        $count = 0;
        while(!($this->factory instanceof Mail) && $count < $attempts)
        {
            $this->factory = Mail::factory('smtp', $params);
            $count++;
        }

        if(PEAR::isError($this->factory))
        {
            echo 'there was an error with Mail::factory()';
            print_r($this->factory);
            exit;
        }

        $this->disableErrorHandling();
    }


    public function sendMessage($to, $from, $subject, $body, $bcc = null, $cc = null, $returnPath = null, $replyTo = null, $attempts = 1)
    {
        $factory = $this->factory;

        if(is_null($returnPath))
            $returnPath = $from;
        if(is_null($replyTo))
            $replyTo = $from;

        $headers = array
        (
            'To' => $to,
            'From' => $from,
            'Return-Path' => $returnPath,
            'Reply-To' => $replyTo,
            'Subject' => $subject
        );

        if(!is_null($bcc))
            $headers['Bcc'] = $bcc;
        if(!is_null($cc))
            $headers['Cc'] = $cc;

        $this->enableErrorHandling();

        $count = 0;
        $return = false;
        while(!($return === true) && $count < $attempts)
        {
            $return = $factory->send($to, $headers, $body);
            $count++;
        }

        if(PEAR::isError($return))
        {
            echo 'there was an error with Mail::send()';
            print_r($return);
            exit;
        }

        $this->disableErrorHandling();

        return $return;
    }



    private function enableErrorHandling()
    {
        $this->lastErrorHandler = set_error_handler(array($this, 'errorCaught'));
    }

    private function disableErrorHandling()
    {
        if(!is_null($this->lastErrorHandler))
        {
            set_error_handler($this->lastErrorHandler);
        }
        else
        {
            restore_error_handler();
        }
    }

    public function errorCaught($num, $str, $file, $line, $context)
    {
        if($num != E_STRICT)
        {
            die("Error {$num}: {$str} on line {$line} in {$file}. ({$context})");
        }
    }
}



?>
