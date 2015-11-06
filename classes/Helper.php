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
 * Class Helper
 *
 * @author Medialta <http://www.medialta.com>
 */
class Helper extends \System
{
    /**
     * Post login hook
     *
     * @param \User $objUser current logged in user
     */
    public function postLogin($objUser)
    {
        $session = \Session::getInstance();
        $modules = $session->get('backend_modules');

        if (is_array($modules) && !empty($modules))
        {
            foreach ($modules as $groupName => $value)
            {
                $modules[$groupName] = 1;
            }

            $session->set('backend_modules', $modules);
        }
    }

    /**
     * Returns true if given string is an image filename
     *
     * @param string $src Icon name or filename
     *
     * @return boolean true if current icon is an image filename
     */
    public static function isImage($src)
    {
        $src = rawurldecode($src);

        if (strpos($src, '/') === false)
        {
            if (strncmp($src, 'icon', 4) === 0)
            {
                $src = 'assets/contao/images/' . $src;
            }
            else
            {
                $src = 'system/themes/' . \Backend::getTheme() . '/images/' . $src;
            }
        }

        return file_exists(TL_ROOT . '/' . $src);
    }

    /**
     * Returns true if given string is an inactive icon (not clickable)
     *
     * @param string $src Icon name or filename
     *
     * @return boolean true if current icon is inactive
     */
    public static function isInactiveIcon($src)
    {
        $inactive = false;

        if (self::isImage($src))
        {
            $filename = basename($src, strrchr($src, '.'));
            $inactive = substr($filename, -1) == '_';
        }

        return $inactive;
    }

    /**
     * Gets the active image corresponding to an inactive one
     *
     * @param string $inactiveImage Inactive image filename
     *
     * @return string Active image filename
     */
    public static function getActiveImage($inactiveImage)
    {
        $extension = strrchr($inactiveImage, '.');
        $filename = basename($inactiveImage, $extension);

        return substr($filename, 0, -1) . $extension;
    }

    /**
     * Replaces an HTML image by the corresponding Material Design icon
     *
     * @param string $html HTML string containing image
     *
     * @return string HTML string with image replaced by icon
     */
    public static function formatButtonCallback($html)
    {
        // Replaces image by icon
        preg_match_all('/(<img.*src=\"(.*)\".*>)/mU', $html, $matches);

        if (isset($matches[1][0]) && isset($matches[2][0]) && strlen($matches[1][0]) && strlen($matches[2][0]))
        {
            $html = str_replace($matches[1][0], self::getIconHtml(basename($matches[2][0])), $html);
        }

        // Replaces title by a tooltip
        $html = preg_replace('/(.* )title(=".*"[ >].*)/mU', '$1data-position="top" data-delay="50" data-tooltip$2', $html);

        // Adds classes
        $html = preg_replace('/(<a.* class=".*)(".*>)/mU', '$1 btn-flat btn-icon waves-effect waves-circle waves-orange tooltipped$2', $html);

        return $html;
    }

    /**
     * Returns a Material Design icon HTML corresponding to a Contao image
     *
     * @param string $src        The image path
	 * @param string $alt        An optional alt attribute
	 * @param string $attributes A string of other attributes
     *
     * @return string The icon HTML tag
     */
    public static function getIconHtml($src, $alt = '', $attributes = '')
    {
        $icon = '';
        $inactive = self::isInactiveIcon($src);

        if (self::isImage($src))
        {
            if (!isset($GLOBALS['MD_ICONS']))
            {
                require __DIR__ . '/../config/icons.php';
            }

            if (isset($GLOBALS['MD_ICONS'][$src]))
            {
                $icon = $GLOBALS['MD_ICONS'][$src];
            }
            else if (isset($GLOBALS['MD_ICONS'][basename($src)]))
            {
                $icon = $GLOBALS['MD_ICONS'][basename($src)];
            }
            else if ($inactive)
            {
                $activeImage = self::getActiveImage($src);

                if (isset($GLOBALS['MD_ICONS'][$activeImage]))
                {
                    $icon = $GLOBALS['MD_ICONS'][$activeImage];
                }
            }
        }

        if (strlen($icon))
        {
            $icon = '<i class="material-icons">' . $icon . '</i>';

            if ($inactive)
            {
                $icon = '<span class="inactive-option">' . $icon . '</span>';
            }
        }
        else
        {
            $icon = \Image::getHtml($src, $alt, $attributes);
        }

        return $icon;
    }
}
