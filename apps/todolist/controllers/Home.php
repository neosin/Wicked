<?php

namespace app\controllers;

use app\models\Task;

class Home
{

    /**
     * @var \wicked\core\bridge\Mog
     * @context wicked.mog
     */
    public $mog;

    /**
     * @var \syn\MySQL
     * @context wicked.syn
     */
    public $syn;


    /**
     * Display list
     */
    public function index()
    {
        // get all task
        $list = $this->syn->task->find();

        // give to view
        return ['list' => $list];
    }


    /**
     * Add form
     */
    public function add()
    {

        // submit add
        if($this->mog->post) {

            // create task
            $task = new Task();
            $task->content = $this->mog->post['content'];

            // persist
            $this->syn->task->save($task);
            return home();
        }

    }


    /**
     * Edit form
     * @param $id
     * @return bool
     */
    public function edit($id)
    {

        // retrieve task
        $task = $this->syn->task->find($id);

        // 404
        if(!$task)
            $this->mog->oops('This task does not exist !', 404);

        // submit edit
        if($this->mog->post) {

            // updata
            $task->content = $this->mog->post['content'];

            // persist
            $this->syn->task->save($task);
            return home();

        }

        return ['task' => $task];
    }


    /**
     * Delete form
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        // retrieve task
        $task = $this->syn->task->find($id);

        // delete it
        $this->syn->task->delete($task);

        return home();
    }

}