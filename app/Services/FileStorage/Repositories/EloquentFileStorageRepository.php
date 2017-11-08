<?php

namespace App\Services\FileStorage\Repositories;

use DB;
use Auth;
use Config;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use Illuminate\Support\Facades\Storage;

class EloquentFileStorageRepository extends EloquentBaseRepository implements FileStorageRepository 
{
	public function addFileToStorage($fileName, $folderName = "", $file, $storageName = "")
	{
		$url = "";
		if($file && $fileName != "")
		{
			$folderName = ($folderName != "") ? $folderName : "/";
			
			if($storageName != "" && strtolower($storageName) == "s3")
			{
				if(Storage::disk('s3')->put($folderName.$fileName, file_get_contents($file), 'public'))
				{
					//$url = Storage::disk('s3')->url($folderName.$fileName);		
					$url = $fileName;
				}
			}
			else
			{
				if(Storage::put($folderName.$fileName, file_get_contents($file), 'public'))
				{
					//$url = url(Storage::url($folderName.$fileName));		
					$url = $fileName;
				}	
			}
		}
		return $url;
	}

	public function deleteFileToStorage($fileName, $folderName = "", $storageName = "")
	{
		$return = false;
		if($fileName != "")
		{
			$folderName = ($folderName != "") ? $folderName : "/";
			
			if($storageName != "" && strtolower($storageName) == "s3")
			{
				if(Storage::disk('s3')->exists($folderName.$fileName))
				{
					if(Storage::disk('s3')->delete($folderName.$fileName))
					{
						$return = true;
					}
				}
			}
			else
			{
				if(Storage::exists($folderName.$fileName))
				{
					if(Storage::delete($folderName.$fileName))
					{
						$return = true;
					}
				}	
			}
		}
		return $return;
	}     

}