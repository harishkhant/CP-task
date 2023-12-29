<?php
namespace Commercepundit\Customerdiscount\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Commercepundit\Customerdiscount\Helper\Data as DiscountHelper;

class ApplyCustomDiscount implements ObserverInterface
{
    protected $discountHelper;
    protected $messageManager;

    public function __construct(
        DiscountHelper $discountHelper,
        ManagerInterface $messageManager
    ) {
        $this->discountHelper = $discountHelper;
        $this->messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $product = $item->getProduct();

        $customerDiscount = $this->discountHelper->getCustomerCustomDiscount();
        if ($customerDiscount !== null) {
            $newPrice = $this->calculateDiscountedPrice($product->getPrice(), $customerDiscount);
            $item->setCustomPrice($newPrice);
            $item->setOriginalCustomPrice($newPrice);
            $item->getProduct()->setIsSuperMode(true);
            $productName = $item->getName();
            //$formattedDiscount = number_format($customerDiscount, 2);
            $savedAmount = $item->getProduct()->getPrice() - $newPrice;
        
            $this->messageManager->addSuccessMessage(
                __('Congratulations! You saved $%1 on %2. This discount is exclusive to you.', number_format($savedAmount, 2), $item->getName())
            );
        }else {
            $this->messageManager->addNoticeMessage(
                __('The custom discount is not available for your account.')
            );
        }
    }

    private function calculateDiscountedPrice($originalPrice, $discountPercent)
    {
        if ($discountPercent >= 0 && $discountPercent <= 100) {
            return $originalPrice - ($originalPrice * $discountPercent / 100);
        }
        return $originalPrice;
    }
}
