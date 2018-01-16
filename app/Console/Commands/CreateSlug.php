<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use Config;
use DB;

class CreateSlug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createSlug {--table=} {--name=} {--slug=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Table\'s Slug';

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
        $tableName = $this->option('table');
        $nameField = $this->option('name');
        $slugField = $this->option('slug');

        $professionsData = DB::table($tableName)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                        ->get();
        $count = 1;
        foreach ($professionsData as $key => $value) {
            $slug = $this->clean($value->{$nameField});

            $checkSlug = DB::table($tableName)
                        ->where($slugField, $slug)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                        ->first();
            if($checkSlug){
                $slug = $this->clean($value->{$nameField}).'-'.time().$value->id;
            }

            $response = DB::table($tableName)->where('id',$value->id)->update([$slugField => $slug]);

            if($response){
                echo $count."/".count($professionsData)."\n";
                $count++;
            }else{
                echo "Failed Id : ".$value->id." Name : ".$value->{$nameField}."\n";
            }

        }
    }

    public function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return strtolower(preg_replace('/-+/', '-', $string)); // Replaces multiple hyphens with single one.
    }
}