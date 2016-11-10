<?php
namespace Pristine\Enums;

class Suitability {
	const PREFIX = "SUITABILITY_";
	const    SUITABILITY_CHILDREN_ASK = self::PREFIX . "CHILDREN_ASK";
	const    SUITABILITY_CHILDREN_WELCOME = self::PREFIX . "CHILDREN_WELCOME";
	const    SUITABILITY_CHILDREN_NOT_ALLOWED = self::PREFIX . "CHILDREN_NOT_ALLOWED";
	const    SUITABILITY_PETS_ALLOWED = self::PREFIX . "PETS_ALLOWED";
	const    SUITABILITY_PETS_ASK = self::PREFIX . "PETS_ASK";
	const    SUITABILITY_PETS_ALLOWED_WITH_RESTRICTIONS = self::PREFIX . "PETS_ALLOWED_WITH_RESTRICTIONS";
	const    SUITABILITY_PETS_NOT_ALLOWED = self::PREFIX . "PETS_NOT_ALLOWED";
	const    SUITABILITY_SMOKING_ASK = self::PREFIX . "SMOKING_ASK";
	const    SUITABILITY_SMOKING_ALLOWED = self::PREFIX . "SMOKING_ALLOWED";
	const    SUITABILITY_SMOKING_ALLOWED_WITH_RESTRICTIONS = self::PREFIX . "SMOKING_ALLOWED_WITH_RESTRICTIONS";
	const    SUITABILITY_SMOKING_NOT_ALLOWED = self::PREFIX . "SMOKING_NOT_ALLOWED";
	const    SUITABILITY_ACCESSIBILITY_ASK = self::PREFIX . "ACCESSIBILITY_ASK";
	const    SUITABILITY_ACCESSIBILITY_ELDERLY_GREAT = self::PREFIX . "ACCESSIBILITY_ELDERLY_GREAT";
	const    SUITABILITY_ACCESSIBILITY_ELDERLY_LIMITED_ACCESSIBILITY = self::PREFIX . "ACCESSIBILITY_ELDERLY_LIMITED_ACCESSIBILITY";
	const    SUITABILITY_ACCESSIBILITY_ELDERLY_NOT_RECOMMENDED = self::PREFIX . "ACCESSIBILITY_ELDERLY_NOT_RECOMMENDED";
	const    SUITABILITY_ACCESSIBILITY_WHEELCHAIR_GREAT = self::PREFIX . "ACCESSIBILITY_WHEELCHAIR_GREAT";
	const    SUITABILITY_ACCESSIBILITY_WHEELCHAIR_LIMITED_ACCESSIBILITY = self::PREFIX . "ACCESSIBILITY_WHEELCHAIR_LIMITED_ACCESSIBILITY";
	const    SUITABILITY_ACCESSIBILITY_WHEELCHAIR_NOT_ACCESSIBLE = self::PREFIX . "ACCESSIBILITY_WHEELCHAIR_NOT_ACCESSIBLE";
	const    SUITABILITY_SENIOR_ADULTS_ONLY = self::PREFIX . "SENIOR_ADULTS_ONLY";
	const    SUITABILITY_GREAT_FOR_SAME_SEX = self::PREFIX . "GREAT_FOR_SAME_SEX";
	const    SUITABILITY_EVENTS_CONSIDERED = self::PREFIX . "EVENTS_CONSIDERED";
	const    SUITABILITY_EVENTS_ALLOWED = self::PREFIX . "EVENTS_ALLOWED";
	const    SUITABILITY_WEDDINGS_CONSIDERED = self::PREFIX . "WEDDINGS_CONSIDERED";
	const    SUITABILITY_WEDDINGS_WELCOME = self::PREFIX . "WEDDINGS_WELCOME";
	const    SUITABILITY_LONG_TERM_RENTERS = self::PREFIX . "LONG_TERM_RENTERS";
	const    SUITABILITY_GROUPS_ALLOWED = self::PREFIX . "GROUPS_ALLOWED";
	const    SUITABILITY_GROUPS_ASK = self::PREFIX . "GROUPS_ASK";
	const    SUITABILITY_GROUPS_NOT_ALLOWED = self::PREFIX . "GROUPS_NOT_ALLOWED";
}