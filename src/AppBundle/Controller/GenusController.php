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
     * @Route("/genus/new")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();

        $subFamily = $em->getRepository('AppBundle:SubFamily')
            ->findAny();

        $genus = new Genus();
        $genus->setName('Octopus' . rand(1, 10000));
        $genus->setSubFamily($subFamily);
        $genus->setSpeciesCount(rand(100, 99999));
        $genus->setFirstDiscoveredAt(new \DateTime('50 years'));

        $genusNote = new GenusNote();
        $genusNote->setUsername('AquaWeaver');
        $genusNote->setUserAvatarFilename('ryan.jpeg');
        $genusNote->setNote('I counted 8 legs... as they wrapped around me');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(['email' => 'martin+1@martin.com']);
        $genus->addGenusScientist($user);
        $genus->addGenusScientist($user);

        $em->persist($genus);
        $em->persist($genusNote);
        $em->flush();

        return new Response(sprintf(
            '<html><body>Genus created! <a href="%s">%s</a></body></html>',
            $this->generateUrl('genus_show', ['slug' => $genus->getSlug()]),
            $genus->getName()
        ));
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
     * @Route("/genus/{slug}", name="genus_show")
     */
    public function showAction(Genus $genus, MarkdownTransformer $markdownTransformer)
    {

        $em = $this->getDoctrine()->getManager();

        // Option 1;
        /*
        $transformer = new MarkdownTransformer(
            $this->get('markdown.parser'),
            $this->get('doctrine_cache.providers.markdown_cache')
        );
        */

        // Option 2:
        // Get Transformer as a service added to the container
        //$transformer = $this->get(MarkdownTransformer::class);

        $funFact = $markdownTransformer->parse($genus->getFunFact());

        // slow solution
        /*
        $recentNotes = $genus->getNotes()
            ->filter(function (GenusNote $note) {
                return $note->getCreatedAt() > new DateTime('-3 months');
            });
        */

        $this->get('logger')
            ->info('Showing genus:' . $genus->getName());

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
     * @Route("/genus/{slug}/notes", name="genus_show_notes")
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