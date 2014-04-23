<?php

/**
 * @author Matthew McNaney <mcnaney at gmail dot com>
 * @version $Id$
 */

function carousel_update(&$content, $version)
{
    switch ($version) {
        case version_compare($version, '1.1.0', '<'):
            $db = \Database::newDB();
            $t1 = $db->addTable('caro_slide');
            $dt = $t1->addDataType('url', 'text');
            $dt->add();

            $content[] = '<pre>1.1.0
--------------------
+ Slide is linkable.
</pre>';
        case version_compare($version, '1.2.0', '<'):
            $content[] = '<pre>1.2.0
--------------------
+ Can now associate a slide to a page.
</pre>';
    } // end of switch

    return true;
}

?>