////////////////////////////////////////////////////////////////////////////////
//
//  (C) 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image.palette {

	import flash.display.BitmapData;
	import flash.system.ApplicationDomain;
	import flash.utils.ByteArray;
	
	import avm2.intrinsics.memory.li32;
	import avm2.intrinsics.memory.li8;
	import avm2.intrinsics.memory.si32;
	import avm2.intrinsics.memory.si8;
	
	import by.blooddy.crypto.image.isTransparentImage;

	/**
	 * This class provides a palette that can be used in <code>PNGEncoder</code>.
	 * 
	 * @see						http://en.wikipedia.org/wiki/Color_quantization
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					04.07.2010 1:48:54
	 */
	public class MedianCutPalette implements IPalette {

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
		//  Constructor
		//
		//--------------------------------------------------------------------------

		/**
		 * Creates a MedianCutPalette object.
		 * 
		 * @param	image		The source image to use when creating the palette.
		 * 
		 * @param	maxColors	The maximum number of collors to be stored inside
		 * 						the palette. The possible range is from 2 to 256 inclusive.
		 * 
		 * @throws	TypeError	The <code>image</code> parameter must 
		 * 						not be <code>null</code>.
		 * 
		 * @throws	RangeError	The number of colors is out of bounds.
		 */
		public function MedianCutPalette(image:BitmapData, maxColors:uint=256) {

			super();

			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );
			if ( maxColors < 2 || maxColors > 256 ) Error.throwError( RangeError, 2006 );

			var transparent:Boolean = isTransparentImage( image );
			var colorCount:int = 0;
			
			function writeBlock(
				minA:uint, minR:uint, minG:uint, minB:uint,
				maxA:uint, maxR:uint, maxG:uint, maxB:uint,
				colorPos:uint,
				colorLen:uint
			):void {
				
				var midA:uint = transparent ? ( ( maxA + minA ) / 2 ) & 0xFF000000 : 0xFF000000;
				var midR:uint = 0;
				var midG:uint = 0;
				var midB:uint = 0;
				var count:uint = 0;
				var mid:uint = 0;
				var mask:uint = 0;
				if ( midA > 0 ) {
					midR = ( ( maxR + minR ) >>> 1 ) & 0xFF0000;
					midG = ( ( maxG + minG ) >>> 1 ) & 0xFF00;
					midB = ( ( maxB + minB ) >>> 1 ) & 0xFF;
					var t:uint = maxB - minB;
					if ( t > count ) {
						count = t;
						mid = midB;
						mask = 0x000000FF;
					}
					t = ( maxG - minG ) >>> 8;
					if ( t > count ) {
						count = t;
						mid = midG;
						mask = 0x0000FF00;
					}
					t = ( maxR - minR ) >>> 16;
					if ( t > count ) {
						count = t;
						mid = midR;
						mask = 0x00FF0000;
					}
					if ( transparent ) {
						t = ( maxA - minA ) >>> 24;
						if ( t > count ) {
							count = t;
							mid = midA;
							mask = 0xFF000000;
						}
					}
				}
				
				var i:uint = 0;
				var l:uint = colorCount * 25;
				while ( i < l ) {
					if ( count < li8( i ) ) {
						mem.position = i + 25;
						mem.writeBytes( mem, i, l - i );
						break;
					}
					i += 25;
				};
				
				si8(   count                    , i      );
				si32(  mask                     , i +  1 );
				si32(  midA | midR | midG | midB, i +  5 );
				si32(  minA | minR | minG | minB, i +  9 );
				si32(  maxA | maxR | maxG | maxB, i + 13 );
				si32(  colorPos                 , i + 17 );
				si32(  colorLen                 , i + 21 );
				
				++colorCount;
				
			}
			
			var width:int = image.width;
			var height:int = image.height;
			
			var tmp:ByteArray = _DOMAIN.domainMemory;

			var len:int = ( width * height ) << 2;
			var z:uint = 25 * maxColors;
			
			var mem:ByteArray = new ByteArray();
			mem.length = Math.max( z + len, ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH );
			
			_DOMAIN.domainMemory = mem;
			
			var x:int = 0;
			var y:int = 0;

			var c:uint = 0;
			var cx:uint = ~( transparent ? image.getPixel32( 0, 0 ) : image.getPixel( 0, 0 ) );

			var t:uint = 0;
			
			var lminA:uint = 0xFF000000;
			var lmaxA:uint = 0x00000000;
			var lminR:uint = 0x00FF0000;
			var lmaxR:uint = 0x00000000;
			var lminG:uint = 0x0000FF00;
			var lmaxG:uint = 0x00000000;
			var lminB:uint = 0x000000FF;
			var lmaxB:uint = 0x00000000;
			
			var i:uint = z;
			do {
				x = 0;
				do {
					
					c = image.getPixel32( x, y );
					if ( c == cx ) continue;
					cx = c;
					
					if ( transparent ) {
						t = c & 0xFF000000;
						if ( t < lminA ) lminA = t;
						if ( t > lmaxA ) lmaxA = t;
					}
					
					t = c & 0x00FF0000;
					if ( t < lminR ) lminR = t;
					if ( t > lmaxR ) lmaxR = t;
					
					t = c & 0x0000FF00;
					if ( t < lminG ) lminG = t;
					if ( t > lmaxG ) lmaxG = t;
					
					t = c & 0x000000FF;
					if ( t < lminB ) lminB = t;
					if ( t > lmaxB ) lmaxB = t;
					
					si32( c, i );
					i += 4;
					
				} while ( ++x < width );
			} while ( ++y < height );

			writeBlock(
				lminA, lminR, lminG, lminB,
				lmaxA, lmaxR, lmaxG, lmaxB,
				z, i - z
			);
			
			z = i;
			
			if ( li8( 0 ) > 0 ) {
				
				var z0:uint = 0;
				var v:uint = 0;
				
				var rminA:uint = 0xFF000000;
				var rmaxA:uint = 0x00000000;
				var rminR:uint = 0x00FF0000;
				var rmaxR:uint = 0x00000000;
				var rminG:uint = 0x0000FF00;
				var rmaxG:uint = 0x00000000;
				var rminB:uint = 0x000000FF;
				var rmaxB:uint = 0x00000000;
				
				var mask:uint = 0;
				var mid:uint = 0;
				
				var blockPos:uint = 0;

				while ( colorCount < maxColors && li8( colorCount * 25 - 25 ) > 1 ) {
					
					colorCount--; // последний сплитим
					
					if ( transparent ) {
						lminA = 0xFF000000;
						lmaxA = 0x00000000;
						rminA = 0xFF000000;
						rmaxA = 0x00000000;
					}
					
					lminR = 0x00FF0000;
					lmaxR = 0x00000000;
					rminR = 0x00FF0000;
					rmaxR = 0x00000000;
					
					lminB = 0x000000FF;
					lmaxB = 0x00000000;
					rminB = 0x000000FF;
					rmaxB = 0x00000000;
					
					lminG = 0x0000FF00;
					lmaxG = 0x00000000;
					rminG = 0x0000FF00;
					rmaxG = 0x00000000;

					blockPos = colorCount * 25;
					
					mask = li32( blockPos +  1 );
					mid =  li32( blockPos +  5 ) & mask;
					i =    li32( blockPos + 17 );
					len =  li32( blockPos + 21 );
					
					z0 = z;
					z += len;
					x = z0;
					y = z;
					len += i;
					
					if ( z > 1024 ) mem.length = z;
					
					cx = ~li32( i );
					do {
						
						c = li32( i );
						i += 4;
						
						if ( c == cx ) continue;
						cx = c;
						
						v = c & mask;
						if ( v <= mid ) {
							
							if ( transparent ) {
								t = c & 0xFF000000;
								if ( t < lminA ) lminA = t;
								if ( t > lmaxA ) lmaxA = t;
							}
							
							t = c & 0x00FF0000;
							if ( t < lminR ) lminR = t;
							if ( t > lmaxR ) lmaxR = t;
							
							t = c & 0x0000FF00;
							if ( t < lminG ) lminG = t;
							if ( t > lmaxG ) lmaxG = t;
							
							t = c & 0x000000FF;
							if ( t < lminB ) lminB = t;
							if ( t > lmaxB ) lmaxB = t;
							
							si32( c, x );
							x += 4;
							
						} else {
							
							if ( transparent ) {
								t = c & 0xFF000000;
								if ( t < rminA ) rminA = t;
								if ( t > rmaxA ) rmaxA = t;
							}
							
							t = c & 0x00FF0000;
							if ( t < rminR ) rminR = t;
							if ( t > rmaxR ) rmaxR = t;
							
							t = c & 0x0000FF00;
							if ( t < rminG ) rminG = t;
							if ( t > rmaxG ) rmaxG = t;
							
							t = c & 0x000000FF;
							if ( t < rminB ) rminB = t;
							if ( t > rmaxB ) rmaxB = t;
							
							y -= 4;
							si32( c, y );
							
						}
					} while ( i < len );

					writeBlock(
						lminA, lminR, lminG, lminB,
						lmaxA, lmaxR, lmaxG, lmaxB,
						z0, x - z0
					);
					
					writeBlock(
						rminA, rminR, rminG, rminB,
						rmaxA, rmaxR, rmaxG, rmaxB,
						y, z - y
					);
					
				}

			}
			
			y = 0;
			len = colorCount * 25;
			i = 0;
			do {
				t = i * 25;
				this._list.push( li32( t + 5 ) );
				x = li32( t + 17 );
				z = li32( t + 21 ) + x;
				do {
					++y;
					this._hash[ li32( x ) >>> 0 ] = i;
					x += 4;
				} while ( x < z );
			} while ( ++i < colorCount );
			
			this._transparent = transparent;
			
			this._list.fixed = true;
			
			_DOMAIN.domainMemory = tmp;
			
		}

		//--------------------------------------------------------------------------
		//
		//  Variables
		//
		//--------------------------------------------------------------------------

		/**
		 * @private
		 */
		private const _list:Vector.<uint> = new Vector.<uint>();

		/**
		 * @private
		 */
		private const _hash:Object = new Object();

		//--------------------------------------------------------------------------
		//
		//  Properties
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private var _transparent:Boolean;
		
		/**
		 * @inheritDoc
		 */
		public function get transparent():Boolean {
			return this._transparent;
		}
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------

		/**
		 * @inheritDoc
		 */
		public function getList():Vector.<uint> {
			return this._list.slice();
		}

		/**
		 * @inheritDoc
		 */
		public function getIndexByColor(color:uint):uint {
			return this._hash[ color ];
		}

	}

}