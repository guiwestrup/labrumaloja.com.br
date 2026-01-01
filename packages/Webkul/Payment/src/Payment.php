<?php

namespace Webkul\Payment;

use Illuminate\Support\Facades\Config;

class Payment
{
    /**
     * Returns all supported payment methods
     *
     * @return array
     */
    public function getSupportedPaymentMethods()
    {
        return [
            'payment_methods'  => $this->getPaymentMethods(),
        ];
    }

    /**
     * Returns all supported payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $paymentMethods = [];

        foreach (Config::get('payment_methods') as $paymentMethodConfig) {
            $paymentMethod = app($paymentMethodConfig['class']);

            if ($paymentMethod->isAvailable()) {
                $paymentMethods[] = [
                    'method'       => $paymentMethod->getCode(),
                    'method_title' => $paymentMethod->getTitle(),
                    'description'  => $paymentMethod->getDescription(),
                    'sort'         => $paymentMethod->getSortOrder(),
                    'image'        => $paymentMethod->getImage(),
                ];
            }
        }

        usort($paymentMethods, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }

            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        return $paymentMethods;
    }

    /**
     * Returns payment redirect url if have any
     *
     * @param  \Webkul\Checkout\Contracts\Cart  $cart
     * @return string
     */
    public function getRedirectUrl($cart)
    {
        $configValue = Config::get('payment_methods.'.$cart->payment->method.'.class');

        // Se a configuração não existe ou está vazia, retorna string vazia
        if (empty($configValue)) {
            return '';
        }

        $payment = app($configValue);

        // Verifica se a classe resolvida tem o método antes de chamar
        if (! method_exists($payment, 'getRedirectUrl')) {
            return '';
        }

        return $payment->getRedirectUrl();
    }

    /**
     * Returns payment method additional information
     *
     * @param  string  $code
     * @return array
     */
    public static function getAdditionalDetails($code)
    {
        $configValue = Config::get('payment_methods.'.$code.'.class');

        // Se a configuração não existe ou está vazia, retorna array vazio
        if (empty($configValue)) {
            return [];
        }

        $paymentMethodClass = app($configValue);

        // Verifica se a classe resolvida tem o método antes de chamar
        if (! method_exists($paymentMethodClass, 'getAdditionalDetails')) {
            return [];
        }

        return $paymentMethodClass->getAdditionalDetails();
    }
}
