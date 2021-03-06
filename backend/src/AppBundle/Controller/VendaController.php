<?php

namespace App\AppBundle\Controller;

use App\AppBundle\Entity\Venda;
use App\AppBundle\Form\VendaType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendaController extends FOSRestController
{
    /**
     * Retorna uma lista com todos as vendas cadastradas no sistema, ordenadas alfabeticamente.
     *
     * @return View
     *
     * @ApiDoc(
     *   section = "Vendas",
     *   resource = true,
     *   description = "Retorna a lista de vendas",
     *   output = "array<App\AppBundle\Form\VendasType>",
     *   statusCodes = {
     *     Response::HTTP_OK = "Retornado em caso de sucesso"
     *   }
     * )
     */
    public function getVendasAction()
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Venda')->findBy([], ['data' => 'ASC']);
        return $this->view(['vendas' => $entities], Response::HTTP_OK);
    }

    /**
     * Retorna as informações detalhadas de uma venda cadastrada no sistema.
     *
     * #### Markdown
     *
     * Pode ser utilizado [markdown](http://lmgtfy.com/?q=Markdown+Syntax&l=1) para escrever a documentação.
     *
     *
     * @param int $id ID da venda para buscar.
     * @return View
     *
     * @ApiDoc(
     *   section = "Vendas",
     *   description = "Retorna os dados de uma venda",
     *   output = "App\AppBundle\Form\VendaType",
     *   statusCodes = {
     *     Response::HTTP_OK = "Retornado em caso de sucesso",
     *     Response::HTTP_NOT_FOUND = "Se a venda informada não existe"
     *   }
     * )
     */
    public function getVendaAction($id)
    {
        $entity = $this->getEntity($id);
        return $this->view(['venda' => $entity], Response::HTTP_OK);
    }

    /**
     * Cria uma nova venda.
     *
     * @param Request $request
     * @return View
     *
     * @ApiDoc(
     *   section = "Vendas",
     *   description = "Cria uma venda",
     *   input = "App\AppBundle\Form\VendaType",
     *   statusCodes = {
     *     Response::HTTP_OK = "Retornado em caso de sucesso"
     *   }
     * )
     */
    public function postVendaAction(Request $request)
    {
        $entity = new Venda();
        
        $logger = $this->get('logger');

        // $logger->info($request->get('cliente'));

        $cliente =  $this->getDoctrine()->getRepository('AppBundle:Cliente')->find($request->get('cliente'));
        $empresa =  $this->getDoctrine()->getRepository('AppBundle:Empresa')->find($request->get('empresa'));
        
        $logger->info($request->get('cliente'));
        $logger->info($request->get('empresa'));

        if(!$cliente){
            throw $this->createNotFoundException('Cliente inválido');
        }

        if(!$empresa){
            throw $this->createNotFoundException('Empresa inválida');
        }

        $entity->setCliente($cliente);
        $entity->setEmpresa($empresa);

        // Formulários podem ser usados para gerar o HTML dos forms, mas isso não é usado na API.
        // Aqui são usados apenas para a validação dos dados.
        $form = $this->createForm(new VendaType(), $entity);
        $form->bind($request->get('venda'));


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->view(['venda' => $entity], Response::HTTP_OK);
        }
    }

    /**
     * Cria ou edita uma venda com o ID especificado.
     *
     * Mais detalhes seguem aqui sobre o funcionamento do método.
     *
     * @param Request $request
     * @param int $id Id of the entity
     * @return View
     *
     * @ApiDoc(
     *   section = "Venda",
     *   description = "Edita uma venda",
     *   input = "App\AppBundle\Form\VendaType",
     *   statusCodes = {
     *     Response::HTTP_OK = "Retornado em caso de sucesso",
     *     Response::HTTP_NOT_FOUND = "Se a venda informada não existe"
     *   }
     * )
     */
    public function putVendaAction(Request $request, $id)
    {

        $cliente =  $this->getDoctrine()->getRepository('AppBundle:Cliente')->find($request->get('cliente'));
        $empresa =  $this->getDoctrine()->getRepository('AppBundle:Empresa')->find($request->get('empresa'));
        

        if(!$cliente){
            throw $this->createNotFoundException('Cliente inválido');
        }

        if(!$empresa){
            throw $this->createNotFoundException('Empresa inválida');
        }
        
        $entity->setCliente($cliente);
        $entity->setEmpresa($empresa);

        $entity = $this->getEntity($id);
        $form = $this->createForm(new VendaType(), $entity);
        $form->bind($request->get('venda'));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->view(['venda' => $entity], Response::HTTP_OK);
        }
    }

    /**
     * Delete action
     *
     * @param int $id Id of the entity
     * @return View
     *
     * @ApiDoc(
     *   section = "Vendas",
     *   description = "Exclui uma venda",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Retornado em caso de sucesso",
     *     Response::HTTP_NOT_FOUND = "Se a empresa informada não existe"
     *   }
     * )
     */
    public function deleteVendaAction($id)
    {
        $entity = $this->getEntity($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get entity instance
     *
     * @param int $id Id of the entity
     * @return Empresa
     */
    protected function getEntity($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Venda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Essa venda não existe.');
        }

        return $entity;
    }
}
