<?php
	/*
	 * Random Unique ID generating class
	 * 
	 * Generates Random Unique IDs that can be used in any application
	 * 
	 * @name: class.uuid.php
	 * @author: Marius Karthaus
	 * @author: Bade Iriabho <ebade@yahoo.com>
	 * @copyright: 2011-12 Bade Iriabho
	 * @license: Free to use, just remember the first law of sharing "Give credit where it is due". Author is not liable for any damages that results from using this code.
	 * @version: See VERSION
	 * 
	 */
	class uuid {
	    
	    protected $urand;
	    
	    public function __construct() {
	        $this->urand = @fopen ( '/dev/urandom', 'rb' );
	    }
	
	    /**
	     * @brief Generates a Universally Unique IDentifier, version 4.
	     *
	     * This function generates a truly random UUID. The built in CakePHP String::uuid() function
	     * is not cryptographically secure. You should uses this function instead.
	     *
	     * @see http://tools.ietf.org/html/rfc4122#section-4.4
	     * @see http://en.wikipedia.org/wiki/UUID
	     * @return string A UUID, made up of 32 hex digits and 4 hyphens.
	     */
	    function get() {
	        
	        $pr_bits = false;
	        if (is_a ( $this, 'uuid' )) {
	            if (is_resource ( $this->urand )) {
	                $pr_bits .= @fread ( $this->urand, 16 );
	            }
	        }
	        if (! $pr_bits) {
	            $fp = @fopen ( '/dev/urandom', 'rb' );
	            if ($fp !== false) {
	                $pr_bits .= @fread ( $fp, 16 );
	                @fclose ( $fp );
	            } else {
	                // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
	                $pr_bits = "";
	                for($cnt = 0; $cnt < 16; $cnt ++) {
	                    $pr_bits .= chr ( mt_rand ( 0, 255 ) );
	                }
	            }
	        }
	        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
	        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
	        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
	        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
	        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );
	        
	        $prefix = uuid::prefix();
	        
	        /**
	         * Set the four most significant bits (bits 12 through 15) of the
	         * time_hi_and_version field to the 4-bit version number from
	         * Section 4.1.3.
	         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
	         */
	        $time_hi_and_version = hexdec ( $time_hi_and_version );
	        $time_hi_and_version = $time_hi_and_version >> 4;
	        $time_hi_and_version = $time_hi_and_version | 0x4000;
	        
	        /**
	         * Set the two most significant bits (bits 6 and 7) of the
	         * clock_seq_hi_and_reserved to zero and one, respectively.
	         */
	        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
	        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
	        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;
	        
	        return sprintf ( '%4s%08s%08s%04s%04x%04x%012s', 'UID:', $prefix, $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );
	    }
	    
	    function prefix($n=8) {
	    	$n = intval($n);
	    	if($n < 1 || $n > 100) { $n = 8; }
	    	
	    	$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9');
	    	$pass = '';
	    	for ($i = 0; $i < $n-4; $i++){
	    		$pass .= $chars[(rand() % count($chars))];
	    	}
	    	
	    	$tmp = microtime();
	    	$tmp = str_replace(' ', '', $tmp);
	    	$tmp = substr($tmp, 2, 4);
	    	
	    	return str_shuffle($tmp.$pass);
	    }
	}
?>