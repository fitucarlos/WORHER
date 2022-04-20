<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Stage;

use App\Form\TeamType;
use App\Form\GameType;

use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use App\Repository\StageRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Route("/league", name="league")
 */
class EnglishLeagueController extends AbstractController
{

    public function __construct(GameRepository $gameRepository, TeamRepository $teamRepository, StageRepository $stageRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->teamRepository = $teamRepository;
        $this->stageRepository = $stageRepository;
    }

    /**
     * @Route("/ranking", name="_ranking")
     */
    public function ranking(): Response
    {
        $teams = $this->teamRepository->findAll();

        $clasificacion = array();
        foreach ($teams as $team) {
            $clasificacion[] = array("equipo" => $team->getTeam(), "puntos" => 0);
            foreach ($team->getGamesguess() as $game) {
                if ($game->getScorehost() < $game->getScoreguess()) {
                    $clasificacion[array_key_last($clasificacion)]['puntos'] += 3;
                }
                if ($game->getScorehost() == $game->getScoreguess()) {
                    $clasificacion[array_key_last($clasificacion)]['puntos'] += 1;
                }
            }
            foreach ($team->getGamesHost() as $game) {
                if ($game->getScoreguess() < $game->getScorehost()) {
                    $clasificacion[array_key_last($clasificacion)]['puntos'] += 3;
                }
                if ($game->getScoreguess() == $game->getScorehost()) {
                    $clasificacion[array_key_last($clasificacion)]['puntos'] += 1;
                }
            }
        }

        return $this->render('league/ranking.html.twig', [
            'ranking' => $clasificacion,
        ]);
    }

    /**
     * @Route("/edit/{id}", defaults={"id"=0}, name="_edit")
     */
    public function edit(Request $request,$id)
    {
        $stages = $this->stageRepository->findAll();

        $valor = 0;

        $max = count($stages);

        if($id > $max){
            return $this->render('league/error.html.twig');
        }
        else if($id == 0){
            $valor = $max;
        }
        else {
            $valor = $id;
        }

        $games = $this->gameRepository->findBy(array('Stage' => $valor));
        $list = array();
        foreach ($stages as $item) {
            $list[$item->getId()] =  $item->getId();
        }

         /* SELECT */
         $formu = $this->createFormBuilder()
         ->add('stage',  ChoiceType::class, [
             'choices' => $list,  'data'     => $valor
         ])
         ->add('Send', SubmitType::class)
         ->getForm();

        $formu->handleRequest($request);


        $formBuilder = $this->createFormBuilder();

        foreach ($games as $item) {
            $formBuilder->add('partido_local' . $item->getId(),  TextType::class, [
                'label' => $item->getHost()->getTeam(),  'data' => $item->getScorehost()
            ]);
            $formBuilder->add('partido_visitante' . $item->getId(),  TextType::class, [
                'label' => $item->getGuess()->getTeam(),  'data' => $item->getScoreguess()
            ]);
        }
        $formBuilder->add('Send', SubmitType::class);


        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($games as $item) {


                if (isset($data['partido_local' . $item->getId()])) {

                    $item->setScorehost($data['partido_local' . $item->getId()]);
                }
                if (isset($data['partido_visitante' . $item->getId()])) {
                    $item->setScoreguess($data['partido_visitante' . $item->getId()]);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();
            }
        }

        if ($formu->isSubmitted() && $formu->isValid()) {
            $data = $formu->getData();
            return $this->redirectToRoute('league_edit', ['id' => $data['stage']]);
        } else
            return $this->render('league/edit.html.twig', array('form' => $form->createView(),'formu' => $formu->createView(), 'games' => $games,'num_stage' => $valor));

        return $this->render('league/edit.html.twig', array('form' => $form->createView(),'formu' => $formu->createView()));
    }

    /**
     * @Route("/stage/{id}", defaults={"id" = 0}, name="_stage")
     */
    public function stage(Request $request, $id)
    {
        $stages = $this->stageRepository->findAll();
        $max = count($stages);

        if($id > $max){
            return $this->render('league/error.html.twig');
        }
        else if($id == 0){
            $valor = $max;
        }
        else {
            $valor = $id;
        }

        $games = $this->gameRepository->findBy(array('Stage' => $valor));
        $list = array();
        foreach ($stages as $item) {
            $list[$item->getId()] =  $item->getId();
        }
        /* SELECT */
        $form = $this->createFormBuilder()
            ->add('stage',  ChoiceType::class, [
                'choices' => $list,  'data'     => $valor
            ])
            ->add('Send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('league_stage', ['id' => $data['stage']]);
        } else
            return $this->render('league/stage.html.twig', array('form' => $form->createView(), 'games' => $games,'num_stage' => $valor));
    }

    /**
     * @Route("/new", name="_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        
        $form->add('Aceptar', SubmitType::class );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->teamRepository->save($team);
            
            return $this->redirectToRoute('league_ranking');
        }

        return $this->render('league/new.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/new_game", name="_new_game", methods={"GET","POST"})
     */
    public function newGame(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        
        $form->add('Aceptar', SubmitType::class );
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->gameRepository->save($game);
            
            return $this->redirectToRoute('league_ranking');
        }


        return $this->render('league/new_game.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }
    
}
