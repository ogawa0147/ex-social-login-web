<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    protected $options;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Build the message.
     *
     * view()メソッドでHTMLメールのビューをセット
     * text()メソッドで平文メールのビューをセット
     * subject()メソッドでメールのタイトルをセット
     * with()メソッドでビューに渡す変数をセット
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->options['template_html'])
                    ->text($this->options['template_plain'])
                    ->from($this->options['from'])
                    ->subject($this->options['subject'])
                    ->with($this->options['with']);
    }
}
