<?php

        $html = '';

        // Question  Data Start

        $html .= '<div class="modal-dialog">';
        $html .= '<div class="modal-content">';
        $html .= '<div class="modal-header">';
        $html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        $html .= '<h3 class="modal-title" style="text-align: center;">Leaderboard</h3>';
        $html .= '<h4 class="modal-title">'. $pf_name .'</h4>';
        $html .= '</div>'; // modal-header end
        $html .= '<div class="modal-body">';
        if (isset($data) && !empty($data)) {
            $html .= '<table class="table table-striped">';
            $html .= '<tr>';
            $html .= '<th>Icon</th>';
            $html .= '<th>Name</th>';
            $html .= '<th>Phone No</th>';
            $html .= '<th>Email</th>';
            $html .= '<th>Score</th>';
            //$html .= '<th>Rank</th>';
            $html .= '</tr>';
            foreach ($data as $key => $value) {
                $html .= '<tr>';
                $profilePic = ($value->t_photo != "" && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo) > 0) ? Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo) : Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png');
                $html .= '<td><img src='.$profilePic.' width="60px" height="60px"></td>';
                $html .= '<td>'. $value['t_name'] .'</td>';
                $phoneNum = (empty($value['t_phone'])) ? "-" : $value['t_phone'];
                $html .= '<td>'. $phoneNum .'</td>';
                $html .= '<td>'. $value['t_email'] .'</td>';
                $html .= '<td>' . $value['tlb_points'] . '</td>';
                //$html .= '<td>' . $value['rank'] . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
        }
        $html .= '</div>'; // modal-body end

        $html .= '<div class="modal-footer">';
        $html .= '<button type="button" class="btn btn-default" data-dismiss="modal" style="color: #FFFFFF;">Close</button>';
        $html .= '</div>'; // modal-footer end

        $html .= '</div>'; // modal-content end
        $html .= '</div>'; // modal-dialog end

        echo $html;

?>