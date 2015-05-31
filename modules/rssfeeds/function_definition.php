<?php
/**
 * File containing the rssfeeds module function definition.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2008 Kristof Coomans
 * @copyright Copyright (C) 1999 - 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bcrssfeeds
 */

$FunctionList = array();

$FunctionList['list'] = array( 'name' => 'list',
                               'operation_types' => 'read',
                               'call_method' => array( 'class' => 'BCRssFeedsFunctionCollection',
                                                       'method' => 'fetchList' ),
                               'parameter_type' => 'standard',
                               'parameters' => array() );

$FunctionList['subtree_list'] = array( 'name' => 'subtree_list',
                                       'operation_types' => 'read',
                                       'call_method' => array( 'class' => 'BCRssFeedsFunctionCollection',
                                                               'method' => 'fetchSubtreeList' ),
                                       'parameter_type' => 'standard',
                                       'parameters' => array( array( 'name' => 'node_id',
                                                                     'type' => 'integer',
                                                                     'required' => true ),
                                                              array( 'name' => 'max_depth',
                                                                     'type' => 'integer',
                                                                     'required' => false ) ) );

$FunctionList['has_export_by_node'] = array( 'name' => 'has_node_map',
                               'operation_types' => array( 'read' ),
                               'call_method' => array( 'class' => 'BCRssFeedsFunctionCollection',
                                                       'method' => 'hasExportByNode' ),
                               'parameter_type' => 'standard',
                               'parameters' => array( array( 'name' => 'node_id',
                                                             'type' => 'integer',
                                                             'required' => true ) ) );

$FunctionList['export_by_node'] = array( 'name' => 'node_map',
                                   'operation_types' => array( 'read' ),
                                   'call_method' => array( 'class' => 'BCRssFeedsFunctionCollection',
                                                           'method' => 'exportByNode' ),
                                   'parameter_type' => 'standard',
                                   'parameters' => array( array( 'name' => 'node_id',
                                                                 'type' => 'integer',
                                                                 'required' => true ) ) );

?>