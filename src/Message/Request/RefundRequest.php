<?php

namespace Omnipay\Payrexx\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Payrexx\Message\Response\RefundResponse;
use Payrexx\Models\Request\Transaction;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;

/**
 * @see https://developers.payrexx.com/reference#refund-a-transaction
 */
class RefundRequest extends AbstractRequest
{
    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInstance($value)
    {
        return $this->setParameter('instance', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiBaseDomain($value)
    {
        return $this->setParameter('apiBaseDomain', $value);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('apiKey', 'instance', 'transactionReference');

        $data = [];
        $data['apiKey'] = $this->getApiKey();
        $data['instance'] = $this->getInstance();
        $data['id'] = $this->getTransactionReference();

        if ($this->getAmountInteger()) {
            $data['amount'] = $this->getAmountInteger();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return string
     */
    public function getInstance()
    {
        return $this->getParameter('instance');
    }

    /**
     * @return string
     */
    public function getApiBaseDomain()
    {
        return $this->getParameter('apiBaseDomain');
    }

    /**
     * @param array $data
     * @return RefundResponse
     * @throws InvalidRequestException
     */
    public function sendData($data)
    {
        try {
            $payrexx = new Payrexx($data['instance'], $data['apiKey'], '', $data['apiBaseDomain']);

            $transaction = new Transaction();
            $transaction->setId($data['id']);
            $transaction->setAmount($data['amount'] ?? 0);

            $response = $payrexx->refund($transaction);
        } catch (PayrexxException $e) {
            throw new InvalidRequestException($e->getMessage());
        }

        return $this->response = new RefundResponse($this, $response);
    }
}
