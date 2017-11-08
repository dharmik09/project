@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
            {{trans('labels.lblteencareermap')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addCareerMapping') }}" class="btn btn-block btn-primary">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/importExcel') }}" class="btn btn-block btn-primary">Import career HML</a>
        </div>
    </h1>
</section>

<section class="content">  
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">
                
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body" style="overflow-x:scroll;">
                    <table id="listCareerMapping" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.profession')}}</th>
                                <th>Scientific Reasoning</th>
                                <th>Verbal Reasoning</th>
                                <th>Numerical Ability</th>
                                <th>Logical Reasoning</th>
                                <th>Social Ability</th>
                                <th>Artistic Ability</th>
                                <th>Spatial Ability</th>
                                <th>Creativity</th>
                                <th>Clerical Ability</th>
                                <th>Doers Realistic</th>
                                <th>Thinkers Investigative</th>
                                <th>Creators Artistic</th>
                                <th>Helpers Social</th>
                                <th>Persuaders Enterprising</th>
                                <th>Organizers Conventional</th>
                                <th>Linguistic</th>
                                <th>Logical</th>
                                <th>Musical</th>
                                <th>Spatial</th>
                                <th>Bodily Kinesthetic</th>
                                <th>Naturalist</th>
                                <th>Interpersonal</th>
                                <th>Intrapersonal</th>
                                <th>Existential</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($careerdetail as $career)
                            <tr>
                                <th widht="10%">{{ $career->pf_name }}</th>
                                <th><?php echo isset($career->tcm_scientific_reasoning)?$career->tcm_scientific_reasoning:''; ?></th>
                                <th><?php echo isset($career->tcm_verbal_reasoning)?$career->tcm_verbal_reasoning:''; ?></th>
                                <th><?php echo isset($career->tcm_numerical_ability)?$career->tcm_numerical_ability:''; ?></th>
                                <th><?php echo isset($career->tcm_logical_reasoning)?$career->tcm_logical_reasoning:''; ?></th>
                                <th><?php echo isset($career->tcm_social_ability)?$career->tcm_social_ability:''; ?></th>
                                <th><?php echo isset($career->tcm_artistic_ability)?$career->tcm_artistic_ability:''; ?></th>
                                <th><?php echo isset($career->tcm_spatial_ability)?$career->tcm_spatial_ability:'';  ?></th>
                                <th><?php echo isset($career->tcm_creativity)?$career->tcm_creativity:'';  ?></th>
                                <th><?php echo isset($career->tcm_clerical_ability)?$career->tcm_clerical_ability:'';  ?></th>
                                <th><?php echo isset($career->tcm_doers_realistic)?$career->tcm_doers_realistic:''; ?></th>
                                <th><?php echo isset($career->tcm_thinkers_investigative)?$career->tcm_thinkers_investigative:''; ?></th>
                                <th><?php echo isset($career->tcm_creators_artistic)?$career->tcm_creators_artistic:'';  ?></th>
                                <th><?php echo isset($career->tcm_helpers_social)?$career->tcm_helpers_social:'';  ?></th>
                                <th><?php echo isset($career->tcm_persuaders_enterprising)?$career->tcm_persuaders_enterprising:''; ?></th>
                                <th><?php echo isset($career->tcm_organizers_conventional)?$career->tcm_organizers_conventional:'';  ?></th>
                                <th><?php echo isset($career->tcm_linguistic)?$career->tcm_linguistic:''; ?></th>
                                <th><?php echo isset($career->tcm_logical)?$career->tcm_logical:''; ?></th>
                                <th><?php echo isset($career->tcm_musical)?$career->tcm_musical:''; ?></th>
                                <th><?php echo isset($career->tcm_spatial)?$career->tcm_spatial:''; ?></th>
                                <th><?php echo isset($career->tcm_bodily_kinesthetic)?$career->tcm_bodily_kinesthetic:''; ?></th>
                                <th><?php echo isset($career->tcm_naturalist)?$career->tcm_naturalist:'';  ?></th>
                                <th><?php echo isset($career->tcm_interpersonal)?$career->tcm_interpersonal:''; ?></th>
                                <th><?php echo isset($career->tcm_intrapersonal)?$career->tcm_intrapersonal:''; ?></th>
                                <th><?php echo isset($career->tcm_existential)?$career->tcm_existential:''; ?></th>
                                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                <th><a href="{{ url('/admin/editCareerMapping') }}/{{$career->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a></th>
                            </tr>
                            @empty
                            <tr>
                                <td><center>{{trans('labels.norecordfound')}}</center></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#listCareerMapping').DataTable();
    });
</script>
@stop