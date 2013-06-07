<?php

namespace app\controllers;

use app\models\Task;

class Front
{

    use \wicked\tools\wire\All;

    /**
     * Display list
     */
    public function index()
    {
        // get all task
        $list = syn()->task->find();

        // give to view
        return ['list' => $list];
    }


    /**
     * Add form
     */
    public function add()
    {

        // submit add
        if(post()) {

            // create task
            $task = new Task();
            $task->content = post('content');

            // persist
            syn()->task->save($task);
            go('/');
        }

    }


    /**
     * Edit form
     * @param $id
     * @return array
     */
    public function edit($id)
    {

        // retrieve task
        $task = syn()->task->find($id);

        // 404
        if(!$task)
            oops('This task does not exist !', 404);

        // submit edit
        if(post()) {

            // updata
            $task->content = post('content');

            // persist
            syn()->task->save($task);
            go('/');

        }

        return ['task' => $task];
    }


    /**
     * Delete form
     * @param $id
     */
    public function delete($id)
    {
        // retrieve task
        $task = syn()->task->find($id);

        // delete it
        syn()->task->delete($task);

        go('/');
    }

}