@extends('layouts.admin-master')

@section('content')
<section class="content-header">
    <h1>
        Media of :{!!$level4IntermediateActivityDetail->l4ia_question_text!!}
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">
                <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/editlevel4IntermediateActivity') }}/{{$level4IntermediateActivityDetail->id}}">Back</a>
            </div>
        </div>
        <div class="box-body">
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <form id="intermediateActivity" class="form-horizontal" method="post" action="{{ url('/admin/savelevel4IntermediateMedia') }}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="question_id" value="{{$level4IntermediateActivityDetail->id}}">
                <div class="box-body">
                    <?php $videoCount = 0;?>
                    @forelse($questionMedia as $media)
                    <?php
                      $image = ($media->l4iam_media_name != '' && Storage::disk('s3')->exists($questionThumbImagePath.$media->l4iam_media_name)) ? Config::get('constant.DEFAULT_AWS').$questionThumbImagePath.$media->l4iam_media_name : asset($questionThumbImagePath.'proteen-logo.png');
                    ?> 
                    @if($media->l4iam_media_type == 'I')
                    <div>
                      <table class='l4-intermediate-media'>
                        <tr class="header_text">
                          <td class="img">
                              <span class="multi_image_setup">
                                <input type='file' name="question_image[{{$media->id}}]" data-imgsel="#{{$media->id}}" class="img_select" />
                                <span class="img_replace">
                                    <img class="img_view" src="{{$image}}" id="{{$media->id}}"/>
                                </span>
                             </span>
                          </td>
                          <td class="text_area">
                              <textarea style="height: 120px;" name="question_image_description[{{$media->id}}]">{{isset($media->l4iam_media_desc)?$media->l4iam_media_desc:''}}</textarea>
                          </td>
                          <td class="button_delete" style="vertical-align: middle;">
                              <input type="button" value="Delete" onclick="deleteLevel4Media({{$media->id}},'{{$media->l4iam_media_name}}','{{$media->l4iam_media_type}}');">
                          </td>
                        </tr>
                      </table>
                    </div>
                    @endif
                    @if($media->l4iam_media_type == 'V')
                    <?php $videoCount = 1; $videoCode = Helpers::youtube_id_from_url($media->l4iam_media_name); ?>
                    @if($videoCode != '')
                    <div>
                      <table class='l4-intermediate-media'>
                        <tr class="header_text">
                          <td class="img">
                                <iframe  id="level4_video" width="150" height="120"
                                    src="https://www.youtube.com/embed/{{$videoCode}}" >
                               </iframe >
                          </td>
                          <td class="text_area">
                              <input type="text" name="question_video[{{$media->id}}]" class="form-control" value="{{isset($media->l4iam_media_name)?$media->l4iam_media_name:''}}">
                          </td>
                          <td class="button_delete">
                              <input type="button" value="Delete" onclick="deleteLevel4Media({{$media->id}},'{{$media->l4iam_media_name}}','{{$media->l4iam_media_type}}');">
                          </td>
                        </tr>
                      </table>
                    </div>

                    @endif
                    @endif
                    @empty
                    <div>No Media found for this question</div></br>
                    <div>You can add more images</div>
                    @endforelse

                    <?php $totalMedia = count($questionMedia);  ?>
                    @for ($i = $totalMedia-$videoCount; $i < 2; $i++)
                    <div>
                      <table class='l4-intermediate-media'>
                        <tr class="header_text">
                          <td class="img">
                              <span class="multi_image_setup">
                                <input type='file' name="question_image[{{$lastMediaId+$i}}]" data-imgsel="#{{$lastMediaId+$i}}" class="img_select" />
                                <span class="img_replace">
                                    <img class="img_view" src="" id="{{$lastMediaId+$i}}" alt="Add Question image" />
                                </span>
                             </span>
                          </td>
                          <td class="text_area">
                              <textarea style="height: 120px;" name="question_image_description[{{$lastMediaId+$i}}]"></textarea>
                          </td>

                        </tr>
                      </table>
                    </div>
                    @endfor
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    function deleteLevel4Media(media_id,media_name,media_type)
    {
        res = confirm('Are you sure you want to delete this record?');
        if(res){
        $.ajax({
            url: "{{ url('admin/deleteLevel4IntermediateMediaById') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "media_id": media_id,
                "media_name": media_name,
                "media_type": media_type
            },
            success: function(response) {
               location.reload();
            }
        });
        }else{
            return false;
        }
    }
</script>
@stop