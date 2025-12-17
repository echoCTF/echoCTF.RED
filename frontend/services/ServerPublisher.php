<?php

namespace app\services;

use yii\httpclient\Client;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;

/**
 * Service responsible for publishing events to an external server endpoint.
 *
 * @package app\services
 * @example
 * ```
 * $publisher = new ServerPublisher(Yii::$app->params['serverPublisher']);
 * $publisher->publish('player1', 'score_update', ['score' => 42]);
 * ```
 */
class ServerPublisher
{
  /**
   * Target endpoint URL.
   *
   * @var string
   */
  private string $url;

  /**
   * Bearer token used for Authorization header.
   *
   * @var string
   */
  private string $bearerToken;

  /**
   * HTTP request timeout in seconds.
   *
   * @var int
   */
  private int $timeout;

  /**
   * Maximum number of retry attempts after the initial request.
   *
   * @var int
   */
  private int $maxRetries;

  /**
   * Base backoff delay in milliseconds.
   *
   * @var int
   */
  private int $backoffMs;

  /**
   * ServerPublisher constructor.
   *
   * @param array{
   *     url: string,
   *     token: string,
   *     timeout?: int,
   *     maxRetries?: int,
   *     backoffMs?: int
   * } $config
   *
   * @throws InvalidConfigException
   */
  public function __construct(array $config)
  {
    if (empty($config['url'])) {
      throw new InvalidConfigException('ServerPublisher config "url" is required.');
    }

    if (empty($config['token'])) {
      throw new InvalidConfigException('ServerPublisher config "token" is required.');
    }

    $this->url         = $config['url'] ?? 'http://localhost:8888/publish';
    $this->bearerToken = $config['token'] ?? 'server123token';
    $this->timeout     = $config['timeout'] ?? 5;
    $this->maxRetries  = $config['maxRetries'] ?? 3;
    $this->backoffMs   = $config['backoffMs'] ?? 200;
  }

  /**
   * Publish an event to the remote server.
   *
   * @param string $playerId
   * @param string $event
   * @param array  $payload
   *
   * @return array
   *
   * @throws ServerErrorHttpException When publishing fails after retries.
   */
  public function publish(string $playerId, string $event, array $payload): array
  {
    $client = new Client();
    $attempt = 0;
    $lastException = null;

    while ($attempt <= $this->maxRetries) {
      try {
        $response = $client->createRequest()
          ->setMethod('POST')
          ->setUrl($this->url)
          ->setHeaders([
            'Authorization' => 'Bearer ' . $this->bearerToken,
            'Content-Type'  => 'application/json',
          ])
          ->setOptions([
            CURLOPT_TIMEOUT => $this->timeout,
          ])
          ->setContent(json_encode([
            'player_id' => $playerId,
            'event'     => $event,
            'payload'   => $payload,
          ]))
          ->send();

        if ($response->isOk) {
          return $response->data ?? [];
        }

        throw new ServerErrorHttpException(
          'Remote server error: HTTP ' . $response->statusCode
        );
      } catch (\Throwable $e) {
        $lastException = $e;

        Yii::error([
          'message'   => 'Server publish attempt failed',
          'attempt'   => $attempt + 1,
          'playerId'  => $playerId,
          'event'     => $event,
          'error'     => $e->getMessage(),
        ], __METHOD__);

        if ($attempt >= $this->maxRetries) {
          break;
        }

        usleep($this->backoffMs * (2 ** $attempt) * 1000);
      }

      $attempt++;
    }

    throw new ServerErrorHttpException(
      'Server publish failed after ' . ($this->maxRetries + 1) . ' attempts',
      0,
      $lastException
    );
  }
}
