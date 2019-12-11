<?php
/**
 * Copyright © Gigadesign. All rights reserved.
 */
declare(strict_types=1);

namespace Gigadesign\GoogleClient\Controller\Connect;

use Gigadesign\GoogleClient\Model\GoogleClientManager;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var GoogleClientManager
     */
    protected $clientManager;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @param Context $context
     * @param GoogleClientManager $clientManager
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        GoogleClientManager $clientManager,
        PageFactory $pageFactory
    ) {
        $this->clientManager = $clientManager;
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return Page
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('code'))
        {
            $this->clientManager->setAuthCode($this->getRequest()->getParam('code'));
        }

        echo "Your account has been set up. Please return to the backend."; exit;
    }

}
