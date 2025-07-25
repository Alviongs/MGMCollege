<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>


<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }
    .balance-wrapper {
        display: flex;
        justify-content: space-between; 
        margin-bottom: 20px;
    }
    .container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .table-container {
        width: 60%;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        font-size: 0.9em;
    }
    th {
        background-color: #f2f2f2;
    }
    .total {
        font-weight: bold;
    }
    .daily-header {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        margin-bottom: 10px;
    }
    .daily-balance {
        display: flex;
        justify-content: space-between;
        background-color: #f9f9f9;
        padding: 10px;
        margin-bottom: 10px;
    }
    .download-btn {
        margin-bottom: 20px;
    }
</style>

<!-- Include jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> <?php echo $this->lang->line('accountreport'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php 
                echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/accountreport/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('accountname'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="accountname_id" name="accountname_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('accountname_id') == $class['id']) echo "selected=selected"; ?>><?php echo $class['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('accountname_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date_from"><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date_fee" value="" readonly="readonly" />
                                        <span class="text-danger" id="date_from_error"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date_to"><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date_fee" value="" readonly="readonly" />
                                        <span class="text-danger" id="date_to_error"><?php echo form_error('date_to'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (isset($daily_data)) { ?>
                    <button class="btn btn-primary download-btn" id="download-pdf-btn">Download PDF</button>

                    <div class="box-body table-responsive overflow-visible" id="reportContent">
                        <div class="download_label"><?php echo $this->lang->line('accountreport'); ?></div>
                        <div class="tab-pane active table-responsive no-padding">
                            <div class="balance-wrapper">
                                <div>
                                    <h4><strong><?php echo $this->lang->line('overall_opening_balance'); ?>: </strong>
                                        <?php echo ($openaccountbalance < 0 ? '-' : '') . $currency_symbol . amountFormat(abs($openaccountbalance)); ?>
                                    </h4>
                                </div>
                                <div>
                                    <h4><strong><?php echo $this->lang->line('overall_closing_balance'); ?>: </strong>
                                        <?php echo ($closeaccountblance < 0 ? '-' : '') . $currency_symbol . amountFormat(abs($closeaccountblance)); ?>
                                    </h4>
                                </div>
                            </div>
                            <p><strong><?php echo $this->lang->line('date_range'); ?>:</strong> <?php echo $startdate . " " . $this->lang->line('to') . " " . $enddate; ?></p>

                            <?php 
                            $grand_credit_total = 0;
                            $grand_debit_total = 0;
                            $running_balance = $openaccountbalance;
                            foreach ($daily_data as $date => $data) { 
                                $daily_credit_total = 0;
                                $daily_debit_total = 0;
                                foreach ($data['transactions'] as $tran) {
                                    if ($tran['status'] == 'credit') {
                                        $daily_credit_total += $tran['amount'];
                                    } else {
                                        $daily_debit_total += $tran['amount'];
                                    }
                                }
                                $daily_net = $daily_credit_total - $daily_debit_total;
                                $opening_balance = $running_balance;
                                $running_balance += $daily_net;
                            ?>
                                <div class="daily-header">
                                    <h3><?php echo date('d M Y', strtotime($date)); ?></h3>
                                </div>
                                <div class="daily-balance">
                                    <div><?php echo $this->lang->line('opening_balance'); ?>: <?php echo $currency_symbol . amountFormat($opening_balance); ?></div>
                                    <div><?php echo $this->lang->line('closing_balance'); ?>: <?php echo $currency_symbol . amountFormat($running_balance); ?></div>
                                </div>
                                <div class="container table-responsive">
                                    <div class="table-container table-responsive">
                                        <table class="table-responsive">
                                            <thead>
                                                <tr>
                                                    <th colspan="4"><?php echo $this->lang->line('credit'); ?></th>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                                    <th><?php echo $this->lang->line('type'); ?></th>
                                                    <th><?php echo $this->lang->line('description'); ?></th>
                                                    <th><?php echo $this->lang->line('amount'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($data['transactions'] as $tran) {
                                                    if ($tran['status'] == 'credit') {
                                                        $grand_credit_total += $tran['amount'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $tran['receiptid']; ?></td>
                                                    <td><?php echo $tran['type']; ?></td>
                                                    <td><?php echo $tran['description']; ?></td>
                                                    <td><?php echo $currency_symbol . amountFormat($tran['amount']); ?></td>
                                                </tr>
                                                <?php 
                                                    }
                                                }
                                                ?>
                                                <tr class="total">
                                                    <td colspan="3" class="text-right"><?php echo $this->lang->line('total_credit'); ?></td>
                                                    <td><?php echo $currency_symbol . amountFormat($daily_credit_total); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-container table-responsive">
                                        <table class="table-responsive">
                                            <thead>
                                                <tr>
                                                    <th colspan="4"><?php echo $this->lang->line('debit'); ?></th>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                                    <th><?php echo $this->lang->line('type'); ?></th>
                                                    <th><?php echo $this->lang->line('description'); ?></th>
                                                    <th><?php echo $this->lang->line('amount'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($data['transactions'] as $tran) {
                                                    if ($tran['status'] == 'debit') {
                                                        $grand_debit_total += $tran['amount'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $tran['receiptid']; ?></td>
                                                    <td><?php echo $tran['type']; ?></td>
                                                    <td><?php echo $tran['description']; ?></td>
                                                    <td><?php echo $currency_symbol . amountFormat($tran['amount']); ?></td>
                                                </tr>
                                                <?php 
                                                    }
                                                }
                                                ?>
                                                <tr class="total">
                                                    <td colspan="3" class="text-right"><?php echo $this->lang->line('total_debit'); ?></td>
                                                    <td><?php echo $currency_symbol . amountFormat($daily_debit_total); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="balance-wrapper">
                                <div>
                                    <h4><strong><?php echo $this->lang->line('grand_total_credit'); ?>: </strong><?php echo $currency_symbol . amountFormat($grand_credit_total); ?></h4>
                                </div>
                                <div>
                                    <h4><strong><?php echo $this->lang->line('grand_total_debit'); ?>: </strong><?php echo $currency_symbol . amountFormat($grand_debit_total); ?></h4>
                                </div>
                            </div>
                            <div>
                                <h4><strong><?php echo $this->lang->line('net_transaction'); ?>: </strong><?php echo $currency_symbol . amountFormat($grand_credit_total - $grand_debit_total); ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the download button by its ID
        const downloadBtn = document.getElementById('download-pdf-btn');

        // Add an event listener to the button
        downloadBtn.addEventListener('click', downloadPDF);

        function downloadPDF() {
            // Create a new jsPDF instance
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');

            // Add content to the PDF
            pdf.setFontSize(12);
            pdf.text("Account Report", 10, 10);

            let yOffset = 20;

            // Add overall opening and closing balances
            pdf.text("Overall Opening Balance: <?php echo ($openaccountbalance < 0 ? '-' : '') . $currency_symbol . amountFormat(abs($openaccountbalance)); ?>", 10, yOffset);
            yOffset += 10;
            pdf.text("Overall Closing Balance: <?php echo ($closeaccountblance < 0 ? '-' : '') . $currency_symbol . amountFormat(abs($closeaccountblance)); ?>", 10, yOffset);
            yOffset += 10;

            // Add date range
            pdf.text("Date Range: <?php echo $startdate . " " . $this->lang->line('to') . " " . $enddate; ?>", 10, yOffset);
            yOffset += 10;

            // Add daily data
            <?php 
            $grand_credit_total = 0;
            $grand_debit_total = 0;
            $running_balance = $openaccountbalance;
            foreach ($daily_data as $date => $data) { 
                $daily_credit_total = 0;
                $daily_debit_total = 0;
                foreach ($data['transactions'] as $tran) {
                    if ($tran['status'] == 'credit') {
                        $daily_credit_total += $tran['amount'];
                    } else {
                        $daily_debit_total += $tran['amount'];
                    }
                }
                $daily_net = $daily_credit_total - $daily_debit_total;
                $opening_balance = $running_balance;
                $running_balance += $daily_net;
            ?>
                pdf.text("Date: <?php echo date('d M Y', strtotime($date)); ?>", 10, yOffset);
                yOffset += 10;
                pdf.text("Opening Balance: <?php echo $currency_symbol . amountFormat($opening_balance); ?>", 10, yOffset);
                yOffset += 10;
                pdf.text("Closing Balance: <?php echo $currency_symbol . amountFormat($running_balance); ?>", 10, yOffset);
                yOffset += 10;

                // Add credit transactions
                pdf.text("Credit Transactions:", 10, yOffset);
                yOffset += 10;
                <?php
                foreach ($data['transactions'] as $tran) {
                    if ($tran['status'] == 'credit') {
                        $grand_credit_total += $tran['amount'];
                ?>
                    pdf.text("Receipt No: <?php echo $tran['receiptid']; ?>, Type: <?php echo $tran['type']; ?>, Description: <?php echo $tran['description']; ?>, Amount: <?php echo $currency_symbol . amountFormat($tran['amount']); ?>", 10, yOffset);
                    yOffset += 10;
                <?php 
                    }
                }
                ?>
                pdf.text("Total Credit: <?php echo $currency_symbol . amountFormat($daily_credit_total); ?>", 10, yOffset);
                yOffset += 10;

                // Add debit transactions
                pdf.text("Debit Transactions:", 10, yOffset);
                yOffset += 10;
                <?php
                foreach ($data['transactions'] as $tran) {
                    if ($tran['status'] == 'debit') {
                        $grand_debit_total += $tran['amount'];
                ?>
                    pdf.text("Receipt No: <?php echo $tran['receiptid']; ?>, Type: <?php echo $tran['type']; ?>, Description: <?php echo $tran['description']; ?>, Amount: <?php echo $currency_symbol . amountFormat($tran['amount']); ?>", 10, yOffset);
                    yOffset += 10;
                <?php 
                    }
                }
                ?>
                pdf.text("Total Debit: <?php echo $currency_symbol . amountFormat($daily_debit_total); ?>", 10, yOffset);
                yOffset += 10;
            <?php } ?>

            // Add grand totals
            pdf.text("Grand Total Credit: <?php echo $currency_symbol . amountFormat($grand_credit_total); ?>", 10, yOffset);
            yOffset += 10;
            pdf.text("Grand Total Debit: <?php echo $currency_symbol . amountFormat($grand_debit_total); ?>", 10, yOffset);
            yOffset += 10;
            pdf.text("Net Transaction: <?php echo $currency_symbol . amountFormat($grand_credit_total - $grand_debit_total); ?>", 10, yOffset);

            // Save the PDF
            pdf.save('account_report.pdf');
        }
    });
</script>


