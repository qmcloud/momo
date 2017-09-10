////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.process {

	[ExcludeClass]
	/**
	 * @internal
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					25.03.2016 17:11:05
	 */
	internal interface Process$ {

		/**
		 * @param	WorkerClass		worker class
		 * @param	defenitionName	definition
		 * @param	methodName		definition methods
		 * @param	arguments		method arguments
		 * @param	success			success callback
		 * @param	fault			fault callback
		 */
		function process(WorkerClass:Class, defenitionName:String, methodName:String, arguments:Array, success:Function, fault:Function):void;
		
	}

}