<?php
/**
 * CMI Payment Gateway Library for Laravel
 *
 * This library provides a simple way to integrate CMI payment gateway into your Laravel application.
 * It allows you to process payments, manage transactions, and handle payment responses from the CMI platform.
 *
 * @package    CMI-Payment-Gateway
 * @version    1.0.0
 * @license    MIT
 * @author     Baidou Abdellah <baidou.abd@gmail.com>
 * @link       https://github.com/baidou5/CMI-Payment-Gateway
 *
 * ### Requirements:
 * - PHP 7.4 or higher
 * - Laravel 8.x or higher
 * - CMI payment account
 *
 * ### License:
 *
 * This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
 */
namespace Baidouabdellah\CmiPaymentGateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use RuntimeException;
use function is_string;

class CmiPaymentService
{
    protected ?string $merchantId;
    protected string $clientId;
    protected string $storeKey;
    protected ?string $apiKey;
    protected ?string $secretKey;
    protected bool $sandbox;
    protected string $baseUri;
    protected string $okUrl;
    protected string $failUrl;
    protected string $shopUrl;
    protected string $callbackUrl;
    protected string $storeType;
    protected string $tranType;
    protected string $lang;
    protected string $currency;
    protected string $hashAlgorithm;
    protected string $encoding;
    protected bool $autoRedirect;
    protected bool $callbackResponse;
    protected int $sessionTimeout;
    protected string $transport;
    protected int $httpTimeout;

    public function __construct()
    {
        $this->merchantId = $this->readNullableStringConfig(['merchant_id', 'merchantId']);
        $this->clientId = $this->readStringConfig(['client_id', 'clientId'], '');
        $this->storeKey = $this->readStringConfig(['store_key', 'storeKey'], '');
        $this->apiKey = $this->readNullableStringConfig(['api_key', 'apiKey']);
        $this->secretKey = $this->readNullableStringConfig(['secret_key', 'secretKey']);
        $this->sandbox = $this->readBoolConfig(['sandbox'], true);
        $this->baseUri = $this->readStringConfig(
            ['base_uri', 'baseUri'],
            'https://testpayment.cmi.co.ma/fim/est3Dgate'
        );
        $this->okUrl = $this->readStringConfig(['ok_url', 'okUrl'], '');
        $this->failUrl = $this->readStringConfig(['fail_url', 'failUrl'], '');
        $this->shopUrl = $this->readStringConfig(['shop_url', 'shopUrl'], '');
        $this->callbackUrl = $this->readStringConfig(['callback_url', 'callbackUrl'], '');
        $this->storeType = $this->readStringConfig(['store_type', 'storeType'], '3D_PAY_HOSTING');
        $this->tranType = $this->readStringConfig(['tran_type', 'tranType'], 'PreAuth');
        $this->lang = $this->readStringConfig(['lang'], 'fr');
        $this->currency = $this->readStringConfig(['currency'], '504');
        $this->hashAlgorithm = $this->readStringConfig(['hash_algorithm', 'hashAlgorithm'], 'ver3');
        $this->encoding = $this->readStringConfig(['encoding'], 'UTF-8');
        $this->autoRedirect = $this->readBoolConfig(['auto_redirect', 'autoRedirect'], true);
        $this->callbackResponse = $this->readBoolConfig(['callback_response', 'callbackResponse'], true);
        $this->sessionTimeout = $this->readIntConfig(['session_timeout', 'sessionTimeout'], 1800);
        $this->transport = $this->readStringConfig(['transport'], 'hosted_form');
        $this->httpTimeout = $this->readIntConfig(['http_timeout'], 15);

        $this->guardAgainstInvalidConfiguration();
    }

    /**
     * @param int|float|string $amount
     * @param array<string, int|float|string|bool|null> $params
     * @return array<string, mixed>
     */
    public function createPayment($amount, string $orderId, string $description, array $params = []): array
    {
        $payload = $this->buildPaymentPayload($amount, $orderId, $description, $params);

        if ($this->transport === 'server_api') {
            return $this->sendRequest($this->baseUri, $payload);
        }

        return [
            'gateway_url' => $this->baseUri,
            'payload' => $payload,
            'hash' => $payload['hash'],
            'transport' => 'hosted_form',
        ];
    }

    /**
     * @param int|float|string $amount
     * @param array<string, int|float|string|bool|null> $params
     * @return array<string, mixed>
     */
    public function buildPaymentPayload($amount, string $orderId, string $description, array $params = []): array
    {
        $amount = cmi_format_amount($amount);
        $orderId = trim((string) $orderId);
        $description = trim((string) $description);

        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0.');
        }
        if ($orderId === '') {
            throw new InvalidArgumentException('Order ID cannot be empty.');
        }
        if ($description === '') {
            throw new InvalidArgumentException('Description cannot be empty.');
        }

        $payload = array_merge([
            'clientid' => $this->clientId,
            'storetype' => $this->storeType,
            'trantype' => $this->tranType,
            'amount' => (string) $amount,
            'oid' => $orderId,
            'okUrl' => $this->okUrl,
            'failUrl' => $this->failUrl,
            'shopUrl' => $this->shopUrl,
            'callbackUrl' => $this->callbackUrl,
            'lang' => $this->lang,
            'currency' => $this->currency,
            'encoding' => $this->encoding,
            'hashAlgorithm' => $this->hashAlgorithm,
            'sessionTimeout' => (string) $this->sessionTimeout,
            'rnd' => (string) microtime(true),
            'BillToName' => $description,
        ], $params);

        $payload['hash'] = $this->generateHash($payload);

        return $payload;
    }

    /**
     * @param array<string, int|float|string|bool|null> $data
     */
    public function generateHash(array $data): string
    {
        $plainText = '';
        ksort($data);
        foreach ($data as $key => $value) {
            $formattedValue = trim((string) $value);
            $formattedValue = str_replace('|', '\\|', str_replace('\\', '\\\\', $formattedValue));
            if (strtolower($key) !== 'hash' && strtolower($key) !== 'encoding') {
                $plainText .= $formattedValue.'|';
            }
        }

        $escapedStoreKey = str_replace('|', '\\|', str_replace('\\', '\\\\', $this->storeKey));

        return base64_encode(pack('H*', hash('sha512', $plainText.$escapedStoreKey)));
    }

    /**
     * @param array<string, int|float|string|bool|null> $data
     */
    public function validateHash(array $data, ?string $actualHash = null): bool
    {
        $actualHash = $actualHash ?: ($data['HASH'] ?? $data['hash'] ?? null);
        if (! is_string($actualHash) || $actualHash === '') {
            return false;
        }

        $postParams = array_keys($data);
        natcasesort($postParams);

        $hashString = '';
        foreach ($postParams as $param) {
            $lowerParam = strtolower((string) $param);
            if ($lowerParam === 'hash' || $lowerParam === 'encoding') {
                continue;
            }

            $paramValue = trim((string) html_entity_decode(
                preg_replace("/\n$/", '', (string) $data[$param]) ?? '',
                ENT_QUOTES,
                'UTF-8'
            ));
            $escapedParamValue = str_replace('|', '\\|', str_replace('\\', '\\\\', $paramValue));
            $escapedParamValue = preg_replace('/document(.)/i', 'document.', $escapedParamValue);
            $hashString .= $escapedParamValue.'|';
        }

        $escapedStoreKey = str_replace('|', '\\|', str_replace('\\', '\\\\', $this->storeKey));
        $calculatedHash = base64_encode(pack('H*', hash('sha512', $hashString.$escapedStoreKey)));

        return hash_equals($actualHash, $calculatedHash);
    }

    /**
     * @param array<string, int|float|string|bool|null> $postData
     */
    public function resolveCallbackResponse(array $postData): string
    {
        if (empty($postData)) {
            return 'No Data POST';
        }

        $procReturnCode = (string) ($postData['ProcReturnCode'] ?? '');
        if ($this->validateHash($postData) && $procReturnCode === '00') {
            return 'ACTION=POSTAUTH';
        }

        return 'FAILURE';
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function sendRequest(string $url, array $data): array
    {
        $client = new Client([
            'timeout' => $this->httpTimeout,
            'connect_timeout' => 5,
            'http_errors' => false,
        ]);

        try {
            $response = $client->post($url, [
                'form_params' => $data,
                'headers' => array_filter([
                    'Authorization' => $this->apiKey ? 'Bearer '.$this->apiKey : null,
                ]),
            ]);
        } catch (GuzzleException $exception) {
            throw new RuntimeException('CMI request failed: '.$exception->getMessage(), 0, $exception);
        }

        return [
            'status' => $response->getStatusCode(),
            'body' => (string) $response->getBody(),
            'headers' => $response->getHeaders(),
        ];
    }

    protected function guardAgainstInvalidConfiguration(): void
    {
        if ($this->clientId === '' || preg_match('/\s/', $this->clientId)) {
            throw new InvalidArgumentException('Invalid CMI client ID.');
        }
        if ($this->storeKey === '' || preg_match('/\s/', $this->storeKey)) {
            throw new InvalidArgumentException('Invalid CMI store key.');
        }
        if (! in_array($this->lang, ['ar', 'fr', 'en'], true)) {
            throw new InvalidArgumentException('CMI language must be one of: ar, fr, en.');
        }
        if ((int) $this->sessionTimeout < 30 || (int) $this->sessionTimeout > 2700) {
            throw new InvalidArgumentException('CMI session timeout must be between 30 and 2700 seconds.');
        }

        foreach (['baseUri' => $this->baseUri, 'okUrl' => $this->okUrl, 'failUrl' => $this->failUrl, 'shopUrl' => $this->shopUrl, 'callbackUrl' => $this->callbackUrl] as $name => $url) {
            if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
                throw new InvalidArgumentException(sprintf('Invalid CMI url for %s.', $name));
            }
        }
    }

    /**
     * Read values from both `cmi.*` and `cmi-payment.*` to stay compatible
     * with both configuration styles.
     */
    /**
     * @param array<int, string> $keys
     */
    protected function readNullableStringConfig(array $keys): ?string
    {
        foreach ($keys as $key) {
            foreach (["cmi.$key", "cmi-payment.$key"] as $fullKey) {
                $value = config($fullKey);
                if (is_string($value)) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * @param array<int, string> $keys
     */
    protected function readStringConfig(array $keys, string $default): string
    {
        $value = $this->readNullableStringConfig($keys);

        return $value ?? $default;
    }

    /**
     * @param array<int, string> $keys
     */
    protected function readBoolConfig(array $keys, bool $default): bool
    {
        foreach ($keys as $key) {
            foreach (["cmi.$key", "cmi-payment.$key"] as $fullKey) {
                $value = config($fullKey);
                if (is_bool($value)) {
                    return $value;
                }
                if (is_string($value)) {
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                if (is_int($value)) {
                    return $value === 1;
                }
            }
        }

        return $default;
    }

    /**
     * @param array<int, string> $keys
     */
    protected function readIntConfig(array $keys, int $default): int
    {
        foreach ($keys as $key) {
            foreach (["cmi.$key", "cmi-payment.$key"] as $fullKey) {
                $value = config($fullKey);
                if (is_int($value)) {
                    return $value;
                }
                if (is_string($value) && is_numeric($value)) {
                    return (int) $value;
                }
            }
        }

        return $default;
    }
}
