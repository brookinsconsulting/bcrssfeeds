<?php
/**
 * File containing the BCRssFeedsFunctionCollection class based on the eZRSSFunctionCollection class.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2008 Kristof Coomans
 * @copyright Copyright (C) 1999 - 2015 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version 2014.11
 * @package kernel
 * @package bcrssfeeds
 */

class BCRssFeedsFunctionCollection
{
    /**
     * Return array of valid eZRSSExport objects.
     *
     * @return eZRSSExport|false Return value is inside a array with return value on result, as this is used as template fetch function.
     */
    static function fetchList()
    {
        $result = array( 'result' => eZRSSExport::fetchList() );
        return $result;
    }

    /**
     * Return array of valid eZRSSExport objects for a specific node subtree if any exists.
     *
     * @param int $nodeID
     * @param int $maxDepth
     *
     * @return eZRSSExport|false Return value is inside a array with return value on result, as this is used as template fetch function.
     */
    static function fetchSubtreeList( $nodeID, $maxDepth = false )
    {
        if ( is_numeric( $nodeID ) )
        {
            $node = eZContentObjectTreeNode::fetch( $nodeID );
            $pathString = $node->attribute( 'path_string' );

            $subQuery = "SELECT i.rssexport_id FROM ezrss_export_item i, ezcontentobject_tree n
                         WHERE
                         i.rssexport_id=e.id AND
                         i.source_node_id=n.node_id AND
                         n.path_string LIKE '$pathString%'";

            if ( is_numeric( $maxDepth ) )
            {
                $nodeDepth = $node->attribute( 'depth' );
                $maxQueryDepth = $nodeDepth + $maxDepth;
            }

            $subQuery .= " AND n.depth <= $maxQueryDepth";

            $query = "SELECT * FROM ezrss_export e
                      WHERE EXISTS( $subQuery ) ORDER BY title";

            // eZDebug::writeDebug( $query );

            $db = eZDB::instance();
            $result = $db->arrayQuery( $query );

            $rssExports = array();
            foreach ( $result as $row )
            {
                $rssExports[] = new eZRSSExport( $row );
            }
        }
        else
        {
            $rssExports = array();
        }
        return array( 'result' => $rssExports );
    }

    /**
     * Checks if there is a valid RSS/ATOM Feed export for a node or not.
     *
     * @param int $nodeID
     * @return bool Return value is inside a array with return value on result, as this is used as template fetch function.
     */
    static function hasExportByNode( $nodeID )
    {
        if ( !$nodeID )
            return array( 'error' => array( 'error_type' => 'kernel',
                                            'error_code' => eZError::KERNEL_NOT_FOUND ) );

        $db = eZDB::instance();
        $res = $db->arrayQuery( "SELECT id FROM ezrss_export WHERE node_id='$nodeID' AND status=" . eZRSSExport::STATUS_VALID );

        return array( 'result' => isset( $res[0] ) ? true : false );
    }

    /**
     * Return valid eZRSSExport object for a specific node if it exists.
     *
     * @param int $nodeID
     * @return eZRSSExport|false Return value is inside a array with return value on result, as this is used as template fetch function.
     */
    static function exportByNode( $nodeID )
    {
        if ( !$nodeID )
            return array( 'error' => array( 'error_type' => 'kernel',
                                            'error_code' => eZError::KERNEL_NOT_FOUND ) );

        $rssExport = eZPersistentObject::fetchObject( eZRSSExport::definition(),
                                                null,
                                                array( 'node_id' => $nodeID,
                                                       'status' => eZRSSExport::STATUS_VALID ),
                                                true );

        return array( 'result' => $rssExport );
    }
}

?>