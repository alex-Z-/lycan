<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserBrandRegistry;
use AppBundle\Helper\Importer\ValueConverter\ArrayValueConverterMap;
use AppBundle\Helper\Importer\ValueConverter\MappingValueConverter;
use AppBundle\Helper\Importer\ValueConverter\ForceToNullValueConverter;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Helper\Importer\Step\ConverterStep;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Helper\CSVTypes;
use Ddeboer\DataImport\Workflow\StepAggregator;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Workflow;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Ddeboer\DataImport\Step;
class UpsertCSVController extends Controller
{
	
	public function importFileAction(Request $request) {
		
		// Get FileId to "import"
		$param=$request->request;
		
		$curType=trim($param->get("fileType"));
		$uploadedFile=$request->files->get("csvFile");
		
		// if upload was not ok, just redirect to "shortyStatWrongPArameters"
		if (!CSVTypes::existsType($curType) || $uploadedFile==null)  {
			$this->addFlash('sonata_flash_error', "You have not attached a CSV file to upload.");
			return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
		}
		
		// generate dummy dir
		$dummyImport=getcwd()."/dummyImport";
		$fname="directly.csv";
		$filename= $dummyImport . "/" . $fname;
		@mkdir($dummyImport);
		@unlink($filename);
		
		// move file to dummy filename
		$uploadedFile->move($dummyImport,$fname);
		
		// open file
		$file = new \SplFileObject($filename);
		
		// Create and configure the reader
		$csvReader = new CsvReader($file,",");
		if ($csvReader===false) die("Can't create csvReader $filename");
		$csvReader->setHeaderRowNumber(0);
		
		// JEBUS. No idea why this was neccessary.
		$fields = array_map(function (&$item) {
			return str_replace( "?", "", mb_convert_encoding($item, "ASCII", mb_detect_encoding($item)));
		},  $csvReader->getFields());
		
		// FIX ME. UTF8 char. "id" !=== "id".
		if( false && in_array( mb_convert_encoding("id", 'UTF-8'),  $fields) ){
			$this->addFlash('sonata_flash_error', "Upserting is currently disabled.");
			return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
		}
	
		// this must be done to import CSVs where one of the data-field has CRs within!
		$file->setFlags(\SplFileObject::READ_CSV |
						\SplFileObject::SKIP_EMPTY |
						\SplFileObject::READ_AHEAD);
		
		// Set Database into "nonchecking Foreign Keys"
		$em=$this->getDoctrine()->getManager();
		$em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=0;");
	
		// Create the workflow
		$stepper = new StepAggregator($csvReader);
		
		if ($stepper === false) {
			$this->addFlash('sonata_flash_error', "Failed to create a workflow for uploading file.");
			return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
		}
		
		$curEntityClass=CSVTypes::getEntityClass($curType);
		
		$writer = new DoctrineWriter($em, $curEntityClass);
		$writer->setTruncate(false);
		
		$entityMetadata=$em->getClassMetadata($curEntityClass);
		
		$entityMetadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
		
	
		$stepper->addWriter($writer);
		
		// Remove ID
		$itemConverterStep = new ConverterStep();
		
		$itemConverterStep->add(function ($item) use($em, $entityMetadata, $param)  {
			// Only create a user if it does not already exist...
			if($param->get("createNewUser")) {
				$createUser = true;
				if (isset($item['id'])) {
					$createUser = !(bool)$em->find('CoreBundle:ProviderAuthBase', $item['id']);
				}
				
				if ($createUser) {
					// Create a new User and attach to each.
					$owner = new User();
					
					$prefix = $entityMetadata->newInstance()
						->getProviderName();
					if (isset($item['nickname'])) {
						$prefix = $item['nickname'] . "_" . $prefix;
					}
					$uniq = uniqid($prefix . "_");
					
					$owner->setEmail($prefix . "@example.com");
					$owner->setUsername($prefix);
					$owner->setPlainPassword($uniq);
					
					
					if ($param->get("form")['brands']) {
						$addToBrands   = $param->get("form")['brands'];
						$brandRegistry = new UserBrandRegistry();
						$brandRegistry->setMember($owner);
						// Single
						$brandRegistry->setBrand($em->find("AppBundle:Brand", $addToBrands));
						$em->persist($brandRegistry);
					}
					
					
					$item['owner'] = $owner;
				}
				
				
			}
			
			if ($param->get("form")['brands']) {
				$addToBrands   = $param->get("form")['brands'];
				$item['autoMappedToBrands'] = [$addToBrands];
			}
			return $item;
		});
		
		
		$stepper->addStep($itemConverterStep);
		
		
		// Fix the dates
		$dateTimeConverter = new DateTimeValueConverter('D, d M Y H:i:s e');
		$at = new Step\ValueConverterStep();
		$at->add('[createdAt]', $dateTimeConverter);
		$at->add('[updatedAt]', $dateTimeConverter);
		$at->add('[lastPullCompletedAt]', $dateTimeConverter);
		$at->add('[lastPullStartedAt]', $dateTimeConverter);
		$at->add('[lastPushCompletedAt]', $dateTimeConverter);
		$at->add('[lastPushStartedAt]', $dateTimeConverter);
		$at->add('[lastValidatedCredentialsAt]', $dateTimeConverter);
		

		$stepper->addStep( $at );
		
		// Cast Values
		$valueConverter = new MappingValueConverter([
			'1' => true,
			''	=> null
		]);
		$forceNulls = new ForceToNullValueConverter();
		
		$valueConvertStep = new Step\ValueConverterStep();
		$valueConvertStep->add('[pushInProgress]', $forceNulls);
		$valueConvertStep->add('[pullInProgress]', $forceNulls);
		$valueConvertStep->add('[allowPush]', $valueConverter);
		$valueConvertStep->add('[isValidCredentials]', $valueConverter);
		$valueConvertStep->add('[supportsRealTimePricing]', $valueConverter);
		$stepper->addStep( $valueConvertStep );
		
		
		$result = $stepper->process();
		if($result->getSuccessCount() > 0){
			if($result->getTotalProcessedCount() !== $result->getSuccessCount()){
				$this->addFlash('sonata_flash_success', sprintf("You have successfully imported and/or updated %s records.", $result->getTotalProcessedCount()));
			} else {
				$this->addFlash('sonata_flash_success', sprintf("You have successfully imported %s records.", $result->getSuccessCount()));
			}
		}
			
		if($result->hasErrors()){
			$this->addFlash('sonata_flash_error', "There were unknown errors while importing.");
		}
		
		
		// RESetting Database Check Status
		$em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=1;");
		
		// After successfully import, some files need special treatment --> Reset some DB fields
		
		return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
		
	}
	
}