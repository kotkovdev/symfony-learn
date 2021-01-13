<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @var ConferenceRepository
     */
    private ConferenceRepository $conferenceRepository;

    /**
     * @var CommentRepository
     */
    private CommentRepository $commentRepository;

    /**
     * ConferenceController constructor.
     *
     * @param ConferenceRepository $conferenceRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(
        ConferenceRepository $conferenceRepository,
        CommentRepository $commentRepository
    ) {
        $this->conferenceRepository = $conferenceRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('conference/index.html.twig', [
            'controller_name' => 'ConferenceController',
            'conferences' => $this->conferenceRepository->findAll()
        ]);
    }

    /**
     * @Route("/conference/{id}", name="conference")
     *
     * @param Conference $conference
     *
     * @param Request $request
     * @return Response
     */
    public function show(Conference $conference, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($conference, $offset);
        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => [
                'collection' => $paginator,
                'previous' => $offset - CommentRepository::COMMENTS_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::COMMENTS_PER_PAGE)
            ]
        ]);
    }
}
