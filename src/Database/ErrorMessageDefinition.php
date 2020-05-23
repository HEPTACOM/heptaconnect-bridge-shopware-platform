<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ErrorMessageDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptaconnect_error_message';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ErrorMessageEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ErrorMessageCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new FkField('mapping_id', 'mappingId', MappingDefinition::class))->addFlags(new Required()),
            new LongTextField('message', 'message'),
            new LongTextField('stack_trace', 'stackTrace'),

            new ManyToOneAssociationField('mapping', 'mapping_id', MappingDefinition::class),
        ]);
    }
}
