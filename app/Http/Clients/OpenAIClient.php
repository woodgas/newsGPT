<?php declare(strict_types=1, encoding='UTF-8');

namespace App\Http\Clients;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenAIClient
{
    private static array $settings;
    private static string $token;
    private static array $headers;

    public function __construct($token)
    {
        self::$token = $token;
        self::$settings = config('openai');
        self::$headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::$token,
        ];
    }

    /**
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestEdits(array $input): string
    {
        return $this->request(self::$settings['edits']['uri'], $input);
    }

    /**
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestChat(array $input): string
    {
        return $this->request(self::$settings['chat']['uri'], $input);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function request(string $uri, array $input): string
    {
        try {
            $response = (new Client())->post($uri, [
                'headers' => self::$headers,
                'body' => json_encode($input),
            ]);
        } catch (RequestException $e) {
            // Handle exceptions from the request
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $responseBody = $response->getBody()->getContents();
                $statusCode = $response->getStatusCode();
                throw new Exception("OpenAI API request failed with status code $statusCode. Response body: $responseBody");
            } else {
                throw new Exception("OpenAI API request failed: " . $e->getMessage());
            }
        } catch (Exception $e) {
            // Handle other exceptions
            throw new Exception("OpenAI API request failed: " . $e->getMessage());
        }
        return $response->getBody()->getContents();
    }
}
