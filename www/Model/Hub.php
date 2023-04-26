<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Model/Order.php');
class Hub
{
    public readonly string $hubId;
    public readonly string $hubName;
    private $orderList = array();

    public function __construct($hubId, $hubName, $orderList = array())
    {
        $this->hubId = $hubId;
        $this->hubName = $hubName;
        $this->orderList = $this->orderList;
    }

    public function setOrderList($orderList): bool
    {
        if (is_null($orderList)) {
            return false;
        }
        $this->orderList = $orderList;
        return true;
    }
    public function addOrder(Order $order): bool
    {
        if (is_null($order)) {
            return false;
        }
        $this->orderList[$order->orderId] = $order;
        return true;
    }

    public function getOrderById(string $orderId): Order
    {
        return $this->orderList[$orderId];
    }

    public function getActiveOrder(): array
    {
        $activeOrderList = array();

        foreach ($this->orderList as  $key => $value) {
            if ($value->getStatus() == OrderStatus::Active) {
                $activeOrderList[$key] = $value;
            }
        }
        return $activeOrderList;
    }
    public function getHubOrderList(): array
    {
        return $this->orderList;
    }
}
