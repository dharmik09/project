@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->

<section class="content-header">
    <h1>
        {{trans('labels.frmcareermapping')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($cmDetails) &&!empty($cmDetails)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.frmcareermapping')}}</h3>
                </div><!-- /.box-header -->
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{trans('validation.whoops')}}</strong>{{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addCareerMapping" class="form-horizontal" method="post" action="{{ url('/admin/saveAddCareerMapping') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                    <input type="hidden" name="id" value="<?php echo (isset($cmDetails) &&!empty($cmDetails)) ? $cmDetails->id : '0' ?>">
                    <input type="hidden" name="tcm_profession" value="<?php echo (isset($cmDetails) &&!empty($cmDetails)) ? $cmDetails->id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="professions" class="col-sm-2 control-label">Select Profession</label>
                            @if(isset($cmDetails) && !empty($cmDetails))
                            <?php
                            if (old('tcm_profession'))
                            $tcm_profession = old('tcm_profession');
                            elseif ($cmDetails)
                            $tcm_profession = $cmDetails->pf_name;
                            else
                            $tcm_profession = '';
                            ?>
                            <div class="col-sm-8">
                                <input type="hidden" class="form-control" name="tcm_profession_id" value="{{$cmDetails->tcm_profession}}" readonly="readonly">
                                <input type="text" class="form-control" name="tcm_profession" value="{{$tcm_profession}}" readonly="readonly">
                            </div>
                            @else


                            <div class="col-sm-8">

                                <?php
                                $professions = Helpers::getAvailableProfessions();
                                ?>
                                <select class="form-control" name="tcm_profession_id">
                                    <option value="">{{trans('labels.formlblselectprofessionheader')}}</option>
                                    <?php
                                    foreach($professions as $value)
                                    { ?>
                                        <option value="{{$value->id}}" >{{$value->pf_name}}({{$value->id}})</option>
                                    <?php } ?>
                                </select>

                            </div>
                            @endif
                        </div>


                        <div class="form-group">

                            <h4 class="box-header">Multiple Intelligence Type</h4>

                            <div class="form-group">

                                <?php
                                if (old('tcm_linguistic'))
                                $tcm_linguistic = old('tcm_linguistic');
                                elseif ($cmDetails)
                                $tcm_linguistic = $cmDetails->tcm_linguistic;
                                else
                                $tcm_linguistic = '';
                                ?>
                                <label for="linguistic" class="col-sm-2 control-label">Linguistic</label>
                                <div class="col-sm-1">
                                    <select name="tcm_linguistic">
                                        <option value="L" <?php if ($tcm_linguistic == "L") echo 'selected="selected"'; ?> >L</option>
                                        <option value="M" <?php if ($tcm_linguistic == "M") echo 'selected="selected"'; ?> >M</option>
                                        <option value="H" <?php if ($tcm_linguistic == "H") echo 'selected="selected"'; ?> >H</option>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_logical'))
                                $tcm_logical = old('tcm_logical');
                                elseif ($cmDetails)
                                $tcm_logical = $cmDetails->tcm_logical;
                                else
                                $tcm_logical = '';
                                ?>
                                <label for="logical" class="col-sm-2 control-label">Logical</label>
                                <div class="col-sm-1">
                                    <select name="tcm_logical">
                                        <option value="L" <?php if ($tcm_logical == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_logical == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_logical == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_musical'))
                                $tcm_musical = old('tcm_musical');
                                elseif ($cmDetails)
                                $tcm_musical = $cmDetails->tcm_musical;
                                else
                                $tcm_musical = '';
                                ?>
                                <label for="musical" class="col-sm-2 control-label">Musical</label>
                                <div class="col-sm-1">
                                    <select name="tcm_musical">
                                        <option value="L" <?php if ($tcm_musical == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_musical == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_musical == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">

                                <?php
                                if (old('tcm_spatial'))
                                $tcm_spatial = old('tcm_spatial');
                                elseif ($cmDetails)
                                $tcm_spatial = $cmDetails->tcm_spatial;
                                else
                                $tcm_spatial = '';
                                ?>
                                <label for="spatial" class="col-sm-2 control-label">Spatial</label>
                                <div class="col-sm-1">
                                    <select name="tcm_spatial">
                                        <option value="L" <?php if ($tcm_spatial == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_spatial == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_spatial == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_bodily_kinesthetic'))
                                $tcm_bodily_kinesthetic = old('tcm_bodily_kinesthetic');
                                elseif ($cmDetails)
                                $tcm_bodily_kinesthetic = $cmDetails->tcm_bodily_kinesthetic;
                                else
                                $tcm_bodily_kinesthetic = '';
                                ?>
                                <label for="bodily_kinesthetic" class="col-sm-2 control-label">Bodily Kinesthetic</label>
                                <div class="col-sm-1">
                                    <select name="tcm_bodily_kinesthetic">
                                        <option value="L" <?php if ($tcm_bodily_kinesthetic == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_bodily_kinesthetic == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_bodily_kinesthetic == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_naturalist'))
                                $tcm_naturalist = old('tcm_naturalist');
                                elseif ($cmDetails)
                                $tcm_naturalist = $cmDetails->tcm_naturalist;
                                else
                                $tcm_naturalist = '';
                                ?>
                                <label for="naturalist" class="col-sm-2 control-label">Naturalist</label>
                                <div class="col-sm-1">
                                    <select name="tcm_naturalist">
                                        <option value="L" <?php if ($tcm_naturalist == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_naturalist == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_naturalist == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">

                                <?php
                                if (old('tcm_interpersonal'))
                                $tcm_interpersonal = old('tcm_interpersonal');
                                elseif ($cmDetails)
                                $tcm_interpersonal = $cmDetails->tcm_interpersonal;
                                else
                                $tcm_interpersonal = '';
                                ?>
                                <label for="interpersonal" class="col-sm-2 control-label">Interpersonal</label>
                                <div class="col-sm-1">
                                    <select name="tcm_interpersonal">
                                        <option value="L" <?php if ($tcm_interpersonal == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_interpersonal == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_interpersonal == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_intrapersonal'))
                                $tcm_intrapersonal = old('tcm_intrapersonal');
                                elseif ($cmDetails)
                                $tcm_intrapersonal = $cmDetails->tcm_intrapersonal;
                                else
                                $tcm_intrapersonal = '';
                                ?>

                                <label for="intrapersonal" class="col-sm-2 control-label">Intrapersonal</label>
                                <div class="col-sm-1">
                                    <select name="tcm_intrapersonal">
                                        <option value="L" <?php if ($tcm_intrapersonal == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_intrapersonal == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_intrapersonal == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_existential'))
                                $tcm_existential = old('tcm_existential');
                                elseif ($cmDetails)
                                $tcm_existential = $cmDetails->tcm_existential;
                                else
                                $tcm_existential = '';
                                ?>
                                <label for="existential" class="col-sm-2 control-label">Existential</label>
                                <div class="col-sm-1">
                                    <select name="tcm_existential">
                                        <option value="L" <?php if ($tcm_existential == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_existential == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_existential == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <h4 class="box-header">Personality Type</h4>
                            <div class="form-group">
                                <?php
                                if (old('tcm_doers_realistic'))
                                $tcm_doers_realistic = old('tcm_doers_realistic');
                                elseif ($cmDetails)
                                $tcm_doers_realistic = $cmDetails->tcm_doers_realistic;
                                else
                                $tcm_doers_realistic = '';
                                ?>
                                <label for="doers_realistic" class="col-sm-2 control-label">Doers Realistic</label>
                                <div class="col-sm-1">
                                    <select name="tcm_doers_realistic">
                                        <option value="L" <?php if ($tcm_doers_realistic == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_doers_realistic == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_doers_realistic == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_thinkers_investigative'))
                                $tcm_thinkers_investigative = old('tcm_thinkers_investigative');
                                elseif ($cmDetails)
                                $tcm_thinkers_investigative = $cmDetails->tcm_thinkers_investigative;
                                else
                                $tcm_thinkers_investigative = '';
                                ?>
                                <label for="thinkers_investigative" class="col-sm-2 control-label">Thinkers Investigative</label>
                                <div class="col-sm-1">
                                    <select name="tcm_thinkers_investigative">
                                        <option value="L" <?php if ($tcm_thinkers_investigative == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_thinkers_investigative == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_thinkers_investigative == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_helpers_social'))
                                $tcm_helpers_social = old('tcm_helpers_social');
                                elseif ($cmDetails)
                                $tcm_helpers_social = $cmDetails->tcm_helpers_social;
                                else
                                $tcm_helpers_social = '';
                                ?>
                                <label for="helpers_social" class="col-sm-2 control-label">Helpers Social</label>
                                <div class="col-sm-1">
                                    <select name="tcm_helpers_social">
                                        <option value="L" <?php if ($tcm_helpers_social == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_helpers_social == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_helpers_social == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group">

                                <?php
                                if (old('tcm_organizers_conventional'))
                                $tcm_organizers_conventional = old('tcm_organizers_conventional');
                                elseif ($cmDetails)
                                $tcm_organizers_conventional = $cmDetails->tcm_organizers_conventional;
                                else
                                $tcm_organizers_conventional = '';
                                ?>

                                <label for="organizers_conventional" class="col-sm-2 control-label">Organizers Conventional</label>
                                <div class="col-sm-1">
                                    <select name="tcm_organizers_conventional">
                                        <option value="L" <?php if ($tcm_organizers_conventional == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_organizers_conventional == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_organizers_conventional == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_persuaders_enterprising'))
                                $persuaders_enterprising = old('tcm_persuaders_enterprising');
                                elseif ($cmDetails)
                                $persuaders_enterprising = $cmDetails->tcm_persuaders_enterprising;
                                else
                                $persuaders_enterprising = '';
                                ?>
                                <label for="persuaders_enterprising" class="col-sm-2 control-label">Persuaders Enterprising</label>
                                <div class="col-sm-1">
                                    <select name="tcm_persuaders_enterprising">
                                        <option value="L" <?php if ($persuaders_enterprising == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($persuaders_enterprising == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($persuaders_enterprising == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_creators_artistic'))
                                $tcm_creators_artistic = old('tcm_creators_artistic');
                                elseif ($cmDetails)
                                $tcm_creators_artistic = $cmDetails->tcm_creators_artistic;
                                else
                                $tcm_creators_artistic = '';
                                ?>
                                <label for="creators_artistic" class="col-sm-2 control-label">Creators Artistic</label>
                                <div class="col-sm-1">
                                    <select name="tcm_creators_artistic">
                                        <option value="L" <?php if ($tcm_creators_artistic == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_creators_artistic == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_creators_artistic == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">

                            <h4 class="box-header">Aptitude Type</h4>

                            <div class="form-group">
                                <?php
                                if (old('tcm_scientific_reasoning'))
                                $tcm_scientific_reasoning = old('tcm_scientific_reasoning');
                                elseif ($cmDetails)
                                $tcm_scientific_reasoning = $cmDetails->tcm_scientific_reasoning;
                                else
                                $tcm_scientific_reasoning = '';
                                ?>
                                <label for="scientific_reasoning" class="col-sm-2 control-label">Scientific Reasoning</label>
                                <div class="col-sm-1">
                                    <select name="tcm_scientific_reasoning">
                                        <option value="L" <?php if ($tcm_scientific_reasoning == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_scientific_reasoning == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_scientific_reasoning == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_verbal_reasoning'))
                                $tcm_verbal_reasoning = old('tcm_verbal_reasoning');
                                elseif ($cmDetails)
                                $tcm_verbal_reasoning = $cmDetails->tcm_verbal_reasoning;
                                else
                                $tcm_verbal_reasoning = '';
                                ?>
                                <label for="verbal_reasoning" class="col-sm-2 control-label">Verbal Reasoning</label>
                                <div class="col-sm-1">
                                    <select name="tcm_verbal_reasoning">
                                        <option value="L" <?php if ($tcm_verbal_reasoning == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_verbal_reasoning == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_verbal_reasoning == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_numerical_ability'))
                                $tcm_numerical_ability = old('tcm_numerical_ability');
                                elseif ($cmDetails)
                                $tcm_numerical_ability = $cmDetails->tcm_numerical_ability;
                                else
                                $tcm_numerical_ability = '';
                                ?>
                                <label for="numerical_ability" class="col-sm-2 control-label">Numerical Ability</label>
                                <div class="col-sm-1">
                                    <select name="tcm_numerical_ability">
                                        <option value="L" <?php if ($tcm_numerical_ability == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_numerical_ability == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_numerical_ability == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="logical_reasoning" class="col-sm-2 control-label">Logical Reasoning</label>
                                <?php
                                if (old('tcm_logical_reasoning'))
                                $tcm_logical_reasoning = old('tcm_logical_reasoning');
                                elseif ($cmDetails)
                                $tcm_logical_reasoning = $cmDetails->tcm_logical_reasoning;
                                else
                                $tcm_logical_reasoning = '';
                                ?>
                                <div class="col-sm-1">

                                    <select name="tcm_logical_reasoning">
                                        <option value="L" <?php if ($tcm_logical_reasoning == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_logical_reasoning == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_logical_reasoning == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_social_ability'))
                                $tcm_social_ability = old('tcm_social_ability');
                                elseif ($cmDetails)
                                $tcm_social_ability = $cmDetails->tcm_social_ability;
                                else
                                $tcm_social_ability = '';
                                ?>
                                <label for="social_ability" class="col-sm-2 control-label">Social Ability</label>
                                <div class="col-sm-1">
                                    <select name="tcm_social_ability">
                                        <option value="L" <?php if ($tcm_social_ability == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_social_ability == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_social_ability == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_artistic_ability'))
                                $tcm_artistic_ability = old('tcm_artistic_ability');
                                elseif ($cmDetails)
                                $tcm_artistic_ability = $cmDetails->tcm_artistic_ability;
                                else
                                $tcm_artistic_ability = '';
                                ?>
                                <label for="artistic_ability" class="col-sm-2 control-label">Artistic Ability</label>
                                <div class="col-sm-1">
                                    <select name="tcm_artistic_ability">
                                        <option value="L" <?php if ($tcm_artistic_ability == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_artistic_ability == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_artistic_ability == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group">

                                <?php
                                if (old('tcm_spatial_ability'))
                                $tcm_spatial_ability = old('tcm_spatial_ability');
                                elseif ($cmDetails)
                                $tcm_spatial_ability = $cmDetails->tcm_spatial_ability;
                                else
                                $tcm_spatial_ability = '';
                                ?>
                                <label for="spatial_ability" class="col-sm-2 control-label">Spatial Ability</label>
                                <div class="col-sm-1">
                                    <select name="tcm_spatial_ability">
                                        <option value="L" <?php if ($tcm_spatial_ability == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_spatial_ability == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_spatial_ability == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_creativity'))
                                $tcm_creativity = old('tcm_creativity');
                                elseif ($cmDetails)
                                $tcm_creativity = $cmDetails->tcm_creativity;
                                else
                                $tcm_creativity = '';
                                ?>
                                <label for="creativity" class="col-sm-2 control-label">Creativity</label>
                                <div class="col-sm-1">
                                    <select name="tcm_creativity">
                                        <option value="L" <?php if ($tcm_creativity == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($tcm_creativity == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($tcm_creativity == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                                <?php
                                if (old('tcm_clerical_ability'))
                                $clerical_ability = old('tcm_clerical_ability');
                                elseif ($cmDetails)
                                $clerical_ability = $cmDetails->tcm_clerical_ability;
                                else
                                $clerical_ability = '';
                                ?>
                                <label for="clerical_ability" class="col-sm-2 control-label">Clerical Ability</label>
                                <div class="col-sm-1">
                                    <select name="tcm_clerical_ability">
                                        <option value="L" <?php if ($clerical_ability == "L") echo 'selected="selected"'; ?> >L</optin>
                                        <option value="M" <?php if ($clerical_ability == "M") echo 'selected="selected"'; ?> >M</optin>
                                        <option value="H" <?php if ($clerical_ability == "H") echo 'selected="selected"'; ?> >H</optin>
                                    </select>
                                </div>

                            </div>

                        </div>

                    </div>

<?php // $row++; }  ?>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('/admin/careerMapping') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>  
        </div>
    </div>
</section><!-- /.content -->

<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">

jQuery(document).ready(function () {

    var validationRules = {
        tcm_profession_id: {
            required: true
        }

    }

    $("#addCareerMapping").validate({
        rules: validationRules,
        messages: {
            tcm_profession_id: {
                required: "<?php echo trans('validation.professionoptionrequired'); ?>"
            }
        }
    });
});
</script>
@stop



