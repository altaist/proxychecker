<?php

namespace App\Services;

use App\Models\Host;
use App\Models\Task;
use Illuminate\Support\Collection;

class CheckingProxyService extends BaseService
{

    private $timeoutForSingleRequest = 1;
    private $testingUrl = "https://checkerproxy.net/";

    public function __construct()
    {
        // TODO: configure params from config
    }

    public function processChunk($hosts): void
    {
        $this->checkProxies($hosts); // TODO - get chunked hosts
    }

    private function checkProxies($hosts)
    {
        // TODO - get multiple curl
        //dd($hosts);
        collect($hosts)->each(fn ($item) => $this->checkSingleProxy($item));
    }

    private function checkSingleProxy(Host $host)
    {
        $timeStart = microtime(true);

        $address = $host ->address;

        $resultHttp = $this->proccessCurl($address);
        $resultSocks = $this->proccessCurl($address, CURLPROXY_SOCKS5);

        $duration = microtime(true) - $timeStart;
        $info = [
            'http' => $resultHttp,
            'socks' => $resultSocks,
            'duration' => $duration
        ];

        $host->info = $info;
        $host->save();
        // TODO: send event to init broadcasting
    }

    private function proccessCurl($address, $proxyType = CURLPROXY_HTTP): bool
    {
        $result = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->testingUrl);
        curl_setopt($ch, CURLOPT_PROXY, $address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PROXYTYPE, $proxyType);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutForSingleRequest);

        curl_exec($ch);
        if(curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) !=200) {
            $result = false;
        }

        return $result;
    }
}
