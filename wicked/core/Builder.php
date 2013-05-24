<?php
/**
 * This file is part of the Wicked package.
 *
 * Copyright Aymeric Assier <aymeric.assier@gmail.com>
 *
 * For the full copyright and license information, please view the Licence.txt
 * file that was distributed with this source code.
 *
 * @author Aymeric Assier <aymeric.assier@gmail.com>
 * @date 2013-05-21
 * @version 0.1
 */
namespace wicked\core;

class Builder
{


    /**
     * Create folders, controller and view as CRUD
     * Ex : $builder->crud('app\models\User');
     * Ex : $builder->crud('app\models\User', 'bundles/back/');
     * @param $model
     * @param string $path
     */
    public function crud($model, $path = '')
    {
        // set vars
        $ex = explode('\\', $model);
        $path = $path ? '/' . trim($path, '/') : null;

        $data = [];
        $data['model_long'] = $model;
        $data['model_short_upper'] = end($ex);
        $data['model_short_lower'] = strtolower($data['model_short_upper']);
        $data['namespace'] = str_replace('/', '\\', 'app' . $path . '/controllers');


        // create folders
        $base = dirname($_SERVER['SCRIPT_FILENAME']);
        $controllerPath = $base . $path . DIRECTORY_SEPARATOR . 'controllers';
        $viewsPath = $base . $path . DIRECTORY_SEPARATOR . 'views/';

        if(!is_dir($controllerPath))
            mkdir($controllerPath, 0777, true);

        if(!is_dir($viewsPath))
            mkdir($viewsPath, 0777, true);

        // create controller file
        static::fill($controllerPath . DIRECTORY_SEPARATOR . $data['model_short_upper'] . '.php', 'crud.controller', $data);

        // create layout
        static::fill($viewsPath . 'layout.php', 'view.layout', $data);

        // create views
        static::fill($viewsPath . '/' . $data['model_short_lower'] . '/index.php', 'crud.view.index', $data);
        static::fill($viewsPath . '/' . $data['model_short_lower'] . '/show.php', 'crud.view.show', $data);
        static::fill($viewsPath . '/' . $data['model_short_lower'] . '/form.php', 'crud.view.form', $data);

    }


    /**
     * Make file from template and data
     * @param $file
     * @param $template
     * @param $data
     * @return bool|int
     */
    protected static function fill($file, $template, $data)
    {
        // only if file does not exist
        if(!file_exists($file)) {

            // get template
            $template = file_get_contents(dirname(__FILE__) . '/builder/' . $template);

            // fill with data
            foreach($data as $placeholder => $value)
                $template = str_replace('{:' . $placeholder . ':}', $value, $template);

            // write file
            return file_put_contents($file, $template);
        }

        return false;
    }

}