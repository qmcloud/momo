////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto {

	import flash.system.ApplicationDomain;
	import flash.utils.ByteArray;
	
	import avm2.intrinsics.memory.li8;
	
	import by.blooddy.crypto.process.Process;

	/**
	 * Generates a Adler-32 checksum.
	 * 
	 * @see		http://www.faqs.org/rfcs/rfc1950.html	RFC
	 *
	 * @author					BlooDHounD
	 * @version					2.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 */
	public final class Adler32 extends Process {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
		
		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------
		
		[Deprecated( replacement="hashBytes" )]
		/**
		 * Generates a polinominal code checksum represented as unsigned integer.
		 *
		 * @param	bytes	The <code>ByteArray</code> to be hashed.
		 *
		 * @return			The resluting checksum.
		 */
		public static function hash(bytes:ByteArray):uint {
			return hashBytes( bytes );
		}
		
		/**
		 * Generates a polinominal code checksum represented as unsigned integer.
		 *
		 * @param	bytes	The <code>ByteArray</code> to be hashed.
		 *
		 * @return			The resluting checksum.
		 */
		public static function hashBytes(bytes:ByteArray):uint {
			if ( bytes && bytes.length > 0 ) {

				var len:uint = bytes.length;

				var tmp:ByteArray = _DOMAIN.domainMemory;

				if ( len < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) bytes.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;

				_DOMAIN.domainMemory = bytes;
				
				var i:int = 0;
				var a:int = 1;
				var b:int = 0;

				var tlen:int = 0;

				do
				{
					tlen = i + 5552;
					if ( tlen > len ) tlen = len;

					do {
						a = a + li8( i );
						b = b + a;
						i++;
					} while ( i < tlen );
					
					a = a % 65521;
					b = b % 65521;

				} while ( i < len );
				
				_DOMAIN.domainMemory = tmp;

				bytes.length = len;
				
				return ( b << 16 ) | a;

			} else {

				return 1;

			}
		}
		
		CRYPTO::worker {
			
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="Adler32.swf", mimeType="application/octet-stream" )]
		/**
		 * @private
		 */
		private static const WorkerClass:Class;
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Creates Adler32 object.
		 */
		public function Adler32() {
			super( WorkerClass );
		}
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @inheritDoc
		 */
		public function hashBytes(bytes:ByteArray):void {
			super.call( 'hashBytes', bytes );
		}

		}
		
	}

}