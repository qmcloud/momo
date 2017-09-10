////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto {

	import flash.system.ApplicationDomain;
	import flash.utils.ByteArray;
	
	import avm2.intrinsics.memory.li32;
	import avm2.intrinsics.memory.li8;
	import avm2.intrinsics.memory.si32;
	
	import by.blooddy.crypto.process.Process;

	/**
	 * Generates a CRC-32 (Cyclic Redundancy Check) checksum.
	 * 
	 * @see		http://www.faqs.org/rfcs/rfc1952.html	RFC
	 *
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					28.01.2011 20:07:33
	 */
	public final class CRC32 extends Process {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
		
		/**
		 * @private
		 */
		private static const _TABLE:ByteArray = ( function():ByteArray {
		
			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			var mem:ByteArray = new ByteArray();
			mem.length = Math.max( 1024, ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH );
			
			_DOMAIN.domainMemory = mem;
			
			var c:int = 0;
			var j:int = 0;
			var i:int = 0;
			for ( i=0; i<256; ++i ) {
				c = i;
				for ( j=0; j<8; ++j ) {
					if ( ( c & 1 ) == 1 ) {
						c = 0xEDB88320 ^ ( c >>> 1 );
					} else {
						c >>>= 1;
					}
				}
				si32( c, i << 2 );
			}
			
			_DOMAIN.domainMemory = tmp;
			
			mem.length = 1024;
			
			return mem;

		}() );

		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------

		[Deprecated( replacement="hashBytes" )]
		/**
		 * Generates a polinominal code checksum represented as unsigned integer.
		 *
		 * @param	bytes	The data to be hashed.
		 *
		 * @return			The resluting checksum.
		 */
		public static function hash(bytes:ByteArray):uint {
			return hashBytes( bytes );
		}
		
		/**
		 * Generates a polinominal code checksum represented as unsigned integer.
		 *
		 * @param	bytes	The data to be hashed.
		 *
		 * @return			The resluting checksum.
		 */
		public static function hashBytes(bytes:ByteArray):uint {
			if ( bytes && bytes.length > 0 ) {

				var len:uint = bytes.length + 1024;

				var tmp:ByteArray = _DOMAIN.domainMemory;

				var mem:ByteArray = new ByteArray();
				mem.writeBytes( _TABLE );
				mem.writeBytes( bytes );

				if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
				_DOMAIN.domainMemory = mem;

				var c:int = -1;
				var i:int = 1024;
				do {
					c = li32( ( ( ( c ^ li8( i ) ) & 0xFF ) << 2 ) ) ^ ( c >>> 8 );
				} while ( ++i < len );

				_DOMAIN.domainMemory = tmp;

				return c ^ 0xFFFFFFFF;

			} else {

				return 0;

			}
		}

		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="CRC32.swf", mimeType="application/octet-stream" )]
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
		 * Creates a CRC32 object.
		 */
		public function CRC32() {
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