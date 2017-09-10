////////////////////////////////////////////////////////////////////////////////
//
//  © 2011 BlooDHounD
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
	 * Encodes and decodes binary data using MD5 (Message Digest) algorithm.
	 * 
	 * @see		http://www.faqs.org/rfcs/rfc1321.html	RFC
	 * 
	 * @author					BlooDHounD
	 * @version					3.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					07.03.2011 14:48:31
	 */
	public final class MD5 extends Process {

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
		 * Performs MD5 hash algorithm on a <code>String</code>.
		 *
		 * @param	str		The <code>String</code> to hash.
		 *
		 * @return			A <code>String</code> containing the hash value of <code>str</code>.
		 */
		public static function hash(str:String):String {

			if ( !str ) str = '';
			
			var bytes:ByteArray = new ByteArray();
			bytes.writeUTFBytes( str );

			return hashBytes( bytes );

		}

		/**
		 * Performs MD5 hash algorithm on a <code>ByteArray</code>.
		 *
		 * @param	bytes	The <code>ByteArray</code> data to hash.
		 *
		 * @return			A <code>String</code> containing the hash value of <code>bytes</code>.
		 */
		public static function hashBytes(bytes:ByteArray):String {

			var tmp:ByteArray = _DOMAIN.domainMemory;

			var mem:ByteArray = digest( bytes );
			
			mem.position = 16;
			mem.writeBytes( mem, 0, 16 );

			mem.position = 0;
			mem.writeUTFBytes( '0123456789abcdef' );
			
			mem.length = 16 + 16 + 16 * 2;
			
			if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
			
			_DOMAIN.domainMemory = mem;
			
			var k:int = 0;
			var i:int = 16;
			var j:int = 16 + 16 - 1;

			do {
				
				k = li8( i );
				si8( li8( k >>> 4 ), ++j );
				si8( li8( k & 0xF ), ++j );
				
			} while ( ++i < 16 + 16 );
			
			_DOMAIN.domainMemory = tmp;
			
			mem.position = 16 + 16;
			return mem.readUTFBytes( 16 * 2 );

		}

		
		/**
		 * Performs MD5 hash algorithm on a <code>ByteArray</code>.
		 * 
		 * @param	bytes	The <code>ByteArray</code> data to hash.
		 * 
		 * @return			A <code>ByteArray</code> containing the hash value of <code>bytes</code>.
		 */
		public static function digest(bytes:ByteArray):ByteArray {

			if ( !bytes ) bytes = new ByteArray();
			
			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			var pos:uint = bytes.position;
			var len:uint = bytes.length;
			
			// бинарники могут быть очень большими, и его копирование может быть
			// слишким дорогим. поэтому копируем только паизменяемую часть
			var padPos:uint = Math.max( 0, bytes.length - 64 );
			var pad:ByteArray = new ByteArray();
			pad.writeBytes( bytes, padPos );
			
			var k:int = len & 63;
			
			bytes.length = Math.max(
				len + ( k ? 128 - k : 64 ),
				ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH
			);
			
			_DOMAIN.domainMemory = bytes;
			
			var i:int = len << 3;
			var bytesLength:int = ( ( ( ( i + 64 ) >>> 9 ) << 4 ) + 15 ) << 2; // длинна для подсчёта в блоках
			
			si32( li32( ( i >>> 5 ) << 2 ) | 0x80 << ( i & 31 ), ( i >>> 5 ) << 2 );
			si32( i, bytesLength - 4 );

			var x0:int = 0;
			var x1:int = 0;
			var x2:int = 0;
			var x3:int = 0;
			var x4:int = 0;
			var x5:int = 0;
			var x6:int = 0;
			var x7:int = 0;
			var x8:int = 0;
			var x9:int = 0;
			var xA:int = 0;
			var xB:int = 0;
			var xC:int = 0;
			var xD:int = 0;
			var xE:int = 0;
			var xF:int = 0;
			
			var a:int =   1732584193;
			var b:int = -  271733879;
			var c:int = - 1732584194;
			var d:int =    271733878;
			
			var aa:int = 0;
			var bb:int = 0;
			var cc:int = 0;
			var dd:int = 0;
			
			i = 0;
			
			do {
				
				aa = a;
				bb = b;
				cc = c;
				dd = d;
				
				x0 = li32( i      );
				x1 = li32( i +  4 );
				x2 = li32( i +  8 );
				x3 = li32( i + 12 );
				x4 = li32( i + 16 );
				x5 = li32( i + 20 );
				x6 = li32( i + 24 );
				x7 = li32( i + 28 );
				x8 = li32( i + 32 );
				x9 = li32( i + 36 );
				xA = li32( i + 40 );
				xB = li32( i + 44 );
				xC = li32( i + 48 );
				xD = li32( i + 52 );
				xE = li32( i + 56 );
				xF = li32( i + 60 );
				
				// a = FF( a, b, c, d, x0, 7, -680876936 );
				a += ( b & c | ~b & d ) + x0 - 680876936;
				a = ( a << 7 | a >>> 25 ) + b;
				// d = FF( d, a, b, c, x1, 12, -389564586 );
				d += ( a & b | ~a & c ) + x1 - 389564586;
				d = ( d << 12 | d >>> 20 ) + a;
				// c = FF( c, d, a, b, x2, 17, 606105819 );
				c += ( d & a | ~d & b ) + x2 + 606105819;
				c = ( c << 17 | c >>> 15 ) + d;
				// b = FF( b, c, d, a, x3, 22, -1044525330 );
				b += ( c & d | ~c & a ) + x3 - 1044525330;
				b = ( b << 22 | b >>> 10 ) + c;
				// a = FF( a, b, c, d, x4, 7, -176418897 );
				a += ( b & c | ~b & d ) + x4 - 176418897;
				a = ( a << 7 | a >>> 25 ) + b;
				// d = FF( d, a, b, c, x5, 12, 1200080426 );
				d += ( a & b | ~a & c ) + x5 + 1200080426;
				d = ( d << 12 | d >>> 20 ) + a;
				// c = FF( c, d, a, b, x6, 17, -1473231341 );
				c += ( d & a | ~d & b ) + x6 - 1473231341;
				c = ( c << 17 | c >>> 15 ) + d;
				// b = FF( b, c, d, a, x7, 22, -45705983 );
				b += ( c & d | ~c & a ) + x7 - 45705983;
				b = ( b << 22 | b >>> 10 ) + c;
				// a = FF( a, b, c, d, x8, 7, 1770035416 );
				a += ( b & c | ~b & d ) + x8 + 1770035416;
				a = ( a << 7 | a >>> 25 ) + b;
				// d = FF( d, a, b, c, x9, 12, -1958414417 );
				d += ( a & b | ~a & c ) + x9 - 1958414417;
				d = ( d << 12 | d >>> 20 ) + a;
				// c = FF( c, d, a, b, xA, 17, -42063 );
				c += ( d & a | ~d & b ) + xA - 42063;
				c = ( c << 17 | c >>> 15 ) + d;
				// b = FF( b, c, d, a, xB, 22, -1990404162 );
				b += ( c & d | ~c & a ) + xB - 1990404162;
				b = ( b << 22 | b >>> 10 ) + c;
				// a = FF( a, b, c, d, xC, 7, 1804603682 );
				a += ( b & c | ~b & d ) + xC + 1804603682;
				a = ( a << 7 | a >>> 25 ) + b;
				// d = FF( d, a, b, c, xD, 12, -40341101 );
				d += ( a & b | ~a & c ) + xD - 40341101;
				d = ( d << 12 | d >>> 20 ) + a;
				// c = FF( c, d, a, b, xE, 17, -1502002290 );
				c += ( d & a | ~d & b ) + xE - 1502002290;
				c = ( c << 17 | c >>> 15 ) + d;
				// b = FF( b, c, d, a, xF, 22, 1236535329 );
				b += ( c & d | ~c & a ) + xF + 1236535329;
				b = ( b << 22 | b >>> 10 ) + c;
				
				// a = GG( a, b, c, d, x1, 5, -165796510 );
				a += ( b & d | c & ~d ) + x1 - 165796510;
				a = ( a << 5 | a >>> 27 ) + b;
				// d = GG( d, a, b, c, x6, 9, -1069501632 );
				d += ( a & c | b & ~c ) + x6 - 1069501632;
				d = ( d << 9 | d >>> 23 ) + a;
				// c = GG( c, d, a, b, xB, 14, 643717713 );
				c += ( d & b | a & ~b ) + xB + 643717713;
				c = ( c << 14 | c >>> 18 ) + d;
				// b = GG( b, c, d, a, x0, 20, -373897302 );
				b += ( c & a | d & ~a ) + x0 - 373897302;
				b = ( b << 20 | b >>> 12 ) + c;
				// a = GG( a, b, c, d, x5, 5, -701558691 );
				a += ( b & d | c & ~d ) + x5 - 701558691;
				a = ( a << 5 | a >>> 27 ) + b;
				// d = GG( d, a, b, c, xA, 9, 38016083 );
				d += ( a & c | b & ~c ) + xA + 38016083;
				d = ( d << 9 | d >>> 23 ) + a;
				// c = GG( c, d, a, b, xF, 14, -660478335 );
				c += ( d & b | a & ~b ) + xF - 660478335;
				c = ( c << 14 | c >>> 18 ) + d;
				// b = GG( b, c, d, a, x4, 20, -405537848 );
				b += ( c & a | d & ~a ) + x4 - 405537848;
				b = ( b << 20 | b >>> 12 ) + c;
				// a = GG( a, b, c, d, x9, 5, 568446438 );
				a += ( b & d | c & ~d ) + x9 + 568446438;
				a = ( a << 5 | a >>> 27 ) + b;
				// d = GG( d, a, b, c, xE, 9, -1019803690 );
				d += ( a & c | b & ~c ) + xE - 1019803690;
				d = ( d << 9 | d >>> 23 ) + a;
				// c = GG( c, d, a, b, x3, 14, -187363961 );
				c += ( d & b | a & ~b ) + x3 - 187363961;
				c = ( c << 14 | c >>> 18 ) + d;
				// b = GG( b, c, d, a, x8, 20, 1163531501 );
				b += ( c & a | d & ~a ) + x8 + 1163531501;
				b = ( b << 20 | b >>> 12 ) + c;
				// a = GG( a, b, c, d, xD, 5, -1444681467 );
				a += ( b & d | c & ~d ) + xD - 1444681467;
				a = ( a << 5 | a >>> 27 ) + b;
				// d = GG( d, a, b, c, x2, 9, 51403784 );
				d += ( a & c | b & ~c ) + x2 - 51403784;
				d = ( d << 9 | d >>> 23 ) + a;
				// c = GG( c, d, a, b, x7, 14, 1735328473 );
				c += ( d & b | a & ~b ) + x7 + 1735328473;
				c = ( c << 14 | c >>> 18 ) + d;
				// b = GG( b, c, d, a, xC, 20, -1926607734 );
				b += ( c & a | d & ~a ) + xC - 1926607734;
				b = ( b << 20 | b >>> 12 ) + c;
				
				// a = HH( a, b, c, d, x5, 4, -378558 );
				a += ( b ^ c ^ d ) + x5 - 378558;
				a = ( a << 4 | a >>> 28 ) + b;
				// d = HH( d, a, b, c, x8, 11, -2022574463 );
				d += ( a ^ b ^ c ) + x8 - 2022574463;
				d = ( d << 11 | d >>> 21 ) + a;
				// c = HH( c, d, a, b, xB, 16, 1839030562 );
				c += ( d ^ a ^ b ) + xB + 1839030562;
				c = ( c << 16 | c >>> 16 ) + d;
				// b = HH( b, c, d, a, xE, 23, -35309556 );
				b += ( c ^ d ^ a ) + xE - 35309556;
				b = ( b << 23 | b >>> 9 ) + c;
				// a = HH( a, b, c, d, x1, 4, -1530992060 );
				a += ( b ^ c ^ d ) + x1 - 1530992060;
				a = ( a << 4 | a >>> 28 ) + b;
				// d = HH( d, a, b, c, x4, 11, 1272893353 );
				d += ( a ^ b ^ c ) + x4 + 1272893353;
				d = ( d << 11 | d >>> 21 ) + a;
				// c = HH( c, d, a, b, x7, 16, -155497632 );
				c += ( d ^ a ^ b ) + x7 - 155497632;
				c = ( c << 16 | c >>> 16 ) + d;
				// b = HH( b, c, d, a, xA, 23, -1094730640 );
				b += ( c ^ d ^ a ) + xA - 1094730640;
				b = ( b << 23 | b >>> 9 ) + c;
				// a = HH( a, b, c, d, xD, 4, 681279174 );
				a += ( b ^ c ^ d ) + xD + 681279174;
				a = ( a << 4 | a >>> 28 ) + b;
				// d = HH( d, a, b, c, x0, 11, -358537222 );
				d += ( a ^ b ^ c ) + x0 - 358537222;
				d = ( d << 11 | d >>> 21 ) + a;
				// c = HH( c, d, a, b, x3, 16, -722521979 );
				c += ( d ^ a ^ b ) + x3 - 722521979;
				c = ( c << 16 | c >>> 16 ) + d;
				// b = HH( b, c, d, a, x6, 23, 76029189 );
				b += ( c ^ d ^ a ) + x6 + 76029189;
				b = ( b << 23 | b >>> 9 ) + c;
				// a = HH( a, b, c, d, x9, 4, -640364487 );
				a += ( b ^ c ^ d ) + x9 - 640364487;
				a = ( a << 4 | a >>> 28 ) + b;
				// d = HH( d, a, b, c, xC, 11, -421815835 );
				d += ( a ^ b ^ c ) + xC - 421815835;
				d = ( d << 11 | d >>> 21 ) + a;
				// c = HH( c, d, a, b, xF, 16, 530742520 );
				c += ( d ^ a ^ b ) + xF + 530742520;
				c = ( c << 16 | c >>> 16 ) + d;
				// b = HH( b, c, d, a, x2, 23, -995338651 );
				b += ( c ^ d ^ a ) + x2 - 995338651;
				b = ( b << 23 | b >>> 9 ) + c;

				// a = II( a, b, c, d, x0, 6, -198630844 );
				a += ( c ^ ( b | ~d ) ) + x0 - 198630844;
				a = ( a << 6 | a >>> 26 ) + b;
				// d = II( d, a, b, c, x7, 10, 1126891415 );
				d += ( b ^ ( a | ~c ) ) + x7 + 1126891415;
				d = ( d << 10 | d >>> 22 ) + a;
				// c = II( c, d, a, b, xE, 15, -1416354905 );
				c += ( a ^ ( d | ~b ) ) + xE - 1416354905;
				c = ( c << 15 | c >>> 17 ) + d;
				// b = II( b, c, d, a, x5, 21, -57434055 );
				b += ( d ^ ( c | ~a ) ) + x5 - 57434055;
				b = ( b << 21 | b >>> 11 ) + c;
				// a = II( a, b, c, d, xC, 6, 1700485571 );
				a += ( c ^ ( b | ~d ) ) + xC + 1700485571;
				a = ( a << 6 | a >>> 26 ) + b;
				// d = II( d, a, b, c, x3, 10, -1894986606 );
				d += ( b ^ ( a | ~c ) ) + x3 - 1894986606;
				d = ( d << 10 | d >>> 22 ) + a;
				// c = II( c, d, a, b, xA, 15, -1051523 );
				c += ( a ^ ( d | ~b ) ) + xA - 1051523;
				c = ( c << 15 | c >>> 17 ) + d;
				// b = II( b, c, d, a, x1, 21, -2054922799 );
				b += ( d ^ ( c | ~a ) ) + x1 - 2054922799;
				b = ( b << 21 | b >>> 11 ) + c;
				// a = II( a, b, c, d, x8, 6, 1873313359 );
				a += ( c ^ ( b | ~d ) ) + x8 + 1873313359;
				a = ( a << 6 | a >>> 26 ) + b;
				// d = II( d, a, b, c, xF, 10, -30611744 );
				d += ( b ^ ( a | ~c ) ) + xF - 30611744;
				d = ( d << 10 | d >>> 22 ) + a;
				// c = II( c, d, a, b, x6, 15, -1560198380 );
				c += ( a ^ ( d | ~b ) ) + x6 - 1560198380;
				c = ( c << 15 | c >>> 17 ) + d;
				// b = II( b, c, d, a, xD, 21, 1309151649 );
				b += ( d ^ ( c | ~a ) ) + xD + 1309151649;
				b = ( b << 21 | b >>> 11 ) + c;
				// a = II( a, b, c, d, x4, 6, -145523070 );
				a += ( c ^ ( b | ~d ) ) + x4 - 145523070;
				a = ( a << 6 | a >>> 26 ) + b;
				// d = II( d, a, b, c, xB, 10, -1120210379 );
				d += ( b ^ ( a | ~c ) ) + xB - 1120210379;
				d = ( d << 10 | d >>> 22 ) + a;
				// c = II( c, d, a, b, x2, 15, 718787259 );
				c += ( a ^ ( d | ~b ) ) + x2 + 718787259;
				c = ( c << 15 | c >>> 17 ) + d;
				// b = II( b, c, d, a, x9, 21, -343485551 );
				b += ( d ^ ( c | ~a ) ) + x9 - 343485551;
				b = ( b << 21 | b >>> 11 ) + c;
				
				a += aa;
				b += bb;
				c += cc;
				d += dd;
				
				i += 64;
				
			} while ( i < bytesLength );
			
			si32( a, len );
			si32( b, len + 4 );
			si32( c, len + 8 );
			si32( d, len + 12 );

			_DOMAIN.domainMemory = tmp;
			
			var result:ByteArray = new ByteArray();
			result.writeBytes( bytes, len, 16 );
			result.position = 0;
			
			bytes.position = padPos;
			bytes.writeBytes( pad );
			
			bytes.length = len;
			bytes.position = pos;
			
			return result;

		}

		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="MD5.swf", mimeType="application/octet-stream" )]
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
		 * Creates a MD5 object.
		 */
		public function MD5() {
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