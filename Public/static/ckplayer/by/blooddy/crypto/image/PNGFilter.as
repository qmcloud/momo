////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image {

	import flash.utils.getQualifiedClassName;

	/**
	 * This is an enumerator class containing filter constants as defined 
	 * in PNG-Filter specification.
	 * 
	 * @see https://www.w3.org/Graphics/PNG/    W3C
	 * @see by.blooddy.crypto.image.PNGEncoder
	 * @see by.blooddy.crypto.image.PNG8Encoder
	 * @see by.blooddy.crypto.image.PNG24Encoder
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					25.06.2010 16:18:02
	 */
	public final class PNGFilter {

		//--------------------------------------------------------------------------
		//
		//  Class constants
		//
		//--------------------------------------------------------------------------

		/**
		 * Does not modify the scanline.
		 */
		public static const NONE:int =		0;

		/**
		 * Writes the byte as a difference of the given pixel and the previous pixel:
		 * <pre>Sub(x) = Raw(x) - Raw(x-bpp)</pre>
		 * where 
		 * <ul>
		 * <li><code>x</code> ranges from zero to the number of bytes representing the scanline 
		 * minus one,</li> 
		 * <li><code>Raw(x)</code> refers to the raw data byte at that byte position in the 
		 * scanline, and bpp is defined as the number of bytes per complete pixel, 
		 * rounding up to one. 
		 * For example, for color type 2 with a bit depth of 16, bpp is equal to 6 
		 * (three samples, two bytes per sample);</li> 
		 * <li>for color type 0 with a bit depth of 2, <code>bpp</code> is equal 
		 * to 1 (rounding up); 
		 * for color type 4 with a bit depth of 16, <code>bpp</code> is equal to 4 
		 * (two-byte grayscale sample, plus two-byte alpha sample).</li>
		 * </ul>
		 */
		public static const SUB:int =		1;

		/**
		 * Writes the byte as a difference of the given pixel and the pixel above it:
		 * <pre>Up(x) = Raw(x) - Prior(x)</pre>
		 * where 
		 * <ul>
		 * <li><code>x</code> ranges from zero to the number of bytes representing 
		 * the scanline minus one,</li> 
		 * <li><code>Raw(x)</code> refers to the raw data byte at that byte position 
		 * in the scanline, </li>
		 * <li>and <code>Prior(x)</code> refers to the unfiltered bytes of the prior scanline.</li>
		 * <ul>
		 */
		public static const UP:int =		2;

		/**
		 * Writes the byte based on the average calculated from the pixel above and 
		 * to the left of the given pixel.
		 * <pre>Average(x) = Raw(x) - floor((Raw(x-bpp)+Prior(x))/2)</pre>
		 * where 
		 * <ul>
		 * <li><code>x</code> ranges from zero to the number of bytes representing 
		 * the scanline minus one,</li> 
		 * <li><code>Raw(x)</code> refers to the raw data byte at that byte position 
		 * in the scanline, </li>
		 * <li><code>Prior(x)</code> refers to the unfiltered bytes of the prior 
		 * scanline, and bpp is defined as for the Sub filter.</li>
		 * </ul>
		 */
		public static const AVERAGE:int =	3;

		/**
		 * Writes the byte based on the output of a linear function that takes upper
		 * pixel, left neighbor pixel and upper-left pixels for an input. This 
		 * function was named after it's inventor Alan Paeth.
		 * <pre>Paeth(x) = Raw(x) - PaethPredictor(Raw(x-bpp), Prior(x), Prior(x-bpp))</pre>
		 * The function istelf may be presented in pseudocode as follows:
		 * <pre>function PaethPredictor (a, b, c)
		 *    begin
		 *         ; a = left, b = above, c = upper left
		 *         p := a + b - c        ; initial estimate
		 *         pa := abs(p - a)      ; distances to a, b, c
		 *         pb := abs(p - b)
		 *         pc := abs(p - c)
		 *         ; return nearest of a,b,c,
		 *         ; breaking ties in order a,b,c.
		 *         if pa <= pb AND pa <= pc then return a
		 *         else if pb <= pc then return b
		 *         else return c
		 *    end</pre>
		 * The calculations within the PaethPredictor function must be performed 
		 * exactly, without overflow. Arithmetic modulo 256 is to be used only for 
		 * the final step of subtracting the function result from the target byte value. 
		 */
		public static const PAETH:int =		4;

		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------

		/**
		 * @private
		 * @constructor
		 */
		public function PNGFilter() {
			Error.throwError( ArgumentError, 2012, getQualifiedClassName( this ) );
		}

	}

}