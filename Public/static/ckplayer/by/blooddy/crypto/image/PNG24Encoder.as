////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image {

	import flash.display.BitmapData;
	import flash.display.PNGEncoderOptions;
	import flash.utils.ByteArray;

	/**
	 * Encodes image data using PNG-24 compression algorithm.
	 *
	 * @see https://www.w3.org/Graphics/PNG/	W3C
	 * 
	 * @author					BlooDHounD
	 * @version					2.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 */
	public final class PNG24Encoder extends PNGEncoder {

		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------

		/**
		 * Creates a PNG image from the specified <code>BitmapData</code>.
		 * 
		 * Used <code>flash.display.BitmapData.encode</code>, if possible.
		 *
		 * @param	image			The <code>BitmapData</code> to be converted to PNG format.
		 *
		 * @param	filter			The encoding algorithm to use when processing the image.
		 * 							Use the constants provided in
		 * 							<code>by.blooddy.crypto.image.PNGFilter</code> class.
		 *
		 * @return					A <code>ByteArray</code> containing the PNG encoded image data.
		 *
		 * @throws	ArgumentError	No such filter.
		 *
		 * @see						by.blooddy.crypto.image.PNGFilter
		 * @see						flash.display.BitmapData#encode()
		 */
		public static function encode(image:BitmapData, filter:uint=0):ByteArray {

			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );
			if ( filter < 0 || filter > 4 ) Error.throwError( ArgumentError, 2008, 'filter' );

			if ( _NATIVE ) {
				return image.encode( image.rect, new PNGEncoderOptions( filter == PNGFilter.NONE ) );
			} else {
				return $encode( image, filter );
			}

		}

		/**
		 * Creates a PNG-encoded byte sequence from the specified <code>ByteArray</code>.
		 * 
		 * @param	bytes			The <code>ByteArray</code> to be encoded.
		 * @param	width			Image width.
		 * @param	height			Image height.
		 * 
		 * @param	filter			The encoding algorithm you wish to apply while encoding.
		 * 							Use the constants provided in 
		 * 							<code>by.blooddy.crypto.image.PNGFilter</code> class.
		 * 
		 * @return					The sequence of bytes containing the encoded image.
		 * 
		 * @throws	ArgumentError	No such filter.
		 * 
		 * @see						by.blooddy.crypto.image.PNGFilter
		 */
		public static function encodeBytes(bytes:ByteArray, width:uint, height:uint, filter:uint=0):ByteArray {
			
			if ( bytes == null ) Error.throwError( TypeError, 2007, 'bytes' );
			if ( filter < 0 || filter > 4 ) Error.throwError( ArgumentError, 2008, 'filter' );
			
			var image:BitmapData = new BitmapData( width, height, false );
			
			var pos:uint = bytes.position;
			bytes.position = 0;
			image.setPixels( image.rect, bytes );
			bytes.position = pos;
			
			return encode( image, filter );
			
		}
		
		//--------------------------------------------------------------------------
		//
		//  Private class methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static function $encode(image:BitmapData, filter:uint):ByteArray {
			
			var transparent:Boolean = isTransparentImage( image );
			
			// create output byte array
			var result:ByteArray = new ByteArray();
			
			// PNG signature
			writeSignature( result );
			
			// IHDR
			writeIHDR( result, image.width, image.height, 0x08, ( transparent ? 0x06 : 0x02 ) );

			// IDAT
			writeIDAT( result, PNG24Encoder$.encode( image, filter, transparent ) );
			
			// tEXt
			//writeTEXT( result, 'Software', 'by.blooddy.crypto.image.PNG24Encoder' );

			// IEND
			writeIEND( result );

			_TMP.clear();
			
			return result;
			
		}
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Creates a PNG24Encoder object.
		 */
		public function PNG24Encoder() {
			super();
		}
		
	}

}

import flash.display.BitmapData;
import flash.system.ApplicationDomain;
import flash.utils.ByteArray;

import avm2.intrinsics.memory.li8;
import avm2.intrinsics.memory.si32;
import avm2.intrinsics.memory.si8;

/**
 * @private
 */
internal final class PNG24Encoder$ {
	
	//--------------------------------------------------------------------------
	//
	//  Encode
	//
	//--------------------------------------------------------------------------
	
	internal static function encode(image:BitmapData, filter:uint, transparent:Boolean):ByteArray {
		
		var tmp:ByteArray = _DOMAIN.domainMemory;
		
		var mem:ByteArray = new ByteArray();
		mem.length = Math.max(
			( image.width * image.height ) * ( transparent ? 4 : 3 ) + image.height + image.width * 4,
			ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH
		);

		_DOMAIN.domainMemory = mem;
		
		_ENCODERS[ int( transparent ) ][ filter ]( image );
		
		_DOMAIN.domainMemory = tmp;

		return mem;
		
	}
	
	//--------------------------------------------------------------------------
	//  encode main methods
	//--------------------------------------------------------------------------
	
	private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
	
	private static const _ENCODERS:Vector.<Vector.<Function>> = new <Vector.<Function>>[
		new <Function>[ encodeNone,            encodeSub,            encodeUp,            encodeAverage,            encodePaeth            ],
		new <Function>[ encodeNoneTransparent, encodeSubTransparent, encodeUpTransparent, encodeAverageTransparent, encodePaethTransparent ]
	];
	
	private static function encodeNoneTransparent(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		
		if ( width >= 64 ) { // для широких картинок быстрее копировать целиком ряды байтов
			
			var bmp:ByteArray = image.getPixels( image.rect );
			var mem:ByteArray = _DOMAIN.domainMemory;
			
			width <<= 2;
			do {

				mem.writeBytes( bmp, y * width, width );
				mem.writeByte( 0 ); // NONE

				i = x + width;
				do {
					si8( li8( i - 4 ), i );
					i -= 4;
				} while ( i > x );
				
				x += width + 1;

			} while ( ++y < height );
			
		} else {
			
			var c:uint = 0;
			
			do {
				si8( 0, i++ ); // NONE
				x = 0;
				do {
					c = image.getPixel32( x, y );
					si8( c >> 16, i++ );
					si8( c >>  8, i++ );
					si8( c      , i++ );
					si8( c >> 24, i++ );
				} while ( ++x < width );
			} while ( ++y < height );
			
		}
		
	}

	private static function encodeSubTransparent(image:BitmapData):void {

		var width:int = image.width;
		var height:int = image.height;
		
		var a:int = 0;
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;

		var a0:int = 0;
		var r0:int = 0;
		var g0:int = 0;
		var b0:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		
		do {

			si8( 1, i++ ); // SUB

			a0 = 0;
			r0 = 0;
			g0 = 0;
			b0 = 0;

			x = 0;
			do {
				
				b = image.getPixel32( x, y );
				
				r = b >>> 16;
				si8( r - r0, i++ );
				r0 = r;
				
				g = b >>>  8;
				si8( g - g0, i++ );
				g0 = g;
				
				si8( b - b0, i++ );
				b0 = b;
				
				a = b >>> 24;
				si8( a - a0, i++ );
				a0 = a;
				
			} while ( ++x < width );

		} while ( ++y < height );

	}

	private static function encodeUpTransparent(image:BitmapData):void {

		var width:int = image.width;
		var height:int = image.height;
		
		var len:int = ( width * height ) * 4 + height;

		var c:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		var j:int = 0;
		
		do {

			si8( 2, i++ ); // UP

			j = len;

			x = 0;
			do {
				c = image.getPixel32( x, y );
				si8( ( c >>> 16 ) - li8( j + 2 ), i++ );
				si8( ( c >>>  8 ) - li8( j + 1 ), i++ );
				si8(   c          - li8( j     ), i++ );
				si8( ( c >>> 24 ) - li8( j + 3 ), i++ );
				si32( c, j );
				j += 4;
			} while ( ++x < width );

		} while ( ++y < height );

	}	

	private static function encodeAverageTransparent(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var len:int = ( width * height ) * 4 + height;

		var c:int = 0;
		
		var a:int = 0;
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;
		
		var a0:int = 0;
		var r0:int = 0;
		var g0:int = 0;
		var b0:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		var j:int = 0;
		
		do {

			si8( 3, i++ ); // AVERAGE
			
			j = len;

			a0 = 0;
			r0 = 0;
			g0 = 0;
			b0 = 0;

			x = 0;
			do {
				
				c = image.getPixel32( x, y );
				
				r = ( c >> 16 ) & 0xFF;
				si8( r - ( ( r0 + li8( j + 2 ) ) >>> 1 ), i++ );
				r0 = r;
				
				g = ( c >>  8 ) & 0xFF;
				si8( g - ( ( g0 + li8( j + 1 ) ) >>> 1 ), i++ );
				g0 = g;
				
				b = ( c       ) & 0xFF;
				si8( b - ( ( b0 + li8( j ) ) >>> 1 ), i++ );
				b0 = b;
				
				a =   c >>> 24;
				si8( a - ( ( a0 + li8( j + 3 ) ) >>> 1 ), i++ );
				a0 = a;
				
				si32( c, j );
				j += 4;
				
			} while ( ++x < width );

		} while ( ++y < height );

	}

	private static function encodePaethTransparent(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var len:int = ( width * height ) * 4 + height;
		
		var c:int = 0;
		
		var a:int = 0;
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;
		
		var a0:int = 0;
		var r0:int = 0;
		var g0:int = 0;
		var b0:int = 0;
		
		var a1:int = 0;
		var r1:int = 0;
		var g1:int = 0;
		var b1:int = 0;
		
		var a2:int = 0;
		var r2:int = 0;
		var g2:int = 0;
		var b2:int = 0;

		var p:int = 0;
		var pa:int = 0;
		var pb:int = 0;
		var pc:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		var j:int = 0;
		
		do {
			
			si8( 4, i++ ); // PAETH

			j = len;

			a0 = 0;
			a2 = 0;
			r0 = 0;
			r2 = 0;
			g0 = 0;
			g2 = 0;
			b0 = 0;
			b2 = 0;

			x = 0;
			do {
				
				c = image.getPixel32( x, y );
				
				r = ( c >> 16 ) & 0xFF;
				r1 = li8( j + 2 );
				p = r0 + r1 - r2;
				pa = p - r0; if ( pa < 0 ) pa = -pa;
				pb = p - r1; if ( pb < 0 ) pb = -pb;
				pc = p - r2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = r0;
				else if ( pb <= pc )		p = r1;
				else						p = r2;
				si8( r - p, i++ );
				r0 = r;
				r2 = r1;
				
				g = ( c >> 8 ) & 0xFF;
				g1 = li8( j + 1 );
				p = g0 + g1 - g2;
				pa = p - g0; if ( pa < 0 ) pa = -pa;
				pb = p - g1; if ( pb < 0 ) pb = -pb;
				pc = p - g2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = g0;
				else if ( pb <= pc )		p = g1;
				else						p = g2;
				si8( g - p, i++ );
				g0 = g;
				g2 = g1;
				
				b = c & 0xFF;
				b1 = li8( j     );
				p = b0 + b1 - b2;
				pa = p - b0; if ( pa < 0 ) pa = -pa;
				pb = p - b1; if ( pb < 0 ) pb = -pb;
				pc = p - b2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = b0;
				else if ( pb <= pc )		p = b1;
				else						p = b2;
				si8( b - p, i++ );
				b0 = b;
				b2 = b1;
				
				a = c >>> 24;
				a1 = li8( j + 3 );
				p = a0 + a1 - a2;
				pa = p - a0; if ( pa < 0 ) pa = -pa;
				pb = p - a1; if ( pb < 0 ) pb = -pb;
				pc = p - a2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = a0;
				else if ( pb <= pc )		p = a1;
				else						p = a2;
				si8( a - p, i++ );
				a0 = a;
				a2 = a1;
				
				si32( c, j );
				j += 4;
				
			} while ( ++x < width );

		} while ( ++y < height );
		
	}

	private static function encodeNone(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		
		var c:uint = 0;
		
		do {
			si8( 0, i++ ); // NONE
			x = 0;
			do {
				c = image.getPixel( x, y );
				si8( c >> 16, i++ );
				si8( c >>  8, i++ );
				si8( c      , i++ );
			} while ( ++x < width );
		} while ( ++y < height );
			
	}
	
	private static function encodeSub(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;
		
		var r0:int = 0;
		var g0:int = 0;
		var b0:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		
		do {
			
			si8( 1, i++ ); // SUB
			
			r0 = 0;
			g0 = 0;
			b0 = 0;
			
			x = 0;
			do {
				
				b = image.getPixel( x, y );
				
				r = b >>> 16;
				si8( r - r0, i++ );
				r0 = r;
				
				g = b >>>  8;
				si8( g - g0, i++ );
				g0 = g;
				
				si8( b - b0, i++ );
				b0 = b;
				
			} while ( ++x < width );
			
		} while ( ++y < height );
		
	}
	
	private static function encodeUp(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var len:int = ( width * height ) * 3 + height;
		
		var c:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		var j:int = 0;
		
		do {
			
			si8( 2, i++ ); // UP
			
			j = len;
			
			x = 0;
			do {
				c = image.getPixel( x, y );
				si8( ( c >>> 16 ) - li8( j + 2 ), i++ );
				si8( ( c >>>  8 ) - li8( j + 1 ), i++ );
				si8(   c          - li8( j     ), i++ );
				si32( c, j );
				j += 4;
			} while ( ++x < width );
			
		} while ( ++y < height );
		
	}	
	
	private static function encodeAverage(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var len:int = ( width * height ) * 3 + height;
		
		var c:int = 0;
		
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;
		
		var r0:int = 0;
		var g0:int = 0;
		var b0:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		var j:int = 0;
		
		do {
			
			si8( 3, i++ ); // AVERAGE
			
			j = len;
			
			r0 = 0;
			g0 = 0;
			b0 = 0;
			
			x = 0;
			do {
				
				c = image.getPixel( x, y );
				
				r = c >>> 16;
				si8( r - ( ( r0 + li8( j + 2 ) ) >>> 1 ), i++ );
				r0 = r;
				
				g = ( c >>  8 ) & 0xFF;
				si8( g - ( ( g0 + li8( j + 1 ) ) >>> 1 ), i++ );
				g0 = g;
				
				b = ( c       ) & 0xFF;
				si8( b - ( ( b0 + li8( j ) ) >>> 1 ), i++ );
				b0 = b;
				
				si32( c, j );
				j += 4;
				
			} while ( ++x < width );
			
		} while ( ++y < height );
		
	}
	
	private static function encodePaeth(image:BitmapData):void {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var len:int = ( width * height ) * 3 + height;
		
		var c:int = 0;
		
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;
		
		var r0:int = 0;
		var g0:int = 0;
		var b0:int = 0;
		
		var r1:int = 0;
		var g1:int = 0;
		var b1:int = 0;
		
		var r2:int = 0;
		var g2:int = 0;
		var b2:int = 0;
		
		var p:int = 0;
		var pa:int = 0;
		var pb:int = 0;
		var pc:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var i:int = 0;
		var j:int = 0;
		
		do {
			
			si8( 4, i++ ); // PAETH
			
			j = len;
			
			r0 = 0;
			r2 = 0;
			g0 = 0;
			g2 = 0;
			b0 = 0;
			b2 = 0;
			
			x = 0;
			do {
				
				c = image.getPixel( x, y );
				
				r = c >>> 16;
				r1 = li8( j + 2 );
				p = r0 + r1 - r2;
				pa = p - r0; if ( pa < 0 ) pa = -pa;
				pb = p - r1; if ( pb < 0 ) pb = -pb;
				pc = p - r2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = r0;
				else if ( pb <= pc )		p = r1;
				else						p = r2;
				si8( r - p, i++ );
				r0 = r;
				r2 = r1;
				
				g = ( c >> 8 ) & 0xFF;
				g1 = li8( j + 1 );
				p = g0 + g1 - g2;
				pa = p - g0; if ( pa < 0 ) pa = -pa;
				pb = p - g1; if ( pb < 0 ) pb = -pb;
				pc = p - g2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = g0;
				else if ( pb <= pc )		p = g1;
				else						p = g2;
				si8( g - p, i++ );
				g0 = g;
				g2 = g1;
				
				b = c & 0xFF;
				b1 = li8( j     );
				p = b0 + b1 - b2;
				pa = p - b0; if ( pa < 0 ) pa = -pa;
				pb = p - b1; if ( pb < 0 ) pb = -pb;
				pc = p - b2; if ( pc < 0 ) pc = -pc;
				if ( pa <= pb && pa <= pc ) p = b0;
				else if ( pb <= pc )		p = b1;
				else						p = b2;
				si8( b - p, i++ );
				b0 = b;
				b2 = b1;
				
				si32( c, j );
				j += 4;
				
			} while ( ++x < width );
			
		} while ( ++y < height );
		
	}
	
}