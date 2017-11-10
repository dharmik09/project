<?php

        $html = '';

        // Question  Data Start

        $html .= '<div class="modal-dialog">';
        $html .= '<div class="modal-content model-add-coins-new">';
        $html .= '<div class="modal-header">';
        $html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        $html .= '<h3 class="modal-title" style="text-align: center;">Add ProCoins</h3>';
        $html .= '</div>';
        $html .= '<div class="modal-body">';

        $html .= '<form id="addCoins" class="form-horizontal" method="post" action="'. url('/admin/saveCoinsDataForTemplate').'">';
        $html .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
        $html .= '<input type="hidden" name="id" value="'. $data['template_Id'] .'">';
        $html .= '<input type="hidden" name="searchBy" value="'. $data['searchBy'] .'">';
        $html .= '<input type="hidden" name="searchText" value="'. $data['searchText'] .'">';
        $html .= '<input type="hidden" name="orderBy" value="'. $data['orderBy'] .'">';
        $html .= '<input type="hidden" name="sortOrder" value="'. $data['sortOrder'] .'">';
        $html .= '<input type="hidden" name="page" value="'. $data['page'] .'">';

        $html .= '<div class="form-group">';
        $html .= '<label for="gt_coins" class="col-sm-3 control-label">Enter ProCoins</label>';
        $html .= '<div class="col-sm-6">';
        $html .= '<input type="text" class="form-control numeric" id="gt_coins" name="gt_coins" placeholder="Enter ProCoins" value=""/>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="box-footer">';
        $html .= '<div class="pull-right">';
        $html .= '<button type="submit" id="submit" class="btn btn-primary btn-flat" >Save</button>';
        $html .= '<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal" style="color: #FFFFFF;">Close</button>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</form>';

        $html .= '</div>';

        $html .= '</div>'; // modal-content end
        $html .= '</div>'; // modal-dialog end

        $html .= '<script type="text/javascript">';
        $html .= '$(".numeric").on("keyup", function() {
                    this.value = this.value.replace(/[^0-9]/gi, "");
                  });';
        $html .= 'jQuery(document).ready(function() {
                  var Rules = {
                      gt_coins: {
                          required: true
                      }
                  };
                  $("#addCoins").validate({
                      rules: Rules,
                      messages: {
                          gt_coins: {
                            required: "'. trans("validation.requiredfield") .'"
                          }
                      }
                  });
                  });';
        $html .= '</script>';


        echo $html;

?>