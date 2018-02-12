<span class="title dashboard_profession">
    <span class="profession_text" style="font-size: 14px;" title="{{$professionName}}">
        <span>{{$professionName}}</span>
    </span>
</span>
<a href="javascript:void(0);" class="close_next"><i class="fa fa-times" aria-hidden="true"></i></a>
<span>
    <?php $profession_acadamic_path = ($profession_acadamic_path != '')? $profession_acadamic_path : "No data found"; ?>
    <span class="flip_scroll">{!! strip_tags($profession_acadamic_path, "<br><p><ul><li><sub><sup><span>") !!}</span>
</span>

