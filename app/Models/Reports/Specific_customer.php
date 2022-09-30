<?php

namespace App\Models\Reports;

use app\Models\Sale;

/**
 *
 *
 * @property sale sale
 *
 */
class Specific_customer extends Report
{
	public function create(array $inputs): void
	{
		//Create our temp tables to work with the data in our report
		$this->sale->create_temp_table($inputs);
	}

	public function getDataColumns(): array
	{
		return [
			'summary' => [
				['id' => lang('Reports.sale_id')],
				['type_code' => lang('Reports.code_type')],
				['sale_date' => lang('Reports.date'), 'sortable' => FALSE],
				['quantity' => lang('Reports.quantity')],
				['employee_name' => lang('Reports.sold_by')],
				['subtotal' => lang('Reports.subtotal'), 'sorter' => 'number_sorter'],
				['tax' => lang('Reports.tax'), 'sorter' => 'number_sorter'],
				['total' => lang('Reports.total'), 'sorter' => 'number_sorter'],
				['cost' => lang('Reports.cost'), 'sorter' => 'number_sorter'],
				['profit' => lang('Reports.profit'), 'sorter' => 'number_sorter'],
				['payment_type' => lang('Reports.payment_type'), 'sortable' => FALSE],
				['comment' => lang('Reports.comments')]
			],
			'details' => [
				lang('Reports.name'),
				lang('Reports.category'),
				lang('Reports.item_number'),
				lang('Reports.description'),
				lang('Reports.quantity'),
				lang('Reports.subtotal'),
				lang('Reports.tax'),
				lang('Reports.total'),
				lang('Reports.cost'),
				lang('Reports.profit'),
				lang('Reports.discount')
			],
			'details_rewards' => [
				lang('Reports.used'),
				lang('Reports.earned')
			]
		];
	}

	public function getData(array $inputs): array
	{
		$builder = $this->db->table('sales_items_temp');
		$builder->select('
			sale_id,
			MAX(CASE
			WHEN sale_type = ' . SALE_TYPE_POS . ' && sale_status = ' . COMPLETED . ' THEN \'' . lang('Reports.code_pos') . '\'
			WHEN sale_type = ' . SALE_TYPE_INVOICE . ' && sale_status = ' . COMPLETED . ' THEN \'' . lang('Reports.code_invoice') . '\'
			WHEN sale_type = ' . SALE_TYPE_WORK_ORDER . ' && sale_status = ' . SUSPENDED . ' THEN \'' . lang('Reports.code_work_order') . '\'
			WHEN sale_type = ' . SALE_TYPE_QUOTE . ' && sale_status = ' . SUSPENDED . ' THEN \'' . lang('Reports.code_quote') . '\'
			WHEN sale_type = ' . SALE_TYPE_RETURN . ' && sale_status = ' . COMPLETED . ' THEN \'' . lang('Reports.code_return') . '\'
			WHEN sale_status = ' . CANCELED . ' THEN \'' . lang('Reports.code_canceled') . '\'
			ELSE \'\'
			END) AS type_code,
			MAX(sale_status) as sale_status,
			MAX(sale_date) AS sale_date,
			SUM(quantity_purchased) AS items_purchased,
			MAX(employee_name) AS employee_name,
			SUM(subtotal) AS subtotal,
			SUM(tax) AS tax,
			SUM(total) AS total,
			SUM(cost) AS cost,
			SUM(profit) AS profit,
			MAX(payment_type) AS payment_type,
			MAX(comment) AS comment');

		$builder->where('customer_id', $inputs['customer_id']);	//TODO: Duplicated code

		if($inputs['payment_type'] == 'invoices')
		{
			$builder->where('sale_type', SALE_TYPE_INVOICE);
		}
		elseif($inputs['payment_type'] != 'all')
		{
			$builder->like('payment_type', lang('Sales.'.$inputs['payment_type']));
		}

		if($inputs['sale_type'] == 'complete')
		{
			$builder->where('sale_status', COMPLETED);
			$builder->groupStart();
			$builder->where('sale_type', SALE_TYPE_POS);
			$builder->orWhere('sale_type', SALE_TYPE_INVOICE);
			$builder->orWhere('sale_type', SALE_TYPE_RETURN);
			$builder->groupEnd();
		}
		elseif($inputs['sale_type'] == 'sales')
		{
			$builder->where('sale_status', COMPLETED);
			$builder->groupStart();
			$builder->where('sale_type', SALE_TYPE_POS);
			$builder->orWhere('sale_type', SALE_TYPE_INVOICE);
			$builder->groupEnd();
		}
		elseif($inputs['sale_type'] == 'quotes')
		{
			$builder->where('sale_status', SUSPENDED);
			$builder->where('sale_type', SALE_TYPE_QUOTE);
		}
		elseif($inputs['sale_type'] == 'work_orders')
		{
			$builder->where('sale_status', SUSPENDED);
			$builder->where('sale_type', SALE_TYPE_WORK_ORDER);
		}
		elseif($inputs['sale_type'] == 'canceled')
		{
			$builder->where('sale_status', CANCELED);
		}
		elseif($inputs['sale_type'] == 'returns')
		{
			$builder->where('sale_status', COMPLETED);
			$builder->where('sale_type', SALE_TYPE_RETURN);
		}

		$builder->groupBy('sale_id');	//TODO: Duplicated code
		$builder->orderBy('MAX(sale_date)');

		$data = [];
		$data['summary'] = $builder->get()->getResultArray();
		$data['details'] = [];
		$data['rewards'] = [];

		foreach($data['summary'] as $key => $value)
		{
			$builder = $this->db->table('sales_items_temp');
			$builder->select('name, category, item_number, description, quantity_purchased, subtotal, tax, total, cost, profit, discount, discount_type');
			$builder->where('sale_id', $value['sale_id']);
			$data['details'][$key] = $builder->get()->getResultArray();

			$builder = $this->db->table('sales_reward_points');
			$builder->select('used, earned');
			$builder->where('sale_id', $value['sale_id']);
			$data['rewards'][$key] = $builder->get()->getResultArray();
		}

		return $data;
	}

	public function getSummaryData(array $inputs): array
	{
		$builder = $this->db->table('sales_items_temp');
		$builder->select('SUM(subtotal) AS subtotal, SUM(tax) AS tax, SUM(total) AS total, SUM(cost) AS cost, SUM(profit) AS profit');

		$builder->where('customer_id', $inputs['customer_id']);	//TODO: Duplicate code

		if($inputs['payment_type'] == 'invoices')
		{
			$builder->where('sale_type', SALE_TYPE_INVOICE);
		}
		elseif ($inputs['payment_type'] != 'all')
		{
			$builder->like('payment_type', lang('Sales.'.$inputs['payment_type']));
		}

		//TODO: This needs to be converted to a switch statement
		if($inputs['sale_type'] == 'complete')
		{
			$builder->where('sale_status', COMPLETED);
			$builder->groupStart();
			$builder->where('sale_type', SALE_TYPE_POS);
			$builder->orWhere('sale_type', SALE_TYPE_INVOICE);
			$builder->orWhere('sale_type', SALE_TYPE_RETURN);
			$builder->groupEnd();
		}
		elseif($inputs['sale_type'] == 'sales')
		{
			$builder->where('sale_status', COMPLETED);
			$builder->groupStart();
			$builder->where('sale_type', SALE_TYPE_POS);
			$builder->orWhere('sale_type', SALE_TYPE_INVOICE);
			$builder->groupEnd();
		}
		elseif($inputs['sale_type'] == 'quotes')
		{
			$builder->where('sale_status', SUSPENDED);
			$builder->where('sale_type', SALE_TYPE_QUOTE);
		}
		elseif($inputs['sale_type'] == 'work_orders')
		{
			$builder->where('sale_status', SUSPENDED);
			$builder->where('sale_type', SALE_TYPE_WORK_ORDER);
		}
		elseif($inputs['sale_type'] == 'canceled')
		{
			$builder->where('sale_status', CANCELED);
		}
		elseif($inputs['sale_type'] == 'returns')
		{
			$builder->where('sale_status', COMPLETED);
			$builder->where('sale_type', SALE_TYPE_RETURN);
		}

		return $builder->get()->getRowArray();
	}
}