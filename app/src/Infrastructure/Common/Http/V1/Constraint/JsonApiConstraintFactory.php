<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

/**
 * Фабрика общих Symfony-ограничений для JSON:API-запросов.
 */
final class JsonApiConstraintFactory
{
    /**
     * Строгий объект с обязательными полями и без лишних.
     *
     * @param array<string, Constraint|array<int, Constraint>|Constraints\Optional> $fields Поля объекта.
     */
    public static function object(array $fields): Constraints\Collection
    {
        return new Constraints\Collection(
            fields: $fields,
            allowExtraFields: false,
            allowMissingFields: false,
            extraFieldsMessage: CollectionConstraintMessage::EXTRA,
            missingFieldsMessage: CollectionConstraintMessage::MISSING,
        );
    }

    /**
     * Документ JSON:API с data.type и data.attributes.
     *
     * @param string                         $type       Ожидаемый data.type.
     * @param Constraint|list<Constraint>    $attributes Ограничения для data.attributes.
     */
    public static function resourceDocument(string $type, Constraint|array $attributes): Constraints\Collection
    {
        return self::object([
            'data' => self::object([
                'type' => [
                    new Constraints\NotBlank(message: 'Укажите тип ресурса data.type.'),
                    new Constraints\Choice(
                        choices: [$type],
                        message: 'Недопустимый тип ресурса data.type.',
                    ),
                ],
                'attributes' => $attributes,
            ]),
        ]);
    }

    /**
     * Ограничения для идентификатора загаданного слова.
     *
     * @param string $pathLabel Путь поля в сообщении (attributes.secretId / filter[secretId]).
     *
     * @return list<Constraint>
     */
    public static function secretId(string $pathLabel): array
    {
        return [
            new Constraints\NotBlank(
                message: \sprintf('Укажите идентификатор загаданного слова %s.', $pathLabel),
            ),
            new Constraints\Type(
                type: 'string',
                message: 'Идентификатор загаданного слова должен быть строкой.',
            ),
            new Constraints\Length(
                max: 32,
                maxMessage: 'Идентификатор загаданного слова должен быть не длиннее {{ limit }} символов.',
            ),
        ];
    }
}
