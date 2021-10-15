# Symfony Api Adapter
## Описание:
Данный пакет является Симфони-бандлом.
Он реализует паттерн CQRS, давая возможность работать с Query, и, Command запросами.

Проблема, которую решает данный пакет:
Снимает с разработчика необходимость писать повторяющийся код в UI-точках входа в приложение(Controllers, CommandBus),
далее Controller.

Для работы с Command пакет предоставляет определенный интерфейс для передачи "контекстов" из слоя "Controller" на слой
"Application(UseCase)".

В случае с Query работа пакета полностью автоматизирована, для корректной фильтрации и выборки по идентификатору
необходимо только указать некоторую конфигурацию. Никаких больше ручных SQL-запросов и ручной возни с QueryBuilder ;)

## Внешнее использование:
### Пример строки запроса:
```http request
GET /clients?filter[emails.email][like]=26d@&sort=-createdAt,updatedAt&page[number]=1&page[size]=20&filter[userId][eq]=ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23&filter[name.translations.last][eq]=Tesla&lang=ru
```

### Контракты:
#### Сортировка:
##### Описание: 
Сортировка задается параметром "sort". 
Направление сортировки задается опциональным знаком '-' перед названием свойства, по которому предполагается сортировка.
Если знак '-' присутствует, то сортировка по этому полю ведется с модификатором DESC, иначе - ASC.
Допускается сортировка по нескольким полям агрегата. Для этого необходимо написать несколько полей, разделив их символом
','. Чем раньше было указано поле, тем больший "вес" оно имеет при выборке.
##### Пример:
```
sort='-createdAt,updatedAt'
```

#### Пагинация:
#### Сортировка:
Пагинация задается параметром "page".
Параметр имеет два поля - number и size.
- "number" указывает на номер страницы, которую запрашивает клиент. По умолчанию: 1
- "size" указывает размер страницы(сколько агрегатов должно быть отображено). По умолчанию: 20
##### Описание:
```
page[number]='1'
page[size]='20'
```

#### Фильтрация:
##### Описание:
Операторы поиска:

Название | Допустимые значения | Пример | Описание
--- | --- | --- | ---
NOT_IN | 'not-in' | filter[status][not-in][]='blocked' | Свойство не содержит ни одно из указанных значений
IN | 'in' | filter[status][in][]='active' | Свойство содержит одно из указанных значений
RANGE | 'range' | filter[rating][range]='17,42' | Свойство находится в выбранном указанном диапазоне
IS_NULL | 'is-null' | filter[gender][is-null] | Свойство равно null
NOT_NULL | 'not-null' | filter[name][not-null] | Свойство не равно null
LESS_THAN | 'less-than', '<', 'lt' | filter[rating][<]='94' | Свойство меньше указанного значения
GREATER_THAN | 'greater-than', '>', 'gt' | filter[rating][>]='42' | Свойство больше указанного значения
LESS_OR_EQUALS | 'less-or-equals', '<=', 'lte' | filter[rating][<=]='15' | Свойство меньше или равно указанному значению
GREATER_OR_EQUALS | 'greater-or-equals', '>=', 'gte' | filter[rating][>=]='97' | Свойство больше или равно указанному значению
LIKE | 'like' | filter[email][like]='26d@' | Свойство содержит часть указанного значения
NOT_LIKE | 'not-like' | filter[email][not-like]='27d@' | Свойство не содержит часть указанного значения
EQUALS | 'equals', '=', 'eq' | filter[userId][eq]='ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23' | Свойство эквивалентно указанному значению
NOT_EQUALS | 'not-equals', '!=', '<>', 'neq' | filter[userId][neq]='aaf92b7a-8e05-4f4b-9f0a-e4360dbacb23' | Свойство не эквивалентно указанному значению

##### Пример:
```
filter[userId][eq]='ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23'
filter[name.translations.last][eq]='Tesla'
filter[emails.email][like]='26d@'
filter[userId][eq]='ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23'
filter[name.translations.last][eq]='Tesla'
filter[emails.email][in][]='0791d11b6a952a3804e7cb8a220d0a9b@mail.ru'
filter[emails.email][in][]='0891d11b6a952a3804e7cb8a220d0a9b@mail.ru'
```

## Определения:
### InputContract
#### Описание:
InputContract - это DTO-описание входящих параметров точки входа в приложение.
Все поля DTO должны являться скалярными типами.
Может содержать в себе "Validation Asserts".
Может использоваться для формирования авто-документации.

#### Назначение:
Сериализация и валидация данных из Request, формирование авто-документации.

#### Пример:
```php
<?php

declare(strict_types=1);

namespace Path\To\Class;

use Bundle\UIBundle\Core\Contract\Command\InputContractInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Contract implements InputContractInterface
{
    #[Assert\Uuid]
    #[Assert\NotBlank]
    public string $userId;

    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;
}
```

### Command
#### Описание:
Command - это DTO, которая заполняется уже провалидированными данными из InputContract.
Отличие состоит в том, что данный тип DTO уже может содержать не только скалярные типы, но и ValueObject. 
По этой причине не может использоваться для формирования авто-документации.

#### Назначение: 
Передача подготовленных, сгруппированных данных в Handler(UseCase).

#### Пример:
```php
<?php

declare(strict_types=1);

namespace Path\To\Class;

use App\Path\To\Entity\User\ValueObject\Id as UserId;
use Bundle\UIBundle\Core\Contract\Command\CommandInterface;

final class Command implements CommandInterface
{
    public string $email;
    public UserId $userId;
}
```

### Handler
#### Описание:
Handler - это сценарий использования сервиса. Обычно имеет метод "handle", принимающий в качестве параметра "CommandDto".

#### Назначение:
Исполнение сценариев использования сервиса

#### Пример:
```php
<?php

declare(strict_types=1);

namespace Path\To\Class;

use App\Model\Flusher;
use App\Path\To\Entity\Client;
use App\Path\To\Entity\ClientRepository;
use Bundle\UIBundle\Core\Contract\Command\CommandInterface;
use Bundle\UIBundle\Core\Contract\Command\HandlerInterface;

class Handler implements HandlerInterface
{
    private ClientRepository $clientRepository;
    private Flusher $flusher;

    public function __construct(ClientRepository $clientRepository, Flusher $flusher)
    {
        $this->clientRepository = $clientRepository;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     */
    public function handle(CommandInterface $command): void
    {
        $client = Client::create(
            $command->userId
        );

        $this->clientRepository->add($client);
        $client->addEmail($command->email);

        $this->flusher->flush($client);
    }
}
```

### OutputContract
#### Описание:
OutputContract - это DTO, которую формирует и возвращает Handler(при необходимости). Содержит в себе только Get-методы,
в которых может храниться логика о том, каким образом вывести значение того, или иного поля.
Может использоваться для формирования авто-документации.

#### Назначение:
Создание контракта возвращаемых данных приложением, формирование авто-документации

#### Пример:
```php
<?php

declare(strict_types=1);

namespace Path\To\Class;

use App\Model\Profile\Clients\Entity\Client\Client;
use App\Model\Profile\Clients\Entity\Email\Email;
use Bundle\UIBundle\Core\Contract\Command\LocalizationOutputContractInterface;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

class CommonOutputContract implements LocalizationOutputContractInterface
{
    /** @Ignore() */
    public Client $client;
    /** @Ignore() */
    private string $locale;

    public function __construct(Client $client, string $locale)
    {
        $this->client = $client;
        $this->locale = $locale;
    }

    public function getId(): string
    {
        return $this->client->getId()->getValue();
    }

    /**
     * @return string[]
     */
    public function getEmails(): array
    {
        return array_map(function (Email $email) {
            return $email->getEmail();
        }, $this->client->getEmails());
    }

    public function getMiddleName(): ?string
    {
        return $this->client->getName()?->getTranslation($this->locale)?->getMiddle();
    }

    public function getLastName(): ?string
    {
        return $this->client->getName()?->getTranslation($this->locale)?->getLast();
    }

    public function getFirstName(): ?string
    {
        return $this->client->getName()?->getTranslation($this->locale)?->getFirst();
    }

    public function getGender(): ?string
    {
        return $this->client->getGender()?->toScalar();
    }

    public function getCreatedAt(): string
    {
        return $this->client->getCreatedAt()->format(DateTimeInterface::ATOM);
    }

    public function getLang(): string
    {
        return $this->locale;
    }
}
```

## Внутреннее использование:

### Query:
#### GetOne:
Пример Read-action:
```php
use App\Path\To\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Bundle\UIBundle\Core as UI;

class Controller {
    /**
     * @Route("/{id}.{_format}", methods={"GET"}, name=".read", defaults={"_format"="json"})
     * @OA\Response(
     *     response=200,
     *     description="Read Entity",
     *     @OA\JsonContent(
     *         allOf={
     *             @OA\Schema(ref=@Model(type=UI\Contract\ApiFormatter::class)),
     *             @OA\Schema(type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="entity",
     *                         ref=@Model(type=CommonOutputContract::class)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     */
    public function read(
        string $id,
        UI\CQRS\Query\GetOne\Processor $processor,
        UI\Dto\OutputFormat $outputFormat,
        UI\Dto\Locale $locale
    ): Response {
        $context = new UI\CQRS\Query\GetOne\Context(
            outputFormat: $outputFormat->getFormat(),
            entityId: $id,
            targetEntityClass: Entity::class,
            outputDtoClass: CommonOutputContract::class,
            locale: $locale
        );
    
        $processor->process($context);
        return $processor->makeResponse();
    }
}
```
#### Search:
Пример Search-action:
```php
use App\Path\To\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Bundle\UIBundle\Core as UI;

class Controller {
    /**
     * @Route(".{_format}", methods={"GET"}, name=".search", defaults={"_format"="json"})
     * @OA\Get(
     *     @OA\Parameter(
     *          name="searchParams",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              ref=@Model(type=UI\Contract\Filter\FilterSortPagination::class)
     *          ),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Search by Clients",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=UI\Contract\ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entities",
     *                          ref=@Model(type=CommonOutputContract::class)
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *              )
     *          }
     *      )
     * )
     */
    public function search(
        UI\CQRS\Query\Search\Processor $processor,
        UI\Service\Filter\SearchQuery $searchQuery,
        UI\Dto\Locale $locale,
        UI\Dto\OutputFormat $outputFormat
    ): Response {
        $context = new UI\CQRS\Query\Search\Context(
            targetEntityClass: Entity::class,
            outputFormat: $outputFormat->getFormat(),
            outputDtoClass: UseCase\CommonOutputContract::class,
            filterBlackList: ['id'],
            locale: $locale,
            pagination: $searchQuery->getPagination(),
            filters: $searchQuery->getFilters(),
            sorts: $searchQuery->getSorts()
        );
    
        $processor->process($context);
    
        return $processor->makeResponse();
    }
}
```
### Command:
#### Sync(Синхронные команды):
Пример:
```php
use App\Path\To\UseCase as UseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Bundle\UIBundle\Core as UI;

class Controller {
    /**
     * @Route(".{_format}", methods={"POST"}, name=".create", defaults={"_format"="json"})
     * @OA\Post(
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 ref=@Model(type=UseCase\Create\Contract::class)
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Create User",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=UI\Contract\ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(
     *                          property="entities",
     *                          ref=@Model(type=UseCase\CommonOutputContract::class)
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *              )
     *          }
     *      )
     * )
     */
    public function create(
        UI\CQRS\Command\Sync\Processor $processor,
        UI\Dto\OutputFormat $outputFormat,
        UseCase\Create\Contract $contract,
        UseCase\Create\Handler $handler
    ): Response {
        $command = new UseCase\Create\Command();
        $command->mapContract($contract);
    
        $context = new UI\CQRS\Command\Sync\Context(
            handler: $handler,
            command: $command,
            outputFormat: $outputFormat->getFormat(),
        );
    
        $processor->process($context);
        return $processor->makeResponse();
    }
}
```
#### Async(Асинхронные команды):
Пример:
```php
use App\Path\To\Entity;
use App\Path\To\UseCase as UseCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Bundle\UIBundle\Core as UI;

class Controller {
    /**
     * @Route(".{_format}", methods={"POST"}, name=".create", defaults={"_format"="json"})
     * @OA\Post(
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 ref=@Model(type=UseCase\Create\Contract::class)
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Create Message",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=UI\Contract\ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="ok",
     *                      example=true
     *                 )
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *              )
     *          }
     *      )
     * )
     */
    #[Route(".{_format}", name: '.create', defaults: ['_format' => 'json'], methods: ["POST"])]
    public function create(
        UI\CQRS\Command\Async\Processor $processor,
        UI\Dto\OutputFormat $outputFormat,
        UseCase\Create\Contract $contract
    ): Response {
        $command = new UseCase\Create\Command();
        $command->mapContract($contract);
    
        $context = new UI\CQRS\Command\Async\Context(
            command: $command,
            outputFormat: $outputFormat->getFormat(),
        );
    
        $processor->process($context);
        return $processor->makeResponse();
    }
}
```