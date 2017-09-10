////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.process {

	import flash.system.ApplicationDomain;
	import flash.utils.getQualifiedClassName;
	import flash.utils.setTimeout;

	[ExcludeClass]
	/**
	 * @internal
	 * Timer implementation.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					25.03.2016 17:07:01
	 */
	internal final class Process$Consistent implements Process$ {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 */
		internal static const instance:Process$Consistent = new Process$Consistent();
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		public function Process$Consistent() {
			if ( !instance ) {
				super();
			} else {
				Error.throwError( ArgumentError, 2012, getQualifiedClassName( this ) );
			}
		}

		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @inheritDoc
		 */
		public function process(WorkerClass:Class, defenitionName:String, methodName:String, args:Array, success:Function, fault:Function):void {
			setTimeout( function():void {
				try {

					var defenition:Object = ApplicationDomain.currentDomain.getDefinition( defenitionName );
					success( defenition[ methodName ].apply( defenition, args ) );

				} catch ( e:Error ) {

					fault( e );

				}
			}, 0 );
		}
		
	}

}