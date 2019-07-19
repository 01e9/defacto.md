<?php

namespace App\Controller;

use App\Form\EditorImageType;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminEditorImagesController extends AbstractController
{
    private $uploadDir = "/editor";

    /**
     * @Route(path="/admin/editor-images", methods={"POST"})
     * @return Response
     */
    public function addAction(Request $request, FileUploader $fileUploader)
    {
        $form = $this->createForm(EditorImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData()['image'];
            $fileName = $fileUploader->upload($this->uploadDir, $image);

            return $this->json(["url" => "/uploads{$this->uploadDir}/{$fileName}"]);
        }

        return $this->json(["error" => ["message" => "Invalid request"]]);
    }
}