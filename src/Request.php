<?php

namespace Huangdijia\Curl;

use Huangdjia\Curl\Response;

class Request
{
    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var string
     */
    protected $bodyFormat;
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var int
     */
    protected $tries;
    /**
     * @var int
     */
    protected $retryDelay;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    /**
     * Set the base URL for the pending request.
     *
     * @param  string  $url
     * @return $this
     */
    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Specify the body format of the request.
     *
     * @param  string  $format
     * @return $this
     */
    public function bodyFormat(string $format)
    {
        $this->bodyFormat = $format;

        return $this;
    }

    /**
     * Specify the request's content type.
     *
     * @param  string  $contentType
     * @return $this
     */
    public function contentType(string $contentType)
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    /**
     * Add the given headers to the request.
     *
     * @param  array  $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->options = array_merge_recursive($this->options, [
            'headers' => $headers,
        ]);

        return $this;
    }

    /**
     * Specify the basic authentication username and password for the request.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withBasicAuth(string $username, string $password)
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD  => "{$username}:{$password}",
        ]);

        return $this;

        return $this;
    }

    /**
     * Specify the digest authentication username and password for the request.
     *
     * @param  string  $username
     * @param  string  $password
     * @return $this
     */
    public function withDigestAuth($username, $password)
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
            CURLOPT_USERPWD  => "{$username}:{$password}",
        ]);

        return $this;
    }

    /**
     * Specify an authorization token for the request.
     *
     * @param  string  $token
     * @param  string  $type
     * @return $this
     */
    public function withToken($token, $type = 'Bearer')
    {
        // $this->options['headers']['Authorization'] = trim($type . ' ' . $token);
        // return $this;

        return $this->withHeaders([
            'Authorization' => trim($type . ' ' . $token),
        ]);
    }

    /**
     * Specify the user agent for the request.
     *
     * @param  string  $userAgent
     * @return $this
     */
    public function withUserAgent($userAgent)
    {
        return $this->withHeaders(['User-Agent' => $userAgent]);
    }

    /**
     * Specify the cookies that should be included with the request.
     *
     * @param  array  $cookies
     * @param  string  $domain
     * @return $this
     */
    public function withCookies(array $cookies, string $domain)
    {
        $cookieArr = [];

        foreach ($cookies as $key => $value) {
            $cookieArr[] = "{$key}:{$value}";
        }

        return $this->withHeaders([
            'Cookie' => implode('; ', $cookieArr),
        ]);
    }

    /**
     * Indicate that TLS certificates should not be verified.
     *
     * @return $this
     */
    public function withoutVerifying()
    {
        // $this->options['verify'] = false;

        $this->options = array_merge_recursive($this->options, [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function timeout(int $seconds)
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_TIMEOUT => $seconds,
        ]);

        return $this;
    }

    /**
     * Specify the number of times the request should be attempted.
     *
     * @param  int  $times
     * @param  int  $sleep
     * @return $this
     */
    public function retry(int $times, int $sleep = 0)
    {
        $this->tries      = $times;
        $this->retryDelay = $sleep;

        return $this;
    }

    /**
     * Merge new options into the client.
     *
     * @param  array  $options
     * @return $this
     */
    public function withOptions(array $options)
    {
        $this->options = array_merge_recursive($this->options, $options);

        return $this;
    }

    /**
     * @param string $contentType
     * @return $this
     */
    public function accept($contentType)
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /**
     * Indicate that JSON should be returned by the server.
     *
     * @return $this
     */
    public function acceptJson()
    {
        return $this->accept('application/json');
    }

    /**
     * Indicate the request contains JSON.
     *
     * @return $this
     */
    public function asJson()
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    /**
     * Indicate the request contains form parameters.
     *
     * @return $this
     */
    public function asForm()
    {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    /**
     * @param string $url
     * @param array $query
     * @return Response
     */
    public function get(string $url, array $query = [])
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_HTTPGET => true,
        ]);

        return $this->send('GET', $url);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function post(string $url, array $data = [])
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $data,
        ]);

        return $this->send('POST', $url);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function put(string $url, array $data = [])
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_POSTFIELDS => $data,
        ]);

        return $this->send('PUT', $url);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function delete(string $url, array $data = [])
    {
        return $this->send('DELETE', $url);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function head(string $url, array $data = [])
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_NOBODY => true,
        ]);
        return $this->send('HEAD', $url);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function patch(string $url, array $data = [])
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_POSTFIELDS => $data,
        ]);

        return $this->send('PATCH', $url);
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function options(string $url, array $data = [])
    {
        return $this->send('OPTIONS', $url);
    }

    /**
     * @param string $method
     * @param string $url
     * @return Response
     */
    protected function send(string $method, string $url)
    {
        $this->options = array_merge_recursive($this->options, [
            CURLOPT_URL            => rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'),
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLINFO_HEADER_OUT    => true,
        ]);

        $options = $this->pendingOptions();

        curl_setopt_array($this->ch, $options);

        return retry($this->tries ?? 1, function() {
            return new Response($this->ch);
        }, $this->retryDelay ?? 100);
    }

    /**
     * @return array
     */
    protected function pendingOptions()
    {
        $options = $this->options;

        // headers
        if (isset($options['headers'])) {
            $headers = [];

            foreach ($options['headers'] as $key => $value) {
                $headers[] = sprintf(
                    '%s: %s',
                    $key,
                    is_array($value) ? implode(',', $value) : $value
                );
            }

            unset($options['headers']);
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        return $options;
    }
}
