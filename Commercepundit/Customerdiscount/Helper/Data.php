<?php
namespace Commercepundit\Customerdiscount\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\CustomerFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $customerSession;

    protected $customerFactory;

    public function __construct(
        CustomerSession $customerSession,
        CustomerFactory $customerFactory
    ) {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    public function getCustomerCustomDiscount()
    {
        // Check if customer is logged in
        if ($this->customerSession->isLoggedIn()) {
            // Retrieve the customer ID
            $customerId = $this->customerSession->getCustomerId();

            // Load the customer model
            $customer = $this->customerFactory->create()->load($customerId);

            // Retrieve the custom attribute value
            $customDiscount = $customer->getData('cp_discount');
            if($customDiscount){
                return $customDiscount;
            }else{
                return null;
            }
            
        }

        return null; // Return null or any default value if customer is not logged in
    }
}
