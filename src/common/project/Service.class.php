<?php

/**
* Service
* 
* TODO: description
* 
* Copyright (c) Xerox Corporation, CodeX Team, 2001-2007. All rights reserved
*
* @author  N. Terray
*/
class Service {
    
    var $data;
	
    /**
    * Constructor
    */
    function Service($data) {
        $this->data = $data;
    }
    
    function getId() {
        return $this->data['service_id'];
    }
    function getDescription() {
        return $this->data['description'];
    }
    function getShortName() {
        return $this->data['short_name'];
    }
    function getLabel() {
        return $this->data['label'];
    }
    function getRank() {
        return $this->data['rank'];
    }
    function isUsed() {
        return $this->data['is_used'];
    }
    function isActive() {
        return $this->data['is_active'];
    }
    function getServerId() {
        return $this->data['server_id'];
    }
    function getLocation() {
        return $this->data['location'];
    }
    function getUrl() {
        $url = $this->data['link'];
        if (!$this->isAbsolute($url) && $this->getLocation() != 'same') {
            $sf =& $this->_getServerFactory();
            if ($s =& $sf->getServerById($this->getServerId())) {
                $url = $s->getUrl() . $url;
            }
        }
        return $url;
    }
    function &_getServerFactory() {
        return new ServerFactory();
    }
    
    /**
    * @see http://www.ietf.org/rfc/rfc2396.txt Annex B
    */
    function isAbsolute($url) {
        $components = array();
        preg_match('`^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?`i', $url, $components);
        return isset($components[1]) && $components[1] ? true : false;
    }
}

?>
