<?php

/**
 * This file is part of the Wicked package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-02
 * @version 0.1
 */
namespace wicked\tools;

class Mail
{

    /** @var array */
    public $to = [];

    /** @var string */
    public $from;

    /** @var string */
    public $subject;

    /** @var string */
    public $content;


    /**
     * @return bool
     */
    public function send()
    {
        // create header
        $headers = 'From: ' . $this->from . "\r\n" .
            'Reply-To: ' . $this->from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        // checker
        $success = true;

        // send every mail
        foreach((array)$this->to as $to)
        {
            $result = mail($to, $this->subject, $this->content, $headers);

            if(!$result)
                $success = false;
        }

        return $success;
    }


    /**
     * Shortcut
     * @param $from
     * @param $to
     * @param $subject
     * @param $content
     * @return bool
     */
    public static function forge($from, $to, $subject, $content)
    {
        $mail = new self();
        $mail->from = $from;
        $mail->to = $to;
        $mail->subject = $subject;
        $mail->content = $content;

        return $mail->send();
    }

}
