<?php

namespace App\Controller;

use App\Form\EditorImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminEditorImagesController extends AbstractController
{
    private $uploadDirRelativePath = "/uploads/editor";
    private $uploadDirFullPath;

    public function __construct(string $projectDir)
    {
        $this->uploadDirFullPath = "{$projectDir}{$this->uploadDirRelativePath}";
    }

    /**
     * @Route(path="/admin/editor-images", methods={"POST"})
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(EditorImageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->getData()['image'];
            $fileName = md5(uniqid()) . '.' . $image->guessExtension();

            $image->move($this->uploadDirFullPath, $fileName);

            return $this->json(["url" => "{$this->uploadDirRelativePath}/{$fileName}"]);
        }

        return $this->json(["error" => ["message" => "Invalid request"]]);
    }
}