<?php

namespace yii\tongji;

/**
 * LoginService
 */
class LoginService {
    /**
     * @var string
     */
    private $loginUrl;
    
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $account_type;
    
    /**
     * construct
     * @param string $loginUrl
     * @param string $uuid
     */
    public function __construct($loginUrl, $uuid, $account_type) {
        $this->loginUrl = $loginUrl;
        $this->uuid = $uuid;
        $this->account_type = $account_type;
    }
    
    /**
     * preLogin
     * @param string $userName
     * @param string $token
     * @return boolean 
     */
    public function preLogin($userName, $token) {
        $preLogin = new LoginConnection();
        $preLogin->init($this->loginUrl, $this->uuid, $this->account_type);
        $preLoginData = array(
            'username' => $userName,
            'token' => $token,
            'functionName' => 'preLogin',
            'uuid' => $this->uuid,
            'request' => array(
                'osVersion' => 'windows',
                'deviceType' => 'pc',
                'clientVersion' => '1.0',
            ),
        );
        $preLogin->POST($preLoginData);

        if ($preLogin->returnCode === 0) {
            //$retData = gzdecode($preLogin->retData, strlen($preLogin->retData));
            $retData = gzinflate(substr($preLogin->retData,10,-8));
            $retArray = json_decode($retData, true);
            if (!isset($retArray['needAuthCode']) || $retArray['needAuthCode'] === true) {
                //echo "[error] preLogin return data format error: {$retData}";
                return false;
            }
            else if ($retArray['needAuthCode'] === false) {
                return true;
            }
            else {
                //echo "[error] unexpected preLogin return data: {$retData}";
                return false;
            }
        }
        else {
            //echo "[error] preLogin unsuccessfully with return code: {$preLogin->returnCode}";
            return false;
        }
    }

    /**
     * doLogin
     * @param string $userName
     * @param string $password
     * @param string $token
     * @return array
     */
    public function doLogin($userName, $password, $token) {
        $doLogin = new LoginConnection();
        $doLogin->init($this->loginUrl, $this->uuid, $this->account_type);
        $doLoginData = array(
            'username' => $userName,
            'token' => $token,
            'functionName' => 'doLogin',
            'uuid' => $this->uuid,
            'request' => array(
                'password' => $password,
            ),
        );
        $doLogin->POST($doLoginData);

        if ($doLogin->returnCode === 0) {
            //$retData = gzdecode($doLogin->retData, strlen($doLogin->retData));
            $retData = gzinflate(substr($doLogin->retData,10,-8));
            $retArray = json_decode($retData, true);
            if (!isset($retArray['retcode']) || !isset($retArray['ucid']) || !isset($retArray['st'])) {
                //echo "[error] doLogin return data format error: {$retData}";
                return null;
            }
            else if ($retArray['retcode'] === 0) {
                //echo '[notice] doLogin successfully!';
                return array(
                    'ucid' => $retArray['ucid'],
                    'st' => $retArray['st'],
                );
            }
            else {
                //echo "[error] doLogin unsuccessfully with retcode: {$retArray['retcode']}";
                return null;
            }
        }
        else {
            //echo "[error] doLogin unsuccessfully with return code: {$doLogin->returnCode}";
            return null;
        }
    }

    /**
     * doLogout
     * @param string $userName
     * @param string $token
     * @param string $ucid
     * @param string $st
     * @return boolean
     */
    public function doLogout($userName, $token, $ucid, $st) {
        $doLogout = new LoginConnection();
        $doLogout->init($this->loginUrl, $this->uuid, $this->account_type);
        $doLogoutData = array(
            'username' => $userName,
            'token' => $token,
            'functionName' => 'doLogout',
            'uuid' => $this->uuid,
            'request' => array(
                'ucid' => $ucid,
                'st' => $st,
            ),
        );
        $doLogout->POST($doLogoutData);

        if ($doLogout->returnCode === 0) {
            $retData = gzdecode($doLogout->retData, strlen($doLogout->retData));
            $retArray = json_decode($retData, true);
            if (!isset($retArray['retcode'])) {
                //echo "[error] doLogout return data format error: {$retData}";
                return false;
            }
            else if ($retArray['retcode'] === 0 ) {
                //echo '[notice] doLogout successfully!';
                return true;
            }
            else {
                //echo "[error] doLogout unsuccessfully with retcode: {$retArray['retcode']}";
                return false;
            }
        }
        else {
            //echo "[error] doLogout unsuccessfully with return code: {$doLogout->returnCode}";
            return false;
        }
    }
}
