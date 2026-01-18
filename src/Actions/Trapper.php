<?php

declare(strict_types=1);

namespace BytesCommerce\Zabbix\Actions;

use BytesCommerce\Zabbix\Enums\RequestEnum;
use BytesCommerce\Zabbix\ZabbixApiException;

final class Trapper
{
    private const string ZABBIX_HEADER = "ZBXD\x01";

    private const int DEFAULT_PORT = 10051;

    private const int DEFAULT_TIMEOUT = 30;

    public function __construct(
        private readonly string $zabbixServer,
        private readonly int $port = self::DEFAULT_PORT,
        private readonly int $timeout = self::DEFAULT_TIMEOUT,
    ) {
    }

    /**
     * @return array{
     *     response: string,
     *     info: string,
     *     processed?: int,
     *     failed?: int,
     *     total?: int,
     *     seconds_spent?: float
     * }
     *
     * @throws ZabbixApiException
     */
    public function send(string $host, string $key, mixed $value, ?int $clock = null): array
    {
        $data = [
            [
                'host' => $host,
                'key' => $key,
                'value' => (string) $value
            ]
        ];

        if ($clock !== null) {
            $data[0]['clock'] = $clock;
        }

        return $this->sendBatch($data);
    }

    /**
     * @param array<array{
     *     host: string,
     *     key: string,
     *     value: mixed,
     *     clock?: int
     * }> $metrics Array of metric data
     *
     * @return array{
     *     response: string,
     *     info: string,
     *     processed?: int,
     *     failed?: int,
     *     total?: int,
     *     seconds_spent?: float
     * }
     *
     * @throws ZabbixApiException
     */
    public function sendBatch(array $metrics): array
    {
        $data = [];
        foreach ($metrics as $metric) {
            if (!isset($metric['host']) || !isset($metric['key']) || !isset($metric['value'])) {
                throw new ZabbixApiException('Each metric must have host, key, and value', -1);
            }

            $item = [
                'host' => $metric['host'],
                'key' => $metric['key'],
                'value' => (string) $metric['value']
            ];

            if (isset($metric['clock'])) {
                $item['clock'] = $metric['clock'];
            }

            $data[] = $item;
        }

        $payload = [
            'request' => RequestEnum::SENDER_DATA->value,
            'data' => $data
        ];

        $json = json_encode($payload, \JSON_THROW_ON_ERROR);
        $message = $this->buildMessage($json);

        return $this->sendToZabbix($message);
    }

    private function buildMessage(string $json): string
    {
        $length = strlen($json);
        $lengthPacked = pack('P', $length); // 64-bit little-endian unsigned long long

        return self::ZABBIX_HEADER . $lengthPacked . $json;
    }

    private function sendToZabbix(string $message): array
    {
        $socket = @stream_socket_client(
            "tcp://{$this->zabbixServer}:{$this->port}",
            $errno,
            $errstr,
            $this->timeout,
        );

        if (!$socket) {
            throw new ZabbixApiException("Cannot connect to Zabbix server: {$errstr} ({$errno})", -1);
        }

        stream_set_timeout($socket, $this->timeout);

        if (fwrite($socket, $message) === false) {
            fclose($socket);

            throw new ZabbixApiException('Failed to send data to Zabbix server', -1);
        }

        $response = '';
        while (!feof($socket)) {
            $chunk = fread($socket, 8192);
            if ($chunk === false) {
                break;
            }
            $response .= $chunk;
        }

        fclose($socket);

        if (empty($response)) {
            throw new ZabbixApiException('No response received from Zabbix server', -1);
        }

        if (strlen($response) < 13) {
            throw new ZabbixApiException('Invalid response format from Zabbix server', -1);
        }

        $jsonResponse = substr($response, 13);

        try {
            $result = json_decode($jsonResponse, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ZabbixApiException('Invalid JSON response from Zabbix server: ' . $e->getMessage(), -1);
        }

        if (isset($result['info'])) {
            $result = array_merge($result, $this->parseInfoString($result['info']));
        }

        return $result;
    }

    private function parseInfoString(string $info): array
    {
        $metrics = [];

        if (preg_match('/processed:\s*(\d+)/', $info, $matches)) {
            $metrics['processed'] = (int) $matches[1];
        }

        if (preg_match('/failed:\s*(\d+)/', $info, $matches)) {
            $metrics['failed'] = (int) $matches[1];
        }

        if (preg_match('/total:\s*(\d+)/', $info, $matches)) {
            $metrics['total'] = (int) $matches[1];
        }

        if (preg_match('/seconds spent:\s*([\d.]+)/', $info, $matches)) {
            $metrics['seconds_spent'] = (float) $matches[1];
        }

        return $metrics;
    }
}
