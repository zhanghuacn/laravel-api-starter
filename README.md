## 概览

### 目录结构一览

```
├── app
│   ├── Console
│   │   ├── Commands
│   │   └── Kernel.php
│   ├── Contracts                                // 定义 interface
│   │   ├── Enums
│   │   └── Repositories
│   │   └── Services
│   ├── Exceptions                              // 异常处理
│   │   ├── Handler.php
│   │   ├── InvalidEnumKeyException.php
│   │   └── InvalidEnumValueException.php
│   ├── Http
│   │   ├── Controllers                         // Controller 任务分发，返回响应
│   │   ├── Middleware
│   │   └── Requests                            // Request 参数效验  
│   │   └── Resources                           // Api Resource 数据转换
│   ├── Providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EloquentUserProvider.php
│   │   ├── EnumServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RepositoryServiceProvider.php
│   │   └── ServeServiceProvider.php
│   ├── Repositories
│   │   ├── Criteria                            // 数据查询条件的组装拼接
│   │   ├── Eloquent                            // 处理无关业务的数据维护逻辑（传说中的 Repository）
│   │   ├── Enums                               // 系统中的枚举/常量定义
│   │   ├── Models                              // 定义数据实体，以及实体之间的关系（Laravel 原始的 Eloquent Model）
│   │   ├── Presenters                          // 数据显示前的处理，需要引入 transformer（配合 Repository 使用）
│   │   ├── Transformers                        // 数据转换
│   │   └── Validators                          // 数据维护前的参数校验（配合 Repository 使用）
│   ├── Services
│   │   └── PostService.php                     // 具体的业务需求处理逻辑
│   └── Support                                 // 对框架的扩展，或者实际项目中需要封装一些与业务无关的通用功能（你或许会发现，这里 Support 中的实现其实放到 Laravel 项目中也能用）
│       ├── Enum                                // 扩展常量/枚举的定义和使用
│       ├── Logger                              // 扩展 Lumen 的日志支持记录到 Mongodb
│       ├── Response.php                        // 统一 API 响应格式（data、code、status、message），同时支持 Api Resource 与 Transformer
│       ├── Traits                              // class 中常用到的方法
│       └── helpers.php                         // 全局会用到的函数

## 规范的响应结构

[RESTful 服务最佳实践](https://www.cnblogs.com/jaxu/p/7908111.html)

> - code——包含一个整数类型的HTTP响应状态码。
> - status——包含文本："success"，"fail"或"error"。HTTP状态响应码在500-599之间为"fail"，在400-499之间为"error"，其它均为"success"（例如：响应状态码为1XX、2XX和3XX）。
> - message——当状态值为"fail"和"error"时有效，用于显示错误信息。参照国际化（il8n）标准，它可以包含信息号或者编码，可以只包含其中一个，或者同时包含并用分隔符隔开。
> - data——包含响应的body。当状态值为"fail"或"error"时，data仅包含错误原因或异常名称。

整体响应结构设计参考如上，相对严格地遵守了 RESTful 设计准则，返回合理的 HTTP 状态码。

考虑到业务通常需要返回不同的“业务描述处理结果”，在所有响应结构中都支持传入符合业务场景的`message`。

### 说明

- data: 
    - 查询单条数据时直接返回对象结构，减少数据层级；
    - 查询全部数据时返回数组结构；
    - 查询分页数据时返回对象结构
    - 创建或更新成功，返回修改后的数据；（也可以不返回数据直接返回空对象）
    - 删除成功时返回空对象
- status:
    - error, 客服端出错，HTTP 状态响应码在400-599之间。如，传入错误参数，访问不存在的数据资源等
    - fail，服务端出错，HTTP 状态响应码在500-599之间。如，代码语法错误，空对象调用函数，连接数据库失败，undefined index等
    - success, HTTP 响应状态码为1XX、2XX和3XX，用来表示业务处理成功。
- message: 描述执行的请求操作处理的结果；也可以支持国际化，根据实际业务需求来切换。
- code: HTTP 响应状态码；可以根据实际业务需求，调整成业务操作码

### 使用

在需要用到的地方使用 `\App\Traits\Helpers`对`\App\Http\Response`中封装的响应方法进行调用，通常是在 Controller 层中根据业务处理的结果进行响应，所以 `\App\Http\Controllers`基类中已经引入了 `Helpers`trait，可以直接在 Controller 中进行如下调用：

```php
// 操作成功情况
$this->response->success($data,$message);
$this->response->success(new  UserCollection($resource),  '成功');// 返回 API Resouce Collection
$this->response->success(new  UserResource($user),  '成功');// 返回 API Resouce
$user  =  ["name"=>"nickname","email"=>"longjian.huang@foxmail.com"];
$this->response->success($user,  '成功');// 返回普通数组

$this->response->created($data,$message);
$this->response->accepted($message);
$this->response->noContent();

// 操作失败或异常情况
$this->response->fail($message);
$this->response->errorNotFound();
$this->response->errorBadRequest();
$this->response->errorForbidden();
$this->response->errorInternal();
$this->response->errorUnauthorized();
$this->response->errorMethodNotAllowed();
```

### 操作成功时的响应结构

- 返回单条数据

```json
{
    "data": {
        "nickname": "Jiannei",
        "email": "longjian.huang@foxmail.com"
    },
    "status": "success",
    "code": 200,
    "message": "成功"
}
```

- 返回全部数据

```json
{
    "data": [
        {
            "nickname": "Jiannei",
            "email": "longjian.huang@foxmail.com"
        },
        {
            "nickname": "Qian",
            "email": "1234567891@foxmail.com"
        },
        {
            "nickname": "Turbo",
            "email": "123456789@foxmail.com"
        }
        // ...
    ],
    "status": "success",
    "code": 200,
    "message": "成功"
}
```

- 返回分页数据

```json
{
    "status": "success",
    "code": 200,
    "message": "操作成功",
    "data": {
        "data": [
            {
                "nickname": "Jiannei",
                "email": "longjian.huang@foxmail.com"
            },
            {
                "nickname": "Turbo",
                "email": "123456789@qq.com"
            },
            {
                "nickname": "Qian",
                "email": "987654321@qq.com"
            }
        ],
        "meta": {
            "pagination": {
                "total": 13,
                "count": 3,
                "per_page": 3,
                "current_page": 1,
                "total_pages": 5,
                "links": {
                    "previous": null,
                    "next": "http://lumen-api.laravel-api-starter/users?page=2"
                }
            }
        }
    }
}
```

### 操作失败时的响应结构

```json
{
    "status": "fail",
    "code": 500,
    "message": "Service error",
    "data": {}
}
```

### 异常捕获时的响应结构

整体格式与业务操作成功和业务操作失败时的一致，相比失败时，data 部分会增加额外的异常信息展示，方便项目开发阶段进行快速地问题定位。

- 自定义实现了 `ValidationException` 的响应结构：

```json
{
    "status": "error",
    "code": 422,
    "message": "Validation error",
    "data": {
        "email": [
            "The email has already been taken."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```

- `NotFoundException` 异常捕获的响应结构

关闭 debug 时：

```json
{
    "status": "error",
    "code": 404,
    "message": "Service error",
    "data": {
        "message": "No query results for model [App\\Models\\User] 19"
    }
}
```

开启 debug 时：

```json
{
    "status": "error",
    "code": 404,
    "message": "Service error",
    "data": {
        "message": "No query results for model [App\\Models\\User] 19",
        "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
        "file": "/var/www/lumen-api-starter/vendor/laravel/lumen-framework/src/Exceptions/Handler.php",
        "line": 107,
        "trace": [
            {
                "file": "/var/www/lumen-api-starter/app/Exceptions/Handler.php",
                "line": 55,
                "function": "render",
                "class": "Laravel\\Lumen\\Exceptions\\Handler",
                "type": "->"
            },
            {
                "file": "/var/www/lumen-api-starter/vendor/laravel/lumen-framework/src/Routing/Pipeline.php",
                "line": 72,
                "function": "render",
                "class": "App\\Exceptions\\Handler",
                "type": "->"
            },
            {
                "file": "/var/www/lumen-api-starter/vendor/laravel/lumen-framework/src/Routing/Pipeline.php",
                "line": 50,
                "function": "handleException",
                "class": "Laravel\\Lumen\\Routing\\Pipeline",
                "type": "->"
            }
            // ...
        ]
    }
}
```

- 其他类型异常捕获时的响应结构

```json
{
    "status": "fail",
    "code": 500,
    "message": "syntax error, unexpected '$user' (T_VARIABLE)",
    "data": {
        "message": "syntax error, unexpected '$user' (T_VARIABLE)",
        "exception": "ParseError",
        "file": "/var/www/lumen-api-starter/app/Http/Controllers/UsersController.php",
        "line": 34,
        "trace": [
            {
                "file": "/var/www/lumen-api-starter/vendor/composer/ClassLoader.php",
                "line": 322,
                "function": "Composer\\Autoload\\includeFile"
            },
            {
                "function": "loadClass",
                "class": "Composer\\Autoload\\ClassLoader",
                "type": "->"
            },
            {
                "function": "spl_autoload_call"
            }
           // ...
        ]
    }
}
```


### 根据实际业务场景定制的响应返回

- 操作成功

拿「登录成功返回用户信息」举个栗子：

**第一种**：指定 message

使用

```php
return $this->response->success($user,'注册成功');
```

返回

```json
{
    "status": "success",
    "code": 200,
    "message": "注册成功",
    "data": {
        "nickname": "Jiannei",
        "email": "longjian.huang@foxmail.com"
  }
}
```

**第二种**：message 参数为空，使用 ResponseConstant 中自定义的业务操作码，读取 `resources/lang/zh-CN/response.php`中的业务描述信息，也就说明支持多语言了

```php
return $this->response->success($user,'',ResponseConstant::SERVICE_LOGIN_SUCCESS);
```

```json
{
    "status": "success",
    "code": 200101,
    "message": "注册成功",
    "data": {
        "nickname": "Jiannei",
        "email": "longjian.huang@foxmail.com"
    }
}
```

**注意**：两种的返回数据有中的 code 不同，第二种返回的是自定义的操作码，具体定义规则可以查看 `app/Constants/ResponseConstant.php`

- 操作失败

直接抛出 `HttpException`，使用自定义的错误码就可以了，如此简单。

使用

```php
abort(ResponseConstant::SERVICE_LOGIN_ERROR);
// 等价于
throw new \Symfony\Component\HttpKernel\Exception\HttpException(ResponseConstant::SERVICE_LOGIN_ERROR);
```

返回

```json
{
    "status": "fail",
    "code": 500102,
    "message": "登录失败",
    "data": {
        "message": ""
    }
}
```

### 特别说明

使用 Postman 等 Api 测试工具的使用需要添加 `X-Requested-With：XMLHttpRequest`或者`Accept:application/json`header 信息来表明是 Api 请求，否则在异常捕获到后返回的可能不是预期的 JSON 格式响应。

## Repository & Service 模式架构

在添加这部分描述的时候，联想到了 Vue 中的 Vuex，熟悉 Vuex 的同学可以类比一下。

```
Controller => dispatch，校验请求后分发业务处理
Service => action，具体的业务实现
Repository => state、mutation、getter，具体的数据维护
```


### 职责说明

**Controller 岗位职责**：

1. 校验是否有必要处理请求，是否有权限和是否请求参数合法等。无权限或不合法请求直接 response 返回格式统一的数据
2. 将校验后的参数或 Request 传入 Service 中具体的方法，安排 Service 实现具体的功能业务逻辑
3. Controller 中可以通过`__construct()`依赖注入多个 Service。比如 `UserController` 中可能会注入 `UserService`（用户相关的功能业务）和 `EmailService`（邮件相关的功能业务）
4. 使用统一的 `$this->response`调用`sucess`或`fail`方法来返回统一的数据格式
5. （可选）使用 Laravel Api Resource 的同学可能在 Controller 中还会有转换数据的逻辑。比如，`return $this->response->success(new UserCollection($resource));`或`return $this->response->success(new UserResource($user));`
    
**Service 岗位职责**：

1. 实现项目中的具体**功能**业务。所以 Service 中定义的方法名，应该是用来**描述功能或业务**的（动词+业务描述）。比如`handleListPageDisplay`和`handleProfilePageDisplay`，分别对应用户列表展示和用户详情页展示的需求。
2. 处理 Controller 中传入的参数，进行**业务判断**
3.（可选）根据业务需求配置相应的 Criteria 和 Presenter 后（不需要的可以不用配置，或者将通用的配置到 Repository 中）
4. 调用 Repository 处理**数据的逻辑**
5. Service 可以不注入 Repository，或者只注入与处理当前业务**存在数据关联**的 Repository。比如，`EmailService`中或许就只有调用第三方 API 的逻辑，不需要更新维护系统中的数据，就不需要注入 Repository；`OrderService`中实现了订单出库逻辑后，还需要生成相应的财务结算单据，就需要注入 `OrderReposoitory`和`FinancialDocumentRepository`，财务单据中的原单号关联着订单号，存在着数据关联。
6. Service 中不允许调用其他 Service，保持职责单一，如有需要，应该考虑 Controller 中调用

**Repository 岗位职责**：

1. 只负责**数据维护**的逻辑，数据怎么查询、更新、创建、删除，以及**相关联**的数据如何维护。所以 Repository 中定义的方法名，应该是用来描述**数据是以怎样的形式去维护的**。比如 `searchUsersByPage`、`searchUsersById`和`insertUser`。
2. Repository 只绑定**一个** model，**只允许**维护与当前 Repository 绑定的 Model 数据，**最多允许**维护与绑定的 Model 存在关联关系的 Model。比如，订单 OrderRepository 中会涉及到更新订单商品 OrderGoodsRepository 的数据。
3. Repository 中可以配置条件查询（Criteria）、数据校验（Validator）和数据转换显示（Presenter），通常是将通用的配置在 Repository，不通用的独立出相应文件。
4. Repository 本质是在 Laravel ORM Model 中的一层封装，可以完全不用担心使用 Repository 等同于放弃了 ORM 灵活性。原先常用的 ORM 方法**并没有移除**，只是位置从 Controller 中换到了 Repository 而已。
5. Repository 中的 `$this->model` 实际就是绑定的 Model 实例，所以就有了这样的写法`$this->model::all()`,与原先的 ORM 写法`User::all()`是完全等价的。
6. Repository 中不允许引入其他 Repository

**Model 岗位职责**：

经过前面的 Service 和 Repository 「分层」，剥离了可能存在于 Model 中的很多逻辑，比如校验参数，拼接查询，处理业务和转换数据结构等。所以，现如今的 Model 只需要相对简单地数据定义就可以了。比如，对数据表的定义，字段的映射，以及数据表之间关联关系等，提供给 Repository 中使用就够了。

### Repository 模式中涉及到的一些名词理解

完整的执行顺序：`Criteria -> Validator -> Presenter`

**Constants**:

这个是 lumen-api-starter 新增的部分，用来定义应用系统中常量的数据。

**Criteria**：[l5-repository criteria](https://github.com/andersao/l5-repository#example-the-criteria) 

作用类似 Eloquent Model 中的 Scope 查询，把常用的查询提取出来，但是比 Scope 更强大。
可以省去 Model 中大量的根据请求参数判断并拼接查询条件的代码，与此同时，能够做到将多种数据之间存在的**通用**筛选条件剥离出来。
比如 `make:repository`创建生成的 Repository 中默认包含以下代码，就是给 Repository 默认配置了一个 RequestCriteria，就可以直接使用下面的方式来过滤数据，难道不香吗，嗯？

```php
public function boot()
{
    $this->pushCriteria(app(RequestCriteria::class));
}
```

```
http://prettus.local/users?search=age:17;email:john@gmail.com&searchJoin=and

Filtering fields

http://prettus.local/users?filter=id;name

[
    {
        "id": 1,
        "name": "John Doe"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum"
    },
    {
        "id": 3,
        "name": "Laravel"
    }
]
Sorting the results

http://prettus.local/users?filter=id;name&orderBy=id&sortedBy=desc

[
    {
        "id": 3,
        "name": "Laravel"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum"
    },
    {
        "id": 1,
        "name": "John Doe"
    }
]
```

**Presenter**：[L5-repository presenters](https://github.com/andersao/l5-repository#presenters)

可选，使用 Api Resource 的同学可以略过。需要安装 `composer require league/fractal`，Dingo Api 中的 transformer 也是使用了这个扩展包。

作用类似 Laravel 的 Api Resource，或者可以说 Api Resource 是 Transformer 的轻量实现。

L5-repository 认为你将数据表结构的**数据转换**后是为了用来**展示**的，所以它将数据转换相关的逻辑独立出来，称为 Presenter。本质是整合了 fractal 中的 transformer 功能。 

Transformer 的优秀之处这里暂不做讨论，因为这里的主角是 Presenter。[传送门](https://fractal.thephpleague.com/)

先对比一下几种数据转换方式：

- Dingo Api 中 transformer 的使用方式

在 Controller 中调用 Response 中的 item 返回数据时传入 transformer 来转换数据

```php
return $this->item($user, new UserTransformer, ['key' => 'user']);
```


- Laravel 中 Api Resource 的使用方式

在 Controller 中调用 Resource 或者 ResourceCollection 转换数据

```php
 //return $this->response->success(new UserResource($user));// 使用 lumen-api-starter 统一 code\status\message\data
return new UserResource($user);// 未统一响应结构
```

- L5-repository 中 transformer 的使用方式（为了避免混淆，这里讲的是独立出文件的形式，当然也有可以直接在 model 或 repository 中定义的方式，更详细的使用请参考 l5-repository 的说明）

需要先定义 transformer，然后在 Presenter 中「注册」，最后在调用 Repository 时使用。

举例：

定义 UserTransformer

```
// app/Repositories/Transformers/UserTransformer.php
<?php


namespace App\Repositories\Transformers;

use App\Repositories\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'nickname' => $user->name,
            'email' => $user->email,
        ];
    }
}

```

「注册」到 UserPresenter

```php
// app/Repositories/Presenters/UserPresenter.php
<?php


namespace App\Repositories\Presenters;


use App\Repositories\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;use Prettus\Repository\Presenter\FractalPresenter;

class UserPresenter extends FractalPresenter
{
    /**
     * Prepare data to present
     *
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}

```

在调用 repository 的时候使用

```php
// app/Services/UserService.php
public function listPage(Request $request)
{
    $this->repository->pushCriteria(new UserCriteria($request));
    $this->repository->setPresenter(UserPresenter::class);

    return $this->repository->searchUsersByPage();
}
```

### 规范

* 命名规范：

- controller：
    - 类名：名词，复数形式，描述是对整个资源集合进行操作；当没有集合概念的时候。换句话说，当资源只有一个的情况下，使用单数资源名称也是可以的——即一个单一的资源。例如，如果有一个单一的总体配置资源，你可以使用一个单数名称来表示
    - 方法名：动词+名词，体现资源操作。如，store\destroy

- service:
    - 类名：名词，单数。比如`UserService`、`EmailService`和`OrderService`
    - 方法名：`动词+名词`，描述能够实现的业务需求。比如：`handleRegistration`表示实现用户注册功能。

- repository
    - 类名：名词，单数。`make:repository`命令可以直接生成。
    - 方法名：动词+名词，描述数据的维护（CRUD）。   比如：`searchUsersByPage`
    - 可能会出现的动词：createXXX（add）;searchXXX；queryXXX、findXXX、fetch（get）；updateXXX；deleteXXX（destroy）；组合形式：with\Join...，如 searchOrdersLeftJoinGodds
    - 通常情况 Database、Cache、Redis、Carbon 操作只能出现在 repository
