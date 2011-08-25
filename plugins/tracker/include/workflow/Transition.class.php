<?php
/**
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi. If not, see <http://www.gnu.org/licenses/>.
 */

class Transition {
    public $transition_id;
    public $workflow_id;
    public $from;
    public $to;
    
    
    public function __construct($transition_id, $workflow_id, $from, $to) {
        $this->transition_id = $transition_id;
        $this->workflow_id      = $workflow_id;
        $this->from = $from;
        $this->to   = $to;
    }
    
    public function getFieldValueFrom() {
        return $this->from;
    }
    
    public function getFieldValueTo() {
        return $this->to;
    }
    
    public function equals($transition) {
        return $transition->getFieldValueFrom() === $this->from && $transition->getFieldValueTo() === $this->to;
    }
    
    public function getTransitionId() {
        return $this->transition_id;
    }
    
    public function getWorkflow() {
        return WorkflowFactory::instance()->getWorkflow($this->workflow_id);
    }
    
    public function displayTransitionDetails() {
    }
}
?>
