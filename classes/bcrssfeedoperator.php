<?php
/**
 * File containing the BCRssFeedOperator class based on the eZRSSFeedOperator class.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2008 Bertrand Dunogier
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @package bcrssfeeds
 */

class BCRssFeedOperator
{
    /**
     * Return array containing operator names
     *
     * @return array Return array containing operator names.
     */
    function operatorList()
    {
        return array( 'bcrssfeed' );
    }

    /**
     * Return true to tell the template engine that the parameter list exists per operator type,
     *        this is needed for operator classes that have multiple operators.
     *
     * @return bool Return true.
     */
    function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * Return array of operator named parameters and defaults
     *
     * See: eZTemplateOperator::namedParameterList
     *
     * @return bool Return true.
     */
    function namedParameterList()
    {
        return array( 'bcrssfeed' => array( 'node' => array( 'type' => 'mixed',
                                                             'required' => false,
                                                             'default' => 0 ) ) );
    }

    /**
     * Executes the PHP function for the operator cleanup and modifies $operatorValue.
     *
     * @return bool Return true|fase based on success of operation.
     */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            // The node parameter can either be an eZContentObjectTreeNode, or a node ID
            case 'rssfeed':
            {
                $operatorValue = '';

                if ( is_numeric( $namedParameters['node'] ) )
                {
                    // If the parameter was not provided, we use node 2
                    if ( $namedParameters['node'] == 0 )
                    {
                        $node = eZContentObjectTreeNode::fetch( 2 );
                    }
                    else
                    {
                        $node = eZContentObjectTreeNode::fetch( $namedParameters['node'] );
                    }
                }
                else
                    $node = $namedParameters['node'];

                if ( !is_a( $node, 'ezcontentobjecttreenode' ) )
                    return false;

                $rssINI = eZINI::instance( 'bcrssfeeds.ini' );
                $supportedClasses = $rssINI->variable( 'Settings', 'ContentClasses' );
                $path = $node->fetchPath();
                $path[] = $node;

                // We scan the node's path up until we find a supported content class
                for ( $depth = $node->attribute( 'depth' ) - 1; $depth >= 0; $depth-- )
                {
                    $currentNode =& $path[$depth];
                    if ( in_array( $currentNode->classIdentifier(), $supportedClasses ) )
                    {
                        $dataMap = $currentNode->dataMap();

                        if ( !isset( $dataMap['rss_feed'] ) )
                        {
                            eZDebug::writeError( 'Content node #' . $node->attribute( 'node_id' ) . ' is configured as a RSS content class ('.$currentNode->classIdentifier().'), but has no \'rss_feed\' attribute', 'BCRssFeedOperator' );
                            continue;
                        }

                        if ( $dataMap['rss_feed']->hasContent() )
                        {
                            $feedObject = $dataMap['rss_feed']->content();
                            $feedObjectDataMap = $feedObject->dataMap();
                            $title = htmlspecialchars( $feedObjectDataMap['title']->content(), ENT_QUOTES );
                            $url = htmlspecialchars( $feedObjectDataMap['url']->content(), ENT_QUOTES );
                            $operatorValue = '<link rel="Alternate" type="application/rss+xml" title="'.$title.'" href="'.$url.'" />';
                            return true;
                        }
                    }
                }
            } break;
        }
    }
}

?>