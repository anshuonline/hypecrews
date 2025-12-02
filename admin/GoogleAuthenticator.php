<?php

class GoogleAuthenticator {
    
    protected $secret;
    protected $passCodeLength = 6;
    protected $pinModulo;
    protected $secretLength = 10;
    
    public function __construct() {
        $this->pinModulo = pow(10, $this->passCodeLength);
    }
    
    /**
     * Generate a secret key
     */
    public function createSecret($secretLength = 16) {
        $validChars = $this->getBase32LookupTable();
        unset($validChars[32]);
        
        $secret = '';
        for ($i = 0; $i < $secretLength; $i++) {
            $secret .= $validChars[array_rand($validChars)];
        }
        return $secret;
    }
    
    /**
     * Calculate the code, with given secret and point in time
     */
    public function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        
        $secretkey = $this->base32Decode($secret);
        
        // Pack time into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        // Hash it with users secret key
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        // Use last nipple of result as index/offset
        $offset = ord(substr($hm, -1)) & 0x0F;
        // grab 4 bytes of the result
        $hashpart = substr($hm, $offset, 4);
        
        // Unpak binary value
        $value = unpack('N', $hashpart);
        $value = $value[1];
        // Only 32 bits
        $value = $value & 0x7FFFFFFF;
        
        $modulo = pow(10, $this->passCodeLength);
        return str_pad($value % $modulo, $this->passCodeLength, '0', STR_PAD_LEFT);
    }
    
    /**
     * Check if the code is correct. This will accept codes starting from $discrepancy*30sec ago to $discrepancy*30sec from now
     */
    public function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = null) {
        if ($currentTimeSlice === null) {
            $currentTimeSlice = floor(time() / 30);
        }
        
        if (strlen($code) != 6) {
            return false;
        }
        
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($this->timingSafeEquals($calculatedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Timing safe equals comparison
     */
    private function timingSafeEquals($safeString, $userString) {
        if (function_exists('hash_equals')) {
            return hash_equals($safeString, $userString);
        }
        $safeLen = strlen($safeString);
        $userLen = strlen($userString);
        
        if ($userLen != $safeLen) {
            return false;
        }
        
        $result = 0;
        
        for ($i = 0; $i < $userLen; $i++) {
            $result |= (ord($safeString[$i]) ^ ord($userString[$i]));
        }
        
        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }
    
    /**
     * Get QR code URL
     */
    public function getQRCodeUrl($name, $secret) {
        $urlencoded = urlencode('otpauth://totp/'.$name.'?secret='.$secret.'');
        return 'https://api.qrserver.com/v1/create-qr-code/?data=' . $urlencoded . '&size=200x200&ecc=M';
    }
    
    /**
     * Base32 decoding
     */
    private function base32Decode($secret) {
        if (empty($secret)) return '';
        
        $base32chars = $this->getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);
        
        $paddingCharCount = substr_count($secret, $base32chars[32]);
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues)) return false;
        for ($i = 0; $i < 4; $i++){
            if ($paddingCharCount == $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) return false;
        }
        $secret = str_replace('=','', $secret);
        $secret = str_split($secret);
        $bin = "";
        $i = 0;
        while ($i < count($secret)) {
            $d = "";
            for ($j = 0; $j < 8 && $i + $j < count($secret); $j++) {
                $d .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $bin .= substr($d, 0, intval(strlen($d) / 8) * 8);
            $i += 8;
        }
        $bin = str_split($bin, 8);
        $res = "";
        for ($i = 0; $i < count($bin); $i++) {
            $res .= chr(bindec($bin[$i]));
        }
        return $res;
    }
    
    /**
     * Get base32 lookup table
     */
    private function getBase32LookupTable() {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '='  // padding char
        );
    }
}
?>