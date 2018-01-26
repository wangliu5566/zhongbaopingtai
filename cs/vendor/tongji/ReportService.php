<?php

namespace yii\tongji;

/**
 * ReportService
 */
class ReportService {
    private $apiUrl;
    private $userName;
    private $token;
    private $ucid;
    private $st;
    private $account_type;
    
    /**
     * construct
     * @param string $apiUrl
     * @param string $userName
     * @param string $token
     * @param string $ucid
     * @param string $st
     */
    public function __construct($apiUrl, $userName, $token, $ucid, $st, $uuid, $account_type) {
        $this->apiUrl = $apiUrl;
        $this->userName = $userName;
        $this->token = $token;
        $this->ucid = $ucid;
        $this->st = $st;
        $this->uuid = $uuid;
        $this->account_type = $account_type;
    }
    
    /**
     * get site list
     * @return array
     */
    public function getSiteList() {
        $apiConnection = new DataApiConnection();
        $apiConnection->init($this->apiUrl . '/getSiteList', $this->ucid, $this->uuid);

        $apiConnectionData = array(
            'header' => array(
                'username' => $this->userName,
                'password' => $this->st,
                'token' => $this->token,
                'account_type' => $this->account_type,
            ),
            'body' => null,
        );
        $apiConnection->POST($apiConnectionData);
        
        return array(
            'header' => $apiConnection->retHead,
            'body' => $apiConnection->retBody,
            'raw' => $apiConnection->retRaw,
        );
    }

    /**
     * get data
     * @param array $parameters
     * @return array
     */
    public function getData($parameters) {
        $apiConnection = new DataApiConnection();
        $apiConnection->init($this->apiUrl . '/getData', $this->ucid, $this->uuid);

        $apiConnectionData = array(
            'header' => array(
                'username' => $this->userName,
                'password' => $this->st,
                'token' => $this->token,
                'account_type' => $this->account_type,
            ),
            'body' => $parameters,
        );
        $apiConnection->POST($apiConnectionData);
        
        return array(
            'header' => $apiConnection->retHead,
            'body' => $apiConnection->retBody,
            'raw' => $apiConnection->retRaw,
        );
    }
}
