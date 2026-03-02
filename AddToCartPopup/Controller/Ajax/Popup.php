<?php
namespace Ankush\AddToCartPopup\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Popup extends Action
{
    protected $productRepository;
    protected $registry;
    protected $pageFactory;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Registry $registry,
        PageFactory $pageFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('product_id');

        if (!$productId) {
            return $this->resultJsonFactory->create()->setData([
                'html' => ''
            ]);
        }

        /** Load product */
        $product = $this->productRepository->getById($productId);

        /**
         * 🔑 IMPORTANT
         * 1. Registry set (Magento default blocks ke liye)
         * 2. Duplicate register se bachao
         */
        if (!$this->registry->registry('current_product')) {
            $this->registry->register('current_product', $product);
        }

        /** Create page layout (XML WILL BE USED) */
        $page = $this->pageFactory->create();
        $layout = $page->getLayout();

        /**
         * 🔑 FORCE product into related & upsell blocks
         * (AJAX context me registry kabhi late hoti hai)
         */
        if ($layout->getBlock('catalog.product.related')) {
            $layout->getBlock('catalog.product.related')->setData('product', $product);
        }

        if ($layout->getBlock('product.info.upsell')) {
            $layout->getBlock('product.info.upsell')->setData('product', $product);
        }

        /** Render popup block (children included) */
        $html = $layout->getBlock('addtocart.popup')->toHtml();

        return $this->resultJsonFactory->create()->setData([
            'html' => $html
        ]);
    }
}
