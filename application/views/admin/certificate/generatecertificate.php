<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) {?>
            <?php 
                echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php }?>

        

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/generatecertificate/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
foreach ($classlist as $class) {
    ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
        echo "selected=selected";
    }
    ?>><?php echo $class['class'] ?></option>
                                                <?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('certificate'); ?></label><small class="req"> *</small>
                                        <select name="certificate_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
if (isset($certificateList)) {
    foreach ($certificateList as $list) {
        ?>
                                                    <option value="<?php echo $list->id ?>" <?php if (set_value('certificate_id') == $list->id) {
            echo "selected=selected";
        }
        ?>><?php echo $list->certificate_name ?></option>
                                                    <?php
}
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('certificate_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php
if (isset($resultlist)) {
    ?>
                        <form method="post" action="<?php echo base_url('admin/generatecertificate/generatemultiple') ?>">
                            <div  class="" id="duefee">
                                <div class="box-header ptbnull"></div>
                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
                                    <button  class="btn btn-info btn-sm printSelected pull-right" type="button" name="generate" title="generate multiple certificate"><?php echo $this->lang->line('generate'); ?></button>
                                </div>
                                <div class="box-body table-responsive overflow-visible">
                                    <div class="download_label"><?php echo $this->lang->line('student_list'); ?></div>
                                    <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="select_all" /></th>
                                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                    <th><?php echo $this->lang->line('student_name'); ?></th>
                                                    <th><?php echo $this->lang->line('class'); ?></th>
                                                    <th><?php echo $this->lang->line('father_name'); ?></th>
                                                    <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                                    <th><?php echo $this->lang->line('category'); ?></th>
                                                    <th class=""><?php echo $this->lang->line('mobile_number'); ?></th>
                                                    <th><?php echo $this->lang->line('fee_due');?></th>
                                                    <th><?php echo $this->lang->line('status');?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                        if (empty($resultlist)) {
                                                                ?>
                                                    <?php
                                                        } else {
                                                        
                                                                    $count = 1;
                                                                    foreach ($resultlist as $student) {
                                                            
                                                                    $student_due_fee = $this->studentfeemaster_model->getStudentFees($student['student_session_id']);

                                                                    $student1               = $this->student_model->getByStudentSession($student['student_session_id']);
                                                                    $route_pickup_point_id = $student1['route_pickup_point_id'];
                                                                    $student_session_id    = $student['student_session_id'];
                                                                    $transport_fees=[];

                                                                    $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
                                                                    
                                                                 
                                                                    $total_amount           = 0;
                                                                    $total_deposite_amount  = 0;
                                                                    $total_fine_amount      = 0;
                                                                    $total_discount_amount  = 0;
                                                                    $total_balance_amount   = 0;
                                                                    $alot_fee_discount      = 0;
                                                                    $total_fees_fine_amount = 0;

                                                                    foreach ($student_due_fee as $key => $fee) {

                                                                        foreach ($fee->fees as $fee_key => $fee_value) {

                                                                            $fee_paid          = 0;
                                                                            $fee_discount      = 0;
                                                                            $fee_fine          = 0;
                                                                            $alot_fee_discount = 0;

                                                                            if (!empty($fee_value->amount_detail)) {
                                                                                $fee_deposits = json_decode(($fee_value->amount_detail));

                                                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                                    $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                                                    $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                                                                }
                                                                            }
                                                                        
                                                                            $total_amount           = $total_amount + $fee_value->amount;
                                                                            $total_discount_amount  = $total_discount_amount + $fee_discount;
                                                                            $total_deposite_amount  = $total_deposite_amount + $fee_paid;
                                                                            $total_fine_amount      = $total_fine_amount + $fee_fine;
                                                                            $feetype_balance        = $fee_value->amount - ($fee_paid + $fee_discount);
                                                                            $total_balance_amount   = $total_balance_amount + $feetype_balance;
                                                                            $total_fees_fine_amount = $total_fees_fine_amount + $fee_value->fine_amount;
                                                                        }
                                                                    }


                                                                    if (!empty($transport_fees)) {
                                                                        foreach ($transport_fees as $transport_fee_key => $transport_fee_value) {
                                                                    
                                                                            $fee_paid         = 0;
                                                                            $fee_discount     = 0;
                                                                            $fee_fine         = 0;
                                                                            $fees_fine_amount = 0;
                                                                            $feetype_balance  = 0;
                                                                    
                                                                            if (!empty($transport_fee_value->amount_detail)) {
                                                                                $fee_deposits = json_decode(($transport_fee_value->amount_detail));
                                                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                                    $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                                                    $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                                                                }
                                                                            }
                                                                    
                                                                            $feetype_balance = $transport_fee_value->fees - ($fee_paid + $fee_discount);
                                                                    
                                                                            if (($transport_fee_value->due_date != "0000-00-00" && $transport_fee_value->due_date != null) && (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                                                $fees_fine_amount       = is_null($transport_fee_value->fine_percentage) ? $transport_fee_value->fine_amount : percentageAmount($transport_fee_value->fees, $transport_fee_value->fine_percentage);
                                                                                $total_fees_fine_amount = $total_fees_fine_amount + $fees_fine_amount;
                                                                            }
                                                                    
                                                                            $total_amount += $transport_fee_value->fees;
                                                                            $total_discount_amount += $fee_discount;
                                                                            $total_deposite_amount += $fee_paid;
                                                                            $total_fine_amount += $fee_fine;
                                                                            $total_balance_amount += $feetype_balance;
                                                                    
                                                                            
                                                                        }
                                                                    }
                                                                

                                                            ?>

                                                            <?php
                                                                
                                                                    $hidde = 'checkbox';
                                                                    if ($total_balance_amount>0) {
                                                                        $hidde = 'hidden';
                                                                        // Change the color if the condition is true
                                                                    } 
                                                            ?>

                                                        
                                                        
                                                        <tr>

                                                            <td class="text-center">
                                                                
                                                                <input type="<?php echo $hidde; ?>" class="checkbox center-block"  name="check" data-student_id="<?php echo $student['id'] ?>" value="<?php echo $student['id'] ?>">
                                                                
                                                                <input type="hidden" name="class_id" value="<?php echo $student['class_id'] ?>">
                                                                <input type="hidden" name="certificate_id" value="<?php echo $certificateResult[0]->id ?>" id="certificate_id">
                                                            
                                                            </td>

                                                            <td><?php echo $student['admission_no']; ?></td>
                                                            <td>
                                                                <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"><?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
                                                            <td><?php echo $student['father_name']; ?></td>
                                                            <td><?php if ($student['dob'] != '' && $student['dob'] != '0000-00-00') {echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob']));}?></td>
                                                            <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>
                                                            <td><?php echo $student['category']; ?></td>
                                                            <td><?php echo $student['mobileno']; ?></td>
                                                            

                                                            <td class="text-center">
                                                                <?php echo $total_balance_amount?>
                                                            </td>

                                                            

                                                            <td align="left" class="text text-left">
                                                                <?php
                                                                    if ($total_balance_amount == 0) {
                                                                                    ?><span class="label label-success"><?php echo $this->lang->line('paid'); ?></span><?php
                                                                    }else {
                                                                        ?><span class="label label-danger"><?php echo $this->lang->line('unpaid'); ?></span><?php
                                                                    }
                                                                ?>
                                                            </td>

                                                        </tr>




                                                        <?php
                                                    $count++;
                                                            }
                                                        }
                                                        ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php
}
?>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#select_all').on('click', function () {
            if (this.checked) {
                $('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function () {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
            var classId = $("#class_id").val();
            var certificateId = $("#certificate_id").val();
            $.each($("input[name='check']:checked"), function () {
                var studentId = $(this).data('student_id');
                item = {}
                item ["student_id"] = studentId;
                array_to_print.push(item);
            });
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/generatecertificate/generatemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print), 'class_id': classId, 'certificate_id': certificateId, },
                    success: function (response) {
                        Popup(response);

                    }
                });
            }
        });
    });
</script>
<script type="text/javascript">

    var base_url = '<?php echo base_url() ?>';
    function Popup(data)
    {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
//Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);

        return true;
    }
</script>