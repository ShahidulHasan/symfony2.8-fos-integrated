<?php

namespace Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bundle\UserBundle\Entity\User;
use Bundle\UserBundle\Form\Type\UserFormType;
use Bundle\UserBundle\Form\Type\UserEditFormType;

/**
 * User Controller.
 *
 */
class UserController extends Controller
{
    /**
     * @return object
     */
    protected function getDoctrineManager($repository){
        return $this->getDoctrine()->getManager()->getRepository($repository);
    }

    /**
     * @return string
     */
    public function getSuccessMsg($msg){
        return $this->get('session')->getFlashBag()->add('success', $msg);
    }

    /**
     * @Route("/user/list", name="user_list")
     * @Template("UserBundle:User:list.html.twig")
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function indexAction(){
        return array('users' => $this->getDoctrineManager("UserBundle:User")->findAll());
    }

    /**
     * @Route("/user/details/{id}", name="user_details")
     * @Template("UserBundle:Profile:show.html.twig")
     * @param User $user
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function detailsAction(User $user){
        return array('user' => $user);
    }

    /**
     * @Route("/user/profile", name="user_profile")
     * @Template("UserBundle:Profile:show.html.twig")
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function profileAction(){
        return array('user' => $this->getUser());
    }

    /**
     * @Route("/user/enabled/{id}", name="user_enabled")
     * @param User $user
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function enableAction(User $user){
        $user->isEnabled() ? $user->setEnabled(false) AND $this->getSuccessMsg("User Successfully Disabled.") :
            $user->setEnabled(true) AND $this->getSuccessMsg("User Successfully Enabled.");
        $this->getDoctrineManager("UserBundle:User")->update($user);

        return $this->redirect($this->generateUrl('user_list'));
    }

    /**
     * @Route("/user/add", name="user_add")
     * @Template("UserBundle:Registration:register.html.twig")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function addAction(Request $request){
        $user = new User();
        $service = $this->get('userbundle_user.registration.form.type');
        $form = $this->createForm($service, $user);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrineManager("UserBundle:User")->create($user);
                $this->getSuccessMsg("User Successfully Created.");
                return $this->redirect($this->generateUrl('user_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/edit/profile", name="user_edit_profile")
     * @Template("UserBundle:Profile:edit.html.twig")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function editProfileAction(Request $request){
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new UserEditFormType(), $user);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrineManager("UserBundle:User")->update($user);
                $this->getSuccessMsg("User Successfully Updated.");

                return $this->redirect($this->generateUrl('user_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     * @Template("UserBundle:Registration:register.html.twig")
     * @param Request $request
     * @param User $user
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function editAction(Request $request, User $user){
        $form = $this->createForm(new UserEditFormType(), $user);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrineManager("UserBundle:User")->update($user);
                $this->getSuccessMsg("User Successfully Updated.");

                return $this->redirect($this->generateUrl('user_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     * @param User $user
     * @JMS\Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function deleteAction(User $user){
        $this->getDoctrineManager("UserBundle:User")->delete($user);
        $this->getSuccessMsg("User Successfully Deleted.");

        return $this->redirect($this->generateUrl('user_list'));
    }

    public function add($a, $b)
    {
        return $a + $b;
    }
}