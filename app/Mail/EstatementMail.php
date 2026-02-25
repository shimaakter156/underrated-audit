<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstatementMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->name = $data['name'];
        $this->staffId = $data['staffId'];
        $this->department = $data['department'];
        $this->designation = $data['designation'];
        $this->closingDate = $data['closingDate'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@aci-bd.com')->subject('AMS E-Statement Report')->view('mail.eStatement',[
            'data' => $this->data,
            'name' => $this->name,
            'staffId' => $this->staffId,
            'department' => $this->department,
            'designation' => $this->designation,
            'closingDate' => $this->closingDate
        ]);
    }
}
