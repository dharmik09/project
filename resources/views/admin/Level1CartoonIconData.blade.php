<table id="listCartoonIcon" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{trans('labels.cartooniconheadname')}}</th>
                                <th>{{trans('labels.humaniconheadcategory')}}</th>
    <!--                            <th>{{trans('labels.humaniconheadprofession')}}</th>-->
                                <th>{{trans('labels.cartooniconheadimage')}}</th>
                                <th>{{trans('labels.cartooniconheadaction')}}</th>
                                
                            </tr>
                        </thead>
                        <tbody> 
                        
                    
@forelse($level1cartoonicon as $level1icon)
<tr>
    <td><input name="iconsCheckbox[]" type="checkbox" value="{{ $level1icon->id }}"></td>
    <td>
        {{$level1icon->ci_name}}
    </td>
    <td>
        {{$level1icon->cic_name}}
    </td>
    <td>
        <?php 
            $image = ($level1icon->ci_image != "" && isset($level1icon->ci_image)) ? Storage::url($cartoonThumbPath.$level1icon->ci_image) : asset('/backend/images/proteen_logo.png'); 
        ?>
        <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
    </td>
    <td>
        <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
        <a href="{{ url('/admin/editCartoon') }}/{{$level1icon->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
        <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteCartoon') }}/{{$level1icon->id}}"><i class="i_delete fa fa-trash"></i></a>
   </td>
</tr>
@empty
<tr>
    <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
</tr>
@endforelse
</tbody>
</table>