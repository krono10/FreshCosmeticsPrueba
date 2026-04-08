<?php

namespace App\Controller\Api;

use App\Entity\Banner;
use App\Repository\BannerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/banners')]
class BannerApiController extends AbstractController
{
    /**
     * POST
     */
    #[Route('', name: 'api_banner_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['internal_name'])) {
            return new JsonResponse(['error' => 'El nombre interno es obligatorio'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $banner = new Banner();
            
            //El id lo genera la BD
            $banner->setInternalName($data['internal_name']);
            $banner->setActive($data['active'] ?? true);
            $banner->setBackgroundColor($data['background_color'] ?? '#ffffff');
            $banner->setContent($data['content'] ?? []);
            
            // Manejo de fechas
            if (!empty($data['start_date'])) {
                $banner->setStartDate(new \DateTimeImmutable($data['start_date']));
            }
            if (!empty($data['end_date'])) {
                $banner->setEndDate(new \DateTimeImmutable($data['end_date']));
            }

            $em->persist($banner);
            $em->flush();

            return new JsonResponse([
                'message' => 'Banner creado con éxito', 
                'id' => $banner->getId()
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Error al procesar los datos: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT
     */
    #[Route('/{id}', name: 'api_banner_update', methods: ['PUT'])]
    public function update(string $id, Request $request, BannerRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $banner = $repository->find($id);
        if (!$banner) {
            return new JsonResponse(['error' => 'Banner no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['internal_name'])) $banner->setInternalName($data['internal_name']);
        if (isset($data['active'])) $banner->setActive($data['active']);
        if (isset($data['background_color'])) $banner->setBackgroundColor($data['background_color']);
        if (isset($data['content'])) $banner->setContent($data['content']);
        if (isset($data['start_date'])) $banner->setStartDate(new \DateTimeImmutable($data['start_date']));
        if (isset($data['end_date'])) $banner->setEndDate(new \DateTimeImmutable($data['end_date']));

        $em->flush();

        return new JsonResponse(['message' => 'Banner actualizado con éxito']);
    }

    /**
     * DELETE
     */
    #[Route('/{id}', name: 'api_banner_delete', methods: ['DELETE'])]
    public function delete(string $id, BannerRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $banner = $repository->find($id);
        if (!$banner) {
            return new JsonResponse(['error' => 'Banner no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($banner);
        $em->flush();

        return new JsonResponse(['message' => 'Banner eliminado correctamente'], Response::HTTP_OK);
    }

    /**
     * GET
     */
    #[Route('', name: 'api_banner_list', methods: ['GET'])]
    public function list(BannerRepository $repository): JsonResponse
    {
        
        $banners = $repository->findActiveBanners();

        $response = [];
        foreach ($banners as $banner) {
            $response[] = [
                'id' => $banner->getId(),
                'name' => $banner->getInternalName(),
                'start_date' => $banner->getStartDate(),
                'end_date' => $banner->getEndDate(),
                'content' => $banner->getContent(),
                'bg_color' => $banner->getBackgroundColor(),
            ];
        }

        return new JsonResponse($response);
    }
}