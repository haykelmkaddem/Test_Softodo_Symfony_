<?php

namespace App\Controller;

use App\Entity\Project;
use App\Data\SearchData;
use App\Form\ProjectType;
use App\Form\ProjectTypeEdit;
use App\Form\SearchType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


#[Route('/projects')]
//#[IsGranted('ROLE_ADMIN')]
class ProjectsController extends AbstractController
{
    #[Route('/', name: 'app_projects_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search = new SearchData();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $pagination = $paginator->paginate(
            $projectRepository->paginationQuery($search),
            $request->query->get('page', 1),
            4
        );
        return $this->render('projects/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_projects_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, ProjectRepository $projectRepository, SluggerInterface $slugger): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('project_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $project->setImage($newFilename);
            }
            $project->setUpdatedAt(date_create_immutable('now'));
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_projects_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projects_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('projects/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projects_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, MailerInterface $mailer, Project $project, ProjectRepository $projectRepository, UserRepository $userRepository, SluggerInterface $slugger): Response
    {
        $oldStatus = $projectRepository->findOneBy(['id' => $project->getId()])->getStatus();
        $admins = $userRepository->findByRole('ROLE_ADMIN');
        $form = $this->createForm(ProjectTypeEdit::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('project_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $project->setImage($newFilename);
            }


            $status = $form->get('status')->getData();
            if ($oldStatus != $status) {
                foreach ($admins as $admin) {
                    $email = (new Email())
                        ->from('haykel.mkaddem1@esprit.tn')
                        ->to($admin->getEmail())
                        ->subject('Status Updated')
                        ->text('the status of the project ' . $project->getTitle() . ' has been updated');
                    $mailer->send($email);
                }
            }
            $project->setUpdatedAt(date_create_immutable('now'));
            $projectRepository->save($project, true);
            return $this->redirectToRoute('app_projects_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('projects/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_projects_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_projects_index', [], Response::HTTP_SEE_OTHER);
    }
}
