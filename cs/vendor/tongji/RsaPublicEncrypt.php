<?php

namespace yii\tongji;

/**
 * Utility, provide RSA public encrypt method and gzdecode function
 */
class RsaPublicEncrypt {
    /**
     * @var string
     */
    private $publicKey;
    
    /**
     * @var string
     */
    private $path;

    /**
     * construct
     * @param string $path
     */
    public function __construct($path)
    {
        if(empty($path) || !is_dir($path))
        {
            echo "[error] error public key path: {$path}";
        }
        $this->path = $path;
    }

    /**
     * setup public key
     * @return boolean
     */
    public function setupPublicKey()
    {
        if(is_resource($this->publicKey))
        {
            return true;
        }
        $file = $this->path . DIRECTORY_SEPARATOR .  'api_pub.key';
        $puk = file_get_contents($file);
        $this->publicKey = openssl_pkey_get_public($puk);
        return true;
    }

    /**
     * pub encrypt
     * @param string $data
     * @return string
     */
    public function pubEncrypt($data)
    {
        if(!is_string($data))
        {
            return null;
        }
        $this->setupPublicKey();
        $ret = openssl_public_encrypt($data, $encrypted, $this->publicKey);
        if($ret)
        {
            return $encrypted;
        }
        else
        {
            return null;
        }
    }
    
    /**
     * destruct
     */
    public function __destruct()
    {
        @fclose($this->publicKey);
    }
}

