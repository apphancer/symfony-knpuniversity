<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 27/11/2017
 * Time: 19:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Service\MarkdownTransformer;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class GenusController extends Controller
{
    /**
     * @Route("/genus/new ")
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus ' . rand(1, 100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 99999));

        $genusNote = new GenusNote();
        $genusNote->setUsername('AcquaWeaver');
        $genusNote->setUserAvatarFilename('ryan.jpeg');
        $genusNote->setNote('Bla di bla di bla bla bla....');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->persist($genusNote);
        $em->flush();

        return new Response('<html><body>Genus Createad!</body></html>');
    }

    /**
     * @Route("/genus")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAllByPublishedOrderedByRecentlyActive();

        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses,
        ]);
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName)
    {

        $em = $this->getDoctrine()->getManager();

        $genus = $em->getRepository('AppBundle:Genus')
            ->findOneBy(['name' => $genusName]);

        if (!$genus)
        {
            throw $this->createNotFoundException('No Genus found');
        }

        // Option 1;
        $transformer = new MarkdownTransformer(
            $this->get('markdown.parser'),
            $this->get('doctrine_cache.providers.markdown_cache')
        );

        // Option 2:
        // Get Transformer as a service added to the container
        $transformer = $this->get('app.markdown_transformer');

        $funFact = $transformer->parse($genus->getFunFact());

        // slow solution
        $recentNotes = $genus->getNotes()
            ->filter(function (GenusNote $note) {
                return $note->getCreatedAt() > new DateTime('-3 months');
            });

        // faster solution
        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', [
            'genus'           => $genus,
            'recentNoteCount' => count($recentNotes),
            'funFact'         => $funFact,
        ]);
    }

    /**
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {

        // we could use the Entity Manager and query like normale, or....
        // use parameter conversion
        // in this case we are passing {name} in the route /genus/{name}/notes
        // {name} matches the db table column name

        // dump($genus);

        // genus_repository->findBy(['genus'=>$genus]); or to be even lazier....

        $notes = [];
        foreach ($genus->getNotes() as $note)
        {
            $notes[] = [
                'id'        => $note->getId(),
                'username'  => $note->getUsername(),
                'avatarUri' => '/images/' . $note->getUserAvatarFilename(),
                'note'      => $note->getNote(),
                'date'      => $note->getCreatedAt()->format('M d, Y'),
            ];
        }

        $data = [
            'notes' => $notes,
        ];

        return new JsonResponse($data);
    }
}