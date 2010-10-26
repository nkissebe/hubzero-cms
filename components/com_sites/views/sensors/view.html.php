<?php

/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright	Copyright 2010 by NEES
 */
// no direct access

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * 
 * 
 */
class sitesViewSensors extends JView {

    function display($tpl = null) {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $fac_name = $facility->getName();
        $fac_shortname = $facility->getShortName();

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document = &JFactory::getDocument();
        $pathway = & $mainframe->getPathway();
        $document->setTitle($fac_name);

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Sensor tab info to breadcrumb
        $pathway->addItem("Sensors", JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID));

        // Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs);

        $ssm = SensorPeer::findSensorsGroupBySensorModel($facilityID);
        $this->assignRef('ssm', $ssm);
        $this->assignRef('facilityID', $facilityID);

        $uploadCalibrations = $uploadSensors = $addNewSensor = $addNewSensorModel = $exportCalibrations = $exportSensors = $exportSensorModels = "";

        $uploadCalibrations = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=uploadsensorcalibrations&id=' . $facilityID) . '">Upload Calibrations</a>';
        $uploadSensors = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=uploadsensors&id=' . $facilityID) . '">Upload Sensors</a>';
        $addNewSensor = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=editsensor&sensorid=-1&id=' . $facilityID) . '">Add New Sensor</a>';
        $addNewSensorModel = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=editsensormodel&sensormodelid=-1&id=' . $facilityID) . '">Add New Sensor Model</a>';
        $exportCalibrations = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=exportcalibrations&facid=' . $facilityID) . '">Export Calibrations</a>';
        $exportSensors = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=exportsensors&facid=' . $facilityID) . '">Export Sensors</a>';
        $exportSensorModels = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=exportsensormodels&facid=' . $facilityID) . '">Export Sensor Models</a>';

        $addSensor = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=editsensor&sensorid=-1&id=' . $facilityID) . '">Add Sensor</a>';

        $this->assignRef('uploadCalibrations', $uploadCalibrations);
        $this->assignRef('uploadSensors', $uploadSensors);
        $this->assignRef('addNewSensor', $addNewSensor);
        $this->assignRef('addNewSensorModel', $addNewSensorModel);
        $this->assignRef('exportCalibrations', $exportCalibrations);
        $this->assignRef('exportSensors', $exportSensors);
        $this->assignRef('exportSensorModels', $exportSensorModels);
        $this->assignRef('addsensor', $addSensor);

        // See if current logged in user can edit in this facility
	$allowEdit = FacilityHelper::canEdit($facility);
	$this->assignRef('allowEdit', $allowEdit);

	$allowCreate = FacilityHelper::canCreate($facility);
	$this->assignRef('allowCreate', $allowCreate);


        // The ability to add sensor models is special, it is something that will affect all
        // facilities
        $allowAddSensorModel = FacilityHelper::canCreateSensorModel($facility);
        $this->assignRef('allowaddsensormodel', $allowAddSensorModel);


        parent::display($tpl);
    }

}
