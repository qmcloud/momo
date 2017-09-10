////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image {

	import flash.display.BitmapData;
	import flash.geom.Point;
	
	[Exclude( kind="method", name="isTransparent" )]
	/**
	 * @internal
	 * Углубленный способ проверки прозрачности. флаг прозрачности может стоять,
	 * но картинка может быть не прозрачна. немного теряем в скорости на прозрачных
	 * картинках, зато выйигрываем с установленным флагом в ~5 раз.
	 *
	 * @param	image			Image
	 *
	 * @return					Is transparent.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					Mar 25, 2016 10:00:22 PM
	 */
	public function isTransparentImage(image:BitmapData):Boolean {
		return image.transparent && (
			image.getPixel32( 0,               0                ) < 0xFF000000 ||
			image.getPixel32( image.width - 1, 0                ) < 0xFF000000 ||
			image.getPixel32( image.width - 1, image.height - 1 ) < 0xFF000000 ||
			image.getPixel32( 0,               image.height - 1 ) < 0xFF000000 ||
			image.clone().threshold( image, image.rect, new Point(), '!=', 0xFF000000, 0, 0xFF000000, true ) != 0
		);
	}
	
}