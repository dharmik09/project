<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications;

class RemoveNotificationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RemoveNotificationData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Notification Data';

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
     * @return mixed
     */
    public function handle()
    {
        $objNotification = new Notifications();
        $result = $objNotification->deleteNotificationData();
        $this->info('Notifications Deleted successfully!');
    }
}
