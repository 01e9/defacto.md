<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route(path="/admin/settings")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminSettingsController extends AbstractController
{
    /**
     * @Route(path="", name="admin_settings")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $settings = $this->getDoctrine()->getRepository('App:Setting')->getAdminList($request);

        return $this->render('admin/page/setting/index.html.twig', [
            'settings' => $settings,
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_setting_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id, TranslatorInterface $translator)
    {
        $config = $this->getDoctrine()->getRepository('App:Setting')->getConfig($id);

        if (!$config) {
            throw $this->createNotFoundException();
        }

        $value = $this->getDoctrine()->getRepository('App:Setting')->get($id, false);

        $form = $this->createForm(SettingType::class, [
            'value' => $value,
        ], [
            'label' => $config['name'],
            'type' => $config['type'],
            'has_default' => !is_null($config['default'])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Setting $setting */
            $setting = $this->getDoctrine()->getRepository('App:Setting')->find($id) ?? new Setting();

            $value = $form->getData()['value'];
            $setting->setId($id)->setValue($value);

            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            $this->addFlash('success', $translator->trans('flash.setting_updated'));

            return $this->redirectToRoute('admin_settings');
        }

        return $this->render('admin/page/setting/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}