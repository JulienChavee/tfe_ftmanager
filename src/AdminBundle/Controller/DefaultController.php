<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\EditUser;
use AdminBundle\Form\EditUserType;
use MainBundle\Entity\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render( 'AdminBundle:Default:index.html.twig' );
    }

    /* Gestion des utilisateurs */

    public function usersAction() {
        $rep = $this->getDoctrine()->getRepository( 'UserBundle:User' );

        $array_users = $rep->findAll();

        return $this->render( 'AdminBundle:User:index.html.twig', array( 'array_users' => $array_users ) );
    }

    public function usersEditAction( $id ) {
        $userManager = $this->get( 'fos_user.user_manager' );

        $user = $userManager->findUserBy( array( 'id' => $id ) );

        $form = $this->createForm( EditUserType::class, $user );

        if( $form->isSubmitted() ) {
            $userManager->updateUser( $user );
        }

        return $this->render( 'AdminBundle:User:edit.html.twig', array( 'user' => $user, 'form' => $form->createView() ) );
    }

    public function ajaxUserPromoteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository( 'UserBundle:User' )->findOneById( $request->get( 'id' ) );

            if( $user->hasRole( 'ROLE_SUPER_ADMIN' ) && !$this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission pour cela' ) ) );
                $response->headers->set( 'Content-Type', 'application/json') ;

                return $response;
            } else if( $user->hasRole( 'ROLE_ADMIN' ) && !$this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission pour cela' ) ) );
                $response->headers->set( 'Content-Type', 'application/json') ;

                return $response;
            } else {
                // TODO : Vérifier les différents groupes à supprimer
                switch( $request->get( 'role' ) ) {
                    case 'sup_admin':
                        $user->addRole( 'ROLE_SUPER_ADMIN' );
                        break;

                    case 'admin':
                        $user->addRole( 'ROLE_ADMIN' );
                        break;

                    case 'arbitre':
                        $user->addRole( 'ROLE_ARBITRE' );
                        break;

                    case 'member':
                        // TODO
                        break;

                }

                $em->persist( $user );
                $em->flush();

                $response = new Response( json_encode( array( 'status' => 'ok', 'debug' => $user->hasRole( 'ROLE_ADMIN') ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json') ;

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json');

        return $response;
    }

    public function ajaxUserDeleteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            if( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SUPER_ADMIN' ) ) {
                try {
                    $em = $this->getDoctrine()->getManager();

                    $user = $em->getRepository( 'UserBundle:User' )->findOneById( $request->get( 'id' ) );

                    if( $user ) {
                        $em->remove($user);
                        $em->flush();

                        $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                    } else
                        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'L\'utilisateur n\'existe pas' ) ) );

                }
                catch( \Exception $e ) {
                    $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
                }
            } else {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Vous n\'avez pas la permission de supprimer des utilisateurs' ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    /* Gestion des news */

    public function newsAction()
    {
        $rep = $this->getDoctrine()->getRepository( 'MainBundle:News' );

        $array_news = $rep->findAll();

        return $this->render( 'AdminBundle:News:index.html.twig', array( 'array_news' => $array_news ) );
    }
    
    public function ajaxNewsAddAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            // TODO : Validation des données
            $em = $this->getDoctrine()->getManager();
            $user= $this->get( 'security.token_storage' )->getToken()->getUser();

            $news = new News();
            $news->setAuthor( $user );
            $news->setTitle( $request->get('title') );
            $news->setMessage( $request->get('message') );
            $news->setPublishDate( new \DateTime() );
            
            $em->persist( $news );
            $em->flush();

            $response = new Response( json_encode( array( 'status' => 'ok', 'return' => $this->render( 'AdminBundle:News:newsRow.html.twig', array( 'news' => $news, 'loop' => array( 'index' => '-' ) ) )->getContent() ) ) );
            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    public function ajaxNewsDeleteAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $news = $em->getRepository( 'MainBundle:News' )->findOneById( $request->get( 'id' ) );

                if( $news ) {
                    $em->remove( $news );
                    $em->flush();

                    $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
                } else
                    $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'La news n\'existe pas' ) ) );


            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    public function ajaxNewsGetAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $news = $em->getRepository( 'MainBundle:News' )->findOneById( $request->get( 'id' ) );

                $serializer = $this->get('serializer');
                $news = $serializer->normalize($news);

                $response = new Response( json_encode( array( 'status' => 'ok', 'news' => $news ) ) );
            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }

    public function ajaxNewsEditAction( Request $request ) {
        if( $request->isXmlHttpRequest() ) {
            try {
                $em = $this->getDoctrine()->getManager();

                $news = $em->getRepository( 'MainBundle:News' )->findOneById( $request->get( 'id' ) );

                $news->setTitle( $request->get( 'title' ) );
                $news->setMessage( $request->get( 'message' ) );

                $em->flush();

                $response = new Response( json_encode( array( 'status' => 'ok' ) ) );
            }
            catch( \Exception $e ) {
                $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => $e->getMessage() ) ) );
            }

            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $response = new Response( json_encode( array( 'status' => 'ko', 'debug' => 'Bad request' ) ) );
        $response->headers->set( 'Content-Type', 'application/json') ;

        return $response;
    }
}
