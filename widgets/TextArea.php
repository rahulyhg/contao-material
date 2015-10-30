<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace ContaoMaterial;


/**
 * Provide methods to handle textareas.
 *
 * @property integer $maxlength
 * @property boolean $mandatory
 * @property boolean $rte
 * @property integer $rows
 * @property integer $cols
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class TextArea extends \Contao\TextArea
{

    /**
     * Generate the widget and return it as string
     *
     * @return string
     */
    public function generate()
    {
        if ($this->rte)
        {
            $this->strClass = trim($this->strClass . ' noresize');
        }

        return sprintf('<textarea name="%s" id="ctrl_%s" class="materialize-textarea%s" rows="%s" cols="%s"%s onfocus="Backend.getScrollOffset()">%s</textarea>%s',
                        $this->strName,
                        $this->strId,
                        (($this->strClass != '') ? ' ' . $this->strClass : ''),
                        $this->intRows,
                        $this->intCols,
                        $this->getAttributes(),
                        specialchars($this->varValue),
                        $this->wizard);
    }
}