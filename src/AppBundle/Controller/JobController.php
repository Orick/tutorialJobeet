<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{
    /**
     * Lists all job entities.
     *
     */
    public function indexAction()
    {
        // $em = $this->getDoctrine()->getManager();
        // $jobs = $em->getRepository('AppBundle:Job')->getActiveJobs();

        // return $this->render('job/index.html.twig', array(
        //     'jobs' => $jobs,
        // ));

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')->getWithJobs();
        foreach ($categories as $category) {
            $category->setActiveJobs($em->getRepository('AppBundle:Job')->getActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
            $category->setMoreJobs($em->getRepository('AppBundle:Job')->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage'));
        }
        return $this->render('job/index.html.twig', array(
            'categories' => $categories
        ));
    }

    /**
     * Creates a new job entity.
     *
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $job->setType('full-time');
        $form = $this->createForm('AppBundle\Form\JobType', $job);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $job->getLogo();
            if($file instanceof UploadedFile) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('jobs_directory'),
                    $fileName

                );
                $job->setLogo($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();
            return $this->redirectToRoute('job_preview', array(
                'token' => $job->getToken(),
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'position' => $job->getPositionSlug()));
        }

        return $this->render('job/new.html.twig', array(
            'job' => $job,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a job entity.
     * @ParamConverter("job", options={"repository_method" = "getActiveJob"})
     */
    public function showAction(Job $job)
    {
        // ParamConverter("job",array("repository_method" => "getActiveJob"));
        $deleteForm = $this->createDeleteForm($job);

        return $this->render('job/show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing job entity.
     *
     */
    public function editAction(Request $request, Job $job)
    {
        if ($request->getMethod() != Request::METHOD_POST) {
            if(is_file($this->getParameter('jobs_directory').'/'.$job->getLogo())) {
                $job->setLogo(
                    new File($this->getParameter('jobs_directory').'/'.$job->getLogo())
                );
            }
        }

        $deleteForm = $this->createDeleteForm($job);
        $editForm = $this->createForm('AppBundle\Form\JobType', $job);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_preview', array(
                'token' => $job->getToken(),
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'position' => $job->getPositionSlug()));
        }

        return $this->render('job/edit.html.twig', array(
            'job' => $job,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a job entity.
     *
     */
    public function deleteAction(Request $request, Job $job)
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * Creates a form to delete a job entity.
     *
     * @param Job $job The job entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Job $job)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('job_delete', array('token' => $job->getToken())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * Finds and displays the preview page for a job entity.
     * @ParamConverter("job", options={"exclude": {"company", "location", "position"}})
     */
    public function previewAction(Job $job)  {
        $deleteForm = $this->createDeleteForm($job);
        $publishForm = $this->createPublishForm($job);
        
        return $this->render('job/show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView(),
        ));
    }

    public function publishAction(Request $request, Job $job)
    {
        $form = $this->createPublishForm($job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $job->publish();
            $em->persist($job);
            $em->flush();
            $this->addFlash('notice', 'Your job is now online for 30 days.');
        }
        return $this->redirectToRoute('job_preview', array(
            'token' => $job->getToken(),
            'company' => $job->getCompanySlug(),
            'location' => $job->getLocationSlug(),
            'position' => $job->getPositionSlug()));
    }

    private function createPublishForm(Job $job)
    {
        return $this->createFormBuilder(array('token' => $job->getToken()))
            ->add('token', 'hidden')
            ->setMethod('POST')
            ->getForm()
        ;
    }

  
}
