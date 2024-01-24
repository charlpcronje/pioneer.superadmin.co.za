<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Twilio\Rest\Client;

class TwilioMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_key'));
        $number = "+27742291898";
        $response = $client->messages
            ->create("whatsapp:{$number}", // to
                ["from" => "whatsapp:+14155238886", "body" => "WhatApps from twilio"]);
        dd($response);
        return 0;
    }
}
