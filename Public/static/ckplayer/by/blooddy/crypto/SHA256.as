////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto {

	import flash.utils.ByteArray;

	/**
	 * Encodes and decodes binary data using SHA-256 (Secure Hash Algorithm) algorithm.
	 * 
	 * @see		http://www.faqs.org/rfcs/rfc4634.html	RFC
	 * 
	 * @author					BlooDHounD
	 * @version					2.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					12.10.2010 18:00:37
	 */
	public final class SHA256 extends SHA2 {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static const _H:Vector.<int> = Vector.<int>( new <uint>[
			0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a,
			0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19
		] );
		
		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Performs SHA-256 hash algorithm on a <code>String</code>.
		 *
		 * @param	str		The <code>String</code> to hash.
		 *
		 * @return			A <code>String</code> containing the hash value of <code>str</code>.
		 */
		public static function hash(str:String):String {
			return $hash( str, _H );
		}
		
		/**
		 * Performs SHA-256 hash algorithm on a <code>ByteArray</code>.
		 *
		 * @param	bytes	The <code>ByteArray</code> data to hash.
		 *
		 * @return			A <code>String</code> containing the hash value of <code>bytes</code>.
		 */
		public static function hashBytes(bytes:ByteArray):String {
			return $hashBytes( bytes, _H );
		}
		
		/**
		 * Performs SHA-256 hash algorithm on a <code>ByteArray</code>.
		 *
		 * @param	bytes	The <code>ByteArray</code> data to hash.
		 *
		 * @return			A <code>ByteArray</code> containing the hash value of <code>bytes</code>.
		 */
		public static function digest(bytes:ByteArray):ByteArray {
			return $digest( bytes, _H );
		}
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Creates a SHA256 object.
		 */
		public function SHA256() {
			super();
		}
		
	}

}