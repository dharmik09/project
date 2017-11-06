<?php
    echo "Approved by :- Admin User "."<br/>"."<br/>";
    echo "Profession Name :- " . $content['pf_name']."<br/>"."<br/>";
    if ($content['typeId'] == 3) {
        $count = count($content['image']);
        ?>
        <table cellspacing="10px">
            <tr>
                <th>Image Name</th>
                <th>Status</th>
                <th>Note</th>
            </tr>
        <?php
        for ($i = 0; $i < $count; $i++) { ?>
            <tr>
                <td width="150px">
                    {{$content['image'][$i]}}
                </td>
                <td width="70px">
                    <?php
                        if (isset($content['status'][$i])) {
                            if ($content['status'][$i] == 2) {
                                echo "Varified";
                            } else if ($content['status'][$i] == 2) {
                                echo "Rejected";
                            }
                        }
                    ?>
                </td>
                <td width="50px">
                    {{$content['note'][$i]}}
                </td>
            </tr>

            <?php
        }
        ?> </table><?php
    } else {
        foreach ($content['note'] as $key => $value) {
            echo "Review Note :- " . $value ."<br/>"."<br/>";
        }
        foreach ($content['status'] as $key => $value) {
            if ($value == 2) {
                echo "Status :- Varified "."<br/>"."<br/>";
            } else if ($value == 3) {
                echo "Status :- Rejected "."<br/>"."<br/>";
            }
        }
    }
?>