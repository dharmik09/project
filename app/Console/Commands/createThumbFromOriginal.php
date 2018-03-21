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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "Thumb creation Started on ".date("Y-m-d h:i:s A")."\n\n";

        ini_set('memory_limit', '20000M');
        ini_set('max_execution_time', 0);

        $source = 'uploads/'.$this->option('source');
        $destination = 'uploads/'.$this->option('destination');
        $width = $this->option('width');
        $height = $this->option('height');

        $originalFiles = Storage::disk('s3')->files($source);

        $bar = $this->output->createProgressBar(count($originalFiles));
        
        $bar->setBarCharacter('*');
        
        foreach ($originalFiles as $orignal){
            $bar->advance();
            // echo Storage::url($orignal)."\n";
            $fileName = basename($orignal);
            // echo $fileName."\n";

            $pathThumb = public_path($destination .'/'. $fileName);
            // echo $pathThumb."\n";

            Image::make(Storage::url($orignal))->resize($width,$height)->save($pathThumb);

            $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $destination, $pathThumb, "s3");                
            \File::delete($pathThumb);
        }

        $bar->finish();

        echo "\n\n";
        echo "Thumb creation completed on ".date("Y-m-d h:i:s A")."\n\n";
        echo "Total Images Converted ".count($originalFiles)."\n\n";
    }
}