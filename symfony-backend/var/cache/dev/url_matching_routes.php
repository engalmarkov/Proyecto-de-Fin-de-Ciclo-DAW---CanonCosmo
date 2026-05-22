<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/admin/stats' => [[['_route' => 'api_admin_stats', '_controller' => 'App\\Controller\\AdminController::getStats'], null, ['GET' => 0], null, false, false, null]],
        '/api/admin' => [[['_route' => 'admin_dashboard', '_controller' => 'App\\Controller\\AdminWebController::dashboard'], null, ['GET' => 0], null, true, false, null]],
        '/api/admin/productos' => [
            [['_route' => 'admin_products', '_controller' => 'App\\Controller\\AdminWebController::listProducts'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_admin_productos_create', '_controller' => 'App\\Controller\\ProductoController::create'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/register' => [[['_route' => 'api_register', '_controller' => 'App\\Controller\\AuthController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/calendario' => [[['_route' => 'api_calendario_list', '_controller' => 'App\\Controller\\CalendarioController::index'], null, ['GET' => 0], null, false, false, null]],
        '/api/carrito' => [[['_route' => 'cart_index', '_controller' => 'App\\Controller\\CartController::index'], null, ['GET' => 0], null, true, false, null]],
        '/api/carrito/checkout' => [[['_route' => 'cart_checkout', '_controller' => 'App\\Controller\\CartController::checkout'], null, ['POST' => 0], null, false, false, null]],
        '/api/novedades' => [[['_route' => 'app_novedades', '_controller' => 'App\\Controller\\MainController::index'], null, null, null, false, false, null]],
        '/api/pedidos' => [
            [['_route' => 'api_pedido_create', '_controller' => 'App\\Controller\\PedidoController::create'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'api_pedidos_list', '_controller' => 'App\\Controller\\PedidoController::index'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/productos-web' => [[['_route' => 'product_index', '_controller' => 'App\\Controller\\ProductWebController::index'], null, ['GET' => 0], null, true, false, null]],
        '/api/productos' => [[['_route' => 'api_productos_list', '_controller' => 'App\\Controller\\ProductoController::index'], null, ['GET' => 0], null, false, false, null]],
        '/api/login' => [
            [['_route' => 'app_login', '_controller' => 'App\\Controller\\SecurityController::login'], null, ['GET' => 0, 'POST' => 1], null, false, false, null],
            [['_route' => 'api_login_check'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/registro' => [[['_route' => 'app_register', '_controller' => 'App\\Controller\\SecurityController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/perfil' => [
            [['_route' => 'app_user_profile', '_controller' => 'App\\Controller\\UserProfileController::index'], null, ['GET' => 0, 'OPTIONS' => 1], null, false, false, null],
            [['_route' => 'api_perfil_ver', '_controller' => 'App\\Controller\\UsuarioController::verPerfil'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_perfil_update', '_controller' => 'App\\Controller\\UsuarioController::update'], null, ['PUT' => 0], null, false, false, null],
        ],
        '/api/admin/usuarios' => [[['_route' => 'api_admin_usuarios_list', '_controller' => 'App\\Controller\\UsuarioController::listarUsuarios'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|admin/(?'
                        .'|productos/(?'
                            .'|guardar(?:/([^/]++))?(*:93)'
                            .'|eliminar/([^/]++)(*:117)'
                            .'|([^/]++)(?'
                                .'|(*:136)'
                            .')'
                        .')'
                        .'|usuarios/([^/]++)/estado(*:170)'
                    .')'
                    .'|ca(?'
                        .'|lendario/([^/]++)(*:201)'
                        .'|rrito/(?'
                            .'|add/([^/]++)(*:230)'
                            .'|remove/([^/]++)(*:253)'
                        .')'
                    .')'
                    .'|p(?'
                        .'|edidos/([^/]++)(?'
                            .'|/(?'
                                .'|cancelar(*:297)'
                                .'|estado(*:311)'
                            .')'
                            .'|(*:320)'
                        .')'
                        .'|roductos(?'
                            .'|\\-web/([^/]++)(*:354)'
                            .'|/([^/]++)(*:371)'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        93 => [[['_route' => 'admin_product_save', 'id' => null, '_controller' => 'App\\Controller\\AdminWebController::save'], ['id'], ['POST' => 0], null, false, true, null]],
        117 => [[['_route' => 'admin_product_delete', '_controller' => 'App\\Controller\\AdminWebController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        136 => [
            [['_route' => 'api_admin_productos_update', '_controller' => 'App\\Controller\\ProductoController::update'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'api_admin_productos_delete', '_controller' => 'App\\Controller\\ProductoController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        170 => [[['_route' => 'api_admin_usuario_toggle', '_controller' => 'App\\Controller\\UsuarioController::toggleEstado'], ['id'], ['PATCH' => 0], null, false, false, null]],
        201 => [[['_route' => 'api_calendario_update', '_controller' => 'App\\Controller\\CalendarioController::update'], ['id'], ['PUT' => 0, 'PATCH' => 1], null, false, true, null]],
        230 => [[['_route' => 'cart_add', '_controller' => 'App\\Controller\\CartController::add'], ['id'], ['POST' => 0], null, false, true, null]],
        253 => [[['_route' => 'cart_remove', '_controller' => 'App\\Controller\\CartController::remove'], ['id'], ['DELETE' => 0], null, false, true, null]],
        297 => [[['_route' => 'api_pedido_cancel', '_controller' => 'App\\Controller\\PedidoController::cancelar'], ['id'], ['PATCH' => 0], null, false, false, null]],
        311 => [[['_route' => 'api_pedido_status', '_controller' => 'App\\Controller\\PedidoController::cambiarEstado'], ['id'], ['PATCH' => 0], null, false, false, null]],
        320 => [[['_route' => 'api_pedido_show', '_controller' => 'App\\Controller\\PedidoController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        354 => [[['_route' => 'product_show', '_controller' => 'App\\Controller\\ProductWebController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        371 => [
            [['_route' => 'api_productos_show', '_controller' => 'App\\Controller\\ProductoController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
