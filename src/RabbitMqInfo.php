<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read-only client for the RabbitMQ Management HTTP API.
 *
 * Used by the admin panel to list exchanges and other broker information.
 * All methods return empty results gracefully when the management API is
 * unreachable rather than throwing exceptions.
 */
class RabbitMqInfo
{
    /** Default port for the RabbitMQ Management plugin. */
    private const MANAGEMENT_PORT = 15672;

    /** cURL timeout in seconds. */
    private const TIMEOUT = 3;

    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $pass,
        private readonly int $port = self::MANAGEMENT_PORT
    ) {
    }

    /**
     * Returns the list of exchanges for the given vhost.
     *
     * Each element is an associative array from the RabbitMQ API with at
     * least the keys: name, type, durable, auto_delete, internal, vhost.
     *
     * Returns an empty array when the management API is unavailable.
     *
     * @return list<array<string, mixed>>
     */
    public function getExchanges(string $vhost = '/'): array
    {
        $data = $this->get('exchanges/' . rawurlencode($vhost));

        return is_array($data) ? array_values($data) : [];
    }

    /**
     * Returns the list of queues for the given vhost.
     *
     * Each element includes at least: name, messages, messages_ready,
     * messages_unacknowledged, consumers, durable, auto_delete.
     *
     * Returns an empty array when the management API is unavailable.
     *
     * @return list<array<string, mixed>>
     */
    public function getQueues(string $vhost = '/'): array
    {
        $data = $this->get('queues/' . rawurlencode($vhost));

        return is_array($data) ? array_values($data) : [];
    }

    /**
     * Returns an overview of the broker (cluster name, version, message stats).
     *
     * Returns an empty array when the management API is unavailable.
     *
     * @return array<string, mixed>
     */
    public function getOverview(): array
    {
        $data = $this->get('overview');

        return is_array($data) ? $data : [];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Makes a GET request to the management API and decodes the JSON response.
     *
     * @return mixed Decoded JSON or null on failure.
     */
    private function get(string $path): mixed
    {
        $url = sprintf('http://%s:%d/api/%s', $this->host, $this->port, $path);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_USERPWD        => $this->user . ':' . $this->pass,
            CURLOPT_FAILONERROR    => true,
        ]);

        $body  = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($errno !== 0 || $body === false) {
            return null;
        }

        return json_decode((string) $body, true);
    }
}
