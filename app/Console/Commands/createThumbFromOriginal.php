<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use Helpers;
use Config;
use File;
use Storage;
use Excel;
use Image;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class createThumbFromOriginal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Default Path is set for command is public/uploads/ for aws s3
     *
     * php artisan createThumbFromOriginal --source="" --destination= --width=300 --height=300
     *
     * Example Command : php artisan createThumbFromOriginal --source="testing/original" --destination="testing/thumb" --width=300 --height=300
     *
     * @var string
     */
    protected $signature = 'createThumbFromOriginal {--source=} {--destination=} {--width=} {--height=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Thumb images from orignal by dynamically add source and destination folder with size';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FileStorageRepository $fileStorageRepository)
    {
        parent::__construct();
        $this->fileStorageRepository = $fileStorageRepository;
        $this->log = new Logger('create-thumnb-s3');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "Thumb creation Started on ".date("Y-m-d h:i:s A")."\n\n";
        $this->log->info("Thumb creation Started on ".date("Y-m-d h:i:s A")."\n\n");

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $source = 'uploads/'.$this->option('source');
        $destination = 'uploads/'.$this->option('destination');
        $width = $this->option('width');
        $height = $this->option('height');

        $originalFiles = Storage::disk('s3')->files($source);

        $bar = $this->output->createProgressBar(count($originalFiles));
        
        $bar->setBarCharacter('*');
        
        $this->log->info("Image Path ".$destination);
        
        $countAllImages = count($originalFiles);

        foreach ($originalFiles as $key => $orignal){

            $bar->advance();
            $ext = pathinfo($orignal, PATHINFO_EXTENSION);
            $extension = strtolower($ext);
            
            if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif'){
                // echo Storage::url($orignal)."\n";
                $fileName = basename($orignal);
                // echo $fileName."\n";

                $pathThumb = public_path($destination .'/'. $fileName);
                // echo $pathThumb."\n";                
                
                Image::make(Storage::url($orignal))
                            ->resize($width, null, function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->save($pathThumb);

                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $destination.'/', $pathThumb, "s3");                
                \File::delete($pathThumb);
                $this->log->info("Completed ".($key+1)."/".$countAllImages." => ".$fileName);
            }
        }

        
        $bar->finish();

        // $thumbFiles = Storage::disk('s3')->files($destination);
        // print_r($originalFiles);
        // print_r($thumbFiles);
        
        echo "\n\n";
        echo "Total Images Converted ".count($originalFiles)."\n\n";
        echo "Thumb creation completed on ".date("Y-m-d h:i:s A")."\n\n";

        $this->log->info("Total Images Converted ".count($originalFiles));
        $this->log->info("Thumb creation completed on ".date("Y-m-d h:i:s A"));
    }
}