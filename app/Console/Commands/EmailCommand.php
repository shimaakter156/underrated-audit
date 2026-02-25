<?php

namespace App\Console\Commands;

use App\Http\Controllers\Web\EmailJobController;
use Illuminate\Console\Command;

class EmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch emails';

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
        $emailController = new EmailJobController();
        $emailController->dispatchJob();
        return 1;
    }
}
