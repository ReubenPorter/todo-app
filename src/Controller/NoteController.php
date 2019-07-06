<?php

namespace App\Controller;

use App\Entity\TaskNote;
use App\Repository\TaskNoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractFOSRestController
{
    /**
     * @var TaskNoteRepository
     */
    private $taskNoteRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(TaskNoteRepository $taskNoteRepository, EntityManagerInterface $entityManager)
    {
        $this->taskNoteRepository = $taskNoteRepository;
        $this->entityManager = $entityManager;
    }

    public function getNoteAction(TaskNote $taskNote)
    {
        return $this->view($taskNote, Response::HTTP_OK);
    }

    public function deleteNoteAction(TaskNote $taskNote)
    {
        if ($taskNote) {
            $this->entityManager->remove($taskNote);
            $this->entityManager->persist($taskNote);

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
