<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Video extends Model
{
    protected $table = 'pro_v_video';
    protected $guarded = [];

    public function getAllVideo()
    {
        $videoDetail = Video::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $videoDetail;
    }

    public function saveVideoDetail($videoData)
    {
        if ($videoData['id'] != '' && $videoData['id'] > 0) {
            $return = $this->where('id', $videoData['id'])->update($videoData);
        } else {
            $return = $this->create($videoData);
        }
        return $return;
    }

    public function deleteVideo($id) 
    {
        $video         = $this->find($id);
        $video->deleted = config::get('constant.DELETED_FLAG');
        $response          = $video->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getAllVideoDetail() 
    {
        $result = Video::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }

    public function getMoreVideos($id)
    {
        $videos = Video::select('*')
                        ->where('id', '<', $id)
                        ->where('deleted' ,'1')
                        ->orderBy('id','DESC')
                        ->limit(12)
                        ->get();
        return $videos;
    }

    public function getVideos($slot = "")
    {
        if ($slot > 0) {
            $slot = $slot * 12;
        }
        $result = Video::select('*')
                        ->where('deleted' ,'1')
                        ->orderBy('id','DESC')
                        ->skip($slot)
                        ->take(12)
                        ->get()
                        ->toArray();
        return $result;
    }

    public function loadMoreVideoCount($id)
    {
        $result = Video::select('*')
                        ->where('id', '<', $id)
                        ->where('deleted' ,'1')
                        ->orderBy('created_at','DESC')
                        ->count();
        return $result;
    }

}
