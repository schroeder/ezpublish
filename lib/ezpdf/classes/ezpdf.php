<?php
//
// Created on: <26-Aug-2003 15:15:32 kk>
//
// Copyright (C) 1999-2003 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file eztemplateautoload.php
*/

include_once( 'lib/ezpdf/classes/class.ezpdftable.php' );
include_once( 'lib/ezpdf/classes/class.pdf.php' );

include_once( 'lib/ezfile/classes/ezfile.php' );
include_once( 'lib/ezutils/classes/eztexttool.php' );

/*!
  \class eZPDF ezpdf.php
  \brief The class eZPDF does

*/

class eZPDF
{

    /*!
     Initializes the object with the name $name, default is "attribute".
    */
    function eZPDF( $name = "pdf" )
    {
        $this->Operators = array( $name );
    }

    /*!
     Returns the template operators.
    */
    function &operatorList()
    {
        return $this->Operators;
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array( 'operation' => array( 'type' => 'string',
                                            'required' => true,
                                            'default' => '' ) );
    }

    /*!
     Display the variable.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        if ( ! isset( $this->PDF ) )
        {
            $this->createPDF();
        }

        switch ( $namedParameters['operation'] )
        {
            case 'table':
            {
                $table = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $this->PDF->ezTable( $table['data'], '', '', $table['options'] );
            } break;

            case 'header':
            {
                $header = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                if ( !isset( $header['align'] ) )
                    $header['align'] = 'left';
                $prevSize = $this->PDF->fontSize();
                $this->PDF->ezText( $header['text'] .'<C:rf:'. $header['type'] .rawurlencode( $header['text'] ) .'>',
                                    $header['size'],
                                    array( 'justification' => $header['align'] ) );
                $this->PDF->setFontSize( $prevSize );
                eZDebug::writeNotice( 'PDF: Added header: '. $header['text'] .', size: '. $header['size'] .
                                      ', align: '. $header['align'] .', level: '. $header['type'],
                                      'eZPDF::modify' );
            } break;

            case 'create':
            {
                $this->createPDF();
            } break;

            case 'newline':
            {
                $this->PDF->ezText( "\n" );
            } break;

            case 'newpage':
            {
                $this->PDF->ezNewPage();
                eZDebug::writeNotice( 'PDF: New page', 'eZPDF::modify' );
            } break;

            case 'image':
            {
                $image = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $width = isset( $image['width'] ) ? $image['width']: 100;
                $height = isset( $image['height'] ) ? $image['height']: 100;

                $this->PDF->addJpegFromFile( $image['src'], 0, $this->PDF->offsetY()-$height, $width, $height );
                eZDebug::writeNotice( 'PDF: Added Image '.$image['src'].' to PDF file', 'eZPDF::modify' );
            } break;

            case 'anchor':
            {
                $name = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $this->PDF->addDestination( $name['name'], 'FitH', $this->PDF->offsetY() );
                eZDebug::writeNotice( 'PDF: Added anchor: '.$name['name'], 'eZPDF::modify' );
            } break;

            case 'link': // external link
            {
                $link = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );

                $this->PDF->ezText( '<c:alink:'. $link['url'] .'>'. $link['text'] .'</c:alink>' );
                eZDebug::writeNotice( 'PDF: Added link, url: '.$link['url'], 'eZPDF::modify' );
            } break;

            case 'stream':
            {
                $this->PDF->ezStream();
            }

            case 'close':
            {
                include_once( 'lib/ezutils/classes/eztexttool.php' );
                $filename = 'tmp.pdf';
                eZFile::create( $filename, eZSys::storageDirectory() .'/pdf', $this->PDF->ezOutput() );
                eZDebug::writeNotice( 'PDF file closed and saved to '. eZSys::storageDirectory() .'/pdf/'. $filename, 'eZPDF::modify' );
            } break;

            case 'strike':
            {
                $text = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );
                $this->PDF->ezText( '<c:strike>'. $text .'</c:strike>' );
                eZDebug::writeNotice( 'Striked text added to PDF: "'. $text .'"', 'eZPDF::modify' );
            } break;

            case 'text':
            {
                $operands = array();
                for ( $i = 1; $i < count( $operatorParameters ); ++$i )
                {
                    $operand = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace );
                    if ( !is_object( $operand ) )
                        $operands[] = $operand;
                }
                $text = eZTextTool::concat( $operands );

                $this->PDF->ezText( $text );
                eZDebug::writeNotice( 'Text added to PDF: "'. $text .'"', 'eZPDF::modify' );
            } break;

            default:
            {
                var_dump( $namedParameters );
                echo '<br>';
                $text =& $operatorValue;
                echo 'Text: '. $text;
                echo '<br>';
                echo 'Template:';
                echo '<br>';
                echo '<br>';
                $this->PDF->ezText( $text );
                eZDebug::writeNotice( 'No operation defined, adding to PDF: "'.$text.'"', 'eZPDF::modify' );
//                $operatorValue = null;
            }

        }

    }

    /*
     \private
     Create PDF object
    */
    function createPDF()
    {
        $this->PDF = new eZPDFTable();
        $this->PDF->selectFont( 'lib/ezpdf/classes/fonts/Helvetica' );
        eZDebug::writeNotice( 'PDF: File created' );
    }

    /// The array of operators, used for registering operators
    var $Operators;
    var $PDF;

    var $bufferedText = '';
}


?>
