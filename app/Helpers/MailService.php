<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer;

class MailService
{
    // fromメール
    const FROM_INFO = 'info@example.com';
    const FROM_SUPPORT = 'support@example.com';

    // HTMLメールView
    const TEMPLATE_HTML = 'emails.html';

    // 平文メールView
    const TEMPLATE_PLAIN = 'emails.plain';

    protected $from;
    protected $to;
    protected $subject;
    protected $with;

    public static function forge()
    {
        return new static;
    }

    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function with($with, $data=[])
    {
        $this->with = array_merge($data, ['body' => $with]);
        return $this;
    }

    public function send()
    {
        $options = [
            'from'           => $this->from,
            'to'             => $this->to,
            'subject'        => $this->subject,
            'template_html'  => static::TEMPLATE_HTML,
            'template_plain' => static::TEMPLATE_PLAIN,
            'with'           => $this->with,
        ];
        Mail::to($this->to)->send(new Mailer($options));
    }
}
