<?php

namespace RB\System\Service\Http;

use RB\System\Exception\HttpException;

class HttpService
{
    public const DEFAULT_TIME_OUT = 5;
    public const DEFAULT_METHOD = Request::METHOD_GET;

    private $curl = null;

    public function send(Request $request): Response
    {
        if (!$request->getUrl()) {
            throw new HttpException('Not exist request url');
        }
        if (!$request->getMethod()) $request->setMethod(self::DEFAULT_METHOD);
        if (!$request->getTimeOut()) $request->setTimeOut(self::DEFAULT_TIME_OUT);

        $this->curlInit();

        \curl_setopt($this->curl, CURLOPT_URL, $request->getUrl());
        \curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        \curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $request->getTimeOut());
        \curl_setopt($this->curl, CURLOPT_TIMEOUT, $request->getTimeOut());
        if ($request->getBody()) \curl_setopt($this->curl, CURLOPT_POSTFIELDS, $request->getBody());
        \curl_setopt($this->curl, CURLOPT_HTTPHEADER, $request->getHeaders());

        $result = \curl_exec($this->curl);

        if (\curl_errno($this->curl)) {
            throw new HttpException(sprintf(
                'Got curl error `%s` while `%s` to `%s`.',
                \curl_error($this->curl),
                $request->getMethod(),
                $request->getUrl()
            ));
        }

        $response = new Response(\curl_getinfo($this->curl));

        $body = @\json_decode($result, true);
        if ($body) $response->setBody($body);

        return $response;

    }

    private function curlInit(): void
    {
        if (!$this->curl) {
            $this->curl = \curl_init();
            \curl_setopt($this->curl, CURLOPT_FORBID_REUSE, false);
            \curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
            \curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip');
            \curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($this->curl, CURLINFO_HEADER_OUT, true);
        }
    }
}