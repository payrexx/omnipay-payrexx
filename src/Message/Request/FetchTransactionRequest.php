<?php

namespace Omnipay\Payrexx\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Payrexx\Message\Response\FetchTransactionResponse;
use Payrexx\Models\Request\Transaction;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;

/**
 * @see https://developers.payrexx.com/reference#retrieve-a-transaction
 */
class FetchTransactionRequest extends AbstractRequest
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
        $data['apiBaseDomain'] = $this->getApiBaseDomain() ?: '';
        $data['id'] = $this->getTransactionReference();

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
     * @return FetchTransactionResponse
     */

    /**
     * @param array $data
     * @return FetchTransactionResponse
     * @throws InvalidRequestException
     */
    public function sendData($data)
    {
        try {
            $payrexx = new Payrexx($data['instance'], $data['apiKey'], '', $data['apiBaseDomain']);
            $transaction = new Transaction();
            $transaction->setId($data['id']);
            $response = $payrexx->getOne($transaction);
        } catch (PayrexxException $e) {
            throw new InvalidRequestException($e->getMessage());
        }

        return $this->response = new FetchTransactionResponse($this, $response);
    }
}
