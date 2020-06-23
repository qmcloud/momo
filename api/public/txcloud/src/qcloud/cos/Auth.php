<?php
/**
 * Signature create related functions for authenticating with cos system.
 */

namespace QCloud\Cos;

/**
 * Auth class for creating reusable or nonreusable signature.
 */
class Auth {
    // Secret id or secret key is not valid.
    const AUTH_SECRET_ID_KEY_ERROR = -1;
    private $appId;
    private $secretId;
    private $secretKey;

    public function __construct($appId, $secretId, $secretKey) {
        $this->appId = $appId;
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
    }

    /**
     * Create reusable signature for listDirectory in $bucket or uploadFile into $bucket.
     * If $filepath is not null, this signature will be binded with this $filepath.
     * This signature will expire at $expiration timestamp.
     * Return the signature on success.
     * Return error code if parameter is not valid.
     */
    public function createReusableSignature($expiration, $bucket, $filepath = null) {
        $appId = $this->appId;
        $secretId = $this->secretId;
        $secretKey = $this->secretKey;

        if (empty($appId) || empty($secretId) || empty($secretKey)) {
            return self::AUTH_SECRET_ID_KEY_ERROR;
        }

        if (empty($filepath)) {
            return $this->createSignature($appId, $secretId, $secretKey, $expiration, $bucket, null);
        } else {
            if (preg_match('/^\//', $filepath) == 0) {
                $filepath = '/' . $filepath;
            }

            $fileId = '/' . $appId . '/' . $bucket . $filepath;
            return $this->createSignature($appId, $secretId, $secretKey, $expiration, $bucket, $fileId);
        }
    }

    /**
     * Create nonreusable signature for delete $filepath in $bucket.
     * This signature will expire after single usage.
     * Return the signature on success.
     * Return error code if parameter is not valid.
     */
    public function createNonreusableSignature($bucket, $filepath) {
        $appId = $this->appId;
        $secretId = $this->secretId;
        $secretKey = $this->secretKey;

        if (empty($appId) || empty($secretId) || empty($secretKey)) {
            return self::AUTH_SECRET_ID_KEY_ERROR;
        }

        if (preg_match('/^\//', $filepath) == 0) {
            $filepath = '/' . $filepath;
        }
        $fileId = '/' . $appId . '/' . $bucket . $filepath;

        return $this->createSignature($appId, $secretId, $secretKey, 0, $bucket, $fileId);
    }

    /**
     * A helper function for creating signature.
     * Return the signature on success.
     * Return error code if parameter is not valid.
     */
    private function createSignature($appId, $secretId, $secretKey, $expiration, $bucket, $fileId) {
        if (empty($secretId) || empty($secretKey)) {
            return self::AUTH_SECRET_ID_KEY_ERROR;
        }

        $now = time();
        $random = rand();
        $fileId = $this->encodeKey($fileId);
        $plainText = "a=$appId&b=$bucket&k=$secretId&e=$expiration&t=$now&r=$random&f=$fileId";
        $bin = hash_hmac('SHA1', $plainText, $secretKey, true);
        $bin = $bin.$plainText;

        $signature = base64_encode($bin);

        return $signature;
    }
    public static function encodeKey($key) {
        return str_replace('%2F', '/', rawurlencode($key));
    }
}
