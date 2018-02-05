<html>
<head>
<style>
    @page { margin: 90px 50px; }
    #header { position: fixed; top: -80px; right: 0px;  height: 90px; border-bottom: 4px solid gray;padding-bottom: 5px;}
    .clearfix {
        clear: both;
    }
    #footer { position: fixed; left: 0px; bottom: -80px;  right: 0px; height: 40px;  border-top: 4px solid gray;}
    #footer .page:after { content: counter(page); padding-left : 380px;}
    .pagebreak {
        page-break-after:always;
        position: relative;
    }
    table {
        border-collapse: collapse;
    }

</style>
</head>
<body>
<?php
    $number = $transactionDetail['tn_amount'];
    $word = '';
    $no = round($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'One', '2' => 'Two',
    '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
    '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
    '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
    '13' => 'Thirteen', '14' => 'Fourteen',
    '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
    '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
    '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
    '60' => 'Sixty', '70' => 'Seventy',
    '80' => 'Eighty', '90' => 'Ninety');
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ?
    "." . $words[$point / 10] . " " .
          $words[$point = $point % 10] : '0';
    $word =  $result;
  ?>
<div id="header">
    <div style="padding-top:20px;"><img src="{{ asset('/backend/images/proteen_logo.png')}}" alt="" width="90px"/></div>
</div>

<div id="footer" class="clearfix">
     <p class="page">Copyright &copy; <?php echo date('Y');?> <span style="color:#E66A45;"> ProTeen</span>. All rights reserved.</p>
</div>

<div class="clearfix">
    <div style="margin-top:50px;">
        <table align="center" border="1" width="710px" cellpadding='5'>
            <tr>
                <td colspan="5" style="font-size:20px;font-weight:bold;text-align:center;">UniDEL Ventures Private Limited</td>
            </tr>
            <tr>
                <td style="text-align:center;" colspan="2">86, Jolly Maker Chamber II,<br/>225, Nariman Point,<br/>Mumbai 400 021,<br/>India</td>
                <td style="font-weight:bold;" colspan="2">Service Tax No.</td>
                <td>AAACS7293JST001</td>
            </tr>
            <tr>
                <td style="text-align:center;" colspan="2" width='290px'>Tel: +91 22 2287 3545 / +91 22 2204 3855</td>
                <td style="font-weight:bold;" colspan="2">PAN No.</td>
                <td>AAACS7293J</td>
            </tr>
            <tr><td colspan="5" height="20px"></td></tr>
            <tr>
                <td rowspan="2" style="font-size:20px;font-weight:bold;text-align:center;" colspan="2">TAX INVOICE</td>
                <td style="font-weight:bold;" colspan="2">Invoice No.</td>
                <td>UVPL/<?php echo date('Y');?>/{{$transactionDetail['i_invoice_id']}}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;" colspan="2">Invoice Date</td>
                <td><?php echo date("d-M-Y", strtotime($transactionDetail['tn_trans_date']));?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;text-align:right;">To: </td>
                <td style="font-weight:bold;" colspan="4">{{$transactionDetail['name']}}</td>
            </tr>
            <tr>
                <td rowspan="3"></td>
                <td>{{$transactionDetail['tn_billing_address']}}</td>
                <td style="font-weight:bold;" colspan="2">Payment Mode</td>
                <td>{{$transactionDetail['tn_payment_mode']}}</td>
            </tr>
            <tr>
                <td>{{$transactionDetail['tn_billing_address']}}</td>
                <td style="font-weight:bold;" colspan="2">Contact No:</td>
                <td>{{$transactionDetail['tn_billing_phone']}}</td>
            </tr>
            <tr>
                <td>{{$transactionDetail['tn_billing_city']}} - {{$transactionDetail['tn_billing_zip']}}</td>
                <td style="font-weight:bold;" colspan="2">E-mail</td>
                <td>{{$transactionDetail['tn_email']}}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;text-align:right;">Kind Attention:</td>
                <td style="font-weight:bold;" colspan="3">{{$transactionDetail['tn_billing_name']}}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-weight:bold;text-align:center;">Sr. No.</td>
                <td style="font-weight:bold;text-align:center;">Particulars</td>
                <td style="font-weight:bold;text-align:center;">Quantity</td>
                <td style="font-weight:bold;text-align:right;" width="120px">Unit Price (INR)</td>
                <td style="font-weight:bold;text-align:right;">Total Amount (INR)</td>
            </tr>
            <tr>
                <td style="text-align:center;">1</td>
                <td>{{$transactionDetail['c_package_name']}} Package</td>
                <td style="text-align:center;">1</td>
                <td style="text-align:right;"><?php echo number_format($transactionDetail['tn_amount'],2);?></td>
                <td style="text-align:right;"><?php echo number_format($transactionDetail['tn_amount'],2);?></td>
            </tr>
            <tr>
                <td colspan="5" height="15px"></td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Total</td>
                <td style="text-align:right;font-weight:bold;" colspan="3">Indian Rupees</td>
                <td style="text-align:right;font-weight:bold;"><?php echo number_format($transactionDetail['tn_amount'],2);?></td>
            </tr>
            <tr>
                <td style="text-align:right;font-weight:bold;">Amount in words</td>
                <td style="font-weight:bold;" colspan="4">Indian Rupees {{$word}} only</td>
            </tr>
            <tr>
                <td style="text-align:center;" colspan="5">Includes all applicable taxes and levies.</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;font-weight:bold;">This is a Computer Generated Invoice and does not require a signature.</td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>