<?php

namespace ShowHeroes\Passport\Console\Commands\Test;

use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tests:mail {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests mail configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        if (!$email) {
            $email = 'evgeny.leksunin@showheroes.com';
        }
        Mail::send(['text' => 'emails.test'], [], function($message) use ($email) {
            /** @var Message $message */
            $message
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->to($email)
                ->subject('Testing Mail service');
        });
        $this->info('Mail sent to ' . $email . ' !');
    }
}
