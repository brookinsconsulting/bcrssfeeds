<?php
class eZRSSFeedOperator
{
    /*!
      return an array with the template operator name.
    */
    function operatorList()
    {
        return array( 'rssfeed' );
    }
    /*!
     \return true to tell the template engine that the parameter list exists per operator type,
             this is needed for operator classes that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return array( 'rssfeed' => array( 'node' => array( 'type' => 'mixed',
                                                           'required' => false,
                                                           'default' => 0 ) ) );
    }
    /*!
     Executes the PHP function for the operator cleanup and modifies \a $operatorValue.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {

        switch ( $operatorName )
        {
            // the node parameter can either be an eZContentObjectTreeNode, or a node ID
            case 'rssfeed':
            {
                $operatorValue = '';

                if ( is_numeric( $namedParameters['node'] ) )
                {
                    // if the parameter was not provided, we use node 2
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

                $rssINI = eZINI::instance( 'rssfeedoperator.ini' );
                $supportedClasses = $rssINI->variable( 'Settings', 'ContentClasses' );
                $path = $node->fetchPath();
                $path[] = $node;

                // we scan the node's path up until we find a supported content class
                for ( $depth = $node->attribute( 'depth' ) - 1; $depth >= 0; $depth-- )
                {
                    $currentNode =& $path[$depth];
                    if ( in_array( $currentNode->classIdentifier(), $supportedClasses ) )
                    {
                        $dataMap = $currentNode->dataMap();

                        if ( !isset( $dataMap['rss_feed'] ) )
                        {
                            eZDebug::writeError( 'Content node #' . $node->attribute( 'node_id' ) . ' is configured as a RSS content class ('.$currentNode->classIdentifier().'), but has no \'rss_feed\' attribute', 'eZRSSFeedOperator' );
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