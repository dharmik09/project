@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Copy Concept
    </h1>
</section>

<section class="content"> 
    <div class="row">
        <div class="box box-info">
            <form id="copyConcept" class="form-horizontal" action="{{url('admin/saveCopyConcept/')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div style="text-align: center">
                        <h2>From</h2>
                    </div>
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Profession</label>
                        <div class="col-sm-9">
                            <select name="professionId" id="professionId" class="form-control chosen-select-width" onchange="getConcept(this.value)">  
                                <option value="">Select from profession</option>
                                <?php
                                foreach($professions as $key => $profession) {
                                    ?>                    
                                    <option value="{{$profession->id}}" <?php
                                    if (isset($professionid) && $professionid == $profession->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$profession->pf_name}}</option>               
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" id="profession_concept"> 
                        
                    </div>   
                    <div style="text-align: center">
                        <h2>To</h2>
                    </div>
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Profession</label>
                        <div class="col-sm-9">
                            <select name="to_profession_id" id="to_profession_id" class="form-control chosen-select-width">   
                                <option value="">Select to profession</option>
                                <?php
                                foreach($professions as $key => $profession) {
                                    ?>                    
                                    <option value="{{$profession->id}}" <?php
                                    if (isset($professionid) && $professionid == $profession->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$profession->pf_name}}</option>               
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-3">
                            <button id="search" type="submit" class="btn btn-primary btn-flat">Copy</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>    
</body>
</section>

@stop
@section('script')
<script src="{{ asset('backend/js/chosen.jquery.js')}}"></script>

<script type="text/javascript">
    
    jQuery(document).ready(function() {
            var validationRules = {
                professionId: {
                    required : true 
                },
                to_profession_id : {
                    required : true
                },
                "concept[]" : {
                    required : true
                },                
                template_description: {
                    emptyetbody : true
                },
                deleted : {
                    required : true
                }
            }

        $("#copyConcept").validate({
            ignore: [],
            rules : validationRules,
            messages : {
                professionId : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                to_profession_id : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                "concept[]": {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },                
                template_description : {
                    emptyetbody : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
    
    var config = {
        '.chosen-select': {},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"},
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }          
    var selectedProfession = $('select[name=professionId]').val(); 
    var concept = '<?php echo $concept?>';
    
    getConcept(selectedProfession,concept);
    function getConcept(professionid,concept)
    {
        $.ajax({
            url: "{{ url('/admin/getProfessionConcepts') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionid": professionid,
                "concept": concept,
                "all":0
            },
            success: function(response) {
                $('#profession_concept').html(response);
                $('#concept').chosen();
            }
        });
    }                            
</script>
@stop    

