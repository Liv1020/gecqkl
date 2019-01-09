<?php
/**
 * User: Pavle Lee <523260513@qq.com>
 * Date: 2019-01-09
 * Time: 15:10
 */

class BlockChain
{
    /**
     * @param $url
     * @param null $data
     * @return array
     */
    private function _httpRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output);
    }

    public function findWallet($address){
        $response = $this->_httpRequest('ethereumseed.storage/wallet?address='.$address);

    }
}