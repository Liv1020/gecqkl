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
     * @param array $data
     * @return array
     */
    private function _httpRequest($url, $data = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output, true);
    }

    /**
     * @param string $address
     * @return array
     * @throws Exception
     */
    public function findWallet($address){
        $response = $this->_httpRequest('ethereumseed.storage/wallet?address='.$address);
        if($response['status'] != 200) {
            throw new \Exception('查询钱包错误，请重试');
        }

        return $response['data'];
    }

    /**
     * @param $password
     * @param $mnemonic
     * @return array
     * @throws Exception
     */
    public function createWallet($password, $mnemonic){
        $response = $this->_httpRequest('ethereumseed.storage/wallet', [
            'password' => $password,
            'mnemonic' => $mnemonic,
        ]);
        if($response['status'] != 200) {
            throw new \Exception('创建钱包错误，请重试');
        }

        return $response['data'];
    }

    /**
     * @param $form
     * @param $to
     * @param $val
     * @param $password
     * @return mixed
     * @throws Exception
     */
    public function transaction($form, $to, $val, $password){
        $response = $this->_httpRequest('ethereumseed.storage/transaction', [
            'from' => $form,
            'password' => $password,
            'to' => $to,
            'value' => $val,
        ]);
        if($response['status'] != 200) {
            throw new \Exception('金种子交易失败：'.$response['message']);
        }

        return $response['data'];
    }
}