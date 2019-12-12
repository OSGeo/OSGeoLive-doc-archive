<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License
# and Simplified BSD license.
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/class_Filter.php";
require_once dirname(__FILE__) . "/../http/classes/class_wfs_configuration.php";
require_once dirname(__FILE__) . "/../http/classes/class_universal_gml_factory.php";
require_once dirname(__FILE__) . "/../http/classes/class_universal_wfs_factory.php";


/**
 * Description of class_OgcFilter
 *
 * @author cbaudson
 */
class OgcFilter extends Filter {
	const SPATIAL_OPERATORS = "Intersects";

	private function mapOperator ($op) {
		switch ($op) {
			case "LIKE":
				return array(
					"open" => 'ogc:PropertyIsLike wildCard="*" singleChar="#" escapeChar="!"',
					"close" => 'ogc:PropertyIsLike'
				);
				break;
			case "AND":
				return array(
					"open" => 'ogc:And',
					"close" => 'ogc:And'
				);
				break;
			default:
				return array(
					"open" => $op,
					"close" => $op
				);
		}
	}
	public function __construct () {
		$allOperators = implode(",", array(self::OPERATORS, self::SPATIAL_OPERATORS));
		if (func_num_args() >= 3) {
			$this->operator = func_get_arg(0);
			$this->key = func_get_arg(1);
			$this->value = func_get_arg(2);
			if (func_num_args() >= 4) {
				$this->wfsConf = func_get_arg(3);
				if (!is_a($this->wfsConf, "WfsConfiguration")) {
					throw new Exception ("OgcFilter: wfsConf is not a WFS Configuration.");
				}
			}
			if (!in_array($this->operator, explode(",", $allOperators))) {
				throw new Exception ("OgcFilter: Invalid operator " . $this->operator);
			}
		}
		else if (func_num_args() === 2) {
			$logicalOp = func_get_arg(0);
			$filterArray = func_get_arg(1);
			return parent::__construct($logicalOp, $filterArray);
		}
		else {
			throw new Exception("OgcFilter: Insufficient arguments.");
		}
	}

	public function toXmlNoWrap() {
		if ($this->isComplex()) {
			$str = "";
			foreach ($this->filterArray as $filter) {
				$str .= $filter->toXmlNoWrap();
			}
			$op = $this->mapOperator($this->booleanOperator);
			return "<" . $op["open"] . ">" . $str . "</" . $op["close"] . ">";
		}
		else {
			$k = "<ogc:PropertyName>" . $this->key . "</ogc:PropertyName>";
			if (in_array($this->operator, explode(",", self::SPATIAL_OPERATORS))) {
				$gmlFactory = new UniversalGmlFactory();
				$gml = $gmlFactory->createFromGeoJson($this->value, $this->wfsConf);
				$v = $gml->toGml();
			}
			else {
				$v = "<ogc:Literal>" . $this->value . "</ogc:Literal>";
			}
			$op = $this->mapOperator($this->operator);
			return "<" . $op["open"] . ">" . $k . $v . "</" . $op["close"] . ">";
		}
	}
	
	public function toXml () {
		return "<ogc:Filter>" . $this->toXmlNoWrap() . "</ogc:Filter>";
	}

}
?>
