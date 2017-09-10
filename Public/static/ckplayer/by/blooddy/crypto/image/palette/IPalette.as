////////////////////////////////////////////////////////////////////////////////
//
//  (C) 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image.palette {

	/**
	 * The base interface for creating color palletes to be used in <code>PNG8Encoder</code>.
	 * 
	 * @see						by.blooddy.crypto.image.PNG8Encoder
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					27.06.2010 22:35:21
	 */
	public interface IPalette {

		//--------------------------------------------------------------------------
		//
		//  Properties
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @copy	flash.display.BitmapData#transparent
		 */
		function get transparent():Boolean;
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Returns the list of the colors used in the palette.
		 * 
		 * @return	All the colors used in the palette.
		 * 
		 * @see		#getIndexByColor()
		 */
		function getList():Vector.<uint>;

		/**
		 * Returns the index of the color inside the palette. 
		 * It is possible that the same index will be returned for different color values.
		 * 
		 * @param	color			The color you wish to index.
		 * 
		 * @return					The index of the color inside the palette.
		 * 
		 * @throws	ArgumentError	If the color was not found inside the palette.
		 * 
		 * @see		#getList()
		 */
		function getIndexByColor(color:uint):uint;

	}

}