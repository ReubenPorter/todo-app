<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ListController
 * @package App\Controller
 */
class ListController extends AbstractFOSRestController
{
    /**
     * @var TaskListRepository
     */
    private $taskListRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(TaskListRepository $taskListRepository, EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->taskListRepository = $taskListRepository;
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function getListsAction()
    {
        $data = $this->taskListRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    public function getListAction(TaskList $taskList)
    {
        return $this->view($taskList, Response::HTTP_OK);
    }

    /**
     * @RequestParam(name="title", description="Title of the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function postListsAction(ParamFetcher $paramFetcher)
    {
        $title = $paramFetcher->get('title');
        if ($title) {
            $list = new TaskList();

            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        return $this->view(['title' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @RequestParam(name="title", description="Title of the new task", nullable=false)
     * @param TaskList $taskList
     * @return View
     */
    public function postListTaskAction(ParamFetcher $paramFetcher, TaskList $taskList)
    {
        if ($taskList) {
            $title = $paramFetcher->get('title');

            $task = new Task();
            $task->setTitle($title);
            $task->setList($taskList);

            $taskList->addTask($task);

            $this->entityManager->persist($task);
            $this->entityManager->persist($taskList);
            $this->entityManager->flush();

            return $this->view($task, Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getListsTasksAction(TaskList $taskList)
    {
        return $this->view($taskList->getTasks(), Response::HTTP_OK);
    }

    /**
     * @Rest\FileParam(name="image", description="The background of the list", nullable=false, image=true)
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return View
     */
    public function backgroundListsAction(Request $request, ParamFetcher $paramFetcher, int $id)
    {
        $list = $this->taskListRepository->findOneBy(['id' => $id]);

        $currentBackground = $list->getBackground();
        if (!$currentBackground) {
            $filesystem = new Filesystem();
            $filesystem->remove(
                $this->getUploadsDirectory() . $currentBackground
            );
        }

        /** @var UploadedFile $file */
        $file = ($paramFetcher->get('image'));

        if ($file) {
            $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

            $file->move($this->getUploadsDirectory(), $filename);

            $list->setBackground($filename);
            $list->setBackgroundPath('/uploads/' . $filename);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $data = $request->getUriForPath(
                $list->getBackgroundPath()
            );

            return $this->view($data, Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_BAD_REQUEST);
    }

    public function deleteListAction(TaskList $taskList)
    {
        $this->entityManager->remove($taskList);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @RequestParam(name="title", description="The new title for the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param TaskList $taskList
     * @return View
     */
    public function patchListTitleAction(ParamFetcher $paramFetcher, TaskList $taskList)
    {
        $title = $paramFetcher->get('title');

        if (trim($title) !== '') {
            if ($taskList) {
                $taskList->setTitle($title);
                $this->entityManager->persist($taskList);
                $this->entityManager->flush();

                return $this->view(null, Response::HTTP_NO_CONTENT);
            }
            $errors[] = [
              'title' => 'This value cannot be empty'
            ];
        }
        $errors[] = [
            'list' => 'List not found'
        ];

        return $this->view($errors, Response::HTTP_NO_CONTENT);
    }

    public function getUploadsDirectory()
    {
        return $this->getParameter('uploads_directory');
    }
}
