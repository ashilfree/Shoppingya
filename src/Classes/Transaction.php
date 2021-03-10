<?php


namespace App\Classes;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;

class Transaction
{

	/**
	 * @var Registry
	 */
	private $workFlows;

	public function __construct(
		Registry $workFlows
	)
	{
		$this->workFlows = $workFlows;
	}

	public function applyWorkFlow(Order $order, string $transaction)
	{
		$stateMachine = $this->workFlows->get($order, 'order');
		$stateMachine->apply($order, $transaction);
	}

	/**
	 * @param Order $order
	 * @param string $transaction
	 * @return bool
	 */
	public function check(Order $order, string $transaction)
	{
		$stateMachine = $this->workFlows->get($order, 'order');
		return $stateMachine->can($order, $transaction);
	}
}