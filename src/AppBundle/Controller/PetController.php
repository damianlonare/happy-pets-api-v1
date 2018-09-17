<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Pet;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class PetController extends Controller
{
    /**
     * @Route("/api/v1/pet/create")
     * @Method("POST")
     */
    public function createPetAction(Request $request)
    {
        $data = json_decode($request->getContent());

        $pet = new Pet();
        $pet->setName($data->petName);
        $pet->setPrice($data->petPrice);
        $pet->setDescription($data->petDescription);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($pet);
        $entityManager->flush();

        return new JsonResponse($data);
    }


    /**
     * @Route("/api/v1/pet/read/{petId}")
     * @Method("GET")
     */
    public function readPetAction($petId)
    {
        $pet = $this->getDoctrine()
            ->getRepository(Pet::class)
            ->find($petId);

        if(!$pet) {
            throw $this->createNotFoundException(
                'No pet found for id '.$petId
            );
       }

        $res = [
            "petId" => $pet->getId(),
            "petName" => $pet->getName(),
            "petPrice" => $pet->getPrice(),
            "petDescription" => $pet->getDescription()
        ];

        return new JsonResponse($res);
    }

    /**
     * @Route("/api/v1/pet/update")
     * @Method("PUT")
     */
    public function updatePetAction(Request $request)
    {
        $client_data = json_decode($request->getContent());

        $pet = $this->getDoctrine()
            ->getRepository(Pet::class)
            ->find($client_data->petId);

        if(!$pet) {
            throw $this->createNotFoundException(
                'No pet found for id '.$client_data->petId
            );
        }

        $pet->setName($client_data->petName);
        $pet->setPrice($client_data->petPrice);
        $pet->setDescription($client_data->petDescription);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $json_res = [
            "petName" => $pet->getName(),
            "petPrice" => $pet->getPrice(),
            "petDescription" => $pet->getDescription()
        ];

        return new JsonResponse($json_res);
    }

    /**
     * @Route("/api/v1/pet/delete/{petId}")
     * @Method("DELETE")
     */
    public function deletePetAction($petId)
    {
        $pet = $this->getDoctrine()
            ->getRepository(Pet::class)
            ->find($petId);

        if(!$pet) {
            throw $this->createNotFoundException(
                'No pet found for id '.$petId
            );
        }

        $json_res = [
            "response_error" => 0,
            "response_description" => "success",
            "response_content" => [
                "pet_deleted" => [
                    "petId" => $pet->getId(),
                    "petName" => $pet->getName(),
                    "petPrice" => $pet->getPrice(),
                    "petDescription" => $pet->getDescription()
                ]
            ]
        ];

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($pet);
        $entityManager->flush();

        return new JsonResponse($json_res);
    }
}
