<?php

namespace App\Controller;

use App\Entity\TaskNote;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Param;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractFOSRestController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @return View
     * @var Task $task
     */
    public function deleteTaskAction(Task $task)
    {
        if ($task) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @return View
     * @var Task $task
     */
    public function statusTaskAction(Task $task)
    {
        if ($task) {
            $task->setIsComplete(!$task->getIsComplete());

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->view($task->getIsComplete(), Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getTaskNotesAction(Task $task)
    {
        if ($task) {
            return $this->view($task->getNotes(), Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @RequestParam(name="note", description="Note for the task", nullable=false)
     * @param Task $task
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postTaskNoteAction(ParamFetcher $paramFetcher,Task $task)
    {
        $body = $paramFetcher->get('body');

        if ($body) {
            if ($task) {
                $note = new TaskNote();

                $note->setBody($body);
                $note->setTask($task);

                $task->addNote($note);

                $this->entityManager->persist($note);
                $this->entityManager->flush();

                return $this->view($note, Response::HTTP_OK);
            }
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getTaskAction(Task $task)
    {
        if ($task) {
            return $this->view($this, Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
