<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Article;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\Merchant;
use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class MailMerchantReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail_merchant_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send report in email to merchant.';

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
        $merchants = Merchant::where('is_active', '1')->where('subscription_report', 1)->get();
        foreach ($merchants as $merchant) {
            $url = 'https://app.nucycle.com.my/merchant-report?secret_ki=51J3BLkKDKlGOEFRrOhlW4Vt4SzJqNtnVTKoYcPBTCuf0uD3wJyhnN0y4kV2xsR4pn8mAgIo4VDXXtc1GHpwWYka100QDHJ39uq&merchant_id=' . $merchant->id;
            $file = 'public_html/merchant_report/'.str_replace(" ","-",$merchant->name).'_'.date('Y-M').'.pdf';
            Browsershot::url($url)->setNodeBinary('/usr/bin/node')
                ->setNpmBinary('/usr/bin/npm')->setChromePath("/node_modules/puppeteer/.local-chromium/linux-901912/chrome-linux/chrome")->noSandbox()->save($file);
            $subject = "Nucycle Merchant Report For " . date('Y M');
            $message = "Hi " .$merchant->name .', Please find attached report for the month of '.date('M Y').'.';
            Helper::sendEmail($merchant->email, $subject, $message,$file);
        }
    }
}
