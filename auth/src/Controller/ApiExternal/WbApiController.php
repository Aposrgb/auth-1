<?php

namespace App\Controller\ApiExternal;

use App\Helper\Exception\ApiException;
use App\Service\ValidatorService;
use App\Service\WbApiService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="WB")
 */
#[Route('/wb')]
class WbApiController extends AbstractController
{
    public function __construct(
        protected WbApiService $service,
        protected ValidatorService $validatorService
    )
    {
    }
    /** Продажи */
    #[Route('/sales', name: 'wb_sales', methods: ["GET"])]
    public function sales(): JsonResponse
    {
        $token = $this->validatorService->checkTokenUser($this->getUser());
        return $this->json(['data' => $this->service->sales($token)]);
    }
    /** Поставки */
    #[Route('/incomes', name: 'wb_incomes', methods: ["GET"])]
    public function incomes(): JsonResponse
    {
        $token = $this->validatorService->checkTokenUser($this->getUser());
        return $this->json(['data' => $this->service->incomes($token)]);
    }
    /** Склад */
    #[Route('/stocks', name: 'wb_stocks', methods: ["GET"])]
    public function stocks(): JsonResponse
    {
        $token = $this->validatorService->checkTokenUser($this->getUser());
        return $this->json(['data' => $this->service->stocks($token)]);
    }
    /** Заказы */
    #[Route('/orders', name: 'wb_orders', methods: ["GET"])]
    public function orders(): JsonResponse
    {
        $token = $this->validatorService->checkTokenUser($this->getUser());
        return $this->json(['data' => $this->service->orders($token)]);
    }
    /** Отчеты */
    #[Route('/reportDetailByPeriod', name: 'wb_report_detail_by_period', methods: ["GET"])]
    public function reportDetailByPeriod(): JsonResponse
    {
        $token = $this->validatorService->checkTokenUser($this->getUser());
        return $this->json(['data' => $this->service->reportDetailByPeriod($token)]);
    }
    /** Кизы */
    #[Route('/excise-goods', name: 'wb_excise_goods', methods: ["GET"])]
    public function exciseGoods(): JsonResponse
    {
        $token = $this->validatorService->checkTokenUser($this->getUser());
        return $this->json(['data' => $this->service->exciseGoods($token)]);
    }
}