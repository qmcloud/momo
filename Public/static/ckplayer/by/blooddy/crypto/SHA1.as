///////////////////////////////////////////////////////////////////////////////
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
	import avm2.intrinsics.memory.si8;
	
	import by.blooddy.crypto.process.Process;

	/**
	 * Encodes and decodes binary data using SHA-1 (Secure Hash Algorithm) algorithm.
	 * 
	 * @see		http://www.faqs.org/rfcs/rfc3174.html	RFC
	 * 
	 * @author					BlooDHounD
	 * @version					3.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					03.10.2010 21:07:00
	 */
	public final class SHA1 extends Process {

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
		
		/**
		 * Performs SHA-1 hash algorithm on a <code>String</code>.
		 *
		 * @param	str		The <code>String</code> to hash.
		 *
		 * @return			A <code>String</code> containing the hash value of <code>str</code>.
		 */
		public static function hash(str:String):String {
			
			if ( !str ) str = '';
			
			var bytes:ByteArray = new ByteArray( );
			bytes.writeUTFBytes( str );
			
			return hashBytes( bytes );
			
		}
		
		/**
		 * Performs SHA-1 hash algorithm on a <code>ByteArray</code>.
		 *
		 * @param	bytes	The <code>ByteArray</code> data to hash.
		 *
		 * @return			A <code>String</code> containing the hash value of <code>bytes</code>.
		 */
		public static function hashBytes(bytes:ByteArray):String {
			
			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			var mem:ByteArray = digest( bytes );

			mem.position = 16;
			mem.writeBytes( mem, 0, 20 );

			mem.position = 0;
			mem.writeUTFBytes( '0123456789abcdef' );
			
			mem.length = 16 + 20 + 20 * 2;
			
			if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
			
			_DOMAIN.domainMemory = mem;
			
			var k:int = 0;
			var i:int = 16;
			var j:int = 16 + 20 - 1;
			
			do {

				k = li8( i );
				si8( li8( k >>> 4 ), ++j );
				si8( li8( k & 0xF ), ++j );

			} while ( ++i < 16 + 20 );
			
			_DOMAIN.domainMemory = tmp;
			
			mem.position = 16 + 20;
			return mem.readUTFBytes( 20 * 2 );
			
		}
		
		/**
		 * Performs SHA-1 hash algorithm on a <code>ByteArray</code>.
		 *
		 * @param	bytes	The <code>ByteArray</code> data to hash.
		 *
		 * @return			A <code>ByteArray</code> containing the hash value of <code>bytes</code>.
		 */
		public static function digest(bytes:ByteArray):ByteArray {

			if ( !bytes ) bytes = new ByteArray();
			
			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			var i:int = bytes.length << 3;
			var bytesLength:int = 80 * 4 + ( ( ( ( ( i+ 64 ) >>> 9 ) << 4 ) + 15 ) << 2 );
			
			var mem:ByteArray = new ByteArray();
			mem.length = bytesLength + 4
			mem.position = 80 * 4;
			mem.writeBytes( bytes );
				
			if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
			
			_DOMAIN.domainMemory = mem;
			
			si32( li32( 80 * 4 + ( ( i >>> 5 ) << 2 ) ) | ( 0x80 << ( i % 32 ) ), 80 * 4 + ( ( i >>> 5 ) << 2 ) );

			si8( i >> 24, bytesLength     );
			si8( i >> 16, bytesLength + 1 );
			si8( i >>  8, bytesLength + 2 );
			si8( i      , bytesLength + 3 );

			var h0:int =  1732584193; //0x67452301;
			var h1:int = - 271733879; //0xEFCDAB89;
			var h2:int = -1732584194; //0x98BADCFE;
			var h3:int =   271733878; //0x10325476;
			var h4:int = -1009589776; //0xC3D2E1F0;

			var a:int = 0;
			var b:int = 0;
			var c:int = 0;
			var d:int = 0;
			var e:int = 0;
			var w:int = 0;
			
			var t:int = 0;
			
			i = 80 * 4;
			do {
				
				a = h0;
				b = h1;
				c = h2;
				d = h3;
				e = h4;
				
				t = 0;
				
				// phase( a, b, c, d, e, i, t, 16 );
				do {
					
					w =	( li8( i + t     ) << 24 ) |
						( li8( i + t + 1 ) << 16 ) |
						( li8( i + t + 2 ) <<  8 ) |
						  li8( i + t + 3 )         ;

					si32( w, t );

					w += 0x5A827999 + e + ( ( a << 5 ) | ( a >>> 27 ) ) + ( ( b & c ) | ( ~b & d ) );
					
					e = d;
					d = c;
					c = ( b << 30 ) | ( b >>> 2 );
					b = a;
					a = w;
					
					t += 4;
					
				} while ( t < 16 * 4 );

				// phase( a, b, c, d, e, i, t, 20 );
				do {
					
					w = li32( t -  3 * 4 ) ^
						li32( t -  8 * 4 ) ^
						li32( t - 14 * 4 ) ^
						li32( t - 16 * 4 ) ;

					w = ( w << 1 ) | ( w >>> ( 32 - 1 ) );
					
					si32( w, t );
					
					w += 0x5A827999 + e + ( ( a << 5 ) | ( a >>> 27 ) ) + ( ( b & c ) | ( ~b & d ) );
					
					e = d;
					d = c;
					c = ( b << 30 ) | ( b >>> 2 );
					b = a;
					a = w;
					
					t += 4;
					
				} while ( t < 20 * 4 );
				
				// phase( a, b, c, d, e, i, t, 40 );
				do {
					
					w = li32( t -  3 * 4 ) ^
						li32( t -  8 * 4 ) ^
						li32( t - 14 * 4 ) ^
						li32( t - 16 * 4 ) ;

					w = ( w << 1 ) | ( w >>> ( 32 - 1 ) );

					si32( w, t );
					
					w += 0x6ED9EBA1 + e + ( ( a << 5 ) | ( a >>> 27 ) ) + ( b ^ c ^ d );
					
					e = d;
					d = c;
					c = ( b << 30 ) | ( b >>> 2 );
					b = a;
					a = w;
					
					t += 4;
					
				} while ( t < 40 * 4 );
				
				// phase( a, b, c, d, e, i, t, 60 );
				do {
					
					w = li32( t -  3 * 4 ) ^
						li32( t -  8 * 4 ) ^
						li32( t - 14 * 4 ) ^
						li32( t - 16 * 4 ) ;

					w = ( w << 1 ) | ( w >>> ( 32 - 1 ) );

					si32( w, t );
					
					w += 0x8F1BBCDC + e + ( ( a << 5 ) | ( a >>> 27 ) ) + ( ( b & c ) | ( b & d ) | ( c & d ) );
					
					e = d;
					d = c;
					c = ( b << 30 ) | ( b >>> 2 );
					b = a;
					a = w;
					
					t += 4;
					
				} while ( t < 60 * 4 );
				
				// phase( a, b, c, d, e, i, t, 80 );
				do {
					
					w = li32( t -  3 * 4 ) ^
						li32( t -  8 * 4 ) ^
						li32( t - 14 * 4 ) ^
						li32( t - 16 * 4 ) ;

					w = ( w << 1 ) | ( w >>> ( 32 - 1 ) );

					si32( w, t );
					
					w += 0xCA62C1D6 + e + ( ( a << 5 ) | ( a >>> 27 ) ) + ( b ^ c ^ d );
					
					e = d;
					d = c;
					c = ( b << 30 ) | ( b >>> 2 );
					b = a;
					a = w;
					
					t += 4;
					
				} while ( t < 80 * 4 );
				
				h0 += a;
				h1 += b;
				h2 += c;
				h3 += d;
				h4 += e;
				
				i += 16 * 4;
				
			} while ( i < bytesLength );
			
			si8( h0 >> 24,  0 );
			si8( h0 >> 16,  1 );
			si8( h0 >>  8,  2 );
			si8( h0      ,  3 );

			si8( h1 >> 24,  4 );
			si8( h1 >> 16,  5 );
			si8( h1 >>  8,  6 );
			si8( h1      ,  7 );
			
			si8( h2 >> 24,  8 );
			si8( h2 >> 16,  9 );
			si8( h2 >>  8, 10 );
			si8( h2      , 11 );
			
			si8( h3 >> 24, 12 );
			si8( h3 >> 16, 13 );
			si8( h3 >>  8, 14 );
			si8( h3      , 15 );
			
			si8( h4 >> 24, 16 );
			si8( h4 >> 16, 17 );
			si8( h4 >>  8, 18 );
			si8( h4      , 19 );
			
			_DOMAIN.domainMemory = tmp;

			mem.position = 0;
			mem.length = 5 * 4;
			return mem;
			
		}
		
		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="SHA1.swf", mimeType="application/octet-stream" )]
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
		 * Creates a SHA1 object.
		 */
		public function SHA1() {
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
		public function hash(str:String):void {
			super.call( 'hash', str );
		}
		
		/**
		 * @inheritDoc
		 */
		public function hashBytes(bytes:ByteArray):void {
			super.call( 'hashBytes', bytes );
		}
		
		/**
		 * @inheritDoc
		 */
		public function digest(bytes:ByteArray):void {
			super.call( 'digest', bytes );
		}
		
		}
		
	}

}