<?php
namespace BadaBoom\ChainNode\Sender;

use BadaBoom\Adapter\Logger\LoggerAdapterInterface;
use BadaBoom\DataHolder\DataHolderInterface;
use BadaBoom\Context;

use Symfony\Component\Serializer\SerializerInterface;

class LogSender extends AbstractSender
{
    const INFO     = 10;
    const DEBUG    = 20;
    const NOTICE   = 30;
    const WARNING  = 40;
    const ALERT    = 50;
    const ERROR    = 60;
    const CRITICAL = 70;

    /**
     * @var \BadaBoom\Adapter\Logger\LoggerAdapterInterface
     */
    protected $adapter;

    /**
     * @param \BadaBoom\Adapter\Logger\LoggerAdapterInterface $adapter
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \BadaBoom\DataHolder\DataHolderInterface $configuration
     */
    public function __construct(LoggerAdapterInterface $adapter, SerializerInterface $serializer, DataHolderInterface $configuration)
    {
        parent::__construct($adapter, $serializer, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        $this->adapter->log(
            $this->serialize($context),
            $context->getVar('log_level', $this->configuration->get('log_level', self::INFO))
        );
        
        $this->handleNextNode($context);
    }
}