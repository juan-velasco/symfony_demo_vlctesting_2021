<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class OptimizedApiController extends AbstractController
{
    /**
     * @Route("/optimized-api/people/", name="optimized_api_people_index", methods="GET")
     */
    public function index(): Response
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = "SELECT * FROM person";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        $data = $result->fetchAllAssociative();

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], false);
    }

    /**
     * @Route("/optimized-api/people/", name="optimized_api_people_new", methods="POST")
     */
    public function new(Request $request): Response
    {
        $conn = $this->getDoctrine()->getConnection();

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $sql = "INSERT INTO person (title, first_name, last_name, age, city, postal_code, country, address) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $data['title']);
        $stmt->bindValue(2, $data['firstName']);
        $stmt->bindValue(3, $data['lastName']);
        $stmt->bindValue(4, $data['age']);
        $stmt->bindValue(5, $data['city']);
        $stmt->bindValue(6, $data['postalCode']);
        $stmt->bindValue(7, $data['country']);
        $stmt->bindValue(8, $data['address']);

        $result = $stmt->executeStatement();

        if (!$result) {
            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR, [], false);
        }

        $data['id'] = $conn->lastInsertId();

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], false);
    }

    /**
     * @Route("/optimized-api/people/{id}/", name="optimized_api_people_get", methods="GET")
     */
    public function getOne(int $id): Response
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = "SELECT * FROM person WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $data = $result->fetchAllAssociative();

        return new JsonResponse($data[0], JsonResponse::HTTP_OK, [], false);
    }

    /**
     * @Route("/optimized-api/people/{id}/update", name="optimized_api_people_update", methods="PUT")
     */
    public function update(Request $request, int $id): Response
    {
        $conn = $this->getDoctrine()->getConnection();

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $sql = "UPDATE person SET title = ?, first_name = ?, last_name = ?, age = ?, city = ?, postal_code = ?, country = ?, address = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $data['title']);
        $stmt->bindValue(2, $data['firstName']);
        $stmt->bindValue(3, $data['lastName']);
        $stmt->bindValue(4, $data['age']);
        $stmt->bindValue(5, $data['city']);
        $stmt->bindValue(6, $data['postalCode']);
        $stmt->bindValue(7, $data['country']);
        $stmt->bindValue(8, $data['address']);

        $result = $stmt->executeStatement();

        if (!$result) {
            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR, [], false);
        }

        $data['id'] = $id;

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], false);
    }

    /**
     * @Route("/optimized-api/people/{id}/delete", name="optimized_api_people_get", methods="DELETE")
     */
    public function delete(int $id): Response
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = "DELETE FROM person WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id);

        $result = $stmt->executeStatement();

        if (!$result) {
            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR, [], false);
        }

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT, [], false);
    }
}
