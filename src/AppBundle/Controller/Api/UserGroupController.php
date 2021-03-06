<?php

namespace AppBundle\Controller\Api;


use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserRole;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 *
 * @Route("/api/user-groups")
 */
class UserGroupController extends Controller
{
    /**
     * @Route("", options={"expose"=true})
     * @Method("POST")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $this->get('json_api.validator.json_request_validator')
            ->validate($request);

        $parsedRequest = $this->get('json_api.parser.request.new_user_group')
            ->parse($request);

        $parsedData = $parsedRequest->getData();

        if (!$parsedRequest->isPassed()) {
            return new JsonResponse($parsedRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $userGroup = new UserGroup($parsedData->data->attributes->name);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userGroup);
        $em->flush($userGroup);

        return $this->get('json_api.response.json_api_response_builder')
            ->make(
                $userGroup,
                'userGroups',
                Response::HTTP_CREATED
            );
    }

    /**
     * @Route("/{userGroup}", options={"expose"=true})
     * @Method("DELETE")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, UserGroup $userGroup)
    {
        $this->get('json_api.validator.json_request_validator')
            ->validate($request);

        $parsedRequest = $this->get('json.api.parser.request.delete_user_group')
            ->parse($request);

        $data = $parsedRequest->getData();

        if (!$parsedRequest->isPassed()) {
            return new JsonResponse($parsedRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($userGroup);
        $em->flush($userGroup);

        return $this->get('json_api.response.json_api_response_builder')
            ->make(
                null,
                'userGroups',
                Response::HTTP_NO_CONTENT
            );
    }

    /**
     * @Route("/{userGroup}", options={"expose"=true})
     * @Method("PATCH")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function updateAction(Request $request, UserGroup $userGroup)
    {
        $this->get('json_api.validator.json_request_validator')
            ->validate($request);

        $parsedRequest = $this->get('json.api.parser.request.update_user_group')
            ->parse($request);

        $instructions = $parsedRequest->getData();

        if (!$parsedRequest->isPassed()) {
            return new JsonResponse($parsedRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $attributes = $instructions->data->attributes;
        if (isset($attributes->name)) {
            $userGroup->setName($attributes->name);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($userGroup);
        $em->flush($userGroup);

        return $this->get('json_api.response.json_api_response_builder')
            ->make(
                $userGroup,
                'userGroups',
                Response::HTTP_OK
            );
    }

    /**
     * @Route("/{userGroup}/relationships/user-roles", options={"expose"=true})
     * @Method("POST")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function addUserRolesAction(Request $request, UserGroup $userGroup)
    {
        $this->get('json_api.validator.json_request_validator')
            ->validate($request);

        $parsedRequest = $this->get('json.api.parser.request.user_role_to_user_group')
            ->parse($request);

        $relationShips = $parsedRequest->getData();

        if (!$parsedRequest->isPassed()) {
            return new JsonResponse($parsedRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $userRoleIds = [];
        foreach ($relationShips->data as $relationShip) {
            $userRoleIds[] = $relationShip->id;
        }

        $em = $this->getDoctrine()->getManager();
        $userRoleRepository = $em->getRepository(UserRole::class);

        $userRoles = $userRoleRepository->findById($userRoleIds);

        $foundUserRoleIds = [];
        foreach ($userRoles as $userRole) {
            $userGroup->addRole($userRole);
            $foundUserRoleIds[] = $userRole->getId();
        }

        $missingRoles = array_diff($userRoleIds, $foundUserRoleIds);

        if (count($missingRoles) !== 0) {
            return new JsonResponse('Missing user roles:, %s', implode(', ', $missingRoles), Response::HTTP_BAD_REQUEST);
        }

        $em->persist($userGroup);
        $em->flush($userGroup);

        return $this->get('json_api.response.json_api_response_builder')
            ->make(
                $userGroup,
                'userGroups',
                Response::HTTP_OK
            );
    }

    /**
     * @Route("/{userGroup}/relationships/user-roles", options={"expose"=true})
     * @Method("DELETE")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function deleteUserRolesAction(Request $request, UserGroup $userGroup)
    {
        $this->get('json_api.validator.json_request_validator')
            ->validate($request);

        $parsedRequest = $this->get('json.api.parser.request.user_role_to_user_group')
            ->parse($request);

        $relationShips = $parsedRequest->getData();

        if (!$parsedRequest->isPassed()) {
            return new JsonResponse($parsedRequest->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $userRoleIds = [];
        foreach ($relationShips->data as $relationShip) {
            $userRoleIds[] = $relationShip->id;
        }

        $em = $this->getDoctrine()->getManager();
        $userRoleRepository = $em->getRepository(UserRole::class);

        $userRoles = $userRoleRepository->findById($userRoleIds);

        $foundUserRoleIds = [];
        foreach ($userRoles as $userRole) {
            $userGroup->removeRole($userRole);
            $foundUserRoleIds[] = $userRole->getId();
        }

        $missingRoleIds = array_diff($userRoleIds, $foundUserRoleIds);

        if (count($missingRoleIds) !== 0) {
            return new JsonResponse('Missing user roles:, %s', implode(', ', $missingRoleIds), Response::HTTP_BAD_REQUEST);
        }

        $em->persist($userGroup);
        $em->flush($userGroup);

        return $this->get('json_api.response.json_api_response_builder')
            ->make(
                $userGroup,
                'userGroups',
                Response::HTTP_OK
            );
    }
}
