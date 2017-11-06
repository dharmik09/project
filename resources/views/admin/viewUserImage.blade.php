@extends('layouts.admin-master')

@section('content')



<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1cartoonicons')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">

        <div class="col-md-12">
            <div class="box box-primary">
                <form id="formDeleteIcons" onsubmit="return confirm('Do you really want to delete selected images?');" class="form-horizontal" method="post" action="{{ url('/admin/deleteSelectedIcon') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">                            
                <div class="box-header with-border">                                          
                        <div class="col-md-3">                            
                            <h3 class="box-title">{{trans('labels.userimage')}}</h3>
                        </div>
                        <div class="pull-right col-md-3">
                            <input type="submit" class="btn btn-primary btn-flat" name="deleteIcon" id="deleteIcon" value="Delete Selected Images"/>
                        </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <th>
                                <span class="user_select_mail cst_user_select_mail">
                                    <input type="checkbox" id="checkall" name="checkall" class="checkbox checkall custom_checkbox">
                                    <label for="checkall"><em></em><span></span></label>                                  
                                </span>
                            </th>
                            <th>{{trans('labels.username')}}</th>
                            <th>Icon Name</th>
                            <th>{{trans('labels.cartooniconheadimage')}}</th>
<!--                            <th>{{trans('labels.humaniconheadprofession')}}</th>-->
                            <th>{{trans('labels.useraction')}}</th>
                            <th>{{trans('labels.uploaddate')}}</th>
                        </tr>

                        @forelse($level1cartoonicon as $level1icon)
                        <tr>
                            <td>
                                <span class="user_select_mail cst_user_select_mail">
                                    <input type="checkbox" name="deleteIcons[{{$level1icon->id}}]" value="{{$level1icon->id}}" id="icon_{{$level1icon->id}}" class="indi_checkboc custom_checkbox">
                                    <label for="icon_{{$level1icon->id}}"><em></em><span></span></label>
                                </span>
                            </td>
                            <td>
                                {{$level1icon->t_name}}
                            </td>
                            <td>{{$level1icon->ci_name}}</td>

                            <?php
//                                    $professionName = explode(',', $level1icon->pfname);
//                                    foreach($professionName as $proName)
//                                    {
//                                        echo $proName."<br />";
//                                    }
                            ?>

                            <td>
                                <?php if (File::exists(public_path($cartoonThumbPath . $level1icon->ci_image)) && $level1icon->ci_image != '') { ?>
                                    <img src="{{asset($cartoonThumbPath.$level1icon->ci_image)}}" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>" />

                                <?php } else { ?>
                                    <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">

                                <?php } ?>
                            </td>
                            <td>
                                <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteIcon')}}/{{$level1icon->id}}?&tid=<?php echo $level1icon->teenagerid; ?>"><i class="i_delete fa fa-trash"></i></a>
                            </td>
                            <td>
                                {{$level1icon->updated_at}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse
                    </table>
                    @if (isset($level1cartoonicon) && !empty($level1cartoonicon))
                    <div class="pull-right">
                        <?php echo $level1cartoonicon->render(); ?>
                    </div>
                    @endif
                </div>
               </form>
            </div>
        </div>
    </div>
</section>

@stop

@section('script')
<script type="text/javascript">
    jQuery(document).ready(function ()
    {        
        $("#checkall").on("change", function () {
            $(".custom_checkbox").prop("checked", this.checked);
        });
        
        $(".indi_checkboc").on("change", function () {
            $("#checkall").prop("checked", false);
        });
        
<?php if (isset($id) && $id != '0' && $id != '') { ?>
            var validationRules = {
                l1ci_name: {
                    required: true
                },
                ci_category: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }

<?php } else { ?>
            var validationRules = {
                l1ci_name: {
                    required: true
                },
                l1ci_image: {
                    required: true
                },
                ci_category: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>

        $("#addCartoonIcon").validate({
            rules: validationRules,
            messages: {
                l1ci_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                l1ci_image: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                ci_category: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        })

    });
</script>
@stop