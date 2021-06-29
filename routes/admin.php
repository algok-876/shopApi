<?php

$api = app('Dingo\Api\Routing\Router');

$params = [
    'middleware' => [
        'jwt.role:admin',
//        'check.permission',
        // 减少transform的包裹层
        'serializer:array',
        'bindings'
    ]
];

$api->version('v1', function ($api) use($params) {
    $api->group(['prefix' => 'admin'], function ($api) use($params) {
        $api->post('login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
        $api->group(['middleware' => 'auth:admin'], function ($api) {
            $api->post('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout']);
            $api->post('refresh', [App\Http\Controllers\Admin\AuthController::class, 'refresh']);
            $api->post('me', [App\Http\Controllers\Admin\AuthController::class, 'me']);
            // 阿里云OSS上传token
            $api->get('oss/token', [App\Http\Controllers\Auth\OssController::class, 'token']);
        });

        $api->group($params, function ($api) {
            /**
            用户管理
             */
            // 禁用用户
            $api->patch('users/{user}/lock', [\App\Http\Controllers\Admin\UserController::class, 'lock'])->name('users.lock');
            // 用户管理资源路由
            $api->resource('users', \App\Http\Controllers\Admin\UserController::class, [
                'only' => ['index', 'show', 'store']
            ]);
            $api->post('users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'giveRole'])->name('users.giveRole');
            $api->delete('users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'removeRole'])->name('users.removeRole');
            $api->post('users/{user}/permission', [\App\Http\Controllers\Admin\UserController::class, 'givePermission'])->name('users.givePermission');
            $api->delete('users/{user}/permission', [\App\Http\Controllers\Admin\UserController::class, 'revokePermission'])->name('users.revokePermission');

            /**
            分类管理
             */
            // 分类管理资源路由
            $api->patch('category/{category}/status', [\App\Http\Controllers\Admin\CategoryController::class, 'status'])->name('category.status');
            $api->resource('category', \App\Http\Controllers\Admin\CategoryController::class, [
                'except' => ['destroy']
            ]);

            /**
            商品管理
             */
            // 商品状态
            $api->patch('goods/{good}/status', [\App\Http\Controllers\Admin\GoodsController::class, 'is_on'])->name('goods.status');
            // 商品是否推荐
            $api->patch('goods/{good}/recommend', [\App\Http\Controllers\Admin\GoodsController::class, 'is_recommend'])->name('goods.recommend');
            $api->resource('goods', \App\Http\Controllers\Admin\GoodsController::class, [
                'except' => ['destroy']
            ]);

            /**
            评论管理
             */
            $api->get('comments', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('comments.index');
            $api->get('comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'show'])->name('comments.show');
            $api->patch('comments/{comment}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply'])->name('comments.reply');

            /**
            订单管理
             */
            $api->get('orders', [\App\Http\Controllers\Admin\OrdersController::class, 'index'])->name('orders.index');
            $api->get('orders/{orders}', [\App\Http\Controllers\Admin\OrdersController::class, 'show'])->name('orders.show');
            $api->patch('orders/{orders}/post', [\App\Http\Controllers\Admin\OrdersController::class, 'post'])->name('orders.post');

            $api->resource('slides', \App\Http\Controllers\Admin\SlidesController::class, [
                'except' => ['destroy']
            ]);
            $api->patch('slides/{slide}/status', [\App\Http\Controllers\Admin\SlidesController::class, 'status'])->name('slides.status');

            $api->get('menus', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menus.index');

            /**
            角色权限分配
             */
            // 返回所有的权限
            $api->get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index');
            // 返回角色以及对应的权限
            $api->get('roles', [\App\Http\Controllers\Admin\RolesController::class, 'index'])->name('roles.index');
            // 返回所有的角色
            $api->get('roles/list', [\App\Http\Controllers\Admin\RolesController::class, 'list'])->name('roles.list');
            // 添加角色
            $api->post('roles', [\App\Http\Controllers\Admin\RolesController::class, 'store'])->name('roles.store');
            $api->post('roles/{role}/update', [\App\Http\Controllers\Admin\RolesController::class, 'update'])->name('roles.update');
            $api->delete('roles/{role}/delete', [\App\Http\Controllers\Admin\RolesController::class, 'delete'])->name('roles.delete');
            // 为角色添加权限
            $api->post('roles/{role}/permission', [\App\Http\Controllers\Admin\RolesController::class, 'addPermission'])->name('roles.addPermission');
            $api->put('roles/{role}/permission', [\App\Http\Controllers\Admin\RolesController::class, 'updatePermission'])->name('roles.updatePermission');
        });
    });
});
