<?php

	// Add meta tags
	scrylemgr::addMetaTag('generator', array('name' => 'generator', 'content' => 'Chameleon v' . CS_VERSION . ' - http://chameleon-sites.com/; Powered by the N2 Framework Yverdon v' . N2F_VERSION . ' - http://n2framework.com/'));


	// Add our copyright if we're operational
	if (n2f_cls::getInstance()->hasExtension('chameleon/config')) {
		chameleon::getInstance()->setField('CopyrightYear', date('Y'));
	}

?>