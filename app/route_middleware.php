<?php
/**
*
*/

//	This file contains 'route-level' middleware. These can be attatched to any route
//	by ->add($name); at the end of the route declaration. See examples in the auth
//	routes where users are required to either be a guest or be authenticated to view
//	the pages.

use App\Common\User;
use App\Common\User\UserPermission;

/**
* Requires the user to be authenticated to view the route.
*/
$authenticated = function ($request, $response, $next){
    if (!$this->auth)
    {
        return $response->withRedirect($this->router->pathFor('home'));
    }
    return $next($request, $response);
};

/**
* Requires the user to be a guest to view the route.
*/
$guest = function ($request, $response, $next) {
    if ($this->auth)
    {
        return $response->withRedirect($this->router->pathFor('home'));
    }
    return $next($request, $response);
};
