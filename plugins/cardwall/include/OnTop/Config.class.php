<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'Dao.class.php';
require_once 'Config/ColumnFactory.class.php';
require_once 'Config/TrackerMappingFactory.class.php';
require_once 'Config/ColumnCollection.class.php';
require_once CARDWALL_BASE_DIR. '/Mapping.class.php';

/**
 * Manage configuration of a cardwall on top of a tracker
 */
class Cardwall_OnTop_Config {

    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * @var Cardwall_OnTop_Dao
     */
    private $dao;

    /**
     * @var Cardwall_OnTop_Config_ColumnFactory
     */
    private $column_factory;

    /**
     * @var Cardwall_OnTop_Config_TrackerMappingFactory
     */
    private $tracker_mapping_factory;

    public function __construct(
        Tracker $tracker,
        Cardwall_OnTop_Dao $dao,
        Cardwall_OnTop_Config_ColumnFactory $column_factory,
        Cardwall_OnTop_Config_TrackerMappingFactory $tracker_mapping_factory
    ) {
        $this->tracker                 = $tracker;
        $this->dao                     = $dao;
        $this->column_factory          = $column_factory;
        $this->tracker_mapping_factory = $tracker_mapping_factory;
    }

    public function getTracker() {
        return $this->tracker;
    }

    public function isEnabled() {
        return $this->dao->isEnabled($this->tracker->getId());
    }

    public function enable() {
        return $this->dao->enable($this->tracker->getId());
    }

    public function disable() {
        return $this->dao->disable($this->tracker->getId());
    }

    /**
     * Get Frestyle columns for Cardwall_OnTop, or status columns if none
     * 
     * @param Tracker $tracker
     * @return Cardwall_OnTop_Config_ColumnCollection
     */
    public function getDashboardColumns() {
        return $this->column_factory->getDashboardColumns($this->tracker);
    }

    /**
     * Get Columns from the values of a $field
     * @return Cardwall_OnTop_Config_ColumnCollection
     */
    public function getRendererColumns(Tracker_FormElement_Field_List $cardwall_field) {
        return $this->column_factory->getRendererColumns($cardwall_field);
    }
    
    public function getMappings() {
        return $this->tracker_mapping_factory->getMappings($this->tracker, $this->getDashboardColumns());
    }

    public function getTrackers() {
        return $this->tracker_mapping_factory->getTrackers($this->tracker);
    }
    
    /**
     * @param Tracker $mapping_tracker
     * 
     * @return Cardwall_OnTop_Config_TrackerMapping
     */
    public function getMappingFor(Tracker $mapping_tracker) {
        $mappings = $this->getMappings();
        return isset($mappings[$mapping_tracker->getId()]) ? $mappings[$mapping_tracker->getId()] : null;
    }
    
    private function isMappedTo($tracker, $artifact_status, Cardwall_Column $column) {
        // TODO null object pattern, to return empty valuemappings
        $tracker_field_mapping = $this->getMappingFor($tracker);
        if (!$tracker_field_mapping) return false;
        
        return $tracker_field_mapping->isMappedTo($column, $artifact_status);
    }

    public function isInColumn(Tracker_Artifact                                     $artifact, 
                               Cardwall_FieldProviders_IProvideFieldGivenAnArtifact $field_provider, 
                               Cardwall_Column                                      $column) {
        $field           = $field_provider->getField($artifact);
        $artifact_status = null;
        if ($field) {
            $artifact_status = $field->getFirstValueFor($artifact->getLastChangeset());
        }
        
        return $this->isMappedTo($artifact->getTracker(), $artifact_status, $column) || 
               $column->isMatchForThisColumn($artifact_status);
    }

    /**
     * Get the column/field/value mappings by duck typing the colums labels 
     * with the values of the given fields
     *
     * @param array $fields array of Tracker_FormElement_Field_Selectbox
     *
     * @return Cardwall_MappingCollection
     */
    public function getCardwallMappings(array $fields, Cardwall_OnTop_Config_ColumnCollection $cardwall_columns) {
        $mappings = new Cardwall_MappingCollection();
        $this->fillMappingsByDuckType($mappings, $fields, $cardwall_columns);
        $this->fillMappingsWithOnTopMappings($mappings, $cardwall_columns);
        return $mappings;
    }
    
    private function fillMappingsByDuckType(Cardwall_MappingCollection             $mappings, 
                                            array                                  $fields, 
                                            Cardwall_OnTop_Config_ColumnCollection $columns) {
        foreach ($fields as $status_field) {
            foreach ($status_field->getVisibleValuesPlusNoneIfAny() as $value) {
                $column = $columns->getColumnByLabel($value->getLabel());
                if ($column) {
                    $mappings->add(new Cardwall_Mapping($column->id, $status_field->getId(), $value->getId()));
                }

            }
        }
        return $mappings;
    }

    public function fillMappingsWithOnTopMappings(Cardwall_MappingCollection             $mappings, 
                                                  Cardwall_OnTop_Config_ColumnCollection $columns) {
        foreach ($this->getMappings() as $field_mapping) {
            foreach ($field_mapping->getValueMappings() as $value_mapping) {
                $column = $columns->getColumnById($value_mapping->getColumnId());
                if ($column) {
                    $value = $value_mapping->getValue();
                    $mapped_field = $field_mapping->getField();
                    $mappings->add(new Cardwall_Mapping($column->id, $mapped_field->getId(), $value->getId()));
                }
            }
        }
    }

    

}
?>