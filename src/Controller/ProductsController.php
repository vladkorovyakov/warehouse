<?php

namespace App\Controller;

use App\Model\DocumentDto;
use App\Model\HistoryDto;
use App\Model\InventoryDto;
use App\Service\ApiSerializer;
use App\Service\Document\DocumentService;
use App\Service\History\HistoryService;
use App\Service\History\HistoryStreamer;
use App\Service\InventoryService\InventoryService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Throwable;

final class ProductsController extends AbstractController
{
    public function __construct(
        private readonly HistoryService   $historyService,
        private readonly InventoryService $inventoryService, private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(
        path   : '/products/add-document',
        name   : 'app_products_document',
        methods: ['POST']),
    ]
    #[OA\Post(requestBody: new OA\RequestBody(
        content: new OA\JsonContent(
                     ref: new Model(type: DocumentDto::class)
                 )
    ),
              responses  : [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 500, description: 'Internal error'),
        ],
    )
    ]
    public function addDocument(Request $request, DocumentService $documentService): JsonResponse
    {
        try {
            $serializer = new ApiSerializer();
            $deserializedData = $serializer->deserialize($request->getContent(), DocumentDto::class);
            $documentService->saveDocument($deserializedData);

            $result = new JsonResponse(status: 200);
        } catch (Throwable $exception) {
            $result = new JsonResponse(['error' => $exception->getMessage()], 500);
        }
        return $result;
    }

    #[Route(
        path   : '/products/history',
        name   : 'app_products_history',
        methods: ['GET']),
    ]
    #[OA\Get(
        responses: [
            new OA\Response(
                response   : 200,
                description: 'show documents history by products',
                content    : new OA\JsonContent(
                                 type : 'array',
                                 items: new OA\Items(ref: new Model(type: HistoryDto::class)),
                             )
            )
        ]
    )]
    public function showHistory(): StreamedResponse
    {
        return new StreamedResponse(
            new HistoryStreamer(
                $this->historyService->getAllDocuments(),
                $this->historyService->getDocumentsQuantity(),
                $this->entityManager,
            ),
            200,
            ['Content-Type' => 'application/json'],
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/products/inventory/{date}', name: 'app_products_inventory', methods: ['GET'])]
    #[OA\Get(
        parameters: [
            new OA\Parameter(
                name    : 'date',
                in      : 'path',
                required: true,
                example: '2024-11-05'
            ),
        ],
        responses : [
            new OA\Response(
                response   : 200,
                description: 'show inventory by date',
                content    : new OA\JsonContent(
                                 type : 'array',
                                 items: new OA\Items(ref: new Model(type: InventoryDto::class)),
                             )
            ),
            new OA\Response(response: 500, description: 'Internal error'),
        ]
    )]
    public function showInventoryByDate(string $date): JsonResponse
    {
        try {
            $data = $this->inventoryService->getInventoryDataByDate(new DateTimeImmutable($date));
            $response = new JsonResponse($data, 200, ['Content-Type' => 'application/json']);
        } catch (RuntimeException) {
            $response = new JsonResponse([], 200, ['Content-Type' => 'application/json']);
        } catch (Throwable $exception) {
            $response = new JsonResponse(
                ['error' => $exception->getMessage()],
                500,
                ['Content-Type' => 'application/json']
            );
        }

        return $response;
    }
}
