////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image {
	
	import flash.display.BitmapData;
	import flash.display.JPEGEncoderOptions;
	import flash.system.ApplicationDomain;
	import flash.utils.ByteArray;
	import flash.utils.Endian;
	
	import by.blooddy.crypto.process.Process;
	
	/**
	 * Encodes image data using JPEG compression algorithm.
	 *
	 * @see http://www.w3.org/Graphics/JPEG/		W3C
	 * 
	 * @author					BlooDHounD
	 * @version					2.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 */
	public final class JPEGEncoder extends Process {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static const _NATIVE:Boolean = ApplicationDomain.currentDomain.hasDefinition( 'flash.display.JPEGEncoderOptions' );
		
		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Creates a JPEG image from the specified <code>BitmapData</code>.
		 * 
		 * Used <code>flash.display.BitmapData.encode</code>, if posible.
		 *
		 * @param	image		The <code>BitmapData</code> to be encoded.
		 *
		 * @param	quality		The compression level, possible values are 1 through 100 inclusive.
		 *
		 * @return 				a <code>ByteArray</code> representing the JPEG encoded image data.
		 *
		 * @see					flash.display.JPEGEncoderOptions
		 */
		public static function encode(image:BitmapData, quality:uint=80):ByteArray {
			
			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );

			if ( quality > 100 ) quality = 100;
			else if ( quality < 1 ) quality = 1;
			
			if ( _NATIVE ) {
				return image.encode( image.rect, new JPEGEncoderOptions( quality ) );
			} else {
				return $encode( image, quality );
			}
			
		}
		
		/**
		 * Creates a JPEG image from the specified <code>ByteArray</code>.
		 * 
		 * @param	bytes		The <code>ByteArray</code> to be encoded.
		 * @param	width		Image width
		 * @param	height		Image height
		 *
		 * @param	quality		The compression level, possible values are 1 through 100 inclusive.
		 *
		 * @return 				a <code>ByteArray</code> representing the JPEG encoded image data.
		 */
		public static function encodeBytes(bytes:ByteArray, width:uint, height:uint, quality:uint=80):ByteArray {
			
			if ( bytes == null ) Error.throwError( TypeError, 2007, 'bytes' );

			var image:BitmapData = new BitmapData( width, height, false );

			var pos:uint = bytes.position;
			bytes.position = 0;
			image.setPixels( image.rect, bytes );
			bytes.position = pos;
			
			return encode( image, quality );
			
		}
		
		//--------------------------------------------------------------------------
		//
		//  Private class methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static function $encode(image:BitmapData, quality:uint):ByteArray {
			
			// Create tables
			
			var quantTable:ByteArray = JPEGTable$.getQuantTable( quality );
			var jpegTable:ByteArray = JPEGTable$.getJPEGTable();
			
			// Create JPEG data
			
			var result:ByteArray = new ByteArray();
			result.endian = Endian.BIG_ENDIAN;
			
			result.writeShort( 0xFFD8 ); // SOI
			
			writeAPP0( result );
			
			//writeAPP1( result, 'by.blooddy.crypto.image.JPEGEncoder' );
			
			writeDQT( result, quantTable );
			
			writeSOF0( result, image.width, image.height );
			
			writeDHT( result, jpegTable );
			
			writeSOS( result );
			
			result.writeBytes( JPEGEncoder$.encode( image, quantTable, jpegTable ) );
			
			result.writeShort( 0xFFD9 ); // EOI
			
			return result;
			
		}
		
		/**
		 * @internal
		 */
		private static function writeAPP0(mem:ByteArray):void {
			
			mem.writeShort(			0xFFE0		);	// marker
			mem.writeShort(			0x0010		);	// length
			mem.writeUnsignedInt(	0x4A464946	);	// JFIF
			mem.writeByte(			0x00		);	//
			mem.writeShort(			0x0101		);	// version
			mem.writeByte(			0x00		);	// xyunits
			mem.writeUnsignedInt(	0x00010001	);	// density
			mem.writeShort(			0x0000		);	// thumbn
			
		}
		
		/**
		 * @internal
		 */
		private static function writeAPP1(mem:ByteArray, text:String):void {
			
			mem.writeShort(			0xFFE1		);	// marker
			mem.writeShort(			0x0046		);	// length
			
			mem.writeUnsignedInt(	0x45786966	);	// Exif
			mem.writeShort(			0x0000		);	//
			mem.writeInt(			0x49492A00	);	// TIFF Header
			mem.writeInt(			0x08000000	);	//
			
			mem.writeShort(			0x0100		);
			
			mem.writeShort(			0x3101		);	// tag
			mem.writeShort(			0x0200		);	// type
			mem.writeInt(			0x23000000	);	// count
			mem.writeInt(			0x1A000000	);	// value offset
			mem.writeInt(			0x00000000	);	//
			
			mem.writeMultiByte(		text, 'x-ascii' ); // length=35
			
			mem.writeByte(			0x00		);	// zero
			
		}
		
		/**
		 * @internal
		 */
		private static function writeDQT(mem:ByteArray, table:ByteArray):void {
			
			mem.writeShort(			0xFFDB		);	// marker
			mem.writeShort(			0x0084		);	// length
			mem.writeBytes( 		table, 0, 130 );
			
		}
		
		/**
		 * @internal
		 */
		private static function writeSOF0(mem:ByteArray, width:int, height:int):void {
			
			mem.writeShort(			0xFFC0		);	// marker
			mem.writeShort(			0x0011		);	// length, truecolor YUV JPG
			mem.writeByte(			0x08		);	// precision
			mem.writeShort(			height		);	// height
			mem.writeShort(			width		);	// width
			mem.writeByte(			0x03		);	// nrofcomponents
			mem.writeByte(			0x01		);	// IdY
			mem.writeByte(			0x11		);	// HVY
			mem.writeByte(			0x00		);	// QTY
			mem.writeByte(			0x02		);	// IdU
			mem.writeByte(			0x11		);	// HVU
			mem.writeByte(			0x01		);	// QTU
			mem.writeByte(			0x03		);	// IdV
			mem.writeByte(			0x11		);	// HVV
			mem.writeByte(			0x01		);	// QTV
			
		}
		
		/**
		 * @internal
		 */
		private static function writeDHT(mem:ByteArray, table:ByteArray):void {
			
			mem.writeShort(			0xFFC4		);	// marker
			mem.writeShort(			0x01A2		);	// length
			mem.writeBytes( 		table, 64, 416 );
			
		}
		
		/**
		 * @internal
		 */
		private static function writeSOS(mem:ByteArray):void {
			
			mem.writeShort(			0xFFDA		);	// marker
			mem.writeShort(			0x000C		);	// length
			mem.writeByte(			0x03		);	// nrofcomponents
			mem.writeShort(			0x0100		);	// IdY, HTY
			mem.writeShort(			0x0211		);	// IdU, HTU
			mem.writeShort(			0x0311		);	// IdV, HTV
			mem.writeShort(			0x003F		);	// Ss, Se
			mem.writeByte(			0x00		);	// Bf
			
		}
		
		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="JPEGEncoder.swf", mimeType="application/octet-stream" )]
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
		 * Creates a JPEGEncoder object.
		 */
		public function JPEGEncoder() {
			super( WorkerClass );
		}
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Asynchronously creates a JPEG image from the specified <code>BitmapData</code>.
		 * 
		 * Dispatched sequence of bytes in <code>ProcessEvent</code>.
		 *
		 * @param	image		The <code>BitmapData</code> to be encoded.
		 *
		 * @param	quality		The compression level, possible values are 1 through 100 inclusive.
		 */
		public function encode(image:BitmapData, quality:uint=80):void {
			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );
			super.call( 'encodeBytes', image.getPixels( image.rect ), image.width, image.height, quality );
		}
		
		/**
		 * Asynchronously creates a JPEG image from the specified <code>ByteArray</code>.
		 * 
		 * Dispatched sequence of bytes in <code>ProcessEvent</code>.
		 *
		 * @param	bytes		The <code>ByteArray</code> to be encoded.
		 * @param	width		Image width
		 * @param	height		Image height
		 *
		 * @param	quality		The compression level, possible values are 1 through 100 inclusive.
		 */
		public function encodeBytes(bytes:ByteArray, width:uint, height:uint, quality:uint=80):void {
			if ( bytes == null ) Error.throwError( TypeError, 2007, 'bytes' );
			super.call( 'encodeBytes', bytes, width, height, quality );
		}
		
		}
		
	}
	
}

import flash.display.BitmapData;
import flash.system.ApplicationDomain;
import flash.utils.ByteArray;

import avm2.intrinsics.memory.lf64;
import avm2.intrinsics.memory.li16;
import avm2.intrinsics.memory.li32;
import avm2.intrinsics.memory.li8;
import avm2.intrinsics.memory.sf64;
import avm2.intrinsics.memory.si16;
import avm2.intrinsics.memory.si32;
import avm2.intrinsics.memory.si8;

/**
 * @private
 */
internal final class JPEGEncoder$ {
	
	//--------------------------------------------------------------------------
	//
	//  Encode
	//
	//--------------------------------------------------------------------------
	
	internal static function encode(image:BitmapData, quantTable:ByteArray, jpegTable:ByteArray):ByteArray {
		
		var width:int = image.width;
		var height:int = image.height;
		
		var tmp:ByteArray = _DOMAIN.domainMemory;
		
		var mem:ByteArray = new ByteArray();
		mem.position = 256 + 512 * 3;
		mem.writeBytes( quantTable );
		mem.writeBytes( jpegTable );
		mem.length += Math.max( 8, width * height ) * 3;
		
		if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
		
		_DOMAIN.domainMemory = mem;
		
		// Encode 8x8 macroblocks
		var DCY:int = 0;
		var DCU:int = 0;
		var DCV:int = 0;
		
		var x:int = 0;
		var y:int = 0;
		
		var xi:int = 0;
		var yi:int = 0;
		
		var xm:int = 0;
		var ym:int = 0;
		
		var c:int = 0;
		var r:int = 0;
		var g:int = 0;
		var b:int = 0;
		
		var byteout:int = 256 + 512 * 3 + 199817;
		var bytepos:int = 7;
		var bytenew:int = 0;
		var len:int, val:int;
		
		var dataOff:int;
		var d0:Number, d1:Number, d2:Number, d3:Number, d4:Number, d5:Number, d6:Number, d7:Number;
		var tmp0:Number, tmp1:Number, tmp2:Number, tmp3:Number, tmp4:Number, tmp5:Number, tmp6:Number, tmp7:Number;
		var tmp10:Number, tmp11:Number, tmp12:Number, tmp13:Number;
		var z1:Number, z2:Number, z3:Number, z4:Number, z5:Number;
		var z11:Number, z13:Number;
		
		var fDCTQuant:Number;
		var i:int;
		
		var pos:int = 0;
		var mpos:int = 0;
		
		var end0pos:int;
		
		var lng:int;
		var startpos:int;
		var nrzeroes:int;
		var nrmarker:int;

		var CDU:int, fdtbl:int, DCV:int, HTDC:int, HTAC:int;
		
		y = 0;
		do {
			x = 0;
			do {
				
				// RGB to YUV

				pos = 0;
				
				xi = x;
				yi = y;
				
				xm = x + 8;
				ym = y + 8;
				
				yi = y;
				do {
					xi = x;
					do {
						
						c = image.getPixel( xi, yi );
						
						r =   c >>> 16         ;
						g = ( c >>   8 ) & 0xFF;
						b =   c          & 0xFF;
						
						sf64(   0.29900 * r + 0.58700 * g + 0.11400 * b - 0x80, 256 + 512 * 0 + pos ); // YDU
						sf64( - 0.16874 * r - 0.33126 * g + 0.50000 * b       , 256 + 512 * 1 + pos ); // UDU
						sf64(   0.50000 * r - 0.41869 * g - 0.08131 * b       , 256 + 512 * 2 + pos ); // VDU
						
						pos += 8;
						
					} while ( ++xi < xm );
				} while ( ++yi < ym );

				// ---
				// DCY = processDU2( CDU, fdtbl, HTDC, HTAC, DCY )
				// ---
				
				CDU =	256 + 512 * 0;
				fdtbl =	256 + 512 * 3 + 130;
				HTDC =	256 + 512 * 3 + 1218 + 416;
				HTAC =	256 + 512 * 3 + 1218 + 452;
				
				/* Pass 1: process rows. */
				dataOff = 0;
				do {
					
					d0 = lf64( CDU + dataOff + 0 * 8 );
					d1 = lf64( CDU + dataOff + 1 * 8 );
					d2 = lf64( CDU + dataOff + 2 * 8 );
					d3 = lf64( CDU + dataOff + 3 * 8 );
					d4 = lf64( CDU + dataOff + 4 * 8 );
					d5 = lf64( CDU + dataOff + 5 * 8 );
					d6 = lf64( CDU + dataOff + 6 * 8 );
					d7 = lf64( CDU + dataOff + 7 * 8 );
					
					tmp0 = d0 + d7;
					tmp7 = d0 - d7;
					tmp1 = d1 + d6;
					tmp6 = d1 - d6;
					tmp2 = d2 + d5;
					tmp5 = d2 - d5;
					tmp3 = d3 + d4;
					tmp4 = d3 - d4;
					
					// Even part
					// phase 2
					tmp10 = tmp0 + tmp3;
					tmp13 = tmp0 - tmp3;
					tmp11 = tmp1 + tmp2;
					tmp12 = tmp1 - tmp2;
					
					// phase 3
					sf64( tmp10 + tmp11, CDU + dataOff + 0 * 8 );
					sf64( tmp10 - tmp11, CDU + dataOff + 4 * 8 );
					
					// phase 5
					z1 = ( tmp12 + tmp13 ) * 0.707106781;	// c4
					sf64( tmp13 + z1, CDU + dataOff + 2 * 8 );
					sf64( tmp13 - z1, CDU + dataOff + 6 * 8 );
					
					// Odd part
					// phase 2
					tmp10 = tmp4 + tmp5;
					tmp11 = tmp5 + tmp6;
					tmp12 = tmp6 + tmp7;
					
					// The rotator is modified from fig 4-8 to avoid extra negations.
					z5 = ( tmp10 - tmp12 ) * 0.382683433;	// c6
					z2 = 0.541196100 * tmp10 + z5;			// c2-c6
					z4 = 1.306562965 * tmp12 + z5;			// c2+c6
					z3 = tmp11 * 0.707106781;				// c4
					
					//phase 5
					z11 = tmp7 + z3;
					z13 = tmp7 - z3;
					
					// phase 6
					sf64( z13 + z2, CDU + dataOff + 5 * 8 );
					sf64( z13 - z2, CDU + dataOff + 3 * 8 );
					sf64( z11 + z4, CDU + dataOff + 1 * 8 );
					sf64( z11 - z4, CDU + dataOff + 7 * 8 );
					
					dataOff += 64; // advance pointer to next row
					
				} while ( dataOff < 512 );
				
				// Pass 2: process columns.
				dataOff = 0;
				do {
					
					d0 = lf64( CDU + dataOff +  0 * 8 );
					d1 = lf64( CDU + dataOff +  8 * 8 );
					d2 = lf64( CDU + dataOff + 16 * 8 );
					d3 = lf64( CDU + dataOff + 24 * 8 );
					d4 = lf64( CDU + dataOff + 32 * 8 );
					d5 = lf64( CDU + dataOff + 40 * 8 );
					d6 = lf64( CDU + dataOff + 48 * 8 );
					d7 = lf64( CDU + dataOff + 56 * 8 );
					
					tmp0 = d0 + d7;
					tmp7 = d0 - d7;
					tmp1 = d1 + d6;
					tmp6 = d1 - d6;
					tmp2 = d2 + d5;
					tmp5 = d2 - d5;
					tmp3 = d3 + d4;
					tmp4 = d3 - d4;
					
					// Even part
					// phase 2
					tmp10 = tmp0 + tmp3;
					tmp13 = tmp0 - tmp3;
					tmp11 = tmp1 + tmp2;
					tmp12 = tmp1 - tmp2;
					
					// phase 3
					sf64( tmp10 + tmp11, CDU + dataOff +  0 * 8 );
					sf64( tmp10 - tmp11, CDU + dataOff + 32 * 8 );
					
					// phase 5
					z1 = ( tmp12 + tmp13 ) * 0.707106781;	// c4
					sf64( tmp13 + z1, CDU + dataOff + 16 * 8 );
					sf64( tmp13 - z1, CDU + dataOff + 48 * 8 );
					
					// Odd part
					// phase 2
					tmp10 = tmp4 + tmp5;
					tmp11 = tmp5 + tmp6;
					tmp12 = tmp6 + tmp7;
					
					// The rotator is modified from fig 4-8 to avoid extra negations.
					z5 = ( tmp10 - tmp12 ) * 0.382683433;	// c6
					z2 = 0.541196100 * tmp10 + z5;			// c2-c6
					z4 = 1.306562965 * tmp12 + z5;			// c2+c6
					z3 = tmp11 * 0.707106781;				// c4
					
					// phase 5
					z11 = tmp7 + z3;
					z13 = tmp7 - z3;
					
					// phase 6
					sf64( z13 + z2, CDU + dataOff + 40 * 8 );
					sf64( z13 - z2, CDU + dataOff + 24 * 8 );
					sf64( z11 + z4, CDU + dataOff +  8 * 8 );
					sf64( z11 - z4, CDU + dataOff + 56 * 8 );
					
					dataOff += 8; // advance pointer to next column
				} while ( dataOff < 64 );
				
				// Quantize/descale the coefficients
				i = 0;
				do {
					// Apply the quantization and scaling factor & Round to nearest integer
					fDCTQuant = lf64( CDU + ( i << 3 ) ) * lf64( fdtbl + ( i << 3 ) );
					si32(
						fDCTQuant + ( fDCTQuant > 0.0 ? 0.5 : - 0.5 ),
						li8( 256 + 512 * 3 + 1154 + i ) << 2 // ZigZag reorder
					);
				} while ( ++i < 64 );
				
				// processDU
				
				CDU = li32( 0 );
				dataOff = CDU - DCY;
				DCY = CDU;
				
				// Encode DC
				if ( dataOff == 0 ) {
					
					// Diff might be 0
					// writeBits( li8( HTDC ), li16( HTDC + 1 ) );
					len = li8( HTDC ) - 1;
					if ( len >= 0 ) {
						val = li16( HTDC + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
				} else {
					
					pos = ( 32767 + dataOff ) * 3;
					
					mpos = HTDC + li8( 256 + 512 * 3 + 3212 + pos ) * 3;
					// writeBits( li8( mpos ), li16( mpos + 1 ) );
					len = li8( mpos ) - 1;
					if ( len >= 0 ) {
						val = li16( mpos + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
					mpos = 256 + 512 * 3 + 3212 + pos;
					// writeBits( li8( mpos ), li16( mpos + 1 ) );
					len = li8( mpos ) - 1;
					if ( len >= 0 ) {
						val = li16( mpos + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
				}
				
				// Encode ACs
				end0pos = 63;
				while ( end0pos > 0 && li32( end0pos << 2 ) == 0 ) end0pos--;
				
				// end0pos = first element in reverse order !=0
				if ( end0pos != 0 ) {
					
					i = 1;
					while ( i <= end0pos ) {
						startpos = i;
						while ( i <= end0pos && li32( i << 2 ) == 0 ) ++i;
						nrzeroes = i - startpos;
						if ( nrzeroes >= 16 ) {
							
							lng = nrzeroes >> 4;
							nrmarker = 1;
							while ( nrmarker <= lng ) {
								
								mpos = HTAC + 0xF0 * 3;
								// writeBits( li8( mpos ), li16( mpos + 1 ) );
								len = li8( mpos ) - 1;
								if ( len >= 0 ) {
									val = li16( mpos + 1 );
									do {
										if ( ( val & ( 1 << len ) ) != 0 ) {
											bytenew |= 1 << bytepos;
										}
										if ( --bytepos < 0 ) {
											if ( bytenew == 0xFF ) {
												si16( 0x00FF, byteout );
												byteout += 2;
											} else {
												si8( bytenew, byteout );
												byteout++;
											}
											bytepos = 7;
											bytenew = 0;
										}
									} while ( --len >= 0 );
								}
								
								++nrmarker;
								
							}
							nrzeroes &= 0xF;
							
						}
						
						pos = ( 32767 + li32( i << 2 ) ) * 3;
						
						mpos = HTAC + ( nrzeroes << 4 ) * 3 + li8( 256 + 512 * 3 + 3212 + pos ) * 3;
						// writeBits( li8( mpos ), li16( mpos + 1 ) );
						len = li8( mpos ) - 1;
						if ( len >= 0 ) {
							val = li16( mpos + 1 );
							do {
								if ( ( val & ( 1 << len ) ) != 0 ) {
									bytenew |= 1 << bytepos;
								}
								if ( --bytepos < 0 ) {
									if ( bytenew == 0xFF ) {
										si16( 0x00FF, byteout );
										byteout += 2;
									} else {
										si8( bytenew, byteout );
										byteout++;
									}
									bytepos = 7;
									bytenew = 0;
								}
							} while ( --len >= 0 );
						}
						
						mpos = 256 + 512 * 3 + 3212 + pos;
						// writeBits( li8( mpos ), li16( mpos + 1 ) );
						len = li8( mpos ) - 1;
						if ( len >= 0 ) {
							val = li16( mpos + 1 );
							do {
								if ( ( val & ( 1 << len ) ) != 0 ) {
									bytenew |= 1 << bytepos;
								}
								if ( --bytepos < 0 ) {
									if ( bytenew == 0xFF ) {
										si16( 0x00FF, byteout );
										byteout += 2;
									} else {
										si8( bytenew, byteout );
										byteout++;
									}
									bytepos = 7;
									bytenew = 0;
								}
							} while ( --len >= 0 );
						}
						
						++i;
						
					}
					
				}
				
				if ( end0pos != 63 ) {
					// writeBits( li8( HTAC ), li16( HTAC + 1 ) );
					len = li8( HTAC ) - 1;
					if ( len >= 0 ) {
						val = li16( HTAC + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
				}
					
				// ---
				// DCU = processDU2( CDU, fdtbl, HTDC, HTAC, DCU )
				// ---
				
				CDU =	256 + 512 * 1;
				fdtbl =	256 + 512 * 3 + 642;
				HTDC =	256 + 512 * 3 + 1218 + 1205;
				HTAC =	256 + 512 * 3 + 1218 + 1241;
				
				/* Pass 1: process rows. */
				dataOff = 0;
				do {
					
					d0 = lf64( CDU + dataOff + 0 * 8 );
					d1 = lf64( CDU + dataOff + 1 * 8 );
					d2 = lf64( CDU + dataOff + 2 * 8 );
					d3 = lf64( CDU + dataOff + 3 * 8 );
					d4 = lf64( CDU + dataOff + 4 * 8 );
					d5 = lf64( CDU + dataOff + 5 * 8 );
					d6 = lf64( CDU + dataOff + 6 * 8 );
					d7 = lf64( CDU + dataOff + 7 * 8 );
					
					tmp0 = d0 + d7;
					tmp7 = d0 - d7;
					tmp1 = d1 + d6;
					tmp6 = d1 - d6;
					tmp2 = d2 + d5;
					tmp5 = d2 - d5;
					tmp3 = d3 + d4;
					tmp4 = d3 - d4;
					
					// Even part
					// phase 2
					tmp10 = tmp0 + tmp3;
					tmp13 = tmp0 - tmp3;
					tmp11 = tmp1 + tmp2;
					tmp12 = tmp1 - tmp2;
					
					// phase 3
					sf64( tmp10 + tmp11, CDU + dataOff + 0 * 8 );
					sf64( tmp10 - tmp11, CDU + dataOff + 4 * 8 );
					
					// phase 5
					z1 = ( tmp12 + tmp13 ) * 0.707106781;	// c4
					sf64( tmp13 + z1, CDU + dataOff + 2 * 8 );
					sf64( tmp13 - z1, CDU + dataOff + 6 * 8 );
					
					// Odd part
					// phase 2
					tmp10 = tmp4 + tmp5;
					tmp11 = tmp5 + tmp6;
					tmp12 = tmp6 + tmp7;
					
					// The rotator is modified from fig 4-8 to avoid extra negations.
					z5 = ( tmp10 - tmp12 ) * 0.382683433;	// c6
					z2 = 0.541196100 * tmp10 + z5;			// c2-c6
					z4 = 1.306562965 * tmp12 + z5;			// c2+c6
					z3 = tmp11 * 0.707106781;				// c4
					
					//phase 5
					z11 = tmp7 + z3;
					z13 = tmp7 - z3;
					
					// phase 6
					sf64( z13 + z2, CDU + dataOff + 5 * 8 );
					sf64( z13 - z2, CDU + dataOff + 3 * 8 );
					sf64( z11 + z4, CDU + dataOff + 1 * 8 );
					sf64( z11 - z4, CDU + dataOff + 7 * 8 );
					
					dataOff += 64; // advance pointer to next row
					
				} while ( dataOff < 512 );
				
				// Pass 2: process columns.
				dataOff = 0;
				do {
					
					d0 = lf64( CDU + dataOff +  0 * 8 );
					d1 = lf64( CDU + dataOff +  8 * 8 );
					d2 = lf64( CDU + dataOff + 16 * 8 );
					d3 = lf64( CDU + dataOff + 24 * 8 );
					d4 = lf64( CDU + dataOff + 32 * 8 );
					d5 = lf64( CDU + dataOff + 40 * 8 );
					d6 = lf64( CDU + dataOff + 48 * 8 );
					d7 = lf64( CDU + dataOff + 56 * 8 );
					
					tmp0 = d0 + d7;
					tmp7 = d0 - d7;
					tmp1 = d1 + d6;
					tmp6 = d1 - d6;
					tmp2 = d2 + d5;
					tmp5 = d2 - d5;
					tmp3 = d3 + d4;
					tmp4 = d3 - d4;
					
					// Even part
					// phase 2
					tmp10 = tmp0 + tmp3;
					tmp13 = tmp0 - tmp3;
					tmp11 = tmp1 + tmp2;
					tmp12 = tmp1 - tmp2;
					
					// phase 3
					sf64( tmp10 + tmp11, CDU + dataOff +  0 * 8 );
					sf64( tmp10 - tmp11, CDU + dataOff + 32 * 8 );
					
					// phase 5
					z1 = ( tmp12 + tmp13 ) * 0.707106781;	// c4
					sf64( tmp13 + z1, CDU + dataOff + 16 * 8 );
					sf64( tmp13 - z1, CDU + dataOff + 48 * 8 );
					
					// Odd part
					// phase 2
					tmp10 = tmp4 + tmp5;
					tmp11 = tmp5 + tmp6;
					tmp12 = tmp6 + tmp7;
					
					// The rotator is modified from fig 4-8 to avoid extra negations.
					z5 = ( tmp10 - tmp12 ) * 0.382683433;	// c6
					z2 = 0.541196100 * tmp10 + z5;			// c2-c6
					z4 = 1.306562965 * tmp12 + z5;			// c2+c6
					z3 = tmp11 * 0.707106781;				// c4
					
					// phase 5
					z11 = tmp7 + z3;
					z13 = tmp7 - z3;
					
					// phase 6
					sf64( z13 + z2, CDU + dataOff + 40 * 8 );
					sf64( z13 - z2, CDU + dataOff + 24 * 8 );
					sf64( z11 + z4, CDU + dataOff +  8 * 8 );
					sf64( z11 - z4, CDU + dataOff + 56 * 8 );
					
					dataOff += 8; // advance pointer to next column
				} while ( dataOff < 64 );
				
				// Quantize/descale the coefficients
				i = 0;
				do {
					// Apply the quantization and scaling factor & Round to nearest integer
					fDCTQuant = lf64( CDU + ( i << 3 ) ) * lf64( fdtbl + ( i << 3 ) );
					si32(
						fDCTQuant + ( fDCTQuant > 0.0 ? 0.5 : - 0.5 ),
						li8( 256 + 512 * 3 + 1154 + i ) << 2 // ZigZag reorder
					);
				} while ( ++i < 64 );
				
				// processDU
				
				CDU = li32( 0 );
				dataOff = CDU - DCU;
				DCU = CDU;
				
				// Encode DC
				if ( dataOff == 0 ) {
					
					// Diff might be 0
					// writeBits( li8( HTDC ), li16( HTDC + 1 ) );
					len = li8( HTDC ) - 1;
					if ( len >= 0 ) {
						val = li16( HTDC + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
				} else {
					
					pos = ( 32767 + dataOff ) * 3;
					
					mpos = HTDC + li8( 256 + 512 * 3 + 3212 + pos ) * 3;
					// writeBits( li8( mpos ), li16( mpos + 1 ) );
					len = li8( mpos ) - 1;
					if ( len >= 0 ) {
						val = li16( mpos + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
					mpos = 256 + 512 * 3 + 3212 + pos;
					// writeBits( li8( mpos ), li16( mpos + 1 ) );
					len = li8( mpos ) - 1;
					if ( len >= 0 ) {
						val = li16( mpos + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
				}
				
				// Encode ACs
				end0pos = 63;
				while ( end0pos > 0 && li32( end0pos << 2 ) == 0 ) end0pos--;
				
				// end0pos = first element in reverse order !=0
				if ( end0pos != 0 ) {
					
					i = 1;
					while ( i <= end0pos ) {
						startpos = i;
						while ( i <= end0pos && li32( i << 2 ) == 0 ) ++i;
						nrzeroes = i - startpos;
						if ( nrzeroes >= 16 ) {
							
							lng = nrzeroes >> 4;
							nrmarker = 1;
							while ( nrmarker <= lng ) {
								
								mpos = HTAC + 0xF0 * 3;
								// writeBits( li8( mpos ), li16( mpos + 1 ) );
								len = li8( mpos ) - 1;
								if ( len >= 0 ) {
									val = li16( mpos + 1 );
									do {
										if ( ( val & ( 1 << len ) ) != 0 ) {
											bytenew |= 1 << bytepos;
										}
										if ( --bytepos < 0 ) {
											if ( bytenew == 0xFF ) {
												si16( 0x00FF, byteout );
												byteout += 2;
											} else {
												si8( bytenew, byteout );
												byteout++;
											}
											bytepos = 7;
											bytenew = 0;
										}
									} while ( --len >= 0 );
								}
								
								++nrmarker;
								
							}
							nrzeroes &= 0xF;
							
						}
						
						pos = ( 32767 + li32( i << 2 ) ) * 3;
						
						mpos = HTAC + ( nrzeroes << 4 ) * 3 + li8( 256 + 512 * 3 + 3212 + pos ) * 3;
						// writeBits( li8( mpos ), li16( mpos + 1 ) );
						len = li8( mpos ) - 1;
						if ( len >= 0 ) {
							val = li16( mpos + 1 );
							do {
								if ( ( val & ( 1 << len ) ) != 0 ) {
									bytenew |= 1 << bytepos;
								}
								if ( --bytepos < 0 ) {
									if ( bytenew == 0xFF ) {
										si16( 0x00FF, byteout );
										byteout += 2;
									} else {
										si8( bytenew, byteout );
										byteout++;
									}
									bytepos = 7;
									bytenew = 0;
								}
							} while ( --len >= 0 );
						}
						
						mpos = 256 + 512 * 3 + 3212 + pos;
						// writeBits( li8( mpos ), li16( mpos + 1 ) );
						len = li8( mpos ) - 1;
						if ( len >= 0 ) {
							val = li16( mpos + 1 );
							do {
								if ( ( val & ( 1 << len ) ) != 0 ) {
									bytenew |= 1 << bytepos;
								}
								if ( --bytepos < 0 ) {
									if ( bytenew == 0xFF ) {
										si16( 0x00FF, byteout );
										byteout += 2;
									} else {
										si8( bytenew, byteout );
										byteout++;
									}
									bytepos = 7;
									bytenew = 0;
								}
							} while ( --len >= 0 );
						}
						
						++i;
						
					}
					
				}
				
				if ( end0pos != 63 ) {
					// writeBits( li8( HTAC ), li16( HTAC + 1 ) );
					len = li8( HTAC ) - 1;
					if ( len >= 0 ) {
						val = li16( HTAC + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
				}
					
				// ---
				// DCV = processDU2( CDU, fdtbl, HTDC, HTAC, DCV )
				// ---
				
				CDU =	256 + 512 * 2;
				fdtbl =	256 + 512 * 3 + 642;
				HTDC =	256 + 512 * 3 + 1218 + 1205;
				HTAC =	256 + 512 * 3 + 1218 + 1241;
				
				/* Pass 1: process rows. */
				dataOff = 0;
				do {
					
					d0 = lf64( CDU + dataOff + 0 * 8 );
					d1 = lf64( CDU + dataOff + 1 * 8 );
					d2 = lf64( CDU + dataOff + 2 * 8 );
					d3 = lf64( CDU + dataOff + 3 * 8 );
					d4 = lf64( CDU + dataOff + 4 * 8 );
					d5 = lf64( CDU + dataOff + 5 * 8 );
					d6 = lf64( CDU + dataOff + 6 * 8 );
					d7 = lf64( CDU + dataOff + 7 * 8 );
					
					tmp0 = d0 + d7;
					tmp7 = d0 - d7;
					tmp1 = d1 + d6;
					tmp6 = d1 - d6;
					tmp2 = d2 + d5;
					tmp5 = d2 - d5;
					tmp3 = d3 + d4;
					tmp4 = d3 - d4;
					
					// Even part
					// phase 2
					tmp10 = tmp0 + tmp3;
					tmp13 = tmp0 - tmp3;
					tmp11 = tmp1 + tmp2;
					tmp12 = tmp1 - tmp2;
					
					// phase 3
					sf64( tmp10 + tmp11, CDU + dataOff + 0 * 8 );
					sf64( tmp10 - tmp11, CDU + dataOff + 4 * 8 );
					
					// phase 5
					z1 = ( tmp12 + tmp13 ) * 0.707106781;	// c4
					sf64( tmp13 + z1, CDU + dataOff + 2 * 8 );
					sf64( tmp13 - z1, CDU + dataOff + 6 * 8 );
					
					// Odd part
					// phase 2
					tmp10 = tmp4 + tmp5;
					tmp11 = tmp5 + tmp6;
					tmp12 = tmp6 + tmp7;
					
					// The rotator is modified from fig 4-8 to avoid extra negations.
					z5 = ( tmp10 - tmp12 ) * 0.382683433;	// c6
					z2 = 0.541196100 * tmp10 + z5;			// c2-c6
					z4 = 1.306562965 * tmp12 + z5;			// c2+c6
					z3 = tmp11 * 0.707106781;				// c4
					
					//phase 5
					z11 = tmp7 + z3;
					z13 = tmp7 - z3;
					
					// phase 6
					sf64( z13 + z2, CDU + dataOff + 5 * 8 );
					sf64( z13 - z2, CDU + dataOff + 3 * 8 );
					sf64( z11 + z4, CDU + dataOff + 1 * 8 );
					sf64( z11 - z4, CDU + dataOff + 7 * 8 );
					
					dataOff += 64; // advance pointer to next row
					
				} while ( dataOff < 512 );
				
				// Pass 2: process columns.
				dataOff = 0;
				do {
					
					d0 = lf64( CDU + dataOff +  0 * 8 );
					d1 = lf64( CDU + dataOff +  8 * 8 );
					d2 = lf64( CDU + dataOff + 16 * 8 );
					d3 = lf64( CDU + dataOff + 24 * 8 );
					d4 = lf64( CDU + dataOff + 32 * 8 );
					d5 = lf64( CDU + dataOff + 40 * 8 );
					d6 = lf64( CDU + dataOff + 48 * 8 );
					d7 = lf64( CDU + dataOff + 56 * 8 );
					
					tmp0 = d0 + d7;
					tmp7 = d0 - d7;
					tmp1 = d1 + d6;
					tmp6 = d1 - d6;
					tmp2 = d2 + d5;
					tmp5 = d2 - d5;
					tmp3 = d3 + d4;
					tmp4 = d3 - d4;
					
					// Even part
					// phase 2
					tmp10 = tmp0 + tmp3;
					tmp13 = tmp0 - tmp3;
					tmp11 = tmp1 + tmp2;
					tmp12 = tmp1 - tmp2;
					
					// phase 3
					sf64( tmp10 + tmp11, CDU + dataOff +  0 * 8 );
					sf64( tmp10 - tmp11, CDU + dataOff + 32 * 8 );
					
					// phase 5
					z1 = ( tmp12 + tmp13 ) * 0.707106781;	// c4
					sf64( tmp13 + z1, CDU + dataOff + 16 * 8 );
					sf64( tmp13 - z1, CDU + dataOff + 48 * 8 );
					
					// Odd part
					// phase 2
					tmp10 = tmp4 + tmp5;
					tmp11 = tmp5 + tmp6;
					tmp12 = tmp6 + tmp7;
					
					// The rotator is modified from fig 4-8 to avoid extra negations.
					z5 = ( tmp10 - tmp12 ) * 0.382683433;	// c6
					z2 = 0.541196100 * tmp10 + z5;			// c2-c6
					z4 = 1.306562965 * tmp12 + z5;			// c2+c6
					z3 = tmp11 * 0.707106781;				// c4
					
					// phase 5
					z11 = tmp7 + z3;
					z13 = tmp7 - z3;
					
					// phase 6
					sf64( z13 + z2, CDU + dataOff + 40 * 8 );
					sf64( z13 - z2, CDU + dataOff + 24 * 8 );
					sf64( z11 + z4, CDU + dataOff +  8 * 8 );
					sf64( z11 - z4, CDU + dataOff + 56 * 8 );
					
					dataOff += 8; // advance pointer to next column
				} while ( dataOff < 64 );
				
				// Quantize/descale the coefficients
				i = 0;
				do {
					// Apply the quantization and scaling factor & Round to nearest integer
					fDCTQuant = lf64( CDU + ( i << 3 ) ) * lf64( fdtbl + ( i << 3 ) );
					si32(
						fDCTQuant + ( fDCTQuant > 0.0 ? 0.5 : - 0.5 ),
						li8( 256 + 512 * 3 + 1154 + i ) << 2 // ZigZag reorder
					);
				} while ( ++i < 64 );
				
				// processDU
				
				CDU = li32( 0 );
				dataOff = CDU - DCV;
				DCV = CDU;
				
				// Encode DC
				if ( dataOff == 0 ) {
					
					// Diff might be 0
					// writeBits( li8( HTDC ), li16( HTDC + 1 ) );
					len = li8( HTDC ) - 1;
					if ( len >= 0 ) {
						val = li16( HTDC + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
				} else {
					
					pos = ( 32767 + dataOff ) * 3;
					
					mpos = HTDC + li8( 256 + 512 * 3 + 3212 + pos ) * 3;
					// writeBits( li8( mpos ), li16( mpos + 1 ) );
					len = li8( mpos ) - 1;
					if ( len >= 0 ) {
						val = li16( mpos + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
					mpos = 256 + 512 * 3 + 3212 + pos;
					// writeBits( li8( mpos ), li16( mpos + 1 ) );
					len = li8( mpos ) - 1;
					if ( len >= 0 ) {
						val = li16( mpos + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
					
				}
				
				// Encode ACs
				end0pos = 63;
				while ( end0pos > 0 && li32( end0pos << 2 ) == 0 ) end0pos--;
				
				// end0pos = first element in reverse order !=0
				if ( end0pos != 0 ) {
					
					i = 1;
					while ( i <= end0pos ) {
						startpos = i;
						while ( i <= end0pos && li32( i << 2 ) == 0 ) ++i;
						nrzeroes = i - startpos;
						if ( nrzeroes >= 16 ) {
							
							lng = nrzeroes >> 4;
							nrmarker = 1;
							while ( nrmarker <= lng ) {
								
								mpos = HTAC + 0xF0 * 3;
								// writeBits( li8( mpos ), li16( mpos + 1 ) );
								len = li8( mpos ) - 1;
								if ( len >= 0 ) {
									val = li16( mpos + 1 );
									do {
										if ( ( val & ( 1 << len ) ) != 0 ) {
											bytenew |= 1 << bytepos;
										}
										if ( --bytepos < 0 ) {
											if ( bytenew == 0xFF ) {
												si16( 0x00FF, byteout );
												byteout += 2;
											} else {
												si8( bytenew, byteout );
												byteout++;
											}
											bytepos = 7;
											bytenew = 0;
										}
									} while ( --len >= 0 );
								}
								
								++nrmarker;
								
							}
							nrzeroes &= 0xF;
							
						}
						
						pos = ( 32767 + li32( i << 2 ) ) * 3;
						
						mpos = HTAC + ( nrzeroes << 4 ) * 3 + li8( 256 + 512 * 3 + 3212 + pos ) * 3;
						// writeBits( li8( mpos ), li16( mpos + 1 ) );
						len = li8( mpos ) - 1;
						if ( len >= 0 ) {
							val = li16( mpos + 1 );
							do {
								if ( ( val & ( 1 << len ) ) != 0 ) {
									bytenew |= 1 << bytepos;
								}
								if ( --bytepos < 0 ) {
									if ( bytenew == 0xFF ) {
										si16( 0x00FF, byteout );
										byteout += 2;
									} else {
										si8( bytenew, byteout );
										byteout++;
									}
									bytepos = 7;
									bytenew = 0;
								}
							} while ( --len >= 0 );
						}
						
						mpos = 256 + 512 * 3 + 3212 + pos;
						// writeBits( li8( mpos ), li16( mpos + 1 ) );
						len = li8( mpos ) - 1;
						if ( len >= 0 ) {
							val = li16( mpos + 1 );
							do {
								if ( ( val & ( 1 << len ) ) != 0 ) {
									bytenew |= 1 << bytepos;
								}
								if ( --bytepos < 0 ) {
									if ( bytenew == 0xFF ) {
										si16( 0x00FF, byteout );
										byteout += 2;
									} else {
										si8( bytenew, byteout );
										byteout++;
									}
									bytepos = 7;
									bytenew = 0;
								}
							} while ( --len >= 0 );
						}
						
						++i;
						
					}
					
				}
				
				if ( end0pos != 63 ) {
					// writeBits( li8( HTAC ), li16( HTAC + 1 ) );
					len = li8( HTAC ) - 1;
					if ( len >= 0 ) {
						val = li16( HTAC + 1 );
						do {
							if ( ( val & ( 1 << len ) ) != 0 ) {
								bytenew |= 1 << bytepos;
							}
							if ( --bytepos < 0 ) {
								if ( bytenew == 0xFF ) {
									si16( 0x00FF, byteout );
									byteout += 2;
								} else {
									si8( bytenew, byteout );
									byteout++;
								}
								bytepos = 7;
								bytenew = 0;
							}
						} while ( --len >= 0 );
					}
				}
					
				x += 8;
			} while ( x < width );
			y += 8;
		} while ( y < height );
		
		// Do the bit alignment of the EOI marker
		if ( bytepos >= 0 ) {

			// writeBits( _BYTE_POS + 1, ( 1 << ( _BYTE_POS + 1 ) ) - 1 );
			len = bytepos;
			if ( len >= 0 ) {
				do {
					val = ( 1 << ( bytepos + 1 ) ) - 1;
					if ( ( val & ( 1 << len ) ) != 0 ) {
						bytenew |= 1 << bytepos;
					}
					if ( --bytepos < 0 ) {
						if ( bytenew == 0xFF ) {
							si16( 0x00FF, byteout );
							byteout += 2;
						} else {
							si8( bytenew, byteout );
							byteout++;
						}
						bytepos = 7;
						bytenew = 0;
					}
				} while ( --len >= 0 );
			}

		}
		
		_DOMAIN.domainMemory = tmp;
		
		mem.position = 0;
		mem.writeBytes( mem, 256 + 512 * 3 + 199817, byteout - 256 - 512 * 3 - 199817 );
		mem.length = byteout - 256 + 512 * 3 + 199817;
		
		return mem;
		
	}
	
	//--------------------------------------------------------------------------
	//  encode main methods
	//--------------------------------------------------------------------------
	
	private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
	
}

/**
 * @private
 */
internal final class JPEGTable$ {
	
	//--------------------------------------------------------------------------
	//  variables
	//--------------------------------------------------------------------------
	
	/**
	 * @private
	 */
	private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
	
	//--------------------------------------------------------------------------
	//  quant table
	//--------------------------------------------------------------------------
	
	/**
	 * @see	#getQuantTable()
	 */
	private static const _QUANTS:Vector.<ByteArray> = new Vector.<ByteArray>( 101, true );
	
	/**
	 * <table>
	 *	<tr><th>   0</th><td>0							</td><td>[1]{1}		</td></tr>
	 *	<tr><th>   1</th><td>YTable						</td><td>[1]{64}	</td></tr>
	 *	<tr><th>  64</th><td>0							</td><td>[1]{1}		</td></tr>
	 *	<tr><th>  65</th><td>UVTable					</td><td>[1]{64}	</td></tr>
	 *	<tr><th> 130</th><td>fdtbl_Y					</td><td>[8]{64}	</td></tr>
	 *	<tr><th> 642</th><td>fdtbl_UV					</td><td>[8]{64}	</td></tr>
	 * 	<tr><th>1154</th><td>							</td><td>			</td></tr>
	 * </table>
	 */
	internal static function getQuantTable(quality:int):ByteArray {
		
		var mem:ByteArray = _QUANTS[ quality - 1 ];
		if ( !mem ) {
			
			var sf:int = ( quality <= 1
				?	5000
				:	( quality < 50
					?	5000 / quality
					:	200 - ( quality << 1 )
				)
			);
			
			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			mem = new ByteArray();
			
			mem.position = 130;
			// YQT
			mem.writeUTFBytes( '\x10\x0b\x0a\x10\x18\x28\x33\x3d\x0c\x0c\x0e\x13\x1a\x3a\x3c\x37\x0e\x0d\x10\x18\x28\x39\x45\x38\x0e\x11\x16\x1d\x33\x57\x50\x3e\x12\x16\x25\x38\x44\x6d\x67\x4d\x18\x23\x37\x40\x51\x68\x71\x5c\x31\x40\x4e\x57\x67\x79\x78\x65\x48\x5c\x5f\x62\x70\x64\x67\x63' );
			// UVQT
			mem.writeUTFBytes( '\x11\x12\x18\x2f\x63\x63\x63\x63\x12\x15\x1a\x42\x63\x63\x63\x63\x18\x1a\x38\x63\x63\x63\x63\x63\x2f\x42\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63\x63' );
			
			// ZigZag
			mem.position = 1154;
			mem.writeUTFBytes( '\x00\x01\x05\x06\x0e\x0f\x1b\x1c\x02\x04\x07\x0d\x10\x1a\x1d\x2a\x03\x08\x0c\x11\x19\x1e\x29\x2b\x09\x0b\x12\x18\x1f\x28\x2c\x35\x0a\x13\x17\x20\x27\x2d\x34\x36\x14\x16\x21\x26\x2e\x33\x37\x3c\x15\x22\x25\x2f\x32\x38\x3b\x3d\x23\x24\x30\x31\x39\x3a\x3e\x3f' );
			
			mem.length += 64;
			if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
			
			_DOMAIN.domainMemory = mem;
			
			var i:int;
			var t:int;
			
			// YTable
			i = 0;
			do {
				t = ( li8( 130 + i ) * sf + 50 ) / 100;
				if ( t < 1 ) t = 1;
				else if ( t > 255 ) t = 255;
				si8( t, 1 + li8( 1154 + i ) );
			} while ( ++i < 64 );
			
			// UVTable
			i = 0;
			do {
				t = ( li8( 194 + i ) * sf + 50 ) / 100;
				if ( t < 1 ) t = 1;
				else if ( t > 255 ) t = 255;
				si8( t, 66 + li8( 1154 + i ) );
			} while ( ++i < 64 );
			
			// aasf
			sf64( 1.000000000, 1154 + 64 + 8 * 0 );
			sf64( 1.387039845, 1154 + 64 + 8 * 1 );
			sf64( 1.306562965, 1154 + 64 + 8 * 2 );
			sf64( 1.175875602, 1154 + 64 + 8 * 3 );
			sf64( 1.000000000, 1154 + 64 + 8 * 4 );
			sf64( 0.785694958, 1154 + 64 + 8 * 5 );
			sf64( 0.541196100, 1154 + 64 + 8 * 6 );
			sf64( 0.275899379, 1154 + 64 + 8 * 7 );
			
			// fdtbl_Y
			// fdtbl_UV
			var row:int;
			var col:int;
			var n:Number;
			i = 0;
			row = 0;
			do {
				col = 0;
				do {
					n = lf64( 1154 + 64 + row ) * lf64( 1154 + 64 + col ) * 8;
					sf64( 1.0 / ( li8(  1 + li8( 1154 + i ) ) * n ), 130       + ( i << 3 ) );
					sf64( 1.0 / ( li8( 66 + li8( 1154 + i ) ) * n ), 130 + 512 + ( i << 3 ) );
					++i;
					col += 8;
				} while ( col < 64 );
				row += 8;
			} while ( row < 64 );
			
			// control
			si8( 0x00,  0 );
			si8( 0x01, 65 );
			
			_DOMAIN.domainMemory = tmp;
			
			mem.length = 1154;
			_QUANTS[ quality - 1 ] = mem;
			
		}
		
		return mem;
		
	}
	
	//--------------------------------------------------------------------------
	//  JPEG table
	//--------------------------------------------------------------------------
	
	/**
	 * @see	#getJPEGTable()
	 */
	private static var _TABLE:ByteArray;
	
	/**
	 * <table>
	 *	<tr><th>     0</th><td>ZigZag					</td></tr>
	 *	<tr><th>    64</th><td>HuffmanTable				</td></tr>
	 *	<tr><th>  2058</th><td>CategoryTable			</td></tr>
	 *	<tr><th>198663</th><td>							</td></tr>
	 * </table>
	 * 
	 * @see	#generateZigZagTable()
	 * @see	#generateHuffmanTable()
	 * @see	#generateCategoryTable()
	 */
	internal static function getJPEGTable():ByteArray {
		
		if ( !_TABLE ) {

			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			_TABLE = new ByteArray();
			_TABLE.length = Math.max( 64 + 1994 + 0xFFFF * 3, ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH );

			_DOMAIN.domainMemory = _TABLE;
			
			generateZigZagTable();
			generateHuffmanTable();
			generateCategoryTable();

			_DOMAIN.domainMemory = tmp;
			
		}
		
		return _TABLE;
		
	}
	
	/**
	 * <table>
	 *	<tr><th> 0</th><td>ZigZag						</td><td>[1]{64}	</td></tr>
	 *	<tr><th>64</th><td>								</td><td>			</td></tr>
	 * </table>
	 */
	private static function generateZigZagTable():void {
		_DOMAIN.domainMemory.writeUTFBytes( '\x00\x01\x05\x06\x0e\x0f\x1b\x1c\x02\x04\x07\x0d\x10\x1a\x1d\x2a\x03\x08\x0c\x11\x19\x1e\x29\x2b\x09\x0b\x12\x18\x1f\x28\x2c\x35\x0a\x13\x17\x20\x27\x2d\x34\x36\x14\x16\x21\x26\x2e\x33\x37\x3c\x15\x22\x25\x2f\x32\x38\x3b\x3d\x23\x24\x30\x31\x39\x3a\x3e\x3f' );
	}
	
	/**
	 * <table>
	 *	<tr><th>   0</th><td>0							</td><td>[1]{1}		</td></tr>
	 *	<tr><th>   1</th><td>std_dc_luminance_nrcodes	</td><td>[1]{16}	</td></tr>
	 *	<tr><th>  17</th><td>std_dc_luminance_values	</td><td>[1]{12}	</td></tr>
	 *	<tr><th>  29</th><td>0							</td><td>[1]{1}		</td></tr>
	 *	<tr><th>  30</th><td>std_ac_luminance_nrcodes	</td><td>[1]{16}	</td></tr>
	 *	<tr><th>  47</th><td>std_ac_luminance_values	</td><td>[1]{162}	</td></tr>
	 *	<tr><th> 208</th><td>0							</td><td>[1]{1}		</td></tr>
	 *	<tr><th> 209</th><td>std_dc_chrominance_nrcodes	</td><td>[1]{16}	</td></tr>
	 *	<tr><th> 225</th><td>std_dc_chrominance_values	</td><td>[1]{12}	</td></tr>
	 *	<tr><th> 237</th><td>0							</td><td>[1]{1}		</td></tr>
	 *	<tr><th> 238</th><td>std_ac_chrominance_nrcodes	</td><td>[1]{16}	</td></tr>
	 *	<tr><th> 254</th><td>std_ac_chrominance_values	</td><td>[1]{162}	</td></tr>
	 *	<tr><th> 416</th><td>YDC_HT						</td><td>[1,2]{12}	</td></tr>
	 *	<tr><th> 452</th><td>YAC_HT						</td><td>[1,2]{251}	</td></tr>
	 *	<tr><th>1205</th><td>UVDC_HT					</td><td>[1,2]{12}	</td></tr>
	 *	<tr><th>1241</th><td>UVAC_HT					</td><td>[1,2]{251}	</td></tr>
	 *	<tr><th>1994</th><td>							</td><td>			</td></tr>
	 * </table>
	 */
	private static function generateHuffmanTable():void {
		
		var mem:ByteArray = _DOMAIN.domainMemory;
		
		var i:int;
		var arr:Vector.<uint>;

		// HTYDCinfo
		mem.writeByte( 0x00 );
		// std_dc_luminance_nrcodes
		mem.writeUTFBytes( '\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00' );
		// std_dc_luminance_values
		mem.writeUTFBytes( '\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b' );
		// HTYACinfo
		mem.writeByte( 0x10 );
		// std_ac_luminance_nrcodes
		mem.writeUTFBytes( '\x00\x02\x01\x03\x03\x02\x04\x03\x05\x05\x04\x04\x00\x00\x01\x7d' );
		// std_ac_luminance_values
		arr = new <uint>[ 0x00030201, 0x12051104, 0x06413121, 0x07615113, 0x32147122, 0x08a19181, 0xc1b14223, 0xf0d15215, 0x72623324, 0x160a0982, 0x1a191817, 0x28272625, 0x35342a29, 0x39383736, 0x4544433a, 0x49484746, 0x5554534a, 0x59585756, 0x6564635a, 0x69686766, 0x7574736a, 0x79787776, 0x8584837a, 0x89888786, 0x9493928a, 0x98979695, 0xa3a29a99, 0xa7a6a5a4, 0xb2aaa9a8, 0xb6b5b4b3, 0xbab9b8b7, 0xc5c4c3c2, 0xc9c8c7c6, 0xd4d3d2ca, 0xd8d7d6d5, 0xe2e1dad9, 0xe6e5e4e3, 0xeae9e8e7, 0xf4f3f2f1, 0xf8f7f6f5, 0x0000faf9 ];
		i = 0;
		do { si32( arr[ i ], 64 + 46 + ( i << 2 ) ) } while ( ++i < 41 );
		mem.position += 162;
		// HTUDCinfo
		mem.writeByte( 0x01 );
		// std_dc_chrominance_nrcodes
		mem.writeUTFBytes( '\x00\x03\x01\x01\x01\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00' );
		// std_dc_chrominance_values
		mem.writeBytes( mem, 64 + 17, 12 );
		// HTUACinfo
		mem.writeByte( 0x11 );
		// std_ac_chrominance_nrcodes
		mem.writeUTFBytes( '\x00\x02\x01\x02\x04\x04\x03\x04\x07\x05\x04\x04\x00\x01\x02\x77' );
		// std_ac_chrominance_values
		arr = new <uint>[ 0x03020100, 0x21050411, 0x41120631, 0x71610751, 0x81322213, 0x91421408, 0x09c1b1a1, 0xf0523323, 0xd1726215, 0x3424160a, 0x17f125e1, 0x261a1918, 0x2a292827, 0x38373635, 0x44433a39, 0x48474645, 0x54534a49, 0x58575655, 0x64635a59, 0x68676665, 0x74736a69, 0x78777675, 0x83827a79, 0x87868584, 0x928a8988, 0x96959493, 0x9a999897, 0xa5a4a3a2, 0xa9a8a7a6, 0xb4b3b2aa, 0xb8b7b6b5, 0xc3c2bab9, 0xc7c6c5c4, 0xd2cac9c8, 0xd6d5d4d3, 0xdad9d8d7, 0xe5e4e3e2, 0xe9e8e7e6, 0xf4f3f2ea, 0xf8f7f6f5, 0x0000faf9 ];
		i = 0;
		do { si32( arr[ i ], 64 + 254 + ( i << 2 ) ) } while ( ++i < 41 );
		mem.position += 162;
		
		// compute
		computeHuffmanTable( 64 +   0, 64 + 416 );	// YDC_HT
		computeHuffmanTable( 64 +  29, 64 + 452 );	// YAC_HT
		computeHuffmanTable( 64 + 208, 64 + 1205 );	// UVDC_HT
		computeHuffmanTable( 64 + 237, 64 + 1241 );	// UVAC_HT
		
		mem.position += 1578;
		
	}
	
	/**
	 * <table>
	 *	<tr><th>     0</th><td>cat						</td><td>[1,2]{65534}	</td></tr>
	 *	<tr><th>196605</th><td>							</td><td>				</td></tr>
	 * </table>
	 */
	private static function generateCategoryTable():void {
		
		var pos:int = _DOMAIN.domainMemory.position;
		
		var low:int = 1;
		var upp:int = 2;
		
		var p:int;
		var i:int;
		var l:int;
		var cat:int = 1;
		do {
			
			// Positive numbers
			i = low;
			l = upp;
			do {
				p = pos + ( 32767 + i ) * 3;
				si8( cat, p );
				si16( i, p + 1 );
			} while ( ++i < l );
			
			// Negative numbers
			i = - upp + 1;
			l = - low;
			do {
				p = pos + ( 32767 + i ) * 3;
				si8( cat, p );
				si16( upp - 1 + i, p + 1 );
			} while ( ++i <= l );
			
			low <<= 1;
			upp <<= 1;
			
		} while ( ++cat <= 15 );
		
		_DOMAIN.domainMemory.position += 196605;
		
	}
	
	/**
	 * @private
	 */
	private static function computeHuffmanTable(toRead:int, toWrite:int):void {
		
		var codeValue:int = 0;
		var pos_in_table:int = 0;
		
		var i:int;
		var j:int;
		var l:int;
		var p:int;
		
		i = 1;
		do {
			
			l = li8( toRead + i );
			j = 1;
			while ( j <= l ) {
				
				p = toWrite + li8( toRead + 17 + pos_in_table ) * 3;
				
				si8( i, p );
				si16( codeValue, p + 1 );
				
				++pos_in_table;
				++codeValue;
				++j;
				
			}
			
			codeValue <<= 1;
			
		} while ( ++i <= 16 );
		
	}
	
}