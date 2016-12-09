<?php
/**
 * Created by IntelliJ IDEA.
 * User: layke
 * Date: 08/12/16
 * Time: 21:29
 */

namespace AppBundle\Helper\Importer\ValueConverter;

use Ddeboer\DataImport\Exception\UnexpectedValueException;

/**
 * @author Grégoire Paris
 */
class ForceToNullValueConverter
{
	/**
	 * @param array $mapping
	 */
	public function __construct()
	{
		
	}
	
	public function __invoke($input)
	{
		return null;
	}
}
