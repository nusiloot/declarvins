<?php include_partial('daids_export/pdfLine', array('libelle' => $libelle,
						  'counter' => isset($counter) ? $counter : null,
						  'colonnes' => $colonnes,
						  'hash' => $hash,
						  'partial' => 'daids_export/pdfLineItemFloat',
						  'partial_params' => array('unite' => isset($unite) ? $unite : null),
						  'cssclass_libelle' => isset($cssclass_libelle) ? $cssclass_libelle : null,
						  'cssclass_value' => isset($cssclass_value) ? $cssclass_value.' number' : 'number',
						  'partial_cssclass_value' => isset($partial_cssclass_value) ? $partial_cssclass_value : null)) ?>