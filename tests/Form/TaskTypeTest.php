<?php

namespace App\Tests\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'title' => 'test',
            'content' => 'test_content',
        ];

        $task = new Task();
        $task->setTitle($formData['title']);
        $task->setContent($formData['content']);

        $task_to_compare = new Task();
        $form = $this->factory->create(TaskType::class, $task_to_compare);
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $formData was modified as expected when the form was submitted
        $this->assertEquals($task_to_compare->getTitle(), $task->getTitle());
        $this->assertEquals($task_to_compare->getContent(), $task->getContent());
    }
}