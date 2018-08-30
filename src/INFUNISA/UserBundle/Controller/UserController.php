<?php

namespace INFUNISA\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request; //Este objeto Request nos va a servir para ir recibiendo la petición desde nuestro formulario para guardarlo a parir de nuestra entidad en BD
use Symfony\Component\HttpFoundation\Response;
use INFUNISA\UserBundle\Entity\User;
use INFUNISA\UserBundle\Form\UserType;

class UserController extends Controller
{
    
    //public function indexAction(Request $request) ----- Esto era antes de utilizar dql
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        //$users = $em->getRepository('INFUNISAUserBundle:User')->findAll(); ----- Esto era antes de usar dql
        
        /*
        $res = 'Lista de usuarios: <br />';
        
        foreach($users  as $user)
        {
            $res .= 'Usuario: ' . $user->getUsername() . ' - Email: ' . $user->getEmail() . '<br />';
        }
        
        return new Response($res);
        */
        
        $dql = "SELECT u FROM INFUNISAUserBundle:User u";
        $users = $em->createQuery($dql);
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users, $request->query->getInt('page', 1), //esto va hacer el número de página en el cual se va a situar nuestra paginación, que inicialmente va a ser en la página 1 (esto es configurable)
            3 // este tercer parámetro es el límite por página, es decir cuántos registros nos va a mostrar por cada página de nuestra vista
            );
        
        return $this->render('INFUNISAUserBundle:User:index.html.twig', array('pagination'=> $pagination));
    }
    
    public function addAction()
    {
        $user = new User(); //$user es el objeto, y User() es la insancia del objeto $user
        $form = $this->createCreateForm($user); //createCreateForm es un método que estamos llamando para la variable $form
        
        return $this->render('INFUNISAUserBundle:User:add.html.twig', array('form' => $form->createView())); //Bundle = INFUNISAUserBundle, Directorio = User y la acción es add.html.twig //que es la vista //el método createView() va a contener todo nuestro formulario
    }
    
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
                'action' => $this->generateUrl('infunisa_user_create'),
                'method' => 'POST'
            ));
            
        return $form;
    }
    
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form ->handleRequest($request); //la variable form va a contener lo que está después de ->
        
        if($form->isValid())
        {
            $password = $form->get('password')->getData();
            
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            
            $user->setPassword($encoded);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush(); //método flush para realizar la persistencia de datos, guardando en la BD
            
            $successMessage = $this->get('translator')->trans('The user has been created.'); //método get llamando al servicio translator y seguido de esto al método trans
            $this->addFlash('mensaje', $successMessage);
            //$this->addFlash('mensaje', 'The user has been created.'); //así fue escrito inicialmente sin agregar la variable $succesMessage
            
            return $this->redirectToRoute('infunisa_user_index');
        }
        
        return $this->render('INFUNISAUserBundle:User:add.html.twig', array('form' => $form->createView())); //Bundle = INFUNISAUserBundle, Directorio = User y la acción es add.html.twig //que es la vista //el método createView() va a contener todo nuestro formulario
    }
    
    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('INFUNISAUserBundle:User');
        
        $user = $repository->find($id);
        
        // $user = $repository->findOneById($id);
        
        // $user = $repository->findOneByUsername($nombre);
        
        return new Response('Usuario: ' . $user->getUsername() . ' con email: ' . $user->getEmail());
    
    }
    
    
}
