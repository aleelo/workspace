<?php $Accounting_model = model("Accounting\Models\Accounting_model"); ?>
<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
          <td colspan="3">
              <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_setting('companyname'); ?></h3>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="3">
            <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo app_lang('profit_and_loss_as_of_total_income'); ?></h4>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td colspan="3">
            <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo html_entity_decode($data_report['from_date'] .' - '. $data_report['to_date']); ?></p>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr class="border-top">
          <td></td>
          <td colspan="2" class="text-center th_total_2 text-bold"><?php echo app_lang('total'); ?></td>
          <td></td>
        </tr>
        <tr class="border-bottom">
          <td></td>
          <td class="th_total_2 text-bold"><?php echo html_entity_decode($data_report['from_date'] .' - '. $data_report['to_date']); ?></td>
          <td class="th_total_2 text-bold"><?php echo app_lang('percent_of_income'); ?></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo app_lang('acc_income'); ?></td>
            <td class="total_amount"></td>
            <td class="total_amount"></td>
          </tr>
          <?php
          $row_index += 1;
          ?>
          <?php 
            $_index = $row_index;
            $data = $Accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['income'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency_symbol);
            $row_index = $data['row_index'];
            echo html_entity_decode($data['html']);
            $total_income = $data['total_amount'];
            $percent_income = $data['percent'];
            ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('total_income'); ?></td>
            <td class="total_amount"><?php echo to_currency($total_income, $currency_symbol); ?> </td>
            <td class="total_amount"><?php echo html_entity_decode($percent_income); ?>% </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
           <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo app_lang('acc_cost_of_sales'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $data = $Accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['cost_of_sales'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency_symbol);
            $row_index = $data['row_index'];
            echo html_entity_decode($data['html']);
            $total_cost_of_sales = $data['total_amount'];
            $percent_cost_of_sales = $data['percent'];

          ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('total_cost_of_sales'); ?></td>
            <td class="total_amount"><?php echo to_currency($total_cost_of_sales, $currency_symbol); ?> </td>
            <td class="total_amount"><?php echo html_entity_decode($percent_cost_of_sales); ?>% </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('gross_profit_uppercase'); ?></td>
            <td class="total_amount"><?php echo to_currency($data_report['total']['income'] - $data_report['total']['cost_of_sales'], $currency_symbol); ?> </td>
            <td class="total_amount"><?php echo html_entity_decode($data_report['percent']['income'] - $data_report['percent']['cost_of_sales']); ?>% </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo app_lang('acc_other_income'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $data = $Accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['other_income'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency_symbol);
            $row_index = $data['row_index'];
            echo html_entity_decode($data['html']);
            $total_other_income = $data['total_amount'];
            $percent_other_income = $data['percent'];

            ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('total_other_income_loss'); ?></td>
            <td class="total_amount"><?php echo to_currency($total_other_income, $currency_symbol); ?> </td>
            <td class="total_amount"><?php echo html_entity_decode($percent_other_income); ?>% </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo app_lang('acc_expenses'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
          $data = $Accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['expenses'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency_symbol);
            $row_index = $data['row_index'];
            echo html_entity_decode($data['html']);
            $total_expenses = $data['total_amount'];
            $percent_expenses = $data['percent'];

           ?>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('total_expenses'); ?></td>
            <td class="total_amount"><?php echo to_currency($total_expenses, $currency_symbol); ?> </td>
            <td class="total_amount"><?php echo html_entity_decode($percent_expenses); ?>% </td>
          </tr>
          <?php $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo app_lang('acc_other_expenses'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
          $data = $Accounting_model->get_html_profit_and_loss_as_of_total_income($data_report['data']['other_expenses'], $data_report['total']['income'],['html' => '', 'row_index' => $row_index + 1, 'total_amount' => 0, 'total_py_amount' => 0], $parent_index, $currency_symbol);
            $row_index = $data['row_index'];
            echo html_entity_decode($data['html']);
            $total_other_expenses = $data['total_amount'];
            $percent_other_expenses = $data['percent'];

            $row_index += 1;
          ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('total_other_expenses'); ?></td>
            <td class="total_amount"><?php echo to_currency($total_other_expenses, $currency_symbol); ?> </td>
            <td class="total_amount"><?php echo html_entity_decode($percent_other_expenses); ?>% </td>
          </tr>
          <?php $row_index += 1; ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo app_lang('net_earnings_uppercase'); ?></td>
            <td class="total_amount"><?php echo to_currency(($total_income + $total_other_income) - ($total_cost_of_sales + $total_expenses + $total_other_expenses), $currency_symbol); ?> </td>
            <?php if($data_report['total']['income'] != 0){ ?>
              <td class="total_amount"><?php echo round(((($total_income + $total_other_income) - ($total_cost_of_sales + $total_expenses + $total_other_expenses)) / $data_report['total']['income']) * 100, 2); ?>% </td>
            <?php }else{ ?>
              <td class="total_amount">0%</td>
           <?php } ?>
          </tr>
        </tbody>
    </table>
  </div>
</div>