<?php

        $html = '';

        // Question  Data Start

        $html .= '<div class="modal-dialog">';
        $html .= '<div class="modal-content model-add-coins">';
        $html .= '<div class="modal-header">';
        $html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        $html .= '<h3 class="modal-title" style="text-align: center;">Add ProCoins</h3>';
        $html .= '</div>';
        $html .= '<div class="modal-body">';

        $html .= '<form id="addTeenCoins" class="form-horizontal" method="post" action="'. url('/admin/save-coins-data-for-teenager').'">';
        $html .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
        $html .= '<input type="hidden" name="id" value="'. $teenagerDetail->id .'">';
        //$html .= '<input type="hidden" name="searchBy" value="'. $data['searchBy'] .'">';
        //$html .= '<input type="hidden" name="searchText" value="'. $data['searchText'] .'">';
        //$html .= '<input type="hidden" name="orderBy" value="'. $data['orderBy'] .'">';
        //$html .= '<input type="hidden" name="sortOrder" value="'. $data['sortOrder'] .'">';
        //$html .= '<input type="hidden" name="page" value="'. $data['page'] .'">';

        $html .= '<div class="form-group">';
        $html .= '<label for="t_coins" class="col-sm-3 control-label">Existing Procoins</label>';
        $html .= '<div class="col-sm-6">';
        $html .= '<span>'.$teenagerDetail->t_coins.'</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="t_coins" class="col-sm-3 control-label">Name</label>';
        $html .= '<div class="col-sm-6">';
        $html .= '<span>'.$teenagerDetail->t_name.'</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="t_coins" class="col-sm-3 control-label">Email</label>';
        $html .= '<div class="col-sm-6">';
        $html .= '<span>'.$teenagerDetail->t_email.'</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="t_coins" class="col-sm-3 control-label">Enter ProCoins</label>';
        $html .= '<div class="col-sm-6">';
        $html .= '<input type="text" class="form-control numeric" id="t_coins" name="t_coins" placeholder="Enter ProCoins" value=""/>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="box-footer">';
        $html .= '<div class="pull-right">';
        $html .= '<button type="submit" id="submit" class="btn btn-primary btn-flat" >Save</button>&nbsp;&nbsp;';
        $html .= '<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal" style="color: #FFFFFF;">Close</button>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</form>';

        $html .= '</div>';

        $html .= '</div>'; // modal-content end
        $html .= '</div>'; // modal-dialog end

        $html .= '<script type="text/javascript">';
        $html .= '$(document).on("keyup", ".numeric", function () {
                    if(this.value.indexOf("-") != 0){
                        this.value = this.value.replace(/[^0-9]/gi, "");
                     }
                     this.value = this.value.replace(/[^0-9-]/gi, "");
                  });';
        $html .= 'jQuery(document).ready(function() {
                  var loginRules = {
                      t_coins: {
                          required: true
                      }
                  };
                  $("#addTeenCoins").validate({
                      rules: loginRules,
                      messages: {
                          t_coins: {
                            required: "'. trans("validation.requiredfield") .'"
                          }
                      }
                  });
                  });';
        $html .= '</script>';


        echo $html;

?>